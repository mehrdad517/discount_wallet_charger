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
        Schema::create('finance', function (Blueprint $table) {

            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->uuid('id')->primary();
            $table->bigInteger('user_id')->index();
            $table->bigInteger('financeable_id')->nullable()->index();
            $table->string('financeable_type', 100)->nullable();
            $table->enum('type', ['credit', 'locked_credit'])->default('credit');
            $table->double('creditor')->default(0);
            $table->double('debtor')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance_tables');
    }
};
