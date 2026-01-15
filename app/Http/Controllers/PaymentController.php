<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use App\Models\PricingTier;
use App\Services\FacebookService;
use App\Services\TeachableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\PaymentIntent;
use Stripe\Price;
use Stripe\Product;
use Stripe\Subscription;
use Stripe\PromotionCode;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }
    }

    public function index(Request $request)
    {
        if ($request->has('lang')) {
            app()->setLocale($request->query('lang'));
            session(['locale' => $request->query('lang')]);
        } elseif (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        $tier = PricingTier::getCurrentTier();

        if (!$tier) {
            abort(503, 'No active pricing tier');
        }

        return view('payment', [
            'stripeKey' => config('services.stripe.key'),
            'tier' => $tier,
            'fbPixelId' => config('services.facebook.pixel_id'),
            'successUrl' => config('services.payment.success_url'),
        ]);
    }

    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'coupon_code' => 'nullable|string',
        ]);

        $tier = PricingTier::getCurrentTier();

        if (!$tier) {
            return response()->json(['error' => 'No active pricing tier'], 400);
        }

        $amount = $tier->one_time_price;

        if ($request->coupon_code) {
            try {
                $promotionCodes = PromotionCode::all([
                    'code' => $request->coupon_code,
                    'active' => true,
                    'expand' => ['data.coupon'],
                ]);

                if (count($promotionCodes->data) > 0) {
                    $promotionCode = $promotionCodes->data[0];
                    $coupon = $promotionCode->coupon;

                    // Handle new Stripe API structure
                    if (!$coupon && isset($promotionCode->promotion->coupon)) {
                        $coupon = \Stripe\Coupon::retrieve($promotionCode->promotion->coupon);
                    }

                    if ($coupon && $coupon->percent_off) {
                        $amount = (int) ($amount * (100 - $coupon->percent_off) / 100);
                    } elseif ($coupon && $coupon->amount_off) {
                        $amount = max(0, $amount - $coupon->amount_off);
                    }
                } else {
                    return response()->json(['error' => __('payment.message.coupon_invalid')], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => __('payment.message.coupon_error')], 400);
            }
        }

        try {
            $customer = Customer::create([
                'email' => $request->email,
                'preferred_locales' => [app()->getLocale()],
            ]);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'krw',
                'customer' => $customer->id,
                'payment_method_types' => ['kakao_pay', 'naver_pay'],
                'metadata' => [
                    'payment_type' => 'one_time',
                    'email' => $request->email,
                    'tier_id' => $tier->id,
                    'tier_name' => $tier->name,
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $amount,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createSubscription(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $tier = PricingTier::getCurrentTier();

        if (!$tier) {
            return response()->json(['error' => 'No active pricing tier'], 400);
        }

        try {
            $customer = Customer::create([
                'email' => $request->email,
                'preferred_locales' => [app()->getLocale()],
            ]);

            // 用 PaymentIntent 收取第一期款項，並設定未來自動扣款
            $paymentIntent = PaymentIntent::create([
                'amount' => $tier->installment_price,
                'currency' => 'krw',
                'customer' => $customer->id,
                'setup_future_usage' => 'off_session',
                'payment_method_types' => ['kakao_pay', 'naver_pay'],
                'metadata' => [
                    'payment_type' => 'installment',
                    'email' => $request->email,
                    'tier_id' => $tier->id,
                    'tier_name' => $tier->name,
                    'installment_price' => $tier->installment_price,
                    'create_subscription' => 'true',
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $tier->installment_price,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $tier = PricingTier::getCurrentTier();

        if (!$tier) {
            return response()->json(['error' => 'No active pricing tier'], 400);
        }

        try {
            $promotionCodes = PromotionCode::all([
                'code' => $request->coupon_code,
                'active' => true,
            ]);

            if (count($promotionCodes->data) === 0) {
                return response()->json(['error' => __('payment.message.coupon_invalid')], 400);
            }

            $promotionCode = $promotionCodes->data[0];
            $coupon = $promotionCode->coupon;

            // Handle new Stripe API structure: coupon ID in promotion.coupon
            if (!$coupon && isset($promotionCode->promotion->coupon)) {
                $coupon = \Stripe\Coupon::retrieve($promotionCode->promotion->coupon);
            }

            if (!$coupon) {
                return response()->json(['error' => __('payment.message.coupon_invalid')], 400);
            }

            $originalAmount = $tier->one_time_price;
            $discountedAmount = $originalAmount;

            if ($coupon->percent_off) {
                $discountedAmount = (int) ($originalAmount * (100 - $coupon->percent_off) / 100);
            } elseif ($coupon->amount_off) {
                $discountedAmount = max(0, $originalAmount - $coupon->amount_off);
            }

            return response()->json([
                'valid' => true,
                'originalAmount' => $originalAmount,
                'discountedAmount' => $discountedAmount,
                'discount' => $originalAmount - $discountedAmount,
                'percentOff' => $coupon->percent_off,
                'amountOff' => $coupon->amount_off,
            ]);
        } catch (\Exception $e) {
            \Log::error('Coupon error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => __('payment.message.coupon_error')], 500);
        }
    }

    public function success(Request $request)
    {
        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        // Check the redirect_status from Stripe
        $redirectStatus = $request->query('redirect_status');
        $paymentIntentId = $request->query('payment_intent');

        // If redirect_status is not 'succeeded', show failed page
        if ($redirectStatus && $redirectStatus !== 'succeeded') {
            return view('success', [
                'paymentType' => $request->query('type', 'one_time'),
                'failed' => true,
            ]);
        }

        // If payment_intent exists, verify the payment status
        if ($paymentIntentId) {
            try {
                $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

                // Check if payment actually succeeded
                if ($paymentIntent->status !== 'succeeded') {
                    return view('success', [
                        'paymentType' => $request->query('type', 'one_time'),
                        'failed' => true,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to verify payment', ['error' => $e->getMessage()]);
            }
        }

        return view('success', [
            'paymentType' => $request->query('type', 'one_time'),
            'failed' => false,
        ]);
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Webhook signature verification failed'], 400);
        }

        $teachable = new TeachableService();
        $facebook = new FacebookService();

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                \Log::info('Payment succeeded', ['id' => $paymentIntent->id]);

                // Get email and payment details
                $email = $paymentIntent->metadata->email ?? null;
                $paymentType = $paymentIntent->metadata->payment_type ?? '';
                $createSubscription = $paymentIntent->metadata->create_subscription ?? null;

                if ($email && $paymentType === 'one_time') {
                    // Send Facebook CAPI Purchase event for one-time payment
                    $facebook->sendPurchaseEvent([
                        'email' => $email,
                        'value' => $paymentIntent->amount,
                        'currency' => strtoupper($paymentIntent->currency),
                        'order_id' => $paymentIntent->id,
                        'content_name' => 'One-time Payment',
                        'content_type' => 'product',
                        'action_source' => 'website',
                    ]);

                    // Enroll user in Teachable
                    $enrolled = $teachable->createAndEnroll($email);
                    \Log::info('Teachable enrollment result', [
                        'email' => $email,
                        'enrolled' => $enrolled,
                    ]);

                    // Send welcome email
                    $this->sendWelcomeEmail($email, $paymentIntent->customer);
                }

                // Create subscription for installment payments
                if ($email && $paymentType === 'installment' && $createSubscription === 'true') {
                    try {
                        $customerId = $paymentIntent->customer;
                        $paymentMethodId = $paymentIntent->payment_method;
                        $installmentPrice = $paymentIntent->metadata->installment_price ?? $paymentIntent->amount;
                        $tierName = $paymentIntent->metadata->tier_name ?? 'Monthly';

                        // Set the payment method as default for the customer
                        Customer::update($customerId, [
                            'invoice_settings' => [
                                'default_payment_method' => $paymentMethodId,
                            ],
                        ]);

                        // Create a recurring price
                        $price = Price::create([
                            'unit_amount' => (int) $installmentPrice,
                            'currency' => 'krw',
                            'recurring' => ['interval' => 'month'],
                            'product_data' => [
                                'name' => $tierName . ' - Monthly',
                            ],
                        ]);

                        // Create subscription starting next month (first payment already made)
                        $subscription = Subscription::create([
                            'customer' => $customerId,
                            'items' => [['price' => $price->id]],
                            'default_payment_method' => $paymentMethodId,
                            'billing_cycle_anchor' => strtotime('+1 month'),
                            'proration_behavior' => 'none',
                            'cancel_at' => strtotime('+12 months'),
                            'metadata' => [
                                'email' => $email,
                                'payment_type' => 'installment',
                            ],
                        ]);

                        \Log::info('Subscription created', [
                            'subscription_id' => $subscription->id,
                            'customer_id' => $customerId,
                        ]);

                        // Send Facebook CAPI Purchase event
                        $facebook->sendPurchaseEvent([
                            'email' => $email,
                            'value' => $paymentIntent->amount,
                            'currency' => strtoupper($paymentIntent->currency),
                            'order_id' => $paymentIntent->id,
                            'content_name' => '12-Month Subscription',
                            'content_type' => 'product',
                            'action_source' => 'website',
                        ]);

                        // Enroll user in Teachable
                        $enrolled = $teachable->createAndEnroll($email);
                        \Log::info('Teachable enrollment result (subscription)', [
                            'email' => $email,
                            'enrolled' => $enrolled,
                        ]);

                        // Send welcome email
                        $this->sendWelcomeEmail($email, $customerId);
                    } catch (\Exception $e) {
                        \Log::error('Failed to create subscription', [
                            'error' => $e->getMessage(),
                            'payment_intent_id' => $paymentIntent->id,
                        ]);
                    }
                }
                break;

            case 'invoice.paid':
                $invoice = $event->data->object;
                \Log::info('Invoice paid', ['id' => $invoice->id]);

                // Enroll user in Teachable for first subscription payment
                if ($invoice->billing_reason === 'subscription_create') {
                    $customerEmail = $invoice->customer_email;
                    if ($customerEmail) {
                        // Send Facebook CAPI Purchase event for subscription
                        $facebook->sendPurchaseEvent([
                            'email' => $customerEmail,
                            'value' => $invoice->amount_paid,
                            'currency' => strtoupper($invoice->currency),
                            'order_id' => $invoice->id,
                            'content_name' => '12-Month Subscription',
                            'content_type' => 'product',
                            'action_source' => 'website',
                        ]);

                        // Enroll user in Teachable
                        $enrolled = $teachable->createAndEnroll($customerEmail);
                        \Log::info('Teachable enrollment result (subscription)', [
                            'email' => $customerEmail,
                            'enrolled' => $enrolled,
                        ]);
                    }
                }
                break;

            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                \Log::info('Subscription ended', ['id' => $subscription->id]);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Send welcome email to customer with locale from Stripe customer preferences
     */
    private function sendWelcomeEmail(string $email, string $customerId): void
    {
        try {
            $customer = Customer::retrieve($customerId);
            $preferredLocales = $customer->preferred_locales ?? [];
            $locale = $preferredLocales[0] ?? 'ko';

            // Ensure locale is supported, default to Korean
            if (!in_array($locale, ['en', 'ko'])) {
                $locale = 'ko';
            }

            Mail::to($email)->send(new WelcomeEmail($email, $locale));

            \Log::info('Welcome email sent', [
                'email' => $email,
                'locale' => $locale,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
