<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id(); // Primary key

            // User reference
            $table->unsignedBigInteger('user_id'); // Foreign key to users table

            // Leave period
            $table->date('from_date')->comment('Leave starting date');
            $table->date('to_date')->comment('Leave ending date');

            // Reason and status
            $table->string('reason')->comment('Reason for leave');
            $table->string('status')->default('Pending')->comment('Leave status');

            // Number of days
            $table->integer('number_of_days')->default(0)->comment('Total number of leave days');

            // New: attachment column for uploaded files
            $table->string('attachment')->nullable()->comment('Optional leave attachment filename');

            // Timestamps
            $table->timestamps();

            // Foreign key constraint: user_id references users(id)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
