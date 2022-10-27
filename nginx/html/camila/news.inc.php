<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2022 Umberto Bresciani

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


function camila_news_init($usergroup) {

  global $_CAMILA;
  $query = 'select id from camila_news_services WHERE id not in (select service_id from camila_news_counters where usergroup='.$_CAMILA['db']->qstr($usergroup).')';

  $result = $_CAMILA['db']->Execute($query);
  if ($result === false)
      camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

  while (!$result->EOF) {
      $result2 = $_CAMILA['db']->Execute("insert into camila_news_counters(service_id,usergroup,status,value) VALUES (?,?,?,?)", Array($result->fields['id'], $usergroup, 'active', 0));
          if ($result2 === false)
              camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

      $result->MoveNext();
  }
}


function camila_news_counter_add($usergroup, $service_id, $num) {

  global $_CAMILA;
  $result = $_CAMILA['db']->Execute("update camila_news_counters set value=value+? where usergroup=? and service_id=?", Array($num,$usergroup, $service_id));
  if ($result === false)
      camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());

}


function camila_news_counter_value($usergroup, $service_id) {

  global $_CAMILA;
  $result = $_CAMILA['db']->Execute("select value from camila_news_counters where usergroup=? and service_id=?", Array($usergroup, $service_id));
  if ($result === false)
      camila_error_page(camila_get_translation('camila.sqlerror') . ' ' . $_CAMILA['db']->ErrorMsg());
  return $result->fields['value'];
}

?>
