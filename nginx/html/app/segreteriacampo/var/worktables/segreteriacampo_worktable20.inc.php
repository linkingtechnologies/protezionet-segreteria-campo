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


if (camila_form_in_update_mode('segreteriacampo_worktable20')) {

    

    $form = new dbform('segreteriacampo_worktable20', 'id');

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

    
    new form_textbox($form, 'organizzazione', 'ORGANIZZAZIONE', true, 30, 255, '');

    
    new form_textbox($form, 'tipologia', 'TIPOLOGIA', false, 30, 255, '');

    
    new form_textbox($form, 'attrezzatura', 'ATTREZZATURA', true, 30, 100, '');

    
    new form_static_listbox($form, 'udm', 'U.D.M.', 'pezzo,metro,litro,Kg', false, '');

    
    new form_integer($form, 'quantita', 'QUANTITA\'', false, 5, 5, '');

    
    new form_textbox($form, 'turno', 'TURNO', false, 30, 10, '');
if (is_object($form->fields['turno'])) $form->fields['turno']->defaultvalue = worktable_get_last_value_from_file('TURNO');
if (is_object($form->fields['turno'])) $form->fields['turno']->write_value_to_file = worktable_get_safe_temp_filename('TURNO');

    
    new form_datetime($form, 'dataoraregistrazione', 'DATA/ORA REGISTRAZIONE', false, '');
if (is_object($form->fields['dataoraregistrazione'])) $form->fields['dataoraregistrazione']->hslots = 60;
if (is_object($form->fields['dataoraregistrazione'])) $form->fields['dataoraregistrazione']->defaultvalue = date('Y-m-d H:i:s');

    
    new form_datetime($form, 'dataorauscitadefinitiva', 'DATA/ORA USCITA DEFINITIVA', false, '');
if (is_object($form->fields['dataorauscitadefinitiva'])) $form->fields['dataorauscitadefinitiva']->hslots = 60;

    
    new form_textbox($form, 'caratteristiche', 'CARATTERISTICHE', false, 30, 255, '');

    
    new form_textbox($form, 'matricola', 'MATRICOLA', false, 30, 255, '');

    
    new form_textbox($form, 'servizio', 'SERVIZIO', false, 30, 255, '');
if (is_object($form->fields['servizio'])) $form->fields['servizio']->defaultvalue = worktable_parse_default_expression('IN ATTESA DI SERVIZIO', $form);

    
    new form_static_listbox($form, 'primaria', 'PRIMARIA', 'NO,SI', false, '');

    
    new form_date($form, 'datainizioattestato', 'DATA INIZIO ATTESTATO', false, '');
if (is_object($form->fields['datainizioattestato'])) $form->fields['datainizioattestato']->defaultvalue = date('Y-m-d');

    
    new form_date($form, 'datafineattestato', 'DATA FINE ATTESTATO', false, '');

    
    new form_textbox($form, 'provincia', 'PROVINCIA', false, 30, 255, '');

    
    new form_textbox($form, 'codiceorganizzazione', 'CODICE ORGANIZZAZIONE', false, 30, 255, '');

    
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

    if (is_object($form->fields['organizzazione']))
{
$form->fields['organizzazione']->autosuggest_table = 'segreteriacampo_worktable24';
$form->fields['organizzazione']->autosuggest_field = 'organizzazione';
$form->fields['organizzazione']->autosuggest_idfield = 'id';
$form->fields['organizzazione']->autosuggest_infofields = 'tipologia,attrezzatura,udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione,note';
$form->fields['organizzazione']->autosuggest_pickfields = 'tipologia,attrezzatura,udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione,note';
$form->fields['organizzazione']->autosuggest_destfields = 'tipologia,attrezzatura,udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione,note';
}
if (is_object($form->fields['tipologia']))
{
$form->fields['tipologia']->autosuggest_table = 'segreteriacampo_worktable24';
$form->fields['tipologia']->autosuggest_field = 'tipologia';
$form->fields['tipologia']->autosuggest_idfield = 'id';
$form->fields['tipologia']->autosuggest_infofields = 'attrezzatura,udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione';
$form->fields['tipologia']->autosuggest_pickfields = 'attrezzatura,udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione';
$form->fields['tipologia']->autosuggest_destfields = 'attrezzatura,udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione';
}
if (is_object($form->fields['attrezzatura']))
{
$form->fields['attrezzatura']->autosuggest_table = 'segreteriacampo_worktable24';
$form->fields['attrezzatura']->autosuggest_field = 'attrezzatura';
$form->fields['attrezzatura']->autosuggest_idfield = 'id';
$form->fields['attrezzatura']->autosuggest_infofields = 'udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia';
$form->fields['attrezzatura']->autosuggest_pickfields = 'udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia';
$form->fields['attrezzatura']->autosuggest_destfields = 'udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia';
}
if (is_object($form->fields['udm']))
{
$form->fields['udm']->autosuggest_table = 'segreteriacampo_worktable24';
$form->fields['udm']->autosuggest_field = 'udm';
$form->fields['udm']->autosuggest_idfield = 'id';
$form->fields['udm']->autosuggest_infofields = 'quantita,turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura';
$form->fields['udm']->autosuggest_pickfields = 'quantita,turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura';
$form->fields['udm']->autosuggest_destfields = 'quantita,turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura';
}
if (is_object($form->fields['quantita']))
{
$form->fields['quantita']->autosuggest_table = 'segreteriacampo_worktable24';
$form->fields['quantita']->autosuggest_field = 'quantita';
$form->fields['quantita']->autosuggest_idfield = 'id';
$form->fields['quantita']->autosuggest_infofields = 'turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm';
$form->fields['quantita']->autosuggest_pickfields = 'turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm';
$form->fields['quantita']->autosuggest_destfields = 'turno,caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm';
}
if (is_object($form->fields['turno']))
{
$form->fields['turno']->autosuggest_table = 'segreteriacampo_worktable24';
$form->fields['turno']->autosuggest_field = 'turno';
$form->fields['turno']->autosuggest_idfield = 'id';
$form->fields['turno']->autosuggest_infofields = 'caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm,quantita';
$form->fields['turno']->autosuggest_pickfields = 'caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm,quantita';
$form->fields['turno']->autosuggest_destfields = 'caratteristiche,provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm,quantita';
}
if (is_object($form->fields['caratteristiche']))
{
$form->fields['caratteristiche']->autosuggest_table = 'segreteriacampo_worktable24';
$form->fields['caratteristiche']->autosuggest_field = 'caratteristiche';
$form->fields['caratteristiche']->autosuggest_idfield = 'id';
$form->fields['caratteristiche']->autosuggest_infofields = 'provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm,quantita,turno';
$form->fields['caratteristiche']->autosuggest_pickfields = 'provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm,quantita,turno';
$form->fields['caratteristiche']->autosuggest_destfields = 'provincia,codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm,quantita,turno';
}
if (is_object($form->fields['provincia']))
{
$form->fields['provincia']->autosuggest_table = 'segreteriacampo_worktable24';
$form->fields['provincia']->autosuggest_field = 'provincia';
$form->fields['provincia']->autosuggest_idfield = 'id';
$form->fields['provincia']->autosuggest_infofields = 'codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm,quantita,turno,caratteristiche';
$form->fields['provincia']->autosuggest_pickfields = 'codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm,quantita,turno,caratteristiche';
$form->fields['provincia']->autosuggest_destfields = 'codiceorganizzazione,note,organizzazione,tipologia,attrezzatura,udm,quantita,turno,caratteristiche';
}
if (is_object($form->fields['codiceorganizzazione']))
{
$form->fields['codiceorganizzazione']->autosuggest_table = 'segreteriacampo_worktable24';
$form->fields['codiceorganizzazione']->autosuggest_field = 'codiceorganizzazione';
$form->fields['codiceorganizzazione']->autosuggest_idfield = 'id';
$form->fields['codiceorganizzazione']->autosuggest_infofields = 'note,organizzazione,tipologia,attrezzatura,udm,quantita,turno,caratteristiche,provincia';
$form->fields['codiceorganizzazione']->autosuggest_pickfields = 'note,organizzazione,tipologia,attrezzatura,udm,quantita,turno,caratteristiche,provincia';
$form->fields['codiceorganizzazione']->autosuggest_destfields = 'note,organizzazione,tipologia,attrezzatura,udm,quantita,turno,caratteristiche,provincia';
}
if (is_object($form->fields['note']))
{
$form->fields['note']->autosuggest_table = 'segreteriacampo_worktable24';
$form->fields['note']->autosuggest_field = 'note';
$form->fields['note']->autosuggest_idfield = 'id';
$form->fields['note']->autosuggest_infofields = 'organizzazione,tipologia,attrezzatura,udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione';
$form->fields['note']->autosuggest_pickfields = 'organizzazione,tipologia,attrezzatura,udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione';
$form->fields['note']->autosuggest_destfields = 'organizzazione,tipologia,attrezzatura,udm,quantita,turno,caratteristiche,provincia,codiceorganizzazione';
}


    $form->process();
    
    $form->draw();

} else {
      $report_fields = 'id,cf_bool_is_special,cf_bool_is_selected,organizzazione,tipologia,attrezzatura,udm,quantita,turno,dataoraregistrazione,dataorauscitadefinitiva,caratteristiche,matricola,servizio,primaria,datainizioattestato,datafineattestato,provincia,codiceorganizzazione,note,created,created_by,created_by_surname,created_by_name,last_upd,last_upd_by,last_upd_by_surname,last_upd_by_name,mod_num';
	  //$admin_report_fields = '';
      $default_fields = 'cf_bool_is_special,cf_bool_is_selected,organizzazione,tipologia,attrezzatura,udm,quantita,turno,dataoraregistrazione,dataorauscitadefinitiva,caratteristiche,matricola,servizio,primaria,datainizioattestato,datafineattestato,provincia,codiceorganizzazione,note';

      if (isset($_REQUEST['camila_rest'])) {
          $report_fields = str_replace('cf_bool_is_special,', '', $report_fields);
          $report_fields = str_replace('cf_bool_is_selected,', '', $report_fields);
          $default_fields = $report_fields;
      }
	  
	  //if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
		//  $default_fields = $admin_report_fields;

      if ($_CAMILA['page']->camila_exporting())
          $mapping = 'created=Data creazione#last_upd=Ultimo aggiornamento#last_upd_by=Utente ult. agg.#last_upd_src=Sorgente Ult. agg.#last_upd_by_name=Nome Utente ult. agg.#last_upd_by_surname=Cognome Utente ult. agg.#mod_num=Num. mod.#id=Cod. riga#created_by=Utente creaz.#created_src=Sorgente creaz.#created_by_surname=Cognome Utente creaz.#created_by_name=Nome Utente creaz.#cf_bool_is_special=contrassegnati come speciali#cf_bool_is_selected=selezionati#organizzazione=ORGANIZZAZIONE#tipologia=TIPOLOGIA#attrezzatura=ATTREZZATURA#udm=U.D.M.#quantita=QUANTITA\'#turno=TURNO#dataoraregistrazione=DATA/ORA REGISTRAZIONE#dataorauscitadefinitiva=DATA/ORA USCITA DEFINITIVA#caratteristiche=CARATTERISTICHE#matricola=MATRICOLA#servizio=SERVIZIO#primaria=PRIMARIA#datainizioattestato=DATA INIZIO ATTESTATO#datafineattestato=DATA FINE ATTESTATO#provincia=PROVINCIA#codiceorganizzazione=CODICE ORGANIZZAZIONE#note=NOTE';
      else
          $mapping = 'created=Data creazione#last_upd=Ultimo aggiornamento#last_upd_by=Utente ult. agg.#last_upd_src=Sorgente Ult. agg.#last_upd_by_name=Nome Utente ult. agg.#last_upd_by_surname=Cognome Utente ult. agg.#mod_num=Num. mod.#id=Cod. riga#created_by=Utente creaz.#created_src=Sorgente creaz.#created_by_surname=Cognome Utente creaz.#created_by_name=Nome Utente creaz.#cf_bool_is_special=contrassegnati come speciali#cf_bool_is_selected=selezionati#organizzazione=ORGANIZZAZIONE#tipologia=TIPOLOGIA#attrezzatura=ATTREZZATURA#udm=U.D.M.#quantita=QUANTITA\'#turno=TURNO#dataoraregistrazione=DATA/ORA REG.#dataorauscitadefinitiva=DATA/ORA USCITA#caratteristiche=CARATTERISTICHE#matricola=MATRICOLA#servizio=SERVIZIO#primaria=PRIMARIA#datainizioattestato=DATA INIZIO ATTEST.#datafineattestato=DATA FINE ATTEST.#provincia=PROVINCIA#codiceorganizzazione=COD. ORGANIZZAZIONE#note=NOTE';

      $filter = '';

      if ($_CAMILA['user_visibility_type']=='personal')
          $filter= ' where created_by='.$_CAMILA['db']->qstr($_CAMILA['user']);
	  
	  if ($_CAMILA['user_visibility_type']=='group')
          $filter= ' where grp='.$_CAMILA['db']->qstr($_CAMILA['user_group']);

	  //if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
	//	  $stmt = 'select ' . $admin_report_fields . ' from segreteriacampo_worktable20';
	  //else
		  $stmt = 'select ' . $report_fields . ' from segreteriacampo_worktable20';
      
      $report = new report($stmt.$filter, '', 'organizzazione', 'asc', $mapping, null, 'id', $default_fields, '', (isset($_REQUEST['camila_rest'])) ? false : true, (isset($_REQUEST['camila_rest'])) ? false : true);

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
$jarr['url'] = "javascript:camila_inline_update_selected('organizzazione','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA ORGANIZZAZIONE...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('tipologia','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA TIPOLOGIA...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('attrezzatura','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA ATTREZZATURA...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = 'udm';
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA U.D.M.';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','pezzo')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'pezzo';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','metro')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'metro';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','litro')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'litro';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','Kg')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'Kg';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('turno','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA TURNO...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('caratteristiche','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA CARATTERISTICHE...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('matricola','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA MATRICOLA...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('servizio','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA SERVIZIO...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = 'primaria';
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA PRIMARIA';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('primaria','NO')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'NO';
$jarr['parent'] = 'primaria';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('primaria','SI')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'SI';
$jarr['parent'] = 'primaria';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('datainizioattestato','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA DATA INIZIO ATTESTATO...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('datafineattestato','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA DATA FINE ATTESTATO...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('provincia','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA PROVINCIA...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('codiceorganizzazione','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA CODICE ORGANIZZAZIONE...';
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