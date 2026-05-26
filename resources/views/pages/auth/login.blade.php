@extends('layouts.xxxxx-layout')

@section('page-title', 'Sign In')

@section('content')
<div class="login-main-card">
    <div class="login-left-brand">
        <div class="login-logo-box"></div>
        <h1 class="login-brand-text">FlowSync</h1>
        <p class="text-content" style="margin-top: 8px;">A simple tool to sync task with your team</p>
    </div>

    <div class="login-right-form">
        <div class="login-form-box">
            <h2 class="login-title">Sign In</h2>

            <form class="login-form" method="POST" action="{{ route('dashboard') }}" id="loginForm" style="width: 100%;">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div class="form-input-group username-field">
                        <label class="form-input-label" for="username">Username</label>
                        <input class="form-input-field" type="text" id="username" name="username" placeholder="Enter username" value="admin" required />
                    </div>

                    <div class="form-input-group password-field">
                        <label class="form-input-label" for="password">Password</label>
                        <input class="form-input-field" type="password" id="password" name="password" placeholder="Enter password" value="admin123" required />
                    </div>
                </div>

                <div style="margin-top: 32px;">
                    <button class="btn btn-primary btn-login-submit" type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
