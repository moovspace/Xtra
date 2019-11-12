<?php
namespace Xtra\Image;

/**
 *  Show iamge
 */
class ImageSecureShow
{
    function __construct(){}

    function ShowImage($name){
        $file = basename($name);
        $path  = 'img/'.$file;
        $mime = mime_content_type($path);
        $types = [ 'gif'=> 'image/gif', 'png'=> 'image/png', 'jpeg'=> 'image/jpeg', 'jpg'=> 'image/jpeg'];
        // if allowed type
        if(in_array($mime, $types)){
            if(file_exists($path)){
                header('Content-type: '.$mime);
                header("Content-Length: " . filesize($path));
                //echo file_get_contents($path);
                readfile($path);
            }
        }
    }
}
?>
