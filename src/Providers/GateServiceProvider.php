<?php
namespace NinePay\Bidv\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class BankServiceProvider.
 */
class GateServiceProvider extends ServiceProvider
{
	protected $app;

	public function boot()
	{
		$this->mergeConfigFrom(__DIR__ . '/../../config/bidv_gate.php', 'bidv_gate');

		if ($this->app->runningInConsole() && function_exists('config_path')) {
			$this->publishes([__DIR__ . '/../../config/bidv_gate.php' => config_path('bidv_gate.php')], 'config');
		}
	}
}