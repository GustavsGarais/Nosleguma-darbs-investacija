@extends('layouts.dashboard')

@section('title', __('Report an Issue'))

@section('dashboard_content')
<section class="auth-card" aria-label="{{ __('Report an Issue') }}">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h1 style="margin:0;">{{ __('Report an Issue') }}</h1>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline">{{ __('View My Tickets') }}</a>
    </div>

    @if($errors->any())
        <div role="alert" style="margin-top:12px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, #ef4444 8%);">
            <ul style="margin:0; padding-left:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tickets.store') }}" style="margin-top:24px;">
        @csrf

        <div style="display:grid; gap:20px;">
            <div>
                <label for="error_type" style="display:block; margin-bottom:6px; font-weight:600;">{{ __('Error Type') }}</label>
                <select id="error_type" name="error_type" required style="width:100%; padding:10px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface);">
                    <option value="">{{ __('Select the type of error...') }}</option>
                    <option value="simulation_error" {{ old('error_type') === 'simulation_error' ? 'selected' : '' }}>{{ __('Simulation Error') }}</option>
                    <option value="visual_error" {{ old('error_type') === 'visual_error' ? 'selected' : '' }}>{{ __('Visual/UI Error') }}</option>
                    <option value="personal_error" {{ old('error_type') === 'personal_error' ? 'selected' : '' }}>{{ __('Account/Personal Error') }}</option>
                    <option value="translation_error" {{ old('error_type') === 'translation_error' ? 'selected' : '' }}>{{ __('Translation Error') }}</option>
                    <option value="performance_issue" {{ old('error_type') === 'performance_issue' ? 'selected' : '' }}>{{ __('Performance Issue') }}</option>
                    <option value="bug_report" {{ old('error_type') === 'bug_report' ? 'selected' : '' }}>{{ __('Bug Report') }}</option>
                    <option value="feature_request" {{ old('error_type') === 'feature_request' ? 'selected' : '' }}>{{ __('Feature Request') }}</option>
                    <option value="other" {{ old('error_type') === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                </select>
                <p style="margin:8px 0 0; font-size:13px; color:var(--c-on-surface-2);">{{ __('Please select the category that best describes your issue') }}</p>
            </div>

            <div>
                <label for="subject" style="display:block; margin-bottom:6px; font-weight:600;">{{ __('Title') }}</label>
                <input 
                    type="text" 
                    id="subject" 
                    name="subject" 
                    value="{{ old('subject') }}" 
                    required
                    maxlength="255"
                    placeholder="{{ __('Brief title for your report...') }}"
                    style="width:100%; padding:10px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface);"
                >
                <p style="margin:8px 0 0; font-size:13px; color:var(--c-on-surface-2);">{{ __('A short, descriptive title for your issue') }}</p>
            </div>

            <div>
                <label for="description" style="display:block; margin-bottom:6px; font-weight:600;">{{ __('Description') }}</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="12" 
                    required
                    maxlength="2000"
                    placeholder="{{ __('Please describe the issue you\'re experiencing in detail (up to 400 words)...') }}"
                    style="width:100%; padding:10px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface); font-family:inherit; resize:vertical;"
                >{{ old('description') }}</textarea>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                    <p style="margin:0; font-size:13px; color:var(--c-on-surface-2);">{{ __('Maximum 400 words. Please provide as much detail as possible.') }}</p>
                    <span id="word-count" style="font-size:13px; color:var(--c-on-surface-2);">{{ __('0 words') }}</span>
                </div>
            </div>

            <div style="display:flex; gap:12px; margin-top:8px;">
                <button type="submit" class="btn btn-primary">{{ __('Submit Report') }}</button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
            </div>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('description');
    const wordCount = document.getElementById('word-count');
    const wordLabel = "{{ __('words') }}";
    
    function updateWordCount() {
        const text = textarea.value.trim();
        const words = text ? text.split(/\s+/).filter(word => word.length > 0) : [];
        const count = words.length;
    wordCount.textContent = count + ' ' + wordLabel;
        
        if (count > 400) {
            wordCount.style.color = '#ef4444';
        } else if (count > 350) {
            wordCount.style.color = '#f59e0b';
        } else {
            wordCount.style.color = 'var(--c-on-surface-2)';
        }
    }
    
    textarea.addEventListener('input', updateWordCount);
    updateWordCount();
});
</script>
@endsection

