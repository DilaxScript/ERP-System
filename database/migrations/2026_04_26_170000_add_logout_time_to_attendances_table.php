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
        if (!Schema::hasColumn('attendances', 'logout_time')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->timestamp('logout_time')->nullable()->after('scanned_at');
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
        if (Schema::hasColumn('attendances', 'logout_time')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('logout_time');
            });
        }
    }
};
