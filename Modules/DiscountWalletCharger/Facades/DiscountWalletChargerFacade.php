<?php

namespace Modules\DiscountWalletCharger\Facades;

use Illuminate\Support\Facades\Facade;

class DiscountWalletChargerFacade extends Facade
{

    /**
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'DiscountWalletChargerFacade';
    }
}
