<?php

namespace Modules\DiscountWalletCharger;

use App\Models\Finance;
use App\Models\User;
use App\Models\Discount;
use App\Repositories\DiscountRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Pure;

class UserFacadeRunner
{

    private $userRepositoty;


    public function __construct()
    {
        $this->userRepositoty = new UserRepository();
    }

    /**
     * @param $mobile
     * @return \Imanghafoori\Helpers\Nullable
     */
    public function userFindOrCreateBy($mobile)
    {
        $user = $this->userRepositoty->findBy('mobile', $mobile);

        if (! $user ) {

            $user = $this->userRepositoty->create(['mobile' => $mobile]);
        }


        return nullable($user);
    }


}
