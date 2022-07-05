<?php

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;

class UserRepository
{

    use RepositoryTrait;

    /**
     * @return User
     */
    protected function model(): User
    {
        return new User();
    }


}
