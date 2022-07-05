<?php

use Illuminate\Support\Facades\Route;

use Modules\DiscountWalletCharger\Http\Controllers\DiscountWalletChargerController;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('admin')->middleware('auth:api')->group(function () {

    Route::prefix('discounts')->group(function () {

        Route::get('/usage/{discount}', function ($discount) {
            $response = DB::table('discount_usage as d')
                ->leftJoin('users as u', 'u.id', '=', 'd.user_id')
                ->where('discount_id', $discount)
                ->get();

            return response()->json(['status' => true, 'entities' => $response]);
        });

    });

});

Route::prefix('discountWalletCharger')->group(function () {

    Route::post('/', [
        DiscountWalletChargerController::class,
        'DiscountWalletCharger'
    ])->name('discount_code_wallet_charger');

});
