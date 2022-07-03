<?php

namespace Modules\DiscountWalletCharger\Http\Controllers;

use App\Models\Discount;
use App\Models\Finance;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\DiscountWalletCharger\Facades\DiscountWalletChargerFacade;
use Modules\DiscountWalletCharger\Facades\DiscountWalletChargerResponderFacade;
use Modules\DiscountWalletCharger\Http\Requests\DiscountWalletChargerRequest;

class DiscountWalletChargerController extends Controller
{
    /**
     * @param DiscountWalletChargerRequest $request
     * @return mixed
     */
    public function DiscountWalletCharger(DiscountWalletChargerRequest $request)
    {
        $discount = DiscountWalletChargerFacade::findDiscountBycode($request->get('discount_code'))->getOrSend(function () {
           return DiscountWalletChargerResponderFacade::discountNotFound();
        });


        if( ! DiscountWalletChargerFacade::discountTypeIsFinanceCharger($discount->id) ) {
            return DiscountWalletChargerResponderFacade::discountTypeInvalid();
        };

        if(DiscountWalletChargerFacade::discountHasExpired($discount->id)) {
            return DiscountWalletChargerResponderFacade::discountHasExpired();
        };


        if(DiscountWalletChargerFacade::discountIsFull($discount->id)) {
            return DiscountWalletChargerResponderFacade::discountIsFull();
        };


        $user = DiscountWalletChargerFacade::userFirstOrCreateWithMobile($request->get('mobile'))->getOrSend(function () {
            return DiscountWalletChargerResponderFacade::UserFirstOrCreateFaild();
        });




        if ( DiscountWalletChargerFacade::checkBeforeDiscountUsage($discount->id, $user->id) ) {
            return DiscountWalletChargerResponderFacade::discountAleadyUsed();
        }

        $response = DiscountWalletChargerFacade::store($discount->id, $user->id); // storeWithProcedure

        if ($response['status']) {
            return DiscountWalletChargerResponderFacade::discountStoreSuccessful();
        }


        return DiscountWalletChargerResponderFacade::exceptionError($response['message']);


    }
}
