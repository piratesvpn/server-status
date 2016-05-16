<?php

error_reporting(-1);
ini_set('display_errors', true);

spl_autoload_register(function($class) {
	if(is_file($file = __DIR__ . '/src/' . $class . '.php')) {
		return require $file;
	}

	return false;
});
