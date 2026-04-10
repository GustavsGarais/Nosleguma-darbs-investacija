@php
    $btnStyle = 'display:inline-block;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;';
    $dangerBtn = $btnStyle . 'background:#b91c1c;color:#ffffff;';
@endphp
<div style="font-family: Inter, system-ui, -apple-system, Segoe UI, Arial, sans-serif; line-height: 1.6; max-width: 560px;">
    <p style="margin:0 0 12px;">
        {{ __('mail.password_reset_initiated_greeting', ['name' => $userName]) }}
    </p>

    <p style="margin:0 0 12px;">
        {{ __('mail.password_reset_initiated_body') }}
    </p>

    @if (! empty(trim((string) $supportMessage)))
        <p style="margin:0 0 6px; font-weight:600;">
            {{ __('mail.password_reset_initiated_your_message') }}
        </p>
        <div style="margin:12px 0;padding:12px 14px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;font-size:14px;white-space:pre-wrap;word-break:break-word;">{{ $supportMessage }}</div>
    @endif

    <p style="margin:16px 0 8px;">
        {{ __('mail.password_reset_initiated_if_you') }}
    </p>

    <p style="margin:0 0 16px;">
        {{ __('mail.password_reset_initiated_if_not') }}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 20px;border-collapse:collapse;">
        <tr>
            <td style="padding-bottom:8px;">
                <a href="{{ $reportUnauthorizedUrl }}" style="{{ $dangerBtn }}">{{ __('mail.password_reset_initiated_btn_not_me') }}</a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 12px;font-size:14px;">
        {{ __('mail.password_reset_initiated_footer') }}
    </p>

    <p style="margin:0 0 8px;font-size:13px;color:#64748b;">
        {{ __('mail.password_reset_initiated_links_plain') }}<br>
        <span style="word-break:break-all;">{{ $reportUnauthorizedUrl }}</span><br>
        <span style="word-break:break-all;">{{ $supportUrl }}</span>
    </p>

    <p style="margin:16px 0 0; color:#6b7280; font-size:13px;">
        {{ __('mail.password_reset_initiated_auto', ['app' => config('app.name')]) }}
    </p>
</div>
