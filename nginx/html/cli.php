<?php
require './vendor/autoload.php';

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

require './camila/cli/Exception.php';
require './camila/cli/TableFormatter.php';
require './camila/cli/Options.php';
require './camila/cli/Base.php';
require './camila/cli/Colors.php';
require './camila/cli/CLI.php';

class CamilaMasterCli extends CLI
{
    protected function setup(Options $options)
    {
		$this->registerDefaultCommands($options);
		$this->registerMasterCommands($options);
    }

    protected function main(Options $options)
    {
		$this->handleMasterCommands($options);
    }
}

$cli = new CamilaMasterCli();
$cli->run();