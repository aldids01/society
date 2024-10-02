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
        Schema::create('approved_grains', function (Blueprint $table) {
            $table->id();
            $table->string('grain_id')->index();
            $table->foreign('grain_id')->references('slug')->on('grains')->cascadeOnDelete();
            $table->string('checkedby')->index()->nullable();
            $table->foreign('checkedby')->references('staff_id')->on('applicants')->cascadeOnDelete();
            $table->timestamp('checkeddate')->nullable();
            $table->string('approvedby')->index()->nullable();
            $table->foreign('approvedby')->references('staff_id')->on('applicants')->cascadeOnDelete();
            $table->timestamp('approveddate')->nullable();
            $table->string('disbursedby')->index()->nullable();
            $table->foreign('disbursedby')->references('staff_id')->on('applicants')->cascadeOnDelete();
            $table->timestamp('disburseddate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_grains');
    }
};
