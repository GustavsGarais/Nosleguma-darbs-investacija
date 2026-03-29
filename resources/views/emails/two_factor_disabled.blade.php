@php
    $btnStyle = 'display:inline-block;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;';
    $primaryBtn = $btnStyle . 'background:#07a05a;color:#ffffff;';
    $secondaryBtn = $btnStyle . 'background:#f1f5f9;color:#0f172a;border:1px solid #cbd5e1;';
@endphp
<div style="font-family: Inter, system-ui, -apple-system, Segoe UI, Arial, sans-serif; line-height: 1.6; max-width: 560px;">
    <p style="margin:0 0 12px;">
        {{ __('mail.2fa_disabled_greeting', ['name' => $userName]) }}
    </p>

    <p style="margin:0 0 12px;">
        {{ __('mail.2fa_disabled_body') }}
    </p>

    <p style="margin:0 0 12px;">
        {{ __('mail.2fa_disabled_ticket', ['id' => $ticketId]) }}
    </p>

    <p style="margin:0 0 16px;">
        {{ __('mail.2fa_disabled_footer') }}
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 20px;border-collapse:collapse;">
        <tr>
            <td style="padding-right:10px;padding-bottom:8px;">
                <a href="{{ $loginUrl }}" style="{{ $primaryBtn }}">{{ __('mail.2fa_disabled_link_login') }}</a>
            </td>
            <td style="padding-bottom:8px;">
                <a href="{{ $passwordResetUrl }}" style="{{ $secondaryBtn }}">{{ __('mail.2fa_disabled_link_reset') }}</a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 8px;font-size:13px;color:#64748b;">
        {{ __('mail.2fa_disabled_links_plain') }}<br>
        <span style="word-break:break-all;">{{ $loginUrl }}</span><br>
        <span style="word-break:break-all;">{{ $passwordResetUrl }}</span>
    </p>

    <p style="margin:16px 0 0; color:#6b7280; font-size:13px;">
        {{ __('mail.2fa_disabled_auto', ['app' => config('app.name')]) }}
    </p>
</div>
