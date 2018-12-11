<?php
namespace NinePay\Bidv\Contracts;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

abstract class Api
{
	use Helper;

	protected $url;

	protected $typeSign;

	abstract public function getUrl();

	abstract public function setConfig();

	public function __construct()
	{
		$this->setUrl();

		$this->readConfig($this->setConfig());
	}

	private function setUrl()
	{
		$this->url = $this->getUrl();
	}

	public function call($path, $action, $param)
	{
		try {
			$param = $this->buildParam($param, $this->typeSign);

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

			return $this->transformResponse($response->getBody()->getContents(), $this->typeSign);
		} catch (\Exception $e) {
			Log::error('Error call BIDV: ' . $e);

			return [
				'RESPONSE_CODE' => 500,
				'MESSAGE'       => 'Có lỗi xảy ra, xin vui lòng thử lại'
			];
		}
	}
}