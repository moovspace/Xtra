<?php
// autoload php classes from namespace sample without composer
spl_autoload_register(function($class) {
	// convert namespace to full file path
	$class = str_replace("Xtra\\","",$class); // remove Xtra\\ from path
	// Load class file
	$class = $_SERVER['DOCUMENT_ROOT'].'/src/' . str_replace('\\', '/', $class) . '.php';
	// Load class if exists
	if (file_exists($class)) {
		if (!class_exists($class)) {
			require_once($class);
		}
	}
});

// Php settings, start session after class load !!!!!
if(file_exists('php-ini.php')){
        require_once('php-ini.php');
}
?>
