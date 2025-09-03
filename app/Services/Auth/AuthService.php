<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function registerUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->token = $this->createToken($user);

        return $user;

    }

    /**
     * @throws \Exception
     */
    public function loginUser(string $email, string $password): User
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new \Exception("Invalid credentials");
        }

        $user->token = $this->createToken($user);

        return $user;
    }

    private function createToken(User $user): string
    {
        return $user->createToken('api')->plainTextToken;
    }

}
