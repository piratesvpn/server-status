<?php

class Devices {

	protected $proc;

	public function __construct(Proc $proc) {
		$this->proc = $proc;
	}

	public function mounts() {
		return $this->proc->mounts();
	}

	function stats() {
		$mounts = $this->mounts();
		$stats = array();

		foreach($this->proc->diskstats() as $diskstat) {
			foreach($mounts as $mount) {
				if(strpos($mount, '/' . $diskstat['device'])) {
					$stats[$mount] = $diskstat;
				}
			}
		}

		return $stats;
	}

	function usage() {
		$a = $this->stats();

		// delay
		usleep($elapsed = 100000);

		$b = $this->stats();

		// changes
		$c = array();

		foreach(array_keys($a) as $device) {
			foreach(array_keys($a[$device]) as $prop) {
				$c[$device][$prop] = $b[$device][$prop] - $a[$device][$prop];
			}
		}

		// results
		$stats = array();

		foreach(array_keys($c) as $device) {
			$usage = 100 * $c[$device]['ms_spent_doing_io'] / ($elapsed / 1000);

			$stats[] = array(
				'device' => $device,
				'usage' => round($usage),
			);
		}

		return $stats;
	}

}