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


require_once(CAMILA_LIB_DIR . '/csv_bv/class.csv_bv.php');
require_once(CAMILA_DIR.'datagrid/configurator.class.php');


function CSV_import($file, $table, $db) {

    $result = Array();

    $csv = new csv_bv($file, ',', '"' , '\\');											
    $csv->SkipEmptyRows(true);
    // Remove leading and trailing \s and \t.
    $csv->TrimFields(false);

    $header = $csv->NextLine();

    $rs = $db->Execute('select * from ' . $table);
    if (!$rs) {
        $result['result'] = 0; //failure
        $result['error'] = $db->ErrorMsg();
    } else {
        $fcount = 0;
        $icount = 0;
        $metatypes = Array();

        $xml_parser = xml_parser_create();

        if (($fp = fopen(str_replace('.csv', '.xml', $file), 'r'))) {

            $data = fread($fp, filesize($file));
            fclose($fp);
            xml_parse_into_struct($xml_parser, $data, $vals, $index);
            xml_parser_free($xml_parser);

            if (is_array($index['FIELD']))
                foreach ($index['FIELD'] as $value) {
                    $metatypes[$vals[$value]['attributes']['NAME']]=$vals[$value]['attributes']['TYPE'];
                }

        } else {
            for ($i = 0; $i < $rs->FieldCount(); $i++) {
                  $curr_field = $rs->FetchField($i);
                  $curr = $curr_field->name;
                  $type = $rs->MetaType($curr_field->type);
                  $metatypes[$curr]=$type;
            }
        }


        $m = 3;//camila_get_translation('camila.dateformat.monthpos');
        $d = 0;//camila_get_translation('camila.dateformat.daypos');
        $y = 6;//camila_get_translation('camila.dateformat.yearpos');


		$db->beginTrans();
		
        while ($arr_data = $csv->NextLine()){
            $record = array();
            $count = 0;

            foreach ($header as $value) {
                if ($metatypes[$value] == 'D') {
                    if ($arr_data[$count] != '')
                         $record[$value] = $db->BindDate(substr($arr_data[$count],$y,4).'-'.substr($arr_data[$count],$m,2).'-'.substr($arr_data[$count],$d,2));
                } else 
                    $record[$value] = $arr_data[$count];
                $count++;
            }

            $insertSQL = $db->GetInsertSQL($rs, $record);
            $icount++;
            $rs2 = $db->Execute($insertSQL); # Insert the record into the database
            if (!$rs2)
                $fcount++;
	
			if ($icount % 200 == 0)
			{
				//$date = date('m/d/Y h:i:s a', time());
				//echo $date;
				$db->commitTrans();
				$db->beginTrans();
			}

        }
		$db->commitTrans();

        if ($fcount>0) {
            $result['result'] = 1; //errors
            $result['processed'] = $icount;
            $result['failed'] = $fcount;
            $result['skipped'] = $csv->SkippedRowCount();
        } else {
            $result['result'] = 2; //success
            $result['processed'] = $icount;
            $result['failed'] = $fcount;
            $result['skipped'] = $csv->SkippedRowCount();
        }
    }

    return $result;

}

function XLS_import($file, $table, $db) {

    $result = Array();

    $configurator = new configurator();

    $configurator->filename = $file;
    $configurator->sheetnum = 0;
    $configurator->interactive = false;
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
    $configurator->db = $db;

    $filename=basename($file);
    $sequence = intval(substr($filename,0,strpos($filename,'_')));

    $id = $configurator->xls_read($sequence);

    $result['result'] = 2; //success
    $result['processed'] = 1;
    $result['failed'] = 0;

    return $result;

}

function XLS_import2($file, $table, $db) {

    $filename=basename($file);
    $sequence = intval(substr($filename,0,strpos($filename,'_')));

    $res = $db->Execute('select id from ' . CAMILA_TABLE_WORKT . ' where filename='.$db->qstr($filename));
    if ($res === false)
        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $db->ErrorMsg());
    $id = $res->fields['id'];

    $result = Array();

    $configurator = new configurator();

    $configurator->filename = $file;
    $configurator->sheetnum = 0;
    $configurator->interactive = false;
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
    $configurator->db = $db;

    $configurator->configure_table($id, false);

    $record = Array();
	//$record2 = Array();

    $res = $db->Execute('select * from ' . CAMILA_TABLE_WORKT . ' where id='.$db->qstr($id));
    if ($res === false)
        camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $db->ErrorMsg());
    $record['short_title'] = $res->fields['short_title'];
    $record['full_title'] = $res->fields['full_title'];
    //888
	$record['label_order']=$sequence;
    $scriptname = $res->fields['scriptname'];
    $category = $res->fields['category'];

    $updateSQL = $db->AutoExecute(CAMILA_TABLE_PLANG, $record, 'UPDATE', 'page_url='.$db->qstr($scriptname) . ' and lang='.$db->qstr($_REQUEST['lang']));
    if (!$updateSQL) {
         camila_information_text(camila_get_translation('camila.worktable.db.error'));
    }

/*	$updateSQL = $db->AutoExecute(CAMILA_TABLE_WORKT, $record2, 'UPDATE', 'id='.$db->qstr($id));
    if (!$updateSQL) {
         camila_information_text(camila_get_translation('camila.worktable.db.error'));
    }*/

    if (trim($category) != '')
    {
        $res = $db->Execute('select page_url from ' . CAMILA_TABLE_PLANG . ' where short_title='.$db->qstr($category) . ' AND page_url LIKE '.$db->qstr('cf_app.php?cat%') . ' and lang='.$db->qstr($_REQUEST['lang']));
        if ($res === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $db->ErrorMsg());

        $cat_url = '-';

        if ($res->RecordCount() == 0)
        {

            $res = $db->Execute('select page_url from ' . CAMILA_TABLE_PLANG . ' where full_title<>'.$db->qstr('-') . ' AND page_url LIKE '.$db->qstr('cf_app.php?cat%') . ' and lang='.$db->qstr($_REQUEST['lang']));
            if ($res === false)
                camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $db->ErrorMsg());

            $cat_url = $res->fields['page_url'];

            $record = Array();
            $record['label_order'] = $sequence;
            $record['short_title'] = $category;
            $record['full_title'] = '-';
            $updateSQL = $db->AutoExecute(CAMILA_TABLE_PLANG, $record, 'UPDATE', 'page_url='.$db->qstr($cat_url) . ' and lang='.$db->qstr($_REQUEST['lang']));
            if (!$updateSQL)
                 camila_information_text(camila_get_translation('camila.worktable.db.error'));

        }
        else
        {
            $cat_url = $res->fields['page_url'];
        }

            $record = Array();
            $record['parent'] = $cat_url;
            $updateSQL = $db->AutoExecute(CAMILA_TABLE_PAGES, $record, 'UPDATE', 'url='.$db->qstr($scriptname));
            if (!$updateSQL)
                camila_information_text(camila_get_translation('camila.worktable.db.error'));

            $record = Array();
            $record['visible'] = 'yes';
            $updateSQL = $db->AutoExecute(CAMILA_TABLE_PAGES, $record, 'UPDATE', 'url='.$db->qstr($cat_url));
            if (!$updateSQL)
                camila_information_text(camila_get_translation('camila.worktable.db.error'));

    }

	        $record = Array();
            $record['label_order'] = $sequence;
            $updateSQL = $db->AutoExecute(CAMILA_TABLE_PAGES, $record, 'UPDATE', 'url='.$db->qstr($scriptname));
            if (!$updateSQL)
                camila_information_text(camila_get_translation('camila.worktable.db.error'));

    $result['result'] = 2; //success
    $result['processed'] = 1;
    $result['failed'] = 0;

    return $result;

}
?>