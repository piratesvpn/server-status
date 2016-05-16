<?php

class Mem {

	protected $proc;

	public function __construct(Proc $proc) {
		$this->proc = $proc;
	}

	public function info() {
		$meminfo = $this->proc->meminfo();

		$info['total'] = $meminfo['MemTotal'];
		$info['free'] = $meminfo['MemFree'];
		$info['buffers'] = $meminfo['Buffers'];
		$info['cached'] = $meminfo['Cached'];

		return $info;
	}

	public function usage($pid) {
		$memory = $this->proc->statm($pid);

		return $memory['rss'] * 1024;
	}

	public function profile() {
		$usage = array();

		foreach($this->proc->pids() as $pid) {
			$cmd = $this->proc->cmdline($pid);

			if($cmd == '') continue;

			$memory = $this->usage($pid);

			$index = crc32($cmd);

			if( ! isset($usage[$index])) {
				$usage[$index] = array(
					'cmd' => $cmd,
					'pids' => array(),
					'memory' => 0,
				);
			}

			$usage[$index]['pids'][] = $pid;
			$usage[$index]['memory'] += $memory;
		}

		usort($usage, function($a, $b) {
			if($a['memory'] == $b['memory']) {
				return 0;
			}
			return ($a['memory'] > $b['memory']) ? -1 : 1;
		});

		return $usage;
	}

}