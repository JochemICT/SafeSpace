<?php

namespace App\Actions\Fortify;

use App\Models\Identity;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
            'username' => ['required', 'string', 'max:50', Rule::unique(Identity::class, 'username')],
            'communities' => ['nullable', 'array'],
        ])->validate();


        $user = User::create([
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $user->identity()->create([
            'username' => $input['username'],
            'avatar' => $input['avatar'] ?? null,
        ]);

        return $user;
    }
}
