<?php
/*  This File is part of Camila PHP Framework
    Copyright (C) 2006-2017 Umberto Bresciani

    Camila PHP Framework is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Camila PHP Framework is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Camila PHP Framework. If not, see <http://www.gnu.org/licenses/>. */


function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

class WorkTableResourceMover
{
    public $workTableFolder = "worktable";
    public $resourceWorkTableServiceColumn = "SERVIZIO";
	public $resourceWorkTableGroupColumn = "ORGANIZZAZIONE";
	//public $resourceWorkTableLastStatusColumn = "DATA/ORA USCITA";
	//public $resourceWorkTableLastStatusColumnName;
    public $serviceWorkTable = "SERVIZI";
	public $serviceWorkTableNameColumn = "NOME";
	//public $serviceStartServiceValue = "IN ATTESA DI SERVIZIO";
	//public $serviceEndServiceValue = "FINE SERVIZIO";
	public $resourceWorkTableServiceColumnName;
    public $resourceWorkTableId;
	public $client;

    function WorkTableResourceMover()
    {
		global $_CAMILA;
   }

	function insertRow($camilaWT, $worktableName, $lang, $fields, $values)
	{
		global $_CAMILA;
		$now = $_CAMILA['db']->BindTimeStamp(date("Y-m-d H:i:s", time()));
		
		$fields2 = Array();
		$fields2[]='id';
		$fields2[]='last_upd';
		$fields2[]='last_upd_by';
		$fields2[]='last_upd_src';
		$fields2[]='last_upd_by_surname';
		$fields2[]='last_upd_by_name';

		$values[]=$_CAMILA['db']->GenID(CAMILA_APPLICATION_PREFIX.'worktableseq', 100000);
		$values[]=$now;
		$values[]=$_CAMILA['user'];
		$values[]='application';
		$values[]=$_CAMILA['user_surname'];
		$values[]=$_CAMILA['user_name'];

		$query = 'INSERT INTO ${' . $worktableName . '} (';
		$count = 0;
		foreach($fields as $val) {
			if ($count>0)
				$query .= ',';
			$query .= '${'. $worktableName . '.' . $val .'}' ;
			$count++;
		}
		foreach($fields2 as $val) {
			$query .= ',' . $val;
		}
		$query .= ') VALUES (';
		$count = 0;
		foreach($values as $val) {
			if ($count>0)
				$query .= ',';
			$query .= $camilaWT->db->qstr($val);
			$count++;
		}
		$query .= ')';

		//echo $query;
		$result = $camilaWT->startExecuteQuery($query,false);

		return $result;
	}

    function getResourceList($resource, $filter, $orderBy, $col1, $col2)
    {
		$obj = $this->client->query($resource, 'it', $filter, $orderBy);

        $col1 = $this->getColId($obj->table->cols, $col1);
        $col2 = $this->getColId($obj->table->cols, $col2);
        $col3 = $this->getColId($obj->table->cols, $orderBy);
        $col4 = $this->getColId($obj->table->cols, "id");

        $r         = new stdClass();
        $group     = new stdClass();
        $r->groups = array();
        $labels    = array();
        $values    = array();
        $lastGroup = '';
        $count     = 0;

        foreach ($obj->table->rows as $name => $value) {
            $count++;
            if ($lastGroup != '' && $lastGroup != $value->c[$col3]->v) {
                $group         = new stdClass();
                $group->name   = $lastGroup;
                $group->labels = $labels;
                $group->values = $values;
                $r->groups[]   = $group;
                $labels        = array();
                $values        = array();
            }

            $labels[]  = $value->c[$col1]->v . ' ' . $value->c[$col2]->v;
            $values[]  = $value->c[0]->v;
            $lastGroup = $value->c[$col3]->v;
        }
        $group         = new stdClass();
        $group->name   = $lastGroup;
        $group->labels = $labels;
        $group->values = $values;
        $r->groups[]   = $group;
        $r->totalItems = $count;
        return $r;
    }

    function getServiceList($resource, $filter)
    {
		$obj = $this->client->query($resource, 'it', $filter, 'ORDINE');
		//print_r($obj);
        $col1 = $this->getColId($obj->table->cols, $this->serviceWorkTableNameColumn);

        $r         = new stdClass();
        $r->labels = array();
        $r->values = array();
        
        foreach ($obj->table->rows as $name => $value) {
            $r->labels[] = $value->c[$col1]->v;
            $r->values[] = $value->c[$col1]->v;
        }
 
        return $r;
    }

    function getColId($cols, $label)
    {
        $id = -1;
        foreach ($cols as $name => $value) {
            if ($value->label == $label)
                $id = $value->id;
        }
        return $id;
    }
    
    function getColValue($rows, $rowId, $colId)
    {
        return $rows[$rowId]->c[$colId]->v;
    }

    function searchRowByColValue($rows, $colId, $colValue)
    {
        $id = -1;
        
        foreach ($rows as $name => $value) {
            if ($value->c[$colId]->v == $colValue)
                $id = $name;
        }
        
        return $id;
    }
    
    function getColName($cols, $label)
    {
        return strtolower($label);
    }

    function getGroupByArray($resource, $filter, $orderBy)
    {
        $arr = array();
		$obj = $this->client->query($resource, 'it', $filter, $orderBy);

        foreach ($obj->table->rows as $name => $value) {
		    $group = new stdClass();
			$group->name = $value->c[0]->v;
			$group->count = $value->c[1]->v;
		
            $arr[] = $group;
        }
        return $arr;
    }
}


$camilaUI = new CamilaUserInterface();

$camilaUI->insertTitle($titleText, $titleIcon);

$mover = new WorkTableResourceMover();

$limit = 100;
$showNext = false;
$page = 0;
if (isset($_REQUEST['page'])) {
	$page = $_REQUEST['page'];
}


//$mission = $mover->getGroupByArray($resourceTable,'"' . $serviceColumn . '" (raggruppa per campo selezionato)', $serviceColumn);


$camilaWT  = new CamilaWorkTable();
$camilaWT->db = $_CAMILA['db'];
$query = 'SELECT ${'.$resourceTable.'.'.$serviceColumn.'}, count(*) FROM ${'.$resourceTable.'} GROUP BY ${'.$resourceTable.'.'.$serviceColumn.'}';
$missionResult = $camilaWT->startExecuteQuery($query);

{
$resourceWorkTableId = $camilaWT->getWorktableSheetId($resourceTable);




if (!isset($_REQUEST['service'])) {
    require_once(CAMILA_DIR . 'datagrid/form.class.php');

    require_once(CAMILA_DIR . 'datagrid/elements/form/textbox.php');

    
    $form = new phpform(CAMILA_APPLICATION_PREFIX.'___worktable'.$resourceWorkTableId, 'id');
    $form->drawrules = false;
    //$form->drawheadersubmitbutton = true;
    $t = 'segreteriacampo_worktable'.$resourceWorkTableId;
    new form_textbox($form, 'nominativo', 'Ricerca servizio per '.$col1, false, 30, 255);
	//$script = 'function (input) { field = document.getElementById("camila_w1f").value; field = field.substring(3); return "index.php?input="+input+"&camila_autosuggest_filterbox&table=' . $table . '&field="+field+"&id=id&infofields="+field+"&pickfields="+field+"&maxresults=5&"; }';
	//$script = "index.php?camila_autosuggest&table=".$this->autosuggest_table."&field=".$this->autosuggest_field."&id=".$this->autosuggest_idfield."&infofields=".urlencode($this->autosuggest_infofields)."&pickfields=".urlencode($this->autosuggest_pickfields)."&maxresults=".$this->autosuggest_maxresults."&";

	$searchField = $camilaWT->getWorktableSheetColumnName($resourceWorkTableId, $col1);
	$infoField1 = $camilaWT->getWorktableSheetColumnName($resourceWorkTableId, $col2);
	$infoField2 = $camilaWT->getWorktableSheetColumnName($resourceWorkTableId, $groupColumn);
	$infoField3 = $camilaWT->getWorktableSheetColumnName($resourceWorkTableId, $serviceColumn);
	
	$form->fields['nominativo']->autosuggest_table = CAMILA_APPLICATION_PREFIX.'worktable'.$resourceWorkTableId;
	$form->fields['nominativo']->autosuggest_field = $searchField;
	$form->fields['nominativo']->autosuggest_idfield = 'id';
	$form->fields['nominativo']->autosuggest_infofields = $infoField1.','.$infoField2.','.$infoField3;
	$form->fields['nominativo']->autosuggest_pickfields = $infoField1.','.$infoField2.','.$infoField3;
	$form->fields['nominativo']->autosuggest_destfields = $infofield3;

	$form->fields['nominativo']->autosuggest_callback = 'ale';

    $code = "<script>\n";
    $code .= 'function ' .$form->fields['nominativo']->autosuggest_callback . '(o){ /*alert(o.info);*/}';
    $code .= ' camila_addDOMLoadEvent(function(){ ' . $script . ' });';
    $code .= "</script>\n";
    $_CAMILA['page']->camila_add_js($code);
	  
	$script = "'index.php?camila_autosuggest&table=".$form->fields['nominativo']->autosuggest_table."&field=".$form->fields['nominativo']->autosuggest_field."&id=".$form->fields['nominativo']->autosuggest_idfield."&infofields=".urlencode($form->fields['nominativo']->autosuggest_infofields)."&pickfields=".urlencode($form->fields['nominativo']->autosuggest_pickfields)."&maxresults=".$form->fields['nominativo']->autosuggest_maxresults."&'";
	$form->fields['nominativo']->autosuggest_advanced_script = $script;
    $form->process();
    
    $form->draw(false);
}	
}

if (!isset($_REQUEST['service'])) {
	$myText = new CHAW_text('');
	$_CAMILA['page']->add_text($myText);
	$wCount = 0;

	while (!$missionResult->EOF) {
		$wCount++;
		$a = $missionResult->fields;
		$myLink = new CHAW_link((($a[0] == '') ? '(Nessun servizio)' : $a[0]) . ' (' . $a[1] . ')', '?dashboard='.$_REQUEST['dashboard'].'&service='.urlencode($a[0]));
		$myLink->set_css_class('btn btn-md btn-default btn-info');
		$myLink->set_br(2);
		$_CAMILA['page']->add_link($myLink);
		$missionResult->MoveNext();
	}

	if ($wCount == 0) {
		$camilaUI->insertWarning('Al momento non ci sono risorse da movimentare!');
	}

	/*foreach ($mission as $name => $value) {        
		$myLink = new CHAW_link((($value->name == '') ? '(Nessun servizio)' : $value->name) . ' (' . $value->count . ')', '?dashboard='.$_REQUEST['dashboard'].'&service='.urlencode($value->name));
		$myLink->set_css_class('btn btn-md btn-default btn-info');
		$myLink->set_br(2);
		$_CAMILA['page']->add_link($myLink);
    }*/
} else {
	//2017
    $form = new phpform($_REQUEST['dashboard'], 'index.php?dashboard=' . $_REQUEST['dashboard'] .'&service='.urlencode($_REQUEST['service']).'&page='.$_REQUEST['page']);
    new form_hidden($form, 'dashboard', $_REQUEST['dashboard']);
	new form_hidden($form, 'service', $_REQUEST['service']);
	
	$oCount = 0;

	while (!$missionResult->EOF) {
		$a = $missionResult->fields;

		if ($a[0] != '')
            $filter = '${'.$resourceTable.'.'.$serviceColumn.'}='.$camilaWT->db->qstr($a[0]);
        else
            $filter = '${'.$resourceTable.'.'.$serviceColumn.'} IS NULL OR ${'.$resourceTable.'.'.$serviceColumn.'}='.$camilaWT->db->qstr('');

		if ($_REQUEST['service'] != $a[0]) {
        } else {
			
			//2017
			$query = 'SELECT ${'.$resourceTable.'.'.$groupColumn.'},Id,${'.$resourceTable.'.'.$col1.'}, ${'.
			$resourceTable.'.'.$col2.'} FROM ${'.$resourceTable.'} WHERE '.$filter.' ORDER BY ${'.$resourceTable.'.'.$groupColumn.'} LIMIT ' . $limit;
			if ($page>0)
			{
				$query .= ' OFFSET ' . $page*$limit;
			}
			$resourceResult = $camilaWT->startExecuteQuery($query);

			$t=$a[0];
			if ($t=='')
				$t = '(Nessun servizio)';

			$resourceWorkTableId = $camilaWT->getWorktableSheetId($resourceTable);
			$resourceWorkTableServiceColumnName = $camilaWT->getWorktableSheetColumnName($resourceWorkTableId, $serviceColumn);

			$link = 'cf_worktable'.$resourceWorkTableId. '.php?camila_w1f=_C_'.$resourceWorkTableServiceColumnName.'&camila_w1c=eq&camila_w1v='.urlencode($t);

			$gCount = 0;
			$tCount = $resourceResult->RecordCount();

			new form_link_separator($form, '' .$t . " (" . $tCount . (($tCount==$limit || $page>0) ? '+' : ''). ")", HAW_TEXTFORMAT_BOLD, $link, 'btn btn-md btn-default btn-info');

			$gResLabels = array();
			$gResValues = array();
			while (!$resourceResult->EOF) {
				$b = $resourceResult->fields;

				if ($gCount == 0)
					$currentGroup = $b[0];

				if (($gCount >0 && $b[0] != $currentGroup) /*|| ($tCount == ($gCount+1))*/)
				{	
					//New section
					//$k = 'resource_' . $a[0] . '_' . $b[0] . '_';

					//if ($tCount != ($gCount+1))
					{
					$k = 'resource_' . $gCount . '_';

					new form_hidden($form, $k . '_service_name', $_REQUEST['service']);
					new form_hidden($form, $k . '_group_name', $currentGroup);
					new form_checklist($form, $k, $currentGroup . ' (' . count($gResValues) . ')', $gResLabels, $gResValues, false, false);
					$form->fields[$k]->cols = 1;
					}
					$currentGroup = $b[0];
					$gResLabels = array();
					$gResValues = array();

					$gResLabels[]=$b[2] . ' ' . $b[3];
					$gResValues[]=$b[1];
				}
				else {
					$gResLabels[]=$b[2] . ' ' . $b[3];
					$gResValues[]=$b[1];
				}
				$gCount++;

				$resourceResult->MoveNext();
			}
			if ($gCount == $limit)
			{
				$showNext = true;
			}
		}
		$oCount++;
		$missionResult->MoveNext();
	}
	
	//echo 'o:'.$oCount;
	
	//New section
	//$k = 'resource_' . $a[0] . '_' . $b[0] . '_';
	$k = 'resource_' . $gCount . '_';

	new form_hidden($form, $k . '_service_name', $_REQUEST['service']);
	new form_hidden($form, $k . '_group_name', $currentGroup);

	
	new form_checklist($form, $k, $currentGroup . ' (' . count($gResValues) . ')', $gResLabels, $gResValues, false, false);
	$form->fields[$k]->cols = 1;


	$query = 'SELECT ${'.$serviceTable.'.NOME} FROM ${'.$serviceTable.'} ORDER BY ${'.$serviceTable.'.ORDINE}';
	$serviceResult = $camilaWT->startExecuteQuery($query);
	
	if ($serviceResult->RecordCount() == 0) {
		$camilaUI->insertWarning('Attenzione! Non è stato definito alcun servizio per l\'evento.');
	}

	$s = array();

	while (!$serviceResult->EOF) {
		$c = $serviceResult->fields;

		$s[] = $c[0];
		$serviceResult->MoveNext();
	}

	$options = implode($s, ',');
	new form_text_separator($form, "Assegnazione nuovo servizio alle risorse selezionate");
    new form_static_listbox($form, 'new_service_name', 'Nuovo servizio', $options, true);
	
	
    if (!$form->process()) {
		$form->draw();
		if ($page > 0)
			{
				$myLink = new CHAW_link('Pagina precedente', '?dashboard='.$_REQUEST['dashboard'].'&service='.urlencode($_REQUEST['service']).'&page='.($page-1));
				$myLink->set_br(0);
				$_CAMILA['page']->add_link($myLink);
			}
		if ($showNext) {
			
			
			$myLink = new CHAW_link('Pagina successiva', '?dashboard='.$_REQUEST['dashboard'].'&service='.urlencode($_REQUEST['service']).'&page='.($page+1));
			$_CAMILA['page']->add_link($myLink);
			
		}
	}
        
    else {
		
		$serviceEndServiceValue = "USCITA DEFINITIVA";

		$count=0;

        //foreach ($form->fields as $key => $value) {
		foreach ($_POST as $key => $value) {

			//if (startsWith($key, 'resource_')) {
            if (startsWith($key, $_REQUEST['dashboard'].'_resource_')) {

				if (strpos($key, 'service_name') === false && strpos($key, 'group_name') === false && strpos($key, 'labels') === false && strpos($key, 'count') === false)
                //foreach ($form->fields[$key]->value as $k => $v) {
				{
					//foreach ($_POST[$key] as $k => $v) {
						$v = $_POST[$key];
						//echo $key;
						$skey = substr($key,0,strpos($key,'__'));
						$ekey = substr($key,strpos($key,'__')+2);
						
						if ($v != '') {
							$id       = $v;
							$resource = $_POST[$skey . '__labels_' . $ekey];
							$group    = $_POST[$skey . '__group_name'];
							$from     = $_POST[$skey . '__service_name'];
							$to       = $form->fields['new_service_name']->value;

							$now     = $_CAMILA['db']->BindTimeStamp(date("Y-m-d H:i:s", time()));
							$now2     = $_CAMILA['db']->BindDate(date("Y-m-d", time()));

							$query = 'update ${' . $resourceTable . '}';
							$query.= ' set ${' .$resourceTable. '.' .$serviceColumn . '}=' . $camilaWT->db->qstr($to);
							$query.= ', last_upd=' . $camilaWT->db->qstr($now);
							$query.= ', last_upd_by=' . $camilaWT->db->qstr($_CAMILA['user']);
							$query.= ', last_upd_src=' . $camilaWT->db->qstr('application');
							$query.= ', last_upd_by_surname=' . $camilaWT->db->qstr($_CAMILA['user_surname']);
							$query.= ', last_upd_by_name=' . $camilaWT->db->qstr($_CAMILA['user_name']);

							if ($serviceEndServiceValue != '')
								$query.= ',${' .$resourceTable. '.DATA FINE ATTESTATO}=' . $camilaWT->db->qstr($now2);

							if ($to == $serviceEndServiceValue && $serviceEndServiceValue != '') {
								$query.= ',${' .$resourceTable. '.DATA/ORA USCITA DEFINITIVA}=' . $camilaWT->db->qstr($now);
							//	$query.= ',${' .$resourceTable. '.DATA FINE ATTESTATO}=' . $camilaWT->db->qstr($now2);
							}

							if ($from == $serviceEndServiceValue && $serviceEndServiceValue != '') {
								$query.= ',${' .$resourceTable. '.DATA/ORA USCITA DEFINITIVA}=' . $camilaWT->db->qstr('');
							//	$query.= ',${' .$resourceTable. '.DATA FINE ATTESTATO}=' . $camilaWT->db->qstr('');
							}

							$query.= ',mod_num = mod_num + 1';
							$query.= ' WHERE id =' . $camilaWT->db->qstr($id);

							$result = $camilaWT->startExecuteQuery($query,false);
							//echo $query;

							if ($result === false) {
								camila_error_text($resource . ' (' . $group . '): ' . $from . '->' . $to . ': Aggiornamento KO!!!');
							} else {
								$camilaUI->insertLink('cf_worktable'.$resourceWorkTableId.'.php?camila_w1f=_I_id&camila_w1c=eq&camila_w1v='.$id.'&submit_button=Filtra+i+dati', $resource . ' (' . $group . ')',0);

								$camilaUI->insertText('- ' . $from . '->' . $to . ': Aggiornamento OK.');
								
								//if ($to == $serviceEndServiceValue && $serviceEndServiceValue != '') {
									//$camilaUI->insertButton('cf_worktable'.$resourceWorkTableId.'.php?camila_w1f=_I_id&camila_w1c=eq&camila_w1v='.$id.'&submit_button=Filtra+i+dati','Visualizza');
								//}
								//camila_information_text($resource . ' (' . $group . '): ' . $from . '->' . $to . ': Aggiornamento OK.');
							}

							if ($brogliaccio != '')
							{
								$res = $mover->insertRow($camilaWT,$brogliaccio, 'it', Array('DATA/ORA','RISORSA','TIPO RISORSA','GRUPPO','DA','A'), Array($now,$resource,$resourceType,$group,$from,$to));

								if ($res === false)
								{
									camila_error_text('ERRORE nella registrazione del movimento nella scheda ' . $brogliaccio . '.');
								}
								else
								{
									//camila_information_text();
									$camilaUI->insertText('Registrato il movimento nella scheda ' . $brogliaccio);
									//http://localhost/app/segreteriacampo/cf_worktable17.php?camila_w1f=_I_id&camila_w1c=like&camila_w1v=101224&submit_button=Filtra+i+dati
								}
							}
							$count++;
						}


					//}
				}
            }
        }

		if ($count>0) {
			$text = new CHAW_text('');
			$_CAMILA['page']->add_text($text);
			$myLink = new CHAW_link('Torna al riepilogo ' . $to, '?dashboard='.$_REQUEST['dashboard'].'&service='.urlencode($to));
			$myLink->set_css_class('btn btn-md btn-default btn-info btn-space');
			$_CAMILA['page']->add_link($myLink);
			$myLink = new CHAW_link('Torna al riepilogo ' . $from, '?dashboard='.$_REQUEST['dashboard'].'&service='.urlencode($from) . '&page='. $_REQUEST['page']);
			$myLink->set_css_class('btn btn-md btn-default btn-info btn-space');
			$_CAMILA['page']->add_link($myLink);
			$myLink = new CHAW_link('Torna al riepilogo ' . $resourceTable, '?dashboard='.$_REQUEST['dashboard']);
			$myLink->set_css_class('btn btn-md btn-default btn-info btn-space');
			$_CAMILA['page']->add_link($myLink);
		}
		else
		{
			camila_error_text('Nessuna risorsa selezionata!!!');

			$myLink = new CHAW_link('Torna al riepilogo ' . $resourceTable, '?dashboard='.$_REQUEST['dashboard']);
			$myLink->set_css_class('btn btn-md btn-default btn-info btn-space');
			$_CAMILA['page']->add_link($myLink);
		}
    }
}

?>