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


if (camila_form_in_update_mode(segreteriacampo_worktable14)) {

    

    $form = new dbform('segreteriacampo_worktable14', 'id');

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

    
    new form_textbox($form, 'prodotto', 'Prodotto', true, 30, 255, 'uppercase');

    
    new form_textbox($form, 'cod', 'Cod.', false, 30, 255, '');

    
    new form_static_listbox($form, 'udm', 'U.d.m.', 'pezzo,bancale,bottiglia,Kg,l,confezione,conf. 250g,conf. 400g,conf. 500g,conf. 200ml,rotolo,flacone,altro (vedi note)', false, '');

    
    new form_textbox($form, 'qtiniziale', 'Q.tà iniziale', true, 30, 255, '');

    
    new form_textbox($form, 'tot', 'TOT.', false, 1, 255, '');
if (is_object($form->fields['tot'])) $form->fields['tot']->updatable = false;

    
    new form_textbox($form, 'c1', 'C1', false, 30, 255, '');

    
    new form_textbox($form, 's', 'S1', false, 30, 255, '');

    
    new form_textbox($form, 'c2', 'C2', false, 30, 255, '');

    
    new form_textbox($form, 's1', 'S2', false, 30, 255, '');

    
    new form_textbox($form, 'c3', 'C3', false, 30, 255, '');

    
    new form_textbox($form, 's2', 'S3', false, 30, 255, '');

    
    new form_textbox($form, 'c4', 'C4', false, 30, 255, '');

    
    new form_textbox($form, 's3', 'S4', false, 30, 255, '');

    
    new form_textbox($form, 'c5', 'C5', false, 30, 255, '');

    
    new form_textbox($form, 's4', 'S5', false, 30, 255, '');

    
    new form_textbox($form, 'c6', 'C6', false, 30, 255, '');

    
    new form_textbox($form, 's5', 'S6', false, 30, 255, '');

    
    new form_textbox($form, 'c7', 'C7', false, 30, 255, '');

    
    new form_textbox($form, 's6', 'S7', false, 30, 255, '');

    
    new form_textbox($form, 'note', 'Note', false, 30, 255, '');

    

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

    

    $form->process();
    
    $form->draw();

} else {
      $report_fields = 'id,cf_bool_is_special,cf_bool_is_selected,prodotto,cod,udm,qtiniziale,tot as cf_formula_tot,c1,s,c2,s1,c3,s2,c4,s3,c5,s4,c6,s5,c7,s6,note,created,created_by,created_by_surname,created_by_name,last_upd,last_upd_by,last_upd_by_surname,last_upd_by_name,mod_num';
	  //$admin_report_fields = '';
      $default_fields = 'cf_bool_is_special,cf_bool_is_selected,prodotto,cod,udm,qtiniziale,tot as cf_formula_tot,c1,s,c2,s1,c3,s2,c4,s3,c5,s4,c6,s5,c7,s6,note';

      if (isset($_REQUEST['camila_rest'])) {
          $report_fields = str_replace('cf_bool_is_special,', '', $report_fields);
          $report_fields = str_replace('cf_bool_is_selected,', '', $report_fields);
          $default_fields = $report_fields;
      }
	  
	  //if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
		//  $default_fields = $admin_report_fields;

      if ($_CAMILA['page']->camila_exporting())
          $mapping = 'created=Data creazione#last_upd=Ultimo aggiornamento#last_upd_by=Utente ult. agg.#last_upd_src=Sorgente Ult. agg.#last_upd_by_name=Nome Utente ult. agg.#last_upd_by_surname=Cognome Utente ult. agg.#mod_num=Num. mod.#id=Cod. riga#created_by=Utente creaz.#created_src=Sorgente creaz.#created_by_surname=Cognome Utente creaz.#created_by_name=Nome Utente creaz.#cf_bool_is_special=contrassegnati come speciali#cf_bool_is_selected=selezionati#prodotto=Prodotto#cod=Cod.#udm=U.d.m.#qtiniziale=Q.tà iniziale#cf_formula_tot=TOT.#totas cf_formula_tot=TOT.#c1=C1#s=S1#c2=C2#s1=S2#c3=C3#s2=S3#c4=C4#s3=S4#c5=C5#s4=S5#c6=C6#s5=S6#c7=C7#s6=S7#note=Note';
      else
          $mapping = 'created=Data creazione#last_upd=Ultimo aggiornamento#last_upd_by=Utente ult. agg.#last_upd_src=Sorgente Ult. agg.#last_upd_by_name=Nome Utente ult. agg.#last_upd_by_surname=Cognome Utente ult. agg.#mod_num=Num. mod.#id=Cod. riga#created_by=Utente creaz.#created_src=Sorgente creaz.#created_by_surname=Cognome Utente creaz.#created_by_name=Nome Utente creaz.#cf_bool_is_special=contrassegnati come speciali#cf_bool_is_selected=selezionati#prodotto=Prodotto#cod=Cod.#udm=U.d.m.#qtiniziale=Q.tà iniziale#cf_formula_tot=TOT.#tot as cf_formula_tot=TOT.#c1=C1#s=S1#c2=C2#s1=S2#c3=C3#s2=S3#c4=C4#s3=S4#c5=C5#s4=S5#c6=C6#s5=S6#c7=C7#s6=S7#note=Note';

      $filter = '';

      if ($_CAMILA['user_visibility_type']=='personal')
          $filter= ' where created_by='.$_CAMILA['db']->qstr($_CAMILA['user']);
	  
	  if ($_CAMILA['user_visibility_type']=='group')
          $filter= ' where grp='.$_CAMILA['db']->qstr($_CAMILA['user_group']);

	  //if ($_CAMILA['adm_user_group'] == CAMILA_ADM_USER_GROUP)
	//	  $stmt = 'select ' . $admin_report_fields . ' from segreteriacampo_worktable14';
	  //else
		  $stmt = 'select ' . $report_fields . ' from segreteriacampo_worktable14';
      
      $report = new report($stmt.$filter, '', 'prodotto', 'asc', $mapping, null, 'id', $default_fields, '', (isset($_REQUEST['camila_rest'])) ? false : true, (isset($_REQUEST['camila_rest'])) ? false : true);

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

      $report->formulas=Array('cf_formula_tot'=>'${Q.tà iniziale}+${C1}-${S1}+${C2}-${S2}+${C3}-${S3}+${C4}-${S4}+${C5}-${S5}+${C6}-${S6}+${C7}-${S7}');
      $report->queries=Array();

      $jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('prodotto','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA Prodotto...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('cod','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA Cod....';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = 'udm';
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA U.d.m.';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','pezzo')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'pezzo';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','bancale')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'bancale';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','bottiglia')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'bottiglia';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','Kg')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'Kg';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','l')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'l';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','confezione')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'confezione';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','conf. 250g')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'conf. 250g';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','conf. 400g')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'conf. 400g';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','conf. 500g')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'conf. 500g';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','conf. 200ml')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'conf. 200ml';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','rotolo')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'rotolo';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','flacone')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'flacone';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('udm','altro (vedi note)')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'altro (vedi note)';
$jarr['parent'] = 'udm';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('qtiniziale','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA Q.tà iniziale...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('c1','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA C1...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('s','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA S1...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('c2','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA C2...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('s1','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA S2...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('c3','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA C3...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('s2','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA S3...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('c4','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA C4...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('s3','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA S4...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('c5','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA C5...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('s4','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA S5...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('c6','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA C6...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('s5','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA S6...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('c7','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA C7...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('s6','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA S7...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;
$jarr=Array();
$jarr['url'] = "javascript:camila_inline_update_selected('note','')";
$jarr['visible'] = 'yes';
$jarr['short_title'] = 'MODIFICA Note...';
$jarr['parent'] = 'index.php';
$report->menuitems[]=$jarr;


      $report->process();
      $report->draw();

}
?>