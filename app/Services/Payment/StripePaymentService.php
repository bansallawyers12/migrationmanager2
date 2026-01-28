<?php

namespace App\Services\Payment;

use App\Models\BookingAppointment;
use App\Models\AppointmentPayment;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Exception\CardException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class StripePaymentService
{
    /**
     * Initialize Stripe with API key
     */
    public function __construct()
    {
        // Set Stripe API key from config
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Process payment for an appointment
     * 
     * @param BookingAppointment $appointment
     * @param string $paymentMethodId Payment method ID from Stripe.js
     * @param array $metadata Additional metadata (IP, user agent, etc.)
     * @return array ['success' => bool, 'data' => array, 'message' => string]
     */
    public function processPayment(BookingAppointment $appointment, string $paymentMethodId, array $metadata = []): array
    {
        DB::beginTransaction();
        
        try {
            // Create payment record with pending status
            $payment = AppointmentPayment::create([
                'appointment_id' => $appointment->id,
                'payment_gateway' => 'stripe',
                'payment_method_id' => $paymentMethodId,
                'amount' => $appointment->final_amount ?? $appointment->amount,
                'currency' => 'AUD',
                'status' => 'pending',
                'client_ip' => $metadata['ip'] ?? null,
                'user_agent' => $metadata['user_agent'] ?? null,
            ]);

            // Get or create Stripe customer
            $customer = $this->getOrCreateCustomer($appointment);
            
            // Update payment with customer ID
            $payment->update(['customer_id' => $customer->id]);

            // Create PaymentIntent
            $paymentIntent = $this->createPaymentIntent(
                $appointment,
                $customer->id,
                $paymentMethodId,
                $payment->id
            );

            // Update payment record with Stripe data
            $payment->update([
                'transaction_id' => $paymentIntent->id,
                'charge_id' => $paymentIntent->latest_charge ?? null,
                'status' => $this->mapStripeStatus($paymentIntent->status),
                'transaction_data' => $paymentIntent->toArray(),
                'receipt_url' => $paymentIntent->charges->data[0]->receipt_url ?? null,
                'processed_at' => now(),
            ]);

            // If payment succeeded, update appointment
            if ($paymentIntent->status === 'succeeded') {
                $this->updateAppointmentAfterPayment($appointment, $payment);
                
                DB::commit();
                
                Log::info('Stripe payment succeeded', [
                    'appointment_id' => $appointment->id,
                    'payment_id' => $payment->id,
                    'payment_intent_id' => $paymentIntent->id,
                    'amount' => $payment->amount,
                ]);

                return [
                    'success' => true,
                    'data' => [
                        'payment_id' => $payment->id,
                        'appointment_id' => $appointment->id,
                        'payment_intent_id' => $paymentIntent->id,
                        'charge_id' => $paymentIntent->latest_charge,
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'status' => 'succeeded',
                        'receipt_url' => $payment->receipt_url,
                        'paid_at' => $appointment->paid_at->toIso8601String(),
                    ],
                    'message' => 'Payment processed successfully',
                ];
            }

            // Payment requires additional action (e.g., 3D Secure)
            if ($paymentIntent->status === 'requires_action') {
                DB::commit();
                
                return [
                    'success' => false,
                    'data' => [
                        'payment_id' => $payment->id,
                        'payment_intent_id' => $paymentIntent->id,
                        'requires_action' => true,
                        'client_secret' => $paymentIntent->client_secret,
                        'next_action' => $paymentIntent->next_action,
                    ],
                    'message' => 'Payment requires additional authentication',
                ];
            }

            // Payment failed or in other status
            DB::commit();
            
            return [
                'success' => false,
                'data' => [
                    'payment_id' => $payment->id,
                    'status' => $paymentIntent->status,
                ],
                'message' => 'Payment could not be completed. Status: ' . $paymentIntent->status,
            ];

        } catch (CardException $e) {
            DB::rollBack();
            
            // Card was declined
            $error = $e->getError();
            $errorMessage = $error->message ?? 'Card was declined';
            
            if (isset($payment)) {
                $payment->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                    'processed_at' => now(),
                ]);
            }
            
            Log::warning('Stripe card declined', [
                'appointment_id' => $appointment->id,
                'error' => $errorMessage,
                'code' => $error->code ?? null,
            ]);

            return [
                'success' => false,
                'data' => ['payment_id' => $payment->id ?? null],
                'message' => $errorMessage,
            ];

        } catch (RateLimitException $e) {
            DB::rollBack();
            
            Log::error('Stripe rate limit exceeded', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => [],
                'message' => 'Too many payment requests. Please try again later.',
            ];

        } catch (InvalidRequestException $e) {
            DB::rollBack();
            
            $errorMessage = $e->getMessage();
            
            if (isset($payment)) {
                $payment->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                    'processed_at' => now(),
                ]);
            }
            
            Log::error('Stripe invalid request', [
                'appointment_id' => $appointment->id,
                'error' => $errorMessage,
            ]);

            return [
                'success' => false,
                'data' => ['payment_id' => $payment->id ?? null],
                'message' => 'Invalid payment request. Please check your payment details.',
            ];

        } catch (AuthenticationException $e) {
            DB::rollBack();
            
            Log::error('Stripe authentication failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => [],
                'message' => 'Payment system authentication error. Please contact support.',
            ];

        } catch (ApiConnectionException $e) {
            DB::rollBack();
            
            Log::error('Stripe API connection failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => [],
                'message' => 'Unable to connect to payment system. Please try again.',
            ];

        } catch (ApiErrorException $e) {
            DB::rollBack();
            
            $errorMessage = $e->getMessage();
            
            if (isset($payment)) {
                $payment->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                    'processed_at' => now(),
                ]);
            }
            
            Log::error('Stripe API error', [
                'appointment_id' => $appointment->id,
                'error' => $errorMessage,
            ]);

            return [
                'success' => false,
                'data' => ['payment_id' => $payment->id ?? null],
                'message' => 'Payment processing error. Please try again.',
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            if (isset($payment)) {
                $payment->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'processed_at' => now(),
                ]);
            }
            
            Log::error('Unexpected payment error', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'data' => ['payment_id' => $payment->id ?? null],
                'message' => 'An unexpected error occurred. Please try again.',
            ];
        }
    }

    /**
     * Get or create Stripe customer
     * 
     * @param BookingAppointment $appointment
     * @return Customer
     */
    private function getOrCreateCustomer(BookingAppointment $appointment): Customer
    {
        // Check if customer already exists by email
        $existingPayment = AppointmentPayment::where('appointment_id', $appointment->id)
            ->whereNotNull('customer_id')
            ->first();

        if ($existingPayment && $existingPayment->customer_id) {
            try {
                return Customer::retrieve($existingPayment->customer_id);
            } catch (Exception $e) {
                // Customer not found, create new one
                Log::warning('Stripe customer not found, creating new', [
                    'customer_id' => $existingPayment->customer_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Create new customer
        return Customer::create([
            'email' => $appointment->client_email,
            'name' => $appointment->client_name,
            'phone' => $appointment->client_phone,
            'metadata' => [
                'appointment_id' => $appointment->id,
                'client_id' => $appointment->client_id,
            ],
        ]);
    }

    /**
     * Create PaymentIntent
     * 
     * @param BookingAppointment $appointment
     * @param string $customerId
     * @param string $paymentMethodId
     * @param int $paymentRecordId
     * @return PaymentIntent
     */
    private function createPaymentIntent(
        BookingAppointment $appointment,
        string $customerId,
        string $paymentMethodId,
        int $paymentRecordId
    ): PaymentIntent {
        $amount = $appointment->final_amount ?? $appointment->amount;
        
        // Convert amount to cents (Stripe requires amount in smallest currency unit)
        $amountInCents = (int) ($amount * 100);

        return PaymentIntent::create([
            'amount' => $amountInCents,
            'currency' => 'aud',
            'customer' => $customerId,
            'payment_method' => $paymentMethodId,
            'confirm' => true, // Automatically confirm the payment
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never', // Disable redirects for API payments
            ],
            'description' => "Payment for appointment #{$appointment->id} - {$appointment->service_type}",
            'metadata' => [
                'appointment_id' => $appointment->id,
                'payment_record_id' => $paymentRecordId,
                'client_id' => $appointment->client_id,
                'client_email' => $appointment->client_email,
                'service_type' => $appointment->service_type ?? 'consultation',
            ],
            'receipt_email' => $appointment->client_email,
        ]);
    }

    /**
     * Update appointment after successful payment
     * 
     * @param BookingAppointment $appointment
     * @param AppointmentPayment $payment
     * @return void
     */
    private function updateAppointmentAfterPayment(BookingAppointment $appointment, AppointmentPayment $payment): void
    {
        $appointment->update([
            'status' => 'paid',
            'is_paid' => true,
            'payment_status' => 'completed',
            'payment_method' => 'stripe',
            'paid_at' => now(),
        ]);

        Log::info('Appointment updated after payment', [
            'appointment_id' => $appointment->id,
            'status' => 'paid',
            'payment_id' => $payment->id,
        ]);
    }

    /**
     * Map Stripe payment status to our internal status
     * 
     * @param string $stripeStatus
     * @return string
     */
    private function mapStripeStatus(string $stripeStatus): string
    {
        return match($stripeStatus) {
            'succeeded' => 'succeeded',
            'processing' => 'processing',
            'requires_payment_method', 'requires_confirmation', 'requires_action' => 'pending',
            'canceled', 'failed' => 'failed',
            default => 'pending',
        };
    }

    /**
     * Get payment history for an appointment
     * 
     * @param int $appointmentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPaymentHistory(int $appointmentId)
    {
        return AppointmentPayment::where('appointment_id', $appointmentId)
            ->orderByDesc('created_at')
            ->get();
    }
}
