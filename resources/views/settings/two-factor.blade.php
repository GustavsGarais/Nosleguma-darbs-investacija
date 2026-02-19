@extends('layouts.dashboard')

@section('title', __('Two-Factor Authentication'))

@section('dashboard_content')
<section class="auth-card" aria-label="{{ __('Two-Factor Authentication Setup') }}" style="padding:32px; display:flex; flex-direction:column; gap:24px;">
	<header style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
		<div>
			<h1 style="margin:0;">{{ __('Two-Factor Authentication') }}</h1>
			<p style="margin:6px 0 0; color:var(--c-on-surface-2);">{{ __('Add an extra layer of security to your account.') }}</p>
		</div>
		<a class="btn btn-outline" href="{{ route('settings') }}">← {{ __('Back') }}</a>
	</header>

	@if ($user->hasTwoFactorEnabled())
		<div role="status" style="padding:12px 16px; border-radius:10px; background:color-mix(in srgb, var(--c-primary) 18%, var(--c-surface)); border:1px solid color-mix(in srgb, var(--c-primary) 35%, var(--c-border));">
			<strong>{{ __('Two-factor authentication is enabled.') }}</strong>
		</div>

		<article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
			<h2 style="margin:0;">{{ __('Recovery Codes') }}</h2>
			<p style="margin:0; color:var(--c-on-surface-2); font-size:14px;">
				{{ __('Save these recovery codes in a safe place. You can use them to access your account if you lose access to your authenticator device.') }}
			</p>
			
			@if(session('recoveryCodes'))
				<div style="background:var(--c-surface); border:1px solid var(--c-border); border-radius:10px; padding:16px; display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:8px;">
					@foreach(session('recoveryCodes') as $code)
						<code style="padding:8px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); border-radius:6px; font-family:monospace; font-size:13px;">{{ $code }}</code>
					@endforeach
				</div>
			@else
				<div style="background:var(--c-surface); border:1px solid var(--c-border); border-radius:10px; padding:16px; display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:8px;">
					@foreach($recoveryCodes as $code)
						<code style="padding:8px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); border-radius:6px; font-family:monospace; font-size:13px;">{{ $code }}</code>
					@endforeach
				</div>
			@endif

			<form method="POST" action="{{ route('two-factor.recovery-codes') }}" style="margin-top:8px;">
				@csrf
				<button type="submit" class="btn btn-outline">{{ __('Regenerate Recovery Codes') }}</button>
			</form>
		</article>

		<article style="border:1px solid #e53935; border-radius:16px; padding:24px; background:color-mix(in srgb, #e53935 10%, var(--c-surface)); display:flex; flex-direction:column; gap:16px;">
			<h2 style="margin:0; color:#e53935;">{{ __('Disable Two-Factor Authentication') }}</h2>
			<p style="margin:0; color:var(--c-on-surface-2); font-size:14px;">
				{{ __('Once disabled, you will only need your password to sign in.') }}
			</p>
			<form method="POST" action="{{ route('two-factor.disable') }}" style="display:flex; flex-direction:column; gap:12px;">
				@csrf
				<label style="display:flex; flex-direction:column; gap:6px;">
					<span style="font-weight:600;">{{ __('Confirm Password') }}</span>
					<input type="password" name="password" class="footer-email-input" required />
				</label>
				<button type="submit" class="btn btn-outline" style="color:#e53935; border-color:#e53935; align-self:flex-start;">{{ __('Disable 2FA') }}</button>
			</form>
		</article>
	@else
		<article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
			<h2 style="margin:0;">{{ __('Step 1: Scan QR Code') }}</h2>
			<p style="margin:0; color:var(--c-on-surface-2); font-size:14px;">
				{{ __('Scan this QR code with your authenticator app (Google Authenticator, Authy, Microsoft Authenticator, etc.).') }}
			</p>
			
			<div style="display:flex; justify-content:center; padding:20px; background:white; border-radius:12px; border:1px solid var(--c-border);">
				<img src="{{ $qrCode }}" alt="{{ __('QR Code') }}" style="max-width:100%; height:auto;" />
			</div>

			<div style="padding:12px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); border-radius:8px;">
				<p style="margin:0 0 8px; font-weight:600; font-size:13px;">{{ __('Manual Entry Key') }}</p>
				<code style="font-family:monospace; font-size:14px; word-break:break-all;">{{ $secret }}</code>
			</div>
		</article>

		<article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
			<h2 style="margin:0;">{{ __('Step 2: Verify Code') }}</h2>
			<p style="margin:0; color:var(--c-on-surface-2); font-size:14px;">
				{{ __('Enter the 6-digit code from your authenticator app to enable two-factor authentication.') }}
			</p>
			
			<form method="POST" action="{{ route('two-factor.enable') }}" style="display:flex; flex-direction:column; gap:12px;">
				@csrf
				<label style="display:flex; flex-direction:column; gap:6px;">
					<span style="font-weight:600;">{{ __('Authentication Code') }}</span>
					<input 
						type="text" 
						name="code" 
						required 
						autofocus
						class="footer-email-input" 
						placeholder="000000"
						maxlength="6"
						style="text-align:center; letter-spacing:4px; font-size:20px; font-weight:600;"
					/>
					@error('code')
						<span style="color:#e53935; font-size:12px;">{{ $message }}</span>
					@enderror
				</label>
				<button type="submit" class="btn btn-primary" style="align-self:flex-start;">{{ __('Enable 2FA') }}</button>
			</form>
		</article>
	@endif
</section>
@endsection
