<?php
$_CAMILA['page']->camila_worktable = true;

$wt_id = substr($_SERVER['PHP_SELF'], 12, -4);

if (intval($wt_id) > 0)
    $_CAMILA['page']->camila_worktable_id = $wt_id;

function worktable_get_safe_temp_filename($name) {
    global $_CAMILA;
    return CAMILA_TMP_DIR . '/lastval_' . $_CAMILA['lang'] . '_' . preg_replace('/[^a-z]/', '', strtolower($name));
}

function worktable_get_last_value_from_file($name) {
    return file_get_contents(worktable_get_safe_temp_filename($name));
}


function worktable_get_next_autoincrement_value($table, $column) {

    global $_CAMILA;

    $result = $_CAMILA['db']->Execute('select max('.$column.') as id from ' . $table);
    if ($result === false)
        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

    return intval($result->fields['id']) + 1;

}


function worktable_parse_default_expression($expression, $form) {
    return camila_parse_default_expression($expression, $form->fields['id']->defaultvalue);
}


if (camila_form_in_update_mode(segreteriacampo_worktable7)) {

    

    $form = new dbform('segreteriacampo_worktable7', 'id');

    if ($_CAMILA['adm_user_group'] != CAMILA_ADM_USER_GROUP)
    {
        $form->caninsert = true;
        $form->candelete = true;
        $form->canupdate = true;
    }
    else
    if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
    {
        $form->caninsert = true;
        $form->candelete = true;
        $form->canupdate = true;
    }

    $form->drawrules = true;
    $form->drawheadersubmitbutton = true;

    new form_textbox($form, 'id', camila_get_translation('camila.worktable.field.id'));
    if (is_object($form->fields['id'])) {
        if ($_REQUEST['camila_update'] == 'new' && !isset($_REQUEST['camila_phpform_sent'])) {
            $_CAMILA['db_genid'] = $_CAMILA['db']->GenID(CAMILA_APPLICATION_PREFIX.'worktableseq', 100000);
            $form->fields['id']->defaultvalue = $_CAMILA['db_genid'];
        }
        $form->fields['id']->updatable = false;
        $form->fields['id']->forcedraw = true;
    }

    
    new form_textbox($form, 'numeroradio', 'NUMERO RADIO', false, 30, 255, '');

    
    new form_integer($form, 'numeroconsegna', 'NUMERO CONSEGNA', false, 5, 255, '');
if (is_object($form->fields['numeroconsegna'])) $form->fields['numeroconsegna']->defaultvalue = worktable_get_next_autoincrement_value('segreteriacampo_worktable7','numeroconsegna');
if ($_CAMILA['adm_user_group'] != CAMILA_ADM_USER_GROUP && is_object($form->fields['numeroconsegna'])) $form->fields['numeroconsegna']->updatable = false;

    
    new form_date($form, 'giorno', 'GIORNO', false, '');
if (is_object($form->fields['giorno'])) $form->fields['giorno']->defaultvalue = date('Y-m-d H:i:s');

    
    new form_datetime($form, 'dataoraconsegna', 'DATA/ORA CONSEGNA', false, '');
if (is_object($form->fields['dataoraconsegna'])) $form->fields['dataoraconsegna']->hslots = 60;
if (is_object($form->fields['dataoraconsegna'])) $form->fields['dataoraconsegna']->defaultvalue = date('Y-m-d H:i:s');

    
    new form_textbox($form, 'cognome', 'COGNOME', false, 30, 255, '');

    
    new form_textbox($form, 'nome', 'NOME', false, 30, 255, '');

    
    new form_textbox($form, 'organizzazioneente', 'ORGANIZZAZIONE/ENTE', false, 30, 255, '');

    
    new form_textbox($form, 'ruolofunzione', 'RUOLO/FUNZIONE', false, 30, 255, '');

    
    new form_textbox($form, 'cellulare', 'CELLULARE', false, 30, 255, '');

    
    new form_textbox($form, 'codicevolontario', 'CODICE VOLONTARIO', false, 30, 255, '');

    
    new form_datetime($form, 'dataorarestituzione', 'DATA/ORA RESTITUZIONE', false, '');
if (is_object($form->fields['dataorarestituzione'])) $form->fields['dataorarestituzione']->hslots = 60;

    
    new form_textbox($form, 'note', 'NOTE', false, 30, 255, '');

    

    if (CAMILA_WORKTABLE_SPECIAL_ICON_ENABLED || $_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
        new form_static_listbox($form, 'cf_bool_is_selected', camila_get_translation('camila.worktable.field.selected'), camila_get_translation('camila.worktable.options.noyes'));

    if (CAMILA_WORKTABLE_SELECTED_ICON_ENABLED || $_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
        new form_static_listbox($form, 'cf_bool_is_special', camila_get_translation('camila.worktable.field.special'), camila_get_translation('camila.worktable.options.noyes'));

    if ($_REQUEST['camila_update'] != 'new') {

    new form_datetime($form, 'created', camila_get_translation('camila.worktable.field.created'));
    if (is_object($form->fields['created'])) $form->fields['created']->updatable = false;

    new form_textbox($form, 'created_by', camila_get_translation('camila.worktable.field.created_by'));
    if (is_object($form->fields['created_by'])) $form->fields['created_by']->updatable = false;

    new form_textbox($form, 'created_by_surname', camila_get_translation('camila.worktable.field.created_by_surname'));
    if (is_object($form->fields['created_by_surname'])) $form->fields['created_by_surname']->updatable = false;

    new form_textbox($form, 'created_by_name', camila_get_translation('camila.worktable.field.created_by_name'));
    if (is_object($form->fields['created_by_name'])) $form->fields['created_by_name']->updatable = false;

    new form_static_listbox($form, 'created_src', camila_get_translation('camila.worktable.field.created_src'), camila_get_translation('camila.worktable.options.recordmodsrc'));
    if (is_object($form->fields['created_src'])) $form->fields['created_src']->updatable = false;

    new form_datetime($form, 'last_upd', camila_get_translation('camila.worktable.field.last_upd'));
    if (is_object($form->fields['last_upd'])) $form->fields['last_upd']->updatable = false;

    new form_textbox($form, 'last_upd_by', camila_get_translation('camila.worktable.field.last_upd_by'));
    if (is_object($form->fields['last_upd_by'])) $form->fields['last_upd_by']->updatable = false;

    new form_textbox($form, 'last_upd_by_surname', camila_get_translation('camila.worktable.field.last_upd_by_surname'));
    if (is_object($form->fields['last_upd_by_surname'])) $form->fields['last_upd_by_surname']->updatable = false;

    new form_textbox($form, 'last_upd_by_name', camila_get_translation('camila.worktable.field.last_upd_by_name'));
    if (is_object($form->fields['last_upd_by_name'])) $form->fields['last_upd_by_name']->updatable = false;

    new form_textbox($form, 'last_upd_by_name', camila_get_translation('camila.worktable.field.last_upd_by_name'));
    if (is_object($form->fields['last_upd_by_name'])) $form->fields['last_upd_by_name']->updatable = false;

    new form_static_listbox($form, 'last_upd_src', camila_get_translation('camila.worktable.field.last_upd_src'), camila_get_translation('camila.worktable.options.recordmodsrc'));
    if (is_object($form->fields['last_upd_src'])) $form->fields['last_upd_src']->updatable = false;

    new form_textbox($form, 'mod_num', camila_get_translation('camila.worktable.field.mod_num'));
    if (is_object($form->fields['mod_num'])) $form->fields['mod_num']->updatable = false;


}

    if (is_object($form->fields['cognome']))
{
$form->fields['cognome']->autosuggest_table = 'segreteriacampo_worktable18';
$form->fields['cognome']->autosuggest_field = 'cognome';
$form->fields['cognome']->autosuggest_idfield = 'id';
$form->fields['cognome']->autosuggest_infofields = 'nome,organizzazione,cellulare,codicevolontario';
$form->fields['cognome']->autosuggest_pickfields = 'nome,organizzazione,cellulare,codicevolontario';
$form->fields['cognome']->autosuggest_destfields = 'nome,organizzazioneente,cellulare,codicevolontario';
}
if (is_object($form->fields['nome']))
{
$form->fields['nome']->autosuggest_table = 'segreteriacampo_worktable18';
$form->fields['nome']->autosuggest_field = 'nome';
$form->fields['nome']->autosuggest_idfield = 'id';
$form->fields['nome']->autosuggest_infofields = 'organizzazione,cellulare,codicevolontario,cognome';
$form->fields['nome']->autosuggest_pickfields = 'organizzazione,cellulare,codicevolontario,cognome';
$form->fields['nome']->autosuggest_destfields = 'organizzazioneente,cellulare,codicevolontario,cognome';
}
if (is_object($form->fields['organizzazioneente']))
{
$form->fields['organizzazioneente']->autosuggest_table = 'segreteriacampo_worktable18';
$form->fields['organizzazioneente']->autosuggest_field = 'organizzazione';
$form->fields['organizzazioneente']->autosuggest_idfield = 'id';
$form->fields['organizzazioneente']->autosuggest_infofields = 'cellulare,codicevolontario,cognome,nome';
$form->fields['organizzazioneente']->autosuggest_pickfields = 'cellulare,codicevolontario,cognome,nome';
$form->fields['organizzazioneente']->autosuggest_destfields = 'cellulare,codicevolontario,cognome,nome';
}
if (is_object($form->fields['cellulare']))
{
$form->fields['cellulare']->autosuggest_table = 'segreteriacampo_worktable18';
$form->fields['cellulare']->autosuggest_field = 'cellulare';
$form->fields['cellulare']->autosuggest_idfield = 'id';
$form->fields['cellulare']->autosuggest_infofields = 'codicevolontario,cognome,nome,organizzazione';
$form->fields['cellulare']->autosuggest_pickfields = 'codicevolontario,cognome,nome,organizzazione';
$form->fields['cellulare']->autosuggest_destfields = 'codicevolontario,cognome,nome,organizzazioneente';
}
if (is_object($form->fields['codicevolontario']))
{
$form->fields['codicevolontario']->autosuggest_table = 'segreteriacampo_worktable18';
$form->fields['codicevolontario']->autosuggest_field = 'codicevolontario';
$form->fields['codicevolontario']->autosuggest_idfield = 'id';
$form->fields['codicevolontario']->autosuggest_infofields = 'cognome,nome,organizzazione,cellulare';
$form->fields['codicevolontario']->autosuggest_pickfields = 'cognome,nome,organizzazione,cellulare';
$form->fields['codicevolontario']->autosuggest_destfields = 'cognome,nome,organizzazioneente,cellulare';
}


    $form->process();
    
    $form->draw();

} else {
      $report_fields = 'id,cf_bool_is_special,cf_bool_is_selected,numeroradio,numeroconsegna,giorno,dataoraconsegna,cognome,nome,organizzazioneente,ruolofunzione,cellulare,codicevolontario,dataorarestituzione,note,created,created_by,created_by_surname,created_by_name,last_upd,last_upd_by,last_upd_by_surname,last_upd_by_name,mod_num';
	  //$admin_report_fields = '';
      $default_fields = 'cf_bool_is_special,cf_bool_is_selected,numeroradio,numeroconsegna,giorno,dataoraconsegna,cognome,nome,organizzazioneente,ruolofunzione,cellulare,codicevolontario,dataorarestituzione,note';

      if (isset($_REQUEST['camila_rest'])) {
          $report_fields = str_replace('cf_bool_is_special,', '', $report_fields);
          $report_fields = str_replace('cf_bool_is_selected,', '', $report_fields);
          $default_fields = $report_fields;
      }
	  
	  //if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
		//  $default_fields = $admin_report_fields;

      if ($_CAMILA['page']->camila_exporting())
          $mapping = 'created=Data creazione#last_upd=Ultimo aggiornamento#last_upd_by=Utente ult. agg.#last_upd_src=Sorgente Ult. agg.#last_upd_by_name=Nome Utente ult. agg.#last_upd_by_surname=Cognome Utente ult. agg.#mod_num=Num. mod.#id=Cod. riga#created_by=Utente creaz.#created_src=Sorgente creaz.#created_by_surname=Cognome Utente creaz.#created_by_name=Nome Utente creaz.#cf_bool_is_special=contrassegnati come speciali#cf_bool_is_selected=selezionati#numeroradio=NUMERO RADIO#numeroconsegna=NUMERO CONSEGNA#giorno=GIORNO#dataoraconsegna=DATA/ORA CONSEGNA#cognome=COGNOME#nome=NOME#organizzazioneente=ORGANIZZAZIONE/ENTE#ruolofunzione=RUOLO/FUNZIONE#cellulare=CELLULARE#codicevolontario=CODICE VOLONTARIO#dataorarestituzione=DATA/ORA RESTITUZIONE#note=NOTE';
      else
          $mapping = 'created=Data creazione#last_upd=Ultimo aggiornamento#last_upd_by=Utente ult. agg.#last_upd_src=Sorgente Ult. agg.#last_upd_by_name=Nome Utente ult. agg.#last_upd_by_surname=Cognome Utente ult. agg.#mod_num=Num. mod.#id=Cod. riga#created_by=Utente creaz.#created_src=Sorgente creaz.#created_by_surname=Cognome Utente creaz.#created_by_name=Nome Utente creaz.#cf_bool_is_special=contrassegnati come speciali#cf_bool_is_selected=selezionati#numeroradio=NUMERO RADIO#numeroconsegna=NUM. CONSEGNA#giorno=GIORNO#dataoraconsegna=DATA/ORA CONSEGNA#cognome=COGNOME#nome=NOME#organizzazioneente=ORGANIZZAZIONE/ENTE#ruolofunzione=RUOLO/FUNZIONE#cellulare=CELLULARE#codicevolontario=COD. VOLONTARIO#dataorarestituzione=DATA/ORA REST.#note=NOTE';

      $filter = '';

      if ($_CAMILA['user_visibility_type']=='personal')
          $filter= ' where created_by='.$_CAMILA['db']->qstr($_CAMILA['user']);
	  
	  if ($_CAMILA['user_visibility_type']=='group')
          $filter= ' where grp='.$_CAMILA['db']->qstr($_CAMILA['user_group']);

	  //if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
	//	  $stmt = 'select ' . $admin_report_fields . ' from segreteriacampo_worktable7';
	  //else
		  $stmt = 'select ' . $report_fields . ' from segreteriacampo_worktable7';
      
      $report = new report($stmt.$filter, '', 'numeroconsegna', 'desc', $mapping, null, 'id', $default_fields, '', (isset($_REQUEST['camila_rest'])) ? false : true, (isset($_REQUEST['camila_rest'])) ? false : true);

      if (true && !isset($_REQUEST['camila_rest'])) {
          $report->additional_links = Array(camila_get_translation('camila.report.insertnew') => basename($_SERVER['PHP_SELF']) . '?camila_update=new');

          $myImage1 = new CHAW_image(CAMILA_IMG_DIR . 'wbmp/add.wbmp', CAMILA_IMG_DIR . 'png/add.png', '-');
          //$report->additional_links_images = Array(camila_get_translation('camila.report.insertnew') => $myImage1);
		  $report->additional_links_css_classes = Array(camila_get_translation('camila.report.insertnew') => 'btn '.CAMILA_UI_DEFAULT_BTN_SIZE.' btn-default btn-primary');

          if (($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP) || CAMILA_WORKTABLE_IMPORT_ENABLED)          
          $report->additional_links[camila_get_translation('camila.worktable.import')] = 'cf_worktable_wizard_step4.php?camila_custom=' . $wt_id . '&camila_returl=' . urlencode($_SERVER['PHP_SELF']);
      }

      if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP) {
          $report->additional_links[camila_get_translation('camila.worktable.rebuild')] = 'cf_worktable_admin.php?camila_custom=' . $wt_id . '&camila_worktable_op=rebuild' . '&camila_returl=' . urlencode($_SERVER['PHP_SELF']);
          $report->additional_links[camila_get_translation('camila.worktable.reconfig')] = 'cf_worktable_wizard_step2.php?camila_custom=' . $wt_id . '&camila_returl=' . urlencode($_SERVER['PHP_SELF']);
      }

      if (CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_ENABLED) {
          $report->additional_links[camila_get_translation('camila.worktable.confirm')] = basename($_SERVER['PHP_SELF']) . '?camila_visible_cols_only=y&camila_worktable_export=dataonly&camila_pagnum=-1&camila_export_filename=WORKTABLE&camila_export_action=sendmail&hidden=camila_xls&camila_export_format=camila_xls&camila_xls=Esporta';

          $myImage1 = new CHAW_image(CAMILA_IMG_DIR . 'wbmp/accept.wbmp', CAMILA_IMG_DIR . 'png/accept.png', '-');
          $report->additional_links_images[camila_get_translation('camila.worktable.confirm')]=$myImage1;

      }

      $report->formulas=Array();
      $report->queries=Array();

      $jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('numeroradio','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA NUMERO RADIO...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('giorno','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA GIORNO...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('cognome','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA COGNOME...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('nome','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA NOME...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('organizzazioneente','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA ORGANIZZAZIONE/ENTE...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('ruolofunzione','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA RUOLO/FUNZIONE...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('cellulare','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA CELLULARE...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('codicevolontario','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA CODICE VOLONTARIO...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('note','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA NOTE...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;


      $report->process();
      $report->draw();

}
?>