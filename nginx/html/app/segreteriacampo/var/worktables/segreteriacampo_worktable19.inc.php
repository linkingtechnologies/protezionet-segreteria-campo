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


if (camila_form_in_update_mode(segreteriacampo_worktable19)) {

    

    $form = new dbform('segreteriacampo_worktable19', 'id');

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

    
    new form_textbox($form, 'marcaemodello', 'MARCA E MODELLO', true, 30, 255, '');

    
    new form_textbox($form, 'targa', 'TARGA', false, 30, 255, '');

if (is_object($form->fields['targa'])) $form->fields['targa']->autofocus = true;
    
    new form_static_listbox($form, 'x', '4X4', 'n.d.,NO,SI', false, '');

    
    new form_static_listbox($form, 'cassone', 'CASSONE', 'n.d.,No,Aperto,Chiuso', false, '');

    
    new form_integer($form, 'postiasedere', 'POSTI A SEDERE', false, 5, 255, '');

    
    new form_textbox($form, 'turno', 'TURNO', false, 30, 10, '');
if (is_object($form->fields['turno'])) $form->fields['turno']->defaultvalue = worktable_get_last_value_from_file('TURNO');
if (is_object($form->fields['turno'])) $form->fields['turno']->write_value_to_file = worktable_get_safe_temp_filename('TURNO');

    
    new form_decimal($form, 'costotabelleaci', 'COSTO TABELLE ACI', false, 5, 2, '');

    
    new form_textbox($form, 'kminiziomissione', 'KM INIZIO MISSIONE', false, 30, 255, '');

    
    new form_integer($form, 'kmallarrivo', 'KM ALL\'ARRIVO', false, 5, 255, '');

    
    new form_integer($form, 'kmallapartenza', 'KM ALLA PARTENZA', false, 5, 255, '');

    
    new form_datetime($form, 'dataoraregistrazione', 'DATA/ORA REGISTRAZIONE', false, '');
if (is_object($form->fields['dataoraregistrazione'])) $form->fields['dataoraregistrazione']->hslots = 60;
if (is_object($form->fields['dataoraregistrazione'])) $form->fields['dataoraregistrazione']->defaultvalue = date('Y-m-d H:i:s');

    
    new form_datetime($form, 'dataorauscitadefinitiva', 'DATA/ORA USCITA DEFINITIVA', false, '');
if (is_object($form->fields['dataorauscitadefinitiva'])) $form->fields['dataorauscitadefinitiva']->hslots = 60;

    
    new form_textbox($form, 'servizio', 'SERVIZIO', false, 30, 255, '');
if (is_object($form->fields['servizio'])) $form->fields['servizio']->defaultvalue = worktable_parse_default_expression('IN ATTESA DI SERVIZIO', $form);

    
    new form_integer($form, 'numero', 'NUMERO', true, 5, 255, 'unique');
if (is_object($form->fields['numero'])) $form->fields['numero']->defaultvalue = worktable_get_next_autoincrement_value('segreteriacampo_worktable19','numero');

    
    new form_date($form, 'datainizioattestato', 'DATA INIZIO ATTESTATO', false, '');
if (is_object($form->fields['datainizioattestato'])) $form->fields['datainizioattestato']->defaultvalue = date('Y-m-d');

    
    new form_date($form, 'datafineattestato', 'DATA FINE ATTESTATO', false, '');

    
    new form_textbox($form, 'codiceorganizzazione', 'CODICE ORGANIZZAZIONE', false, 30, 255, '');

    
    new form_textbox($form, 'provincia', 'PROVINCIA', true, 30, 255, '');

    
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
$form->fields['organizzazione']->autosuggest_table = 'segreteriacampo_worktable23';
$form->fields['organizzazione']->autosuggest_field = 'organizzazione';
$form->fields['organizzazione']->autosuggest_idfield = 'id';
$form->fields['organizzazione']->autosuggest_infofields = 'marcaemodello,targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note';
$form->fields['organizzazione']->autosuggest_pickfields = 'marcaemodello,targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note';
$form->fields['organizzazione']->autosuggest_destfields = 'marcaemodello,targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note';
}
if (is_object($form->fields['marcaemodello']))
{
$form->fields['marcaemodello']->autosuggest_table = 'segreteriacampo_worktable23';
$form->fields['marcaemodello']->autosuggest_field = 'marcaemodello';
$form->fields['marcaemodello']->autosuggest_idfield = 'id';
$form->fields['marcaemodello']->autosuggest_infofields = 'targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione';
$form->fields['marcaemodello']->autosuggest_pickfields = 'targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione';
$form->fields['marcaemodello']->autosuggest_destfields = 'targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione';
}
if (is_object($form->fields['targa']))
{
$form->fields['targa']->autosuggest_table = 'segreteriacampo_worktable23';
$form->fields['targa']->autosuggest_field = 'targa';
$form->fields['targa']->autosuggest_idfield = 'id';
$form->fields['targa']->autosuggest_infofields = 'x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello';
$form->fields['targa']->autosuggest_pickfields = 'x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello';
$form->fields['targa']->autosuggest_destfields = 'x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello';
}
if (is_object($form->fields['x']))
{
$form->fields['x']->autosuggest_table = 'segreteriacampo_worktable23';
$form->fields['x']->autosuggest_field = 'x';
$form->fields['x']->autosuggest_idfield = 'id';
$form->fields['x']->autosuggest_infofields = 'cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa';
$form->fields['x']->autosuggest_pickfields = 'cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa';
$form->fields['x']->autosuggest_destfields = 'cassone,postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa';
}
if (is_object($form->fields['cassone']))
{
$form->fields['cassone']->autosuggest_table = 'segreteriacampo_worktable23';
$form->fields['cassone']->autosuggest_field = 'cassone';
$form->fields['cassone']->autosuggest_idfield = 'id';
$form->fields['cassone']->autosuggest_infofields = 'postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x';
$form->fields['cassone']->autosuggest_pickfields = 'postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x';
$form->fields['cassone']->autosuggest_destfields = 'postiasedere,turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x';
}
if (is_object($form->fields['postiasedere']))
{
$form->fields['postiasedere']->autosuggest_table = 'segreteriacampo_worktable23';
$form->fields['postiasedere']->autosuggest_field = 'postiasedere';
$form->fields['postiasedere']->autosuggest_idfield = 'id';
$form->fields['postiasedere']->autosuggest_infofields = 'turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x,cassone';
$form->fields['postiasedere']->autosuggest_pickfields = 'turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x,cassone';
$form->fields['postiasedere']->autosuggest_destfields = 'turno,numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x,cassone';
}
if (is_object($form->fields['turno']))
{
$form->fields['turno']->autosuggest_table = 'segreteriacampo_worktable23';
$form->fields['turno']->autosuggest_field = 'turno';
$form->fields['turno']->autosuggest_idfield = 'id';
$form->fields['turno']->autosuggest_infofields = 'numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x,cassone,postiasedere';
$form->fields['turno']->autosuggest_pickfields = 'numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x,cassone,postiasedere';
$form->fields['turno']->autosuggest_destfields = 'numero,codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x,cassone,postiasedere';
}
if (is_object($form->fields['numero']))
{
$form->fields['numero']->autosuggest_table = 'segreteriacampo_worktable23';
$form->fields['numero']->autosuggest_field = 'numero';
$form->fields['numero']->autosuggest_idfield = 'id';
$form->fields['numero']->autosuggest_infofields = 'codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno';
$form->fields['numero']->autosuggest_pickfields = 'codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno';
$form->fields['numero']->autosuggest_destfields = 'codiceorganizzazione,provincia,note,organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno';
}
if (is_object($form->fields['codiceorganizzazione']))
{
$form->fields['codiceorganizzazione']->autosuggest_table = 'segreteriacampo_worktable23';
$form->fields['codiceorganizzazione']->autosuggest_field = 'codiceorganizzazione';
$form->fields['codiceorganizzazione']->autosuggest_idfield = 'id';
$form->fields['codiceorganizzazione']->autosuggest_infofields = 'provincia,note,organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno,numero';
$form->fields['codiceorganizzazione']->autosuggest_pickfields = 'provincia,note,organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno,numero';
$form->fields['codiceorganizzazione']->autosuggest_destfields = 'provincia,note,organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno,numero';
}
if (is_object($form->fields['provincia']))
{
$form->fields['provincia']->autosuggest_table = 'segreteriacampo_worktable23';
$form->fields['provincia']->autosuggest_field = 'provincia';
$form->fields['provincia']->autosuggest_idfield = 'id';
$form->fields['provincia']->autosuggest_infofields = 'note,organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione';
$form->fields['provincia']->autosuggest_pickfields = 'note,organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione';
$form->fields['provincia']->autosuggest_destfields = 'note,organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione';
}
if (is_object($form->fields['note']))
{
$form->fields['note']->autosuggest_table = 'segreteriacampo_worktable23';
$form->fields['note']->autosuggest_field = 'note';
$form->fields['note']->autosuggest_idfield = 'id';
$form->fields['note']->autosuggest_infofields = 'organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia';
$form->fields['note']->autosuggest_pickfields = 'organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia';
$form->fields['note']->autosuggest_destfields = 'organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno,numero,codiceorganizzazione,provincia';
}


    $form->process();
    
    $form->draw();

} else {
      $report_fields = 'id,cf_bool_is_special,cf_bool_is_selected,organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno,costotabelleaci,kminiziomissione,kmallarrivo,kmallapartenza,dataoraregistrazione,dataorauscitadefinitiva,servizio,numero,datainizioattestato,datafineattestato,codiceorganizzazione,provincia,note,created,created_by,created_by_surname,created_by_name,last_upd,last_upd_by,last_upd_by_surname,last_upd_by_name,mod_num';
	  //$admin_report_fields = '';
      $default_fields = 'cf_bool_is_special,cf_bool_is_selected,organizzazione,marcaemodello,targa,x,cassone,postiasedere,turno,costotabelleaci,kminiziomissione,kmallarrivo,kmallapartenza,dataoraregistrazione,dataorauscitadefinitiva,servizio,numero,datainizioattestato,datafineattestato,codiceorganizzazione,provincia,note';

      if (isset($_REQUEST['camila_rest'])) {
          $report_fields = str_replace('cf_bool_is_special,', '', $report_fields);
          $report_fields = str_replace('cf_bool_is_selected,', '', $report_fields);
          $default_fields = $report_fields;
      }
	  
	  //if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
		//  $default_fields = $admin_report_fields;

      if ($_CAMILA['page']->camila_exporting())
          $mapping = 'created=Data creazione#last_upd=Ultimo aggiornamento#last_upd_by=Utente ult. agg.#last_upd_src=Sorgente Ult. agg.#last_upd_by_name=Nome Utente ult. agg.#last_upd_by_surname=Cognome Utente ult. agg.#mod_num=Num. mod.#id=Cod. riga#created_by=Utente creaz.#created_src=Sorgente creaz.#created_by_surname=Cognome Utente creaz.#created_by_name=Nome Utente creaz.#cf_bool_is_special=contrassegnati come speciali#cf_bool_is_selected=selezionati#organizzazione=ORGANIZZAZIONE#marcaemodello=MARCA E MODELLO#targa=TARGA#x=4X4#cassone=CASSONE#postiasedere=POSTI A SEDERE#turno=TURNO#costotabelleaci=COSTO TABELLE ACI#kminiziomissione=KM INIZIO MISSIONE#kmallarrivo=KM ALL\'ARRIVO#kmallapartenza=KM ALLA PARTENZA#dataoraregistrazione=DATA/ORA REGISTRAZIONE#dataorauscitadefinitiva=DATA/ORA USCITA DEFINITIVA#servizio=SERVIZIO#numero=NUMERO#datainizioattestato=DATA INIZIO ATTESTATO#datafineattestato=DATA FINE ATTESTATO#codiceorganizzazione=CODICE ORGANIZZAZIONE#provincia=PROVINCIA#note=NOTE';
      else
          $mapping = 'created=Data creazione#last_upd=Ultimo aggiornamento#last_upd_by=Utente ult. agg.#last_upd_src=Sorgente Ult. agg.#last_upd_by_name=Nome Utente ult. agg.#last_upd_by_surname=Cognome Utente ult. agg.#mod_num=Num. mod.#id=Cod. riga#created_by=Utente creaz.#created_src=Sorgente creaz.#created_by_surname=Cognome Utente creaz.#created_by_name=Nome Utente creaz.#cf_bool_is_special=contrassegnati come speciali#cf_bool_is_selected=selezionati#organizzazione=ORGANIZZAZIONE#marcaemodello=MARCA E MODELLO#targa=TARGA#x=4X4#cassone=CASSONE#postiasedere=POSTI A SEDERE#turno=TURNO#costotabelleaci=COSTO ACI#kminiziomissione=KM INIZIO MISSIONE#kmallarrivo=KM ALL\'ARRIVO#kmallapartenza=KM ALLA PARTENZA#dataoraregistrazione=DATA/ORA REG.#dataorauscitadefinitiva=DATA/ORA USCITA#servizio=SERVIZIO#numero=NUMERO#datainizioattestato=DATA INIZIO ATTEST.#datafineattestato=DATA FINE ATTEST.#codiceorganizzazione=COD. ORGANIZZAZIONE#provincia=PROVINCIA#note=NOTE';

      $filter = '';

      if ($_CAMILA['user_visibility_type']=='personal')
          $filter= ' where created_by='.$_CAMILA['db']->qstr($_CAMILA['user']);
	  
	  if ($_CAMILA['user_visibility_type']=='group')
          $filter= ' where grp='.$_CAMILA['db']->qstr($_CAMILA['user_group']);

	  //if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
	//	  $stmt = 'select ' . $admin_report_fields . ' from segreteriacampo_worktable19';
	  //else
		  $stmt = 'select ' . $report_fields . ' from segreteriacampo_worktable19';
      
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
$jarr['url'] = "javascript:camila_inline_update_selected('marcaemodello','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA MARCA E MODELLO...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('targa','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA TARGA...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = 'x';
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA 4X4';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('x','n.d.')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'n.d.';
$jarr['parent'] = 'x';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('x','NO')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'NO';
$jarr['parent'] = 'x';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('x','SI')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'SI';
$jarr['parent'] = 'x';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = 'cassone';
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA CASSONE';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('cassone','n.d.')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'n.d.';
$jarr['parent'] = 'cassone';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('cassone','No')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'No';
$jarr['parent'] = 'cassone';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('cassone','Aperto')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'Aperto';
$jarr['parent'] = 'cassone';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('cassone','Chiuso')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'Chiuso';
$jarr['parent'] = 'cassone';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('turno','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA TURNO...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('kminiziomissione','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA KM INIZIO MISSIONE...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('servizio','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA SERVIZIO...';
$jarr['parent'] = 'index.php';
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
$jarr['url'] = "javascript:camila_inline_update_selected('codiceorganizzazione','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA CODICE ORGANIZZAZIONE...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('provincia','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA PROVINCIA...';
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