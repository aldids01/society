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
        Schema::create('grains', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable()->index();
            $table->string('applicant_id')->nullable()->index();
            $table->foreign('applicant_id')->references('staff_id')->on('applicants')->onDelete('cascade');
            $table->decimal('rate', 5, 2);
            $table->integer('terms');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'checked', 'rejected', 'disbursed'])->default('pending');
            $table->date('start_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grains');
    }
};
