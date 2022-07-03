<?php

namespace Modules\DiscountWalletCharger;

use App\Models\Finance;
use App\Models\User;
use App\Models\Discount;
use Illuminate\Support\Facades\DB;

class DiscountWalletCharger
{

    /**
     * @param $discount_id
     * @param $user_id
     * @return int
     */
    public function checkBeforeDiscountUsage($discount_id, $user_id)
    {
        return Discount::discountUsageByUser($discount_id, $user_id);
    }

    /**
     * @param $discount_code
     * @return \Imanghafoori\Helpers\Nullable
     */
    public function findDiscountBycode($discount_code)
    {
        $discount = Discount::findDiscountByCode($discount_code);

        return nullable($discount);
    }

    /**
     * @param $id
     * @return bool
     */
    public function discountTypeIsFinanceCharger($id)
    {
        return Discount::discountTypeIsFinanceCharger($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function discountHasExpired($id)
    {
        return Discount::discountHasExpired($id);
    }


    /**
     * @param $id
     * @return bool
     */
    public function discountIsFull($id)
    {
        return Discount::discountIsFull($id);
    }

    /**
     * @param $mobile
     * @return \Imanghafoori\Helpers\Nullable
     */
    public function userFirstOrCreateWithMobile($mobile)
    {

        $user = User::firstOrCreateWithMobile($mobile);

        return nullable($user);
    }


    /**
     * @param $user_id
     * @param $discount_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeWithProcedure($user_id, $discount_id)
    {

        try {
            $result = DB::select('call discount_handle_user_finance(?, ?)', [
                $user_id, // no difference between id and mobile, If the user exists, the user will not be created
                $discount_id // no difference between id and code
            ]);

            if (!isset($result)) {
                return response()->json(['status' => true]);
            }

            return response()->json(['status' => false, 'message' => $result['err']]);
        } catch (\Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()]);
        }
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
