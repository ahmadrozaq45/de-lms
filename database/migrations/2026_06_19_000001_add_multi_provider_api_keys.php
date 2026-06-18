<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah key per provider. Jika key lama ai_api_key ada isinya,
        // salin ke anthropic (provider default).
        $oldKey = DB::table('settings')->where('key', 'ai_api_key')->value('value') ?? '';

        $newKeys = [
            'ai_api_key_anthropic' => $oldKey, // warisi dari key lama
            'ai_api_key_gemini'    => '',
            'ai_api_key_groq'      => '',
            'ai_api_key_openai'    => '',
            'ai_model_anthropic'   => 'claude-sonnet-4-6',
            'ai_model_gemini'      => 'gemini-1.5-flash',
            'ai_model_groq'        => 'llama-3.1-8b-instant',
            'ai_model_openai'      => 'gpt-4o-mini',
        ];

        foreach ($newKeys as $key => $value) {
            $exists = DB::table('settings')->where('key', $key)->exists();
            if (!$exists) {
                DB::table('settings')->insert([
                    'key'        => $key,
                    'value'      => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Hapus key lama yang kini digantikan per-provider
        DB::table('settings')->whereIn('key', ['ai_api_key', 'ai_model'])->delete();
    }

    public function down(): void
    {
        // Kembalikan ke struktur lama
        $anthropicKey = DB::table('settings')->where('key', 'ai_api_key_anthropic')->value('value') ?? '';
        $anthropicModel = DB::table('settings')->where('key', 'ai_model_anthropic')->value('value') ?? 'claude-sonnet-4-6';

        DB::table('settings')->whereIn('key', [
            'ai_api_key_anthropic', 'ai_api_key_gemini',
            'ai_api_key_groq', 'ai_api_key_openai',
            'ai_model_anthropic', 'ai_model_gemini',
            'ai_model_groq', 'ai_model_openai',
        ])->delete();

        DB::table('settings')->insert([
            ['key' => 'ai_api_key', 'value' => $anthropicKey,   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_model',   'value' => $anthropicModel,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
};