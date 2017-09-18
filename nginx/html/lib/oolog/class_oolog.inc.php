<?php
/**
* Logging Class supports Modi: toFILE, toSCREEN, ifDEBUG, CLEARlogbefore
*
* This Class supports logging to different Destinations.
*
* @copyright heiko * dillemuth de (2004-2005)
* @author Heiko Dillemuth
* @version 0.66
* @todo print_r simulation
**/


/**
* Define Parameter for Logging into File
*
* @see openlog();
**/
define(FILE, 1);

/**
* Define Parameter for Logging into File
*
* @see openlog();
**/
define(LOG, 1);

/**
* Define Parameter for Logging on Screen
*
* @see openlog();
**/
define(SCREEN, 2);

/**
* Define Parameter to print also DEBUG Messages
*
* @see openlog();
**/
define(DEBUG, 4);

/**
* Define Parameter for printing with HTML Linefeed for printout to the browser (<br />)
*
* @see openlog();
**/
define(HTML, 8);

/**
* Define Parameter for Clear Logfile while opening.
*
* @see openlog();
**/
define(CLEAR, 128);

/**
* Define Parameter for Logging into File and Screen
*
* @see openlog();
**/
define(ALL, 3);

/**
* Define Parameter for Logging into File and Browser
*
* @see openlog();
**/
define(ALL-HTML, 11);


class oolog
{
	/**
    * Version string, internal use only
    *
    * @access private
    **/
	var $version="oolog Version 0.66 [20040519, 1911, 1545, 2975]";

	/**
    * Handle for open Logfile
    *
    *
    * @access private
    **/
	var $logh=false;

	/**
    * Var for default modus (htnml, debug,...)
    *
    *
    * @access private
    **/
	var $modus=false;

	/**
    * Exit Error Code for _die();
    *
    *
    * @access public
    **/
	var $ret_err=-1;

	/**
    * Construktor für PHP4
    *
    * @access private
    * @see openlog();
    **/
	function oolog($pfad="default.log", $modus=1)
	{
		return $this->openlog($pfad, $modus);
	}


	/**
    * Construktor für PHP5
    *
    * @access private
    * @see openlog();
    **/
	function __constructor($pfad="default.log", $modus=1)
	{
		return $this->openlog($pfad, $modus);
	}


	/**
    * Open Logfile
    *
    * @param string $pfad Path to logfile (Default is -default.log-)
    * @param int $modus Open-Mode, see Defines like FILE|SCREEN|DEBUG|CLEAR|HTLM
    * @access public
    **/
	function openlog($pfad="default.log", $modus=1)
	{
		$append=(($modus & CLEAR) == CLEAR) ? "w" : "a";      #Clear or append file
		
		if(($modus & FILE) == FILE)         #Logging to file?
		{
			$this->logh=fopen($pfad, $append);
		}

		$this->modus = ($modus>0)  ? $this->modus=$modus : -1;   #Save mode, -1=file only
		
		if($this->printlogheader)								#set to true if...
		$this->log("Start Logging ".$this->version." :M".$modus." H:".$this->logh);
	}

	/**
    * Close log
    *
    * @access public
    **/
	function closelog()
	{
		print "\n";
		fwrite($this->logh, "\n");
		fclose($this->logh);
		$this->logh=false;
	}

	/**
    * Print into log
    *
    * @param string $text Text to be logged
    * @param int $modus Opt. Mode for this line, if diffrent to default
    * @param string $prio PRE String for text, not used currently
    * @param string $line set here the __LINE__ parameter while calling to printout the line no. in the script
    * @access public
    **/
	function log($text, $modus=-1, $prio=false, $line=false)
	{
		if(!$text)                   	#No Text, no Output (useful for ErrorMsgs)
		return false;

		if($this->logh==false)          #Open Output file if not alredy open
		$this->openlog("default.log", FILE|SCREEN);

		if($modus<0)      #set default mode if not set already
		{
			$modus=$this->modus;
		}


		if(($modus & DEBUG) == DEBUG and ($this->modus & DEBUG) == 0 )  #Print DEBUG only if set in line
		{
			return false;
		}

		if($modus == DEBUG) #Wenn nur DEBUG angegeben wurde, dann Rest als
		{                   #Default übernehmen
			$modus=$this->modus|DEBUG;
		}

		$datum=date("d.m.Y H:i:s |");
		$line = ($line) ? "$prio #$line" : $prio;

		$text="\n".$text;
		$pre="$datum $line ";
		$zeile=str_replace("\n", "\n$pre", $text);

		if(($modus & FILE) == FILE)
		{
			if($this->logh == false)                    #Write to File, but its not open
			{
				$zeile="***not allowed to write into FILE*** --> ".$zeile;
			}
			else
			{
				fwrite($this->logh, strip_tags($zeile));
				#fflush($this->logh);
			}
		}

		if(($modus & SCREEN) == SCREEN)
		{
			if(($this->modus & HTML) == HTML)     #Print HTML to SCREEN
			print nl2br(html_entity_decode($zeile));           #Convert it before
			else
			print  strip_tags($zeile);
			flush();
		}
	}


	/**
    * Print text to log, close fogfile and terminate programm with error Code -1
    *
    * @param string $text Text to be logged
    * @param int $modus Opt. Mode for this line, if diffrent to default
    * @param string $prio PRE String for text, not used
    * @access public
    * @see closelog(), log()
    **/
	function _die($text, $modus=-1, $prio=false, $line=false)
	{
		$this->log($text, $modus, $prio, $line);
		$this->closelog();
		exit($this->ret_err);
	}
}#endclass

/*
oolog is a class to log messages to one or different output destinations.
You have to choose by default at least one of this output formats:

1. to Screen (with auto \n or)
2. to Screen (with auto <br> or both)
and/or
3. Logfile (clear it before) or
4. Logfile (append to existing file)
and also
5. Debug messages, which will print only in flagged entries
6. print entries with ALL parameter for errors
or 
7. print and auto _die()

Every line is starting with the current date und time. If needed it is also
possible the change the default output for this line.
Lines with a DEBUG Flag are only printed if the Logfile was opend with enabled debugging.
With ALL flaged lines are used for error msg and printed to all open outputs.

Have a look the class_oolog_test.php-test-file.


2005-07-29:
+ Support multi line outputs (\n)
+ improved output, supports html tags
+ new parameter for log(): added $line parameter for __LINE__
+ _die() supports exit code (default = $this->ret_err=-1)
+ alpha: print header while start logging
! fixed some errors


(c) 2005 by Heiko Dillemuth, Limeshain, Germany
*/
?>