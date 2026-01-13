<?php

return [
    // Page title
    'title' => 'Payment',
    'subtitle' => 'Choose your payment method',

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
];
