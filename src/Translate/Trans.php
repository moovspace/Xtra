<?php
namespace Xtra\Translate;

/**
 * Translate
 * 	Loading fies with translated strings. 
 *
 *	Filename: 
 *	Lang/messages-pl.trans
 *	Lang/messages-en.trans
 *  Fileformat:
 *  UNIQUE_ID| Translated string here
 *
 * <code>
 * <?php
 * use Xtra\Translate\Trans;
 * 
 * // Change language
 * Trans::change('en');
 *
 * // Show translated string from file
 * echo Trans::find('UNIQUE_ID','messages');
 * ?>
 * </code>
 */
class Trans
{	
	// Errors
	public static $lang;
	public static $msg;

	function __construct()
	{	
		self::Load();
	}

	/**
	 * Load user language from session
	 */
	public static function Load($file = 'messages')
	{
		if(!empty($_SESSION['user']['language'])){
			self::$lang = $_SESSION['user']['language'];
		}else if(empty(self::$lang) || empty($_SESSION['user']['language']) || strlen(self::$lang) != 2){
			self::$lang = 'en';
			$_SESSION['user']['language'] = 'en';
		}
		self::loadFile($file);
	}

	/**
	 * loadFile with translated messages
	 * @param  string $name Translate filename	 
	 */
	public static function loadFile($name = 'messages')
	{
		// $f = $_SERVER['DOCUMENT_ROOT'].'/src/Xtra/Translate/Lang/'.$name.'-'.self::$lang.'.trans';
		$f = 'Lang/'.$name.'-'.self::$lang.'.trans';
		if(file_exists($f)){
			self::$msg = file_get_contents($f);			
		}else{			
			self::$lang = 'en';
			// $f = $_SERVER['DOCUMENT_ROOT'].'/src/Xtra/Translate/Lang/'.$name.'-'.self::$lang.'.trans';
			$f = 'Lang/'.$name.'-'.self::$lang.'.trans';
			if(file_exists($f)){
				self::$msg = file_get_contents($f);
			}else{
				throw new Exception("Error - Translate file does not exist: ". $f, 321);				
			}
		}
	}

	/**
	 * find() search text translation
	 * @param  string $txt  Unique text id
	 * @param  string $file File name default 'messages' -> messages-en.trans
	 * @return string       Return translated string or empty if string does not exist
	 */
	public static function find($txt = '', $file = 'messages')
	{
		self::Load($file);
		preg_match('/('.$txt.')\|(.+)\n/',self::$msg,$match);
		return ltrim(end(explode('|',$match[0])));
	}

	public static function change($lang = 'en')
	{
		$_SESSION['user']['language'] = $lang[0].$lang[1];
		self::$lang = $lang[0].$lang[1];
	}

	public static function getLang()
	{
		return $_SESSION['user']['language'];
	}
}
?>
