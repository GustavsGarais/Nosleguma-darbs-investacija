@extends('layouts.app')

@section('title', __('Verify Email'))

@section('content')
<div class="auth-page">
	<section class="auth-card" aria-label="{{ __('Verify Email') }}">
		<h1 style="margin:0 0 6px;">{{ __('Verify Email') }}</h1>
		<p style="margin:0 0 16px; color: var(--c-on-surface-2);">
			{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn’t receive the email, we will gladly send you another.') }}
		</p>

		@if (session('status') === 'verification-link-sent')
			<div role="status" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
				{{ __('A new verification link has been sent to the email address you provided during registration.') }}
			</div>
		@endif

		<div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
			<form method="POST" action="{{ route('verification.send') }}" style="margin:0;">
				@csrf
				<button type="submit" class="btn btn-primary">{{ __('Resend Verification Email') }}</button>
			</form>

			<form method="POST" action="{{ route('logout') }}" style="margin:0;">
				@csrf
				<button type="submit" class="btn btn-outline">{{ __('Logout') }}</button>
			</form>
		</div>
	</section>
</div>
@endsection

