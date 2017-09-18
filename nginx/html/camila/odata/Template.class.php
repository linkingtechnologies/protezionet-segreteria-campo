<?php

class Template {

	private $vars  = array();
	
	public function render($template_name) {

		if ($template_name == 'templates/service_description.xml' || $template_name == 'templates/metadata.xml')
			header('Content-type: application/xml');
		else
			header('Content-type: application/atom+xml');

		extract($this->vars);

		ob_start();
		include($template_name);
		return ob_get_clean();
	}
	
	public function __get($name) {
		return $this->vars[$name];
	}
	
	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}
}
?>