<?php

namespace NinePay\Bidv\Facades;

use Illuminate\Support\Facades\Facade;
use NinePay\Bidv\Gate;

/**
 * Class AssetsFacade.
 *
 * @since 22/07/2015 11:25 PM
 */
class GateFacade extends Facade
{
	/**
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return Gate::class;
	}
}