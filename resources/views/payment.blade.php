<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('payment.title') }}</title>

    <!-- Facebook Pixel Code -->
    @if($fbPixelId)
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
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $fbPixelId }}&ev=PageView&noscript=1"/></noscript>
    @endif
    <!-- End Facebook Pixel Code -->

    <script src="https://js.stripe.com/v3/"></script>
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
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Allow selection in input fields */
        input, textarea {
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }

        .container {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            width: 50vw;
            min-width: 480px;
            padding: 32px 48px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 28px;
        }

        h1 {
            font-size: 22px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 6px;
        }

        .subtitle {
            color: #6b7280;
            font-size: 14px;
        }

        .lang-switch {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            width: 100%;
            position: absolute;
            left: 0;
            bottom: -40px;
        }

        .wrapper {
            position: relative;
        }

        .lang-switch a {
            color: #9ca3af;
            font-size: 12px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .lang-switch a:hover {
            color: #6b7280;
        }

        .lang-switch a.active {
            color: #111827;
        }

        .lang-switch .divider {
            color: #d1d5db;
            font-size: 12px;
        }

        .plan-selector {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .plan-option {
            flex: 1;
            padding: 16px 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .plan-option:hover {
            border-color: #3b82f6;
        }

        .plan-option.selected {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .plan-option input {
            display: none;
        }

        .plan-name {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .plan-price {
            font-size: 20px;
            font-weight: 700;
            color: #3b82f6;
        }

        .plan-price-suffix {
            font-size: 13px;
            font-weight: 400;
            color: #6b7280;
        }

        .plan-detail {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        input[type="email"],
        input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            color: #111827;
            transition: border-color 0.2s;
        }

        input[type="email"]::placeholder,
        input[type="text"]::placeholder {
            color: #9ca3af;
        }

        input[type="email"]:focus,
        input[type="text"]:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .coupon-section {
            display: none;
        }

        .coupon-section.show {
            display: block;
        }

        .coupon-row {
            display: flex;
            gap: 8px;
        }

        .coupon-row input {
            flex: 1;
        }

        .coupon-btn {
            padding: 10px 16px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
        }

        .coupon-btn:hover {
            background: #e5e7eb;
        }

        .coupon-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .coupon-result {
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 13px;
        }

        .coupon-result.success {
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #16a34a;
        }

        .coupon-result.error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
        }

        .price-summary {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .price-row:last-child {
            margin-bottom: 0;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-weight: 600;
            color: #111827;
        }

        .price-row.discount span:last-child {
            color: #16a34a;
        }

        #payment-element {
            margin-bottom: 20px;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .submit-btn:hover:not(:disabled) {
            background: #2563eb;
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 12px;
            text-align: center;
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 16px;
            font-size: 11px;
            color: #64748b;
        }

        .secure-badge svg {
            width: 12px;
            height: 12px;
        }

        .legal-disclosure {
            margin-top: 12px;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
            line-height: 1.4;
        }

        .legal-disclosure a {
            color: #6b7280;
            text-decoration: underline;
        }

        .legal-disclosure a:hover {
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="wrapper">
    <div class="container">
        <div class="header">
            <h1>{{ __('payment.title') }}</h1>
            <p class="subtitle">{{ __('payment.subtitle') }}</p>
        </div>

        <div class="plan-selector">
            <label class="plan-option selected" id="plan-onetime">
                <input type="radio" name="plan" value="onetime" checked>
                <div class="plan-name">{{ __('payment.plan.onetime.name') }}</div>
                <div class="plan-price">₩{{ number_format($tier->one_time_price) }}</div>
                <div class="plan-detail">{{ __('payment.plan.onetime.detail') }}</div>
            </label>
            <label class="plan-option" id="plan-installment">
                <input type="radio" name="plan" value="installment">
                <div class="plan-name">{{ __('payment.plan.installment.name') }}</div>
                <div class="plan-price">₩{{ number_format($tier->installment_price) }}<span class="plan-price-suffix">{{ __('payment.plan.installment.price_suffix') }}</span></div>
                <div class="plan-detail">{{ __('payment.price.total') }} ₩{{ number_format($tier->installment_price * 12) }}</div>
            </label>
        </div>

        <form id="payment-form">
            <div class="form-group">
                <label for="email">{{ __('payment.form.email') }}</label>
                <input type="email" id="email" name="email" required placeholder="{{ __('payment.form.email_placeholder') }}">
            </div>

            <div class="form-group coupon-section show" id="coupon-section">
                <label for="coupon">{{ __('payment.form.coupon') }}</label>
                <div class="coupon-row">
                    <input type="text" id="coupon" name="coupon" placeholder="{{ __('payment.form.coupon_placeholder') }}">
                    <button type="button" class="coupon-btn" id="apply-coupon">{{ __('payment.form.apply') }}</button>
                </div>
                <div class="coupon-result" id="coupon-result" style="display: none;"></div>
            </div>

            <div class="price-summary" id="price-summary">
                <div class="price-row">
                    <span>{{ __('payment.price.subtotal') }}</span>
                    <span id="subtotal">₩{{ number_format($tier->one_time_price) }}</span>
                </div>
                <div class="price-row discount" id="discount-row" style="display: none;">
                    <span>{{ __('payment.price.discount') }}</span>
                    <span id="discount-amount">-₩0</span>
                </div>
                <div class="price-row">
                    <span>{{ __('payment.price.total') }}</span>
                    <span id="total">₩{{ number_format($tier->one_time_price) }}</span>
                </div>
            </div>

            <div id="payment-element"></div>

            <button type="submit" class="submit-btn" id="submit-btn">
                <span id="btn-text">{{ __('payment.button.pay', ['amount' => number_format($tier->one_time_price)]) }}</span>
            </button>

            <div class="error-message" id="error-message" style="display: none;"></div>

            <div class="secure-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span>Secured by Stripe</span>
            </div>

            <div class="legal-disclosure">
                This transaction is processed by NICEPAY in accordance with its <a href="https://www.nicepay.co.kr/terms/service.do" target="_blank" rel="noopener">terms of service</a>.
            </div>
        </form>
    </div>

    <div class="lang-switch">
        <a href="/?lang=ko" class="{{ app()->getLocale() === 'ko' ? 'active' : '' }}">한국어</a>
        <span class="divider">|</span>
        <a href="/?lang=en" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">English</a>
    </div>
    </div>

    <script>
        const stripe = Stripe('{{ $stripeKey }}');
        let elements;
        let currentPlan = 'onetime';
        let oneTimePrice = {{ $tier->one_time_price }};
        let installmentPrice = {{ $tier->installment_price }};
        let originalAmount = oneTimePrice;
        let discountAmount = 0;
        let clientSecret = null;
        let fbEventId = null;
        const successUrl = '{{ $successUrl ?: '' }}';

        // Facebook Pixel helper
        function generateEventId() {
            return 'evt_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        function trackFbEvent(eventName, params = {}) {
            if (typeof fbq !== 'undefined') {
                fbEventId = generateEventId();
                fbq('track', eventName, params, { eventID: fbEventId });
            }
        }

        // i18n strings
        const i18n = {
            coupon_applied: "{{ __('payment.message.coupon_applied', ['amount' => ':amount']) }}",
            coupon_invalid: "{{ __('payment.message.coupon_invalid') }}",
            email_required: "{{ __('payment.message.email_required') }}",
            checking: "{{ __('payment.form.checking') }}",
            apply: "{{ __('payment.form.apply') }}",
            pay: "{{ __('payment.button.pay', ['amount' => ':amount']) }}",
            subscribe: "{{ __('payment.button.subscribe') }}",
            complete: "{{ __('payment.button.complete') }}",
            start_subscription: "{{ __('payment.button.start_subscription') }}",
            processing: "{{ __('payment.button.processing') }}",
            monthly_format: "{{ __('payment.price.monthly_format', ['price' => ':price']) }}",
            monthly: "{{ __('payment.price.monthly') }}",
        };

        const couponSection = document.getElementById('coupon-section');
        const form = document.getElementById('payment-form');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const errorMessage = document.getElementById('error-message');

        // Plan selection
        document.querySelectorAll('.plan-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.plan-option').forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                currentPlan = this.querySelector('input').value;

                if (currentPlan === 'onetime') {
                    originalAmount = oneTimePrice;
                    couponSection.classList.add('show');
                    updatePriceSummary(originalAmount, discountAmount);
                    btnText.textContent = i18n.pay.replace(':amount', (originalAmount - discountAmount).toLocaleString());
                } else {
                    originalAmount = installmentPrice;
                    couponSection.classList.remove('show');
                    discountAmount = 0;
                    document.getElementById('discount-row').style.display = 'none';
                    document.getElementById('coupon-result').style.display = 'none';
                    document.getElementById('coupon').value = '';
                    document.getElementById('subtotal').textContent = i18n.monthly_format.replace(':price', installmentPrice.toLocaleString());
                    document.getElementById('total').textContent = `₩${installmentPrice.toLocaleString()}` + i18n.monthly;
                    btnText.textContent = i18n.subscribe.replace(':amount', installmentPrice.toLocaleString());
                }

                if (elements) {
                    document.getElementById('payment-element').innerHTML = '';
                    elements = null;
                    clientSecret = null;
                }
            });
        });

        // Apply coupon
        document.getElementById('apply-coupon').addEventListener('click', async function() {
            const couponCode = document.getElementById('coupon').value.trim();
            if (!couponCode) return;

            this.disabled = true;
            this.textContent = i18n.checking;

            try {
                const response = await fetch('/payment/coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ coupon_code: couponCode })
                });

                const data = await response.json();
                const resultEl = document.getElementById('coupon-result');

                if (response.ok && data.valid) {
                    discountAmount = data.discount;
                    resultEl.className = 'coupon-result success';
                    resultEl.textContent = i18n.coupon_applied.replace(':amount', data.discount.toLocaleString());
                    resultEl.style.display = 'block';
                    updatePriceSummary(originalAmount, discountAmount);
                    btnText.textContent = i18n.pay.replace(':amount', (originalAmount - discountAmount).toLocaleString());
                } else {
                    resultEl.className = 'coupon-result error';
                    resultEl.textContent = data.error || i18n.coupon_invalid;
                    resultEl.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
            }

            this.disabled = false;
            this.textContent = i18n.apply;
        });

        function updatePriceSummary(subtotal, discount) {
            document.getElementById('subtotal').textContent = `₩${subtotal.toLocaleString()}`;

            if (discount > 0) {
                document.getElementById('discount-row').style.display = 'flex';
                document.getElementById('discount-amount').textContent = `-₩${discount.toLocaleString()}`;
            } else {
                document.getElementById('discount-row').style.display = 'none';
            }

            document.getElementById('total').textContent = `₩${(subtotal - discount).toLocaleString()}`;
        }

        // Form submission
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            if (!email) {
                showError(i18n.email_required);
                return;
            }

            setLoading(true);

            try {
                const endpoint = currentPlan === 'onetime' ? '/payment/intent' : '/payment/subscription';
                const body = currentPlan === 'onetime'
                    ? { email, coupon_code: document.getElementById('coupon').value.trim() || null }
                    : { email };

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(body)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Payment failed');
                }

                clientSecret = data.clientSecret;

                if (!elements) {
                    // Track InitiateCheckout event
                    const checkoutValue = currentPlan === 'onetime'
                        ? (originalAmount - discountAmount)
                        : installmentPrice;
                    trackFbEvent('InitiateCheckout', {
                        value: checkoutValue,
                        currency: 'KRW',
                        content_type: 'product',
                        content_name: currentPlan === 'onetime' ? 'One-time Payment' : '12-Month Subscription'
                    });

                    elements = stripe.elements({
                        clientSecret,
                        locale: '{{ app()->getLocale() }}',
                        appearance: {
                            theme: 'stripe',
                            variables: {
                                colorPrimary: '#3b82f6',
                                colorBackground: '#ffffff',
                                colorText: '#111827',
                                colorTextSecondary: '#6b7280',
                                colorDanger: '#dc2626',
                                fontFamily: 'Inter, -apple-system, sans-serif',
                                borderRadius: '6px',
                            },
                            rules: {
                                '.Input': {
                                    border: '1px solid #d1d5db',
                                    boxShadow: 'none',
                                },
                                '.Input:focus': {
                                    border: '1px solid #3b82f6',
                                    boxShadow: 'none',
                                },
                            }
                        }
                    });

                    const paymentElement = elements.create('payment', {
                        wallets: {
                            link: 'never'
                        },
                        fields: {
                            billingDetails: {
                                address: 'never'
                            }
                        }
                    });
                    paymentElement.mount('#payment-element');

                    setLoading(false);
                    btnText.textContent = currentPlan === 'onetime' ? i18n.complete : i18n.start_subscription;
                    return;
                }

                const returnUrl = successUrl
                    ? `${successUrl}?type=${currentPlan}`
                    : `${window.location.origin}/payment/success?type=${currentPlan}`;

                const { error } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: returnUrl,
                        receipt_email: email,
                        payment_method_data: {
                            billing_details: {
                                email: email,
                                address: {
                                    country: 'KR',
                                    postal_code: '00000',
                                    state: '',
                                    city: '',
                                    line1: '',
                                    line2: ''
                                }
                            }
                        }
                    }
                });

                if (error) {
                    showError(error.message);
                    setLoading(false);
                }
            } catch (error) {
                showError(error.message);
                setLoading(false);
            }
        });

        function setLoading(isLoading) {
            submitBtn.disabled = isLoading;
            if (isLoading) {
                btnText.innerHTML = '<span class="loading"></span>' + i18n.processing;
            }
        }

        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
