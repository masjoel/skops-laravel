@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <style>
        .auth-bg {
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(99, 102, 241, .25) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(168, 85, 247, .2) 0%, transparent 50%),
                #0f172a;
        }

        .auth-particles {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .auth-particles span {
            position: absolute;
            width: 2px;
            height: 2px;
            background: rgba(255, 255, 255, .4);
            border-radius: 50%;
            animation: float linear infinite;
        }

        @keyframes float {
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
                transform: translateY(-100px) rotate(720deg);
                opacity: 0;
            }
        }

        .auth-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Left Branding Panel */
        .auth-left {
            flex: 1;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px;
        }

        @media(min-width: 992px) {
            .auth-left {
                display: flex;
            }
        }

        .brand-logo {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            box-shadow: 0 8px 32px rgba(99, 102, 241, .4);
            margin-bottom: 32px;
        }

        .brand-name {
            font-size: 36px;
            font-weight: 800;
            color: white;
            line-height: 1.1;
            margin-bottom: 16px;
        }

        .brand-name span {
            color: #818cf8;
        }

        .brand-desc {
            color: #94a3b8;
            font-size: 16px;
            line-height: 1.7;
            max-width: 380px;
            margin-bottom: 48px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: rgba(99, 102, 241, .15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #818cf8;
            font-size: 16px;
            flex-shrink: 0;
        }

        .feature-text {
            color: #cbd5e1;
            font-size: 14px;
        }

        /* Right Login Panel */
        .auth-right {
            width: 100%;
            max-width: 460px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
            background: rgba(255, 255, 255, .03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255, 255, 255, .07);
        }

        @media(min-width: 992px) {
            .auth-right {
                min-height: 100vh;
            }
        }

        .login-card {
            width: 100%;
            max-width: 380px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(99, 102, 241, .35);
        }

        .login-title {
            font-size: 22px;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 6px;
        }

        .login-sub {
            color: #64748b;
            font-size: 13.5px;
        }

        .company-name {
            color: #818cf8;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label-auth {
            font-size: 13px;
            font-weight: 500;
            color: #94a3b8;
            margin-bottom: 8px;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            font-size: 14px;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 11px 14px 11px 42px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 10px;
            color: #f8fafc;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color .2s, box-shadow .2s, background .2s;
            outline: none;
        }

        .form-input::placeholder {
            color: #475569;
        }

        .form-input:focus {
            border-color: #6366f1;
            background: rgba(99, 102, 241, .08);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .2);
        }

        .input-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #475569;
            cursor: pointer;
            font-size: 14px;
            padding: 4px;
            transition: color .2s;
        }

        .input-toggle:hover {
            color: #818cf8;
        }

        .error-text {
            font-size: 12px;
            color: #f87171;
            margin-top: 6px;
            display: block;
        }

        .login-error {
            background: rgba(239, 68, 68, .1);
            border: 1px solid rgba(239, 68, 68, .25);
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #f87171;
            margin-bottom: 18px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 4px 15px rgba(99, 102, 241, .4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: 'Inter', sans-serif;
        }

        .btn-login:hover {
            opacity: .9;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, .5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: .6;
            transform: none;
            cursor: not-allowed;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            color: #475569;
            font-size: 12px;
        }

        .login-footer a {
            color: #818cf8;
            text-decoration: none;
        }
    </style>

    <div class="auth-bg"></div>
    <div class="auth-particles" id="particles"></div>

    <div class="auth-wrapper">
        {{-- Left Branding --}}
        <div class="auth-left">
            <div class="brand-logo">
                <i class="fas fa-school"></i>
            </div>
            <div class="brand-name">
                Sistem Kelola <span></span><br>Operasional Sekolah
            </div>
            <p class="brand-desc mb-3">
                {{-- Satu Aplikasi untuk Semua Kebutuhan Sekolah. --}}
                SKOPS adalah platform digital yang membantu sekolah mengelola seluruh operasional dalam satu sistem yang
                terintegrasi.
                {{-- SKOPS adalah platform digital yang membantu sekolah mengelola seluruh operasional dalam satu sistem yang terintegrasi. Dirancang untuk mendukung digitalisasi sekolah, SKOPS memudahkan pengelolaan data akademik maupun administrasi sehingga pekerjaan menjadi lebih cepat, rapi, dan efisien. --}}
                <br><br>Dengan SKOPS, sekolah dapat mengelola:
            </p>
            <div class="row">
                <div class="col-md-6">
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-user-graduate"></i></div>
                            <div class="feature-text">Data siswa dan guru</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-star"></i></div>
                            <div class="feature-text">Penilaian perilaku (Reward & Pelanggaran) </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-book-open"></i></div>
                            <div class="text-secondary">Rapor siswa <i>(dalam pengembangan)</i></div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="text-secondary">Absensi <i>(dalam pengembangan)</i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-wallet"></i></div>
                            <div class="text-secondary">Keuangan sekolah, tabungan, dan tagihan SPP <i>(dalam pengembangan)</i></div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-notes-medical"></i></div>
                            <div class="text-secondary">Rekam kesehatan siswa <i>(dalam pengembangan)</i></div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                            <div class="text-secondary">Laporan dan statistik <i>(dalam pengembangan)</i></div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fas fa-users"></i></div>
                            <div class="text-secondary">Portal orang tua <i>(dalam pengembangan)</i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Login Form --}}
        <div class="auth-right">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-header-icon">
                        <i class="fas fa-school"></i>
                    </div>
                    <h1 class="login-title">Selamat Datang</h1>
                    <p class="login-sub">
                        @if (isset($sekolah) && $sekolah)
                            <span class="company-name">{{ $sekolah->nama_client }}</span>
                        @else
                            Masuk ke akun Anda
                        @endif
                    </p>
                </div>

                {{-- Error --}}
                @if ($errors->has('login'))
                    <div class="login-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first('login') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label class="form-label-auth" for="username">Username</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="username" name="username" class="form-input"
                                placeholder="Masukkan username" value="{{ old('username', '') }}" autocomplete="username"
                                autofocus required>
                        </div>
                        @error('username')
                            <span class="error-text"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label-auth" for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="form-input"
                                placeholder="Masukkan password" autocomplete="current-password"  value="" required>
                            <button type="button" class="input-toggle" onclick="togglePassword()" id="toggleBtn">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-text"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login" id="submitBtn">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk
                    </button>
                </form>

                <div class="login-footer">
                    &copy; {{ date('Y') }} Sistem Kelola Operasional Sekolah
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Toggle password visibility
            function togglePassword() {
                const input = document.getElementById('password');
                const icon = document.getElementById('eyeIcon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            }

            // Loading state on submit
            document.getElementById('loginForm').addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            });

            // Particle background
            const container = document.getElementById('particles');
            for (let i = 0; i < 40; i++) {
                const s = document.createElement('span');
                s.style.left = Math.random() * 100 + 'vw';
                s.style.width = s.style.height = (Math.random() * 3 + 1) + 'px';
                s.style.animationDuration = (Math.random() * 15 + 10) + 's';
                s.style.animationDelay = (Math.random() * 10) + 's';
                container.appendChild(s);
            }
        </script>
    @endpush
@endsection
@include('partials.read-ip')
