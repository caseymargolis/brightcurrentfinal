<x-guest-layout>
    <div class="login-container">
        <!-- Logo -->
        <div class="logo">
            <x-application-logo />
        </div>

        <!-- Welcome Text -->
        <div class="welcome-text">Create your account to get started!</div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Register Form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input id="name" 
                       class="form-input" 
                       type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="Enter your full name"
                       required 
                       autofocus 
                       autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="error-message" />
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" 
                       class="form-input" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="mail@simmmple.com"
                       required 
                       autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="error-message" />
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-container">
                    <input id="password" 
                           class="form-input" 
                           type="password" 
                           name="password" 
                           placeholder="Min. 8 characters"
                           required 
                           autocomplete="new-password" />
                    <button type="button" 
                            class="password-toggle"
                            onclick="togglePassword('password', 'eye-icon-password')">
                        <iconify-icon icon="mdi:eye-outline" id="eye-icon-password" width="20" height="20"></iconify-icon>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="error-message" />
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="password-container">
                    <input id="password_confirmation" 
                           class="form-input" 
                           type="password" 
                           name="password_confirmation" 
                           placeholder="Confirm your password"
                           required 
                           autocomplete="new-password" />
                    <button type="button" 
                            class="password-toggle"
                            onclick="togglePassword('password_confirmation', 'eye-icon-confirm')">
                        <iconify-icon icon="mdi:eye-outline" id="eye-icon-confirm" width="20" height="20"></iconify-icon>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="error-message" />
            </div>

            <!-- Register Button -->
            <button type="submit" class="login-button">
                Create Account
            </button>

            <!-- Login Link -->
            <div class="login-link">
                <span class="login-link-text">Already have an account?</span>
                <a href="{{ route('login') }}" class="login-link-anchor">Sign In</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('icon', 'mdi:eye-off-outline');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('icon', 'mdi:eye-outline');
            }
        }
    </script>
</x-guest-layout>
