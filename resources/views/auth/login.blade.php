@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="auth-page">
	<section id="login-section" class="auth-card" aria-label="Login">
		<h1>Welcome back</h1>
		<p>Sign in to continue your simulations</p>

		@if ($errors->any())
			<div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
				<ul style="margin:0; padding-left:18px;">
					@foreach ($errors->all() as $error)
						<li style="color: var(--c-on-surface);">{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form method="POST" action="{{ route('login') }}" style="display:grid; gap:12px;">
			@csrf

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);">Email</span>
				<input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="footer-email-input" />
			</label>

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);">Password</span>
				<input type="password" name="password" required autocomplete="current-password" class="footer-email-input" />
			</label>

			<label style="display:flex; align-items:center; gap:8px; color: var(--c-on-surface-2);">
				<input type="checkbox" name="remember" style="width:16px; height:16px;" />
				<span>Remember me</span>
			</label>

			<div class="auth-actions">
				<button type="submit" class="btn btn-primary">Sign In</button>
				<a href="{{ route('password.request') }}" class="btn btn-outline">Forgot password?</a>
			</div>

			<div style="margin-top:6px; color: var(--c-on-surface-2);">
				New here?
				<a href="{{ route('register') }}" class="btn btn-link" style="padding:0;">Create an account</a>
			</div>
		</form>
	</section>
</div>
@endsection
