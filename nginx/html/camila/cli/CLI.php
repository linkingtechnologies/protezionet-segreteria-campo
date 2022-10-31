<?php

namespace splitbrain\phpcli;

use ZipArchive;
use CamilaApp;
use CamilaPlugins;


/**
 * Class CLI
 *
 * Your commandline script should inherit from this class and implement the abstract methods.
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @license MIT
 */
abstract class CLI extends Base
{
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = array())
    {
        $this->log('emergency', $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array())
    {
        $this->log('alert', $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array())
    {
        $this->log('critical', $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        $this->log('error', $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        $this->log('warning', $message, $context);
    }



    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        $this->log('notice', $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array())
    {
        $this->log('info', $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        $this->log('debug', $message, $context);
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        $this->logMessage($level, $message, $context);
    }
	
	public function registerDefaultCommands(Options $options) {
		$options->registerCommand('exe-remote-cmd', 'Execute remote command');
		$options->registerArgument('url', 'Remote URL', true, 'exe-remote-cmd');
		$options->registerArgument('cmd', 'Command', true, 'exe-remote-cmd');
	}

	public function registerMasterCommands(Options $options) {
		$options->registerCommand('create-app', 'Create new App');
        $options->registerArgument('slug', 'App slug', true, 'create-app');
		$options->registerArgument('template', 'App template', true, 'create-app');
		$options->registerArgument('lang', 'App language', true, 'create-app');
	}
	
	public function handleMasterCommands(Options $options) {
	    switch ($options->getCmd()) {
            case 'create-app':
				$this->createApp($options);
                break;
			case 'exe-remote-cmd':
				$this->executeRemoteCommand($options);
                break;
            default:
                $this->error('No known command was called, we show the default help instead:');
                echo $options->help();
                exit;
        }
	}
	
	public function registerAppCommands(Options $options) {
		$options->registerCommand('init-app', 'Create new App');
        $options->registerArgument('lang', 'App language', true, 'init-app');
		
		$options->registerCommand('install-plugin', 'Install plugin');
        $options->registerArgument('name', 'Plugin name', true, 'install-plugin');
		$options->registerArgument('lang', 'Plugin language', true, 'install-plugin');

		$options->registerCommand('init-plugin', 'Init plugin');
        $options->registerArgument('name', 'Plugin name', true, 'init-plugin');
		$options->registerArgument('lang', 'Plugin language', true, 'init-plugin');
		
		$options->registerCommand('set-config-var', 'Set config var');
        $options->registerArgument('name', 'Config var name', true, 'set-config-var');
		$options->registerArgument('value', 'Config var value', true, 'set-config-var');
		
		$options->registerCommand('show-plugin-info', 'Show plugin info');
        $options->registerArgument('name', 'Plugin name', true, 'show-plugin-info');
	}
	
	public function handleAppCommands(Options $options) {
		switch ($options->getCmd()) {
            case 'init-app':
				$this->initApp($options);
                break;
			case 'install-plugin':
				$this->installPlugin($options);
                break;
			case 'init-plugin':
				$this->initPlugin($options);
                break;
			case 'set-config-var':
				$this->setConfigVar($options);
                break;
			case 'show-plugin-info':
				$this->showPluginInfo($options);
                break;
			case 'exe-remote-cmd':
				$this->executeRemoteCommand($options);
                break;
            default:
                $this->error('No known command was called, we show the default help instead:');
                echo $options->help();
                exit;
        }
	}

	public function executeRemoteCommand(Options $options) {
		$url = $options->getArgs()[0];
		$cmd = $options->getArgs()[1];
		$url = $url.'/cli.php';//?cmd='.urlencode($cmd);
		//echo file_get_contents($url);

		$c = array('cmd' => $cmd);
		$content = json_encode($c);
		$curl = curl_init($url);
		//curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($status != 201 && $status != 200) {
			die("Error: call to URL $url failed with status $status, response $response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
		}

		curl_close($curl);
		echo $response;
		//$response = json_decode($json_response, true);
	}
	
	/*protected function createApp(Options $options) {
		$slug = $options->getArgs()[0];
		$template = $options->getArgs()[1];
		$lang = $options->getArgs()[2];
		if (is_dir('app/'.$slug)) {
			$this->error('Slug already in use!');
		} else {
			$zipFile = bin2hex(random_bytes(10)).'.zip';
			file_put_contents('app/'.$zipFile, file_get_contents('https://github.com/linkingtechnologies/camila-php-framework-app-template-'.$template.'/archive/refs/heads/main.zip'));
			$zip = new ZipArchive;
			if ($zip->open('app/'.$zipFile) === TRUE) {
				$zip->extractTo('app/');
				$zip->close();
				rename('app/camila-php-framework-app-template-'.$template.'-main', 'app/'.$slug);
				unlink('app/'.$zipFile);
				$this->success('App ' . $options->getArgs()[0] . ' created!');
				echo shell_exec('cd app && cd ' . $slug . ' && php cli.php init-app ' . $lang);
			} else {
				$this->error('Error extracting template zip file');
			}
		}
	}*/
	
	protected function createApp(Options $options) {
		$slug = $options->getArgs()[0];
		$template = $options->getArgs()[1];
		$lang = $options->getArgs()[2];
		if (is_dir('app/'.$slug)) {
			$this->error('Slug already in use!');
		} else {
			$zipFile = bin2hex(random_bytes(10)).'.zip';
			$templateSrc = 'https://github.com/linkingtechnologies/camila-php-framework-app-template-'.$template.'/archive/refs/heads/main.zip';

			/*$handle = fopen($templateSrc, "rb");
			$contents = fread($handle, filesize($templateSrc));
			fclose($handle);*/
			/*if (!file_get_contents($templateSrc)){
				echo ":-(";
			}*/
			if (file_put_contents('app/'.$zipFile, file_get_contents($templateSrc))) {
				$zip = new ZipArchive;
				if ($zip->open('app/'.$zipFile) === TRUE) {
					$zip->extractTo('app/');
					$zip->close();
					rename('app/camila-php-framework-app-template-'.$template.'-main', 'app/'.$slug);
					unlink('app/'.$zipFile);
					$this->success('App ' . $options->getArgs()[0] . ' created!');
					//echo shell_exec('cd app && cd ' . $slug . ' && php cli.php init-app ' . $lang);
				} else {
					$this->error('Error extracting template zip file');
				}
			} else {
					$this->error('Error downloading template zip file ' . $templateSrc);
				}
			
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
				//CamilaPlugins::install($_CAMILA['db'], $lang, $name);
				$this->success('Plugin ' . $options->getArgs()[0] . ' installed!');
			} else {
				$this->error('Error extracting template zip file');
			}
		}
	}

	protected function initPlugin(Options $options) {
		$name = $options->getArgs()[0];
		$lang = $options->getArgs()[1];
		if (!is_dir('plugins/'.$name)) {
			$this->error('Plugin ' . $name . ' not installed!');
		} else {
			global $_CAMILA;
			CamilaPlugins::install($_CAMILA['db'], $lang, $name);
			$this->success('Plugin ' . $options->getArgs()[0] . ' initialized!');
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
