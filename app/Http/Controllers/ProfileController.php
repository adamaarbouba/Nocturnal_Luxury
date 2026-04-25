<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();

        return view('profile.show', [
            'user' => $user,
        ]);
    }

    public function edit(): View
    {
        $user = auth()->user();

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Name is required',
            'name.max' => 'Name cannot exceed 255 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email is already in use',
            'phone.max' => 'Phone number cannot exceed 20 characters',
            'address.max' => 'Address cannot exceed 500 characters',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    public function changePassword(): View
    {
        return view('profile.change-password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('The current password is incorrect.');
                    }
                },
            ],
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Current password is required',
            'new_password.required' => 'New password is required',
            'new_password.min' => 'Password must be at least 8 characters',
            'new_password.confirmed' => 'Passwords do not match',
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password changed successfully!');
    }

    public function delete(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('The password is incorrect.');
                    }
                },
            ],
        ], [
            'password.required' => 'Password is required to delete your account',
        ]);

        $user->delete();

        auth()->logout();

        return redirect()->route('login')
            ->with('success', 'Your account has been deleted successfully.');
    }
}
