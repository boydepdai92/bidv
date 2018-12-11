<?php

namespace NinePay\Bidv\Contracts;

trait Helper
{
	private $config;

	private $is_wallet = false;

	private $prefix = 'ncc:';

	private $headerWallet = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ncc="NCCWalletInput_Schema"><soapenv:Header/><soapenv:Body><ncc:root>';

    private $headerGate = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ncc="NCCInput_Schema"><soapenv:Header/><soapenv:Body><ncc:root>';

    private $footer = '</ncc:root></soapenv:Body></soapenv:Envelope>';

	private $default_param = [
		'Merchant_Name' => '',
		'Trandate'      => '',
		'Trans_Id'      => '',
		'Trans_Desc'    => '',
		'Amount'        => 0,
		'Curr'          => '',
		'Payer_Id'      => '',
		'Payer_Name'    => '',
		'Payer_Addr'    => '',
		'Type'          => '',
		'Custmer_Id'    => '',
		'Customer_Name' => '',
		'IssueDate'     => '',
	];

	private $param_wallet = [
		'Channel_Id' => '',
		'Link_Type'  => '',
		'Otp_Number' => '',
		'More_Info'  => '',
	];

	public function readConfig($name)
	{
		$this->config = config($name);

		if ($name == 'bidv_wallet') {
			$this->is_wallet = true;
		}
	}

    public function buildParam($param, $type)
    {
    	if ($this->is_wallet) {
    		$this->default_param = array_merge($this->default_param, $this->param_wallet);
	    }

	    $param = array_merge($this->default_param, $param);

        $begin = [
            'Service_Id'  => $this->config['service_id'],
            'Merchant_Id' => $this->config['merchant_id'],
        ];

        $param = $begin + $param;

        $param['Secure_Code'] = $this->buildSign($param, $type);

        return $param;
    }

    public function generateValidXmlFromArray($array)
    {
        $array = array_combine(
            array_map(function($key) { return $this->prefix . $key; }, array_keys($array)),
            $array
        );

	    if ($this->is_wallet) {
		    $xml = $this->headerWallet;
	    } else {
	    	$xml = $this->headerGate;
	    }

        $xml .= $this->generateXmlFromArray($array);
        $xml .= $this->footer;

        return $xml;
    }

    private function getData($value)
    {
        $return = [];

        foreach ($value as $val) {
            if (!empty($val['type']) && $val['type'] == 'complete') {
                if (strpos($val['tag'], 'NS0:') !== false) {
                    $val['tag'] = str_replace('NS0:', '', $val['tag']);
                }

                if (!empty($val['value'])) {
                    $return[$val['tag']] = $val['value'];
                } else {
                    $return[$val['tag']] = '';
                }
            }
        }

        $return['IS_CORRECT_SIGN'] = false;

        return $return;
    }

    private function processData($type, &$return)
    {
        if (isset($return['RESPONSE_CODE'])) {
            if (!empty($return['SECURE_CODE'])) {
                $Secure_Code_Res = $return['SECURE_CODE'];
                unset($return['SECURE_CODE']);
                if ($type == Response::$md5) {
                    $secureCode = $this->buildSign($return, $type, true);
                    if (strcmp($secureCode, $Secure_Code_Res) == 0) {
                        $return['IS_CORRECT_SIGN'] = true;
                    }
                } else {
                    $sign_key = $this->GetPublicKeyFromFile($this->config['public_key_bidv']);
                    $secureCode = $this->signature_verify($Secure_Code_Res, $this->createStr($return, true), $sign_key);
                    if ($secureCode) {
                        $return['IS_CORRECT_SIGN'] = true;
                    }
                }
            }

            $return['MESSAGE'] = $this->getMessage($return['RESPONSE_CODE']);
        }

        return $return;
    }

    public function transformResponse($response, $type)
    {
        $xml = xml_parser_create();
        xml_parse_into_struct($xml, $response, $value);
        xml_parser_free($xml);

        $return = $this->getData($value);

        $this->processData($type,$return);

        return $return;
    }

    private function generateXmlFromArray($array)
    {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $value) {
                $xml .= '<' . $key . '>' . self::generateXmlFromArray($value) . '</' . $key . '>';
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES);
        }

        return $xml;
    }

    private function getMessage($code)
    {
        return (!empty(Response::readMessageFromResponseCode($code))) ? Response::readMessageFromResponseCode($code) : 'Không tìm thấy mã lỗi thích hợp';
    }

    private function buildSign($param, $type, $is_remove = false)
    {
        $str = $this->createStr($param, $is_remove);

        if ($type == Response::$md5) {
            return md5($str);
        } else {
            return $this->signature_sign($str, $this->config['private_key_9pay']);
        }
    }

    private function createStr($param, $is_remove = false)
    {
        $str = implode('|', $param);

        $str = $this->config['private_key_bidv'] . '|' . $str;

        if ($is_remove) {
            $str = substr($str, 0, -1);
        }

        return $str;
    }

    private function signature_sign($message, $pathFile)
    {
        $signature = null;

        $sign_key  = $this->GetPrivateKeyFromFile($pathFile);

        openssl_sign($message, $signature, $sign_key, 'SHA1');

        return base64_encode($signature);
    }

    private function signature_verify($signature, $message, $sign_key)
    {
        $signature = base64_decode($signature);

        return openssl_verify($message, $signature, $sign_key, 'SHA1');
    }

    private function GetPrivateKeyFromFile($pathFile)
    {
        $fp = fopen($pathFile,"r");

        $pri_key = fread($fp, filesize($pathFile));

        fclose($fp);

        return $pri_key;
    }

    private function GetPublicKeyFromFile($filePath)
    {
        $fp = fopen($filePath,"r");

        $pub_key = fread($fp, filesize($filePath));

        openssl_get_publickey($pub_key);

        return $pub_key;
    }
}