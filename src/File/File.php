<?php
namespace Xtra\File;

/**
 * File
 */
class File
{
	protected $Path = null;

    function __construct($path)
    {
        if(file_exists($path)){
			$this->Path = $path;
			$this->ImageInfo();
        }else{
            throw new Exception("File does not exist", 1);
        }
    }

	function Extension(){
		return strtolower(pathinfo(basename($this->Path), PATHINFO_EXTENSION));
    }

    function Name($tolower = 1){
		if($tolower == 1){
			return strtolower(pathinfo(basename($this->Path), PATHINFO_FILENAME));
		}else{
			return pathinfo(basename($this->Path), PATHINFO_FILENAME);
		}
	}

	function Size(){
		return filesize($this->Path);
	}

    function Directory(){
		return pathinfo($this->Path, PATHINFO_DIRNAME);
	}

	function RealPath(){
		return realpath($this->Path);
	}

	function RealPathDirectory(){
		return dirname($this->RealPath());
    }

    function Mime(){
		return mime_content_type($this->Path);
    }

	function Content(){
		return file_get_contents($this->Path);
	}

	function ContentBase64(){
		return base64_encode(file_get_contents($this->Path));
	}

    function IsImage(){
        if(strpos($this->Mime(), "image/") >= 0){
            return 1;
        }
        return 0;
    }

    function ImageInfo(){
        if($this->IsImage()){
			$i = getimagesize($this->Path);
			if(is_array($i)){
				$this->ImageWidth = $i[0];
				$this->ImageHeight = $i[1];
				$this->ImageType = $i[2];
				$this->ImageBits = $i['bits'];
				$this->ImageChannels = $i['channels'];
				$this->ImageMime = $i['mime'];
				return $i;
			}
		}
		return [];
	}

	function ImageWidth(){
        return (int) $this->ImageWidth;
	}

	function ImageHeight(){
        return (int) $this->ImageHeight;
	}

	function ImageType(){
        return (int) $this->ImageType;
	}

	function ImageBits(){
        return (int) $this->ImageBits;
    }

    function ImageChannels(){
        return (int) $this->ImageChannels;
	}
}

/*
try
{
    $f = new File("img/wolf.jpg");

	echo $f->RealPathDirectory();
	echo $f->Directory();
	echo $f->Name();
	echo $f->Extension();
	echo $f->Size();

    if($f->IsImage())
    {
		echo $f->ImageWidth();
		echo $f->ImageHeight();
		echo $f->ImageType();
		echo $f->ImageBits();
		echo $f->ImageChannels();
    }

}catch(Exception $e){
    echo $e->getMessage();
    echo $e->getCode();
}
*/
?>
