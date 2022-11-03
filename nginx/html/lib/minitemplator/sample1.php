<?php

// File sample1.php
// A simple example of how to use the MiniTemplator class.

require_once ("MiniTemplator.class.php");

$t = new MiniTemplator;

$t->readTemplateFromFile ("sample1_template.htm");

$t->setVariable ("animal1","fox");
$t->setVariable ("animal2","dog");
$t->addBlock ("block1");

$t->setVariable ("animal1","horse");
$t->setVariable ("animal2","cow");
$t->addBlock ("block1");

$t->generateOutput();

?>
