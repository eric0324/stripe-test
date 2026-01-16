<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($failed ?? false) ? __('payment.failed.title') : __('payment.success.title') }}</title>

    <!-- Facebook Pixel Code -->
    @if($fbPixelId ?? false)
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $fbPixelId }}');
    fbq('track', 'PageView');
    @if(!($failed ?? false) && ($amount ?? 0) > 0)
    fbq('track', 'Purchase', {
        value: {{ $amount }},
        currency: '{{ $currency ?? "KRW" }}',
        content_type: 'product',
        content_name: '{{ $paymentType === "installment" ? "12-Month Subscription" : "One-time Payment" }}'
    });
    @endif
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $fbPixelId }}&ev=PageView&noscript=1"/></noscript>
    @endif
    <!-- End Facebook Pixel Code -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .container {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            max-width: 440px;
            width: 100%;
            padding: 48px 32px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        .success-icon {
            width: 64px;
            height: 64px;
            background: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.4s ease-out;
        }

        .failed-icon {
            width: 64px;
            height: 64px;
            background: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.4s ease-out;
        }

        .failed-icon svg {
            width: 32px;
            height: 32px;
            stroke: white;
            stroke-width: 3;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .success-icon svg {
            width: 32px;
            height: 32px;
            stroke: white;
            stroke-width: 3;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            animation: checkmark 0.4s ease-out 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes checkmark {
            from {
                stroke-dasharray: 30;
                stroke-dashoffset: 30;
            }
            to {
                stroke-dashoffset: 0;
            }
        }

        h1 {
            font-size: 22px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 16px;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #2563eb;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .message {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 32px;
            white-space: pre-line;
        }

        .back-btn {
            display: inline-block;
            padding: 12px 28px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .back-btn:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        @if($failed ?? false)
            <div class="failed-icon">
                <svg viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>

            <h1>{{ __('payment.failed.title') }}</h1>
            <p class="message">{{ __('payment.failed.message') }}</p>
            <a href="/" class="back-btn">{{ __('payment.failed.back_payment') }}</a>
        @else
            <div class="success-icon">
                <svg viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <h1>{{ __('payment.success.title') }}</h1>

            @if($paymentType === 'installment')
                <div class="badge">{{ __('payment.success.installment_badge') }}</div>
                <p class="message">{{ __('payment.success.installment_message') }}</p>
            @else
                <div class="badge">{{ __('payment.success.onetime_badge') }}</div>
                <p class="message">{{ __('payment.success.onetime_message') }}</p>
            @endif

            <a href="/" class="back-btn">{{ __('payment.success.back_home') }}</a>
        @endif
    </div>
</body>
</html>
