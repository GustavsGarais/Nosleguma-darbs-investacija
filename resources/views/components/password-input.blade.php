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
	<div class="password-field__row">
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
			class="btn btn-outline btn-sm password-field__toggle"
			data-password-toggle="{{ $inputId }}"
			data-label-show="{{ __('Show') }}"
			data-label-hide="{{ __('Hide') }}"
			data-aria-show="{{ __('Show password') }}"
			data-aria-hide="{{ __('Hide password') }}"
			aria-pressed="false"
			aria-controls="{{ $inputId }}"
			aria-label="{{ __('Show password') }}"
		>{{ __('Show') }}</button>
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
				var show = input.type === 'password';
				input.type = show ? 'text' : 'password';
				btn.setAttribute('aria-pressed', show ? 'true' : 'false');
				btn.textContent = show ? btn.getAttribute('data-label-hide') : btn.getAttribute('data-label-show');
				btn.setAttribute(
					'aria-label',
					show ? btn.getAttribute('data-aria-hide') : btn.getAttribute('data-aria-show')
				);
			});
		})();
		</script>
	@endpush
@endonce
