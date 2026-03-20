@extends('layouts.app')

@section('title', __('Confirm Password'))

@section('content')
<div class="auth-page">
	<section class="auth-card" aria-label="{{ __('Confirm Password') }}">
		<h1 style="margin:0 0 6px;">{{ __('Confirm Password') }}</h1>
		<p style="margin:0 0 16px; color: var(--c-on-surface-2);">
			{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
		</p>

		@if ($errors->any())
			<div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid #e53935; border-radius:10px; background: color-mix(in srgb, #e53935 10%, var(--c-surface));">
				<ul style="margin:0; padding-left:18px;">
					@foreach ($errors->all() as $error)
						<li style="color: var(--c-on-surface);">{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form method="POST" action="{{ route('password.confirm') }}" style="display:grid; gap:12px;">
			@csrf
			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);">{{ __('Password') }}</span>
				<input type="password" name="password" required autocomplete="current-password" class="footer-email-input" />
			</label>

			<div class="auth-actions">
				<button type="submit" class="btn btn-primary">{{ __('Confirm') }}</button>
				<a href="{{ route('dashboard') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
			</div>
		</form>
	</section>
</div>
@endsection

