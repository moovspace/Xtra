<?php
namespace Xtra\Mysql;

use \PDO;
use \Exception;
use Xtra\Settings\Config;

class MysqlConnect extends Config
{
	function __construct(){
		// Mysql object
		$this->pdo = $this->Conn();
	}

	final function Conn(){
		try{
			// pdo
			$con = new PDO('mysql:host='.self::MYSQL_HOST.';port='.self::MYSQL_PORT.';dbname='.self::MYSQL_DBNAME.';charset=utf8mb4', self::MYSQL_USER, self::MYSQL_PASS);
			// show warning text
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			// throw error exception
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//Default fetch mode
			$con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			// don't colose connecion on script end
			$con->setAttribute(PDO::ATTR_PERSISTENT, true);
			// set utf for connection utf8_general_ci or utf8_unicode_ci
			$con->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
			// prepared statements, don't cache query with prepared statments
			$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			// Auto commit
			// $con->setAttribute(PDO::ATTR_AUTOCOMMIT,flase);
			// Buffered querry default
			// $con->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true);
			return $con;
		}catch(Exception $e){
			echo 'Connection failed: ' . $e->getMessage();
			// print_r($e->errorInfo());
			return null;
		}
	}

	/**
	 * Query
	 * Secure mysql query
	 *
	 * $this->Query("SELECT * FROM users WHERE id = :id", array(':id' => 1));
	 */
	function Query($sql, $arr = array()){
		try{
			$r = $this->pdo->prepare($sql);
			$r->execute($arr);
			$out = $r->fetchAll();
			$lid = (int) $this->pdo->lastInsertId();
			if(!empty($out)){
				return $out;
			}else if($lid > 0){
				return $lid;
			}
			return $r->rowCount();
		}catch(Exception $e){
			print_r($e);
		}
	}
}
?>
