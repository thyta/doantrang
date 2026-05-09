<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');

$dataFile = __DIR__ . '/storage/data.json';

function getData($file)
{
    $default = [
        "visits" => 0,
        "unique_visits" => 0,
        "unique_ips" => [],
        "replay_clicks" => 0,
        "exit_count" => 0,
        "logs" => [],
        "replay_logs" => [],
        "exit_logs" => []
    ];

    if (!file_exists($file)) {
        file_put_contents(
            $file,
            json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
        return $default;
    }

    $data = json_decode(file_get_contents($file), true);

    return is_array($data) ? $data : $default;
}

$data = getData($dataFile);

$ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

$data['visits']++;

if (!in_array($ip, $data['unique_ips'])) {
    $data['unique_ips'][] = $ip;
    $data['unique_visits']++;
}

$data['logs'][] = [
    'time' => date('d/m/Y H:i:s'),
    'ip' => $ip,
    'device' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
    'referer' => $_SERVER['HTTP_REFERER'] ?? 'Direct'
];

$data['logs'] = array_slice($data['logs'], -50);

file_put_contents(
    $dataFile,
    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    LOCK_EX
);

$messages = [
    "Chúc em mơ đẹp 🌸",
    "Ngủ ngon nhé 🌷",
    "Mơ thật đẹp nha ✨",
];

$msg = $messages[array_rand($messages)];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>congchuadongdanh</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<canvas id="canvas"></canvas>

<div class="message-container">
    <div class="message-box">
        <?= htmlspecialchars($msg) ?>
    </div>

    <button id="replayBtn" class="replay-btn">
        Ấn vào để xem lần nữa nhé!
    </button>
</div>

<script src="js/script.js"></script>

</body>
</html>