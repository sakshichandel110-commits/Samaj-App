<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable()->after('profile_image');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('dob');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'education')) {
                $table->text('education')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('education');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            foreach (['profile_image','dob','address','bio','education','website'] as $c) {
                if (Schema::hasColumn('users', $c)) {
                    $cols[] = $c;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
}
