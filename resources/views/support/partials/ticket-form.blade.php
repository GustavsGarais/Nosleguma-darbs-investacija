<section class="auth-card" style="margin-top:24px;" aria-label="{{ __('Report an Issue') }}">
    <h2 style="margin:0 0 8px; font-size:1.25rem;">{{ __('Report an Issue') }}</h2>
    <p style="margin:0 0 16px; color:var(--c-on-surface-2); font-size:14px; line-height:1.5;">
        {{ __('Submit a detailed report while signed in. We will link it to your account.') }}
    </p>

    @if($errors->getBag('ticket')->isNotEmpty())
        <div role="alert" style="margin-bottom:16px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, #ef4444 8%);">
            <ul style="margin:0; padding-left:20px;">
                @foreach($errors->getBag('ticket')->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('support.ticket.store') }}" style="margin-top:8px;">
        @csrf

        <div style="display:grid; gap:20px;">
            <div>
                <label for="error_type" style="display:block; margin-bottom:6px; font-weight:600;">{{ __('Error Type') }}</label>
                <select id="error_type" name="error_type" required class="footer-email-input" style="width:100%; padding:10px 12px;">
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
                    class="footer-email-input"
                    style="width:100%; padding:10px 12px;"
                >
                <p style="margin:8px 0 0; font-size:13px; color:var(--c-on-surface-2);">{{ __('A short, descriptive title for your issue') }}</p>
            </div>

            <div>
                <label for="ticket_description" style="display:block; margin-bottom:6px; font-weight:600;">{{ __('Description') }}</label>
                <textarea
                    id="ticket_description"
                    name="description"
                    rows="12"
                    required
                    maxlength="2000"
                    placeholder="{{ __('Please describe the issue you\'re experiencing in detail (up to 400 words)...') }}"
                    class="footer-email-input"
                    style="width:100%; padding:10px 12px; font-family:inherit; resize:vertical;"
                >{{ old('description') }}</textarea>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                    <p style="margin:0; font-size:13px; color:var(--c-on-surface-2);">{{ __('Maximum 400 words. Please provide as much detail as possible.') }}</p>
                    <span id="ticket-word-count" style="font-size:13px; color:var(--c-on-surface-2);">0 {{ __('words') }}</span>
                </div>
            </div>

            <div style="display:flex; gap:12px; margin-top:8px; flex-wrap:wrap;">
                <button type="submit" class="btn btn-primary">{{ __('Submit Report') }}</button>
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">{{ __('View My Tickets') }}</a>
                <a href="{{ route('simulations.index') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
            </div>
        </div>
    </form>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('ticket_description');
    const wordCount = document.getElementById('ticket-word-count');
    if (!textarea || !wordCount) return;
    const wordLabel = @json(__('words'));
    function updateWordCount() {
        const text = textarea.value.trim();
        const words = text ? text.split(/\s+/).filter(function(word) { return word.length > 0; }) : [];
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
@endpush
