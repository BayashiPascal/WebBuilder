<?php 
  // ------------------ index.php ---------------------
  // Start the PHP session
  session_start();
  // Include the WebBuilder
  require("PHP/WebBuilder.php");
  // Process POST values
  $theWB->ProcessPOSTValues();
  // Process arguments in url
  // 'la' to set language, 'mo' to set mode, 
  // 'setupdb' to automatically setup the database
  $theWB->ProcessURLArg();
  // Include the local builder
  require("PHP/builder.php");
  // Example of data passed from PHP to JS
  $var["date"] = date("Y-m-d"); $theWB->SetJSData($var);
?>
<!DOCTYPE html>
<html lang="<?php echo $theWB->GetLang(); ?>">
  <head>
<?php 
    echo $theWB->BuildMeta();
    echo $theWB->BuildBase();
    // must be ./Img/icon.180x180.png, ./Img/icon.196x196.png and
    // ./Img/icon.32x32.ico -->
    echo $theWB->BuildIcon();
    echo $theWB->BuildCSS();
    echo $theWB->BuildJS();
    echo $theWB->BuildTitle(); 
?>
  </head>
  <body onload = 'WBBodyOnLoad(<?php echo $theWB->GetJSdata(); ?>);BodyOnLoad();'>
<?php 

    echo $theWB->BuildDivTitle('divTitle', 'WebBuilder');

    $divUsage = BuildUsage($theWB);
    echo $theWB->BuildDivTile('divUsage', $divUsage);

    $divError = BuildError($theWB);
    echo $theWB->BuildDivTile('divError', $divError);

    $divGeoText = BuildGeoText($theWB);
    echo $theWB->BuildDivTile('divGeoText', $divGeoText);

    $divMode = BuildMode($theWB);
    echo $theWB->BuildDivTile('divMode', $divMode);

    $divDOM = BuildDOM($theWB);
    echo $theWB->BuildDivTile('divDOM', $divDOM);

    $divHeader = BuildHeader($theWB);
    echo $theWB->BuildDivTile('divHeader', $divHeader);

    $divURLArg = BuildURLArg($theWB);
    echo $theWB->BuildDivTile('divURLArg', $divURLArg);

    $divExecOnServer = BuildExecOnServer($theWB);
    echo $theWB->BuildDivTile('divExecOnServer', $divExecOnServer);

    $divCountryFromIP = BuildCountryFromIP($theWB);
    echo $theWB->BuildDivTile('divCountryFromIP', $divCountryFromIP);

    $divDB = BuildDB($theWB);
    echo $theWB->BuildDivTile('divDB', $divDB);

    $divHTTPRequest = BuildHTTPRequest($theWB);
    echo $theWB->BuildDivTile('divHTTPRequest', $divHTTPRequest);

    $divImgPreloader = BuildImgLoader($theWB);
    echo $theWB->BuildDivTile('divImgPreloader', $divImgPreloader);

    $divIcons = BuildIcons($theWB);
    echo $theWB->BuildDivTile('divIcons', $divIcons);

    $divExternLib = BuildExternLib($theWB);
    echo $theWB->BuildDivTile('divExternLib', $divExternLib);

    $divPHPtoJS = BuildPHPtoJS($theWB);
    echo $theWB->BuildDivTile('divPHPtoJS', $divPHPtoJS);

    $divImgUpload = BuildImgUpload($theWB);
    echo $theWB->BuildDivTile('divImgUpload', $divImgUpload);

    $divAccess = BuildAccess($theWB);
    echo $theWB->BuildDivTile('divAccessTile', $divAccess);

    $divLogger = BuildLogin($theWB);
    echo $theWB->BuildDivTile('divLoggerTile', $divLogger);

    $divDBEditor = BuildDBEditor($theWB);
    echo $theWB->BuildDivTile('divDBEditor', $divDBEditor);

    echo $theWB->BuildDivFooter('divFooter', 
      'WebBuilder - Developper: P. Baillehache');
?>
  </body>

</html>
