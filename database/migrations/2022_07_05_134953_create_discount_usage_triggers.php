<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        DB::unprepared("CREATE DEFINER=`root`@`localhost` TRIGGER `discount_usage_after_insert` AFTER INSERT ON `discount_usage` FOR EACH ROW BEGIN
	-- set discount default value
	SET @discount_type = '';
	SET @discount_value = 0;

	-- update discount usage counter
	UPDATE discounts SET usage_count = usage_count + 1 WHERE id = NEW.discount_id;

	-- fetch discount record and access to type and amount
	SELECT `type`, discount_value INTO @discount_type, @discount_value
	FROM discounts WHERE id = NEW.discount_id;


	-- check discount type is by finance
	IF @discount_type = 'by_finance' AND @discount_value > 0
	THEN

		-- insert into finance table and increase credit
		INSERT INTO finance (user_id, creditor, financeable_id, financeable_type)
		VALUES (NEW.user_id, @discount_value, NEW.discount_id, 'discount');

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
        Schema::dropIfExists('discount_usage_triggers');
    }
};
