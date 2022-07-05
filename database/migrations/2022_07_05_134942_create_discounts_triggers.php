<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE DEFINER=`root`@`localhost` TRIGGER `discounts_before_update` BEFORE UPDATE ON `discounts` FOR EACH ROW BEGIN
	IF NEW.usage_count >= NEW.total_count AND NEW.`status` = 1
	THEN
		SET NEW.`status` = 0; -- expire status
	END IF;
END");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts_triggers');
    }
};
