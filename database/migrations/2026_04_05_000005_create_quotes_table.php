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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_job_id')->constrained('customer_jobs')->cascadeOnDelete();
            $table->foreignId('supplier_user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('delivery_cost', 10, 2)->default(0);
            $table->decimal('discount_offered', 10, 2)->default(0);
            $table->decimal('price_for_job', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->string('status')->default('submitted');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['customer_job_id', 'supplier_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
