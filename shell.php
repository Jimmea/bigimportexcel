<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
define('SITE'		, 1);
define('ROOT_DIR'	, 'C:/xampp5/htdocs/demo');
define('ROOT_LIB'	, ROOT_DIR . '/library');
define('SITE_NAME'	, 'manager');
require_once ROOT_DIR . '/globals.php';

class Bootstrap
{    
    var $_subDomain = '';
    public function __construct()
    {        
        $this->_subDomain = 'manager';
		set_include_path(
			ROOT_DIR .
			PATH_SEPARATOR . ROOT_DIR . '/library/'.
			PATH_SEPARATOR . ROOT_DIR . '/app/manager/' .
			PATH_SEPARATOR . ROOT_DIR . '/app/manager/controllers/' .
			PATH_SEPARATOR . ROOT_DIR . '/app/manager/models/' .
			PATH_SEPARATOR . ROOT_DIR . '/app/manager/views/'.
			PATH_SEPARATOR . get_include_path()
		);		
		
		include 'Zend/Loader.php';
		Zend_Loader::registerAutoload();
		
		Globals::connect();
		Zend_Registry::set('db', Globals::$db);
    }

    public function runApp()
    {
        // setup controller        
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->throwExceptions(true);
        $frontController->addModuleDirectory(ROOT_DIR . '/app');
        
        $argv = $_SERVER['argv'];
        
        $frontController->setDefaultModule(isset($argv[1]) ? $argv[1] : $this->_subDomain);
        $frontController->setDefaultControllerName(isset($argv[2]) ? $argv[2] : 'cron');
        $frontController->setDefaultAction(isset($argv[3]) ? $argv[3] : 'index');
        
        //setup request
        $request = new Zend_Controller_Request_Simple();
        $count = count($argv);
        if ($count>4) {
            for ($i=4; $i<$count; $i++) {
                $param = explode('=', $argv[$i]);
                $request->setParam($param[0], $param[1]);
            }
        }
        
        $frontController->setRequest($request);

        //setup router
        require_once 'cli/router.php';
        $frontController->setRouter(new Bob_Controller_Router_Cli());
        
        //setup response
        $frontController->setResponse(new Zend_Controller_Response_Cli());
        
        $frontController->setParam('disableOutputBuffering', 1);
        
        try {
            $frontController->dispatch();                                    
        } catch (Exception $exception) {
           # var_dump($exception);die();
        }
    }
}

$objBootstrap = new Bootstrap();
$objBootstrap->runApp();