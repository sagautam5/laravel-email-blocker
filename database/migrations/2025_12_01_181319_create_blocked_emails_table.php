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
        Schema::create('blocked_emails', function (Blueprint $table) {
            $table->id();
            $table->string('mailable')->nullable();
            $table->string('email');
            $table->string('subject');
            $table->string('content');
            $table->string('from_name');
            $table->string('from_address');
            $table->string('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_emails');
    }
};
