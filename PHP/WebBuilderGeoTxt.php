<?php

  if (isset($WBDico) == false || 
    $WebBuilderConf["RefreshDico"] == true) {

    $WBDico = array('en', 'ja', 'fr');
    foreach ($WBDico as $key => $lang) {
      $WBDico[$lang] = array();
    }

    // Text can be parametered by using '$0$', '$1$', ...
    // which would be replaced by elements of the array given as 
    // argument 'param' of WebBuilder.GetText

    $WBDico['en']['example'] = 'This is an example';
    $WBDico['ja']['example'] = '例文です';
    $WBDico['fr']['example'] = 'Voici un exemple';

  }

?>

