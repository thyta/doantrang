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

$type = $_POST['type'] ?? 'exit';

if ($type === 'replay') {
    $data['replay_clicks']++;

    $data['replay_logs'][] = [
        'time' => date('d/m/Y H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    ];

    $data['replay_logs'] = array_slice($data['replay_logs'], -50);
} else {
    $data['exit_count']++;

    $data['exit_logs'][] = [
        'time' => date('d/m/Y H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    ];

    $data['exit_logs'] = array_slice($data['exit_logs'], -50);
}

file_put_contents(
    $dataFile,
    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    LOCK_EX
);

echo json_encode(['status' => 'success']);