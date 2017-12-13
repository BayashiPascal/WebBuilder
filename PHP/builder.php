 <?php 
/* ============= builder.php =========== */

function FormatCode($txt) {
  $block = '';
  $block .= '<span class="spanCode"><i>' . $txt . '</i></span>';
  return $block;
}

function BuildUsage() {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Usage';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'Defines the arborescence and copy the WebBuilder files 
    in your website as follow:<br>';
  $block .= '<ul>';
  $block .= '<li>/</li>';
  $block .= '<li><ul>';
  $block .= '<li>index.php</li>';
  $block .= '<li>CSS</li>';
  $block .= '<li><ul>';
  $block .= '<li>WebBuilder.css</li>';
  $block .= '<li> ... </li>';
  $block .= '</ul></li>';
  $block .= '<li>Img</li>';
  $block .= '<li><ul>';
  $block .= '<li>icon.32x32.ico</li>';
  $block .= '<li>icon.180x180.png</li>';
  $block .= '<li>icon.196x196.png</li>';
  $block .= '<li> ... </li>';
  $block .= '<li>Icons</li>';
  $block .= '<li><ul>';
  $block .= '<li> ... </li>';
  $block .= '</ul></li>';
  $block .= '<li>UploadImg</li>';
  $block .= '<li><ul>';
  $block .= '<li> ... </li>';
  $block .= '</ul></li>';
  $block .= '</ul></li>';
  $block .= '<li>Sound</li>';
  $block .= '<li><ul>';
  $block .= '<li> ... </li>';
  $block .= '</ul></li>';
  $block .= '<li>JS</li>';
  $block .= '<li><ul>';
  $block .= '<li>WebBuilder.js</li>';
  $block .= '<li>jquery.min.js</li>';
  $block .= '<li> ... </li>';
  $block .= '</ul></li>';
  $block .= '<li>PHP</li>';
  $block .= '<li><ul>';
  $block .= '<li>WebBuilder.php</li>';
  $block .= '<li>WebBuilderConf.php</li>';
  $block .= '<li>WebBuilderDBConf.php</li>';
  $block .= '<li>WebBuilderGeoTxt.php</li>';
  $block .= '<li> ... </li>';
  $block .= '</ul></li>';
  $block .= '</ul></li>';
  $block .= '</ul>';
  $block .= 'Include the WebBuilder with ' . 
    FormatCode('require("PHP/WebBuilder.php");') .
    ', add ' . FormatCode('WBBodyOnLoad("&lt;?php echo 
    $theWB->GetJSdata(); ?&gt;");') . ' to the body onload event 
    and ' . FormatCode('echo $theWB->BuildCSS(); echo 
    $theWB->BuildJS();') . ' to the head section, set the 
    configuration parameters in ' . 
    FormatCode('WebBuilderConf.php') . ' and ' .
    FormatCode('WebBuilderDBConf.php') . ', take care that the 
    UploadImg folder is writable if you plan to use the image 
    uploader, and you\'re ready to use 
    it as described in the following tiles.<br>';
  $block .= '</div>';
  return $block;
}

function BuildError() {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Error handling';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'Errors and exceptions in PHP code are handled by 
    dedicated functions which run in two modes. Current mode is 
    defined by ' . FormatCode('DebugMode') . ' in ' .
    FormatCode('WebBuilderConf.php') . '. If ' . 
    FormatCode('DebugMode') . ' is true, a detailed report is shown 
    on the page. Else, a simple report is shown, and the developper 
    is informed by email (' . FormatCode('DeveloperEmail') . ' in ' . 
    FormatCode('WebBuilderConf.php') . '). If the developper 
    couldn\'t be informed by email, the user is invited to contact 
    him with information displayed on the page. In both modes the 
    rendering is stopped.<br>';
  $block .= 'Errors in JavaScript code are displayed in the console 
    with the execution stack.<br>';
  $block .= '</div>';
  return $block;
}

function BuildGeoText($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Localized texts';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'Texts can be localized. The dictionary is defined in ' .
    FormatCode('WebBuilderGeoTxt.php') . '. The current language is 
    available through the method ' . FormatCode('$theWB->GetLang()') .
    ' and can be set through the method ' . 
    FormatCode('$theWB->SetLang($lang)') . ' (only languages available 
    in the dictionary can bet set).<br>';
  $block .= 'Localized text are available through the method ' . 
    FormatCode('GetTxt($txt, $param)') . '. ' . FormatCode('$txt') . 
    ' is the label identifiying the text. ' . FormatCode('$param') . 
    ' is an optional array of parameters. The 1st parameter is 
    substitute to the string ' . FormatCode('$1$') . ' in the text, 
    the 2nd is substitute to ' . FormatCode('$2$') . ', and so 
    on.<br>';
  $block .= 'If a text is not available in the current language, it 
    is searched in the default language defined by ' . 
    FormatCode('DefaultLang') . ' in '. 
    FormatCode('WebBuilderConf.php') . ', and if not available in the 
    default language too, its label is displayed instead.<br>';
  $block .= 'Example <a href="?la=en">in english</a>, <a 
    href="?la=fr">in french</a>, <a href="?la=ja">in 
    japanese</a>:<br>';
  $block .= $theWB->GetTxt('example') . '<br>';
  $block .= 'The language is first initialized with the location of 
    the visitor (based on IP address) when he arrives on the page.<br>';
  $block .= 'The language is defined at PHP level and cannot be 
    modified via JavaScript.<br>';
  $block .= 'It can be changed by URL argument ' . 
    FormatCode('la') . '. See also "URL arguments".<br>';
  $block .= '</div>';
  return $block;
}

function BuildMode($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Mode';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'A current mode is memorized. It is available through the 
    method ' . FormatCode('$theWB->GetMode()') . ' and can be set 
    through the method ' . FormatCode('$theWB->SetMode($mode)') . 
    ' (only modes available in ' . FormatCode('AvailableModes') . 
    ' in ' . FormatCode('WebBuilderConf.php') . ' can bet set).<br>';
  $block .= 'The mode is defined at PHP level and cannot be 
    modified via JavaScript.<br>';
  $block .= 'Current mode is ' . $theWB->GetMode() . '.<br>';
  $block .= 'Set mode to <a href="?mo=0">0</a>, <a 
    href="?mo=1">1</a>.<br>';
  $block .= 'It is based on URL argument ' . FormatCode('mo') . '. 
    See also "URL arguments".<br>';
  $block .= '</div>';
  return $block;
}

function BuildDOM($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'DOM';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'Some default DOM elements and styles are available (all 
    defined in ' . FormatCode('WebBuilder.css') . ' and visible in 
    the current page):<br>';
  $block .= '<ul>';
  $block .= '<li>body, button, select, ul default CSS styles</li>';
  $block .= '<li>Page title available through the method ' . 
    FormatCode('$theWB->BuildDivTitle($id, $content)') . '(class ' . 
    FormatCode('.divWBTitle') . ')</li>';
  $block .= '<li>Tiles available through the method ' . 
    FormatCode('$theWB->BuildDivTile($id, $content)') . '(class ' .
    FormatCode('.divWBTile') . ')</li>';
  $block .= '<li>Page footer available through the method ' . 
    FormatCode('$theWB->BuildDivFooter($id, $content)') . 
    '(class ' . FormatCode('.divWBFooter') . ')</li>';
  $block .= '<li>' . FormatCode('WBstringifyHTML($var)') . ' and ' . FormatCode('WBstringifyTXT($var)') . ' to print the content of PHP object</li>';
  $block .= '</ul>';
  $block .= '</div>';
  return $block;
}

function BuildHeader($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Header';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'Most of the header\'s elements of the page can be 
    automatically generated using the following methods.<br><ul>';
  $block .= '<li>' . Formatcode('$theWB->BuildMeta()') . 
    ': creates the meta based on ' . FormatCode('MetaDescription') . 
    ', ' . FormatCode('MetaKeywords') . ' and ' . 
    FormatCode('MetaViewportWidth') . ' in ' . 
    FormatCode('WebBuilderConf.php') . '.</li>';
  $block .= '<li>' . Formatcode('$theWB->BuildBase()') . ': creates 
    the base tag based on ' . FormatCode('BaseURL') . ' in ' . 
    FormatCode('WebBuilderConf.php') . '</li>';
  $block .= '<li>' . Formatcode('$theWB->BuildIcon()') . ': creates 
    the page icon. It must be ./Img/icon.180x180.png, 
    ./Img/icon.196x196.png and ./Img/icon.32x32.ico</li>';
  $block .= '<li>' . Formatcode('$theWB->BuildCSS()') . ': creates 
    the CSS includes based on ' . FormatCode('IncludeCSS') . ' in ' . 
    FormatCode('WebBuilderConf.php') . '</li>';
  $block .= '<li>' . Formatcode('$theWB->BuildJS()') . ': creates 
    the JSS includes based on ' . FormatCode('IncludeJS') . ' in ' . 
    FormatCode('WebBuilderConf.php') . '</li>';
  $block .= '<li>' . Formatcode('$theWB->BuildTitle()') . ': creates 
    the page title based on ' . FormatCode('SiteName') . ' in ' . 
    FormatCode('WebBuilderConf.php') . '</li>';;
  $block .= '</ul></div>';
  return $block;
}

function BuildURLArg($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'URL arguments';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'Some arguments in the URL are automatically 
    processed by calling ' . FormatCode('$theWB->ProcessURLArg();') .
     ' after including ' . FormatCode('WebBuilder.php') . '.<br>';
  $block .= '<ul>';
  $block .= '<li>' . FormatCode('mo') . ' defines the current   
    mode</li>';
  $block .= '<li>' . FormatCode('la') . ' defines the current 
    language</li>';
  $block .= '<li>' . FormatCode('setupdb') . ' requests the creation 
    of the MySQL database according to the model ' . 
    FormatCode('DBModel') . ' in ' . 
    FormatCode('WebBuilderConf.php') . '</li>';
  $block .= '</ul>';
  $block .= '</div>';
  return $block;
}

function BuildExecOnServer($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Exec command on server';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'A command can be executed on the server with the code ' .
    FormatCode('$theWB->ExecCmdOnServer($cmd)') . '. For example, ' .
    FormatCode('ls ./Img/*.jpg') . ':<br>';
  $res = $theWB->ExecCmdOnServer('ls ./Img/*.jpg');
  $block .=  WBstringifyHTML($res) . '<br>';
  $block .= 'The result can be obtained as a JS object encoded in
    JSON format with the variant ' .
    FormatCode('$theWB->ExecCmdOnServerJSON($cmd)') . '<br>';
  $block .= '</div>';
  return $block;
}

function BuildCountryFromIP($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Country from IP';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'The country of the current viewer, based on its IP 
    adress can be displayed with ' . 
    FormatCode('$theWB->GetCountryFromIP()') . '<br>';
  $block .= 'Your country is ' . $theWB->GetCountryFromIP();
  $block .= '</div>';
  return $block;
}

function BuildDB($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Database';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'The connection information to the MySQL database are 
    defined in ' . FormatCode('DB_servername') . ', ' . 
    FormatCode('DB_username') . ', ' . FormatCode('DB_password') . 
    ' and ' . FormatCode('DB_dbname') . '.<br>';
  $block .= 'The database can be accessed and edited through the 
    following functions:<br>';
  $block .= '<ul>';
  $block .= '<li>' . FormatCode('$theWB->ExecSelectSQL($sql, 
    $cols, $types = "", $params = array())') . ': Executes a SELECT 
    SQL command and stores the result in an array[records][cols]. 
    Names of columns in the result array are given by the array ' . 
    FormatCode('$cols') . '. If the request takes parameters, there 
    types are given by ' . FormatCode('$types') . ' and their values 
    are given by ' . FormatCode('$params') . '</li>';
  $block .= '<li>' . FormatCode('$theWB->ExecInsertSQL($table, 
    $cols, $types, $vals)') . ': Executes a INSERT SQL command in 
    table ' . FormatCode('$table') . ' with values ' . FormatCode(
    '$vals') . ' of types ' . FormatCode('$types') . ' for columns ' .
    FormatCode('$cols') . '.</li>';
  $block .= '<li>' . FormatCode('$theWB->ExecUpdateSQL($table, 
    $cols, $types, $vals, $cond = "")') . ': Executes a UPDATE SQL 
    command in table ' . FormatCode('$table') . ' with values ' . 
    FormatCode('$vals') . ' of types ' . FormatCode('$types') . ' 
    for columns ' . FormatCode('$cols') . ' under the condition ' . 
    FormatCode('$cond') . ' (if the condition has parameters they 
    are given together with the update parameters).</li>';
  $block .= '<li>' . FormatCode('$theWB->ExecDeleteSQL($table, 
    $cond, $types, $vals)') . ': Executes a DELETE SQL 
    command in table ' . FormatCode('$table') . ' under the 
    condition ' . FormatCode('$cond') . '. Parameters\' types and 
    values of the condition are given by ' . FormatCode('$types') . 
    ' and ' . FormatCode('$vals') . '.</li>';
  $block .= '</ul>';
  $block .= 'Example of use:<br>';

  $block .= FormatCode('$theWB->ExecSelectSQL("SELECT Reference,
    DateCmd, Val FROM WBTableTest", array("Ref", "Date", "Val"));') .
    '<br>';
  $res = $theWB->ExecSelectSQL('SELECT Reference, DateCmd, Val FROM 
    WBTableTest', array('Ref', 'Date', 'Val'));
  $block .= WBstringifyHTML($res) . '<br>';

  $block .= '<br>' . FormatCode('$ref = null;') . '<br>' . 
    FormatCode('$date = date("Y-m-d H:i:s");') . '<br>' . 
    FormatCode('$val = "new value";') . '<br>' . 
    FormatCode('$theWB->ExecInsertSQL("WBTableTest", 
    array("Reference", "DateCmd", "Val"), "iss", array(&$ref, 
    &$date, &$val));') . '<br>';
  $ref = null;
  $date = date('Y-m-d H:i:s');
  $val = 'new value';
  $theWB->ExecInsertSQL('WBTableTest', 
    array('Reference', 'DateCmd', 'Val'), 'iss', 
    array(&$ref, &$date, &$val));
  $res = $theWB->ExecSelectSQL('SELECT Reference, DateCmd, Val FROM 
    WBTableTest', array('Reference', 'DateCmd', 'Val'));
  $block .= WBstringifyHTML($res) . '<br>';

  $block .= '<br>' . FormatCode('$oldval = "new value";') . '<br>' .
    FormatCode('$newval = "value";') . '<br>' .
    FormatCode('$theWB->ExecUpdateSQL("WBTableTest", array("Val"),
    "ss", array(&$newval, &$oldval), "Val = ?");') . '<br>'; 
  $oldval = 'new value';
  $newval = 'value';
  $theWB->ExecUpdateSQL('WBTableTest', array('Val'), 'ss', 
    array(&$newval, &$oldval), 'Val = ?');
  $res = $theWB->ExecSelectSQL('SELECT Reference, DateCmd, Val FROM 
    WBTableTest', array('Reference', 'DateCmd', 'Val'));
  $block .= WBstringifyHTML($res) . '<br>';

  $block .= '<br>' . FormatCode('$theWB->ExecDeleteSQL("WBTableTest", 
    "Val = ?", "s", array(&$newval));') . '<br>';
  $theWB->ExecDeleteSQL('WBTableTest', 'Val = ?', 's', 
    array(&$newval));
  $res = $theWB->ExecSelectSQL('SELECT Reference, DateCmd, Val FROM 
    WBTableTest', array('Reference', 'DateCmd', 'Val'));
  $block .= WBstringifyHTML($res) . '<br>';

  $block .= '</div>';
  return $block;
}

function BuildHTTPRequest($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'HTTPRequest';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'A HTTP GET request toward an API returning JSON data can  
    be summoned through the code:<br>' . 
    FormatCode('theWB.HTTPGetRequest(url, handler);') . '<br>The 
    handler has one argument, an object containing the decoded JSON 
    data. For example, request a random chuck joke from <a 
    href="https://api.chucknorris.io" 
    target="_blank">api.chucknorris.io</a>:<br><div id="divChuck" 
    style="text-align:center;"><img id="imgChuck" 
    src="./Img/waiting.gif" alt=""><br><div 
    id="divTitleChuck"></div></div>';
  $block .= 'A HTTP POST request toward an API returning JSON data can  
    be summoned through the code:<br>' . 
    FormatCode('theWB.HTTPPOStRequest(url, form, handler);') . 
    '<br>The "form" arguments is the form element in the DOM 
    containing the form data to be sent. The 
    handler has one argument, an object containing the decoded JSON 
    data. ';
  $block .= '</div>';
  return $block;
}

function BuildImgLoader($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Image loader';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'A list of images can be loaded asynchronously 
    through the JS code:<br>' . 
    FormatCode('var images = new Array( ... );<br>var ids = 
    new Array( ... );<br>theWB.LoadImg(images, ids, waiting);') . 
    '<br>where ' . FormatCode('images') . ' is an array of url of 
    loaded images, relatively to ' . FormatCode('./Img/') . 
    ', and ' . FormatCode('ids') . ' is the array of respective id of
    img elements to which the loaded images should be affected (if 
    null nothing happen but the image is still available in cache, 
    ready to be displayed later), and ' . FormatCode('waiting') . 
    ' is the image (also relatively to ' . FormatCode('./Img/') . 
    ') that should be displayed while the real image is
    loading (if not null and respective id is defined)<br>';
  $block .= '<img id="imgPreload00" class="imgPreload" 
    src="./Img/waiting.gif" alt="">';
  $block .= '<img id="imgPreload01" class="imgPreload" 
    src="./Img/waiting.gif" alt="">';
  $block .= '<img id="imgPreload02" class="imgPreload" 
    src="./Img/waiting.gif" alt="">';
  $block .= '</div>';
  return $block;
}

function BuildIcons($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Icons';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'Icons available for the design (75x75, PNG, transparent 
    background, in ' . FormatCode('Img/Icons/') . '):<br>';
  $icons = $theWB->ExecCmdOnServer('ls ./Img/Icons/*.png');
  foreach ($icons["output"] as $icon) {
    $block .= '<img src="' . $icon . '" class="imgIcon" alt="">';
  }
  $block .= '</div>';
  return $block;
}

function BuildFlipBook() {
  $block = '';
  $block .= '<div id="flipbook">';
  $block .= '<div class="hard"><img src="Img/FlipBook/01.jpg" 
    class="pageflipbook" alt=""></div>';
  $block .= '<div class="hard"><img src="Img/FlipBook/02.jpg" 
    class="pageflipbook" alt=""></div>';
  $block .= '<div> <img src="Img/FlipBook/03.jpg" class="pageflipbook" 
    alt=""></div>';
  $block .= '<div> <img src="Img/FlipBook/04.jpg" class="pageflipbook" 
    alt=""></div>';
  $block .= '<div class="hard"><img src="Img/FlipBook/05.jpg" 
    class="pageflipbook" alt=""></div>';
  $block .= '<div class="hard"><img src="Img/FlipBook/06.jpg" 
    class="pageflipbook" alt=""></div>';
  $block .= '</div>';
  return $block;
}

function BuildExternLib($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'External useful libraries';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= '<ul>';
  $block .= '<li><a href = 
    "https://daneden.github.io/animate.css/" 
    target="_blank">Animate.css</a>: a great CSS library to simply 
    animate webpages.<div id="divAnimate" 
    onclick="ExampleAnimate();">[Click here for example]</div>' . 
    '#divAnimate.onclick:<br>' .
    FormatCode('$("#divAnimate").addClass("animated 
    bounce");<br>setTimeout(function(){      
    $("#divAnimate").removeClass("animated bounce");}, 1000);') . 
    '<br></li>';
  $block .= '<li><a href="http://www.turnjs.com/" 
    target="_blank">Turn.js</a>: a great JS library to display a book. 
    Examples:<br>' . BuildFlipBook() . '<br>' .
    'in bodyonload:<br>' .
  FormatCode('$("#flipbook").turn({width: 284, height: 200, 
    autoCenter: false});') . '<br>' .
  'in css:<br>' .
  FormatCode('.pageflipbook {width: 142px; height: 200px;}') . '<br>' .
  'in dom:<br>' .
  FormatCode('<xmp><div id="flipbook"></xmp><xmp><div class="hard"><img src="Img/FlipBook/01.jpg" class="pageflipbook" alt=""></div></xmp><xmp><div class="hard"><img src="Img/FlipBook/02.jpg" class="pageflipbook" alt=""></div></xmp><xmp><div><img src="Img/FlipBook/03.jpg" class="pageflipbook" alt=""></div></xmp><xmp><div><img src="Img/FlipBook/04.jpg" class="pageflipbook" alt=""></div></xmp><xmp><div class="hard"><img src="Img/FlipBook/05.jpg" class="pageflipbook" alt=""></div></xmp><xmp><div class="hard"><img src="Img/FlipBook/06.jpg" class="pageflipbook" alt=""></div></xmp><xmp></div></xmp>') . '</li>';
  $block .= '<li><a 
    href="http://www.schillmania.com/projects/soundmanager2/">
    SoundManager</a>: a great JS library to play sounds inline. 
    Simply add the CSS class ' . FormatCode('sm2_link') . 
    ' to a &lt;a&gt; element.<br><a href="Sound/bell.mp3"
    class="sm2_link soundLink">[Click here for example]</a>.</li>';
  $block .= '</ul>';
  $block .= '</div>';
  return $block;
}

function BuildPHPtoJS($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Passing data from PHP to JS';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'Data from the PHP WebBuilder object can be passed to the JS WebBuilder object with the PHP code ' . FormatCode('$theWB->SetJSData($var);') . ' (must be used in the header, before body onload) and retrieved through the JS code ' . FormatCode('theWB._PHPdata') . '<br>For example to get the date on the server and display it with JS:<br>PHP code<br>' . FormatCode('$var["date"] = date("Y-m-d"); $theWB->SetJSData($var);') . '<br>JS code<br>' . FormatCode('$("#divPHPtoJSdate").html(theWB._PHPdata["date"]);') . '<br>Result:<br><div id="divPHPtoJSdate"></div>';
  $block .= '</div>';
  return $block;
}

function BuildImgUpload($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Image upload to the server';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'Image can be uploaded to the server by using the following methods.<br>Use this PHP code to generate the file uploader:<br>' . FormatCode('$theWB->BuildImgUploader($id, $msgSelect, $msgUpload, $formats, $sizeMax, $callback);') .'<br>';
  $block .= 'Example:<br>PHP code: <xmp>$theWB->BuildImgUploader("exampleUploadImg", "Select the image", "Upload !", "Cancel", array("jpg", "JPG", "jpeg"), 500000, HandlerUploadImg);</xmp>';
  $block .= 'JS code:<br>';
  $block .= FormatCode('function HandlerUploadImg(data) {<br>if (data.err == 0) {<br>$("#imgExampleUploadImg").attr("src", data.targetFile);<br>}}<br>');
  $block .= 'Result:';
  $block .= $theWB->BuildImgUploader('ExampleUploadImg', 'Select the image', array('jpg', 'JPG', 'jpeg'), 500000, 'HandlerUploadImg');
  $block .= '<img id="imgExampleUploadImg" alt="" src="" style="width:200px;"><span id="spanExampleUploadImg"></span><br>';
  $block .= 'Example of returned object passed to the callback function: <xmp>{"err":0,"sizeMax":"500000","formats":"jpg,JPG","size":58929,"targetFile":"Img\/UploadImg\/000.jpg","fileType":"jpg","msg":""}</xmp>';
  $block .= 'error codes:';
  $block .= '<ul>';
  $block .= '<li>0: success</li>';
  $block .= '<li>1: file to upload undefined</li>';
  $block .= '<li>2: http request failed</li>';
  $block .= '<li>3: no file in POST data</li>';
  $block .= '<li>4: file with same name already exists</li>';
  $block .= '<li>5: file too big</li>';
  $block .= '<li>6: file format error</li>';
  $block .= '<li>7: unable to copy file into target directory</li>';
  $block .= '<li>8: target directory doesn\'t exist or is not writable</li>';
  $block .= '<li>9: exception occured</li>';
  $block .= '</ul>';
  $block .= '</div>';
  return $block;
}

function BuildAccess($theWB) {
  $block = '';
  $block .= '<div class="divTileTitle">';
  $block .= 'Tracking of access to the webpage';
  $block .= '</div>';
  $block .= '<div class="divTileMainTxt">';
  $block .= 'Statistics about access to the webpage can be logged automatically in the database by setting the value of ' . FormatCode('AccessStat') . ' to ' . FormatCode('true') . ' in the configuration file. A default tile displaying these statistics is also available through the method ' . FormatCode('$theWB->BuildAccessTile($id, 
    $url, $dateStart, $dateEnd)') . '. An example of this method for records of the last 31 days for the current page is given below.<br><br>';
  $block .= '</div>';
  $dateEnd = date('Y-m-d');
  $dateStart = date('Y-m-d', strtotime($dateEnd . ' -31 day'));
  $dateEnd .= ' 23:59:59';
  $block .= $theWB->BuildAccessTile('divAccess', 
    'www.bayashiinjapan.net/WebBuilder/', $dateStart, $dateEnd);
  return $block;
}

?>
