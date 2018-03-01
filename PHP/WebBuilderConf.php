<?php 
/* ============= WebBuilderConf.php =========== */
require('WebBuilderDBConf.php');
// Configuration of the WebBuilder
$WebBuilderConf = array(
  // General
  'SiteName' => 'WebBuilder',
  'DebugMode' => false,  // true or false
  'RefreshDico' => false,  // true or false
  'DeveloperEmail' => 'Pascal@BayashiInJapan.net',
  'DefaultLang' => 'en',
  'DefaultMode' => '0',
  'AvailableModes' => array('0', '1'),
  'TimeZone' => 'Asia/Tokyo',
  'AccessStat' => true, // If true, creates the access table in DB when 
                        // setupDB and log access automatically
  'UserLogin' => true, // If true, creates the user login table in DB
                       // when setupDB
  // Header
  //'BaseURL' => 'http://localhost/WebBuilder/',
  'BaseURL' => 'http://www.bayashiinjapan.net/WebBuilder/',
  'MetaDescription' => 'Main page of the WebBuilder framework',
  'MetaKeywords' => 'WebBuilder, PHP, MySQL, JavaScript, framework',
  'MetaViewportWidth' => 'device-width',
  'IncludeCSS' => array('index.css', 'Animate/animate.css', 'stats.css'),
  'IncludeJS' => array('index.js', 'turnjs4/lib/turn.min.js', 'soundmanager2/soundmanager2.js', 'soundmanager2/inlineplayer.js'),
  // Database connection informations
  'DB_servername' => $DB_servername,
  'DB_username' => $DB_username,
  'DB_password' => $DB_password,
  'DB_dbname' => $DB_dbname,
  // BigBrother function
  'DBBigBrother' => false, // true or false
  // Database model
  'DBModel' => array( 'tables' => array(
    'WBTableTest' => array(
      'Reference' => 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'DateCmd' => 'DATE',
      'Ref' => 'INT',
      'Val' => 'CHAR(10) character set utf8 collate utf8_bin'
      ),
    'WBTableTest2' => array(
      'Reference' => 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'Val' => 'CHAR(10) character set utf8 collate utf8_bin'
      )
    )
  ),
  // Login table for user login
  'DBModelLogin' => array( 'tables' => array(
    'WBLogin' => array(
      'Reference' => 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'Login' => 'TEXT character set utf8 collate utf8_bin',
      'Hash' => 'TEXT character set utf8 collate utf8_bin'
      )
    )
  ),
  // Statistic table for access logger
  'DBModelStat' => array( 'tables' => array(
    'WBAccessTracker' => array(
      'Reference' => 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'DateTime' => 'DATETIME NOT NULL',
      'RefererIP' => 'TEXT character set utf8 collate utf8_bin',
      'HTTP_REFERER' => 
        'TEXT character set utf8 collate utf8_bin',
      'HTTP_USER_AGENT' => 
        'TEXT character set utf8 collate utf8_bin',
      'REQUEST_URI' => 
        'TEXT character set utf8 collate utf8_bin',
      'City' => 'TEXT character set utf8 collate utf8_bin',
      'Region' => 'TEXT character set utf8 collate utf8_bin',
      'Country' => 'TEXT character set utf8 collate utf8_bin',
      'LongLat' => 'TEXT character set utf8 collate utf8_bin',
      'Robot' => 'BOOL NOT NULL DEFAULT false'
      )
    )
  ),
  // Models for DB editor
  'DBEditor' => array(
    'WBTableTest' => array (
      'Reference' => 'Reference',
      'DateCmd' => 'Date',
      'Val' => 'Text',
      'Ref' => 'Select,WBTableTest2,Reference,Val'/*,
      'WBDBEditorFilter' => 'Reference > 1'*/
    ),
    'WBTableTest2' => array (
      'Reference' => 'Reference',
      'Val' => 'Text'
    )
  )
);
  
?>
