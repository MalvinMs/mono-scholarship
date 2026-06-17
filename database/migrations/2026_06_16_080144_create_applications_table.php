<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('registration_number', 50)->unique();
            $table->jsonb('snapshot_profile')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_renewal')->default(false);
            $table->foreignId('previous_application_id')->nullable()->constrained('applications')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('selected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        DB::statement("CREATE UNIQUE INDEX idx_applications_unique_active ON applications(scholarship_id, user_id) WHERE status != 'draft'");
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
