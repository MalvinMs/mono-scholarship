<?php

namespace App\Actions\Application;

use App\Models\User;

final class SnapshotApplicantProfile
{
    public function execute(User $user): array
    {
        return [
            'name' => $user->name,
            'nik' => $user->nik,
            'email' => $user->email,
            'phone' => $user->phone,
            'birth_date' => $user->birth_date?->format('Y-m-d'),
            'birth_place' => $user->birth_place,
            'address' => $user->address,
            'village' => $user->village,
            'district' => $user->district,
            'city' => $user->city,
            'province' => $user->province,
            'education_level' => $user->education_level,
            'school_name' => $user->school_name,
            'nisn' => $user->nisn,
            'university_name' => $user->university_name,
            'major' => $user->major,
            'current_semester' => $user->current_semester,
            'snapshot_at' => now()->toISOString(),
        ];
    }
}
