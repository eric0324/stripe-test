<?php

return [
    // Page title
    'title' => 'Full Body Line Remodeling Project',
    'subtitle' => 'Choose your payment method',
    'course_title' => 'VOD 12-Month Course Pass',
    'course_info' => [
        '36 chapters · 20 hours · Pre-recorded VOD lectures',
        'This course is offered as a pre-order, with videos uploaded sequentially',
        '※ All lectures scheduled to be completed by February 15, 2026',
        'Unlimited viewing for 1 year from purchase date',
    ],
    'upload_schedule' => [
        'title' => 'This course is offered as a pre-order, with videos uploaded sequentially:',
        'items' => [
            'CH0, CH1, CH2, CH3: Uploaded, available now',
            'CH3 Practice Video: Scheduled for 2026/1/15',
            'CH4 + CH4 Practice Video: Scheduled for 2026/1/30',
            'CH5, CH6: Scheduled for 2026/2/15',
        ],
    ],

    // Plan selection
    'plan' => [
        'onetime' => [
            'name' => 'One-time Payment',
            'detail' => 'Coupon available',
        ],
        'installment' => [
            'name' => '12-Month Plan',
            'price_suffix' => '/mo',
            'detail_prefix' => 'Total ₩',
        ],
    ],

    // Form fields
    'form' => [
        'email' => 'Email',
        'email_placeholder' => 'example@email.com',
        'email_hint' => 'We recommend using Gmail. Some email domains may have delivery issues (e.g., iCloud).',
        'coupon' => 'Coupon Code',
        'coupon_placeholder' => 'Enter coupon code',
        'apply' => 'Apply',
        'checking' => 'Checking...',
    ],

    // Price summary
    'price' => [
        'subtotal' => 'Subtotal',
        'discount' => 'Discount',
        'total' => 'Total',
        'monthly' => '/mo',
        'monthly_format' => '₩:price x 12 months',
    ],

    // Buttons
    'button' => [
        'pay' => 'Pay ₩:amount',
        'subscribe' => 'Subscribe ₩:amount/mo',
        'complete' => 'Complete Payment',
        'start_subscription' => 'Start Subscription',
        'processing' => 'Processing...',
    ],

    // Messages
    'message' => [
        'coupon_applied' => 'Coupon applied! You save ₩:amount',
        'coupon_invalid' => 'Invalid coupon code',
        'coupon_error' => 'Failed to verify coupon',
        'email_required' => 'Please enter your email',
        'payment_error' => 'An error occurred during payment',
    ],

    // Success page
    'success' => [
        'title' => 'Payment Successful!',
        'onetime_badge' => 'One-time Payment',
        'installment_badge' => '12-Month Subscription',
        'onetime_message' => "Your payment has been processed successfully.\nA confirmation email has been sent.\nThank you for your purchase!",
        'installment_message' => "Your subscription has been activated.\nYou will be charged monthly for 12 months.\nA confirmation email has been sent.",
        'back_home' => 'Back to Home',
    ],

    // Failed page
    'failed' => [
        'title' => 'Payment Failed',
        'message' => "There was a problem processing your payment.\nPlease try again.",
        'back_payment' => 'Try Again',
    ],
];
