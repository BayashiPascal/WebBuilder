 <?php 
/* ============= WebBuilderDBEdit.php =========== */

  // Start the PHP session
  session_start();
  // Include the WebBuilder
  set_include_path("../");
  require("PHP/WebBuilder.php");
  // Check that the user is logged in
  if ($theWB->IsLogged() == true) {
    if (isset($_GET["modif"])) {
      // Modify a record
      $cols = array();
      $types = "";
      $vals = array();
      $forms = $theWB->GetForms();
      foreach ($forms[$_GET["t"]] as $field => $val) {
        if ($field != "Reference" &&
          $field != "WBDBEditorFilter") {
          $cols[] = $field;
          $vals[] = $_POST[$field];
          $types .= "s";
        }
      }
      $cond = "Reference = " . $_POST["Reference"];
      $theWB->ExecUpdateSQL($_GET["t"], $cols, $types, $vals, $cond);
      $ret = '{"err":""}';
      echo $ret;
    } else if (isset($_GET["add"])) {
      // Add a record
      $cols = array();
      $types = "";
      $vals = array();
      $forms = $theWB->GetForms();
      foreach ($forms[$_GET["t"]] as $field => $val) {
        if ($field != "Reference" &&
          $field != "WBDBEditorFilter") {
          $cols[] = $field;
          if (isset($_POST[$field])) {
            $vals[] = $_POST[$field];
          } else {
            $vals[] = "";
          }
          $types .= "s";
        }
      }
      $theWB->ExecInsertSQL($_GET["t"], $cols, $types, $vals);
      $ret = '{"err":""}';
      echo $ret;
    } else if (isset($_GET["delete"])) {
      // Delete a record
      $types = "i";
      $vals = array($_POST["Reference"]);
      $cond = "Reference = ?";
      $theWB->ExecDeleteSQL($_GET["t"], $cond, $types, $vals);
      $ret = '{"err":""}';
      echo $ret;
    } else if (isset($_GET["load"])) {
      // Reload the DB editor's data
      $data = $theWB->DBEditorGetData($_GET["t"]);
      $ret = '{"err":"", "editor":"' . $_GET["t"] . 
        '","data":' . json_encode($data) . '}';
      echo $ret;
    } else {
      echo '{"err":"No command given."}';
    }
  } else {
    // Return the result
    echo '{"err":"User is not logged in."}';
  }
?>
