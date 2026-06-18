<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Menambah tipe pengumpulan: text, file, atau both
            $table->enum('submission_type', ['text', 'file', 'both'])->default('file')->after('due_date');
        });
    }

    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('submission_type');
        });
    }
};