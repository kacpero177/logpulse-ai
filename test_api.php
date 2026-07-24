<?php

$url = 'http://127.0.0.1:8000/api/v1/logs';
$apiKey = 'YOUR_BEARER_TOKEN';

$errorsList = [
    [
        'service' => 'AuthService',
        'level' => 'error',
        'message' => 'Too many failed login attempts for user ID 402',
    ],
    [
        'service' => 'PaymentGateway',
        'level' => 'critical',
        'message' => 'Stripe API connection timeout after 30000ms',
    ],
    [
        'service' => 'EmailService',
        'level' => 'error',
        'message' => 'SMTP host smtp.sendgrid.net unreachable',
    ],
];

foreach ($errorsList as $index => $err) {
    $data = [
        'service_name' => $err['service'],
        'level'        => $err['level'],
        'message'      => $err['message'],
        'stack_trace'  => "Simulated stack trace for error #" . ($index + 1)
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    echo "Wysłano #" . ($index + 1) . " -> " . $response . "\n";
}