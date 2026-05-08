<?php

$dataFile = __DIR__ . '/storage/data.json';

$data = json_decode(file_get_contents($dataFile), true);

$data['button_clicks']++;

file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));