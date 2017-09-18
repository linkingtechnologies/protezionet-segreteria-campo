<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2015 Umberto Bresciani

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


//$myDivOpen = new HAW_raw(HAW_HTML, '<div id=\'camilaloginbox\'>');
//$_CAMILA['page']->add_raw($myDivOpen);


$myForm = new CHAW_form($_SERVER['PHP_SELF'], HAW_METHOD_POST);

$hidden = new CHAW_hidden('camila_pwloginbox', 'yes');
$myForm->add_hidden($hidden);
$myForm->set_css_class("form-signin");
$hidden = new CHAW_hidden("camila_redirect", basename($_SERVER['PHP_SELF']));
if ($_SERVER['QUERY_STRING'] != '')
    $hidden = new CHAW_hidden('camila_redirect', basename($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']));

if (basename($_SERVER['PHP_SELF']) != CAMILA_LOGIN_HOME)
    $myForm->add_hidden($hidden);

//$text = new CHAW_text(camila_get_translation('camila.login.authrequired'), HAW_TEXTFORMAT_SMALL | HAW_TEXTFORMAT_BOLD);
//$text->set_br(2);
//$myForm->add_text($text);

if (CAMILA_CHALLENGE_LOGIN) {
    if (isset($_REQUEST['surname']))
        $cognome = new CHAW_input('surname', $_REQUEST['surname'], camila_get_translation('camila.login.surname').': ');
    else
        $cognome = new CHAW_input('surname', '', camila_get_translation('camila.login.surname').': ');
    $cognome->set_size(20);
    $cognome->set_maxlength(20);
    if (isset($_REQUEST['name']))
        $nome = new CHAW_input('name', $_REQUEST['name'], camila_get_translation('camila.login.name').': ');
    else
        $nome = new CHAW_input('name', '', camila_get_translation('camila.login.name').': ');
    $nome->set_size(20);
    $nome->set_maxlength(20);

    $myForm->add_input($nome);
    $myForm->add_input($cognome);

    $thePW1 = new CHAW_input('p1', '', camila_get_translation('camila.login.cipher.pre').' '.$c1.camila_get_translation('camila.login.cipher.post'), '*N');
    $thePW1->set_size(1);
    $thePW1->set_maxlength(1);
    $thePW1->set_type(HAW_INPUT_PASSWORD);
    $myForm->add_input($thePW1);

    $thePW2 = new CHAW_input('p2', '', camila_get_translation('camila.login.cipher.pre').' '.$c2.camila_get_translation('camila.login.cipher.post'), '*N');
    $thePW2->set_size(1);
    $thePW2->set_maxlength(1);
    $thePW2->set_type(HAW_INPUT_PASSWORD);
    $myForm->add_input($thePW2);

    $thePW3 = new CHAW_input('p3', '', camila_get_translation('camila.login.cipher.pre').' '.$c3.camila_get_translation('camila.login.cipher.post'), '*N');
    $thePW3->set_size(1);
    $thePW3->set_maxlength(1);
    $thePW3->set_type(HAW_INPUT_PASSWORD);
    $myForm->add_input($thePW3);
} else {
    if (CAMILA_USERNAME_LOGIN) {
		$myParOpen = new HAW_raw(HAW_HTML, '<h2 class="form-signin-heading">'.CAMILA_APPLICATION_TITLE.'</h2>');
        $myForm->add_raw($myParOpen);
        //$myParOpen = new HAW_raw(HAW_HTML, '<h4 class="form-signin-heading">'.camila_get_translation('camila.login.authrequired').'</h4>');
        //$myForm->add_raw($myParOpen);
        if (isset($_REQUEST['username']))
            $cognome = new CHAW_input('username', $_REQUEST['username'], camila_get_translation('camila.login.username').': ');
        else
            $cognome = new CHAW_input('username', '', camila_get_translation('camila.login.username').': ');
		$cognome->set_css_class('form-control');
        $cognome->set_size(20);
        $cognome->set_maxlength(30);
        $myForm->add_input($cognome);
        //$myParClose = new HAW_raw(HAW_HTML, '</p>');
	//$myForm->add_raw($myParClose);
    } else {
        $myParOpen = new HAW_raw(HAW_HTML, '<p>');
        $myForm->add_raw($myParOpen);
        if (isset($_REQUEST['surname']))
            $cognome = new CHAW_input('surname', $_REQUEST['surname'], camila_get_translation('camila.login.surname').': ');
        else
            $cognome = new CHAW_input('surname', '', camila_get_translation('camila.login.surname').': ');
        $cognome->set_size(20);
        $cognome->set_maxlength(20);
        $myForm->add_input($cognome);
        $myParClose = new HAW_raw(HAW_HTML, '</p>');
        $myForm->add_raw($myParClose);
        $myParOpen = new HAW_raw(HAW_HTML, '<p>');
        $myForm->add_raw($myParOpen);

        if (isset($_REQUEST['name']))
            $nome = new CHAW_input('name', $_REQUEST['name'], camila_get_translation('camila.login.name').': ');
        else
            $nome = new CHAW_input('name', '', camila_get_translation('camila.login.name').': ');
        $nome->set_size(20);
        $nome->set_maxlength(20);
        $myForm->add_input($nome);
        $myParClose = new HAW_raw(HAW_HTML, '</p>');
        $myForm->add_raw($myParClose);

    }
    //$myParOpen = new HAW_raw(HAW_HTML, '<p>');
    //$myForm->add_raw($myParOpen);
    $thePW1 = new CHAW_input('pwd', '', camila_get_translation('camila.login.password').': ');
    $thePW1->set_size(20);
    $thePW1->set_maxlength(CAMILA_PASSWORD_LENGTH);
    $thePW1->set_type(HAW_INPUT_PASSWORD);
	$thePW1->set_css_class('form-control');
    $myForm->add_input($thePW1);
    $myParClose = new HAW_raw(HAW_HTML, '</p>');
    //$myForm->add_raw($myParClose);

if (CAMILA_USERNAME_LOGIN) {
    $myParOpen = new HAW_raw(HAW_HTML, '<p>');
    $myForm->add_raw($myParOpen);
    //$myCheckbox = new CHAW_checkbox('remembersession', 'yes', camila_get_translation('camila.login.remembersession'));
    //$myCheckbox->set_br(0);
    //$myForm->add_checkbox($myCheckbox);
    $myForm->add_raw($myParClose);

    if (CAMILA_LOGIN_MLANG && $dh2 = opendir(CAMILA_DIR.'lang/')) {
        $myForm->add_raw($myParOpen);
        $mySelect = new CHAW_select('lang');
        while (($file2 = readdir($dh2)) !== false) {
            if (substr($file2,-9) == '.lang.php') {
	        $lang = substr($file2,0,2);
		if ($lang == CAMILA_LANG)
                    $mySelect->add_option(camila_get_translation('camila.lang.' . substr($file2,0,2)), $lang, HAW_SELECTED);
                else
                    $mySelect->add_option(camila_get_translation('camila.lang.' . substr($file2,0,2)), $lang);
            }
        }
	$myForm->add_select($mySelect);
    $myForm->add_raw($myParClose);

	//$myText = new CHAW_text('');
	//$myForm->add_text($myText);
        closedir($dh2);
    }

}

}


if (isset($_REQUEST['output']) && $_REQUEST['output']!='') {
    $o = new CHAW_hidden('output', $_REQUEST['output']);
    $myForm->add_hidden($o);
}

$thePW4 = new CHAW_hidden('js', '');
$myForm->add_hidden($thePW4);

$code = "<script>document.forms[0].js.value = 'enabled';</script>";
$js = new CHAW_js($code, true);
$myForm->add_userdefined($js);

$myForm->add_raw($myParOpen);
$theSubmission = new CHAW_submit(camila_get_translation('camila.login.enter'), 'submit');
$theSubmission->set_css_class('btn btn-lg btn-primary btn-block');
$myForm->add_submit($theSubmission);
$myForm->add_raw($myParClose);

if (!CAMILA_PRIVATE_SERVER) {
    $myHtmlCode = "<div class='camilaopenidlogin'><a href='cf_login_openid.php?action=verify&openid_identifier=https%3a%2f%2fwww.google.com%2faccounts%2fo8%2fid'>".camila_get_translation('camila.login.openid.google')."</a><br /></div>";
    $myGmailLoginBlock = new HAW_raw(HAW_HTML, $myHtmlCode);
    $myForm->add_raw($myGmailLoginBlock);
}

if ($_CAMILA['auth_failed'])
{
    $text = new CHAW_text(camila_get_translation('camila.login.wrongpassword'));
	$text->set_css_class('alert-box');
    $myForm->add_text($text);
}

$_CAMILA['page']->add_form($myForm);

//$myDivClose = new HAW_raw(HAW_HTML, '</div>');
//$_CAMILA['page']->add_raw($myDivClose);

$myLogin = new CHAW_storepwd();
$_CAMILA['page']->add_userdefined($myLogin);

?>