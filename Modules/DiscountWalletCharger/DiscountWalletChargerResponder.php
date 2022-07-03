<?php

namespace Modules\DiscountWalletCharger;

class DiscountWalletChargerResponder
{

    public function discountTypeInvalid()
    {
        return response()->json([
            'status' => false,
            'message' => 'This discount code is not defined for charging wallets'
        ]);
    }

    public function UserFirstOrCreateFaild()
    {
        return response()->json([
            'status' => false,
            'message' => 'An error occurred in receiving and creating user information'
        ]);
    }

    public function discountIsFull()
    {
        return response()->json([
            'status' => false,
            'message' => 'The capacity of the discount code has been completed'
        ]);
    }

    public function discountHasExpired()
    {
        return response()->json([
            'status' => false,
            'message' => 'The discount code has expired'
        ]);
    }

    public function discountNotFound()
    {
        return response()->json([
            'status' => false,
            'message' => 'The discount code entered is invalid'
        ], 404);
    }

    public function discountStoreSuccessful()
    {
        return response(['status' => true, 'message' => 'The operation was successful']);
    }

    public function exceptionError($msg)
    {
        return response(['status' => false, 'message' => $msg]);
    }

    public function discountAleadyUsed()
    {
        return response(['status' => false, 'message' => 'It has already been used']);
    }


}
