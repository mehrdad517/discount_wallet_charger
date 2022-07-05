<?php

namespace Modules\DiscountWalletCharger\Facades;

use Illuminate\Support\Facades\Facade;

class UserFacade extends Facade
{

    /**
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'UserFacade';
    }
}
