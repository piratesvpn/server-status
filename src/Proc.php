<?php

class Proc {

	protected $proc = '/proc';

	public function __construct() {}

	public function path($resource) {
		return $this->proc.'/'.$resource;
	}

	public function open($resource) {
		$path = $this->path($resource);

		return trim(file_get_contents($path));
	}

	public function toArray($resource) {
		$contents = $this->open($resource);

		return explode("\n", $contents);
	}

	public function stat($pid) {
		$contents = $this->open($pid . '/stat');
		$parts = preg_split('#\s+#', $contents);

		$columns = array(
			'pid', // The process ID
			'comm', // The filename of the executable
			'state', // One character from the string "RSDZTW"
			'ppid', // The PID of the parent
			'pgrp', // The process group ID of the process
			'session', // The session ID of the process
			'tty_nr', // The controlling terminal of the process
			'tpgid', // The ID of the foreground process group of the controlling terminal of the process
			'flags', // The kernel flags word of the process
			'minflt', // The number of minor faults
			'cminflt', // The number of minor faults that the process's waited-for children have made
			'majflt', // The number of major faults
			'cmajflt', // The number of major faults that the process's waited-for children have made
			'utime', // Amount of time that this process has been scheduled in user mode in clock ticks
			'stime', // Amount of time that this process has been scheduled in kernel mode
			'cutime', // Time that this process's waited-for children in user mode
			'cstime', // Time that this process's waited-for children in kernel mode
			'priority', // For processes running a real-time scheduling policy
			'nice', // The nice value
			'num_threads', // Number of threads in this process
			'itrealvalue', // The time in jiffies before the next SIGALRM
			'starttime', // The time the process started after system boot in clock ticks
			'vsize', // Virtual memory size in bytes
			'rss', // Resident Set Size: number of pages the process has in real memory
		);

		return array_combine($columns, array_slice($parts, 0, count($columns)));
	}

	public function statm($pid) {
		$contents = $this->open($pid . '/statm');
		$values = preg_split('#\s+#', $contents);

		$columns = ['total', 'rss', 'shared', 'text', 'data', 'lib', 'dirty'];

		return array_combine($columns, $values);
	}

	public function uptime() {
		$contents = $this->open('uptime');
		$values = preg_split('#\s+#', $contents);
		return time() - intval($values[0]);
	}

	public function pids() {
		$fi = new FilesystemIterator($this->proc, FilesystemIterator::SKIP_DOTS);
		$ps = array();

		foreach($fi as $fileinfo) {
			$name = $fileinfo->getFilename();

			if(is_numeric($name)) {
				$ps[] = $name;
			}
		}

		return $ps;
	}

	public function cmdline($pid) {
		$contents = $this->open($pid.'/cmdline');

		$lines = explode("\0", $contents);
		$cmd = $lines[0];

		if(strpos($cmd, '/') === 0) {
			$cmd = basename($cmd);
		}

		return $cmd;
	}

	public function meminfo() {
		$meminfo = array();

		foreach($this->toArray('meminfo') as $line) {
			$values = preg_split('#\s+#', $line);
			$meminfo[substr($values[0], 0, -1)] = $values[1] * 1024;
		}

		return $meminfo;
	}

	public function mounts() {
		$mounts = array();

		foreach($this->toArray('mounts') as $line) {
			$parts = preg_split('#\s+#', trim($line));

			if(strpos($parts[0], '/dev') === 0) {
				$mounts[] = realpath($parts[0]);
			}
		}

		return $mounts;
	}

	public function diskstats() {
		$stats = array();

		$columns = array(
			'major', // major number
			'minor', // minor mumber
		 	'device', // device name
			'reads', // reads completed successfully
			'reads_merged', // reads merged
			'read_sectors', // sectors read
			'ms_spent_reading', // time spent reading (ms)
			'writes', // writes completed
			'writes_merged', // writes merged
			'written_sectors', // sectors written
			'ms_spent_writing', // time spent writing (ms)
			'ios_in_progress', // I/Os currently in progress
			'ms_spent_doing_io', // time spent doing I/Os (ms)
			'ms_weighted', // weighted time spent doing I/Os (ms)
		);

		foreach($this->toArray('diskstats') as $line) {
			$values = preg_split('#\s+#', trim($line));
			$stats[] = array_combine($columns, $values);
		}

		return $stats;
	}

}