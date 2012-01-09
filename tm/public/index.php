<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', true);
date_default_timezone_set('Europe/London');

$rootDir = dirname(dirname(__FILE__));
set_include_path($rootDir . '/library' . PATH_SEPARATOR . get_include_path());


require_once 'Zend/Controller/Front.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

require_once 'Zend/Config/Ini.php';

$config = new Zend_Config_Ini('../application/configs/config.ini','app');

$title  = $config->appName;
$params = $config->database->toArray();

Zend_Registry::set('title',$title);

$arrName = array('Ilmia Fatin','Aqila Farzana', 'Imanda Fahrizal');
Zend_Registry::set('credits',$arrName);

$DB = new Zend_Db_Adapter_Pdo_Mysql($params);
    
$DB->setFetchMode(Zend_Db::FETCH_OBJ);
Zend_Registry::set('DB',$DB);



Zend_Controller_Front::run('../application/controllers');

?>