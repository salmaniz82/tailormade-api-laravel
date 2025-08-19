<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use \App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function createAdminUser() : jsonResponse {


        $existingUser = User::where('email', 'sam@webential.co.uk')->first();

        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'Admin user already exists',
            ], 409); // Conflict
        }

        try {

            $user = User::create([
                'name' => 'Admin',
                'email' => 'sam@webential.co.uk',
                'password' => Hash::make('test@123456'),
                'role_id' => 1,
                'isActive' => true,
            ]);

            if($user->wasRecentlyCreated) {
                return response()->json([
                    'message' => 'Admin user created successfully.',
                    'user' => $user,
                ], 201); // ✅ HTTP 201 Created
            }

            return response()->json([
                'message' => 'Admin user already exists.',
                'user' => $user,
            ], 200); // ✅ HTTP 200 OK


        }
        catch(\Thowable $e) {

            return response()->json([
                'message' => 'Failed to create admin user.',
                'error' => $e->getMessage(),
            ], 500); // ✅ HTTP 500 Internal Server Error

        }


    }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // Regenerate session to prevent fixation
            $request->session()->regenerate();

            return redirect()->intended('/dashboard'); 
            // will redirect to intended page, defaults to /dashboard
        }

        return back()->withErrors([
            'email' => 'Invalid credentials provided.',
        ])->onlyInput('email');
    }

     // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showLogin(){


        return view('auth/login');

    }

}
