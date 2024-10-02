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
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_id')->index();
            $table->foreign('applicant_id')->references('staff_id')->on('applicants')->cascadeOnDelete();
            $table->year('annual');
            $table->decimal('January', 15, 2)->default(0);
            $table->decimal('February', 15, 2)->default(0);
            $table->decimal('March', 15, 2)->default(0);
            $table->decimal('April', 15, 2)->default(0);
            $table->decimal('May', 15, 2)->default(0);
            $table->decimal('June', 15, 2)->default(0);
            $table->decimal('July', 15, 2)->default(0);
            $table->decimal('August', 15, 2)->default(0);
            $table->decimal('September', 15, 2)->default(0);
            $table->decimal('October', 15, 2)->default(0);
            $table->decimal('November', 15, 2)->default(0);
            $table->decimal('December', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->virtualAs('January + February + March + April + May + June + July + August + September + October + November + December');
            $table->enum('status', ['active', 'inactive', 'withdrawn'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};
