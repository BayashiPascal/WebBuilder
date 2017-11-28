<?php 
  // ------------------ index.php ---------------------
  // Start the PHP session
  session_start();
  // Include the WebBuilder
  require("PHP/WebBuilder.php");
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
<html lang="en">
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

    $divExample = 'You\'re ready to create your webpage using WebBuilder !';
    echo $theWB->BuildDivTile('divExample', $divExample);

    echo $theWB->BuildDivFooter('divFooter', 
      'WebBuilder - Developper: P. Baillehache');
?>
  </body>

</html>
