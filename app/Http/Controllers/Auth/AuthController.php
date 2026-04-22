<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(LoginFormRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Redirect based on user role
            return $this->redirectByRole(Auth::user());
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid email or password.']);
    }

    /**
     * Show register form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(RegisterFormRequest $request)
    {
        // Get role based on user type (maps directly to role slug)
        $roleSlug = $request->user_type;
        $role = Role::where('slug', $roleSlug)->firstOrFail();

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
        ]);

        // Auto-login after registration
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Account created successfully! Welcome ' . $user->name);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user to appropriate dashboard based on role
     */
    private function redirectByRole(User $user)
    {
        $roleSlug = $user->role->slug;

        return match ($roleSlug) {
            'admin' => redirect()->route('admin.dashboard'),
            'owner' => redirect()->route('owner.dashboard'),
            'receptionist' => redirect()->route('receptionist.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'cleaner' => redirect()->route('cleaner.dashboard'),
            'inspector' => redirect()->route('inspector.dashboard'),
            'guest' => redirect()->route('guest.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}
