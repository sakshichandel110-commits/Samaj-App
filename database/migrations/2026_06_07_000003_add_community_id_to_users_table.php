<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommunityIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only add the column if it doesn't already exist (prevents duplicate column errors)
        if (! Schema::hasColumn('users', 'community_id')) {
            Schema::table('users', function (Blueprint $table) {
                // communities.id was created with $table->id() (unsigned big integer)
                $table->unsignedBigInteger('community_id')->nullable()->after('id');
                $table->foreign('community_id')->references('id')->on('communities')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'community_id')) {
            Schema::table('users', function (Blueprint $table) {
                // drop foreign first if it exists
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $doctrineTable = $sm->listTableDetails(Schema::getConnection()->getTablePrefix() . 'users');
                if ($doctrineTable->hasColumn('community_id')) {
                    // attempt to drop foreign if present
                    try {
                        $table->dropForeign(['community_id']);
                    } catch (\Exception $e) {
                        // ignore if foreign key doesn't exist
                    }
                }

                $table->dropColumn('community_id');
            });
        }
    }
}
