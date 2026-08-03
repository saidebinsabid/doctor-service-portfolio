<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
    | সব কনটেন্ট টেবিলে দুটি কলাম সবসময় থাকে:
    |   sort_order → অ্যাডমিন প্যানেলে ড্র্যাগ করে ক্রম বদলানোর জন্য
    |   is_active  → না মুছে সাময়িকভাবে লুকিয়ে রাখার জন্য
    |
    | কোনো টেবিলে একটিও সক্রিয় সারি না থাকলে ওয়েবসাইটে
    | সেই সেকশনটি নিজে থেকেই লুকিয়ে যায়।
    */
    public function up(): void
    {
        /* ---------- সেবাসমূহ ---------- */
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title_bn');
            $table->string('title_en')->nullable();
            $table->text('description_bn')->nullable();
            $table->text('description_en')->nullable();

            $table->string('icon', 30)->default('stetho');
            $table->string('tone', 20)->default('brand');   // আইকনের রঙ

            /* সাধারণ সেবা (১৪টি) বনাম বিশেষ চিকিৎসা (৪টি) */
            $table->boolean('is_special')->default(false);

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_special', 'is_active', 'sort_order']);
        });

        /* ---------- পেশাগত অভিজ্ঞতা ---------- */
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('position_bn');
            $table->string('position_en')->nullable();
            $table->string('organization_bn');
            $table->string('organization_en')->nullable();
            $table->string('period', 60)->nullable();       // "২০১৮ – ২০২২", ঐচ্ছিক
            $table->string('icon', 30)->default('building');
            $table->boolean('is_current')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /* ---------- শিক্ষাগত যোগ্যতা ---------- */
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->string('degree_bn');
            $table->string('degree_en')->nullable();
            $table->string('institution_bn')->nullable();
            $table->string('institution_en')->nullable();
            $table->string('year', 20)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /* ---------- রোগীদের মতামত ----------
           is_approved ডিফল্ট false — অ্যাডমিন না দেখা পর্যন্ত প্রকাশ হবে না। */
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('location')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();   // ১–৫
            $table->text('comment_bn');
            $table->text('comment_en')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_approved', 'sort_order']);
        });

        /* ---------- গ্যালারি ---------- */
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['photo', 'video'])->default('photo');
            $table->string('title_bn')->nullable();
            $table->string('title_en')->nullable();
            $table->string('file_path')->nullable();        // ছবির জন্য
            $table->string('thumb_path')->nullable();
            $table->string('youtube_url')->nullable();      // ভিডিওর জন্য
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /* ---------- নোটিশ ----------
           "চেম্বার বন্ধ থাকবে" / "সময় পরিবর্তন" — হোমপেজের উপরে ব্যানার।
           starts_at ও ends_at দিয়ে নির্দিষ্ট সময়ের জন্য দেখানো যায়। */
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title_bn');
            $table->string('title_en')->nullable();
            $table->text('body_bn')->nullable();
            $table->text('body_en')->nullable();
            $table->enum('severity', ['info', 'warning', 'urgent'])->default('info');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });

        /* ---------- সচরাচর জিজ্ঞাসা ---------- */
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question_bn');
            $table->string('question_en')->nullable();
            $table->text('answer_bn');
            $table->text('answer_en')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('qualifications');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('services');
    }
};
