<?php

class Util
{
	public static function size(int $bytes): string
    {
		$powers = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
		$power = 0;

		while($bytes > 1000) {
			$bytes /= 1024;
			$power++;
		}

		return round($bytes) . $powers[$power];
	}
}
