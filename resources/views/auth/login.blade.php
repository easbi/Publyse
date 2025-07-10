<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publyse</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #2a1810 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated background elements */
        .bg-decoration {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .bg-decoration::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 138, 0, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        .floating-shapes {
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(255, 138, 0, 0.1), rgba(255, 165, 0, 0.05));
            animation: float 6s ease-in-out infinite;
        }

        .floating-shapes:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-shapes:nth-child(2) {
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .floating-shapes:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.05); opacity: 0.5; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .login-container {
            background: rgba(20, 20, 20, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 138, 0, 0.2);
            border-radius: 24px;
            padding: 48px;
            width: 100%;
            max-width: 480px;
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 138, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow:
                0 35px 60px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(255, 138, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ff8a00, #ffa500, #ff6b35);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .login-subtitle {
            color: #9ca3af;
            font-size: 1rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            color: #e5e7eb;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 8px;
            letter-spacing: 0.02em;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            background: rgba(30, 30, 30, 0.8);
            border: 2px solid rgba(255, 138, 0, 0.2);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 400;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            outline: none;
            border-color: #ff8a00;
            box-shadow: 0 0 0 4px rgba(255, 138, 0, 0.1);
            background: rgba(40, 40, 40, 0.9);
        }

        .form-input::placeholder {
            color: #6b7280;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 32px;
        }

        .checkbox-input {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            accent-color: #ff8a00;
            border-radius: 4px;
        }

        .checkbox-label {
            color: #9ca3af;
            font-size: 0.9rem;
            cursor: pointer;
            user-select: none;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .forgot-password {
            color: #ff8a00;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .forgot-password:hover {
            color: #ffa500;
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #ff8a00, #ff6b35);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(255, 138, 0, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .status-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.9rem;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 8px;
            font-size: 0.9rem;
        }

        /* Responsive design */
        @media (max-width: 640px) {
            .login-container {
                padding: 32px 24px;
                margin: 20px;
            }

            .login-title {
                font-size: 2rem;
            }

            .form-actions {
                flex-direction: column;
                gap: 16px;
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="bg-decoration">
        <div class="floating-shapes"></div>
        <div class="floating-shapes"></div>
        <div class="floating-shapes"></div>
    </div>

    <div class="login-container">
        <div class="login-header">
            <h1 class="login-title">Publyse</h1>
            <p class="login-subtitle">Login</p>
        </div>

        <!-- Status Message -->
        @if (session('status'))
            <div class="status-message">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Username Field -->
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-input"
                    placeholder="Enter your username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    autocomplete="username"
                >
                @error('username')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="checkbox-container">
                <input
                    type="checkbox"
                    id="remember_me"
                    name="remember"
                    class="checkbox-input"
                >
                <label for="remember_me" class="checkbox-label">Remember me</label>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-password">Forgot your password?</a>
                @endif
            </div>

            <!-- Login Button -->
            <button type="submit" class="login-button">
                <span class="button-text">Sign In</span>
            </button>
        </form>
    </div>

    <script>
        // Add focus effects
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Add typing animation to title
        const title = document.querySelector('.login-title');
        const text = title.textContent;
        title.textContent = '';

        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                title.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        };

        setTimeout(typeWriter, 500);
    </script>
</body>
</html>
