# Stripe Payment Implementation for Appointments

## Overview
This document provides complete information about the Stripe payment integration for appointment bookings. The implementation allows clients to pay for paid consultation services (service_id 2 and 3) directly through the mobile app.

---

## Implementation Summary

### Files Created/Modified

#### 1. **Database Migration**
- `database/migrations/2026_01_28_100000_create_appointment_payments_table.php`
- Creates a new `appointment_payments` table to store payment transaction records

#### 2. **Model**
- `app/Models/AppointmentPayment.php`
- Eloquent model for managing payment records

#### 3. **Service Class**
- `app/Services/Payment/StripePaymentService.php`
- Handles all Stripe payment processing logic

#### 4. **Controller Methods**
- `app/Http/Controllers/API/ClientPortalAppointmentController.php`
  - `processAppointmentPayment()` - Process Stripe payment
  - `getPaymentHistory()` - Get payment history for an appointment

#### 5. **Routes**
- `routes/api.php`
  - `POST /api/appointments/process-payment` - Process payment
  - `GET /api/appointments/{id}/payment-history` - Get payment history

#### 6. **Configuration**
- `config/services.php` - Stripe configuration added
- `.env` - Test keys configured (active for development)

---

## Database Schema

### `appointment_payments` Table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| appointment_id | bigint | FK to booking_appointments.id |
| payment_gateway | enum | Payment method (stripe, paypal, manual) |
| transaction_id | string | Stripe PaymentIntent ID (pi_xxx) |
| charge_id | string | Stripe Charge ID (ch_xxx) |
| customer_id | string | Stripe Customer ID (cus_xxx) |
| payment_method_id | string | Stripe Payment Method ID (pm_xxx) |
| amount | decimal(10,2) | Payment amount |
| currency | string(3) | Currency code (AUD) |
| status | enum | pending, processing, succeeded, failed, refunded |
| error_message | text | Error message if payment failed |
| transaction_data | json | Full Stripe response |
| receipt_url | string | Stripe receipt URL |
| refund_amount | decimal(10,2) | Total refunded amount |
| refunded_at | datetime | Refund timestamp |
| client_ip | string(45) | Client IP address |
| user_agent | text | Client user agent |
| processed_at | datetime | When payment was processed |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

---

## API Documentation

### 1. Process Payment

**Endpoint:** `POST /api/appointments/process-payment`

**Authentication:** Required (Bearer Token)

**Request Headers:**
```
Authorization: Bearer {access_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "appointment_id": 123,
  "payment_method_id": "pm_1234567890abcdef"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| appointment_id | integer | Yes | ID of the appointment to pay for |
| payment_method_id | string | Yes | Stripe payment method ID from mobile app (created using Stripe SDK) |

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Payment processed successfully",
  "data": {
    "appointment_id": 123,
    "payment_id": 1,
    "transaction_id": "pi_1234567890abcdef",
    "charge_id": "ch_1234567890abcdef",
    "amount": "150.00",
    "currency": "AUD",
    "status": "paid",
    "receipt_url": "https://pay.stripe.com/receipts/...",
    "paid_at": "2026-01-28T10:30:00Z",
    "appointment": {
      "id": 123,
      "full_name": "John Doe",
      "status": "paid",
      "is_paid": true,
      "payment_status": "completed",
      ...
    }
  },
  "bansal_synced": true
}
```

**Error Responses:**

**401 Unauthorized:**
```json
{
  "success": false,
  "message": "Unauthenticated",
  "data": []
}
```

**404 Not Found:**
```json
{
  "success": false,
  "message": "Appointment not found or does not belong to you",
  "data": []
}
```

**422 Validation Error:**
```json
{
  "success": false,
  "message": "This appointment does not require payment",
  "data": []
}
```

**422 Already Paid:**
```json
{
  "success": false,
  "message": "This appointment has already been paid",
  "data": []
}
```

**422 Card Declined:**
```json
{
  "success": false,
  "message": "Your card was declined. Please try a different payment method.",
  "data": {
    "payment_id": 1
  }
}
```

**500 Server Error:**
```json
{
  "success": false,
  "message": "An error occurred while processing payment: {error details}",
  "data": []
}
```

### 2. Get Payment History

**Endpoint:** `GET /api/appointments/{id}/payment-history`

**Authentication:** Required (Bearer Token)

**URL Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| id | integer | Appointment ID |

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Payment history retrieved successfully",
  "data": {
    "appointment_id": 123,
    "payments": [
      {
        "id": 1,
        "transaction_id": "pi_1234567890abcdef",
        "charge_id": "ch_1234567890abcdef",
        "amount": "150.00",
        "currency": "AUD",
        "status": "succeeded",
        "payment_gateway": "stripe",
        "receipt_url": "https://pay.stripe.com/receipts/...",
        "error_message": null,
        "processed_at": "2026-01-28T10:30:00Z",
        "created_at": "2026-01-28T10:29:55Z"
      },
      {
        "id": 2,
        "transaction_id": "pi_0987654321fedcba",
        "charge_id": null,
        "amount": "150.00",
        "currency": "AUD",
        "status": "failed",
        "payment_gateway": "stripe",
        "receipt_url": null,
        "error_message": "Your card was declined.",
        "processed_at": "2026-01-28T10:25:00Z",
        "created_at": "2026-01-28T10:24:55Z"
      }
    ],
    "total_attempts": 2
  }
}
```

---

## Mobile App Integration

### 1. Install Stripe SDK

**For React Native:**
```bash
npm install @stripe/stripe-react-native
```

**For Flutter:**
```yaml
dependencies:
  flutter_stripe: ^10.0.0
```

### 2. Initialize Stripe

**React Native:**
```javascript
import { StripeProvider } from '@stripe/stripe-react-native';

const STRIPE_PUBLISHABLE_KEY = 'pk_test_51HAz4JFeMJ48bwS4Www5LApVIBY6KqnGtsdKjpQleJDJIXAS0V8qrKecEO0MEoBnzcqmIo5GFBnXCtJEsj7H6FIH00kSSk38hr';

function App() {
  return (
    <StripeProvider publishableKey={STRIPE_PUBLISHABLE_KEY}>
      {/* Your app components */}
    </StripeProvider>
  );
}
```

### 3. Create Payment Method

**React Native Example:**
```javascript
import { useStripe } from '@stripe/stripe-react-native';

function PaymentScreen() {
  const { createPaymentMethod } = useStripe();

  const handlePayment = async (appointmentId) => {
    try {
      // 1. Create payment method from card details
      const { paymentMethod, error } = await createPaymentMethod({
        paymentMethodType: 'Card',
        paymentMethodData: {
          billingDetails: {
            email: 'user@example.com',
            name: 'John Doe',
          },
        },
      });

      if (error) {
        console.error('Payment method creation failed:', error);
        return;
      }

      // 2. Send payment method to your API
      const response = await fetch('https://your-api.com/api/appointments/process-payment', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${accessToken}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          appointment_id: appointmentId,
          payment_method_id: paymentMethod.id,
        }),
      });

      const result = await response.json();

      if (result.success) {
        // Payment successful
        console.log('Payment successful:', result.data);
        // Navigate to success screen or show confirmation
      } else {
        // Payment failed
        console.error('Payment failed:', result.message);
        // Show error message to user
      }
    } catch (error) {
      console.error('Payment error:', error);
    }
  };

  return (
    // Your payment form UI
  );
}
```

### 4. Handle 3D Secure Authentication

Some cards require 3D Secure authentication. The API will return `requires_action: true` in such cases:

```javascript
const response = await fetch('https://your-api.com/api/appointments/process-payment', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${accessToken}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    appointment_id: appointmentId,
    payment_method_id: paymentMethod.id,
  }),
});

const result = await response.json();

if (result.data && result.data.requires_action) {
  // Handle 3D Secure authentication
  const { error: confirmError } = await stripe.confirmPayment(
    result.data.client_secret,
    {
      paymentMethodType: 'Card',
    }
  );

  if (confirmError) {
    console.error('3D Secure authentication failed:', confirmError);
  } else {
    console.log('Payment authenticated and completed');
  }
}
```

---

## Testing

### Test Card Numbers

Use these test card numbers in development:

| Card Number | Description | Expected Result |
|-------------|-------------|-----------------|
| 4242 4242 4242 4242 | Standard success | Payment succeeds |
| 4000 0027 6000 3184 | 3D Secure required | Requires authentication |
| 4000 0000 0000 0002 | Generic decline | Card declined |
| 4000 0000 0000 9995 | Insufficient funds | Insufficient funds error |
| 4000 0000 0000 9987 | Lost card | Card reported as lost |
| 4000 0000 0000 9979 | Stolen card | Card reported as stolen |

**Additional Details for All Test Cards:**
- Any future expiry date (e.g., 12/34)
- Any 3-digit CVC (e.g., 123)
- Any postal code

### Testing Workflow

1. **Create an appointment** for service_id 2 or 3 (paid services)
2. **Use test card** 4242 4242 4242 4242
3. **Call payment API** with appointment_id and payment_method_id
4. **Verify response:**
   - Payment record created in `appointment_payments` table
   - Appointment status updated to 'paid'
   - `is_paid` set to true
   - `payment_status` set to 'completed'
   - `paid_at` timestamp recorded

### Postman Testing

**Create Payment Request:**
```
POST {{base_url}}/api/appointments/process-payment
Authorization: Bearer {{access_token}}
Content-Type: application/json

{
  "appointment_id": 1,
  "payment_method_id": "pm_card_visa"
}
```

**Note:** For Postman testing, you'll need to create a payment method first using Stripe API:

```bash
curl https://api.stripe.com/v1/payment_methods \
  -u YOUR_STRIPE_SECRET_KEY: \
  -d type=card \
  -d "card[number]=4242424242424242" \
  -d "card[exp_month]=12" \
  -d "card[exp_year]=2034" \
  -d "card[cvc]=123"
```

---

## Configuration

### Environment Variables

**Test Mode (Current):**
```env
STRIPE_KEY=pk_test_YOUR_STRIPE_PUBLISHABLE_KEY
STRIPE_SECRET=sk_test_YOUR_STRIPE_SECRET_KEY
```

**Production Mode (When going live):**
```env
STRIPE_KEY=pk_live_YOUR_STRIPE_LIVE_PUBLISHABLE_KEY
STRIPE_SECRET=sk_live_YOUR_STRIPE_LIVE_SECRET_KEY
```

### Switching to Production

1. **Update `.env` file** - Comment out test keys, uncomment live keys
2. **Update mobile app** - Change publishable key to live key
3. **Test with real cards** - Use small amounts first
4. **Enable webhooks** (optional but recommended)
5. **Monitor Stripe dashboard** for real transactions

---

## Security Considerations

### ✅ Implemented Security Features

1. **Authentication Required** - All endpoints require valid Bearer token
2. **Ownership Verification** - Users can only pay for their own appointments
3. **Idempotency** - Duplicate payment prevention (checks if already paid)
4. **Amount Verification** - Server-side validation of payment amount
5. **No Raw Card Data** - Card data never touches your server (handled by Stripe SDK)
6. **PCI Compliance** - Using Stripe.js/SDK ensures PCI compliance
7. **HTTPS Only** - All API calls should use HTTPS in production

### 🔒 Additional Security Recommendations

1. **Enable Stripe Radar** - Automatic fraud detection (included in Stripe)
2. **IP Logging** - Already implemented (client_ip stored in payments table)
3. **Rate Limiting** - Add rate limiting for payment endpoints
4. **Webhook Validation** - Implement webhooks for additional security
5. **Email Verification** - Verify email before allowing payment

---

## Troubleshooting

### Common Issues

#### 1. "Appointment not found or does not belong to you"
**Cause:** User trying to pay for another user's appointment
**Solution:** Verify the appointment_id belongs to the authenticated user

#### 2. "This appointment does not require payment"
**Cause:** Trying to pay for free consultation (service_id = 2)
**Solution:** Only service_id 1 and 3 require payment

#### 3. "This appointment has already been paid"
**Cause:** Attempting duplicate payment
**Solution:** Check appointment payment status before showing payment form

#### 4. "Your card was declined"
**Cause:** Card issuer declined the transaction
**Solution:** Ask user to:
- Check card details are correct
- Contact their bank
- Try a different card

#### 5. "Payment requires additional authentication"
**Cause:** 3D Secure authentication needed
**Solution:** Implement 3D Secure handling in mobile app (see integration guide)

### Logs

Payment processing logs are stored in:
- Laravel Log: `storage/logs/laravel.log`
- Search for: `'Stripe payment'`, `'Process Payment API'`

---

## Future Enhancements

### Not Implemented (Can be added later)

1. **Webhook Support** - For additional payment confirmation
2. **Refund Functionality** - Allow admins to refund payments
3. **Retry Failed Payments** - Allow users to retry failed payments
4. **Multiple Payment Methods** - Support PayPal, Apple Pay, Google Pay
5. **Partial Payments** - Split payment across multiple cards
6. **Promo Codes** - Apply discounts before payment
7. **Receipt Generation** - PDF receipt for completed payments
8. **Payment Plans** - Installment payment options
9. **Currency Conversion** - Support multiple currencies
10. **Payment Analytics** - Dashboard for payment metrics

---

## Support & Resources

### Stripe Resources
- **Dashboard:** https://dashboard.stripe.com/
- **API Documentation:** https://stripe.com/docs/api
- **Testing Guide:** https://stripe.com/docs/testing
- **Mobile SDKs:** https://stripe.com/docs/mobile

### Contact
For implementation questions or issues, contact the development team.

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-01-28 | Initial implementation |

---

**Last Updated:** January 28, 2026
