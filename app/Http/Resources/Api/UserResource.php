<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'nik' => $this->nik,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'birth_place' => $this->birth_place,
            'address' => $this->address,
            'village' => $this->village,
            'district' => $this->district,
            'city' => $this->city,
            'province' => $this->province,
            'education_level' => $this->education_level,
            'school_name' => $this->school_name,
            'nisn' => $this->nisn,
            'university_name' => $this->university_name,
            'major' => $this->major,
            'current_semester' => $this->current_semester,
            'is_active' => $this->is_active,
            'is_blacklisted' => $this->is_blacklisted,
            'roles' => $this->getRoleNames(),
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'phone_verified_at' => $this->phone_verified_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
