<?php

namespace App\Repositories;

use App\Models\Discount;
use Illuminate\Support\Facades\DB;

class DiscountRepository
{

    use RepositoryTrait;

    protected function model()
    {
        return new Discount();
    }

    /**
     * @param $discount_id
     * @param $user_id
     * @return mixed
     */
    public function attach($discount_id, $user_id)
    {
        return $this->find($discount_id)->users()->attach($user_id);
    }


    public function usageList($id)
    {
        $list =  Discount::query()->with('users')->find($id);;

        return $list;
    }

    public function alreadyUse($id, $user_id)
    {
        return Discount::query()->whereHas('users', function ($q) use($user_id) {
            $q->where('user_id', $user_id);
        })->where('id', 1)->count();
    }

    /**
     * @param $id
     * @return bool
     */
    public function ifFull($id)
    {
        $discount = $this->find($id);

        return ! $discount->total_count > $discount->total_usage;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasExpired($id)
    {
        $discount = $this->find($id);

        return ! $discount->status;
    }

    /**
     * @param $id
     * @return bool
     */
    public function discountType($id)
    {

        $discount = $this->find($id);


        return $discount->type;
    }

}
