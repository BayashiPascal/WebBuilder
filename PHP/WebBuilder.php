 <?php 
/* ============= WebBuilder.php =========== */

// Include the configuration
require("PHP/WebBuilderConf.php");

// Configure the error handling
if ($WebBuilderConf["DebugMode"] == true) {
  ini_set('display_errors', 'On');
  error_reporting(E_ALL | E_STRICT | E_WARNING);
  set_error_handler('WebBuilderErrorHandler');
  set_exception_handler('WebBuilderExceptionHandler');
} else {
  ini_set('display_errors', 'Off');
  set_error_handler('WebBuilderErrorSilentHandler');
  set_exception_handler('WebBuilderExceptionSilentHandler');
}

// Include the local texts
require("PHP/WebBuilderGeoTxt.php");

// WBException class

class WBException extends Exception {
  public $_origin;
  public $_email;
  public $_msg;
  
  function __construct($origin, $email, $msg, 
    $code = 0, Exception $previous = null) {
    $this->_origin = $origin;
    $this->_email = $email;
    $this->_msg = $msg;
    parent::__construct($msg, $code);
  }
}

// WebBuilder class

class WebBuilder {
  private $_config;
  private $_dico;
  private $_lang;
  private $_mode;
  private $_DBconn;
  private $_JSdata;
  
  function __construct($conf, $dico) {
    $this->_config = $conf;
    $this->_dico = $dico;
    if (isset($_SESSION['WBLANG']) == false) {
      $country = $this->GetCountryFromIP();
      if ($country != '??') {
        $this->SetLang($country);
        if ($_SESSION['WBLANG'] == '') {
          $_SESSION['WBLANG'] = $conf['DefaultLang'];
        }
      } else {
        $_SESSION['WBLANG'] = $conf['DefaultLang'];
      }
    }
    $this->_lang = $_SESSION['WBLANG'];
    if (isset($_SESSION['WBMODE']) == false) {
      $_SESSION['WBMODE'] = $conf['DefaultMode'];
    }
    $this->_mode = $_SESSION['WBMODE'];
    $this->_DBconn = null;
    date_default_timezone_set($conf['TimeZone']);
    $this->_JSdata = '{}';
  }
  
  function __destruct() {

  }

  public function SetLang($lang) {
    $lang = strtolower($lang);
    // Convert from country code to language
    // http://www.nationsonline.org/oneworld/country_code_list.htm
    if ($lang == 'au') $lang = 'en';
    if ($lang == 'ca') $lang = 'en';
    if ($lang == 'us') $lang = 'en';
    if (isset($this->_dico[$lang]) == true) {
      $this->_lang = $lang;
      $_SESSION['WBLANG'] = $lang;
    }
  }
  
  public function GetLang() {
    return $this->_lang;
  }

  public function SetMode($v) {
    if (in_array($v, $this->_config['AvailableModes']) == true) {
      $this->_mode = $v;
      $_SESSION['WBMODE'] = $v;
    }
  }
   
  public function GetMode() {
    return $this->_mode;
  }

  public function SetJSData($var) {
    $this->_JSdata = json_encode($var);
  }

  public function GetJSData() {
    return $this->_JSdata;
  }

  public function BuildMeta() {
    $block = '';
    $block .= '<meta charset="UTF-8"/>';
    $block .= '<meta name="viewport"';
    $block .= ' content="width=';
    $block .= $this->_config["MetaViewportWidth"];
    $block .= ',initial-scale=1">';
    $block .= '<meta name="description" content="';
    $block .= $this->_config["MetaDescription"];
    $block .= '"/>';
    $block .= '<meta name="keywords" content="';
    $block .= $this->_config["MetaKeywords"];
    $block .= '"/>';
    return $block;
  }

  public function BuildIcon() {
    $block = '';
    // For IE 9 and below. ICO should be 32x32 pixels in size
    $block .= '<!--[if IE]><link rel="shortcut icon"';
    $block .= ' href="Img/icon.32x32.ico"><![endif]-->';
    // Touch Icons - iOS and Android 2.1+ 180x180 pixels in size.
    $block .= '<link rel="apple-touch-icon-precomposed"';
    $block .= ' href="Img/icon.180x180.png">';
    // Firefox, Chrome, Safari, IE 11+ and Opera. 196x196 pixels in size.
    $block .= '<link rel="icon" href="Img/icon.196x196.png">';
    return $block;
  }

  public function BuildCSS() {
    $block = '';
    $cssfiles = array_merge(array('WebBuilder.css'),
      $this->_config['IncludeCSS']);
    foreach ($cssfiles as $key => $val) {
      $block .= '<link href="CSS/' . $val .'" rel="stylesheet">';
    }
    return $block;
  }

  public function BuildJS() {
    $block = '';
    $jsfiles = array_merge(array('jquery.min.js', 'WebBuilder.js'),
      $this->_config['IncludeJS']);
    foreach ($jsfiles as $key => $val) {
      $block .= '<script charset="UTF-8"';
      $block .= ' src="JS/' . $val . '"></script>';
    }
    return $block;
  }

  public function BuildTitle() {
    $block = '';
    $block .= '<title>';
    $block .= $this->_config["SiteName"];
    $block .= '</title>';
    return $block;
  }

  public function BuildBase() {
    $block = '';
    $block .= '<base href="';
    $block .= $this->_config["BaseURL"];
    $block .= '" />';
    return $block;
  }

  public function GetTxt($txt, $param = array()) {
    $block = '';
    if (isset($this->_dico[$this->_lang][$txt]) == false) {
      $defaultLang = $this->_config['DefaultLang'];
      if (isset($this->_dico[$defaultLang][$txt]) == false) {
        $block .= $txt;
      } else {
        $block .= $this->_dico[$defaultLang][$txt];
      }
    } else {
      $block .= $this->_dico[$this->_lang][$txt];
    }
    if ($param != "") {
      if (is_array($param) == true) {
        $index = 0;
        foreach ($param as $k => $v) {
          $block = str_replace('$' . $index . '$', $v, $block);
          $index = $index + 1;
        }
      } else {
        $block = str_replace('$0$', $param, $block);
      }
    }
    return $block;
  }
  
  public function BuildDivTitle($id, $content) {
    $block = '';
    $block .= '<div id="' . $id . '" class="divWBTitle">';
    $block .= $content;
    $block .= '</div>';
    return $block;
  }

  public function BuildDivTile($id, $content) {
    $block = '';
    $block .= '<div id="' . $id . '" class="divWBTile">';
    $block .= $content;
    $block .= '</div>';
    return $block;
  }

  public function BuildDivFooter($id, $content) {
    $block = '';
    $block .= '<div id="' . $id . '" class="divWBFooter">';
    $block .= $content;
    $block .= '</div>';
    return $block;
  }

  public function ProcessURLArg() {
    if (isset($_GET["la"]) == true) {
      $this->SetLang($_GET["la"]);
    }
    if (isset($_GET["mo"]) == true) {
      $this->SetMode($_GET["mo"]);
    }
    if (isset($_GET["setupdb"]) == true) {
      $this->CreateDB();
    }
  }
  
  // Return the result of the execution of $cmd
  public function ExecCmdOnServer($cmd) {
    unset($output);
    unset($returnVal);
    $data = array();
    exec($cmd, $output, $returnVal); 
    $data["returnVal"] = $returnVal;
    $data["output"] = $output;
    return $data;
  }

  // Return the JSON encoded result of the execution of $cmd
  public function ExecCmdOnServerJSON($cmd) {
    return json_encode(ExecCmdOnServer($cmd));
  }

  // Get Info from IP adress, returned as JSON
  public function GetIPInfo() {
    $client  = '';
    $forward = '';
    $remote  = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']) == true) {
      $client  = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) == true) {
      $forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (isset($_SERVER['REMOTE_ADDR']) == true) {
      $remote  = $_SERVER['REMOTE_ADDR'];
    }
    if (filter_var($client, FILTER_VALIDATE_IP)) {
      $ip = $client;
    } else if (filter_var($forward, FILTER_VALIDATE_IP)) {
      $ip = $forward;
    } else {
      $ip = $remote;
    }
    $ip_data = file_get_contents(
      "http://www.geoplugin.net/json.gp?ip=" . $ip);
    if ($ip_data === FALSE) {
      $ip_data = '{';
      $ip_data .= '"geoplugin_request":"' . $ip . '",';
      $ip_data .= '"geoplugin_status":400,';
      $ip_data .= '"geoplugin_city":"Unknown",';
      $ip_data .= '"geoplugin_region":"Unknown",';
      $ip_data .= '"geoplugin_countryCode":"??",';
      $ip_data .= '"geoplugin_countryName":"Unknown",';
      $ip_data .= '"geoplugin_continentCode":"??",';
      $ip_data .= '"geoplugin_latitude":"0.0",';
      $ip_data .= '"geoplugin_longitude":"0.0"';
      $ip_data .= '}';
    }
    return $ip_data;
  }

  // Get the country code (ex. 'FR') from the IP
  public function GetCountryFromIP() {
    $ip_data = json_decode($this->GetIPInfo()); 
    return $ip_data->geoplugin_countryCode;
  }

  // Connect to the database
  private function ConnectDB() {
    if ($this->_DBconn == null) {
      $this->_DBconn = new mysqli(
        $this->_config['DB_servername'], 
        $this->_config['DB_username'], 
        $this->_config['DB_password'],
        $this->_config['DB_dbname']);
      $this->_DBconn->set_charset("utf8");
    }
  }
  
  // Close the connection to the database
  private function CloseDB() {
    $this->_DBconn->close();
    $this->_DBconn = null;
  }

  // Check if a table exists in the DB
  private function IsTableInDB($table) {
    $sql = 'SHOW TABLES LIKE "' . $table . '"';
    $this->ConnectDB();
    $res = $this->_DBconn->query($sql);
    if ($res === false || $res->num_rows == 0) {
      $ret = false;
    } else {
      $ret = true;
    }
    $this->CloseDB();
    return $ret;
  }

  // Check if a field exists in a table of the DB
  private function IsFieldInTable($table, $field) {
    $sql = 'SHOW COLUMNS FROM ' . $table . ' like "' . $field . '" ';
    $this->ConnectDB();
    $res = $this->_DBconn->query($sql);
    if ($res === false || $res->num_rows == 0) {
      $ret = false;
    } else {
      $ret = true;
    }
    $this->CloseDB();
    return $ret;
  }

  // Auto create the DB
  private function CreateDB() {
    foreach ($this->_config['DBModel']['tables'] as 
      $table => $tableDef) {
      if ($this->IsTableInDB($table) == false) {
        $flagCreateTable = true;
      } else {
        $flagCreateTable = false;
      }
      foreach ($tableDef as $field => $fieldDef) {
        if ($flagCreateTable == true) {
          $sql = 'CREATE TABLE ' . $table . ' (';
          $sql .= $field . ' ' . $fieldDef;
          $sql .= ')';
          $this->ConnectDB();
          $this->_DBconn->query($sql);
          $this->CloseDB();
          $flagCreateTable = false;
        } else {
          if ($this->IsFieldInTable($table, $field) == false) {
            $sql = 'ALTER TABLE ' . $table;
            $sql .= ' ADD COLUMN ' . $field . ' ' . $fieldDef;
            $this->ConnectDB();
            $this->_DBconn->query($sql);
            $this->CloseDB();
          }
        }
      }
    }
  }

  // Execute a delete SQL command
  public function ExecDeleteSQL($table, $cond, $types, $vals) {
    $sql = '';
    $sql .= 'DELETE FROM ' . $table . ' ';
    $sql .= ' WHERE ' . $cond;
    $this->ConnectDB();
    $stmt = $this->_DBconn->stmt_init();
    if (!$stmt->prepare($sql)) {
      throw new WBException($this->_config['SiteName'],
        $this->_config['DeveloperEmail'],
        'Prepare statement failed. ' . $stmt->error . 
        ' for SQL command "' . $sql . '"');
    }
    $argBind = array();
    $argBind[] = $stmt;
    $argBind[] = $types;
    $argBind = array_merge($argBind, $vals);
    call_user_func_array('mysqli_stmt_bind_param', $argBind);
    if (!$stmt->execute()) {
      throw new WBException($this->_config['SiteName'],
        $this->_config['DeveloperEmail'],
        'Execute statement failed. ' . $stmt->error . 
        ' for SQL command "' . $sql . '"');
    }
    $stmt->close();
    $this->CloseDB();
  }
  
  // Execute a update SQL command
  public function ExecUpdateSQL($table, $cols, $types, $vals, 
    $cond = '') {
    $sql = '';
    $sql .= 'UPDATE ' . $table . ' SET';
    $arr = array();
    foreach ($cols as $col) {
      $arr[] = ' ' . $col . ' = ?';
    }
    $sql .= implode(',', $arr);
    if ($cond != '') {
      $sql .= ' WHERE ' . $cond;
    }
    $this->ConnectDB();
    $stmt = $this->_DBconn->stmt_init();
    if (!$stmt->prepare($sql)) {
      throw new WBException($this->_config['SiteName'],
        $this->_config['DeveloperEmail'],
        'Prepare statement failed. ' . $stmt->error . 
        ' for SQL command "' . $sql . '"');
    }
    $argBind = array();
    $argBind[] = $stmt;
    $argBind[] = $types;
    $argBind = array_merge($argBind, $vals);
    call_user_func_array('mysqli_stmt_bind_param', $argBind);
    if (!$stmt->execute()) {
      throw new WBException($this->_config['SiteName'],
        $this->_config['DeveloperEmail'],
        'Execute statement failed. ' . $stmt->error . 
        ' for SQL command "' . $sql . '"');
    }
    $stmt->close();
    $this->CloseDB();
  }
  
  // Execute a insert SQL command
  public function ExecInsertSQL($table, $cols, $types, $vals) {
    $sql = '';
    $sql .= 'INSERT INTO ' . $table;
    $sql .= ' (' . implode(',', $cols) . ')';
    $sql .= ' VALUES (';
    $arr = array();
    foreach ($cols as $col) {
      $arr[] = '?';
    }
    $sql .= implode(',', $arr);
    $sql .= ')';
    $this->ConnectDB();
    $stmt = $this->_DBconn->stmt_init();
    if (!$stmt->prepare($sql)) {
      throw new WBException($this->_config['SiteName'],
        $this->_config['DeveloperEmail'],
        'Prepare statement failed. ' . $stmt->error . 
        ' for SQL command "' . $sql . '"');
    }
    $argBind = array();
    $argBind[] = $stmt;
    $argBind[] = $types;
    $argBind = array_merge($argBind, $vals);
    call_user_func_array('mysqli_stmt_bind_param', $argBind);
    if (!$stmt->execute()) {
      throw new WBException($this->_config['SiteName'],
        $this->_config['DeveloperEmail'],
        'Execute statement failed. ' . $stmt->error . 
        ' for SQL command "' . $sql . '"');
    }
    $stmt->close();
    $this->CloseDB();
  }
  
  // Execute a select SQL command
  public function ExecSelectSQL($sql, $cols, 
    $types = '', $params = array()) {
    if (strpos($sql, 'SELECT') === 0 || 
      strpos($sql, 'select') === 0) {
      if (is_array($params) == true && is_array($cols) == true && 
        sizeof($params) == strlen($types)) {
        $this->ConnectDB();
        $stmt = $this->_DBconn->stmt_init();
        if (!$stmt->prepare($sql)) {
          throw new WBException($this->_config['SiteName'],
            $this->_config['DeveloperEmail'],
            'Prepare statement failed. ' . $stmt->error . 
            ' for SQL command "' . $sql . '"');
        }
        if (sizeof($params) != 0) {
          $argBind = array();
          $argBind[] = $stmt;
          $argBind[] = $types;
          $argBind = array_merge($argBind, $params);
          call_user_func_array('mysqli_stmt_bind_param', $argBind);
        }
        if (!$stmt->execute()) {
          throw new WBException($this->_config['SiteName'],
            $this->_config['DeveloperEmail'],
            'Execute statement failed. ' . $stmt->error . 
            ' for SQL command "' . $sql . '"');
        }
        $rows = array();
        $argBind = array();
        $argBind[] = $stmt;
        foreach ($cols as $col) {
          $rows[$col] = '';
          $argBind[] = &$rows[$col];
        }
        call_user_func_array('mysqli_stmt_bind_result', $argBind);
        $ret = array();
        while ($stmt->fetch()) {
          $narr = array();
          foreach ($rows as $k => $v) { 
            $narr[$k] = $v ; 
          }
          $ret[] = $narr;
        }
        $stmt->close();
        $this->CloseDB();
        return $ret;
      } else {
        $this->CloseDB();
        throw new WBException($this->_config['SiteName'],
          $this->_config['DeveloperEmail'],
          'WebBuilder.ExecSelectSQL: invalid bind paramaters');
      }
    } else {
      $this->CloseDB();
      throw new WBException($this->_config['SiteName'],
        $this->_config['DeveloperEmail'],
        'WebBuilder.ExecSelectSQL: invalid SQL command: ' . $sql);
    }
  }
  
  public function BuildImgUploader($id, $msg, $formats, 
    $sizeMax, $callback) {
    $block = '';
    $block .= '<form id="formImgUpload' . $id . '" method="post" 
      enctype="multipart/form-data">';
    $block .= '<input type="file" id="inpImgUpload' . $id . '" 
      name="inpUploadImg" style="display:none;"  onchange="theWB.UploadImgInpChange(\'' . $id . '\', \'' . implode(',', $formats) . '\', \'' . $sizeMax . '\', ' . $callback . ');">';
    $block .= '<input type="button" id="btnImgUpload' . $id . '" 
      value="' . $msg . '" onclick="theWB.UploadImgBtnClick(\'' . $id . '\');">';
    $block .= '</form>';
    return $block;
  }

  public function LogAccess() {
    if (isset($_SESSION['WBACCESSTRACKREF']) === false) {
      $datetime = date('Y-m-d H:i:s');
      $ip_data = GetIPInfo();
      if (isset($_SERVER['HTTP_ORIGIN'])) {
        $referer = $_SERVER['HTTP_ORIGIN'];
      }
      else if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
      } else {
        $referer = "";
      }
      $uri = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      $sql = "INSERT INTO WebData.WBAccessTracker 
        (DateTime, RefererIP, HTTP_REFERER, 
        HTTP_USER_AGENT, REQUEST_URI) VALUES ('" . 
        $datetime . "', ?, ?, ?, ?);";
      $this->ConnectDB();
      $stmt = $this->_DBconn->stmt_init();
      if (!$stmt->prepare($sql)) {
        throw new Exception(
          "Prepare statement failed for log access. " .
          $stmt->error);
      }
      if (!$stmt->bind_param('ssss', $ip_data, 
        $referer, $_SERVER['HTTP_USER_AGENT'], $uri)) {
        throw new Exception(
          "Bind statement failed for log access. " .
          $stmt->error);
      }
      if (!$stmt->execute()) {
        throw new Exception(
          "Execute statement failed for log access. " .
          $stmt->error);
      }
      $_SESSION['WBACCESSTRACKREF'] = $conn->insert_id;
      $stmt->close();
      $this->CloseDB();
    }
  }
};

// Create the global instance of the WebBuilder
$theWB = new WebBuilder($WebBuilderConf, $WBDico);

// Error handlers
function WebBuilderErrorHandler($errno, $msg, $file, $line, $ctxt) {
  $block = '';
  // The error can occur before CSS is available, then hard code
  // style here
  $block .= '<div style="';
  $block .= 'background-color:white;position:absolute;top:0;left:0;';
  $block .= 'width:100%;">';
  $block .= '<div style="';
  $block .= 'margin:auto;width: 80%;color:red;font-size:18px;';
  $block .= '">';
  $block .= 'Error occured in file ' . $file;
  $block .= ' on line ' . $line . '<br>';
  $block .= $msg . '<br>';
  $block .= 'errno: ' . $errno . '<br>';
  $block .= 'date: ' . date("Y-m-d H:i:s") . '<br>';
  $block .= 'Context:<br>';
  $block .= WBstringifyHTML($ctxt);
  $block .= '</div>';
  $block .= '</div>';
  echo $block;
  exit();
}

function WebBuilderErrorSilentHandler(
  $errno, $msg, $file, $line, $ctxt) {

  $email = $ctxt['WebBuilderConf']['DeveloperEmail'];
  
  $block = '';
  $block .= 'Error occured in file ' . $file;
  $block .= ' on line ' . $line . "\r\n";
  $block .= $msg . "\r\n";
  $block .= 'errno: ' . $errno . "\r\n";
  $block .= 'date: ' . date("Y-m-d H:i:s") . "\r\n";
  $block .= 'Context:' . "\r\n";
  $block .= WBstringifyTXT($ctxt);

  $headers = 'From: ' . $email . "\r\n";
  $headers .= 'Reply-To: ' . $email;
  $headers .= "\r\n";
  $headers .= 'Content-Type: text/plain;charset=UTF-8';

  $subject = 'Automatic error message from ';
  $subject .= $ctxt['WebBuilderConf']['SiteName'];

  mail($email, $subject, $block, $headers);

  $txt = '';
  $txt .= '<div style="';
  $txt .= 'background-color:white;position:absolute;top:0;left:0;';
  $txt .= 'width:100%;">';
  $txt .= '<div style="';
  $txt .= 'margin:auto;width: 80%;color:red;font-size:18px;';
  $txt .= '">';
  $txt .= $ctxt['WebBuilderConf']['SiteName'];
  $txt .= ': sorry, an error occured.';
  $txt .= ' Please contact the developper (' . $email . ').<br><br>';
  $txt .= '</div>';
  $txt .= '</div>';
  exit($txt);
}

function WBstringifyHTML($var, $shift = 0) {
  $block ='';
  if (is_array($var) || is_object($var)) {
    if (sizeof($var) != 0) {
      $block .= '[<br>';
      $shift = $shift + 2;
      foreach ($var as $key => $value) {
        if (strcmp($key, 'DB_password') != 0) {
          $block .= str_repeat('&nbsp;', $shift) . $key . " => ";
          if (is_array($value) || is_object($value)) {
            $block .= WBstringifyHTML($value, $shift) . "<br>";
          } else {
            $block .= $value . "<br>";
          }
        }
      }
      $block .= str_repeat('&nbsp;', $shift - 2) . ']';
    } else {
      $block .= '[]';
    }
  } else {
    $block .= "'" . $var . "'";
  }
  return $block;
}

function WBstringifyTXT($var, $shift = 0) {
  $block ='';
  if (is_array($var) || is_object($var)) {
    if (sizeof($var) != 0) {
      $block .= 'array(' . "\r\n";
      $shift = $shift + 2;
      foreach ($var as $key => $value) {
        if ($key != 'DB_password') {
          $block .= str_repeat(' ', $shift) . $key . " => ";
          if (is_array($value) || is_object($value)) {
            $block .= WBstringifyTXT($value, $shift) . "\r\n";
          } else {
            $block .= $value . "\r\n";
          }
        }
      }
      $block .= str_repeat(' ', $shift - 2) . ')';
    } else {
      $block .= 'array()';
    }
  } else {
    $block .= "'" . $var . "'";
  }
  return $block;
}

// Exception handlers
function WebBuilderExceptionHandler($e) {
  $block = '';
  // The exception can occur before CSS is available, then hard code
  // style here
  $block .= '<div style="';
  $block .= 'background-color:white;position:absolute;top:0;left:0;';
  $block .= 'width:100%;">';
  $block .= '<div style="';
  $block .= 'margin:auto;width: 80%;color:red;font-size:18px;';
  $block .= '">';
  $block .= 'Exception raised in file ' . $e->getFile();
  $block .= ' on line ' . $e->getLine() . '<br>';
  $block .= 'date: ' . date('Y-m-d H:i:s') . '<br>';
  $block .= $e->getMessage() . '<br>';
  $block .= $e->getTraceAsString();
  $block .= '</div>';
  $block .= '</div>';
  echo $block;
  exit();
}

function WebBuilderExceptionSilentHandler($e) {
  $txt = '';
  $txt .= '<div style="';
  $txt .= 'background-color:white;position:absolute;top:0;left:0;';
  $txt .= 'width:100%;">';
  $txt .= '<div style="';
  $txt .= 'margin:auto;width: 80%;color:red;font-size:18px;';
  $txt .= '">';
  $txt .= 'Sorry, a problem occured.';
  if ($e instanceof WBException) {
    $block = '';
    $block .= 'Exception raised in file ' . $e->getFile();
    $block .= ' on line ' . $e->getLine() . "\r\n";
    $block .= 'date: ' . date("Y-m-d H:i:s") . "\r\n";
    $block .= $e->getMessage() . "\r\n";
    $block .= $e->getTraceAsString();
    $email = $e->_email;
    $headers = 'From: ' . $email . "\r\n";
    $headers .= 'Reply-To: ' . $email;
    $headers .= "\r\n";
    $headers .= 'Content-Type: text/plain;charset=UTF-8';
    $subject = 'Automatic exception message from ';
    $subject .= $e->_origin;
    mail($email, $subject, $block, $headers);
    $txt .= ' An automatic report has been sent to the developper.';
    $txt .= ' Please contact him (' . $email . ').<br><br>';
  } else {
    $txt .= ' The developper could not be automatically informed.';
    $txt .= ' Please contact him with following information.<br><br>';
    $txt .= 'Exception raised in file ' . $e->getFile();
    $txt .= ' on line ' . $e->getLine() . "\r\n";
    $txt .= 'date: ' . date('Y-m-d H:i:s') . "\r\n";
    $txt .= $e->getMessage() . "\r\n";
    $txt .= $e->getTraceAsString();
  }
  $txt .= '</div>';
  $txt .= '</div>';
  exit($txt);
}



?>
