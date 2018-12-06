<?php
namespace NinePay\Bidv\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class BankServiceProvider.
 */
class BankServiceProvider extends ServiceProvider
{
	protected $app;

	public function boot()
	{
		$this->mergeConfigFrom(__DIR__ . '/../../config/bidv_wallet.php', 'bidv_wallet');

		if ($this->app->runningInConsole() && function_exists('config_path')) {
            $this->publishes([__DIR__ . '/../../config/bidv_wallet.php' => config_path('bidv_wallet.php')], 'config');
		}
	}
}