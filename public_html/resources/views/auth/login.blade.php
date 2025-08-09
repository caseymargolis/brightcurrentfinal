<x-guest-layout>
    <div class="login-container">
        <!-- Logo -->
        <div class="logo">
            <x-application-logo />
        </div>

        <!-- Welcome Text -->
        <div class="welcome-text">Enter your email and password to log in!</div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

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
                       autofocus 
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
                           autocomplete="current-password" />
                    <button type="button" 
                            class="password-toggle"
                            onclick="togglePassword()">
                        <iconify-icon icon="mdi:eye-outline" id="eye-icon" width="20" height="20"></iconify-icon>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="error-message" />
            </div>

            <!-- Remember Me -->
            <div class="checkbox-container">
                <input id="remember_me" 
                       type="checkbox" 
                       class="checkbox-custom" 
                       name="remember">
                <label for="remember_me" class="checkbox-label">Remember me</label>
            </div>

            <!-- Login Button -->
            <button type="submit" class="login-button">
                Login
            </button>

            <!-- Register Link -->
            <div class="login-link">
                <span class="login-link-text">Don't have an account?</span>
                <a href="{{ route('register') }}" class="login-link-anchor">Sign Up</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
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
