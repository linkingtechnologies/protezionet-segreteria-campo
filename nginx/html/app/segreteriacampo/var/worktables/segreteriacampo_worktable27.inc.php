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


if (camila_form_in_update_mode('segreteriacampo_worktable27')) {

    

    $form = new dbform('segreteriacampo_worktable27', 'id');

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

    
    new form_integer($form, 'ordine', 'ORDINE', true, 5, 255, '');
if (is_object($form->fields['ordine'])) $form->fields['ordine']->help = 'Numero utilizzato per ordinare i servizi negli elenchi (es. movimentazione risorse)';

    
    new form_textbox($form, 'lettera', 'LETTERA', false, 30, 1, '');
if (is_object($form->fields['lettera'])) $form->fields['lettera']->help = 'Verrà utilizzata per identificare il servizio sulla mappa';

    
    new form_textbox($form, 'nome', 'NOME', true, 30, 255, '');

    
    new form_textarea($form, 'descrizione', 'DESCRIZIONE', false, 10, 80, 255, '');

    
    new form_textbox($form, 'comune', 'COMUNE', false, 30, 255, '');

    
    new form_textbox($form, 'provincia', 'PROVINCIA', false, 30, 255, '');

    
    new form_textbox($form, 'indirizzo', 'INDIRIZZO', false, 30, 255, '');

    
    new form_textbox($form, 'latitudine', 'LATITUDINE', false, 30, 255, '');

    
    new form_textbox($form, 'longitudine', 'LONGITUDINE', false, 30, 255, '');

    
    new form_datetime($form, 'inizio', 'INIZIO', false, '');
if (is_object($form->fields['inizio'])) $form->fields['inizio']->hslots = 60;

    
    new form_datetime($form, 'fine', 'FINE', false, '');
if (is_object($form->fields['fine'])) $form->fields['fine']->hslots = 60;

    
    new form_textarea($form, 'operatoriasupporto', 'OPERATORI A SUPPORTO', false, 10, 80, 255, '');

    
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

    if (is_object($form->fields['comune']))
{
$form->fields['comune']->autosuggest_table = 'segreteriacampo_it_city';
$form->fields['comune']->autosuggest_field = 'name';
$form->fields['comune']->autosuggest_idfield = 'id';
$form->fields['comune']->autosuggest_infofields = 'province,lat,lon';
$form->fields['comune']->autosuggest_pickfields = 'province,lat,lon';
$form->fields['comune']->autosuggest_destfields = 'provincia,latitudine,longitudine';
}
if (is_object($form->fields['provincia']))
{
$form->fields['provincia']->autosuggest_table = 'segreteriacampo_it_city';
$form->fields['provincia']->autosuggest_field = 'province';
$form->fields['provincia']->autosuggest_idfield = 'id';
$form->fields['provincia']->autosuggest_infofields = 'lat,lon,name';
$form->fields['provincia']->autosuggest_pickfields = 'lat,lon,name';
$form->fields['provincia']->autosuggest_destfields = 'latitudine,longitudine,comune';
}
if (is_object($form->fields['latitudine']))
{
$form->fields['latitudine']->autosuggest_table = 'segreteriacampo_it_city';
$form->fields['latitudine']->autosuggest_field = 'lat';
$form->fields['latitudine']->autosuggest_idfield = 'id';
$form->fields['latitudine']->autosuggest_infofields = 'lon,name,province';
$form->fields['latitudine']->autosuggest_pickfields = 'lon,name,province';
$form->fields['latitudine']->autosuggest_destfields = 'longitudine,comune,provincia';
}
if (is_object($form->fields['longitudine']))
{
$form->fields['longitudine']->autosuggest_table = 'segreteriacampo_it_city';
$form->fields['longitudine']->autosuggest_field = 'lon';
$form->fields['longitudine']->autosuggest_idfield = 'id';
$form->fields['longitudine']->autosuggest_infofields = 'name,province,lat';
$form->fields['longitudine']->autosuggest_pickfields = 'name,province,lat';
$form->fields['longitudine']->autosuggest_destfields = 'comune,provincia,latitudine';
}


    $form->process();
    
    $form->draw();

} else {
      $report_fields = 'id,cf_bool_is_special,cf_bool_is_selected,ordine,lettera,nome,descrizione,comune,provincia,indirizzo,latitudine,longitudine,inizio,fine,operatoriasupporto,note,created,created_by,created_by_surname,created_by_name,last_upd,last_upd_by,last_upd_by_surname,last_upd_by_name,mod_num';
	  //$admin_report_fields = '';
      $default_fields = 'cf_bool_is_special,cf_bool_is_selected,ordine,lettera,nome,descrizione,comune,provincia,indirizzo,latitudine,longitudine,inizio,fine,operatoriasupporto,note';

      if (isset($_REQUEST['camila_rest'])) {
          $report_fields = str_replace('cf_bool_is_special,', '', $report_fields);
          $report_fields = str_replace('cf_bool_is_selected,', '', $report_fields);
          $default_fields = $report_fields;
      }
	  
	  //if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
		//  $default_fields = $admin_report_fields;

      if ($_CAMILA['page']->camila_exporting())
          $mapping = 'created=Data creazione#last_upd=Ultimo aggiornamento#last_upd_by=Utente ult. agg.#last_upd_src=Sorgente Ult. agg.#last_upd_by_name=Nome Utente ult. agg.#last_upd_by_surname=Cognome Utente ult. agg.#mod_num=Num. mod.#id=Cod. riga#created_by=Utente creaz.#created_src=Sorgente creaz.#created_by_surname=Cognome Utente creaz.#created_by_name=Nome Utente creaz.#cf_bool_is_special=contrassegnati come speciali#cf_bool_is_selected=selezionati#ordine=ORDINE#lettera=LETTERA#nome=NOME#descrizione=DESCRIZIONE#comune=COMUNE#provincia=PROVINCIA#indirizzo=INDIRIZZO#latitudine=LATITUDINE#longitudine=LONGITUDINE#inizio=INIZIO#fine=FINE#operatoriasupporto=OPERATORI A SUPPORTO#note=NOTE';
      else
          $mapping = 'created=Data creazione#last_upd=Ultimo aggiornamento#last_upd_by=Utente ult. agg.#last_upd_src=Sorgente Ult. agg.#last_upd_by_name=Nome Utente ult. agg.#last_upd_by_surname=Cognome Utente ult. agg.#mod_num=Num. mod.#id=Cod. riga#created_by=Utente creaz.#created_src=Sorgente creaz.#created_by_surname=Cognome Utente creaz.#created_by_name=Nome Utente creaz.#cf_bool_is_special=contrassegnati come speciali#cf_bool_is_selected=selezionati#ordine=ORDINE#lettera=LETTERA#nome=NOME#descrizione=DESCRIZIONE#comune=COMUNE#provincia=PROVINCIA#indirizzo=INDIRIZZO#latitudine=LATITUDINE#longitudine=LONGITUDINE#inizio=INIZIO#fine=FINE#operatoriasupporto=OPERATORI A SUPPORTO#note=NOTE';

      $filter = '';

      if ($_CAMILA['user_visibility_type']=='personal')
          $filter= ' where created_by='.$_CAMILA['db']->qstr($_CAMILA['user']);
	  
	  if ($_CAMILA['user_visibility_type']=='group')
          $filter= ' where grp='.$_CAMILA['db']->qstr($_CAMILA['user_group']);

	  //if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
	//	  $stmt = 'select ' . $admin_report_fields . ' from segreteriacampo_worktable27';
	  //else
		  $stmt = 'select ' . $report_fields . ' from segreteriacampo_worktable27';
      
      $report = new report($stmt.$filter, '', 'ordine', 'asc', $mapping, null, 'id', $default_fields, '', (isset($_REQUEST['camila_rest'])) ? false : true, (isset($_REQUEST['camila_rest'])) ? false : true);

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
$jarr['url'] = "javascript:camila_inline_update_selected('lettera','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA LETTERA...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('nome','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA NOME...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('comune','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA COMUNE...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('provincia','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA PROVINCIA...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('indirizzo','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA INDIRIZZO...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('latitudine','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA LATITUDINE...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('longitudine','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA LONGITUDINE...';
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