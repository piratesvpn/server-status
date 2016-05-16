<?php

require __DIR__ . '/../start.php';

$proc = new Proc;
$cpu = new Cpu($proc);
$mem = new Mem($proc, $cpu);
$devices = new Devices($proc);

$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

switch($type) {
	case 'cpu':
		$data = $cpu->usage();
		break;

	case 'memory':
		$data = $mem->info();
		break;

	case 'io':
		$data = $devices->usage();
		break;

	case 'ps':
		$data = $mem->profile();
		break;
}

header('Content-type: application/json; charset=utf-8');
echo json_encode($data);
