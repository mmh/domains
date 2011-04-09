<?php

require 'includes/mimvic/uvic.php';
use MiMViC as mvc;  

$config = require 'config.php';
mvc\store('config',$config);

require 'includes/redbean/rb.php';
require 'includes/classes/class.ajaxHandler.php';
require 'includes/classes/class.contentHandler.php';
require 'includes/classes/class.dataCollectorHandler.php';
require 'includes/functions.php';
require 'includes/Mobile_Detect.php';

$dsn = 'mysql:host='.mvc\retrieve('config')->dbHost.';dbname='.mvc\retrieve('config')->dbName;
R::setup($dsn,mvc\retrieve('config')->dbUser,mvc\retrieve('config')->dbPassword);
// TODO: RedBean freeze schema

$detect = new Mobile_Detect();

$debug = false;
mvc\store('debug',$debug);
$theme = $detect->isMobile() ? 'mobile' : 'desktop';
mvc\store('theme',$theme);

mvc\get('/service/ajax/:action/:format/*', mvc\Action('ajaxHandler') );  
mvc\post('/service/datacollector/', mvc\Action('dataCollectorHandler') );  
mvc\get('/:module', mvc\Action('contentHandler') );  
mvc\get('/:module/', mvc\Action('contentHandler') );  
mvc\get('/:module/:view/*', mvc\Action('contentHandler') );  
mvc\get('/*', mvc\Action('contentHandler') );  

mvc\start();

?>
