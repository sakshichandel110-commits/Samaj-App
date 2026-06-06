<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeEmailNullableOnUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to avoid requiring doctrine/dbal for column changes
        DB::statement("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Make email NOT NULL again. Ensure no null emails exist before running down.
        DB::statement("ALTER TABLE `users` MODIFY `email` VARCHAR(255) NOT NULL;");
    }
}
