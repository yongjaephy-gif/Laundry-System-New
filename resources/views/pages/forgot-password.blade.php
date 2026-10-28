@extends('layouts.Authentication')

@section('title', 'Forgot Password - Laundry 6 Plus')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center p-4">
    <div class="p-8 rounded-3xl shadow-md w-full max-w-md bg-white">
        <!-- Logo -->
        <img src="{{ asset('images/logo.PNG') }}" alt="Logo" class="w-20 h-20 rounded-full object-cover mx-auto mb-2">
        <h2 class="text-2xl font-bold mb-1 text-center text-gray-800">Reset Password</h2>
        <p class="text-xs text-center text-gray-500 mb-4">Enter your registered email and your new password.</p>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-2xl mb-3 text-sm text-center">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded-2xl mb-3 text-sm text-center">
                {{ session('status') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            
            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="block text-gray-700 text-xs font-semibold mb-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 border border-gray-300 rounded-3xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. admin@gmail.com">
            </div>

            <!-- New Password -->
            <div class="mb-3">
                <label for="password" class="block text-gray-700 text-xs font-semibold mb-1">New Password</label>
                <div class="relative flex items-center">
                    <input type="password" name="password" id="password" required
                        class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-3xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter new password (min. 6 chars)">
                    <button type="button" onclick="togglePasswordVisibility('password', 'eyeForgotNew')"
                        class="absolute right-4 text-gray-400 hover:text-gray-700 focus:outline-none cursor-pointer flex items-center justify-center p-1"
                        title="Show/Hide Password" aria-label="Toggle password visibility">
                        <i id="eyeForgotNew" class="fa-regular fa-eye text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-5">
                <label for="password_confirmation" class="block text-gray-700 text-xs font-semibold mb-1">Confirm New Password</label>
                <div class="relative flex items-center">
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-3xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Repeat new password">
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eyeForgotConfirm')"
                        class="absolute right-4 text-gray-400 hover:text-gray-700 focus:outline-none cursor-pointer flex items-center justify-center p-1"
                        title="Show/Hide Password" aria-label="Toggle password visibility">
                        <i id="eyeForgotConfirm" class="fa-regular fa-eye text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-blue-500 text-white py-2.5 px-4 rounded-3xl hover:bg-blue-600 transition duration-200 font-semibold justify-center flex items-center mx-auto shadow-sm cursor-pointer">
                Reset Password
            </button>
        </form>

        <p class="mt-5 text-center text-sm text-gray-600">
            Remembered your password? 
            <a href="{{ route('login') }}" class="text-blue-500 font-semibold hover:underline">Back to Login</a>
        </p>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input) return;
        
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye', 'fa-regular');
                icon.classList.add('fa-eye-slash', 'fa-solid');
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash', 'fa-solid');
                icon.classList.add('fa-eye', 'fa-regular');
            }
        }
    }
</script>
@endsection
