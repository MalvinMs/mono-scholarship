<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE INDEX idx_users_blacklist ON users(is_blacklisted) WHERE is_blacklisted = true');
        DB::statement("CREATE INDEX idx_applications_verif_queue ON applications(scholarship_id, status) WHERE status IN ('submitted', 'under_review', 'needs_revision')");
        DB::statement('CREATE INDEX idx_scholarship_verifiers ON scholarship_verifiers(scholarship_id, user_id)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_users_blacklist');
        DB::statement('DROP INDEX IF EXISTS idx_applications_verif_queue');
        DB::statement('DROP INDEX IF EXISTS idx_scholarship_verifiers');
    }
};
