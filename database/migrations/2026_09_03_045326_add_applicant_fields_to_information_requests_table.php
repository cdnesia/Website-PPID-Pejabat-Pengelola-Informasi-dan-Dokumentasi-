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
        Schema::table('information_requests', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();

            $table->string('applicant_name')->nullable()->after('user_id');
            $table->string('applicant_nik')->nullable()->after('applicant_name');
            $table->string('applicant_occupation')->nullable()->after('applicant_nik');
            $table->string('applicant_phone')->nullable()->after('applicant_occupation');
            $table->string('applicant_email')->nullable()->after('applicant_phone');
            $table->text('applicant_address')->nullable()->after('applicant_email');
            $table->string('delivery_method')->nullable()->after('format_requested');
            $table->string('response_delivery_method')->nullable()->after('delivery_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('information_requests', function (Blueprint $table) {
            $table->dropColumn([
                'applicant_name', 'applicant_nik', 'applicant_occupation',
                'applicant_phone', 'applicant_email', 'applicant_address',
                'delivery_method', 'response_delivery_method',
            ]);

            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
