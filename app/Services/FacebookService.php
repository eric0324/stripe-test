<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookService
{
    protected string $pixelId;
    protected string $accessToken;
    protected string $apiVersion;
    protected ?string $testEventCode;

    public function __construct()
    {
        $this->pixelId = config('services.facebook.pixel_id');
        $this->accessToken = config('services.facebook.access_token');
        $this->apiVersion = config('services.facebook.api_version');
        $this->testEventCode = config('services.facebook.test_event_code') ?: null;
    }

    /**
     * Send a Purchase event to Facebook Conversions API
     */
    public function sendPurchaseEvent(array $data): bool
    {
        return $this->sendEvent('Purchase', $data);
    }

    /**
     * Send an InitiateCheckout event
     */
    public function sendInitiateCheckoutEvent(array $data): bool
    {
        return $this->sendEvent('InitiateCheckout', $data);
    }

    /**
     * Send a Lead event
     */
    public function sendLeadEvent(array $data): bool
    {
        return $this->sendEvent('Lead', $data);
    }

    /**
     * Send event to Facebook Conversions API
     */
    public function sendEvent(string $eventName, array $data): bool
    {
        try {
            $eventData = [
                'event_name' => $eventName,
                'event_time' => $data['event_time'] ?? time(),
                'event_id' => $data['event_id'] ?? $this->generateEventId(),
                'action_source' => $data['action_source'] ?? 'website',
                'user_data' => $this->buildUserData($data),
            ];

            // Add event_source_url for website events
            if (($eventData['action_source'] === 'website') && isset($data['event_source_url'])) {
                $eventData['event_source_url'] = $data['event_source_url'];
            }

            // Add custom_data for Purchase events
            if (isset($data['value']) || isset($data['currency'])) {
                $eventData['custom_data'] = $this->buildCustomData($data);
            }

            $payload = [
                'data' => [json_encode($eventData)],
            ];

            // Add test event code if set (for testing)
            if ($this->testEventCode) {
                $payload['test_event_code'] = $this->testEventCode;
            }

            $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->pixelId}/events";

            $response = Http::asForm()->post($url, [
                'access_token' => $this->accessToken,
                'data' => json_encode([$eventData]),
                'test_event_code' => $this->testEventCode,
            ]);

            if ($response->successful()) {
                Log::info('Facebook CAPI event sent', [
                    'event_name' => $eventName,
                    'event_id' => $eventData['event_id'],
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::error('Facebook CAPI event failed', [
                'event_name' => $eventName,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Facebook CAPI exception', [
                'event_name' => $eventName,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Build user_data object with hashed PII
     */
    protected function buildUserData(array $data): array
    {
        $userData = [];

        // Email (hashed)
        if (isset($data['email'])) {
            $userData['em'] = [hash('sha256', strtolower(trim($data['email'])))];
        }

        // Phone (hashed)
        if (isset($data['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $data['phone']);
            $userData['ph'] = [hash('sha256', $phone)];
        }

        // First name (hashed)
        if (isset($data['first_name'])) {
            $userData['fn'] = [hash('sha256', strtolower(trim($data['first_name'])))];
        }

        // Last name (hashed)
        if (isset($data['last_name'])) {
            $userData['ln'] = [hash('sha256', strtolower(trim($data['last_name'])))];
        }

        // Client IP address
        if (isset($data['client_ip_address'])) {
            $userData['client_ip_address'] = $data['client_ip_address'];
        }

        // Client user agent
        if (isset($data['client_user_agent'])) {
            $userData['client_user_agent'] = $data['client_user_agent'];
        }

        // Facebook click ID (fbc)
        if (isset($data['fbc'])) {
            $userData['fbc'] = $data['fbc'];
        }

        // Facebook browser ID (fbp)
        if (isset($data['fbp'])) {
            $userData['fbp'] = $data['fbp'];
        }

        // External ID (hashed)
        if (isset($data['external_id'])) {
            $userData['external_id'] = [hash('sha256', $data['external_id'])];
        }

        return $userData;
    }

    /**
     * Build custom_data object for Purchase events
     */
    protected function buildCustomData(array $data): array
    {
        $customData = [];

        if (isset($data['value'])) {
            $customData['value'] = $data['value'];
        }

        if (isset($data['currency'])) {
            $customData['currency'] = $data['currency'];
        }

        if (isset($data['content_name'])) {
            $customData['content_name'] = $data['content_name'];
        }

        if (isset($data['content_type'])) {
            $customData['content_type'] = $data['content_type'];
        }

        if (isset($data['content_ids'])) {
            $customData['content_ids'] = $data['content_ids'];
        }

        if (isset($data['order_id'])) {
            $customData['order_id'] = $data['order_id'];
        }

        return $customData;
    }

    /**
     * Generate unique event ID for deduplication
     */
    public function generateEventId(): string
    {
        return uniqid('evt_', true);
    }
}
