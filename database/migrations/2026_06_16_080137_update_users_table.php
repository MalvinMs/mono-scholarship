<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('nik')->nullable()->unique()->after('name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->text('address')->nullable();
            $table->string('village')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('education_level')->nullable();
            $table->string('school_name')->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('university_name')->nullable();
            $table->string('major')->nullable();
            $table->smallInteger('current_semester')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_blacklisted')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nik', 'phone', 'phone_verified_at', 'birth_date', 'birth_place',
                'address', 'village', 'district', 'city', 'province',
                'education_level', 'school_name', 'nisn', 'university_name',
                'major', 'current_semester', 'is_active', 'is_blacklisted',
            ]);
        });
    }
};
