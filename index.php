<?php

$dataFile = __DIR__ . '/storage/data.json';

if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([
        "visits" => 0,
        "button_clicks" => 0,
        "logs" => []
    ], JSON_PRETTY_PRINT));
}

$data = json_decode(file_get_contents($dataFile), true);

$data['visits']++;

$data['logs'][] = [
    'time' => date('d/m/Y H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'],
    'device' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
    'referer' => $_SERVER['HTTP_REFERER'] ?? 'Direct'
];

$data['logs'] = array_slice($data['logs'], -20);

file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

$messages = [
    "Ngủ ngon nhé ❤️",
    "Chúc em mơ đẹp 🌸",
    "Mong em có giấc ngủ thật yên bình 🌙",
    "Anh nhớ em nhiều 💖"
];

$msg = $messages[array_rand($messages)];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chúc ngủ ngon</title>

<link rel="stylesheet" href="css/style.css">

<style>
/* ===== ORIENTATION WARNING ===== */
#rotateWarning {
    position: fixed;
    inset: 0;
    background: #000;
    color: #fff;
    display: none;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-size: 18px;
    padding: 20px;
    z-index: 9999;
}
</style>

</head>
<body>

<!-- WARNING OVERLAY -->
<div id="rotateWarning">
    Vui lòng xoay ngang điện thoại để có trải nghiệm tốt hơn
</div>

<canvas id="canvas"></canvas>

<div class="message">
    <?= $msg ?>
</div>

<script src="js/script.js"></script>

</body>
</html>