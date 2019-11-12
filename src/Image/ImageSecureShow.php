<?php
namespace Xtra\Image;
use \Exception;

class ImageSecureShow
{
	public $MimeTypes = ['gif'=> 'image/gif', 'png'=> 'image/png', 'jpeg'=> 'image/jpeg', 'jpg'=> 'image/jpeg'];

	function __construct(){}

	function ShowImage($path){
		if(file_exists($path)){
			$mime = mime_content_type($path);
			// if allowed type
			if(in_array($mime, $this->MimeTypes)){
				header('Content-type: '.$mime);
				header("Content-Length: " . filesize($path));
				readfile($path);
				//echo file_get_contents($path);
			}else{
				throw new Exception("Error file extension", 1);
			}
		}else{
			throw new Exception("Error file path", 1);
		}
	}
}
?>

<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

try{
	// Test if correct key
	if($_SESSION['user']['key'] == 'secret'){
		// Create object
		$img = new ImageSecureShow();
		// Change types
		$img->MimeTypes = ['png'=> 'image/png', 'jpeg'=> 'image/jpeg', 'jpg'=> 'image/jpeg'];
		// Show image
		$img->ShowImage('/media/image.jpg');
	}

}catch(Exception $e){
	print_r($e);
}
*/
?>

