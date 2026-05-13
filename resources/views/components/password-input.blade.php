@props([
    'name' => 'password',
    'id' => null,
    'label' => null,
    'autocomplete' => 'current-password',
    'required' => true,
])
@php
    $labelText = $label ?? __('Password');
    $inputId = $id ?? 'pw_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
@endphp
<div class="password-field">
	<label class="password-field__label" for="{{ $inputId }}">{{ $labelText }}</label>
	<div class="password-field__wrap">
		<input
			id="{{ $inputId }}"
			type="password"
			name="{{ $name }}"
			@if ($required) required @endif
			autocomplete="{{ $autocomplete }}"
			{{ $attributes->class(['footer-email-input', 'password-field__input']) }}
		/>
		<button
			type="button"
			class="password-field__toggle"
			data-password-toggle="{{ $inputId }}"
			data-aria-show="{{ __('Show password') }}"
			data-aria-hide="{{ __('Hide password') }}"
			aria-pressed="false"
			aria-controls="{{ $inputId }}"
			aria-label="{{ __('Show password') }}"
		>
			<span class="password-field__icon password-field__icon--show" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
					<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
					<circle cx="12" cy="12" r="3" />
				</svg>
			</span>
			<span class="password-field__icon password-field__icon--hide" aria-hidden="true" hidden>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
					<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
					<path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
					<path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
					<line x1="2" x2="22" y1="2" y2="22" />
				</svg>
			</span>
		</button>
	</div>
	@if (trim((string) $slot) !== '')
		<div class="password-field__after">{{ $slot }}</div>
	@endif
</div>

@once
	@push('scripts')
		<script>
		(function () {
			document.addEventListener('click', function (e) {
				var btn = e.target.closest('[data-password-toggle]');
				if (!btn) return;
				var id = btn.getAttribute('data-password-toggle');
				if (!id) return;
				var input = document.getElementById(id);
				if (!input || (input.type !== 'password' && input.type !== 'text')) return;
				var showIcon = btn.querySelector('.password-field__icon--show');
				var hideIcon = btn.querySelector('.password-field__icon--hide');
				var reveal = input.type === 'password';
				input.type = reveal ? 'text' : 'password';
				btn.setAttribute('aria-pressed', reveal ? 'true' : 'false');
				btn.setAttribute(
					'aria-label',
					reveal ? btn.getAttribute('data-aria-hide') : btn.getAttribute('data-aria-show')
				);
				if (showIcon) showIcon.hidden = reveal;
				if (hideIcon) hideIcon.hidden = !reveal;
			});
		})();
		</script>
	@endpush
@endonce
