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
        Schema::table('quotes', function (Blueprint $table) {
            $table->unsignedTinyInteger('customer_rating')->nullable()->after('status');
            $table->text('customer_review')->nullable()->after('customer_rating');
            $table->timestamp('rated_at')->nullable()->after('customer_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['customer_rating', 'customer_review', 'rated_at']);
        });
    }
};

