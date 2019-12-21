<?php
// Start session always after
// require vendor/autoload.php 
// ($_SESSION class serialize error in cart)
session_start();

// Errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// File upload
ini_set('memory_limit', '-1');
ini_set('post_max_size','500M');
ini_set('upload_max_filesize','50M');

// Compresion
ini_set('zlib.output_compression', 'On');
ini_set('zlib.output_compression_level',6);

// Php script execution time
// ini_set('max_input_time',600);
// ini_set('max_execution_time', 600);
set_time_limit(0);

// Timezone
date_default_timezone_set('Etc/UTC');

// Charset
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
?>
