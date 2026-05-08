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
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'login_method')) {
                $table->string('login_method')->nullable()->after('login_time');
            }

            if (!Schema::hasColumn('attendances', 'logout_method')) {
                $table->string('logout_method')->nullable()->after('logout_time');
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
        Schema::table('attendances', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('attendances', 'login_method') ? 'login_method' : null,
                Schema::hasColumn('attendances', 'logout_method') ? 'logout_method' : null,
            ]);

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
