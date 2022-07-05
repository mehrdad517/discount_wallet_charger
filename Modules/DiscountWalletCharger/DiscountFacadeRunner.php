<?php

namespace Modules\DiscountWalletCharger;

use App\Models\Finance;
use App\Models\User;
use App\Models\Discount;
use App\Repositories\DiscountRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Pure;

class DiscountFacadeRunner
{


    private $discountRepository;

    public function __construct()
    {
        $this->discountRepository = new DiscountRepository();
    }

    /**
     * @param $discount_id
     * @param $user_id
     * @return int
     */
    public function alreadyUse($discount_id, $user_id)
    {
        return $this->discountRepository->alreadyUse($discount_id, $user_id);
    }

    /**
     * @param $discount_code
     * @return \Imanghafoori\Helpers\Nullable
     */
    public function findDiscountBycode($discount_code)
    {
        $discount = $this->discountRepository->findBy('discount_code', $discount_code);

        return nullable($discount);
    }

    /**
     * @param $id
     * @return bool
     */
    public function discountTypeIsFinanceCharger($id)
    {
        return $this->discountRepository->discountType($id) === Discount::DISCOUNT_FINANCE_CHARGER;
    }

    /**
     * @param $id
     * @return bool
     */
    public function discountHasExpired($id)
    {
        return $this->discountRepository->hasExpired($id);
    }


    /**
     * @param $id
     * @return bool
     */
    public function discountIsFull($id)
    {
        return $this->discountRepository->ifFull($id);
    }



    /**
     * @param $discount_id
     * @param $user_id
     * @return array|bool[]
     */
    public function store($discount_id, $user_id)
    {

        DB::beginTransaction();

        try {

            if ( ! $this->discountTypeIsFinanceCharger($discount_id) ) {
                return ['status' => false, 'message' => 'This discount code is not defined for charging wallets'];
            }

            if ($this->discountHasExpired($discount_id)) {
                return ['status' => false, 'message' => 'The discount code has expired!'];
            }

            if ($this->discountIsFull($discount_id)) {
                return ['status' => false, 'message' => 'The discount capacity is full!'];
            }



            DB::table('discount_usage')->insert([
                'user_id' => $user_id,
                'discount_id' => $discount_id
            ]);

            // After insert, the triggers are executed and charge wallet also increment total usage

            DB::commit();

        } catch (\Exception $exception) {

            // rollback transaction
            DB::rollBack();

            return ['status' => false, 'message' => $exception->getMessage()];
        }

        return ['status' => true];
    }


}
