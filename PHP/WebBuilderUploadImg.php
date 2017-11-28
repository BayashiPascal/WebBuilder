<?php 
ini_set('display_errors', 'Off');
error_reporting(0);
session_start();

function UploadImage($formats, $sizeMax) {
  $errCode = 0;
  $targetFile = '';
  $imageFileType = '';
  $msg = '';
  try {
    $targetDir = '../Img/UploadImg/';
    $name = $_FILES['inpUploadImg']['name'];
    $targetFile = $targetDir . basename($name);
    $tmpName = $_FILES['inpUploadImg']['tmp_name'];
    $imageFileType = pathinfo($targetFile, PATHINFO_EXTENSION);
    if (file_exists($targetFile)) {
      $errCode = 4;
    } else {
      if ($_FILES['inpUploadImg']['size'] > $sizeMax) {
        $errCode = 5;
      } else {
        if(strpos($formats, $imageFileType) === false) {
          $errCode = 6;
        } else {
          if ($errCode == 0) {
            if (is_dir($targetDir) && is_writable($targetDir)) {
              if (!move_uploaded_file($tmpName, $targetFile)) {
                $errCode = 7;
              }
            } else {
              $errCode = 8;
            }
          }
        }
      }
    }
  } catch (Exception $e) {
    $errCode = 9;
    $msg = $e->getMessage() . '<br>' . $e->getTraceAsString();
  }
  return array($errCode, $targetFile, $imageFileType, $msg);
}

try {
  if (isset($_FILES['inpUploadImg']) && $_FILES['inpUploadImg']['name'] != "") {
    $ret = UploadImage($_GET['f'], $_GET['s']);
    $data = array();
    $data['err'] = $ret[0];
    $data['sizeMax'] = $_GET['s'];
    $data['formats'] = $_GET['f'];
    $data['size'] = $_FILES['inpUploadImg']['size'];
    $data['targetFile'] = str_replace('../', '', $ret[1]);
    $data['fileType'] = $ret[2];
    $data['msg'] = $ret[3];
  } else {
    $data = array('3', '', '', '');
  }
  $jsonData = json_encode($data);
  echo $jsonData;
} catch (Exception $e) {
  $data = array();
  $data['err'] = 9;
  $data['msg'] = $e->getMessage() . '<br>' . $e->getTraceAsString();
  $jsonData = json_encode($data);
  echo $jsonData;
}
?>
