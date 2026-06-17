<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->jsonb('score_breakdown')->nullable();
            $table->smallInteger('total_score')->default(0);
            $table->smallInteger('max_possible_score');
            $table->integer('rank')->nullable();
            $table->jsonb('tiebreaker_log')->nullable();
            $table->string('selection_result')->nullable();
            $table->boolean('is_final')->default(false);
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('calculated_at')->nullable();
        });

        DB::statement('CREATE INDEX idx_scores_ranking ON application_scores(scholarship_id, total_score DESC, rank) WHERE is_final = true');
    }

    public function down(): void
    {
        Schema::dropIfExists('application_scores');
    }
};
