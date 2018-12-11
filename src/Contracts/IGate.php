<?php

namespace NinePay\Bidv\Contracts;

interface IGate
{
	public function inittrans(array $param);

	public function verify(array $param);

	public function inquiry(array $param);

	public function refund(array $param);
}