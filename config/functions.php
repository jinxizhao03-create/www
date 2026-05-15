<?php
function get_db() {
    static $pdo = null;
    if ($pdo) return $pdo;
    $cfg = include __DIR__ . '/db.php';
    $dsn = 'mysql:host=' . $cfg['host'] . ';port=' . $cfg['port'] . ';dbname=' . $cfg['name'] . ';charset=' . $cfg['charset'];
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    return $pdo;
}

function app_config() {
    return include __DIR__ . '/app.php';
}

function ensure_install() {
    $app = app_config();
    if (!$app['installed'] && strpos($_SERVER['REQUEST_URI'], '/install/') === false) {
        header('Location: /install/index.php');
        exit;
    }
}

function esc($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function call_llm($provider, $apiKey, $prompt, $content) {
    if (trim($apiKey) === '') {
        return "[模拟结果]\n" . $prompt . "\n\n" . $content;
    }
    // PHP 5.6 compatible cURL call template.
    $endpoint = '';
    switch ($provider) {
        case 'deepseek': $endpoint = 'https://api.deepseek.com/v1/chat/completions'; break;
        case 'doubao': $endpoint = 'https://ark.cn-beijing.volces.com/api/v3/chat/completions'; break;
        case 'qwen': $endpoint = 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions'; break;
        default: $endpoint = 'https://api.siliconflow.cn/v1/chat/completions';
    }
    $payload = json_encode(array(
        'model' => 'gpt-4o-mini',
        'messages' => array(
            array('role' => 'system', 'content' => $prompt),
            array('role' => 'user', 'content' => $content)
        ),
        'temperature' => 0.7
    ));

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $resp = curl_exec($ch);
    if ($resp === false) {
        return 'API调用失败：' . curl_error($ch);
    }
    curl_close($ch);
    $json = json_decode($resp, true);
    if (isset($json['choices'][0]['message']['content'])) {
        return $json['choices'][0]['message']['content'];
    }
    return 'API返回异常：' . $resp;
}
