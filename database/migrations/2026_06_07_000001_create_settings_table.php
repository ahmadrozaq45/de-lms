<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Default values
        DB::table('settings')->insert([
            // API
            ['key' => 'ai_provider',        'value' => 'anthropic',     'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_api_key',         'value' => '',              'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_model',           'value' => 'claude-sonnet-4-6', 'created_at' => now(), 'updated_at' => now()],

            // Theme
            ['key' => 'theme_color',        'value' => '#3b5bdb',       'created_at' => now(), 'updated_at' => now()],
            ['key' => 'theme_mode',         'value' => 'light',         'created_at' => now(), 'updated_at' => now()],

            // Landing page
            ['key' => 'lp_title',           'value' => 'DE-LMS Platform', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'lp_subtitle',        'value' => 'Platform belajar online yang modern dan analitik.', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'lp_show_courses',    'value' => '1',             'created_at' => now(), 'updated_at' => now()],

            // Certificate
            ['key' => 'cert_enabled',       'value' => '0',             'created_at' => now(), 'updated_at' => now()],
            ['key' => 'cert_issuer_name',   'value' => 'DE-LMS',        'created_at' => now(), 'updated_at' => now()],
            ['key' => 'cert_footer_text',   'value' => 'Sertifikat ini diberikan sebagai bukti penyelesaian kursus.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};