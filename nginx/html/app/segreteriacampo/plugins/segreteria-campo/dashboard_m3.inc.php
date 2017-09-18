<?php
$camilaWT  = new CamilaWorkTable();
$camilaWT->db = $_CAMILA['db'];


$vSheet = $camilaWT->getWorktableSheetId('VOLONTARI ATTESI');
$mSheet = $camilaWT->getWorktableSheetId('MEZZI ATTESI');
$aSheet = $camilaWT->getWorktableSheetId('ATTREZZATURE ATTESE');

 
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="row">'));	
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));
$camilaUI->insertTitle('Volontari', 'user');
$camilaUI->insertButton('?dashboard=22', 'Assegnazione servizi', 'random');
$camilaUI->insertButton('cf_worktable'.$vSheet.'.php', 'Elenco volontari attesi', 'list');
$camilaUI->insertButton('?dashboard=25', 'Moduli accreditamento', 'barcode');
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));
$camilaUI->insertTitle('Mezzi', 'plane');
$camilaUI->insertButton('?dashboard=24', 'Assegnazione servizi', 'random');
$camilaUI->insertButton('cf_worktable'.$mSheet.'.php', 'Elenco mezzi attesi', 'list');
$camilaUI->insertButton('?dashboard=26', 'Moduli accreditamento', 'barcode');
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '<div class="col-xs-12 col-md-4">'));
$camilaUI->insertTitle('Attrezzature', 'wrench');
$camilaUI->insertButton('?dashboard=23', 'Assegnazione servizi', 'random');
$camilaUI->insertButton('cf_worktable'.$aSheet.'.php', 'Elenco attrezzature attese', 'list');
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));
$_CAMILA['page']->add_raw(new HAW_raw(HAW_HTML, '</div>'));

?>