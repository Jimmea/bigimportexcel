<?php
class CronController extends Zend_Controller_Action
{
	
	function init(){
        if (php_sapi_name() != 'cli') {
        }
		ini_set('memory_limit', '-1');
    }


    //php shell.php manager cron insert-test
    public function insertTestAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $file_name = ROOT_DIR."/data/upload/data_test.csv";
        $dataImport = new dataImport();
        //read file with php excel
        $db = Zend_Registry::get('db');
        $db->query( 'SET NAMES utf8' );
        $db->query( 'SET CHARACTER SET utf8' );
        $reader = PHPExcel_IOFactory::createReader('CSV')
            ->setDelimiter(',')
            ->setEnclosure('"')
            ->setLineEnding("\n")
            ->setSheetIndex(0)
            ->load($file_name);
        $objWorksheet = $reader->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();
        
        //read from file
        $result = array();
        for ($row = 2; $row <= $highestRow; ++$row)
        {
            $sNumber    = $objWorksheet->getCellByColumnAndRow ( 0, $row )->getValue ();
            $arrInsert = array (
                'short_code' => $sNumber,
                'sku' => $objWorksheet->getCellByColumnAndRow ( 1, $row )->getValue (),

            );
            echo $row . '/ Insert sku ' . $sNumber;
            echo "\n";
            try {
                $dataImport->insert($arrInsert);
            } catch (Exception $exception) {
                var_dump($exception);die;
            }
        }
        exit;
    }

}
