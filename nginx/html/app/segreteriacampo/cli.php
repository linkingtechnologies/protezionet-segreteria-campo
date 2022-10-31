<?php

require '../../vendor/autoload.php';


require_once('../../camila/autoloader.inc.php');

require('../../camila/config.inc.php');

require('../../camila/i18n.inc.php');
require('../../camila/camila_hawhaw.php');
require('../../camila/database.inc.php');
require('../../camila/plugins.class.inc.php');

require CAMILA_DIR. 'cli/Exception.php';
require CAMILA_DIR. 'cli/TableFormatter.php';
require CAMILA_DIR. 'cli/Options.php';
require CAMILA_DIR. 'cli/Base.php';
require CAMILA_DIR. 'cli/Colors.php';
require CAMILA_DIR. 'cli/CLI.php';

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;
use splitbrain\phpcli\Exception;


class CamilaAppCli extends CLI
{
	protected function setup(Options $options)
    {
		$this->registerDefaultCommands($options);
		$this->registerAppCommands($options);
    }

    protected function main(Options $options)
    {
		$this->handleAppCommands($options);
    }
}

$cli = new CamilaAppCli();
$cli->run();