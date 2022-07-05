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



}
