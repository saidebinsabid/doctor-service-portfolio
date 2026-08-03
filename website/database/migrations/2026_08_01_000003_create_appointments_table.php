<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            /* রোগীকে দেওয়া কোড — ASF-260805-04 */
            $table->string('booking_code', 20)->unique();

            $table->foreignId('chamber_id')->constrained()->cascadeOnDelete();
            $table->date('appointment_date');
            $table->time('slot_time');
            $table->unsignedSmallInteger('serial_no');

            /*
            |----------------------------------------------------------------
            | ⭐ স্লট দখলের চাবি (hold key)
            |----------------------------------------------------------------
            | সমস্যা: শুধু (chamber, date, serial) এর উপর unique index দিলে
            |         বাতিল করা সিরিয়ালটি চিরতরে আটকে থাকে — অন্য রোগী
            |         আর ওই সময়ে বুক করতে পারেন না।
            |
            | সমাধান: দখলটা আলাদা নালযোগ্য কলামে রাখা।
            |         বুকিং সক্রিয় থাকলে   → "2026-08-05|10:54"
            |         বাতিল / অনুপস্থিত হলে → NULL
            |
            |         MySQL ও SQLite দুটোতেই unique index একাধিক NULL মানে,
            |         তাই স্লটটি সাথে সাথেই আবার খালি হয়ে যায়।
            |         আর appointment_date / slot_time / serial_no অক্ষত থাকে,
            |         ফলে বাতিল হওয়া বুকিংয়ের পূর্ণ ইতিহাস হারায় না।
            */
            $table->string('slot_hold', 40)->nullable();
            $table->string('serial_hold', 40)->nullable();

            /* ---- রোগীর তথ্য ----
               ⚠️ সংবেদনশীল স্বাস্থ্য তথ্য। শুধু প্রয়োজনীয়টুকু নেওয়া হচ্ছে,
                  অ্যাডমিন প্যানেল ছাড়া কোথাও দেখানো হয় না। */
            $table->string('patient_name');
            $table->string('patient_phone', 20);
            $table->unsignedSmallInteger('patient_age')->nullable();
            $table->enum('patient_age_unit', ['day', 'month', 'year'])->default('year');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('address')->nullable();

            $table->enum('visit_type', ['new', 'followup', 'report'])->default('new');
            $table->text('problem')->nullable();

            $table->enum('status', [
                'pending',      // রোগী বুক করেছেন, অ্যাডমিন এখনো দেখেননি
                'confirmed',    // অ্যাডমিন নিশ্চিত করেছেন
                'completed',    // রোগী এসেছেন, দেখা হয়েছে
                'cancelled',    // বাতিল  → স্লট ছেড়ে দেওয়া হয়
                'no_show',      // সিরিয়াল নিয়েও আসেননি → স্লট ছেড়ে দেওয়া হয়
            ])->default('pending');

            $table->text('admin_note')->nullable();

            /* web    → ওয়েবসাইট থেকে
               manual → অ্যাডমিন নিজে যোগ করেছেন (ফোনে আসা সিরিয়াল) */
            $table->enum('source', ['web', 'manual', 'whatsapp'])->default('web');

            $table->string('locale', 5)->default('bn');   // রোগী কোন ভাষায় বুক করেছেন
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            /*
            | ডাটাবেস স্তরে ডাবল-বুকিং প্রতিরোধ।
            | দুইজন রোগী একই মুহূর্তে একই স্লট নিলে ডাটাবেসই একজনকে আটকে দেবে —
            | অ্যাপ্লিকেশন কোডের উপর ভরসা করতে হবে না।
            | BookingService-এর lockForUpdate() এর সাথে মিলে দুই স্তরের সুরক্ষা।
            */
            $table->unique(['chamber_id', 'slot_hold'], 'uniq_slot_hold');
            $table->unique(['chamber_id', 'serial_hold'], 'uniq_serial_hold');

            $table->index(['appointment_date', 'status']);
            $table->index(['chamber_id', 'appointment_date']);
            $table->index('patient_phone');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
