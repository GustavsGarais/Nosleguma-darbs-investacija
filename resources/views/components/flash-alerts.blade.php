@php
    $hasSuccess = session()->has('success');
    $hasError = session()->has('error');
    $hasValidation = isset($errors) && $errors->any();
@endphp
@if ($hasSuccess || $hasError || $hasValidation)
    <div class="flash-stack" aria-live="polite">
        @if ($hasSuccess)
            <div class="flash flash--success" role="status">
                {{ session('success') }}
            </div>
        @endif
        @if ($hasError)
            <div class="flash flash--error" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if ($hasValidation)
            <div class="flash flash--error" role="alert">
                <strong>{{ __('Please correct the following:') }}</strong>
                <ul class="flash-validation-list">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
