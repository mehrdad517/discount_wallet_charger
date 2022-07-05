<?php

namespace Modules\DiscountWalletCharger\Tests\Unit;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\DiscountWalletCharger\Facades\DiscountFacade;
use Tests\TestCase;

class DiscountWalletCharger extends TestCase
{

    use WithFaker;

    public function test_discount_not_found()
    {

        $this->mockDiscountWalletChargerValidator();

        DiscountFacade::shouldReceive('findDiscountBycode')
            ->once()
            ->with('worldcup')
            ->andReturn(nullable(null));

        DiscountFacade::shouldReceive('discountTypeHasFinanceCharger')->never();
        DiscountFacade::shouldReceive('discountIsFull')->never();
        DiscountFacade::shouldReceive('userFindOrCreateBy')->never();
        DiscountFacade::shouldReceive('checkBeforeDiscountUsage')->never();
        DiscountFacade::shouldReceive('store')->never();



        $this
            ->post(route('discount_wallet_charger'), [])
            ->assertJson([
                'status' => false,
                'message' => 'The discount code entered is invalid'
            ], 404);
    }

    public function test_discount_type_is_not_finance_type()
    {


        $discount = Discount::factory()->make();

        $this->mockDiscountWalletChargerValidator();

        DiscountFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(false);


        DiscountFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountFacade::shouldReceive('discountTypeIsFinanceCharger')->never();


        DiscountFacade::shouldReceive('discountHasExpired')->never();

        DiscountFacade::shouldReceive('userFindOrCreateBy')->never();
        DiscountFacade::shouldReceive('checkBeforeDiscountUsage')->never();
        DiscountFacade::shouldReceive('store')->never();



        $this
            ->post(route('discount_wallet_charger'), [])
            ->assertJson([
                'status' => false,
                'message' => 'This discount code is not defined for charging wallets'
            ]);
    }

    public function test_discount_is_expired()
    {
        $discount = Discount::factory()->make();

        $this->mockDiscountWalletChargerValidator();

        DiscountFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(true);

        DiscountFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);


        DiscountFacade::shouldReceive('discountIsFull')->never();
        DiscountFacade::shouldReceive('userFindOrCreateBy')->never();
        DiscountFacade::shouldReceive('checkBeforeDiscountUsage')->never();
        DiscountFacade::shouldReceive('store')->never();



        $this
            ->post(route('discount_wallet_charger'), [])
            ->assertJson([
                'status' => false,
                'message' => 'The discount code has expired'
            ]);
    }

    public function test_discount_is_full()
    {

        $discount = Discount::factory()->make();

        $this->mockDiscountWalletChargerValidator();

        DiscountFacade::shouldReceive('discountIsFull')
            ->with($discount->id)
            ->once()
            ->andReturn(true);


        DiscountFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);


        DiscountFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountFacade::shouldReceive('userFindOrCreateBy')->never();
        DiscountFacade::shouldReceive('checkBeforeDiscountUsage')->never();
        DiscountFacade::shouldReceive('store')->never();



        $this
            ->post(route('discount_wallet_charger'), [])
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

        DiscountFacade::shouldReceive('userFindOrCreateBy')
            ->once()
            ->with($user->mobile)
            ->andReturn(nullable(null));

        DiscountFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);

        DiscountFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountFacade::shouldReceive('discountIsFull')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountFacade::shouldReceive('checkBeforeDiscountUsage')->never();
        DiscountFacade::shouldReceive('store')->never();


        $this
            ->post(route('discount_wallet_charger'), [])
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


        DiscountFacade::shouldReceive('checkBeforeDiscountUsage')
            ->once()
            ->with($discount->id, $user->id)
            ->andReturn(true);


        DiscountFacade::shouldReceive('userFindOrCreateBy')
            ->once()
            ->with($user->mobile)
            ->andReturn(nullable($user));

        DiscountFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);

        DiscountFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountFacade::shouldReceive('discountIsFull')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountFacade::shouldReceive('store')->never();


        $this
            ->post(route('discount_wallet_charger'), [])
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

        DiscountFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);

        DiscountFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountFacade::shouldReceive('discountIsFull')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountFacade::shouldReceive('userFindOrCreateBy')
            ->once()
            ->with($user->mobile)
            ->andReturn(nullable($user));

        DiscountFacade::shouldReceive('checkBeforeDiscountUsage')
            ->once()
            ->with($discount->id, $user->id)
            ->andReturn(false);

        DiscountFacade::shouldReceive('store')
            ->with($discount->id, $user->id)
            ->once()
            ->andReturn(['status' => true]);


        $this
            ->post(route('discount_wallet_charger'), [])
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

        DiscountFacade::shouldReceive('userFindOrCreateBy')
            ->once()
            ->with($user->mobile)
            ->andReturn(nullable($user));

        DiscountFacade::shouldReceive('findDiscountBycode')
            ->with($discount->discount_code)
            ->once()
            ->andReturn(nullable($discount));

        DiscountFacade::shouldReceive('discountTypeIsFinanceCharger')
            ->with($discount->id)
            ->once()
            ->andReturn(true);

        DiscountFacade::shouldReceive('discountHasExpired')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountFacade::shouldReceive('discountIsFull')
            ->with($discount->id)
            ->once()
            ->andReturn(false);

        DiscountFacade::shouldReceive('checkBeforeDiscountUsage')
            ->once()
            ->with($discount->id, $user->id)
            ->andReturn(false);

        DiscountFacade::shouldReceive('store')
            ->with($discount->id, $user->id)
            ->once()
            ->andReturn(['status' => false, 'message' => 'other exception']);


        $this
            ->post(route('discount_wallet_charger'), [])
            ->assertJson([
                'status' => false,
                'message' => 'other exception'
            ]);
    }


}
