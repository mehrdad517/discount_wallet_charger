<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Utils;

class Discount extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $table = 'discounts';

    protected $guarded = [];


    const DISCOUNT_FINANCE_CHARGER = 'by_finance';


    /**
     * @param $code
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public static function findDiscountByCode($code)
    {
        return Discount::query()->where('discount_code', $code)->first();
    }


    /**
     * @param $id
     * @return bool
     */
    public static function discountHasExpired($id)
    {
        $discount = Discount::query()->find($id);

        return ! $discount->status;
    }


    /**
     * @param $id
     * @return bool
     */
    public static function discountIsFull($id)
    {
        $discount = Discount::query()->find($id);

        return ! $discount->total_count > $discount->total_usage;
    }

    /**
     * @param $id
     * @return bool
     */
    public static function discountTypeIsFinanceCharger($id)
    {
        $discount = Discount::query()->find($id);


        return $discount->type === self::DISCOUNT_FINANCE_CHARGER;
    }

    /**
     * @param $discount_id
     * @param $user_id
     * @return int
     */
    public static function discountUsageByUser($discount_id, $user_id)
    {
        return DB::table('discount_usage')->where('discount_id', $discount_id)->where('user_id', $user_id)->count();
    }
}
