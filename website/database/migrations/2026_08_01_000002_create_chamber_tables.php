<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /* ---------- চেম্বার ----------
           এখন একটি (ইবনে সিনা, বাড্ডা), কিন্তু একাধিক সাপোর্ট করে —
           ডাক্তার দ্বিতীয় চেম্বার যোগ করলে কোড বদলাতে হবে না। */
        Schema::create('chambers', function (Blueprint $table) {
            $table->id();
            $table->string('name_bn');
            $table->string('name_en')->nullable();
            $table->text('address_bn');
            $table->text('address_en')->nullable();
            $table->string('hotline', 20)->nullable();
            $table->string('map_query')->nullable();      // Google Maps-এ খোঁজার টেক্সট
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        /* ---------- সাপ্তাহিক সময়সূচি ----------
           সময় নেওয়া হয়েছে ✅ ভিজিটিং কার্ড থেকে:
             শনি–বৃহস্পতি  ১০:৩০ – ২:০০
             শুক্রবার      ৫:০০ – ৮:০০

           ⚠️ কোথাও হার্ডকোড নেই — অ্যাডমিন প্যানেল থেকে বদলানো যাবে। */
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chamber_id')->constrained()->cascadeOnDelete();

            /* 0=রবি, 1=সোম, 2=মঙ্গল, 3=বুধ, 4=বৃহস্পতি, 5=শুক্র, 6=শনি */
            $table->unsignedTinyInteger('day_of_week');

            $table->time('start_time');
            $table->time('end_time');

            /* প্রতি রোগীর জন্য বরাদ্দ মিনিট।
               শনি–বৃহস্পতি: ২১০ মিনিট ÷ ২৫ = ৮ মিনিট
               শুক্রবার:     ১৮০ মিনিট ÷ ২৫ = ৭ মিনিট */
            $table->unsignedSmallInteger('slot_minutes')->default(8);
            $table->unsignedSmallInteger('max_serials')->default(25);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            /* একই চেম্বারে একই বারে দুটি সময়সূচি থাকতে পারবে না */
            $table->unique(['chamber_id', 'day_of_week']);
        });

        /* ---------- ছুটি ও সময় পরিবর্তন ----------
           chamber_id নাল হলে সব চেম্বারে প্রযোজ্য (সাধারণ ছুটি)। */
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chamber_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('date');

            /* closed      → ওই দিন চেম্বার বন্ধ
               custom_time → ওই দিন ভিন্ন সময়ে বসবেন */
            $table->enum('type', ['closed', 'custom_time'])->default('closed');
            $table->time('custom_start')->nullable();
            $table->time('custom_end')->nullable();
            $table->unsignedSmallInteger('custom_max_serials')->nullable();

            $table->string('reason_bn')->nullable();
            $table->string('reason_en')->nullable();
            $table->timestamps();

            $table->unique(['chamber_id', 'date']);
            $table->index('date');
        });

        /* ---------- নির্দিষ্ট স্লট ব্লক ----------
           পুরো দিন বন্ধ না করে কয়েকটি সিরিয়াল আটকে রাখার জন্য
           (যেমন: জরুরি রোগী বা ব্যক্তিগত কাজ)। */
        Schema::create('blocked_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chamber_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('slot_time');
            $table->string('reason')->nullable();
            $table->foreignId('blocked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['chamber_id', 'date', 'slot_time']);
            $table->index(['chamber_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_slots');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('chambers');
    }
};
