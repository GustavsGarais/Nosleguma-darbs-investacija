<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject', 255);
            $table->string('contact_email', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('error_type', 50)->nullable();

            $table->string('status', 20)->default('open')->index();
            $table->string('priority', 20)->default('medium')->index();

            $table->text('admin_response')->nullable();
            $table->foreignId('assigned_to')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};

