<?php
$camilaUI->insertTitle('Carica loghi', 'calendar');

$lang = 'it';
$camilaTemplate = new CamilaTemplate($lang);
$params = $camilaTemplate->getParameters();

$form = new phpform('templeteparams');
$form->submitbutton = 'Carica immagini';
$form->drawrules = true;
$form->preservecontext = true;

foreach ($params as $key => $val) {
	if ($key == 'logo' || $key == 'logo2') {
		new form_filebox($form, $key, $key, 50, CAMILA_TMPL_DIR . '/images/' . $lang . '/');
		$form->fields[$key]->set_br(2);
	}
}

if ($form->process())
{
	$text = new CHAW_text('');
	$_CAMILA['page']->add_text($text);
	camila_information_text('I file sono stati caricati correttamente!');
}
	else
		$form->draw();
?>