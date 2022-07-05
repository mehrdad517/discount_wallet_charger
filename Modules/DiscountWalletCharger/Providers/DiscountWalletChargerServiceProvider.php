<?php

namespace Modules\DiscountWalletCharger\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\DiscountWalletCharger\DiscountFacadeRunner;
use Modules\DiscountWalletCharger\DiscountWalletChargerResponder;
use Modules\DiscountWalletCharger\ResponderFacadeRunner;
use Modules\DiscountWalletCharger\UserFacadeRunner;

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

        $this->app->bind('DiscountFacade', function () {
            return new DiscountFacadeRunner();
        });

        $this->app->bind('UserFacade', function () {
            return new UserFacadeRunner();
        });

        $this->app->bind('ResponderFacade', function () {
            return new ResponderFacadeRunner();
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
