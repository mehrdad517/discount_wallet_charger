<?php

namespace Modules\DiscountWalletCharger\Tests\Feature\Models;

use App\Models\Discount;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use ModelHelperTesting;

    protected function model()
    {
        return new User();
    }
}
