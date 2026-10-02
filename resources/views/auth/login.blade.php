@extends('layouts.app')

@section('title', ' - Login')

@php
    $hide_navbar = true;
    $bodyClass = 'login-page';
@endphp

@section('styles')
<style>
    body {
        background: url('{{ asset("images/login-bg.png") }}') no-repeat center center fixed;
        background-size: 100% 100%;
        min-height: 100vh;
    }

    .login-split {
        height: 100vh;
        min-height: 100vh;
        max-height: 100vh;
        display: flex;
        align-items: center;
        overflow: hidden;
        position: relative;
    }

    .login-branding {
        position: absolute;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 28px 0 0 5%;
        width: 58%;
        z-index: 10;
    }

    .login-branding img {
        height: 58px;
        width: auto;
        object-fit: contain;
    }

    .login-branding .brand-name {
        font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        font-size: 30px;
        font-weight: 800;
        color: #F7D000;
        letter-spacing: 0.8px;
        text-shadow: 0 2px 14px rgba(247, 208, 0, 0.28);
        border-bottom: 3px solid rgba(247, 208, 0, 0.8);
        padding-bottom: 3px;
    }

    .login-left {
        flex: 0 0 58%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding-left: 5%;
        padding-right: 2%;
        padding-top: 120px;
    }

    .login-right {
        flex: 0 0 42%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-right: 5%;
        padding-left: 2%;
    }

    .login-card {
        width: 100%;
        max-width: 400px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        padding: 24px 28px;
    }

    .accent-line {
        width: 60px;
        height: 4px;
        background: #F7D000;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .headline-line1 {
        font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        font-size: 3.2rem;
        font-weight: 700;
        color: #FFFFFF;
        line-height: 1.15;
        margin: 0;
    }

    .headline-line2 {
        font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        font-size: 3.2rem;
        font-weight: 700;
        color: #F7D000;
        line-height: 1.15;
        margin: 0;
        margin-bottom: 20px;
    }

    .sub-headline {
        font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        font-size: 1.35rem;
        font-weight: 700;
        color: #FFFFFF;
        margin-bottom: 14px;
        line-height: 1.4;
    }

    .support-paragraph {
        font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        font-size: 1rem;
        font-weight: 400;
        color: #FFFFFF;
        line-height: 1.6;
        opacity: 0.92;
        max-width: 420px;
        margin: 0;
    }

    .form-label {
        font-weight: 500;
    }

    .form-check-label {
        color: #64748B;
        opacity: 0.7;
    }

    .btn-primary {
        background-color: #10069f !important;
        border-color: #10069f !important;
        color: #ffffff !important;
    }

    .btn-primary:hover, .btn-primary:focus {
        background-color: #0d057a !important;
        border-color: #0d057a !important;
        color: #ffffff !important;
    }

    @media (max-width: 991px) {
        .login-split {
            flex-direction: column;
            text-align: center;
            min-height: auto;
        }
        .login-branding {
            position: static;
            width: 100%;
            justify-content: center;
            padding: 20px 0 0;
            order: 1;
        }
        .login-left {
            flex: 0 0 auto;
            padding: 20px;
            order: 2;
            align-items: center;
        }
        .login-right {
            flex: 0 0 auto;
            padding: 0 20px 40px;
            order: 3;
        }
        .login-card {
            max-width: 100%;
        }
        .accent-line {
            margin-left: auto;
            margin-right: auto;
        }
        .headline-line1,
        .headline-line2 {
            font-size: 2.4rem;
        }
        .sub-headline {
            font-size: 1.15rem;
        }
        .support-paragraph {
            margin-left: auto;
            margin-right: auto;
        }
    }

    @media (max-width: 575px) {
        .headline-line1,
        .headline-line2 {
            font-size: 1.9rem;
        }
        .sub-headline {
            font-size: 1rem;
        }
    }

    @media (max-width: 991px) {
        html body.login-page {
            height: auto;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
        }

        html body.login-page .main-content,
        html body.login-page .login-split {
            height: auto;
            min-height: 100vh;
            max-height: none;
            overflow: visible;
        }

        html body.login-page .login-split {
            padding-bottom: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="login-split">
    <div class="login-branding">
        <img src="{{ asset('images/bipsu_new.png') }}" alt="BiPSU">
        <img src="{{ asset('images/chatgpt_logo.png') }}" alt="G-SCHED">
        <span class="brand-name">G-SCHED</span>
    </div>
    <div class="login-left">
        <div class="accent-line"></div>
        <h1 class="headline-line1">Your guidance journey</h1>
        <h1 class="headline-line2">starts here.</h1>
        <p class="sub-headline">Get the support you need,<br>when you need it.</p>
        <p class="support-paragraph">Connect with your Guidance Associate,<br>manage your appointments, and take the<br>next step toward a better you.</p>
    </div>

    <div class="login-right">
        <div class="login-card">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @endif" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @endif" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @endif
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Login
                    </button>
                </div>
            </form>

            <div class="text-center mt-3">
                <p class="mb-1">Don't have an account? <a href="{{ route('register') }}">Register as Student</a></p>
            </div>

            <hr class="my-3">

            <div class="text-center">
                <small class="text-muted">Demo Credentials:</small>
                <div class="mt-2">
                    <small class="text-muted">Admin: admin@g-sched.test / password</small><br>
                    <small class="text-muted">Guidance: guidance@g-sched.test / password</small><br>
                    <small class="text-muted">Student: student@g-sched.test / password</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection