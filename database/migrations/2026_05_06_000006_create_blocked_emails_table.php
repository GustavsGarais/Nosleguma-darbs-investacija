<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_emails', function (Blueprint $table) {
            $table->id();
            $table->string('match_type', 10); // email|domain
            $table->string('pattern', 255);
            $table->string('note', 255)->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['match_type', 'pattern']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_emails');
    }
};

