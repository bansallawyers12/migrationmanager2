<?php
/**
 * Test Script for Real-time Messaging API
 * Run this to test all messaging endpoints
 */

// Configuration
$baseUrl = 'http://localhost/migration_manager_crm/public/api';
$testUserId = 1; // Replace with actual user ID
$testRecipientId = 2; // Replace with actual recipient ID

// Get authentication token (you'll need to replace this with actual token)
$authToken = 'your-auth-token-here';

echo "🧪 Testing Real-time Messaging API\n";
echo "================================\n\n";

// Test 1: Get Recipients
echo "1. Testing Get Recipients...\n";
$response = makeRequest('GET', '/messages/recipients', [], $authToken);
echo "Status: " . $response['status'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Send Message
echo "2. Testing Send Message...\n";
$messageData = [
    'recipient_id' => $testRecipientId,
    'subject' => 'Test Message from API',
    'message' => 'This is a test message sent via API at ' . date('Y-m-d H:i:s'),
    'message_type' => 'normal'
];
$response = makeRequest('POST', '/messages/send', $messageData, $authToken);
echo "Status: " . $response['status'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['status'] == 201) {
    $messageId = $response['data']['data']['message_id'] ?? null;
    
    if ($messageId) {
        // Test 3: Get Message Details
        echo "3. Testing Get Message Details...\n";
        $response = makeRequest('GET', "/messages/{$messageId}", [], $authToken);
        echo "Status: " . $response['status'] . "\n";
        echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";
        
        // Test 4: Mark as Read
        echo "4. Testing Mark as Read...\n";
        $response = makeRequest('PUT', "/messages/{$messageId}/read", [], $authToken);
        echo "Status: " . $response['status'] . "\n";
        echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";
    }
}

// Test 5: Get Messages
echo "5. Testing Get Messages...\n";
$response = makeRequest('GET', '/messages?limit=10', [], $authToken);
echo "Status: " . $response['status'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

// Test 6: Get Unread Count
echo "6. Testing Get Unread Count...\n";
$response = makeRequest('GET', '/messages/unread-count', [], $authToken);
echo "Status: " . $response['status'] . "\n";
echo "Response: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n\n";

echo "✅ API Testing Complete!\n";
echo "================================\n";
echo "Next Steps:\n";
echo "1. Test website integration\n";
echo "2. Test mobile app integration\n";
echo "3. Test real-time WebSocket connections\n";

function makeRequest($method, $endpoint, $data = [], $authToken = null) {
    $url = $GLOBALS['baseUrl'] . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $authToken,
        'X-CSRF-TOKEN: test-token'
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}
?>
