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
        Schema::create('category_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('organisation_categories')
                ->cascadeOnDelete();

            $table->string('field_label'); // Example: Material Type
            $table->string('field_name');  // Example: material_type

            $table->enum('field_type', [
                'text',
                'textarea',
                'number',
                'select',
                'radio',
                'checkbox',
                'file',
                'date',
                'time',
                'url',
                'color'
            ]);

            $table->longText('field_options')->nullable();
            // Example for select/radio:
            // Wood,Metal,Glass

            $table->boolean('is_required')->default(false);

            $table->integer('sort_order')->default(0);

            $table->boolean('status')->default(true);

            $table->string('placeholder')->nullable();

            $table->string('validation_rules')->nullable();
            $table->text('help_text')->nullable();

            $table->string('default_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_fields');
    }
};
