<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('qualification_id')->constrained()->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('qualification_options')->nullOnDelete();
            $table->jsonb('selected_option_ids')->nullable();
            $table->decimal('numeric_value', 10, 2)->nullable();
            $table->text('text_value')->nullable();
            $table->smallInteger('computed_score')->default(0);
            $table->boolean('is_corrected_by_verifier')->default(false);
            $table->foreignId('original_selected_option_id')->nullable()->constrained('qualification_options')->nullOnDelete();
            $table->decimal('original_numeric_value', 10, 2)->nullable();
            $table->timestamp('corrected_at')->nullable();
            $table->foreignId('corrected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['application_id', 'qualification_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_answers');
    }
};
