<?php 
/* ============= WebBuilderConf.php =========== */

require('WebBuilderDBConf.php');

// Configuration of the WebBuilder
$WebBuilderConf = array(
  // General
  'SiteName' => 'WebBuilder',
  'DebugMode' => true,  // true or false
  'RefreshDico' => false,  // true or false
  'DeveloperEmail' => 'xxx',
  'DefaultLang' => 'en',
  'DefaultMode' => '0',
  'AvailableModes' => array('0', '1'),
  'TimeZone' => 'Asia/Tokyo',

  // Header
  'BaseURL' => 'http://your.website.net/WebBuilder/',
  'MetaDescription' => '',
  'MetaKeywords' => '',
  'MetaViewportWidth' => 'device-width',
  'IncludeCSS' => array('index.css'),
  'IncludeJS' => array('index.js'),

  // Database
  'DB_servername' => $DB_servername,
  'DB_username' => $DB_username,
  'DB_password' => $DB_password,
  'DB_dbname' => $DB_dbname,
  'DBBigBrother' => false, // true or false
  'DBModel' => array( 'tables' => array(
    'WBTableTest' => array(
      'Reference' => 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'DateCmd' => 'DATETIME',
      'Val' => 'CHAR(10) character set utf8 collate utf8_bin'
      )
    )
  )
);
  

?>
