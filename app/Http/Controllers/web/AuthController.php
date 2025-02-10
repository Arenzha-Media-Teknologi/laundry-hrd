<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            // $credentials = $request->only('email', 'password');

            $username = $request->username;
            $password = $request->password;

            // if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            //     //user sent their email 
            //     Auth::attempt(['email' => $username, 'password' => $password]);
            // } else {
            //     //they sent their username instead 
            //     Auth::attempt(['username' => $username, 'password' => $password]);
            // }

            Auth::attempt(['username' => $username, 'password' => $password]);

            if (Auth::check()) {
                //send them where they are going 
                $request->session()->regenerate();

                $employee = Credential::find(Auth::id());

                // $userLoginPermissions = [];

                // if ($employee !== null) {
                //     $userLoginPermissions = json_decode($employee->role->role_permissions);
                // }

                // $request->session()->put('userLoginPermissions', $userLoginPermissions);
                // // return redirect()->intended('home');
                return response()->json([
                    'status' => 'OK',
                    'message' => 'logged on',
                    'code' => 200
                ]);
                // return redirect('/employee');
            }

            return response()->json([
                'message' => 'Username atau password salah',
                'code' => 400,
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
