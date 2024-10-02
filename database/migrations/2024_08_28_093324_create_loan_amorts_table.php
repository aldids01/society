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
        Schema::create('loan_amorts', function (Blueprint $table) {
            $table->id();
            $table->string('loan_id')->index()->nullable();
            $table->foreign('loan_id')->references('slug')->on('loans')->cascadeOnDelete();
            $table->string('loan_owner')->index()->nullable();
            $table->foreign('loan_owner')->references('staff_id')->on('applicants')->cascadeOnDelete();
            $table->year('annual')->nullable();
            $table->string('period')->nullable();
            $table->decimal('interest', 15, 2)->nullable();
            $table->decimal('principal', 15, 2)->nullable();
            $table->decimal('payment', 15, 2)->nullable();
            $table->decimal('start_balance', 15, 2)->nullable();
            $table->decimal('end_balance', 15, 2)->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_amorts');
    }
};
