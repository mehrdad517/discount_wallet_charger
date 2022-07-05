<?php

namespace App\Repositories;

trait RepositoryTrait
{

    abstract protected function model();

    public function find($id)
    {
        return $this->model()::query()->find($id);
    }

    public function create($data)
    {
        return $this->model()::query()->create($data);
    }

    public function findBy($field, $value)
    {
        return $this->model()::query()->where($field, $value)->first();
    }

    public function with($relations)
    {
        return $this->model()::with($relations);
    }
}
