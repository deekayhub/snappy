<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->date('estimated_completion_date')->nullable()->after('notes');
            $table->integer('warranty_months')->nullable()->after('estimated_completion_date');
            $table->text('terms')->nullable()->after('warranty_months');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['estimated_completion_date', 'warranty_months', 'terms']);
        });
    }
};
