<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign the 'user' role to the newly created user
      //  $user->assignRole('user');

        return JWTAuth::fromUser($user);
    }

    public function login(array $credentials)
    {
       
      
      $token  = Auth::attempt($credentials);

      if (!$token) {
          throw new HttpResponseException(ApiResponseService::error('Validation errors', 422,null));
      }

      $user = Auth::user();
      return [
          'user' => $user,
          'token' => $token,
      ];
      
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
