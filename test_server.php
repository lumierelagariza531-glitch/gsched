<?php
$baseUrl = 'http://127.0.0.1:8765';

// Test 1: Check login page loads
$context = stream_context_create(['http' => ['method' => 'GET', 'ignore_errors' => true]]);
$response = @file_get_contents($baseUrl . '/login', false, $context);
$headers = $http_response_header ?? [];

echo "Test 1 - Login page: HTTP response received" . PHP_EOL;
if (strpos($response, 'G-SCHED') !== false) {
    echo "G-SCHED brand found in login page" . PHP_EOL;
} else {
    echo "ERROR: G-SCHED not found in login page" . PHP_EOL;
}
if (strpos($response, 'csrf-token') !== false) {
    echo "CSRF token found in login page" . PHP_EOL;
} else {
    echo "ERROR: CSRF token not found in login page" . PHP_EOL;
}
