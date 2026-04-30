@php
    $btnStyle = 'display:inline-block;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('mail.account_blocked_subject') }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:system-ui,-apple-system,'Segoe UI',sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f4f5;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width:560px;background:#ffffff;border-radius:12px;padding:24px 22px;border:1px solid #e5e7eb;">
                    <tr>
                        <td>
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.6;">
                                {{ __('mail.account_blocked_body', ['email' => $blockedEmail]) }}
                            </p>
                            @if(!empty($appealNote))
                                <p style="margin:0 0 12px;font-size:14px;line-height:1.6;color:#374151;">
                                    <strong>{{ __('mail.account_blocked_note_label') }}</strong> {{ $appealNote }}
                                </p>
                            @endif
                            <p style="margin:0 0 18px;font-size:14px;line-height:1.6;color:#374151;">
                                {{ __('mail.account_blocked_appeal') }}
                            </p>
                            <p style="margin:0 0 20px;">
                                <a href="{{ $supportUrl }}" style="{{ $btnStyle }}background:#059669;color:#ffffff;">
                                    {{ __('mail.account_blocked_support_cta') }}
                                </a>
                            </p>
                            <p style="margin:0;font-size:12px;line-height:1.5;color:#6b7280;">
                                {{ __('mail.account_blocked_plain', ['url' => $supportUrl]) }}
                            </p>
                            <p style="margin:16px 0 0;font-size:12px;line-height:1.5;color:#9ca3af;">
                                {{ __('mail.account_blocked_auto', ['app' => config('app.name')]) }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
