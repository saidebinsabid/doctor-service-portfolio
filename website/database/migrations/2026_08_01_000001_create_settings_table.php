<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        | সাইটের সব একক তথ্য — ডাক্তারের নাম, ডিগ্রি, ফোন, ফি, সোশ্যাল লিংক।
        | key/value গঠন, যাতে নতুন ফিল্ড যোগ করতে মাইগ্রেশন লাগে না।
        |
        | দ্বিভাষিক মানের জন্য value_bn ও value_en আলাদা।
        | যে মানের ভাষা নেই (যেমন ফোন নম্বর) সেগুলো value_bn-এ থাকে।
        */
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value_bn')->nullable();
            $table->text('value_en')->nullable();

            /* অ্যাডমিন প্যানেলে ফর্ম সাজানোর জন্য */
            $table->string('group', 40)->default('general');
            $table->string('type', 20)->default('text');   // text|textarea|image|boolean|number
            $table->string('label_bn')->nullable();
            $table->string('label_en')->nullable();
            $table->text('hint_bn')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['group', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
