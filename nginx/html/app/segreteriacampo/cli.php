#!/usr/bin/php
<?php

require '../../vendor/autoload.php';

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

require_once('../../camila/autoloader.inc.php');

require('../../camila/config.inc.php');

require('../../camila/i18n.inc.php');
require('../../camila/camila_hawhaw.php');
require('../../camila/database.inc.php');
require('../../camila/plugins.class.inc.php');

class CamilaAppCli extends CLI
{

    protected function setup(Options $options)
    {
		$options->registerCommand('init-app', 'Create new App');
        $options->registerArgument('lang', 'App language', true, 'init-app');
		
		$options->registerCommand('install-plugin', 'Install plugin');
        $options->registerArgument('name', 'Plugin name', true, 'install-plugin');
		$options->registerArgument('lang', 'Plugin language', true, 'install-plugin');
		
		$options->registerCommand('set-config-var', 'Set config var');
        $options->registerArgument('name', 'Config var name', true, 'set-config-var');
		$options->registerArgument('value', 'Config var value', true, 'set-config-var');
		
		$options->registerCommand('show-plugin-info', 'Show plugin info');
        $options->registerArgument('name', 'Plugin name', true, 'show-plugin-info');
    }

    protected function main(Options $options)
    {
        switch ($options->getCmd()) {
            case 'init-app':
				$this->initApp($options);
                break;
			case 'install-plugin':
				$this->installPlugin($options);
                break;
			case 'set-config-var':
				$this->setConfigVar($options);
                break;
			case 'show-plugin-info':
				$this->showPluginInfo($options);
                break;
            default:
                $this->error('No known command was called, we show the default help instead:');
                echo $options->help();
                exit;
        }
    }
	
	protected function initApp(Options $options) {
		$lang = $options->getArgs()[0];
		$camilaApp = new CamilaApp();
		$db = NewADOConnection(CAMILA_DB_DSN);
		$camilaApp->db = $db;
		$camilaApp->lang = $lang;
		$camilaApp->resetTables(CAMILA_TABLES_DIR);	
	}
	
	protected function installPlugin(Options $options) {
		$name = $options->getArgs()[0];
		$lang = $options->getArgs()[1];
		if (is_dir('plugins/'.$name)) {
			$this->error('Plugin already in use!');
		} else {
			$zipFile = bin2hex(random_bytes(10)).'.zip';
			file_put_contents('plugins/'.$zipFile, file_get_contents('https://github.com/linkingtechnologies/camila-php-framework-app-plugin-'.$name.'/archive/refs/heads/main.zip'));
			$zip = new ZipArchive;
			if ($zip->open('plugins/'.$zipFile) === TRUE) {
				$zip->extractTo('plugins/');
				$zip->close();
				rename('plugins/camila-php-framework-app-plugin-'.$name.'-main', 'plugins/'.$name);
				unlink('plugins/'.$zipFile);
				$myfile = fopen('plugins/'.$name . '/conf/repo.json', 'w') or die("Unable to open file for repo info!");
				$txt = $this->getRepositoryInfo($name);
				fwrite($myfile, $txt);
				fclose($myfile);
				global $_CAMILA;
				CamilaPlugins::install($_CAMILA['db'], $lang, $name);
				$this->success('Plugin ' . $options->getArgs()[0] . ' installed!');
			} else {
				$this->error('Error extracting template zip file');
			}
		}
	}
	
	protected function showPluginInfo(Options $options) {
		$name = $options->getArgs()[0];
		$this->info($this->getRepositoryInfo($name));
	}	

	protected function setConfigVar(Options $options) {
		$name = $options->getArgs()[0];
		$value = $options->getArgs()[1];
		
		$reading = fopen('var/1270014001.inc.php', 'r');
		$writing = fopen('var/1270014001.inc.php.tmp', 'w');
		$replaced = false;

		while (!feof($reading)) {
			$line = fgets($reading);
			if (stristr($line, $name) && stristr($line, 'define')) {
				$line = "define('".$name."', '".$value."');\r\n";
				$replaced = true;
				$this->success('Var ' . $name . ' set to ' . $value);
			}
			fputs($writing, $line);
		}
		fclose($reading);
		fclose($writing);
		// might as well not overwrite the file if we didn't replace anything
		if ($replaced) 
		{
			rename('var/1270014001.inc.php', 'var/1270014001.inc.php.old');
			rename('var/1270014001.inc.php.tmp', 'var/1270014001.inc.php');
			unlink('var/1270014001.inc.php.old');
		} else {
			$this->error('Var ' . $name . ' not found!');
			unlink('var/1270014001.inc.php.tmp');
		}
	}

	protected function getRepositoryInfo($name) {
		$url = "https://api.github.com/repos/linkingtechnologies/camila-php-framework-app-plugin-".$name;
		$ch = curl_init();				
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, "PHP");
		//curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}

$cli = new CamilaAppCli();
$cli->run();