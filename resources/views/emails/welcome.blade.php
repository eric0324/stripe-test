<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('email.welcome.subject', [], $locale) }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #ffffff; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <!-- Main Container -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 520px; background-color: #ffffff;">

                    <!-- Header -->
                    <tr>
                        <td style="padding-bottom: 32px; border-bottom: 1px solid #e5e7eb;">
                            <h1 style="margin: 0; color: #111827; font-size: 20px; font-weight: 600; text-align: center;">
                                {{ __('email.welcome.title', [], $locale) }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 0;">
                            <!-- Greeting -->
                            <p style="margin: 0 0 16px; color: #111827; font-size: 15px; line-height: 1.6;">
                                {{ __('email.welcome.greeting', [], $locale) }}
                            </p>

                            <!-- Success Message -->
                            <p style="margin: 0 0 28px; color: #374151; font-size: 15px; line-height: 1.7;">
                                {{ __('email.welcome.purchase_success', [], $locale) }}
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding: 0 0 28px;">
                                        <a href="https://sso.teachable.com/secure/2631543/identity/login/otp"
                                           target="_blank"
                                           style="display: inline-block; background-color: #3b82f6; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                            {{ __('email.welcome.cta_button', [], $locale) }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Login Instructions Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="background-color: #f9fafb; border-left: 3px solid #9ca3af; padding: 20px; border-radius: 4px;">
                                        <p style="margin: 0 0 12px; color: #111827; font-size: 14px; font-weight: 600;">
                                            {{ __('email.welcome.login_guide_title', [], $locale) }}
                                        </p>
                                        <ul style="margin: 0; padding: 0 0 0 18px; color: #374151; font-size: 13px; line-height: 1.8;">
                                            <li style="margin-bottom: 6px;">{{ __('email.welcome.login_step_1', [], $locale) }}</li>
                                            <li style="margin-bottom: 6px;">{{ __('email.welcome.login_step_2', [], $locale) }}</li>
                                            <li>{{ __('email.welcome.login_step_3', [], $locale) }}</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="border-top: 1px solid #e5e7eb; padding-top: 24px;">
                            <!-- Support Info -->
                            <p style="margin: 0 0 16px; color: #6b7280; font-size: 13px; line-height: 1.6; text-align: center;">
                                {{ __('email.welcome.support_message', [], $locale) }}<br>
                                <a href="mailto:service@satcool.kr" style="color: #3b82f6; text-decoration: none;">service@satcool.kr</a>
                            </p>

                            <!-- Closing -->
                            <p style="margin: 0; color: #374151; font-size: 14px; line-height: 1.6; text-align: center;">
                                {{ __('email.welcome.closing', [], $locale) }}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding-top: 32px; text-align: center;">
                            <p style="margin: 0 0 4px; color: #9ca3af; font-size: 12px;">
                                새트놀로지 주식회사 · SAT.KNOWLEDGE
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 11px;">
                                © {{ date('Y') }} SAT.KNOWLEDGE, KR
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
