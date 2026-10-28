@extends('layouts.Authentication')

@section('title', 'Register')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center p-4">
    <div class="p-8 rounded-3xl shadow-md w-full max-w-md bg-white">
        <img src="{{ asset('images/logo.PNG') }}" alt="Logo" class="w-20 h-20 rounded-full object-cover mx-auto mb-2">
        <h2 class="text-2xl font-bold mb-2 text-center text-gray-800">Register</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-2xl mb-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="block text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-4 py-2 border border-gray-300 rounded-3xl focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your name">
            </div>

            <div class="mb-3">
                <label for="email" class="block text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-3xl focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your email">
            </div>

            <div class="mb-3">
                <label for="password" class="block text-gray-700 mb-1">Password</label> 
                <div class="relative flex items-center">
                    <input type="password" name="password" id="password" required
                        class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-3xl focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="At least 6 characters">
                    <button type="button" onclick="togglePasswordVisibility('password', 'eyeRegPassword')"
                        class="absolute right-4 text-gray-400 hover:text-gray-700 focus:outline-none cursor-pointer flex items-center justify-center p-1"
                        title="Show/Hide Password" aria-label="Toggle password visibility">
                        <i id="eyeRegPassword" class="fa-regular fa-eye text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700 mb-1">Confirm Password</label> 
                <div class="relative flex items-center">
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-3xl focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Re-enter password">
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eyeRegConfirm')"
                        class="absolute right-4 text-gray-400 hover:text-gray-700 focus:outline-none cursor-pointer flex items-center justify-center p-1"
                        title="Show/Hide Password" aria-label="Toggle password visibility">
                        <i id="eyeRegConfirm" class="fa-regular fa-eye text-lg"></i>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-40 bg-blue-500 text-white py-2 px-4 rounded-3xl hover:bg-blue-600 transition duration-200 font-semibold justify-center flex items-center mx-auto cursor-pointer">
                Register
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-blue-500 font-semibold hover:underline">Login</a>
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
