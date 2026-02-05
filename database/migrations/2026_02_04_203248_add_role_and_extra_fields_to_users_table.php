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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer'); // customer | supplier
            $table->string('phone')->nullable();
            $table->string('organisation')->nullable();

            // Supplier only
            $table->string('company_name')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('review_link')->nullable();
            $table->string('social_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'phone',
                'organisation',
                'company_name',
                'category',
                'address',
                'website',
                'review_link',
                'social_link',
            ]);
        });
    }
};
