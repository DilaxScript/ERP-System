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
        if (Schema::hasColumn('attendances', 'employee_id') && Schema::hasColumn('attendances', 'user_id')) {
            DB::table('attendances')
                ->whereNull('employee_id')
                ->update([
                    'employee_id' => DB::raw('user_id'),
                ]);
        }

        if (Schema::hasColumn('attendances', 'login_time')) {
            DB::table('attendances')
                ->whereNull('login_time')
                ->whereNotNull('created_at')
                ->update([
                    'login_time' => DB::raw('created_at'),
                ]);
        }

        if (Schema::hasColumn('attendances', 'date')) {
            DB::table('attendances')
                ->whereNull('date')
                ->whereNotNull('created_at')
                ->update([
                    'date' => DB::raw('DATE(created_at)'),
                ]);
        }

        DB::statement('ALTER TABLE attendances DROP CONSTRAINT IF EXISTS attendances_user_id_foreign');

        Schema::table('attendances', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('attendances', 'user_id') ? 'user_id' : null,
                Schema::hasColumn('attendances', 'scanned_at') ? 'scanned_at' : null,
                Schema::hasColumn('attendances', 'secret') ? 'secret' : null,
            ]);

            if ($columns) {
                $table->dropColumn($columns);
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
            if (!Schema::hasColumn('attendances', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }

            if (!Schema::hasColumn('attendances', 'scanned_at')) {
                $table->timestamp('scanned_at')->nullable();
            }

            if (!Schema::hasColumn('attendances', 'secret')) {
                $table->string('secret')->nullable();
            }
        });
    }
};
