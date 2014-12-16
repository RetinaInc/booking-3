<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// as it is a demonstration there is no PSR standard autoloader in use for the sake of simplicity
//
//spl_autoload_register(function($className) {
//
//	$sourceFile = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $className . '.php';
//	if (file_exists($sourceFile)) {
//		require_once $sourceFile;
//	}
//});
