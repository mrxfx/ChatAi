<?php
header("Content-Type: application/json");
error_reporting(0);

// Allow GET or POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $prompt = $_GET['text'] ?? '';
    $model = strtolower(trim($_GET['model'] ?? 'gemini'));
} else {
    $input = json_decode(file_get_contents("php://input"), true);
    $prompt = trim($input['prompt'] ?? '');
    $model = strtolower(trim($input['model'] ?? 'gemini'));
}

if (!$prompt) {
    echo json_encode(["error" => "Prompt is required. Use ?text=your+question"]);
    exit;
}

// Define APIs
switch ($model) {
    case 'chatgpt':
        $api = "https://gpt4free.lol/api/chat";
        $payload = [
            "messages" => [["role" => "user", "content" => $prompt]],
            "model" => "gpt-3.5-turbo"
        ];
        break;

    case 'gemini':
        $api = "https://gpt4free.lol/api/google";
        $payload = [
            "messages" => [["role" => "user", "content" => $prompt]]
        ];
        break;

    case 'deepseek':
        $api = "https://gpt4free.lol/api/deepseek";
        $payload = [
            "messages" => [["role" => "user", "content" => $prompt]]
        ];
        break;

    default:
        echo json_encode(["error" => "Invalid model. Use gemini, chatgpt, or deepseek"]);
        exit;
}

$response = call_api($api, $payload);
echo json_encode($response);

function call_api($url, $data) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_TIMEOUT => 15
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true) ?: ["error" => "No response or API error"];
}