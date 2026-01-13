<?php

namespace App\Http\Controllers;

use App\Models\PricingTier;
use App\Services\FacebookService;
use App\Services\TeachableService;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
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
                ]);

                if (count($promotionCodes->data) > 0) {
                    $promotionCode = $promotionCodes->data[0];
                    $coupon = $promotionCode->coupon;

                    if ($coupon->percent_off) {
                        $amount = (int) ($amount * (100 - $coupon->percent_off) / 100);
                    } elseif ($coupon->amount_off) {
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
            ]);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'krw',
                'customer' => $customer->id,
                'payment_method_types' => ['card', 'kakao_pay', 'naver_pay'],
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
            ]);

            // 動態建立 Price
            $price = Price::create([
                'unit_amount' => $tier->installment_price,
                'currency' => 'krw',
                'recurring' => ['interval' => 'month'],
                'product_data' => [
                    'name' => $tier->name . ' - Monthly',
                ],
            ]);

            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [
                    ['price' => $price->id],
                ],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription',
                    'payment_method_types' => ['card', 'kakao_pay', 'naver_pay'],
                ],
                'expand' => ['latest_invoice.payment_intent'],
                'cancel_at' => strtotime('+12 months'),
                'metadata' => [
                    'payment_type' => 'installment',
                    'email' => $request->email,
                    'total_installments' => 12,
                    'tier_id' => $tier->id,
                    'tier_name' => $tier->name,
                ],
            ]);

            return response()->json([
                'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret,
                'subscriptionId' => $subscription->id,
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
            return response()->json(['error' => __('payment.message.coupon_error')], 500);
        }
    }

    public function success(Request $request)
    {
        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        return view('success', [
            'paymentType' => $request->query('type', 'one_time'),
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

                // Send Facebook CAPI Purchase event for one-time payment
                if ($email && $paymentType === 'one_time') {
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
}
