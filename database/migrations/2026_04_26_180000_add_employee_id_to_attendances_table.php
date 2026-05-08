<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (Schema::hasColumn('attendances', 'employee_id')) {
            return;
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('id');
        });

        if (Schema::hasColumn('attendances', 'user_id')) {
            DB::table('attendances')->update([
                'employee_id' => DB::raw('user_id'),
            ]);
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
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
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
};
