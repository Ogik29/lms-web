<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->boolean('is_edited')->default(false)->after('submitted_at');
            $table->timestamp('edited_at')->nullable()->after('is_edited');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['is_edited', 'edited_at']);
        });
    }
};
