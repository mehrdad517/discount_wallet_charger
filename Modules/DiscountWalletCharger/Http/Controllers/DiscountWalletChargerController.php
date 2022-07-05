<?php

namespace Modules\DiscountWalletCharger\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\DiscountWalletCharger\Facades\DiscountFacade;
use Modules\DiscountWalletCharger\Facades\ResponderFacade;
use Modules\DiscountWalletCharger\Facades\UserFacade;
use Modules\DiscountWalletCharger\Http\Requests\DiscountWalletChargerRequest;

class DiscountWalletChargerController extends Controller
{
    /**
     * @param DiscountWalletChargerRequest $request
     * @return mixed
     */
    public function DiscountWalletCharger(DiscountWalletChargerRequest $request)
    {
        $discount = DiscountFacade::findDiscountBycode($request->get('discount_code'))->getOrSend(function () {
           return ResponderFacade::discountNotFound();
        });


        if( ! DiscountFacade::discountTypeIsFinanceCharger($discount->id) ) {
            return ResponderFacade::discountTypeInvalid();
        };

        if(DiscountFacade::discountHasExpired($discount->id)) {
            return ResponderFacade::discountHasExpired();
        };


        if(DiscountFacade::discountIsFull($discount->id)) {
            return ResponderFacade::discountIsFull();
        };


        $user = UserFacade::userFindOrCreateBy($request->get('mobile'))->getOrSend(function () {
            return ResponderFacade::UserFirstOrCreateFaild();
        });


        if ( DiscountFacade::alreadyUse($discount->id, $user->id) ) {
            return ResponderFacade::discountAleadyUsed();
        }

        $response = DiscountFacade::store($discount->id, $user->id); // storeWithProcedure

        if ($response['status']) {
            return ResponderFacade::discountStoreSuccessful();
        }


        return ResponderFacade::exceptionError($response['message']);

    }


    public function discountUsageList($discount_id)
    {
        $response = DiscountFacade::usageList($discount_id)->getOrSend(function () {
            return ResponderFacade::discountUsageError();
        });

        return response()->json(['status' => true, 'entities' => $response]);
    }

}
