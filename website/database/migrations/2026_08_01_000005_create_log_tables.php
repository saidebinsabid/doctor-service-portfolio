<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /* ---------- যোগাযোগ ফর্মের বার্তা ---------- */
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index('is_read');
        });

        /* ---------- পাঠানো বার্তার লগ ----------
           কোন রোগীকে কখন কী পাঠানো হয়েছে তার হিসাব।
           পরে বিতর্ক হলে ("আমাকে তো জানানো হয়নি") প্রমাণ থাকে। */
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('channel', ['whatsapp', 'sms', 'email']);
            $table->string('recipient');
            $table->string('template', 60)->nullable();
            $table->text('body')->nullable();
            $table->enum('status', ['queued', 'sent', 'failed', 'manual'])->default('queued');
            $table->text('provider_response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['channel', 'status']);
        });

        /* ---------- অ্যাক্টিভিটি লগ ----------
           কে কখন কোন অ্যাপয়েন্টমেন্ট বা সেটিং বদলাল।
           একাধিক ম্যানেজার থাকলে জবাবদিহি নিশ্চিত হয়। */
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 60);                  // created|updated|cancelled|deleted
            $table->string('model_type', 80)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description')->nullable();
            $table->json('changes')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('message_logs');
        Schema::dropIfExists('contact_messages');
    }
};
