<?php
/**
 * Io.
 * @filesource
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @copyright Copyright © 2006, guillaume luchet
 * @package Io
 * @version CVS: $Id: Io.php,v 1.4 2007/01/05 23:04:02 geelweb Exp $
 */

// doc {{{
/**
 * Manage the command-line.
 *
 * This class manage the command-line arguments. 
 *
 * The default availables arguments are :
 * - -h, --help      show the help message.
 *
 * Command line arguments definition: 
 * <code>
 * array(
 *     'tag'  => array,  // array of tags to used the option
 *     'desc' => string, // the option's description to the help message
 *     'type' => string, // the option's type [set|use]
 *                       // if set, see validvalues()
 *                       // if use, the option's value in the array returned by 
 *                       // Io::_parseArgv() will be true.
 *     'validvalues' => array // if type equal set the option's value must be 
 *                            //one of them define in this array
 * )
 * </code>
 *
 * Usage:
 * <code>
 * #!/bin/sh
 * #
 * # shell scrip to use xml2pdf in command line.
 *
 * PHP=`which php`
 * exec $PHP -C -q -d output_buffering=1 $0 $@
 * <?php
 * ob_end_clean();
 * include('/usr/share/Io/Io.php');
 * 
 * $io = new Io('xml2pdf', 'xml2pdf -f file.xml');
 * $io->addOption('filename', 'filename', 'f', 'xml file path to parse');
 * $io->addOption('output', 'output', 'o', 'output file name');
 * $io->setDefaults(array('output'=>'/tmp/doc.pdf'));
 * $setting = $io->parseArgv();
 *
 * include('/usr/share/xml2pdf/Xml2Pdf.php');
 * $obj = new Xml2Pdf($setting['filename']);
 * $pdf = $obj->render();
 * $pdf->Output($setting['output'], 'F');
 * ?>
 * </code>
 *
 * @author guillaume l. <guillaume@geelweb.org>
 * @link http://www.geelweb.org
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @copyright Copyright © 2006, guillaume luchet
 * @package Io
 * @version CVS: $Id: Io.php,v 1.4 2007/01/05 23:04:02 geelweb Exp $
 */ // }}}
class Io {
    // class properties {{{
    
    /**
     * Available command-line arguments.
     *
     * format of the array :
     * <code>
     * array(
     *    'optionnameA' => array(
     *                      'tag' => array('-a', '--optionnameA'), 
     *                      'desc' => 'description to the help message',
     *                      'type' => 'set',
     *                      'validvalues' => array('foo', 'bar', 'wiz'))
     *    'optionnameB' => array(i
     *                      'tag' => array('-b', '--optionnameB'),
     *                      'desc' => 'description to the help message',
     *                      'type' => 'use')
     * );
     * </code>
     * @var array
     */
    protected $args = array();

    /**
     * Define the default settings
     *
     * <code>array('optionnameA'=>'foo');</code>
     * @var array
     */
    protected $defaultsOpts = array();

    /**
     * Define the application name.
     *
     * @var string
     */
    protected $app;

    /**
     * Define the command line usage
     *
     * @var string
     */
    protected $usage;
    
    // }}}
    // Io::__construct() {{{

    /**
     * Constructor.
     * 
     * Initialize available options of the command-line.
     *
     * @param string $app Application name
     * @param string $usage Command line usage
     * @return void
     */
    public function __construct($app='', $usage='') {
        $this->app = $app;
        $this->usage = $usage;

        $this->args['help']['tag'] = array('-h', '--help');
        $this->args['help']['desc'] = 'show this help message.';
    }

    // }}}
    // Io::addOption() {{{

    /**
     * Add a command line option.
     *
     * @param string $option option name
     * @param string $tag long option usage
     * @param string $shortcut shortcut
     * @param string $help description for help message
     * @param string $type option type
     * @param array $valieValues valid values for options
     * @return void
     */
    public function addOption($option, $tag, $shortcut, $help='', $type=false, $validValues=false) {
        $this->args[$option] = array(
            'tag' => array('-'.$shortcut, '--'.$tag),
            'desc' => $help);
        if($type && is_string($type)) {
            $this->args[$option]['type'] = $type;
        }
        if($validValues && is_array($validValues)) {
            $this->args[$option]['validvalues'] = $validValues;
        }
    }

    // }}}
    // Io::setOptions() {{{

    /**
     * Set the command line options.
     *
     * @param array $opts Options
     * @param boolean $merge Merge the options width existing options.
     * @return void
     */
    public function setOptions($opts, $merge=false) {
        if(!is_array($opts)) {
            $this->_showErrorMsg('Io::setOptions() opts param must be an array.');
        }
        if($merge) {
            $this->args += $opts;
        } else {
            $this->args = $opts;
        }
    }

    // }}}
    // Io:setDefaults() {{{
    
    /**
     * Set the defaults command line options values.
     *
     * @param array $defaults defaults options
     * @return void
     */
    public function setDefaults($defaults) {
        if(!is_array($defaults)) {
            $this->_showErrorMsg('Io::setDefaults() defaults param must be an array');
        }
        $this->defaultsOpts = $defaults;
    }
    
    // }}}
    // Io::displayHelpMessage() {{{
    
    /**
     * Display the help message.
     *
     * Return a formated string with the command-line options and descriptions.
     *
     * @return string
     */
    public function displayHelpMessage() {
        $spacer = "     ";
        $bigSpacer = "               ";

        $ret = "\n{$this->app} usage :\n";
        $ret .= $spacer . "{$this->usage}\n";
        $ret .= "\n{$this->app} options :\n";
        
        foreach($this->args as $option) {
            $tag ="";
            $desc = "";
            if(isset($option['tag'])) {
                foreach($option['tag'] as $param) {
                    $tag .= $spacer . $param;
                }
            }
            if(isset($option['desc'])) {
                $desc = $bigSpacer . $option['desc'];
            }
            $ret .= $tag . "\n" . $desc . "\n";
        }        
        $ret .= "\n";
        return $ret;
    }

    // }}}
    // Io::parseArgv() {{{
    
    /**
     * Parse the command-line arguments.
     *
     * Return an array with the arguments and values passed to the
     * command-line.
     *
     * array format : 
     * <code>
     * array(
     *    'optionnameA' => 'optionnameA value',
     *    'optionnameB' => 'optionnameB value'
     * );
     * </code>
     *
     * @return array
     */
    public function parseArgv() {
        global $argv;
        
        // set default setting
        $setting = $this->defaultsOpts;
        
        $valnext = "junk";
        $data = array();
        
        if(isset($argv) && is_array($argv)) {
            foreach ($argv as $cmd) {
                if ($cmd == '--') {
                    continue;
                }
                if ($cmd == '-h' || $cmd == '--help') {
                    echo $this->displayHelpMessage();
                    die();
                }
                try {
                    $setting[$valnext] = $this->_validOption($valnext, $cmd);
                } catch(Exception $e) {
                    $this->_showErrorMsg($e->getMessage());
                }
                foreach( $this->args as $name => $data ) {
                    if (!empty($data['tag'])) {
                        if (in_array($cmd,$data['tag'])) {
                            $valnext = $name;
                            // Maybe it's the last tag ?
                            if(isset($this->args[$valnext]['type']) && $this->args[$valnext]['type']=='use') {
                                $setting[$valnext] = true;
                            }
                            break;
                        } else {
                            $valnext = "junk";
                        }
                    }
                }
                if ($valnext == 'junk' && (strpos(trim($cmd),'-') === 0)) {
                    $this->_showErrorMsg("Unknow command line option: $cmd (use -h option to display help message).");
                }
            }
        } else {
            $this->_showErrorMsg("Please use php-cli.exe in windows, or set register_argc_argv On.");
        }
        unset($setting['junk']);
        return $setting;
    }

    // }}}
    // Io::_showErrorMsg() {{{
    
    /**
     * Show an error message
     *
     * @param $msg
     * @return void
     */
    private function _showErrorMsg($msg) {
        echo "$msg\n";
        die();
    }
    
    // }}}
    // Io::_validOption() {{{
    
    /**
     * Check the validation rules for an option.
     *
     * @param string $opt Option name
     * @param mixed $value value
     * @return mixed
     */
    private function _validOption($opt, $value) {
        if(isset($this->args[$opt]['type']) && $this->args[$opt]['type']=='set') {
            if(!in_array($value, $this->args[$opt]['validvalues'], true)) {
                throw new Exception(sprintf("Invalid value %s for option %s (%s).\n    Valid values are: %s\n",
                    $value, implode(',', $this->args[$opt]['tag']), $opt, 
                    implode(',', $this->args[$opt]['validvalues'])));
            }
            return $value;
        }
        if(isset($this->args[$opt]['type']) && $this->args[$opt]['type']=='use') {
            if(!(strpos(trim($value), '-')===0 || strpos(trim($value), '--')===0)) {
                throw new Exception(sprintf("%s option do not must have a value.\n", $opt));
            }
            return true;
        }
        return $value;
    }
    
    // }}}
}
?>
