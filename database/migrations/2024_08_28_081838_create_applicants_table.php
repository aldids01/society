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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('gender');
            $table->string('phone');
            $table->string('email');
            $table->string('address');
            $table->string('kin_name')->nullable();
            $table->string('kin_relationship')->nullable();
            $table->string('kin_phone')->nullable();
            $table->string('kin_address')->nullable();
            $table->decimal('saving', 10, 2);
            $table->enum('status', ['active', 'inactive', 'withdrawn'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
