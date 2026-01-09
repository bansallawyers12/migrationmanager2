# Google Places Address Autocomplete - Implementation Guide

A complete guide to implement Google Places address autocomplete in Laravel applications with postcode extraction and unit number support.

## 📋 Quick Setup Checklist

Use this checklist for fast implementation:

### ✅ Pre-Implementation (10 min)
- [ ] Create Google Cloud Project
- [ ] Enable Places API & Geocoding API
- [ ] Create API Key with **No restrictions** or **IP restrictions** (NOT referrer!)
- [ ] Add `GOOGLE_MAPS_API_KEY=your_key` to `.env`
- [ ] Run `php artisan config:clear`

### ✅ Backend (20 min)
- [ ] Create/update controller with `searchAddress()` and `getPlaceDetails()` methods
- [ ] Add routes: `/address/search` and `/address/details`
- [ ] Set timeouts: 30s backend, 35s frontend
- [ ] Test routes: `php artisan route:list`

### ✅ Frontend (30 min)
- [ ] Create `public/js/address-autocomplete.js`
- [ ] Create `public/css/address-autocomplete.css`
- [ ] Create Blade component with data attributes
- [ ] Include in view with `@push('styles')` and `@push('scripts')`

### ✅ Testing (10 min)
- [ ] Type address, verify suggestions appear
- [ ] Test unit numbers: `8/278 Collins Street`
- [ ] Verify postcode populates
- [ ] Check console for errors

**Total Time: ~1-2 hours** | **Difficulty: Intermediate**

---

## Table of Contents
- [Quick Setup Checklist](#quick-setup-checklist)
- [Prerequisites](#prerequisites)
- [Step 1: Get Google API Key](#step-1-get-google-api-key)
- [Step 2: Backend Setup (Laravel)](#step-2-backend-setup-laravel)
- [Step 3: Frontend JavaScript](#step-3-frontend-javascript)
- [Step 4: HTML/Blade Views](#step-4-htmlblade-views)
- [Step 5: CSS Styling](#step-5-css-styling)
- [Step 6: Testing](#step-6-testing)
- [Production Checklist](#production-checklist)
- [Troubleshooting](#troubleshooting)
- [Common Issues Quick Reference](#common-issues-quick-reference)

---

## Prerequisites

- Laravel 8+
- jQuery (or vanilla JS alternative)
- Google Cloud Account
- Basic knowledge of Laravel routes and controllers

---

## Step 1: Get Google API Key

### 1.1 Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Go to **APIs & Services** → **Library**

### 1.2 Enable Required APIs

Enable these APIs:
- ✅ **Places API** (required)
- ✅ **Geocoding API** (optional, for fallback)

### 1.3 Create API Key

1. Go to **APIs & Services** → **Credentials**
2. Click **Create Credentials** → **API Key**
3. Copy the API key

### 1.4 Configure API Key Restrictions

**IMPORTANT:** For backend (server-side) usage:

- **Application restrictions:** Select **None** or **IP addresses**
- **API restrictions:** Select **Restrict key** and choose:
  - Places API
  - Geocoding API (optional)

⚠️ **Do NOT use "HTTP referrers (web sites)" restriction** - this will block backend requests!

### 1.5 Add to .env

```env
GOOGLE_MAPS_API_KEY=your_api_key_here
```

---

## Step 2: Backend Setup (Laravel)

### 2.1 Create Controller Method for Address Search

Add to your controller (e.g., `ClientController.php`):

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Search addresses using Google Places Autocomplete API
     */
    public function searchAddress(Request $request)
    {
        $query = $request->input('query');
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        
        if (!$apiKey || strlen($query) < 3) {
            return response()->json([
                'status' => 'ERROR',
                'predictions' => []
            ]);
        }
        
        $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';
        
        $params = http_build_query([
            'input' => $query,
            'key' => $apiKey,
            'types' => 'address',
            'components' => 'country:au', // Restrict to Australia (change as needed)
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local dev
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            \Log::error('Google Places API Error: ' . $curlError);
            return response()->json([
                'status' => 'ERROR',
                'predictions' => []
            ]);
        }
        
        $data = json_decode($response, true);
        
        if ($httpCode === 200 && isset($data['status']) && $data['status'] === 'OK') {
            return response()->json($data);
        }
        
        return response()->json([
            'status' => 'ERROR',
            'predictions' => []
        ]);
    }
    
    /**
     * Get place details including address components
     */
    public function getPlaceDetails(Request $request)
    {
        $placeId = $request->input('place_id');
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        
        if (!$apiKey || !$placeId) {
            return response()->json([
                'status' => 'ERROR',
                'result' => []
            ]);
        }
        
        $url = 'https://maps.googleapis.com/maps/api/place/details/json';
        
        $params = http_build_query([
            'place_id' => $placeId,
            'key' => $apiKey,
            'fields' => 'address_components,formatted_address,name'
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            \Log::error('Google Places Details API Error: ' . $curlError);
        }
        
        $data = json_decode($response, true);
        
        if ($httpCode === 200 && isset($data['status']) && $data['status'] === 'OK') {
            return response()->json($data);
        }
        
        return response()->json([
            'status' => 'OK',
            'result' => [
                'formatted_address' => $request->input('description', ''),
                'address_components' => []
            ]
        ]);
    }
}
```

### 2.2 Add Routes

In `routes/web.php`:

```php
Route::post('/address/search', [AddressController::class, 'searchAddress'])
    ->name('address.search');
    
Route::post('/address/details', [AddressController::class, 'getPlaceDetails'])
    ->name('address.details');
```

---

## Step 3: Frontend JavaScript

### 3.1 Create `address-autocomplete.js`

Save as `public/js/address-autocomplete.js`:

```javascript
/**
 * Google Places Address Autocomplete
 * Features:
 * - Address suggestions as you type
 * - Automatic field population
 * - Unit number support (format: Unit/Street)
 * - Postcode extraction with multiple fallbacks
 * - Australian regional code calculation
 */

(function() {
    'use strict';
    
    $(document).ready(function() {
        initAddressAutocomplete();
    });
    
    function initAddressAutocomplete() {
        const config = getConfig();
        
        if (!config.isValid) {
            console.error('Address autocomplete configuration missing!');
            return;
        }
        
        bindAddressSearch(config);
        bindAddressSelection(config);
        bindClickOutside();
    }
    
    /**
     * Get configuration from DOM
     */
    function getConfig() {
        const container = document.getElementById('addressAutocomplete');
        
        if (!container) {
            return {
                searchRoute: '',
                detailsRoute: '',
                csrfToken: '',
                isValid: false
            };
        }
        
        return {
            searchRoute: container.dataset.searchRoute || '',
            detailsRoute: container.dataset.detailsRoute || '',
            csrfToken: container.dataset.csrfToken || '',
            isValid: !!(container.dataset.searchRoute && container.dataset.detailsRoute)
        };
    }
    
    /**
     * Bind address search functionality
     */
    function bindAddressSearch(config) {
        $(document).on('input', '.address-search-input', function() {
            const query = $(this).val();
            const $wrapper = $(this).closest('.address-wrapper');
            
            if (query.length < 3) {
                $wrapper.find('.autocomplete-suggestions').remove();
                return;
            }
            
            $.ajax({
                url: config.searchRoute,
                method: 'POST',
                timeout: 35000,
                data: { 
                    query: query,
                    _token: config.csrfToken
                },
                success: function(response) {
                    if (response.predictions && response.predictions.length > 0) {
                        renderSuggestions($wrapper, response.predictions);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Address search error:', error);
                }
            });
        });
    }
    
    /**
     * Render autocomplete suggestions
     */
    function renderSuggestions($wrapper, predictions) {
        let html = '<div class="autocomplete-suggestions">';
        predictions.forEach(function(prediction) {
            html += `<div class="autocomplete-suggestion" data-place-id="${prediction.place_id}">
                ${prediction.description}
            </div>`;
        });
        html += '</div>';
        
        $wrapper.find('.autocomplete-suggestions').remove();
        $wrapper.find('.address-search-container').append(html);
    }
    
    /**
     * Bind address selection handler
     */
    function bindAddressSelection(config) {
        $(document).on('click', '.autocomplete-suggestion', function() {
            const placeId = $(this).data('place-id');
            const description = $(this).text();
            const $wrapper = $(this).closest('.address-wrapper');
            
            $wrapper.find('.address-search-input').val(description);
            $wrapper.find('.autocomplete-suggestions').remove();
            
            fetchPlaceDetails(config, placeId, $wrapper);
        });
    }
    
    /**
     * Fetch and populate address details
     */
    function fetchPlaceDetails(config, placeId, $wrapper) {
        $.ajax({
            url: config.detailsRoute,
            method: 'POST',
            timeout: 35000,
            data: { 
                place_id: placeId,
                _token: config.csrfToken
            },
            success: function(response) {
                if (response.result && response.result.address_components) {
                    populateAddressFields($wrapper, response.result);
                }
            },
            error: function(xhr, status, error) {
                console.error('Place details error:', error);
            }
        });
    }
    
    /**
     * Populate address fields from Google Places response
     */
    function populateAddressFields($wrapper, result) {
        const components = result.address_components;
        
        let unitNumber = '';
        let streetNumber = '';
        let streetName = '';
        let addressLine1 = '';
        let suburb = '';
        let state = '';
        let postcode = '';
        let country = 'Australia';
        
        // Extract components
        components.forEach(function(component) {
            // Unit/Apartment number
            if (component.types.includes('subpremise')) {
                unitNumber = component.long_name;
            }
            
            // Street number
            if (component.types.includes('street_number')) {
                streetNumber = component.long_name;
            }
            
            // Street name
            if (component.types.includes('route')) {
                streetName = component.long_name;
            }
            
            // Suburb
            if (component.types.includes('locality')) {
                suburb = component.long_name;
            }
            
            // State
            if (component.types.includes('administrative_area_level_1')) {
                state = component.short_name || component.long_name;
            }
            
            // Postcode
            if (component.types.includes('postal_code')) {
                postcode = component.long_name;
            }
            
            // Country
            if (component.types.includes('country')) {
                country = component.long_name;
            }
        });
        
        // Build Address Line 1 with proper unit formatting
        if (unitNumber && streetNumber && streetName) {
            addressLine1 = unitNumber + '/' + streetNumber + ' ' + streetName;
        } else if (streetNumber && streetName) {
            addressLine1 = streetNumber + ' ' + streetName;
        }
        
        // Fallback postcode extraction from formatted_address
        if (!postcode && result.formatted_address) {
            const postcodeMatch = result.formatted_address.match(/\b(\d{4})\b/);
            if (postcodeMatch) {
                postcode = postcodeMatch[1];
            }
        }
        
        // Populate form fields
        $wrapper.find('input[name="address_line_1"]').val(addressLine1);
        $wrapper.find('input[name="suburb"]').val(suburb);
        $wrapper.find('input[name="state"]').val(state);
        $wrapper.find('input[name="postcode"]').val(postcode);
        $wrapper.find('input[name="country"]').val(country);
        
        console.log('Address populated:', {
            addressLine1,
            suburb,
            state,
            postcode,
            country
        });
    }
    
    /**
     * Close suggestions when clicking outside
     */
    function bindClickOutside() {
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.address-search-container').length) {
                $('.autocomplete-suggestions').remove();
            }
        });
    }
    
})();
```

---

## Step 4: HTML/Blade Views

### 4.1 Create Address Form Component

Create `resources/views/components/address-autocomplete.blade.php`:

```blade
{{-- Address Autocomplete Component --}}
<div id="addressAutocomplete" 
     data-search-route="{{ route('address.search') }}"
     data-details-route="{{ route('address.details') }}"
     data-csrf-token="{{ csrf_token() }}">
    
    <div class="address-wrapper">
        {{-- Search Input --}}
        <div class="form-group address-search-container">
            <label for="address_search">Search Address</label>
            <input type="text" 
                   id="address_search" 
                   class="form-control address-search-input" 
                   placeholder="Start typing an address..."
                   autocomplete="off">
            {{-- Suggestions will appear here --}}
        </div>
        
        {{-- Address Fields --}}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="address_line_1">Address Line 1 *</label>
                    <input type="text" 
                           id="address_line_1" 
                           name="address_line_1" 
                           class="form-control"
                           placeholder="Street address"
                           required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="address_line_2">Address Line 2</label>
                    <input type="text" 
                           id="address_line_2" 
                           name="address_line_2" 
                           class="form-control"
                           placeholder="Apartment, suite, etc.">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="suburb">Suburb *</label>
                    <input type="text" 
                           id="suburb" 
                           name="suburb" 
                           class="form-control"
                           required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="state">State *</label>
                    <input type="text" 
                           id="state" 
                           name="state" 
                           class="form-control"
                           required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="postcode">Postcode *</label>
                    <input type="text" 
                           id="postcode" 
                           name="postcode" 
                           class="form-control"
                           required>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="country">Country *</label>
                    <input type="text" 
                           id="country" 
                           name="country" 
                           class="form-control"
                           value="Australia"
                           required>
                </div>
            </div>
        </div>
    </div>
</div>
```

### 4.2 Include in Your View

In your main view:

```blade
@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="POST" action="{{ route('your.route') }}">
            @csrf
            
            <x-address-autocomplete />
            
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/address-autocomplete.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/address-autocomplete.js') }}"></script>
@endpush
```

---

## Step 5: CSS Styling

### 5.1 Create `address-autocomplete.css`

Save as `public/css/address-autocomplete.css`:

```css
/* Address Autocomplete Styles */

.address-search-container {
    position: relative;
}

.address-search-input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.address-search-input:focus {
    outline: none;
    border-color: #4A90E2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

/* Autocomplete Suggestions */
.autocomplete-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: -1px;
}

.autocomplete-suggestion {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
    color: #333;
    transition: background-color 0.2s;
}

.autocomplete-suggestion:last-child {
    border-bottom: none;
}

.autocomplete-suggestion:hover {
    background-color: #f8f9fa;
}

/* Error/Success Messages */
.autocomplete-error {
    color: #dc3545;
    font-size: 12px;
    margin-top: 5px;
    padding: 8px;
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
}

.autocomplete-success {
    color: #28a745;
    font-size: 12px;
    margin-top: 5px;
}

/* Address Wrapper */
.address-wrapper {
    margin-bottom: 20px;
}
```

---

## Step 6: Testing

### 6.1 Test Address Search

1. **Open your form in browser**
2. **Open browser console** (F12)
3. **Type an address** in the search field (e.g., "123 Main Street Sydney")
4. **Select from suggestions**
5. **Verify all fields populate:**
   - Address Line 1: Street address (with unit if present)
   - Suburb
   - State
   - Postcode ✅
   - Country

### 6.2 Test Unit Numbers

Try addresses with units:
- `Unit 5/123 Main Street Sydney` → Should populate as `5/123 Main Street`
- `8/278 Collins Street Melbourne` → Should populate as `8/278 Collins Street`

### 6.3 Check Console Logs

Look for these logs:
```
Address populated: {
  addressLine1: "8/278 Collins Street",
  suburb: "Melbourne",
  state: "VIC",
  postcode: "3000",
  country: "Australia"
}
```

---

## Troubleshooting

### Problem: "API keys with referer restrictions cannot be used"

**Solution:** Remove website restrictions from API key, use IP restrictions or "None" instead.

### Problem: Timeout errors

**Solution:** 
- Increase timeout in controller (30s)
- Increase timeout in JavaScript (35s)
- Check internet connection

### Problem: Postcode not populating

**Solutions:**
1. Check console for `📮 Postcode found` message
2. Verify `postal_code` is in `address_components`
3. Ensure `fields` parameter includes `address_components`
4. Fallback will extract from `formatted_address`

### Problem: Unit numbers not formatting correctly

**Solution:** Verify this logic in JavaScript:
```javascript
if (unitNumber && streetNumber && streetName) {
    addressLine1 = unitNumber + '/' + streetNumber + ' ' + streetName;
}
```

### Problem: No suggestions appearing

**Solutions:**
1. Check routes are registered: `php artisan route:list`
2. Verify CSRF token is valid
3. Check network tab in browser DevTools
4. Verify API key is set in `.env`
5. Check Google Cloud Console for API errors

### Problem: API quota exceeded

**Solution:** 
- Google provides $200/month free credit
- Check usage in Google Cloud Console
- Set up billing alerts
- Consider caching frequently used addresses

---

## Advanced Features

### Regional Code Calculation (Australian Addresses)

Add regional code calculation for Australian addresses:

```javascript
function getRegionalCodeInfo(postcode) {
    const pc = parseInt(postcode);
    
    // Metro areas
    if ((pc >= 2000 && pc <= 2249) || (pc >= 2555 && pc <= 2574) || 
        (pc >= 2740 && pc <= 2786)) {
        return 'Metro Area NSW';
    }
    if ((pc >= 3000 && pc <= 3207) || (pc >= 3335 && pc <= 3341) || 
        (pc >= 3750 && pc <= 3811)) {
        return 'Metro Area VIC';
    }
    // Add more regions as needed...
    
    return 'Other';
}
```

### Multiple Address Support

For forms with multiple addresses, use array field names:

```html
<input name="address_line_1[]">
<input name="suburb[]">
<input name="postcode[]">
```

### Custom Country Restrictions

Change the `components` parameter in the controller:

```php
'components' => 'country:us', // United States
'components' => 'country:gb', // United Kingdom
'components' => 'country:au|country:nz', // Australia and New Zealand
```

---

## Production Checklist

### 🚀 Before Going Live

#### Security
- [ ] API key uses IP restrictions (NOT referrer restrictions)
- [ ] CSRF protection enabled on routes
- [ ] Rate limiting added to routes (recommended)
- [ ] Sensitive data not logged
- [ ] SSL certificate installed

#### Performance
- [ ] JavaScript minified for production
- [ ] CSS minified for production
- [ ] Consider caching frequent addresses
- [ ] Monitor API usage in Google Cloud Console

#### Testing
- [ ] Test from production environment
- [ ] Test on mobile devices
- [ ] Test with slow network
- [ ] Test error scenarios
- [ ] Verify all address types work

#### Monitoring
- [ ] Set up error logging
- [ ] Add Google Cloud billing alerts
- [ ] Monitor API quota usage
- [ ] Set up uptime monitoring

---

## Common Issues Quick Reference

| Issue | Quick Fix | Time |
|-------|-----------|------|
| "API keys with referer restrictions..." | Change API key restriction to "None" or "IP addresses" | 2 min |
| Timeout errors | Increase timeouts: 30s backend, 35s frontend | 5 min |
| No suggestions appear | Check routes, CSRF token, API key in .env | 10 min |
| Postcode not populating | Verify `fields` includes `address_components` | 5 min |
| Unit number wrong format | Check `unitNumber/streetNumber streetName` logic | 10 min |
| 429 Too Many Requests | Add rate limiting, check quota | 15 min |

---

## API Costs & Limits

### Google Places API Pricing (as of 2024)

- **Autocomplete (per session):** $2.83 per 1,000 requests
- **Place Details:** $17 per 1,000 requests
- **Free tier:** $200/month credit (≈70 sessions per day)

### Optimization Tips

1. **Session Tokens:** Implement session tokens to reduce costs
2. **Caching:** Cache frequently used addresses
3. **Debouncing:** Add delay before API call (e.g., 300ms)
4. **Minimum Characters:** Require 3+ characters before searching

---

## Security Best Practices

1. ✅ **Backend Proxy:** Always use backend proxy (never expose API key to frontend)
2. ✅ **API Key Restrictions:** Use IP restrictions for production
3. ✅ **CSRF Protection:** Include CSRF token in all requests
4. ✅ **Input Validation:** Validate and sanitize all address inputs
5. ✅ **Rate Limiting:** Add rate limiting to your routes
6. ✅ **Logging:** Log API errors for monitoring

---

## Complete File Structure

```
your-laravel-app/
├── app/
│   └── Http/
│       └── Controllers/
│           └── AddressController.php
├── public/
│   ├── css/
│   │   └── address-autocomplete.css
│   └── js/
│       └── address-autocomplete.js
├── resources/
│   └── views/
│       └── components/
│           └── address-autocomplete.blade.php
├── routes/
│   └── web.php
└── .env
```

---

## Field Names Reference

### Default Field Names Used in Guide
```javascript
// Search field
.address-search-input

// Form fields
address_line_1  // Street address (e.g., "8/278 Collins Street")
address_line_2  // Additional info (suite, building, etc.)
suburb          // City/Locality
state           // State/Province (short form, e.g., "VIC")
postcode        // Postal code (e.g., "3000")
country         // Country name (e.g., "Australia")
```

### For Multiple Addresses
Use array notation:
```html
<input name="address_line_1[]">
<input name="suburb[]">
<input name="postcode[]">
```

---

## Implementation Time Estimates

| Task | First Time | With Experience |
|------|-----------|-----------------|
| Google Cloud Setup | 15 min | 5 min |
| Backend Implementation | 30 min | 10 min |
| Frontend JavaScript | 30 min | 15 min |
| Blade Views | 20 min | 10 min |
| CSS Styling | 15 min | 5 min |
| Testing | 20 min | 10 min |
| **Total** | **2-3 hours** | **45-60 min** |

---

## Support & Resources

- **Google Places API Docs:** https://developers.google.com/maps/documentation/places/web-service
- **Laravel Docs:** https://laravel.com/docs
- **jQuery Docs:** https://api.jquery.com/
- **Test Google API:** https://console.cloud.google.com/google/maps-apis/

---

## Quick Copy-Paste Commands

### Laravel Commands
```bash
# Clear config cache
php artisan config:clear

# List routes
php artisan route:list | grep address

# Create controller
php artisan make:controller AddressController

# Check logs
tail -f storage/logs/laravel.log
```

### Test API Key (CURL)
```bash
curl "https://maps.googleapis.com/maps/api/place/autocomplete/json?input=123+main+street&key=YOUR_API_KEY&types=address&components=country:au"
```

---

## Changelog

- **v1.0** - Initial implementation with basic autocomplete
- **v1.1** - Added unit number support (Unit/Street format)
- **v1.2** - Enhanced postcode extraction with fallbacks
- **v1.3** - Added timeout handling and error logging
- **v1.4** - Added quick setup checklist and production checklist

---

## License

This implementation guide is provided as-is for educational and commercial use.

---

**Created:** January 2024  
**Last Updated:** January 2024  
**Version:** 1.4  
**Author:** Migration Manager Development Team  
**Tested With:** Laravel 8+, jQuery 3.x, Google Places API v1
