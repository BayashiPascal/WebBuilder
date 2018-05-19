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
    //parent::__construct($msg, $code, $previous);
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
  private $_DBEditors;
  
  function __construct($conf, $dico) {
    $this->_config = $conf;
    $this->_dico = $dico;
    if (isset($_SESSION['WBLANG']) == false) {
      $this->SetDefaultLang($conf['DefaultLang']);
    }
    $this->_lang = $_SESSION['WBLANG'];
    if (isset($_SESSION['WBMODE']) == false) {
      $_SESSION['WBMODE'] = $conf['DefaultMode'];
    }
    $this->_mode = $_SESSION['WBMODE'];
    $this->_DBconn = null;
    date_default_timezone_set($conf['TimeZone']);
    $this->_JSdata = '{}';
    $this->_DBEditors = array();
  }
  
  function __destruct() {

  }

  private function SetDefaultLang($def) {
    // Get the user location from his IP address
    // and set the default language to this country
    $userCountry = $this->GetCountryFromIP();
    if (strtolower($userCountry) == 'jp') {
      $_SESSION['WBLANG'] = 'jp';
    } else if (strtolower($userCountry) == 'gb' ||
      strtolower($userCountry) == 'us' ||
      strtolower($userCountry) == 'au' ||
      strtolower($userCountry) == 'ca' ||
      strtolower($userCountry) == 'ie' ||
      strtolower($userCountry) == 'nz' ||
      strtolower($userCountry) == 'ag' ||
      strtolower($userCountry) == 'bs' ||
      strtolower($userCountry) == 'bb' ||
      strtolower($userCountry) == 'bz' ||
      strtolower($userCountry) == 'dm' ||
      strtolower($userCountry) == 'gd' ||
      strtolower($userCountry) == 'gy' ||
      strtolower($userCountry) == 'jm' ||
      strtolower($userCountry) == 'kn' ||
      strtolower($userCountry) == 'lc' ||
      strtolower($userCountry) == 'vc' ||
      strtolower($userCountry) == 'tt') {
      $_SESSION['WBLANG'] = 'en';
    } else if (strtolower($userCountry) == 'cn' ||
      strtolower($userCountry) == 'tw' ||
      strtolower($userCountry) == 'hk' ||
      strtolower($userCountry) == 'mo' ||
      strtolower($userCountry) == 'sg') {
      $_SESSION['WBLANG'] = 'cn';
    } else if (strtolower($userCountry) == 'fr' ||
      strtolower($userCountry) == 'be' ||
      strtolower($userCountry) == 'ch' ||
      strtolower($userCountry) == 'bj' ||
      strtolower($userCountry) == 'bf' ||
      strtolower($userCountry) == 'bi' ||
      strtolower($userCountry) == 'cm' ||
      strtolower($userCountry) == 'td' ||
      strtolower($userCountry) == 'km' ||
      strtolower($userCountry) == 'cd' ||
      strtolower($userCountry) == 'dj' ||
      strtolower($userCountry) == 'ga' ||
      strtolower($userCountry) == 'gn' ||
      strtolower($userCountry) == 'ht' ||
      strtolower($userCountry) == 'ci' ||
      strtolower($userCountry) == 'lu' ||
      strtolower($userCountry) == 'mg' ||
      strtolower($userCountry) == 'ml' ||
      strtolower($userCountry) == 'mc' ||
      strtolower($userCountry) == 'ne' ||
      strtolower($userCountry) == 'rw' ||
      strtolower($userCountry) == 'sn' ||
      strtolower($userCountry) == 'sc' ||
      strtolower($userCountry) == 'tg' ||
      strtolower($userCountry) == 'vu') {
      $_SESSION['WBLANG'] = 'fr';
    } else if (strtolower($userCountry) == 'kr') {
      $_SESSION['WBLANG'] = 'kr';
    } else if (strtolower($userCountry) == 'es' ||
      strtolower($userCountry) == 'ar' ||
      strtolower($userCountry) == 'bo' ||
      strtolower($userCountry) == 'cl' ||
      strtolower($userCountry) == 'co' ||
      strtolower($userCountry) == 'cr' ||
      strtolower($userCountry) == 'cu' ||
      strtolower($userCountry) == 'do' ||
      strtolower($userCountry) == 'ec' ||
      strtolower($userCountry) == 'sv' ||
      strtolower($userCountry) == 'gq' ||
      strtolower($userCountry) == 'gt' ||
      strtolower($userCountry) == 'hn' ||
      strtolower($userCountry) == 'mx' ||
      strtolower($userCountry) == 'ni' ||
      strtolower($userCountry) == 'pa' ||
      strtolower($userCountry) == 'py' ||
      strtolower($userCountry) == 'pe' ||
      strtolower($userCountry) == 'uy' ||
      strtolower($userCountry) == 've') {
      $_SESSION['WBLANG'] = 'sp';
    } else {
      $_SESSION['WBLANG'] = $def;
    }
  }

  public function SetLang($v) {
    if (isset($this->_dico[$v]) == true) {
      $this->_lang = $v;
      $_SESSION['WBLANG'] = $v;
    }
  }
  
  public function GetLang() {
    return $this->_lang;
  }

  public function GetForms() {
    return $this->_config["DBEditor"];
  }
  
  public function IsLogged() {
    if (isset($_SESSION["WBCURRENTUSER"]) &&
      $_SESSION["WBCURRENTUSER"] != "")
      return true;
    else
      return false;
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
    $ret = '{"PHPdata":' . $this->_JSdata .', "DBEditors":';
    $safeJson = str_replace("\u0022","\\\\\"",
      json_encode($this->_DBEditors, JSON_HEX_QUOT)); 
    $ret .= $safeJson;
    $ret .= ', "DBEditorModels":';
    $safeJson = str_replace("\u0022","\\\\\"",
      json_encode($this->_config["DBEditor"], JSON_HEX_QUOT)); 
    $ret .= $safeJson;
    $ret .= '}';
    $ret = str_replace("'", "\'", $ret);
    return $ret;
  }

  public function GetConfig($key) {
    return $this->_config[$key];
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

  public function BuildDivLogger($submitTo) {
    $block = '';
    $block .= '<form method="post" action ="' . $submitTo . '">';
    $block .= '<div class="divWBLogin">';
    $block .= '<div class="divWBLoginLabel">';
    $block .= 'Login :';
    $block .= '<br>';
    $block .= 'Password :';
    $block .= '</div>';
    $block .= '<div class="divWBLoginInput">';
    $block .= '<input type="text" name="WBlogin" id="WBlogin">';
    $block .= '<br>';
    $block .= 
      '<input type="password" name="WBpasswd" id="WBpasswd">';
    $block .= '</div>';
    $block .= '<br><br>';
    $block .= '<input type="submit" value="Submit">';
    $block .= '</div>';
    $block .= '</form>';
    return $this->BuildDivTile("divWBLogger", $block);
  }

  public function DBEditorGetData($formName) {
    // Ensure the form exists (i.e. is defined in WebBuilderConf.php
    $forms = &$this->_config["DBEditor"];
    if (!isset($forms[$formName]))
      throw new WBException($this->_config['SiteName'],
        $this->_config['DeveloperEmail'],
        'Called BuildDivDBTableEditor for unknown form "' .
        $formName . '"');
    // Ensure the form has a 'Reference' field
    if (!isset($forms[$formName]["Reference"]))
      throw new WBException($this->_config['SiteName'],
        $this->_config['DeveloperEmail'],
        'The form "' . $formName . '" has no "Reference" field.');
    // Create the SQL command
    $cols = array();
    $sql = "SELECT ";
    $flag = false;
    foreach ($forms[$formName] as $field => $val) {
      if ($field != "WBDBEditorFilter") {
        $cols[] = $field;
        if ($flag === true)
          $sql .= ",";
        else
          $flag = true;
        $sql .= $field;
      }
    }
    $sql .= " FROM " . $formName;
    if (isset($forms[$formName]["WBDBEditorFilter"]) === true &&
      $forms[$formName]["WBDBEditorFilter"] != "")
      $sql .= " WHERE " . $forms[$formName]["WBDBEditorFilter"];
    // Return the result of the SQL command
    return $this->ExecSelectSQL($sql, $cols);
  }

  public function BuildDivDBEditor($formName, $title) {
    // Get the data
    $this->_DBEditors[$formName] = $this->DBEditorGetData($formName);
    // Create the DOM to manipulate the data
    $block = '';
    $block .= '<div class="divWBDBEditor">';
    // Title
    if ($title != "") {
      $block .= '<div class="divWBDBEditorTitle">' . $title . '</div>';
    }
    // Control buttons
    $block .= '<div class="divWBDBEditorCtrl">';
    $block .= 
      '<img src="./Img/Icons/refactor.png" alt="load"';
    $block .= ' onclick="theWB.DBEditorReload(\'' . 
      $formName . '\');">';
    $block .= 
      '<img src="./Img/Icons/recordIconFullBackward.png" alt="first"';
    $block .= ' onclick="theWB.DBEditorFirst(\'' . 
      $formName . '\');">';
    $block .= 
      '<img src="./Img/Icons/recordIconBackward.png" alt="previous"';
    $block .= ' onclick="theWB.DBEditorPrevious(\'' . 
      $formName . '\');">';
    $block .= 
      '<img src="./Img/Icons/recordIconForward.png" alt="next"';
    $block .= ' onclick="theWB.DBEditorNext(\'' . 
      $formName . '\');">';
    $block .= 
      '<img src="./Img/Icons/recordIconFullForward.png" alt="last"';
    $block .= ' onclick="theWB.DBEditorLast(\'' . 
      $formName . '\');">';
    $block .= 
      '<img id="imgWBDBEditorDelete' . $formName . 
          '" src="./Img/Icons/recordIconMinusOff.png" alt="delete"';
    $block .= ' onclick="theWB.DBEditorDelete(\'' . 
      $formName . '\');">';
    $block .= 
      '<img src="./Img/Icons/recordIconPlus.png" alt="add"';
    $block .= ' onclick="theWB.DBEditorAdd(\'' . 
      $formName . '\');">';
    $block .= 
      '<img style="display:none;" id="imgWBDBEditorSave' . 
            $formName . '" src="./Img/Icons/saveIcon.png" alt="save"';
    $block .= ' onclick="theWB.DBEditorSave(\'' . 
      $formName . '\');">';
    $block .= '</div>';
    $block .= '<div class="divWBDBEditorPosition">';
    $block .= '<span id="spanDBEditorCurPos' . 
      $formName . '"></span>/<span id="spanDBEditorLastPos' . 
      $formName . '"></span>';
    $block .= '</div>';
    // Displayed data
    $block .= '<div id="divWBDBEditorDataDeleted' . $formName . 
      '" style="display:none;" class="divWBDBEditorData">';
    $block .= 'This record has been deleted.</div>';
    $block .= '<div id="divWBDBEditorData' . $formName . 
      '" class="divWBDBEditorData">';
    // For each field
    $forms = &$this->_config["DBEditor"];
    foreach ($forms[$formName] as $field => $val) {
      if ($field != "WBDBEditorFilter") {
        $block .= '<div class="divWBDBEditorField">';
        $block .= '<div class="divWBDBEditorFieldLbl">';
        $block .= $field;
        $block .= '</div>';
        $block .= '<div id="divWBDBEditorFieldData' . $formName . 
        '" class="divWBDBEditorFieldData">';
        // If it's a reference, display it but enable the user to modify
        if ($field == "Reference")
          $block .= '<input type="text" id="inpWBDBEditorField' . 
            $formName . $field . '" readonly>';
        // If it's a date
        else if ($val == "Date")
          $block .= '<input type="date" id="inpWBDBEditorField' . 
            $formName . $field . '">';
        // If it's a link toward another table
        else if (strpos($val, "Select,") === 0) {
          // Get the target table and fields
          $args = explode(",", $val);
          // Get the values in the target table
          $sqlSelect = "SELECT " . $args[2] . "," . $args[3] . 
            " FROM " . $args[1];
          $colsSelect = array($args[2], $args[3]);
          $opts = $this->ExecSelectSQL($sqlSelect, $colsSelect);
          // Create the select box
          $block .= '<select id="inpWBDBEditorField' . 
             $formName . $field . '">';
          foreach ($opts as $index => $opt)
            $block .= '<option value="' . $opt[$args[2]] . '">' . 
              $opt[$args[3]] . '</option>';
          $block .= '</select>';
        // Standard input text for any other field type
        } else
          $block .= '<input type="text" id="inpWBDBEditorField' . 
            $formName . $field . '">';
        $block .= '</div>';
        $block .= '</div>';
      }
    }
    $block .= '</div>';
    $block .= '</div>';
    return $this->BuildDivTile("divWBDBEditor" . $formName, $block);
  }

  public function BuildDivFooter($id, $content) {
    $block = '';
    $block .= '<div id="' . $id . '" class="divWBFooter">';
    $block .= $content;
    if ($this->IsLogged() == true) {
      $block .= ' - <a href = "?WBlogout=1">Logout</a>';
    }
    $block .= '</div>';
    return $block;
  }

  public function ProcessURLArg() {
    if (isset($_GET["WBlogout"]) === true) {
      $_SESSION["WBCURRENTUSER"] = "";
    }
    if (isset($_GET["la"]) === true) {
      $this->SetLang($_GET["la"]);
    }
    if (isset($_GET["mo"]) === true) {
      $this->SetMode($_GET["mo"]);
    }
    if (isset($_GET["setupdb"]) === true) {
      $this->CreateDB('DBModel');
      if ($this->_config['AccessStat'] === true) {
        $this->CreateDB('DBModelStat');
      }
      if ($this->_config['UserLogin'] === true) {
        $this->CreateDB('DBModelLogin');
      }
    }
    // Log access
    if ($this->_config['AccessStat'] === true) {
      $this->LogAccess();
    }
  }
  
  public function ProcessPOSTValues() {
    // If a login has been submitted
    if (isset($_POST["WBlogin"]) === true &&
      $_POST["WBlogin"] != '' &&
      isset($_POST["WBpasswd"]) === true && 
      $_POST["WBpasswd"] != '') {
      // Check the password
      if ($this->CheckUserLogin($_POST["WBlogin"], 
        $_POST["WBpasswd"]) === true) {
        // Memorize the current user for later use
        $_SESSION["WBCURRENTUSER"] = $_POST["WBlogin"];
      } else {
        // Reset the current user
        $_SESSION["WBCURRENTUSER"] = "";
      }
      // Delete the password for security
      $_POST["WBpasswd"] = '';
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
    try {
      $ip_data = file_get_contents(
        "http://www.geoplugin.net/json.gp?ip=" . $ip);
    } catch (Exception $e) {
      $ip_data = FALSE;
    }
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
        if ($this->_DBconn->connect_errno &&
          $WebBuilderConf["DebugMode"] == true) {
          echo "Failed to connect to MySQL: (" . 
            $this->_DBconn->connect_errno . ") " . 
            $this->_DBconn->connect_error;
        }
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
  private function CreateDB($model) {
    foreach ($this->_config[$model]['tables'] as 
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
    // PHP5.2:
    //$argBind = array_merge($argBind, $vals);
    // PHP7.0:
    foreach($vals as $key => $value)
      $argBind[] = &$vals[$key];
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
    // PHP5.2:
    //$argBind = array_merge($argBind, $vals);
    // PHP7.0:
    foreach($vals as $key => $value)
      $argBind[] = &$vals[$key];
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
    // PHP5.2:
    //$argBind = array_merge($argBind, $vals);
    // PHP7.0:
    foreach($vals as $key => $value)
      $argBind[] = &$vals[$key];
    call_user_func_array('mysqli_stmt_bind_param', $argBind);
    if (!$stmt->execute()) {
      throw new WBException($this->_config['SiteName'],
        $this->_config['DeveloperEmail'],
        'Execute statement failed. ' . $stmt->error . 
        ' for SQL command "' . $sql . '"');
    }
    $stmt->close();
    $this->_insert_id = $this->_DBconn->insert_id;
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
          // PHP5.2:
          //$argBind = array_merge($argBind, $params);
          // PHP7.0:
          foreach($params as $key => $value)
            $argBind[] = &$params[$key];
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

  private function IsAgentRobot($agent, $ip) {
    $robot = false;
    if (strpos(strtolower($agent), 'bot') !== FALSE ||
      strpos(strtolower($agent), 'crawl') !== FALSE ||
      strpos(strtolower($agent), 'slurp') !== FALSE ||
      strpos(strtolower($agent), 'spider') !== FALSE ||
      strpos(strtolower($agent), 'ips-agent') !== FALSE ||
      strpos(strtolower($agent), 'google favicon') !== FALSE ||
      strpos(strtolower($agent), 'mediapartners') !== FALSE) {
      $robot = true;
    } else {
      // If the user agent was not marked as a bot, check again based 
      // on the frequency of requests from this ip adress since 5s
      $dateTimeSince = date('Y-m-d H:i:s', time() - 30);
      $dateTimeUpdate = date('Y-m-d', strtotime('-1 days'));
      $sqlCheck = 'SELECT COUNT(*) FROM WBAccessTracker ';
      $sqlCheck .= 'WHERE RefererIP = ? AND (';
      $sqlCheck .= 'DateTime >= "' . $dateTimeSince . '" OR ';
      $sqlCheck .= '(DateTime >= "' . $dateTimeUpdate . '" AND ';
      $sqlCheck .= 'Robot = 1))';
      $colsCheck = array('nb');
      $paramsCheck = array($ip);
      $res = $this->ExecSelectSQL($sqlCheck, $colsCheck, 's',
        $paramsCheck);
      if ($res[0]['nb'] >= 5) {
        // Mark this access as a robot
        $robot = true;
        // Mark all the previous access from this IP since yesterday
        // as robots
        $colsUpdate = array('Robot');
        $valsUpdate = array(&$robot, &$ip);
        $this->ExecUpdateSQL('WBAccessTracker', $colsUpdate, 'is',
          $valsUpdate, 'RefererIp = ? AND DateTime >= "' . $dateTimeUpdate . '"');
      }
    }
    return $robot;
  }

  public function LogAccess() {
    // Get the accessed domain without $_GET parameters
    $domain = $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    // If we are logging the access and its the first time the 
    // user accesses this domain
    if ($this->_config['AccessStat'] === true &&
      isset($_SESSION['WBACCESSTRACKREF' . $domain]) === false) {
      // Get the date and time of access
      $datetime = date('Y-m-d H:i:s');
      // Get the info about the user based on its IP
      $ip_data = json_decode($this->GetIPInfo());
      if (isset($_SERVER['HTTP_ORIGIN'])) {
        $referer = $_SERVER['HTTP_ORIGIN'];
      }
      else if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
      } else {
        $referer = "";
      }
      // Create the longitude,latitude coordinates
      $longlat = $ip_data->geoplugin_longitude . ',' . 
        $ip_data->geoplugin_latitude;
      // Create the accessed uri
      $uri = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      // Check if the user agent is a bot
      $robot = ($this->IsAgentRobot($_SERVER['HTTP_USER_AGENT'],
        $ip_data->geoplugin_request) ? '1' : '0');
      // Save the access in the DB
      $this->ExecInsertSQL('WBAccessTracker', 
        array('DateTime', 'RefererIP', 'City', 'Country', 'LongLat',
        'HTTP_REFERER', 'HTTP_USER_AGENT', 'REQUEST_URI', 'Robot'), 
        'sssssssss', array($datetime, $ip_data->geoplugin_request, 
        $ip_data->geoplugin_city, $ip_data->geoplugin_countryName, 
        $longlat, $referer, $_SERVER['HTTP_USER_AGENT'], $uri, $robot));
      // Save the access in $_SESSION to avoid counting several times
      // the same user
      $_SESSION['WBACCESSTRACKREF' . $domain] = $this->_insert_id;
    }
  }

  public function GetNbAccess($domain, $since, $until) {
    $sql = 'SELECT COUNT(*) FROM WBAccessTracker ';
    $sql .= 'WHERE REQUEST_URI LIKE ? AND DateTime >= ? ';
    $sql .= 'AND DateTime <= ? AND Robot = FALSE ';
    $cols = array('nb');
    $types = 'sss';
    $d = $domain . '%';
    $params = array(&$d, &$since, &$until);
    $res = $this->ExecSelectSQL($sql, $cols, $types, $params);
    return $res[0]['nb'];
  }

  public function GetAccessRankCountry($domain, $since, $until) {
    $sql = 'SELECT Country, COUNT(*) as c FROM WBAccessTracker ';
    $sql .= 'WHERE REQUEST_URI LIKE ? AND DateTime >= ? ';
    $sql .= 'AND DateTime <= ? AND Robot = FALSE ';
    $sql .= 'GROUP BY Country ORDER BY c DESC ';
    $cols = array('country', 'nb');
    $types = 'sss';
    $d = $domain . '%';
    $params = array(&$d, &$since, &$until);
    $res = $this->ExecSelectSQL($sql, $cols, $types, $params);
    return $res;
  }

  public function GetAccessRankCity($domain, $since, $until) {
    $sql = 'SELECT Country, Region, City, COUNT(*) as c FROM WBAccessTracker ';
    $sql .= 'WHERE REQUEST_URI LIKE ? AND DateTime >= ? ';
    $sql .= 'AND DateTime <= ? AND Robot = FALSE ';
    $sql .= 'GROUP BY City, Region, Country ORDER BY c DESC ';
    $cols = array('country', 'region', 'city', 'nb');
    $types = 'sss';
    $d = $domain . '%';
    $params = array(&$d, &$since, &$until);
    $res = $this->ExecSelectSQL($sql, $cols, $types, $params);
    return $res;
  }

  public function GetAccessRankReferer($domain, $since, $until) {
    $sql = 'SELECT HTTP_REFERER, COUNT(*) as c FROM WBAccessTracker ';
    $sql .= 'WHERE REQUEST_URI LIKE ? AND DateTime >= ? ';
    $sql .= 'AND DateTime <= ? AND Robot = FALSE ';
    $sql .= 'GROUP BY HTTP_REFERER ORDER BY c DESC ';
    $cols = array('referer', 'nb');
    $types = 'sss';
    $d = $domain . '%';
    $params = array(&$d, &$since, &$until);
    $res = $this->ExecSelectSQL($sql, $cols, $types, $params);
    return $res;
  }

  public function GetAccessRankAgent($domain, $since, $until) {
    $sql = 'SELECT HTTP_USER_AGENT, COUNT(*) as c ';
    $sql .= 'FROM WBAccessTracker ';
    $sql .= 'WHERE REQUEST_URI LIKE ? AND DateTime >= ? ';
    $sql .= 'AND DateTime <= ? AND Robot = FALSE ';
    $sql .= 'GROUP BY HTTP_USER_AGENT ';
    $sql .= 'ORDER BY c DESC ';
    $cols = array('agent', 'nb');
    $types = 'sss';
    $d = $domain . '%';
    $params = array(&$d, &$since, &$until);
    $res = $this->ExecSelectSQL($sql, $cols, $types, $params);
    return $res;
  }

  public function GetNbAccessByBot($domain, $since, $until) {
    $sql = 'SELECT COUNT(*) as c ';
    $sql .= 'FROM WBAccessTracker ';
    $sql .= 'WHERE REQUEST_URI LIKE ? AND DateTime >= ? ';
    $sql .= 'AND DateTime <= ? AND Robot = TRUE';
    $cols = array('nb');
    $types = 'sss';
    $d = $domain . '%';
    $params = array(&$d, &$since, &$until);
    $res = $this->ExecSelectSQL($sql, $cols, $types, $params);
    return $res[0]['nb'];
  }

  public function GetAccessLongLat($domain, $since, $until) {
    $sql = 'SELECT LongLat ';
    $sql .= 'FROM WBAccessTracker ';
    $sql .= 'WHERE REQUEST_URI LIKE ? AND DateTime >= ? ';
    $sql .= 'AND DateTime <= ? AND Robot = FALSE';
    $cols = array('longlat');
    $types = 'sss';
    $d = $domain . '%';
    $params = array(&$d, &$since, &$until);
    $res = $this->ExecSelectSQL($sql, $cols, $types, $params);
    return $res;
  }

  public function GetAccessByDay($domain, $since, $until) {
    $sql = 'SELECT DATE(DateTime), COUNT(*) ';
    $sql .= 'FROM WBAccessTracker ';
    $sql .= 'WHERE REQUEST_URI LIKE ? AND DateTime >= ? ';
    $sql .= 'AND DateTime <= ? AND Robot = FALSE ';
    $sql .= 'GROUP BY DATE(DateTime) ORDER BY DATE(DateTime) ASC';
    $cols = array('date', 'nb');
    $types = 'sss';
    $d = $domain . '%';
    $params = array(&$d, &$since, &$until);
    $res = $this->ExecSelectSQL($sql, $cols, $types, $params);
    return $res;
  }

  // Function to create a horyzontal gauge
  // ratio in [0, 1]
  public function BuildGaugeHoriz($id, $label, $width, $ratio) {
    $block = '';
    $block .= '<div id="' . $id . 
      '" class="divWBGauge" style="width:' . $width . 'px;">';
    $block .= '<div class="divWBGaugeBack" style="width:' . 
      ($ratio * 100) . '%;">';
    $block .= '</div>';
    $block .= '<div class="divWBGaugeFront" 
      style="width:' . $width . 'px;">';
    $block .= $label;
    $block .= '</div>';
    $block .= $label;
    $block .= '</div>';
    return $block;
  }

  // Function to create a horizontal histogram of dimension width, height
  // data = array() of values in order from left to right
  public function BuildHisto($id, $data, $width, $height, 
    $rgba = array(0, 0, 0, 1)) {
    $block = '';
    $block .= '<div id="' . $id . '" class="divWBHisto" ';
    $block .= 'style="width:' . $width . 'px;height:' . $height . 
      'px;">';
    if (sizeof($data) > 0) {
      $max = $data[0];
      foreach ($data as $k => $v) {
        if ($max < $v) {
          $max = $v;
        }
      }
      foreach ($data as $k => $v) {
        $block .= '<div id="' . $id . 'Sample' . $k . 
          '" class="divWBHistoSample" style="';
        if ($max != 0) {
          $h = floor($v / $max * $height);
        } else {
          $h = 0;
        }
        $block .= 'top:' . floor($height - $h - 1) .
          'px;left:' . floor($k * $width / sizeof($data) - 1). 'px;';
        $block .= 'width:' . floor($width / sizeof($data) - 1) . 'px;';
        $block .= 'height:' . $h . 'px;">';
        if ($h >= 20) {
          $block .= $v;
        }
        $block .= '</div>';
      }
    }
    $block .= '</div>';
    return $block;
  }

  private function BuildMapAccess($longlat, $widthMap, 
    $heightMap, $sizeDot) {
    $block = '';
    $block .= '<div class="divWBAccessMap">';
    foreach ($longlat as $k => $v) {
      if ($v['longlat'] != '') {
        $ll = explode(',', $v['longlat']);
        $long = (0.5 + $ll[0] / 360.0) * $widthMap - 0.5 * $sizeDot;
        $lat = (1 - (0.5 + $ll[1] / 180.0)) * 
          $heightMap - 0.5 * $sizeDot;
        $block .= '<img style="top:' . $lat . 
          'px; left:' . $long . 'px;">';
      }
    }
    $block .= '</div>';
    return $block;
  }
  
  public function BuildAccessTile($id, $domain, 
    $dateStartCounter, $dateEndCounter) {
    $block = '';
    $block .= '<div id="' . $id . '" class="divWBAccessTile">';
    $block .= '<div class="divWBAccessTileTitle">Counters for<br>' . 
      $domain . '</div>';
    $block .= '<div class="divWBAccessTileContent">';
    $block .= 'Total access : ' . 
      $this->GetNbAccess($domain, $dateStartCounter, 
      $dateEndCounter);
    $block .= ' ( and ' . 
      $this->GetNbAccessByBot($domain, $dateStartCounter, 
      $dateEndCounter);
    $block .= ' bots)<br>';
    $accessByDay = 
      $this->GetAccessByDay($domain, $dateStartCounter,
      $dateEndCounter);
    $histo = array();
    $curDate = $dateStartCounter;
    foreach ($accessByDay as $k => $v) {
      while ($v['date'] != $curDate && $curDate <= $dateEndCounter) {
        $histo[] = 0;
        $curDate = date('Y-m-d', strtotime($curDate .' +1 day'));
      }
      $histo[] = $v['nb'];
      $curDate = date('Y-m-d', strtotime($curDate .' +1 day'));
    }
    while ($curDate < $dateEndCounter) {
      $histo[] = 0;
      $curDate = date('Y-m-d', strtotime($curDate .' +1 day'));
    }
    $block .= $this->BuildHisto('divHisto' . $id, $histo, 350, 100);
    $block .= '<br>';
    $block .= '<u>Access by country :</u><br>';
    $block .= '<div class="divWBAccessTileSubContent">';
    $rankCountry = 
      $this->GetAccessRankCountry($domain, $dateStartCounter,
      $dateEndCounter);
    foreach ($rankCountry as $k => $rank) {
      if ($rank['country'] == "") {
        $country = "??";
      } else {
        $country = $rank['country'];
      }
      $block .= $this->BuildGaugeHoriz('divGauge', $country . ': ' . 
        $rank['nb'],
        350, $rank['nb'] / $rankCountry[0]['nb']);
    }
    $block .= '</div>';
    $block .= '<br><br>';
    $block .= '<u>Access by city :</u><br>';
    $block .= '<div class="divWBAccessTileSubContent">';
    $rankCity = 
      $this->GetAccessRankCity($domain, $dateStartCounter,
      $dateEndCounter);
    foreach ($rankCity as $k => $rank) {
      if ($rank['country'] == "") {
        $city = '??';
      } else {
        $city = $rank['country'];
      }
      $city .= ' - ';
      if ($rank['region'] != "") {
        $city .= $rank['region'] . ', ';
      }
      if ($rank['city'] == "") {
        $city .= '??';
      } else {
        $city .= $rank['city'];
      }
      $block .= $this->BuildGaugeHoriz('divGauge', 
        $city . ': ' . $rank['nb'],
        350, $rank['nb'] / $rankCity[0]['nb']);
    }
    $block .= '</div>';
    $block .= '<br><br>';
    $block .= '<u>Map :</u><br><br>';
    $longlat = $this->GetAccessLongLat($domain, 
      $dateStartCounter, $dateEndCounter);
    $block .= $this->BuildMapAccess($longlat, 350, 200, 5);
    $block .= '<br><br>';
    $block .= '<u>Access by referer :</u><br>';
    $block .= '<div class="divWBAccessTileSubContent">';
    $rankReferer = 
      $this->GetAccessRankReferer($domain, $dateStartCounter,
      $dateEndCounter);
    foreach ($rankReferer as $k => $rank) {
      if ($rank['referer'] == "") {
        $referer = '??';
      } else {
        $referer = $rank['referer'];
      }
      if ($referer != '??') {
        $link = '<a href="' . $referer . '">' . $referer . 
          '</a>: ' . $rank['nb'];
      } else {
        $link = $referer . ': ' . $rank['nb'];
      }
      $block .= $this->BuildGaugeHoriz('divGauge', $link,
        350, $rank['nb'] / $rankReferer[0]['nb']);
    }
    $block .= '</div>';
    $block .= '</div>';
    $block .= '</div>';

    return $block;
  }

  public function CreateUserLogin($login, $passwd) {
    $hash = password_hash($passwd, PASSWORD_DEFAULT);
    $this->ExecInsertSQL('WBLogin', 
      array('Login', 'Hash'), 
      'ss', array($login, $hash));
  }

  public function CheckUserLogin($login, $passwd) {
    $sql = 'SELECT Hash ';
    $sql .= 'FROM WBLogin ';
    $sql .= 'WHERE Login = ? ';
    $cols = array('hash');
    $types = 's';
    $params = array(&$login);
    $ret = $this->ExecSelectSQL($sql, $cols, $types, $params);
    if (isset($ret[0]) && isset($ret[0]['hash']))
      return password_verify($passwd, $ret[0]['hash']);
    else
      return false;
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
