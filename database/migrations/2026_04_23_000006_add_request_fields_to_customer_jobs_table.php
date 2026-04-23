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
        Schema::table('customer_jobs', function (Blueprint $table) {
            $table->boolean('delivery_in_uk')->default(true)->after('budget');
            $table->boolean('personalisation_required')->default(false)->after('delivery_in_uk');
            $table->string('personalisation_mode')->nullable()->after('personalisation_required');
            $table->string('supplier_target_type')->default('all')->after('personalisation_mode');
            $table->unsignedInteger('supplier_target_count')->nullable()->after('supplier_target_type');
            $table->text('notes')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_in_uk',
                'personalisation_required',
                'personalisation_mode',
                'supplier_target_type',
                'supplier_target_count',
                'notes',
            ]);
        });
    }
};
