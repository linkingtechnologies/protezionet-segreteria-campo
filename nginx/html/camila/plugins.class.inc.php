<?php
/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2017 Umberto Bresciani

   Camila PHP Framework is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Camila PHP Framework is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Camila PHP Framework; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */

class CamilaPlugins
{

	public static function getList($db) {
		$res = Array();
		$files = CamilaFileManagement::listDir(CAMILA_APP_PATH . '/plugins'); 
		sort($files, SORT_LOCALE_STRING);

		foreach($files as $file) {
			if ($file != '.' && $file != '..') {
				$arr['id'] = $file;
				$arr['status'] = self::getPluginStatus($db, $file);
				$res[] = $arr;
			}
		}
		return $res;
	}

	public static function getPluginStatus($db, $pluginId) {
		$old = $db->SetFetchMode(ADODB_FETCH_ASSOC);
		$query = 'SELECT status FROM ' . CAMILA_TABLE_PLUGINS . ' WHERE id = ' . $db->qstr($pluginId);
		$result = $db->Execute($query);
		$db->SetFetchMode($old);
		return $result->fields['status'];
	}

	public static function getPluginType($db, $pluginId) {
		$old = $db->SetFetchMode(ADODB_FETCH_ASSOC);
		$query = 'SELECT type FROM ' . CAMILA_TABLE_PLUGINS . ' WHERE id = ' . $db->qstr($pluginId);
		$result = $db->Execute($query);
		$db->SetFetchMode($old);
		return $result->fields['type'];
	}
	
	public static function getPluginInformation($pluginId) {
		return new SimpleXMLElement(file_get_contents(CAMILA_APP_PATH . '/plugins/'.$pluginId.'/conf/plugin.xml'));
	}

	public static function install($db, $lang, $pluginId) {
		global $_CAMILA;
		$camilaApp = new CamilaApp();
		$camilaApp->db = $db;
		$camilaApp->lang = $lang;
		$camilaApp->resetTables(CAMILA_APP_PATH . '/plugins/'.$pluginId.'/tables');
		$camilaApp->resetWorkTables(CAMILA_APP_PATH . '/plugins/'.$pluginId.'/tables');
		CamilaFileManagement::copyFiles(CAMILA_APP_PATH . '/plugins/'.$pluginId.'/templates/'.$lang,CAMILA_TMPL_DIR.'/'.$lang,'txt',false);
		CamilaFileManagement::copyFiles(CAMILA_APP_PATH . '/plugins/'.$pluginId.'/templates/images/'.$lang,CAMILA_TMPL_DIR.'/images/'.$lang,'',false);
		
		$record  = Array();
		$record['id'] = $pluginId;
        $record['status'] = 'active';
        $record['type']  = (String)CamilaPlugins::getPluginInformation($pluginId)->info->type;

        $insertSQL = $camilaApp->db->AutoExecute(CAMILA_TABLE_PLUGINS, $record, 'INSERT');
		if (!$insertSQL) {
			camila_information_text(camila_get_translation('camila.worktable.db.error'));
        }
		
		$pluginType = self::getPluginType($db, $pluginId);
		if ($pluginType == 'index')
		{
			$myfile = fopen(CAMILA_PLUGINS_DIR.'/'.$pluginType.'.txt', "w") or die("Unable to open file!");
			fwrite($myfile, $pluginId);
			fclose($myfile);
		}
	}
}

?>