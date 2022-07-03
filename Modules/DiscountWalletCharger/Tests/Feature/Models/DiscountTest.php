<?php

namespace Modules\DiscountWalletCharger\Tests\Feature\Models;

use App\Models\Discount;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DiscountTest extends TestCase
{

    use ModelHelperTesting;

    protected function model()
    {
        return new Discount();
    }

}
