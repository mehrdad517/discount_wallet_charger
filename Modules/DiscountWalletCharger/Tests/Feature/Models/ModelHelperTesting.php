<?php

namespace Modules\DiscountWalletCharger\Tests\Feature\Models;

trait ModelHelperTesting
{

    abstract protected function model();

    /**
     * @return void
     */
    public function test_insert_data()
    {
        $model = $this->model();

        $data = $model::factory()->make()->toArray();

        $model::create($data);

        $this
            ->assertDatabaseHas($model->getTable(), $data);
    }

}
