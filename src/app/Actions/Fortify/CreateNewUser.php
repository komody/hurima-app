<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // RegisterRequestのルールを使用してバリデーション
        $rules = (new RegisterRequest())->rules();
        Validator::make($input, $rules, (new RegisterRequest())->messages())->validate();

        // ユーザーを作成（postal_codeとaddressはnull）
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'postal_code' => null,
            'address' => null,
        ]);
    }
}
