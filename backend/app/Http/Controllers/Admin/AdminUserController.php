<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users,email',
            'password'        => 'required|string|min:8|max:255|confirmed',
            'is_superadmin'   => 'sometimes|boolean',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => 'admin',
            'password' => Hash::make($validated['password']),
        ]);

        $admin = Admin::create([
            'user_id'         => $user->id,
            'is_superadmin'   => (bool)($validated['is_superadmin'] ?? false),
        ]);

        return response()->json([
            'message' => 'Admin created.',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
            'admin' => $admin,
        ], 201);
    }
}

