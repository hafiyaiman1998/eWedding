<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eWeddingCard Creative Studio - Enter Your Dreams</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-gradient: linear-gradient(135deg, #ff6b9d 0%, #c44569 50%, #f8b500 100%);
            --secondary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-primary: #2c3e50;
            --text-secondary: #7f8c8d;
            --bg-main: linear-gradient(135deg, #ffecd2 0%, #fcb69f 25%, #ffecd2 50%, #a8edea 75%, #fed6e3 100%);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-main);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating Hearts Animation */
        .floating-hearts {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .heart {
            position: absolute;
            color: rgba(255, 107, 157, 0.1);
            animation: floatUp 8s infinite ease-in-out;
        }

        @keyframes floatUp {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Sparkles Animation */
        .sparkles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 2;
        }

        .sparkle {
            position: absolute;
            color: rgba(255, 215, 0, 0.3);
            animation: twinkle 3s infinite ease-in-out;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0; transform: scale(0) rotate(0deg); }
            50% { opacity: 1; transform: scale(1) rotate(180deg); }
        }

        /* Main Login Container */
        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100vw;
            min-height: 100vh;
            position: relative;
            z-index: 10;
        }

        /* Left Side - Creative Showcase */
        .creative-showcase {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        .showcase-content {
            text-align: center;
            z-index: 10;
            position: relative;
        }

        .main-logo {
            font-family: 'Dancing Script', cursive;
            font-size: 80px;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            position: relative;
            animation: logoGlow 3s ease-in-out infinite;
        }

        @keyframes logoGlow {
            0%, 100% { filter: drop-shadow(0 0 10px rgba(255, 107, 157, 0.3)); }
            50% { filter: drop-shadow(0 0 25px rgba(255, 107, 157, 0.6)); }
        }

        .main-logo::before {
            content: "💕";
            position: absolute;
            left: -80px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 60px;
            animation: heartBeat 2s ease-in-out infinite;
        }

        .main-logo::after {
            content: "💕";
            position: absolute;
            right: -80px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 60px;
            animation: heartBeat 2s ease-in-out infinite 1s;
        }

        @keyframes heartBeat {
            0%, 100% { transform: translateY(-50%) scale(1); }
            50% { transform: translateY(-50%) scale(1.3); }
        }

        .showcase-subtitle {
            font-size: 24px;
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeInUp 1s ease 0.5s forwards;
        }

        .showcase-description {
            font-size: 18px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 40px;
            opacity: 0;
            animation: fadeInUp 1s ease 1s forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feature-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 40px;
            opacity: 0;
            animation: fadeInUp 1s ease 1.5s forwards;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 30px rgba(255, 107, 157, 0.2);
        }

        .feature-icon {
            font-size: 40px;
            margin-bottom: 15px;
            display: block;
        }

        .feature-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .feature-desc {
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* Right Side - Login Form */
        .login-form-section {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
            min-height: 100vh;
        }

        .login-form-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            animation: slideInRight 1s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .login-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            border-radius: 30px 30px 0 0;
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-title {
            font-family: 'Dancing Script', cursive;
            font-size: 48px;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .form-subtitle {
            font-size: 16px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .form-group {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 18px 25px 18px 55px;
            border: 2px solid var(--glass-border);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: var(--text-primary);
            font-size: 16px;
            font-weight: 500;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input::placeholder {
            color: var(--text-secondary);
        }

        .form-input:focus {
            border-color: rgba(255, 107, 157, 0.5);
            box-shadow: 0 0 0 4px rgba(255, 107, 157, 0.1);
            transform: scale(1.02);
        }

        .form-input.is-invalid {
            border-color: #e74c3c;
            box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.1);
        }

        .form-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .form-input:focus + .form-icon {
            color: #ff6b9d;
            transform: translateY(-50%) scale(1.1);
        }

        .password-toggle {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            color: #ff6b9d;
            transform: translateY(-50%) scale(1.1);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .custom-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid var(--glass-border);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .custom-checkbox.checked {
            background: var(--primary-gradient);
            border-color: transparent;
        }

        .custom-checkbox i {
            color: white;
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .custom-checkbox.checked i {
            opacity: 1;
        }

        .remember-text {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .forgot-password {
            color: #ff6b9d;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .forgot-password:hover {
            color: #c44569;
            transform: translateY(-1px);
        }

        .login-button {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 18px 30px;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            margin-top: 10px;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.5s ease;
        }

        .login-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .login-button:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 30px rgba(255, 107, 157, 0.4);
        }

        .login-button:active {
            transform: translateY(-1px) scale(0.98);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            gap: 15px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: var(--glass-border);
        }

        .divider-text {
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
        }

        .social-login {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border: 2px solid var(--glass-border);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .social-btn:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .social-btn.google:hover {
            background: linear-gradient(135deg, #ea4335, #fbbc05);
            color: white;
            border-color: transparent;
        }

        .social-btn.facebook:hover {
            background: linear-gradient(135deg, #1877f2, #42a5f5);
            color: white;
            border-color: transparent;
        }

        .social-btn.twitter:hover {
            background: linear-gradient(135deg, #1da1f2, #0d8bd9);
            color: white;
            border-color: transparent;
        }

        .signup-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--glass-border);
        }

        .signup-text {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .signup-link a {
            color: #ff6b9d;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .signup-link a:hover {
            color: #c44569;
            transform: translateY(-1px);
        }

        /* Loading Animation */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading .login-button {
            background: linear-gradient(45deg, #ff6b9d, #c44569);
        }

        .loading .login-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .login-container {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }
            
            .creative-showcase {
                padding: 40px 20px;
                min-height: auto;
            }
            
            .main-logo {
                font-size: 60px;
            }
            
            .feature-cards {
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            .login-container {
                display: block;
                height: auto;
                min-height: 100vh;
            }
            
            .creative-showcase {
                padding: 30px 20px;
            }
            
            .main-logo {
                font-size: 50px;
            }
            
            .main-logo::before,
            .main-logo::after {
                display: none;
            }
            
            .feature-cards {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .login-form-section {
                padding: 30px 20px;
            }
            
            .login-form-container {
                padding: 40px 30px;
            }
            
            .form-title {
                font-size: 36px;
            }
        }

        @media (max-width: 480px) {
            .creative-showcase {
                padding: 20px 15px;
            }
            
            .login-form-section {
                padding: 20px 15px;
            }
            
            .login-form-container {
                padding: 30px 20px;
            }
            
            .feature-cards {
                grid-template-columns: 1fr;
            }
        }

        /* Sparkle effect for interactions */
        @keyframes sparkleUp {
            0% {
                transform: translateY(0) scale(0);
                opacity: 1;
            }
            100% {
                transform: translateY(-30px) scale(1);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Hearts Animation -->
    <div class="floating-hearts" id="floatingHearts"></div>
    
    <!-- Sparkles Animation -->
    <div class="sparkles" id="sparkles"></div>

    <div class="login-container">
        <!-- Creative Showcase Side -->
        <div class="creative-showcase">
            <div class="showcase-content">
                <h1 class="main-logo">eWeddingCard</h1>
                <h2 class="showcase-subtitle">Creative Studio</h2>
                <p class="showcase-description">
                    Where love stories become beautiful digital memories. Join thousands of creative professionals 
                    crafting magical wedding experiences that couples will treasure forever. ✨
                </p>
                
                <div class="feature-cards">
                    <div class="feature-card">
                        <span class="feature-icon">🎨</span>
                        <div class="feature-title">Design Magic</div>
                        <div class="feature-desc">Beautiful templates & tools</div>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">💕</span>
                        <div class="feature-title">Love Stories</div>
                        <div class="feature-desc">Personalized experiences</div>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">⚡</span>
                        <div class="feature-title">Lightning Fast</div>
                        <div class="feature-desc">Instant delivery system</div>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">📱</span>
                        <div class="feature-title">Mobile Ready</div>
                        <div class="feature-desc">Perfect on any device</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Form Side -->
        <div class="login-form-section">
            <div class="login-form-container" id="loginContainer">
                <div class="form-header">
                    <h2 class="form-title">Welcome Back</h2>
                    <p class="form-subtitle">Enter your creative sanctuary ✨</p>
                </div>

                <form class="login-form" method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger" style="background: rgba(231, 76, 60, 0.1); border: 1px solid rgba(231, 76, 60, 0.2); color: #e74c3c; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px;">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <input type="email" 
                               class="form-input @error('email') is-invalid @enderror" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="Your creative email address" 
                               required 
                               autocomplete="email" 
                               autofocus>
                        <i class="form-icon fas fa-envelope"></i>
                        @error('email')
                            <div style="color: #e74c3c; font-size: 12px; margin-top: 5px; margin-left: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="password" 
                               class="form-input @error('password') is-invalid @enderror" 
                               name="password" 
                               id="passwordInput" 
                               placeholder="Your secret magic key" 
                               required 
                               autocomplete="current-password">
                        <i class="form-icon fas fa-lock"></i>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordToggleIcon"></i>
                        </button>
                        @error('password')
                            <div style="color: #e74c3c; font-size: 12px; margin-top: 5px; margin-left: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-options">
                        <label class="remember-me" onclick="toggleRemember()">
                            <div class="custom-checkbox" id="rememberCheckbox">
                                <i class="fas fa-check"></i>
                            </div>
                            <span class="remember-text">Remember my magic</span>
                            <input type="checkbox" name="remember" id="remember" style="display: none;" {{ old('remember') ? 'checked' : '' }}>
                        </label>
                        <a href="#" class="forgot-password">Forgot your magic? ✨</a>
                    </div>

                    <button type="submit" class="login-button" id="loginButton">
                        <span id="buttonText">Enter Creative Studio</span>
                    </button>
                </form>

                <div class="divider">
                    <div class="divider-line"></div>
                    <span class="divider-text">or create with</span>
                    <div class="divider-line"></div>
                </div>

                <div class="social-login">
                    <button class="social-btn google" onclick="socialLogin('google')">
                        <i class="fab fa-google"></i>
                    </button>
                    <button class="social-btn facebook" onclick="socialLogin('facebook')">
                        <i class="fab fa-facebook-f"></i>
                    </button>
                    <button class="social-btn twitter" onclick="socialLogin('twitter')">
                        <i class="fab fa-twitter"></i>
                    </button>
                </div>

                <div class="signup-link">
                    <p class="signup-text">
                        New to our creative family? 
                        <a href="#" onclick="showSignupMessage()">Start your creative journey here! 🚀</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Floating Hearts Animation
        function createFloatingHeart() {
            const heartsContainer = document.getElementById('floatingHearts');
            const heart = document.createElement('div');
            heart.className = 'heart';
            
            const hearts = ['💕', '💖', '💗', '💓', '💘', '💝'];
            heart.innerHTML = hearts[Math.floor(Math.random() * hearts.length)];
            
            heart.style.left = Math.random() * 100 + 'vw';
            heart.style.animationDuration = (Math.random() * 4 + 6) + 's';
            heart.style.opacity = Math.random() * 0.3 + 0.1;
            heart.style.fontSize = (Math.random() * 15 + 20) + 'px';
            
            heartsContainer.appendChild(heart);
            
            setTimeout(() => {
                heart.remove();
            }, 10000);
        }

        // Sparkles Animation
        function createSparkle() {
            const sparklesContainer = document.getElementById('sparkles');
            const sparkle = document.createElement('div');
            sparkle.className = 'sparkle';
            
            const sparkles = ['✨', '💫', '⭐', '🌟', '💎'];
            sparkle.innerHTML = sparkles[Math.floor(Math.random() * sparkles.length)];
            
            sparkle.style.left = Math.random() * 100 + 'vw';
            sparkle.style.top = Math.random() * 100 + 'vh';
            sparkle.style.animationDelay = Math.random() * 3 + 's';
            sparkle.style.fontSize = (Math.random() * 10 + 15) + 'px';
            
            sparklesContainer.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
            }, 3000);
        }

        // Create animations periodically
        setInterval(createFloatingHeart, 3000);
        setInterval(createSparkle, 2000);

        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
            
            // Add sparkle effect
            createSparkleEffect(event.target);
        }

        // Remember me toggle
        function toggleRemember() {
            const checkbox = document.getElementById('rememberCheckbox');
            const hiddenCheckbox = document.getElementById('remember');
            
            checkbox.classList.toggle('checked');
            hiddenCheckbox.checked = checkbox.classList.contains('checked');
            
            // Add sparkle effect
            createSparkleEffect(checkbox);
        }

        // Sparkle effect for interactions
        function createSparkleEffect(element) {
            const sparkles = ['✨', '💫', '⭐'];
            const sparkle = document.createElement('span');
            sparkle.innerHTML = sparkles[Math.floor(Math.random() * sparkles.length)];
            sparkle.style.position = 'absolute';
            sparkle.style.left = '50%';
            sparkle.style.top = '50%';
            sparkle.style.transform = 'translate(-50%, -50%)';
            sparkle.style.pointerEvents = 'none';
            sparkle.style.animation = 'sparkleUp 1s ease-out forwards';
            sparkle.style.fontSize = '20px';
            sparkle.style.zIndex = '1000';
            
            element.style.position = 'relative';
            element.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
            }, 1000);
        }

        // Enhanced form interactions
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.3s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
            
            input.addEventListener('input', function() {
                if (this.value) {
                    this.style.background = 'rgba(255, 255, 255, 0.2)';
                } else {
                    this.style.background = 'rgba(255, 255, 255, 0.1)';
                }
            });
        });

        // Social login with animations
        function socialLogin(provider) {
            const btn = event.target.closest('.social-btn');
            
            // Add ripple effect
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255,255,255,0.6)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.left = '50%';
            ripple.style.top = '50%';
            ripple.style.marginLeft = '-25px';
            ripple.style.marginTop = '-25px';
            ripple.style.width = '50px';
            ripple.style.height = '50px';
            
            btn.style.position = 'relative';
            btn.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
            
            // Simulate social login
            setTimeout(() => {
                alert(`✨ Connecting to ${provider.toUpperCase()} creative magic...`);
            }, 300);
        }

        // Enhanced login form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginContainer = document.getElementById('loginContainer');
            const loginButton = document.getElementById('loginButton');
            const buttonText = document.getElementById('buttonText');
            
            // Add loading state
            loginContainer.classList.add('loading');
            buttonText.textContent = 'Entering Studio...';
            
            // Create magical entrance effect
            for (let i = 0; i < 10; i++) {
                setTimeout(() => {
                    createSparkleEffect(loginButton);
                }, i * 100);
            }
            
            // Let the form submit normally to Laravel
        });

        // Signup message
        function showSignupMessage() {
            createSparkleEffect(event.target);
            setTimeout(() => {
                alert('🚀 Ready to start your creative journey? Sign up to join thousands of wedding designers creating magic!');
            }, 300);
        }

        // Forgot password with magic
        document.querySelector('.forgot-password').addEventListener('click', function(e) {
            e.preventDefault();
            createSparkleEffect(this);
            setTimeout(() => {
                alert('✨ No worries! We\'ll send some magic to help you remember. Check your email for password reset instructions.');
            }, 300);
        });

        // Add ripple animation style
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Enhanced hover effects for feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(255, 255, 255, 0.2)';
                createSparkleEffect(this);
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.background = 'rgba(255, 255, 255, 0.1)';
            });
        });

        // Add entrance animation to form elements
        window.addEventListener('load', function() {
            const formElements = document.querySelectorAll('.form-group, .form-options, .login-button, .divider, .social-login, .signup-link');
            
            formElements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    element.style.transition = 'all 0.6s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, (index + 1) * 100);
            });
        });
    </script>
</body>
</html>