<?php

class Cpu {

	protected $proc;

	public function __construct(Proc $proc) {
		$this->proc = $proc;
	}

	protected function stats() {
		$cpus = array();

		$columns = array(
			'cpu', // Cpu name
			'user', // Time spent in user mode.
			'nice', // Time spent in user mode with low priority
			'system', // Time spent in system mode.
			'idle', // Time spent in the idle task.
			'iowait', // Time waiting for I/O to complete.
			'irq', // Time servicing interrupts.
			'softirq', // Time servicing softirqs.
			'steal', // Stolen time, which is the time spent in other operating systems when running in a virtualized environment
			'guest', // Time spent running a virtual CPU for guest
			'guest_nice', // Time spent running a niced guest (virtual CPU for guest operating systems under the control of the Linux kernel).
		);

		foreach($this->proc->toArray('stat') as $index => $line) {
			$parts = preg_split('#\s+#', trim($line));

			if(preg_match('#^cpu[0-9]+#', $parts[0])) {
				$cpus[$index] = array_combine($columns, $parts);
				$cpus[$index]['total'] = array_sum(array_slice($parts, 1));
			}
		}

		return $cpus;
	}

	public function usage() {
		$a = $this->stats();

		// delay
		usleep($elapsed = 100000);

		$b = $this->stats();

		// changes
		$c = array();

		foreach(array_keys($a) as $index) {
			foreach(array_keys($a[$index]) as $prop) {
				$c[$index][$prop] = $b[$index][$prop] - $a[$index][$prop];
			}
		}

		// results
		$cpus = array();

		foreach(array_keys($a) as $index) {
			$usage = 100 * (($c[$index]['user'] + $c[$index]['system']) / $c[$index]['total']);

			$cpus[] = array(
				'cpu' => $a[$index]['cpu'],
				'usage' => round($usage),
			);
		}

		return $cpus;
	}

}