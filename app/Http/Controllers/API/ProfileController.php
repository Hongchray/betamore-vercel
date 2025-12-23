<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Profile retrieved successfully.',
            'user' => [
                'customer_id' => $user->user_id,
                'phone' => $user->phone,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'gender' => $user->gender,
                'type' => $user->type,
                'image' => $user->image,
                'telegram' => $user->telegram,
                'phone_verified_at' => $user->phone_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ], 200);
    }

    /**
     * Update User Profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'gender' => ['nullable', 'in:male,female,other'],
            'image' => ['nullable', 'string'],
            'telegram' => ['nullable', 'string', 'max:255', 'unique:users,telegram,' . $user->id],
        ]);

        // Update user with validated data
        $user->update(array_filter($validated)); // array_filter removes null values

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => [
                'customer_id' => $user->user_id,
                'phone' => $user->phone,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'gender' => $user->gender,
                'type' => $user->type,
                'image' => $user->image,
                'telegram' => $user->telegram,
                'phone_verified_at' => $user->phone_verified_at,
                'updated_at' => $user->updated_at,
            ]
        ], 200);
    }
}
