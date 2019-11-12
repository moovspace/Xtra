<?php
namespace Xtra\Upload;
use \Exception;

/**
 * Upload - upload multiple and single files from from or curl
 *
 * Form sample upload
 * <form enctype="multipart/form-data" method="post">
 *  Multiple files
 *  <input type="file" name="files[]" multiple>
 *  Single file
 *  <input type="file" name="file">
 *  <input type="submit" value="Send">
 * </form>
 */
class Upload
{
    protected $Files = array();
    protected $MaxSizeMb = 10;
    protected $SecretKey = '';
    protected $FilesLimit = 5;

    function __construct(){
        // php.ini
        ini_set('post_max_size', $this->MaxSizeMb.'M');
        ini_set('upload_max_filesize', $this->MaxSizeMb.'M');
    }

    /**
     * ValidSecretKey Authentication method
     * @param string $key      Your secret key
     * @param string $post_key $_POST secret key from user
     */
    function ValidSecretKey($key, $post_key){
        if($key != $post_key){
            throw new Exception("Error access key", 7);
        }
    }

    function RemoveEmptySubFolders($path){
        foreach (glob($path.'/'."*") as $file){
            if(is_dir($file)){
                rmdir($file);
            }
        }
    }

    function Files(){
        return $this->Files;
    }

    function FilesLimit($limit = 5){
        if($limit >= 1){
            $this->FilesLimit = $limit;
        }else{
            $this->FilesLimit = 5;
        }
    }

    function MaxSizeMb($mb = 10){
        $mb = (int) $mb;
        if($mb <= 0){ $mb = 10; }
        $this->MaxSizeMb = $mb;
        // php.ini
        ini_set('post_max_size', ($mb+$mb).'M');
        ini_set('upload_max_filesize', $mb.'M');
    }

    function GetFiles(){
        foreach ($_FILES as $k => $v) {
            if (is_array($v['name'])) {
                $cnt = count($v['name']);
                for($i = 0; $i < $cnt; $i++) {
                    $err = $v['error'][$i];
                    if($err == 0){
                        $f['name'] = $v['name'][$i];
                        $f['tmp_name'] = $v['tmp_name'][$i];
                        $f['type'] = $v['type'][$i];
                        $f['error'] = $v['error'][$i];
                        $f['size'] = $v['size'][$i];

                        $this->Files[] = $f;
                    }else{
                        if($err == 1 || $err == 2){
                            throw new Exception("Error file size! Max file size (post/file) in php.ini: ".ini_get('post_max_size').'/'.ini_get('upload_max_filesize') , 1);
                        }
                        if($err == 6 || $err == 7){
                            throw new Exception("Error file write to disk" , 2);
                        }
                    }
                }
            }else{
                if($v['error'] == 0){
                    $this->Files[] = $v;
                }
            }
        }

        if(count($this->Files) > $this->FilesLimit){
            throw new Exception("Error files limit reached: ".$this->FilesLimit , 8);
        }
    }

    function ValidError(){
        foreach ($this->Files as $f) {
            $err = $f['error'];
            if($err == 1 || $err == 2){
                throw new Exception("Error file size! Max file size (post/file): ".ini_get('post_max_size').'/'.ini_get('upload_max_filesize') , 1);
            }
            if($err == 6 || $err == 7){
                throw new Exception("Error file write to disk" , 2);
            }
        }
    }

    function ValidSize(){
        foreach ($this->Files as $f) {
            $size = $f['size'];
            if($size > $this->MaxSizeMb * (1024 * 1024)){
                throw new Exception("Error file size! Max file size: ".$this->MaxSizeMb."MB"  , 3);
            }
        }
    }

    function ValidExtension(){
        foreach ($this->Files as $f) {
            $ext = $this->GetExtension($f['name']);
            // Allow all files if empty
            if(!empty($this->Extensions)){
                if(!in_array($ext, $this->Extensions)){
                    throw new Exception("Error file extension! Allowed: ".implode(', ', $this->Extensions) , 4);
                }
            }
        }
    }

    function AddExtension($ext = ""){
        if(is_array($ext)){
            foreach ($ext as $k => $v) {
                $this->Extensions[] = strtolower($v);
            }
        }else{
            $this->Extensions[] = strtolower($ext);
        }
    }

    /**
     * Get file extension
     *
     * @param string $path Set path to file
     * @return string Return file extension
     */
    function GetExtension($path){
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Random unique id
     *
     * @return string
     */
    function UniqueId(){
        return md5(uniqid().microtime().rand(1,99999));
    }

    function UploadDir($dir = "media"){
        $dir = ltrim($dir,'/');
        $dir = ltrim($dir,'.');
        $dir = ltrim($dir,'/');
        $dir = rtrim($dir,'/');
        if(empty($dir)){ $dir = "media"; }
        $dir = $_SERVER['DOCUMENT_ROOT']."/".$dir."/".date("Y-m-d",time());
        return $dir;
    }

    /**
     * Upload Upload all files
     * @return array Uploaded files list
     */
    function Upload($dirname = "media"){
        // Files
        $this->GetFiles();
        // Test errors
        $this->ValidError();
        $this->ValidSize();
        $this->ValidExtension();

        // Upload
        $i = 0;
        foreach ($this->Files as $f) {
            $tmp = $f['tmp_name'];
            $old = $f['name'];
            $ext = $this->GetExtension($old); // ext
            $uid = $this->UniqueId();
            $file = $uid.'.'.strtolower($ext);
            $dir = $this->UploadDir($dirname).'/'.$uid;
            $path = $dir.'/'.$file;
            mkdir($dir, 0777, true);
            if(!file_exists($dir)){
                throw new Exception("Error can't create upload directory: ".$dir, 5);
            }
            $ok = move_uploaded_file($tmp, $path);
            if($ok == false){
                // Upload error
                // throw new Exception("Error file upload", 6);
            }else{
	        // path from document root
        	$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
	        $this->UploadedFiles[] = $path;
            }
            $i++;
        }
        return $this->UploadedFiles;
    }
}

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

try{
    // Create
    $f = new Upload();

    // Test secure key
    // $f->ValidSecretKey('myKey',$_POST['key']);

    // File size
    $this->MaxSizeMb(10);

    // Files limit
    $this->FilesLimit(3);

    // Extensions
    $f->AddExtension('jpg');
    $f->AddExtension(['pdf','txt']);

    // Upload to folder
    $files = $f->Upload("media");

    // Get uploaded files paths
    print_r($files);

}catch(Exception $e){
    print_r($e);
}
*/
?>
