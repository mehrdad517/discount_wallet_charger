<?php

namespace Modules\DiscountWalletCharger\Tests\Unit;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\DiscountWalletCharger\Facades\DiscountWalletChargerFacade;
use Tests\TestCase;

class DiscountWalletCharger extends TestCase
{

    use WithFaker;

    public function test_discount_not_found()
    {

        $this->mockDiscountWalletChargerValidator();

        DiscountWalletChargerFacade::shouldReceive('findDiscountBycode')
            ->once()
            ->with('worldcup')
            ->andReturn(nullable(null));

        DiscountWalletChargerFacade::shouldReceive('discountTypeHasFinanceCharger')->never();
        DiscountWalletChargerFacade::shouldReceive('discountIsFull')->never();
        DiscountWalletChargerFacade::shouldReceive('userFirstOrCreateWithMobile')->never();
        DiscountWalletChargerFacade::shouldReceive('checkBeforeDiscountUsage')->never();
        DiscountWalletChargerFacade::shouldReceive('store')->never();



        $this
            ->post(route('discount_code_wallet_charger'), [])
            ->assertJson([
                'status' => false,
                'message' => 'The discount code entered is invalid'
            ], 404);
    }

    public function test_discount_type_is_not_finance_type()
    {


        $discount = Discount::factory()->make();

        $this->mockDiscountWalletChargerValidator();

        DiscountWalletChargerFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(false);


        DiscountWalletChargerFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountWalletChargerFacade::shouldReceive('discountTypeIsFinanceCharger')->never();


        DiscountWalletChargerFacade::shouldReceive('discountHasExpired')->never();

        DiscountWalletChargerFacade::shouldReceive('userFirstOrCreateWithMobile')->never();
        DiscountWalletChargerFacade::shouldReceive('checkBeforeDiscountUsage')->never();
        DiscountWalletChargerFacade::shouldReceive('store')->never();



        $this
            ->post(route('discount_code_wallet_charger'), [])
            ->assertJson([
                'status' => false,
                'message' => 'This discount code is not defined for charging wallets'
            ]);
    }

    public function test_discount_is_expired()
    {
        $discount = Discount::factory()->make();

        $this->mockDiscountWalletChargerValidator();

        DiscountWalletChargerFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(true);

        DiscountWalletChargerFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountWalletChargerFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);


        DiscountWalletChargerFacade::shouldReceive('discountIsFull')->never();
        DiscountWalletChargerFacade::shouldReceive('userFirstOrCreateWithMobile')->never();
        DiscountWalletChargerFacade::shouldReceive('checkBeforeDiscountUsage')->never();
        DiscountWalletChargerFacade::shouldReceive('store')->never();



        $this
            ->post(route('discount_code_wallet_charger'), [])
            ->assertJson([
                'status' => false,
                'message' => 'The discount code has expired'
            ]);
    }

    public function test_discount_is_full()
    {

        $discount = Discount::factory()->make();

        $this->mockDiscountWalletChargerValidator();

        DiscountWalletChargerFacade::shouldReceive('discountIsFull')
            ->with($discount->id)
            ->once()
            ->andReturn(true);


        DiscountWalletChargerFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountWalletChargerFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);


        DiscountWalletChargerFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountWalletChargerFacade::shouldReceive('userFirstOrCreateWithMobile')->never();
        DiscountWalletChargerFacade::shouldReceive('checkBeforeDiscountUsage')->never();
        DiscountWalletChargerFacade::shouldReceive('store')->never();



        $this
            ->post(route('discount_code_wallet_charger'), [])
            ->assertJson([
                'status' => false,
                'message' => 'The capacity of the discount code has been completed'
            ]);
    }

    public function test_discount_user_first_or_create_failed()
    {
        $this->mockDiscountWalletChargerValidator();

        $discount = Discount::factory()->make();
        $user = User::factory()->make();

        DiscountWalletChargerFacade::shouldReceive('userFirstOrCreateWithMobile')
            ->once()
            ->with($user->mobile)
            ->andReturn(nullable(null));

        DiscountWalletChargerFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountWalletChargerFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);

        DiscountWalletChargerFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountWalletChargerFacade::shouldReceive('discountIsFull')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountWalletChargerFacade::shouldReceive('checkBeforeDiscountUsage')->never();
        DiscountWalletChargerFacade::shouldReceive('store')->never();


        $this
            ->post(route('discount_code_wallet_charger'), [])
            ->assertJson([
                'status' => false,
                'message' => 'An error occurred in receiving and creating user information'
            ]);
    }

    public function test_discount_already_used()
    {

        $this->mockDiscountWalletChargerValidator();

        $discount = Discount::factory()->make();
        $user = User::factory()->make();


        DiscountWalletChargerFacade::shouldReceive('checkBeforeDiscountUsage')
            ->once()
            ->with($discount->id, $user->id)
            ->andReturn(true);


        DiscountWalletChargerFacade::shouldReceive('userFirstOrCreateWithMobile')
            ->once()
            ->with($user->mobile)
            ->andReturn(nullable($user));

        DiscountWalletChargerFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountWalletChargerFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);

        DiscountWalletChargerFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountWalletChargerFacade::shouldReceive('discountIsFull')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountWalletChargerFacade::shouldReceive('store')->never();


        $this
            ->post(route('discount_code_wallet_charger'), [])
            ->assertJson([
                'status' => false,
                'message' => 'It has already been used'
            ]);

    }

    public function test_discount_store_succefull()
    {
        $this->mockDiscountWalletChargerValidator();

        $discount = Discount::factory()->make();
        $user = User::factory()->make();

        DiscountWalletChargerFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountWalletChargerFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);

        DiscountWalletChargerFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountWalletChargerFacade::shouldReceive('discountIsFull')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountWalletChargerFacade::shouldReceive('userFirstOrCreateWithMobile')
            ->once()
            ->with($user->mobile)
            ->andReturn(nullable($user));

        DiscountWalletChargerFacade::shouldReceive('checkBeforeDiscountUsage')
            ->once()
            ->with($discount->id, $user->id)
            ->andReturn(false);

        DiscountWalletChargerFacade::shouldReceive('store')
            ->with($discount->id, $user->id)
            ->once()
            ->andReturn(['status' => true]);


        $this
            ->post(route('discount_code_wallet_charger'), [])
            ->assertJson([
                'status' => true,
                'message' => 'The operation was successful'
            ]);
    }

    public function test_discount_store_not_succefull()
    {
        $this->mockDiscountWalletChargerValidator();

        $discount = Discount::factory()->make();
        $user = User::factory()->make();

        DiscountWalletChargerFacade::shouldReceive('userFirstOrCreateWithMobile')
            ->once()
            ->with($user->mobile)
            ->andReturn(nullable($user));

        DiscountWalletChargerFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountWalletChargerFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);

        DiscountWalletChargerFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountWalletChargerFacade::shouldReceive('discountIsFull')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountWalletChargerFacade::shouldReceive('checkBeforeDiscountUsage')
            ->once()
            ->with($discount->id, $user->id)
            ->andReturn(false);

        DiscountWalletChargerFacade::shouldReceive('store')
            ->with($discount->id, $user->id)
            ->once()
            ->andReturn(['status' => false, 'message' => 'other exception']);


        $this
            ->post(route('discount_code_wallet_charger'), [])
            ->assertJson([
                'status' => false,
                'message' => 'other exception'
            ]);
    }


}
