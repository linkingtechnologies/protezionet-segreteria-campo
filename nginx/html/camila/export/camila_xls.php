<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2017 Umberto Bresciani

   Camila PHP Framework is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Camila PHP Framework is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Camila PHP Framework; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */


require_once(CAMILA_LIB_DIR . 'php-excel-creator/Workbook.php');


class CAMILA_XLS_deck extends CHAW_deck {

    function create_page() {

        global $_CAMILA;

        if ($_REQUEST['camila_export_action'] == '' || $_REQUEST['camila_export_action'] == 'download' || $_REQUEST['camila_export_action'] == 'sendmail')
            $fname = tempnam(CAMILA_TMP_DIR, 'export.xls');
        else {
            if (!$this->camila_export_file_exists || $_REQUEST['camila_export_overwrite'] == 'y')
                $fname = $this->camila_export_get_dir() . $this->camila_export_filename();
            else
                $fname = tempnam(CAMILA_TMP_DIR, 'export.xls');
        }

        $workbook = &new Spreadsheet_Excel_Writer_Workbook($fname);
        $workbook->setTempDir(CAMILA_TMP_DIR);
        $workbook->setVersion(8);

        $dWorksheet = &$workbook->addworksheet($_CAMILA['page_short_title'] . ' - ' . camila_get_translation('camila.worktable.worksheet.data'));

        $i = 0;
        $m = camila_get_translation('camila.dateformat.monthpos');
        $d = camila_get_translation('camila.dateformat.daypos');
        $y = camila_get_translation('camila.dateformat.yearpos');

        $date_format =& $workbook->addFormat();
        $fmt = str_replace(Array('d', 'm', 'y'), Array('dd', 'mm', 'yyyy'), strtolower($_CAMILA['date_format']));
        $date_format->setNumFormat($fmt);

        $dataFound = false;

	while (isset($this->element[$i])) {
	    $page_element = $this->element[$i];
	    switch ($page_element->get_elementtype()) {
		case HAW_TABLE: {
		    $table = $this->element[$i];

		    $row = $table->row[0];
		    for ($b = 0; $b < $row->number_of_columns; $b++) {
			$column = $row->column[$b];
			if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT)
			    $text = $column->get_text();
			if (is_object($column) && $column->get_elementtype() == HAW_LINK)
			    $text = $column->get_label();

			$dWorksheet->writeString($a, $b, (isUTF8($text) ? utf8_decode($text) : $text));
		    }

                    if (!$_CAMILA['page']->camila_worktable || ($_CAMILA['page']->camila_worktable && ($_REQUEST['camila_worktable_export'] == 'all' || $_REQUEST['camila_worktable_export'] == 'dataonly'))) {

                        for ($a = 1; $a < $table->number_of_rows; $a++) {
                            $row = $table->row[$a];
                            $dataFound = true;

                            for ($b = 0; $b < $row->number_of_columns; $b++) {
                                $column = $row->column[$b];

                                if (is_object($column) && $column->get_elementtype() == HAW_LINK) {
                                    $text = $column->get_label();
                                    $url = $column->get_url();

                                    $dWorksheet->writeUrl($a, $b, $url, $text);

                                } else {

                                    if (is_object($column) && $column->get_elementtype() == HAW_PLAINTEXT)
                                        $text = $column->get_text();

                                    switch($column->metatype) {

                                        case 'I':
                                        case 'N':
                                            if ($text != '') {
                                                $dWorksheet->writeNumber($a, $b, intval($text));
                                            }
                                            break;

                                        case 'D':
                                            if ($text != '') {
                                                $dWorksheet->writeNumber($a, $b, $this->xl_date_list(intval(substr($text,$y,4)), intval(substr($text,$m,2)), intval(substr($text,$d,2))), $date_format);
                                            }
                                            break;

                                        default:
                                            $dWorksheet->writeString($a, $b, (isUTF8($text) ? utf8_decode($text) : $text));
                                    }
                                }

                            }
		        }
		    }
                    break;
                }
            }
            $i++;
        }

    if ($_CAMILA['page']->camila_worktable && ($_REQUEST['camila_worktable_export'] == 'all' || $_REQUEST['camila_worktable_export'] == 'confonly')) {
        $worksheet = &$workbook->addworksheet($_CAMILA['page_short_title'] . ' - ' . camila_get_translation('camila.worktable.worksheet.conf'));

        $aLeft =& $workbook->addformat();
        $aLeft->setAlign('left');

        $opt = Array();
        $opt[] = camila_get_translation('camila.worktable.field.sequence');
        $opt[] = camila_get_translation('camila.worktable.field.name.abbrev');
        $opt[] = camila_get_translation('camila.worktable.field.type');
        $opt[] = camila_get_translation('camila.worktable.field.listofvalues');
        $opt[] = camila_get_translation('camila.worktable.field.maxlength');
        $opt[] = camila_get_translation('camila.worktable.field.required');
        $opt[] = camila_get_translation('camila.worktable.field.defaultval');
        $opt[] = camila_get_translation('camila.worktable.field.readonly');
        $opt[] = camila_get_translation('camila.worktable.field.visible');
        $opt[] = camila_get_translation('camila.worktable.field.force');
        $opt[] = camila_get_translation('camila.worktable.field.unique');
        $opt[] = camila_get_translation('camila.worktable.field.options');
        $opt[] = camila_get_translation('camila.worktable.field.autosuggestwtname');
        $opt[] = camila_get_translation('camila.worktable.field.autosuggestwtcolname');
		$opt[] = camila_get_translation('camila.worktable.field.help');
        //$opt[] = '';
        $opt[] = camila_get_translation('camila.worktable.configuration');
        $opt[] = camila_get_translation('camila.worktable.name');
        $opt[] = camila_get_translation('camila.worktable.desc');
        $opt[] = camila_get_translation('camila.worktable.order.by');
        $opt[] = camila_get_translation('camila.worktable.order.dir');
        $opt[] = camila_get_translation('camila.worktable.canupdate');
        $opt[] = camila_get_translation('camila.worktable.caninsert');
        $opt[] = camila_get_translation('camila.worktable.candelete');
        $opt[] = camila_get_translation('camila.worktable.category');

        foreach($opt as $key => $value) {
            $text = $opt[$key];
            $worksheet->writeString(intval($key) + 1, $o, (isUTF8($text) ? utf8_decode($text) : $text));
        }

        $worksheet->setColumn(0, 0, 30);
        $id = substr($_SERVER['PHP_SELF'], 12, -4);

        $result = $_CAMILA['db']->Execute('select * from ' . CAMILA_TABLE_WORKC . ' where (wt_id='.$_CAMILA['db']->qstr($id) . ' and is_deleted<>'.$_CAMILA['db']->qstr('y') . ') order by sequence');
        if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

        $yesNoArr = camila_get_translation_array('camila.worktable.options.noyes');
        $fieldTypeArr = camila_get_translation_array('camila.worktable.options.fieldtype');
        $forceArr = camila_get_translation_array('camila.worktable.options.force');
        $orderDirArr = camila_get_translation_array('camila.worktable.options.order.dir');
        $colArray = Array();

	$count = 1;
	while (!$result->EOF) {
	    $colArray[$result->fields['col_name']] = $result->fields['name']; 
	    $text = $result->fields['name'];
	    $worksheet->writeString(0, $count, (isUTF8($text) ? utf8_decode($text) : $text));
          if ($_REQUEST['camila_worktable_export'] == 'all' && !$dataFound)
              $dWorksheet->writeString(0, $count-1, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $result->fields['sequence'];
	    $worksheet->writeNumber(1, $count, (isUTF8($text) ? utf8_decode($text) : $text), $aLeft);
	    $text = $result->fields['name_abbrev'];
	    $worksheet->writeString(2, $count, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $fieldTypeArr[$result->fields['type']];
	    $worksheet->writeString(3, $count, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $result->fields['listbox_options'];
	    $worksheet->writeString(4, $count, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $result->fields['maxlength'];
	    $worksheet->writeNumber(5, $count, (isUTF8($text) ? utf8_decode($text) : $text), $aLeft);
	    $text = $yesNoArr[$result->fields['required']];
	    $worksheet->writeString(6, $count, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $result->fields['default_value'];
	    $worksheet->writeString(7, $count, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $text = $yesNoArr[$result->fields['readonly']];
	    $worksheet->writeString(8, $count, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $text = $yesNoArr[$result->fields['visible']];
	    $worksheet->writeString(9, $count, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $forceArr[$result->fields['force_case']];
	    $worksheet->writeString(10, $count, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $yesNoArr[$result->fields['must_be_unique']];
	    $worksheet->writeString(11, $count, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $result->fields['field_options'];
	    $worksheet->writeString(12, $count, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $result->fields['autosuggest_wt_name'];
	    $worksheet->writeString(13, $count, (isUTF8($text) ? utf8_decode($text) : $text));
	    $text = $result->fields['autosuggest_wt_colname'];
	    $worksheet->writeString(14, $count, (isUTF8($text) ? utf8_decode($text) : $text));
		$text = $result->fields['help'];
	    $worksheet->writeString(15, $count, (isUTF8($text) ? utf8_decode($text) : $text));

	    $count++;
	    $result->MoveNext();
	}

        $worksheet->setColumn(1, $count/*-1*/, 15);

        $result = $_CAMILA['db']->Execute('select * from ' . CAMILA_TABLE_WORKT . ' where id='.$_CAMILA['db']->qstr($id));
	if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

        $text = $result->fields['short_title'];
        $worksheet->writeString(17, 1, (isUTF8($text) ? utf8_decode($text) : $text));
	$text = $result->fields['full_title'];
        $worksheet->writeString(18, 1, (isUTF8($text) ? utf8_decode($text) : $text));
	$text = $colArray[$result->fields['order_field']];
        $worksheet->writeString(19, 1, (isUTF8($text) ? utf8_decode($text) : $text));
	$text = $orderDirArr[$result->fields['order_dir']];
        $worksheet->writeString(20, 1, (isUTF8($text) ? utf8_decode($text) : $text));
	$text = $yesNoArr[$result->fields['canupdate']];
        $worksheet->writeString(21, 1, (isUTF8($text) ? utf8_decode($text) : $text));
	$text = $yesNoArr[$result->fields['caninsert']];
        $worksheet->writeString(22, 1, (isUTF8($text) ? utf8_decode($text) : $text));
	$text = $yesNoArr[$result->fields['candelete']];
        $worksheet->writeString(23, 1, (isUTF8($text) ? utf8_decode($text) : $text));
        $text = $result->fields['category'];
        $worksheet->writeString(24, 1, (isUTF8($text) ? utf8_decode($text) : $text));


        $text = camila_get_translation('camila.worktable.bookmarks');
        $worksheet->writeString(16, 2, (isUTF8($text) ? utf8_decode($text) : $text));

        $query = 'select base_url,url,title from ' . CAMILA_APPLICATION_PREFIX.'camila_bookmarks where base_url='.$_CAMILA['db']->qstr('cf_worktable'. $id .'.php') . ' order by sequence';

        $result = $_CAMILA['db']->Execute($query);
        if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

        $i=0;
        while (!$result->EOF) {
            $i++;

            $text = $result->fields['title'];
            $worksheet->writeString(16+$i, 2, (isUTF8($text) ? utf8_decode($text) : $text));

            $url = parse_url($result->fields['url'], PHP_URL_QUERY);
            $qArr = $this->parse_query_string($url);

            $text = $qArr['filter'];
            $worksheet->writeString(16+$i, 3, (isUTF8($text) ? utf8_decode($text) : $text));

            $result->MoveNext();
        }


    }

    if ($_CAMILA['page']->camila_worktable && !$dataFound && $_REQUEST['camila_worktable_export'] == 'dataonly') {
        $id = substr($_SERVER['PHP_SELF'], 12, -4);

        $result = $_CAMILA['db']->Execute('select * from ' . CAMILA_TABLE_WORKC . ' where (wt_id='.$_CAMILA['db']->qstr($id) . ' and is_deleted<>'.$_CAMILA['db']->qstr('y').') order by sequence');
        if ($result === false)
            camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

	$count = 1;
	while (!$result->EOF) {
	    $text = $result->fields['name'];
            $dWorksheet->writeString(0, $count-1, (isUTF8($text) ? utf8_decode($text) : $text));
	    $count++;
	    $result->MoveNext();
	}

    }


    $workbook->close();

    if ($_REQUEST['camila_export_action'] == '' || $_REQUEST['camila_export_action'] == 'download')
    {
        header("Content-Type: application/x-msexcel; name=\"".$this->camila_export_safe_filename() . '.' . $this->camila_export_get_ext()."\"");
        header("Content-Disposition: attachment; filename=\"".$this->camila_export_safe_filename() . '.' . $this->camila_export_get_ext()."\"");
    }

    $fh = fopen($fname, "rb");

    if ($_REQUEST['camila_export_action'] == '' || $_REQUEST['camila_export_action'] == 'download')
    {
        fpassthru($fh);
        unlink($fname);
    }


    if ($_REQUEST['camila_export_action'] == 'sendmail') {

        global $_CAMILA;

        require_once(CAMILA_LIB_DIR . 'phpmailer/class.phpmailer.php');
        $mail = new PHPMailer();

        if (CAMILA_MAIL_IS_SMTP)
            $mail->IsSMTP();
        $mail->Host = CAMILA_MAIL_HOST;
        $mail->SMTPAuth = CAMILA_MAIL_SMTP_AUTH;

        $mail->From = CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_FROM;
        $mail->FromName = CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_FROM_NAME;

        $mail->AddAttachment($fname, 'file.xls');

        $mail->AddAddress(CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_TO);

        //$mail->AddCC(CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_CC, CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_CC);

        $mail->IsHTML(false);

        $mail->Subject = CAMILA_WORKTABLE_CONFIRM_VIA_MAIL_SUBJECT;

        $text = camila_get_translation('camila.worktable.confirm'). " - ". camila_get_translation('camila.login.username') . ': ' . $_CAMILA['user_name'];
        $mail->Body = $text;
        $mail->AltBody = $text;

        $mail->Send();
        unlink($fname);

    }


    }

function xl_date_list($year, $month=1, $day=1, $hour=0, $minute=0, $second=0) {

    $monthdays=array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    // Leap years since 1900 (year is dividable by 4)
    $leapyears=floor(($year-1900)/4);

    // Non-leap years since 1900 (year is dividable by 100)
    $nonleapyears=floor(($year-1900)/100);

    // Non-non-leap years since 1900 (year is dividable by 400)
    // (Yes, it MUST be "1600", not "1900")
    $nonnonleapyears=floor(($year-1600)/400);

    // Don't count the leap day of the specified year if it didn't
    // happen yet (i.e. before 1 March)
    //
    // Please note that $leapyears becomes -1 for dates before 1 March 1900;
    // this is not logical, but later we will add a day for Excel's
    // phantasie leap day in 1900 without checking if the date is actually
    // after 28 February 1900; so these two logic errors "neutralize"
    // each other
    if ($year%4==0 && $month<3) {
      $leapyears--;
    }

    $days=365*($year-1900)+$leapyears-$nonleapyears+$nonnonleapyears;

    for ($c=1;$c<$month;$c++) {
      $days+=$monthdays[$c-1];
    }

    // Excel actually wants the days since 31 December 1899, not since
    // 1 January 1900; this will also add this extra day
    $days+=$day;

    // Excel treats 1900 erroneously as a leap year, so we must
    // add one day
    //
    // Please note that we DON'T have to check if the date is after
    // 28 February 1900, because for such dates $leapyears is -1
    // (see above)
    $days++;

    return (float)($days+($hour*3600+$minute*60+$second)/86400);
}

function parse_query_string($str) { 
    $op = array(); 
    $pairs = explode("&", $str); 
    foreach ($pairs as $pair) { 
        list($k, $v) = array_map("urldecode", explode("=", $pair)); 
        $op[$k] = $v; 
    } 
    return $op; 
} 

};

?>