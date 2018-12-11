<?php
namespace NinePay\Bidv;

use NinePay\Bidv\Contracts\Api;
use NinePay\Bidv\Contracts\IBank;
use NinePay\Bidv\Contracts\Response;

class Bank extends Api implements IBank
{
	const PATH_LINK        = 'link';
	const PATH_UNLINK      = 'UnLink';
	const PATH_BANK2WALLET = 'Wallet2Bank';
	const PATH_WALLET2BANK = 'Bank2Wallet';
	const PATH_CHECK_LINK  = 'checkLink';
	const PATH_CHECK_OTP   = 'checkOtp';
	const PATH_INQUIRY     = 'inquiry';
	const PATH_CHECK_PROVIDER_BALANCE = 'providerBalance';

	const LINK_SOAP_ACTION        = '/NCC_WSDL.serviceagent//link';
	const UNLINK_SOAP_ACTION      = '/NCC_WSDL.serviceagent//unLink';
	const BANK2WALLET_SOAP_ACTION = '/NCC_WSDL.serviceagent//bank2Wallet';
	const WALLET2BANK_ACTION      = '/NCC_WSDL.serviceagent//wallet2Bank';
	const CHECK_LINK_ACTION       = '/NCC_WSDL.serviceagent//checkLink';
	const CHECK_OTP_ACTION        = '/NCC_WSDL.serviceagent//checkOtp';
	const INQUIRY_ACTION          = '/WSDL-service2.serviceagent/NCC_PortTypeEndpoint2/inquiry';
	const CHECK_PROVIDER_BALANCE_ACTION = '/WSDL-service0.serviceagent/NCC_PortTypeEndpoint0/providerBalance';

	public function setConfig()
	{
		return 'bidv_wallet';
	}

	public function getUrl()
	{
		return config('bidv_wallet.url');
	}

	public function link(array $param)
	{
		$this->typeSign = Response::$md5;

		$res = $this->call(self::PATH_LINK, self::LINK_SOAP_ACTION, $param);

		return $res;
	}

	public function unlink(array $param)
	{
		$this->typeSign = Response::$md5;

		$res = $this->call(self::PATH_UNLINK,self::UNLINK_SOAP_ACTION, $param);

		return $res;
	}

	public function checkLink(array $param)
	{
		$this->typeSign = Response::$md5;

		$res = $this->call(self::PATH_CHECK_LINK,self::CHECK_LINK_ACTION, $param);

		return $res;
	}

	public function checkOtp(array $param)
	{
		$this->typeSign = Response::$rsa;

		$res = $this->call(self::PATH_CHECK_OTP,self::CHECK_OTP_ACTION, $param);

		return $res;
	}

	public function bank2Wallet(array $param)
	{
		$this->typeSign = Response::$rsa;

		$res = $this->call(self::PATH_BANK2WALLET,self::BANK2WALLET_SOAP_ACTION, $param);

		return $res;
	}

	public function wallet2Bank(array $param)
	{
		$this->typeSign = Response::$rsa;

		$res = $this->call(self::PATH_WALLET2BANK,self::WALLET2BANK_ACTION, $param);

		return $res;
	}

	public function checkProviderBalance(array $param)
	{
		$this->typeSign = Response::$rsa;

		$res = $this->call(self::PATH_CHECK_PROVIDER_BALANCE,self::CHECK_PROVIDER_BALANCE_ACTION, $param);

		return $res;
	}

	public function inquiry(array $param)
	{
		$this->typeSign = Response::$rsa;

		$res = $this->call(self::PATH_INQUIRY,self::INQUIRY_ACTION, $param);

		return $res;
	}
}