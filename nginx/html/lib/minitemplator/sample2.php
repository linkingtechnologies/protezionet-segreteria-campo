<?php

// File sample2.php
// A more complex example of how to use the MiniTemplator class.

require_once ("MiniTemplator.class.php");

function generateCalendarPage() {
   $t = new MiniTemplator;
   $ok = $t->readTemplateFromFile("sample2_template.htm");
   if (!$ok) die ("MiniTemplator.readTemplateFromFile failed.");
   $t->setVariable ("year","2003");
   $t->setVariable ("month","April");
   for ($weekOfYear=14; $weekOfYear<=18; $weekOfYear++) {
      for ($dayOfWeek=0; $dayOfWeek<7; $dayOfWeek++) {
         $dayOfMonth = ($weekOfYear*7 + $dayOfWeek) - 98;
         if ($dayOfMonth >= 1 && $dayOfMonth <= 30)
            $t->setVariable ("dayOfMonth",$dayOfMonth);
          else
            $t->setVariable ("dayOfMonth","&nbsp;");
         $t->addBlock ("day"); }
      $t->setVariable ("weekOfYear",$weekOfYear);
      $t->addBlock ("week"); }
   $t->generateOutput(); }

generateCalendarPage();

?>
