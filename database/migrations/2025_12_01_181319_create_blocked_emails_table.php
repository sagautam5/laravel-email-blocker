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
        Schema::create(config('email-blocker.log_table', 'blocked_emails'), function (Blueprint $table) {
            $table->id();
            $table->string('mailable')->nullable()->index();
            $table->string('subject')->nullable();

            $table->string('from_name')->nullable();
            $table->string('from_email');

            $table->string('email')->index();
            $table->longText('content')->nullable();

            $table->string('rule')->nullable()->index();
            $table->string('reason')->nullable();
            $table->enum('receiver_type', ['to', 'cc', 'bcc']);

            $table->timestamp('blocked_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('email-blocker.log_table', 'blocked_emails'));
    }
};
