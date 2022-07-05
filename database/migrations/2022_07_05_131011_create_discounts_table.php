<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {

            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->id()->autoIncrement();
            $table->string('discount_code', 50)->unique();
            $table->enum('status', [1, 0])->default(1)->index();
            $table->string('title')->nullable();
            $table->integer('total_count')->default(0);
            $table->integer('usage_count')->default(0);
            $table->enum('is_percent', [1, 0])->default(0);
            $table->string('discount_value', 20);
            $table->enum('type', ['by_finance'])->default('by_finance');
            $table->dateTime('expiration')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->useCurrent();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
};
