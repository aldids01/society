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
        Schema::create('guarantors', function (Blueprint $table) {
            $table->id();
            $table->string('loan_id')->index();
            $table->foreign('loan_id')->references('slug')->on('loans')->cascadeOnDelete();
            $table->string('guarantor_name')->index();
            $table->foreign('guarantor_name')->references('staff_id')->on('applicants')->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('guarantor_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guarantors');
    }
};
