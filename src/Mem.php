<?php

class Mem
{
	protected $proc;

	public function __construct(Proc $proc)
    {
		$this->proc = $proc;
	}

	public function info(): array
    {
		$meminfo = $this->proc->meminfo();

		$info['total'] = $meminfo['MemTotal'];
		$info['free'] = $meminfo['MemFree'];
		$info['buffers'] = $meminfo['Buffers'];
		$info['cached'] = $meminfo['Cached'];

		return $info;
	}

	public function usage(int $pid): int
    {
		$memory = $this->proc->statm($pid);

		return $memory['rss'] * 1024;
	}

	public function profile(): array
    {
        $index = 0;
		$usage = [];

		foreach($this->proc->pids() as $pid) {
			$cmd = $this->proc->cmdline($pid);

			if($cmd == '') {
                continue;
            }

			$memory = $this->usage($pid);

			if( ! isset($usage[$index])) {
				$usage[$index] = array(
					'cmd' => $cmd,
					'pids' => array(),
					'memory' => 0,
				);
			}

			$usage[$index]['pids'][] = $pid;
			$usage[$index]['memory'] += $memory;
            $index++;
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
