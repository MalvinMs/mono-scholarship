<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'village' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'education_level' => ['nullable', 'string', 'in:SMA,SMK,MA,PAKET_C,D3,D4,S1,S2'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'university_name' => ['nullable', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'current_semester' => ['nullable', 'integer', 'min:1', 'max:14'],
        ])->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'] ?? $user->phone,
                'birth_date' => $input['birth_date'] ?? $user->birth_date,
                'birth_place' => $input['birth_place'] ?? $user->birth_place,
                'address' => $input['address'] ?? $user->address,
                'village' => $input['village'] ?? $user->village,
                'district' => $input['district'] ?? $user->district,
                'city' => $input['city'] ?? $user->city,
                'province' => $input['province'] ?? $user->province,
                'education_level' => $input['education_level'] ?? $user->education_level,
                'school_name' => $input['school_name'] ?? $user->school_name,
                'nisn' => $input['nisn'] ?? $user->nisn,
                'university_name' => $input['university_name'] ?? $user->university_name,
                'major' => $input['major'] ?? $user->major,
                'current_semester' => $input['current_semester'] ?? $user->current_semester,
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
            'phone' => $input['phone'] ?? $user->phone,
            'birth_date' => $input['birth_date'] ?? $user->birth_date,
            'birth_place' => $input['birth_place'] ?? $user->birth_place,
            'address' => $input['address'] ?? $user->address,
            'village' => $input['village'] ?? $user->village,
            'district' => $input['district'] ?? $user->district,
            'city' => $input['city'] ?? $user->city,
            'province' => $input['province'] ?? $user->province,
            'education_level' => $input['education_level'] ?? $user->education_level,
            'school_name' => $input['school_name'] ?? $user->school_name,
            'nisn' => $input['nisn'] ?? $user->nisn,
            'university_name' => $input['university_name'] ?? $user->university_name,
            'major' => $input['major'] ?? $user->major,
            'current_semester' => $input['current_semester'] ?? $user->current_semester,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
