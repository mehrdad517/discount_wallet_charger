<?php

namespace Modules\DiscountWalletCharger\Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DiscountWalletChargerValidation extends TestCase
{

    public function test_validation_required_data()
    {

        $error =  [
            'mobile' => 'The mobile field is required.',
            'discount_code' => 'The discount code field is required.',
        ];


        $this
            ->post(route('discount_code_wallet_charger'), [])
            ->assertSessionHasErrors($error);

    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_discount_wallet_charger_that_validation_mobile_max()
    {

        $data = [
            'mobile' => '093617532511'
        ];


        $error =  [
            'mobile' => 'The mobile must not be greater than 11 characters.',
        ];

        $this
            ->post(
                route('discount_code_wallet_charger'), $data
            )->assertSessionHasErrors($error);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_discount_wallet_charger_that_validation_mobile_min()
    {

        $data = [
            'mobile' => '0936'
        ];


        $error =  [
            'mobile' => 'The mobile must be at least 11 characters.',
        ];

        $this
            ->post(
                route('discount_code_wallet_charger'), $data
            )->assertSessionHasErrors($error);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_discount_wallet_charger_that_validation_unique_mobile()
    {

        $user = User::factory()->create();


        $data = [
            'mobile' => $user->mobile,
        ];


        $error =  [
            'mobile' => 'The mobile has already been taken.',
        ];

        $this
            ->post(
                route('discount_code_wallet_charger'), $data
            )->assertSessionHasErrors($error);
    }
}
