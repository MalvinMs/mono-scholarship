<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('predecessor_scholarship_id')->nullable()->constrained('scholarships')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('academic_year', 20)->nullable();
            $table->bigInteger('fund_amount')->nullable();
            $table->smallInteger('quota_primary');
            $table->smallInteger('quota_reserve')->default(0);
            $table->smallInteger('quota_renewal_locked')->default(0);
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_verification_enabled')->default(true);
            $table->jsonb('notification_channels')->nullable();
            $table->jsonb('notification_templates')->nullable();
            $table->string('otp_channel')->default('whatsapp');
            $table->decimal('min_gpa_renewal', 3, 2)->default(3.50);
            $table->string('scoring_display_mode')->default('absolute');
            $table->jsonb('tiebreaker_config')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
