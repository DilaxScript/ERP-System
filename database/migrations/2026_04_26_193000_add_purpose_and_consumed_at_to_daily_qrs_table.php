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
        Schema::table('daily_qrs', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_qrs', 'purpose')) {
                $table->string('purpose')->default('login')->after('token');
            }

            if (!Schema::hasColumn('daily_qrs', 'consumed_at')) {
                $table->timestamp('consumed_at')->nullable()->after('purpose');
            }
        });

        DB::table('daily_qrs')->whereNull('purpose')->update([
            'purpose' => 'login',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_qrs', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('daily_qrs', 'consumed_at') ? 'consumed_at' : null,
                Schema::hasColumn('daily_qrs', 'purpose') ? 'purpose' : null,
            ]);

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
