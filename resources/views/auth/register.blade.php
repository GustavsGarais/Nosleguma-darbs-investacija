@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="auth-page">
	<section id="register-section" class="auth-card" aria-label="Register">
		<h1>Create your account</h1>
		<p>Join and start simulating smarter strategies</p>

		@if ($errors->any())
			<div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
				<ul style="margin:0; padding-left:18px;">
					@foreach ($errors->all() as $error)
						<li style="color: var(--c-on-surface);">{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form method="POST" action="{{ route('register') }}" style="display:grid; gap:12px;">
			@csrf

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);">Name</span>
				<input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" class="footer-email-input" />
			</label>

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);">Email</span>
				<input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="footer-email-input" />
			</label>

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);">Password</span>
				<input type="password" name="password" required autocomplete="new-password" class="footer-email-input" />
			</label>

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);">Confirm Password</span>
				<input type="password" name="password_confirmation" required autocomplete="new-password" class="footer-email-input" />
			</label>

			<div class="auth-actions">
				<button type="submit" class="btn btn-primary">Create Account</button>
				<a href="{{ route('login') }}" class="btn btn-outline">Already have an account?</a>
			</div>
		</form>
	</section>
</div>
@endsection
