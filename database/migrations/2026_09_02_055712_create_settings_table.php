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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('org_name')->default('PPID');
            $table->string('org_email')->nullable();
            $table->string('org_phone')->nullable();
            $table->text('org_address')->nullable();
            $table->unsignedInteger('response_deadline_days')->default(10);
            $table->text('banner_text')->nullable();
            $table->boolean('banner_is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
