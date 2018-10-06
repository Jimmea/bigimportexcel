<?php
class Globals
{
	public static $db=null;
	public static $lang=null;
	static function connect()
	{
		try {
			$zend_config=new Zend_Config_Ini(ROOT_DIR . '/conf/config.ini','production');	
			if($zend_config){
				self::$db=Zend_Db::factory($zend_config->database);
				self::$db->getProfiler()->setEnabled(true);
				Zend_Db_Table_Abstract::setDefaultAdapter(self::$db);
	            try
	            {
	                self::$db->query( 'SET NAMES utf8' );
	                self::$db->query( 'SET CHARACTER SET utf8' );					
	            } catch( Exception $e){
						echo "Can't conntect to database " . $zend_config->database;
				}
			}
		}catch (Exception  $e){
			echo "Not found config file";
			die();
		}
		if(!self::$db){
			echo "Can't conntect to database " . $zend_config->database;
			die();
		}
	}

	public function getDBMgr(&$objDbMaster)
	{	
		Globals::connect($objDbMaster,'not');
	}


}
?>