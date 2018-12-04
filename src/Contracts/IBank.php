<?php
namespace NinePay\Bidv\Contracts;

interface IBank
{
	public function link(array $param);

	public function unlink(array $param);

	public function checkLink(array $param);

	public function checkOtp(array $param);

	public function wallet2Bank(array $param);

	public function bank2Wallet(array $param);

	public function checkProviderBalance(array $param);

	public function inquiry(array $param);
}