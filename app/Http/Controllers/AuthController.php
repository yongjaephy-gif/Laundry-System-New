<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check() || session('is_logged_in') === true) {
            return redirect()->route('dashboard');
        }

        return view('pages.login', [
            'rememberedEmail' => Cookie::get('remember_login_email', ''),
            'isRemembered' => Cookie::has('remember_login_email'),
        ]);
    }

    /**
     * Handle user login directly from the MySQL database.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember') || $request->has('remember');
        $loginInput = trim($credentials['email']);
        $passwordInput = $credentials['password'];

        // Manage remember email cookie
        if ($remember) {
            Cookie::queue('remember_login_email', $loginInput, 60 * 24 * 30);
        } else {
            Cookie::queue(Cookie::forget('remember_login_email'));
        }

        // 1. Query user directly from MySQL database table `users`
        $user = null;
        try {
            if (Schema::hasTable('users')) {
                $columns = Schema::getColumnListing('users');

                $user = User::where(function ($q) use ($loginInput, $columns) {
                    $hasWhere = false;

                    // Check username
                    if (in_array('username', $columns)) {
                        $q->where('username', $loginInput);
                        $hasWhere = true;
                    }

                    // Check email (exact or prefix like "admin" matches "admin@gmail.cor" or "admin@gmail.com")
                    if (in_array('email', $columns)) {
                        if ($hasWhere) {
                            $q->orWhere('email', $loginInput)
                              ->orWhere('email', 'LIKE', "{$loginInput}@%");
                        } else {
                            $q->where('email', $loginInput)
                              ->orWhere('email', 'LIKE', "{$loginInput}@%");
                            $hasWhere = true;
                        }
                    }

                    // Check full_name (if column exists)
                    if (in_array('full_name', $columns)) {
                        $q->orWhere('full_name', $loginInput)
                          ->orWhere('full_name', 'LIKE', "{$loginInput}%");
                    }

                    // Check name (only if column exists)
                    if (in_array('name', $columns)) {
                        $q->orWhere('name', $loginInput);
                    }
                })->first();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Login search error: ' . $e->getMessage());
            $user = null;
        }

        // 2. Verify credentials and authenticate
        if ($user) {
            $storedPassword = $user->getAuthPassword();

            // Match hashed bcrypt or direct plain text (e.g. admin123)
            $passwordMatches = Hash::check($passwordInput, $storedPassword)
                || $passwordInput === $storedPassword
                || (string)$passwordInput === (string)$storedPassword;

            if ($passwordMatches) {
                try {
                    Auth::login($user, $remember);
                } catch (\Throwable $e) {
                    try {
                        Auth::login($user, false);
                    } catch (\Throwable $e2) {}
                }

                $request->session()->regenerate();

                // 3. Map real role directly from database
                $roleMap = [
                    1 => 'Admin',
                    2 => 'Staff',
                    3 => 'Delivery',
                    4 => 'Receptionist',
                ];
                $userRole = $roleMap[$user->role_id] ?? (is_string($user->role_id) && !empty($user->role_id) ? ucfirst(strtolower($user->role_id)) : 'Admin');

                $userId = $user->getKey() ?? ($user->user_id ?? ($user->id ?? 1));
                $userName = $user->full_name ?? ($user->name ?? ($user->username ?? 'Admin'));

                session([
                    'is_logged_in' => true,
                    'user_id' => $userId,
                    'user_name' => $userName,
                    'user_email' => $user->email,
                    'active_role' => $userRole,
                ]);
                $request->session()->forget('url.intended');

                return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid email/username or password.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form.
     */
    public function showRegisterForm()
    {
        if (Auth::check() || session('is_logged_in') === true) {
            return redirect()->route('dashboard');
        }

        return view('pages.register');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (Schema::hasTable('users')) {
            if (User::where('email', $validated['email'])->exists()) {
                return back()->withErrors(['email' => 'This email is already registered.'])->withInput();
            }

            $columns = Schema::getColumnListing('users');
            $payload = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ];

            if (in_array('password_hash', $columns)) {
                $payload['password_hash'] = $payload['password'];
            }
            if (in_array('username', $columns)) {
                $payload['username'] = explode('@', $validated['email'])[0];
            }
            if (in_array('full_name', $columns)) {
                $payload['full_name'] = $validated['name'];
            }
            if (in_array('role_id', $columns)) {
                $payload['role_id'] = 2; // Staff by default
            }
            if (in_array('status', $columns)) {
                $payload['status'] = 'Active';
            }

            $user = User::create($payload);
            Auth::login($user);
            $request->session()->regenerate();

            $roleMap = [
                1 => 'Admin',
                2 => 'Staff',
                3 => 'Delivery',
                4 => 'Receptionist',
            ];
            $regRole = $roleMap[$user->role_id ?? 2] ?? 'Staff';

            session([
                'is_logged_in' => true,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'active_role' => $regRole,
            ]);

            return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to Laundry 6 Plus.');
        }

        return back()->withErrors(['email' => 'Database table users not found.'])->withInput();
    }

    /**
     * Show forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('pages.forgot-password');
    }

    /**
     * Handle password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $updated = false;

        if (Schema::hasTable('users')) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->password = Hash::make($request->password);
                $user->save();
                $updated = true;
            }
        }

        if ($updated) {
            return redirect()->route('login')->with('success', 'Your password has been successfully reset! Please log in with your new password.');
        }

        return back()->withErrors([
            'email' => 'We could not find an account with that email address.',
        ])->onlyInput('email');
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget(['is_logged_in', 'user_id', 'user_name', 'user_email', 'active_role']);

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
