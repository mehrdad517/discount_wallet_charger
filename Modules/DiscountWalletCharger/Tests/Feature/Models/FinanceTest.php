<?php

namespace Modules\DiscountWalletCharger\Tests\Feature\Models;

use App\Models\Discount;
use App\Models\Finance;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinanceTest extends TestCase
{
    use ModelHelperTesting, RefreshDatabase;

    protected function model()
    {
        return new Finance();
    }
}
