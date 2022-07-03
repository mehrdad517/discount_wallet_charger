<?php

namespace Modules\DiscountWalletCharger\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\DiscountWalletCharger\DiscountWalletCharger;
use Modules\DiscountWalletCharger\DiscountWalletChargerResponder;

class DiscountWalletChargerServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'DiscountWalletCharger';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'DiscountWalletCharger';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind('DiscountWalletChargerFacade', function () {
            return new DiscountWalletCharger();
        });

        $this->app->bind('DiscountWalletChargerResponderFacade', function () {
            return new DiscountWalletChargerResponder();
        });
    }




    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
