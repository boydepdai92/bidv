<?php
namespace NinePay\Bidv\Contracts;

use GuzzleHttp\Client;

class Api
{
	const TYPE_MD5 = 'md5';
	const TYPE_RSA = 'rsa';

	protected $url;

	protected $typeSign;

	private $header = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ncc="NCCWalletInput_Schema"><soapenv:Header/><soapenv:Body><ncc:root>';

	private $footer = '</ncc:root></soapenv:Body></soapenv:Envelope>';

	private function processParam($array)
	{
		$return = [];

		if (is_array($array)) {
			foreach ($array as $key => $value) {
				$return['ncc:' . $key] = $value;
			}
		}

		return $return;
	}

	private function generateValidXmlFromArray($array)
	{
		$array = $this->processParam($array);

		$xml  = $this->header;
		$xml .= $this->generateXmlFromArray($array);
		$xml .= $this->footer;

		return $xml;
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

	public function call($path, $action, $param)
	{
		try {
			$param = $this->buildParam($param);

			$body = $this->generateValidXmlFromArray($param);

			$client = new Client([
				'headers' => [
					'SOAPAction'      => $action,
					'Operation'       => $path,
					"Content-Type"    => "text/xml",
					"accept"          => "*/*",
					"accept-encoding" => "gzip, deflate"
				]
			]);

			$response = $client->post($this->url, ['body' => $body]);

			return $this->transformResponse($response->getBody()->getContents());
		} catch (\Exception $e) {
			return [
				'RESPONSE_CODE'   => 500,
				'MESSAGE'         => 'Có lỗi xảy ra, xin vui lòng thử lại',
				'IS_CORRECT_SIGN' => false
			];
		}
	}

	private function getMessage($code)
	{
		return (!empty(Response::readMessageFromResponseCode($code))) ? Response::readMessageFromResponseCode($code) : 'Không tìm thấy mã lỗi thích hợp';
	}

	private function buildParam($param)
	{
		$begin = [
			'Service_Id'  => config('bank.service_id'),
			'Merchant_Id' => config('bank.merchant_id'),
		];

		$param = $begin + $param;

		$param['Secure_Code'] = $this->buildSign($param);

		return $param;
	}

	private function buildSign($param, $is_remove = false)
	{
		if ($this->typeSign == self::TYPE_MD5) {
			return $this->buildSignMd5($param, $is_remove);
		} else {
			return $this->buildSignRsa($param, $is_remove);
		}
	}

	private function buildSignMd5($param, $is_remove)
	{
		$str = $this->createStr($param);

		if ($is_remove) {
			$str = substr($str, 0, -1);
		}

		return md5($str);
	}

	private function buildSignRsa($param, $is_remove)
	{
		$str = $this->createStr($param);

		if ($is_remove) {
			$str = substr($str, 0, -1);
		}

		$signature = $this->signature_sign($str, config('bank.private_key_9pay'));

		return $signature;
	}

	private function transformResponse($response)
	{
		$xml = xml_parser_create();
		xml_parse_into_struct($xml, $response, $value);
		xml_parser_free($xml);

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

		if (isset($return['RESPONSE_CODE'])) {
			if (!empty($return['SECURE_CODE'])) {
				$Secure_Code_Res = $return['SECURE_CODE'];
				unset($return['SECURE_CODE']);
				if ($this->typeSign == self::TYPE_MD5) {
					$secureCode = $this->buildSign($return, true);
					if (strcmp($secureCode, $Secure_Code_Res) == 0) {
						$return['IS_CORRECT_SIGN'] = true;
					}
				} else {
					$sign_key = $this->GetPublicKeyFromFile(config('bank.public_key_bidv'));
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

	private function createStr($param, $is_remove = false)
	{
		$str = implode('|', $param);

		$str = config('bank.private_key') . '|' . $str;

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

		$pub_key = fread($fp, filesize($pathFile));

		fclose($fp);

		return $pub_key;
	}

	private function GetPublicKeyFromFile($filePath)
	{
		$fp = fopen($filePath,"r");

		$pub_key = fread($fp, filesize($filePath));

		$pub_key = openssl_get_publickey($pub_key);

		return $pub_key;
	}
}