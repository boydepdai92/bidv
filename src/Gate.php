<?php
namespace NinePay\Bidv;

use NinePay\Bidv\Contracts\Api;
use NinePay\Bidv\Contracts\IGate;
use NinePay\Bidv\Contracts\Response;

class Gate extends Api implements IGate
{
	const PATH_INITTRANS = 'inittrans';
	const PATH_VERIFY    = 'verify';
	const PATH_INQUIRY   = 'inquiry';
	const PATH_REFUND    = 'refund';

	const ACTION = '/WSDL-service2.serviceagent/NCC_PortTypeEndpoint2/';

	public function setConfig()
	{
		return 'bidv_gate';
	}

	public function getUrl()
	{
		return config('bidv_gate.url');
	}

	public function inittrans(array $param)
	{
		$this->typeSign = Response::$md5;

		$res = $this->call(self::PATH_INITTRANS, self::ACTION . self::PATH_INITTRANS, $param);

		return $res;
	}

	public function verify(array $param)
	{
		$this->typeSign = Response::$md5;

		$res = $this->call(self::PATH_VERIFY,self::ACTION . self::PATH_VERIFY, $param);

		return $res;
	}

	public function inquiry(array $param)
	{
		$this->typeSign = Response::$md5;

		$res = $this->call(self::PATH_INQUIRY,self::ACTION . self::PATH_INQUIRY, $param);

		return $res;
	}

	public function refund(array $param)
	{
		$this->typeSign = Response::$md5;

		$res = $this->call(self::PATH_REFUND,self::ACTION . self::PATH_REFUND, $param);

		return $res;
	}
}