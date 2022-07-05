<?php

namespace App\Repositories;

use App\Models\Finance;
use App\Models\User;

class FinanceRepository
{

    use RepositoryTrait;

    /**
     * @return Finance
     */
    protected function model(): Finance
    {
        return new Finance();
    }


}
