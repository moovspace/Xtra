<?php
// autoload php classes from namespace sample without composer
spl_autoload_register(function($class) {
	// Load from folder
	// $class = $_SERVER['DOCUMENT_ROOT'].'/src/' . str_replace('\\', '/', $class) . '.php';
	// Load from current folder
	$class = str_replace("Xtra","",$class); // remove Xtra
	$class = ltrim($class,"\\");
	$class = str_replace('\\', '/', $class) . '.php';
	// Load class if exists
	if (file_exists($class)) {
		if (!class_exists($class)) {
			require_once($class);
		}
	}
});
// Php settings, start session after class load !!!!!
include('php-ini.php');
?>
