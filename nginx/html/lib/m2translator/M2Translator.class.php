<?php
/*
M2 Translator - Simple translation system

Copyright (C) 2005  Martin Grund

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

You can contact the developer by mail
Martin Grund - dev@grundprinzip.de 
http://www.grundprinzip.de

*/

/**
*  This class provides basic internationalization support for your application.
*  You are able to define multiple language files and access the single stings by
* a keyword you defined in the language file. The format of the language file
* is formed like the following example.
*
* section = translation
*
* The Filename of the language file must have the following format: lang_name.lang.php
* where langname is the desired language name.
*
* Furthermore you are able to access subsection to separate between different parts
* of your application. A subsection is defined like this:
*
* section.subsection = translation
*
* If you want to access a translation just take the M2Translator object and call
* the get() function like this:
*
* $i18n->get("example")
*
* This function returns the translated string or *keyword* if there is no translation
* available for this keyword.
* 
* If you want to extract a single section just work like this:
*
* subsection.keyword = translation
*
* $subsection = $i18n->getSection("subsection");
* $subsection->get("keyword");  // THis will return "translation"
*
*/

    class M2Translator
    {
        
        var $translations = array();
        var $content;
        var $langDir = "lang/";
        
        /**
        * Load Translation from File
        * @param String $langCode The international language Code
        *
        */
        function M2Translator($langCode = null, $langDir = null)
        {
        	if ($langDir != null)
        		$this->langDir = $langDir;
        	
            if ($langCode != null)
            {
                $this->loadTranslationFile($this->langDir."$langCode.lang.php");
                $this->processLanguage();
            }    
             
        }
        
        /**
        * Load Translation File defined by path
        *
        * @param String path Path to the Translation file
        * @return boolean
        */
        function loadTranslationFile($path)
        {
            if (is_readable($path))
            {
                $content = file_get_contents($path);
                $this->content = $content;
                
            } else 
                return false;
        }
        
        /**
        * Process the content of the language file to retreive the correct
        * translations
        *
        */
        function processLanguage()
        {
            $entries = explode("\n", $this->content);
            
            foreach ($entries as $line) {
            	
                //Right part is translation, left part is section category
                $vals = explode("=", $line,2);
                //Ignore comments
                $tmp = trim($line);
                if ($tmp{0} != "#" && !empty($line))
                    $this->translations[trim($vals[0])] = trim($vals[1]);
            }
            
        }
        
        /**
        * Translate requestet element
        * @param String $element element to be translated
        * @return String
        */
        function translate($element)
        {
            if (!key_exists($element, $this->translations) || strlen($this->translations[$element]) == 0)
            {
                return "*".$element."*";
            } else 
                return $this->translations[$element];
        }
        
        /**
        * Translate requestet element. Dummy function for this->translate
        * @param String $element element to be translated
        * @return String
        */
        function get($element)
        {
        	return $this->translate($element);
        }
        
        
        /**
        * This method is meant to make getting the right element easier.
        * Therefore you give the beginning of the disrired path and now you
        * will get a new M2Translator Object only with desired elements.
        */
        function getSection($path)
        {
            
            //Find all Elements that start with $path
            foreach ($this->translations as $key => $val) {
                //echo $key;
                $tmp = strpos($key, $path);
                if (is_int($tmp) && $tmp == 0)
                {
                    $tmpArr = explode(".",$key);
                    
                    $tmpArr = array_reverse($tmpArr);
                    $cnt = substr_count($path, ".");
                    for ($i=0; $i <=$cnt; $i++)
                    {
                        array_pop($tmpArr);
                    }
                    $tmpArr = array_reverse($tmpArr);
                    $string = join(".", $tmpArr);
                    
                    $return[$string] = $val;
                    
                }
            }
            
            $section = new M2Translator;
            $section->translations = $return;
            
            return $section;
        }
        
        
    }


?>
