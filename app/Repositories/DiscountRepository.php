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


    public function usageList($id)
    {
        $list = DB::table('discount_usage as d')
            ->leftJoin('users as u', 'u.id', '=', 'd.user_id')
            ->where('discount_id', $id)
            ->get();

        return $list;
    }

    public function alreadyUse($id, $user_id)
    {
        return DB::table('discount_usage')->where('discount_id', $id)->where('user_id', $user_id)->count();
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
