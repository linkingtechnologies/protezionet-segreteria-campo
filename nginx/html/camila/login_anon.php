<?php
/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2022 Umberto Bresciani

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


$myForm = new CHAW_form($_SERVER['PHP_SELF'], HAW_METHOD_POST); 

$hidden = new CHAW_hidden('camila_pwloginbox', 'yes');
$myForm->add_hidden($hidden);
$hidden = new CHAW_hidden("camila_redirect", basename($_SERVER['PHP_SELF']));
if ($_SERVER['QUERY_STRING'] != '')
    $hidden = new CHAW_hidden('camila_redirect', basename($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']));

if (basename($_SERVER['PHP_SELF']) != CAMILA_LOGIN_HOME)
    $myForm->add_hidden($hidden);

$text = new CHAW_text(''); 
$myForm->add_text($text);

$cognome = new CHAW_input('username', CAMILA_ANON_USER, camila_get_translation('camila.login.username'));
$cognome->set_size(20);
$cognome->set_maxlength(20);
$myForm->add_input($cognome);

$thePW1 = new CHAW_input('pwd', CAMILA_ANON_USER, camila_get_translation('camila.login.password'));
$thePW1->set_size(CAMILA_PASSWORD_LENGTH);
$thePW1->set_maxlength(CAMILA_PASSWORD_LENGTH);
$thePW1->set_type(HAW_INPUT_PASSWORD);
$myForm->add_input($thePW1);

if (isset($_REQUEST['output']) && $_REQUEST['output']!='') {
    $o = new CHAW_hidden('output', $_REQUEST['output']);
    $myForm->add_hidden($o);
}

$thePW4 = new CHAW_hidden('js', '');
$myForm->add_hidden($thePW4);

$code = "<script>document.forms[0].js.value = 'enabled';</script>";
$js = new CHAW_js($code, true);
$myForm->add_userdefined($js);

$theSubmission = new CHAW_submit(camila_get_translation('camila.login.renewsession'), 'submit');
$myForm->add_submit($theSubmission);

$_CAMILA['page']->add_form($myForm);

?>