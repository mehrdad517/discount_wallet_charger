<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Modules\DiscountWalletCharger\Http\Requests\DiscountWalletChargerRequest;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }


    public function mockDiscountWalletChargerValidator()
    {
        $this->mock(DiscountWalletChargerRequest::class, function ($mock) {
            $mock
                ->shouldReceive('get')
                ->andReturn(true);
        });
    }
}
