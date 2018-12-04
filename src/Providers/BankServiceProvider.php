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
		$this->mergeConfigFrom(__DIR__ . '/../../config/bank.php', 'bank');

		if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../../config/bank.php' => config_path('bank.php')], 'config');
		}
	}
}