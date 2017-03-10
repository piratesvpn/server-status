<?php

error_reporting(-1);
ini_set('display_errors', true);

require __DIR__ . '/../vendor/autoload.php';

option('error_404', function() {
    http_response_code(404);
    header('Content-type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Resource not found'], JSON_PRETTY_PRINT);
});

route('/', function() {
    $proc = new Proc;
    $cpu = new Cpu($proc);
    $mem = new Mem($proc, $cpu);
    $devices = new Devices($proc);

    $data = [
        'cpu' => $cpu->usage(),
        'memory' => $mem->info(),
        'devices' => $devices->usage(),
    ];

    header('Content-type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT);
});

run();
