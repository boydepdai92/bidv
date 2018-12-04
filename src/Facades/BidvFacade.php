<?php

namespace NinePay\Bidv\Facades;

use Illuminate\Support\Facades\Facade;
use NinePay\Bidv\Bank;

/**
 * Class AssetsFacade.
 *
 * @since 22/07/2015 11:25 PM
 */
class BidvFacade extends Facade
{
	/**
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return Bank::class;
	}
}