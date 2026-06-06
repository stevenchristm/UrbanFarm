<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$groqKey = env('GROQ_API_KEY');
if (!$groqKey) {
    die("No API Key\n");
}

$url = "https://api.groq.com/openai/v1/chat/completions";

$payload = [
    "model" => "llama-3.2-90b-vision-preview",
    "messages" => [
        [
            "role" => "user", 
            "content" => [
                ["type" => "text", "text" => "What is in this image?"],
                ["type" => "image_url", "image_url" => ["url" => "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAGBAQABPxA="]]
            ]
        ]
    ],
    "temperature" => 0.5,
    "max_tokens" => 1024,
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $groqKey
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

echo "cURL Error: $error\n";
$result = json_decode($response, true);
if (isset($result['choices'][0]['message']['content'])) {
    $text = $result['choices'][0]['message']['content'];
    echo "FULL TEXT (" . strlen($text) . " chars):\n$text\n";
} elseif (isset($result['error'])) {
    echo "API ERROR: " . $result['error']['message'] . "\n";
} else {
    echo "RAW RESPONSE:\n$response\n";
}
