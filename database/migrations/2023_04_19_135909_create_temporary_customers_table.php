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
        if (!Schema::hasTable('temporary_customers')) {
            Schema::create('temporary_customers', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email');
                $table->string('slug')->unique();
                $table->enum('status', [
                    'CREATE',
                    'DELETE',
                    'UPDATE'
                ])->default('CREATE');
                $table->enum('approval_status', [
                    'PENDING',
                    'APPROVED',
                    'CANCELED'
                ])->default('PENDING');
                $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_customers');
    }
};
