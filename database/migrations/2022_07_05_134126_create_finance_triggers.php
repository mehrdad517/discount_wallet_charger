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

        // before delete
        DB::unprepared("CREATE DEFINER=`root`@`localhost` TRIGGER `finance_before_delete` BEFORE DELETE ON `finance` FOR EACH ROW BEGIN
	SIGNAL SQLSTATE '45000' SET MYSQL_ERRNO= 30002, MESSAGE_TEXT= 'The financial record cannot be deleted';
END");


        // before update
        DB::unprepared("CREATE DEFINER=`root`@`localhost` TRIGGER `finance_before_update` BEFORE UPDATE ON `finance` FOR EACH ROW BEGIN
	SIGNAL SQLSTATE '45000' SET MYSQL_ERRNO= 30001, MESSAGE_TEXT= 'The financial record cannot be updated';
END");

        // before insert
        DB::unprepared("CREATE DEFINER=`root`@`localhost` TRIGGER `finance_before_insert` BEFORE INSERT ON `finance` FOR EACH ROW BEGIN
	SET NEW.id = UUID();
END");



        // after insert
        DB::unprepared("CREATE DEFINER=`root`@`localhost` TRIGGER `finance_after_insert` AFTER INSERT ON `finance` FOR EACH ROW BEGIN
	-- update user total credit
	UPDATE users
	SET credit = IFNULL((SELECT SUM(creditor) - SUM(debtor) FROM finance WHERE user_id = NEW.user_id AND `type`='credit'), 0),
	locked_credit = abs(IFNULL((SELECT SUM(creditor) - SUM(debtor) FROM finance WHERE user_id = NEW.user_id AND `type`='locked_credit'), 0))
	WHERE id = NEW.user_id;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance_triggers');
    }
};
