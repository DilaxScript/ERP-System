<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('attendances', 'date')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->date('date')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('attendances', 'date') ? 'date' : null,
            ]);

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
