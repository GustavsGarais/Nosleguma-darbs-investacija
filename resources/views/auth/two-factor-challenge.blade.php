@extends('layouts.app')

@section('title', __('Two-Factor Authentication'))

@section('content')
<div class="auth-page">
	<section class="auth-card" aria-label="{{ __('Two-Factor Authentication') }}">
		<h1>{{ __('Two-Factor Authentication') }}</h1>
		<p>{{ __('Please enter the 6-digit code from your authenticator app or use a recovery code.') }}</p>

		@if ($errors->any())
			<div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid #e53935; border-radius:10px; background: color-mix(in srgb, #e53935 10%, var(--c-surface));">
				<ul style="margin:0; padding-left:18px;">
					@foreach ($errors->all() as $error)
						<li style="color: var(--c-on-surface);">{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form method="POST" action="{{ route('two-factor.login') }}" style="display:grid; gap:12px;">
			@csrf

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);">{{ __('Authentication Code') }}</span>
				<input 
					type="text" 
					name="code" 
					required 
					autofocus 
					autocomplete="one-time-code"
					class="footer-email-input" 
					placeholder="000000"
					maxlength="10"
					style="text-align:center; letter-spacing:4px; font-size:20px; font-weight:600;"
				/>
				<small style="color: var(--c-on-surface-2); font-size:12px;">{{ __('Enter the 6-digit code from your authenticator app, or a recovery code.') }}</small>
			</label>

			<label style="display:flex; align-items:center; gap:8px; color: var(--c-on-surface-2);">
				<input type="checkbox" name="remember" style="width:16px; height:16px;" />
				<span>{{ __('Remember me') }}</span>
			</label>

			<div class="auth-actions">
				<button type="submit" class="btn btn-primary">{{ __('Verify') }}</button>
				<a href="{{ route('login') }}" class="btn btn-outline">{{ __('Back to login') }}</a>
			</div>
		</form>
	</section>
</div>
@endsection
