<?php

/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2009 Umberto Bresciani

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


$hidden = basename($_SERVER['PHP_SELF']);

if ($_SERVER['QUERY_STRING'] != '')
    $hidden = $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];

if (basename($_SERVER['PHP_SELF']) != CAMILA_LOGIN_HOME)
    echo "<input type=\"hidden\" name=\"camila_redirect\" value=\"".$hidden."\"/>";
?>
<input type="hidden" name="camila_pwloginbox" value="yes" />
<br />
<label for="name"><?php echo htmlentities(camila_get_translation('camila.login.name')); ?></label>
<input type="text" name="name" id="name" value="" size="20" maxlength="20" /> <br />
<label for="surname"><?php echo htmlentities(camila_get_translation('camila.login.surname')); ?></label>
<input type="text" name="surname" id="surname" value="" size="20" maxlength="20" />
<input type="hidden" name="p1" id="p1" value="" />
<input type="hidden" name="p2" id="p2" value="" />
<input type="hidden" name="p3" id="p3" value="" /> <br />
<label for="camila_pass"><?php echo htmlentities(camila_get_translation('camila.login.password')); ?></label>
<input type="password" name="camila_pass" id="camila_pass" value="" /> <br />
<input type="submit" name="submit" value="<?php echo camila_get_translation('camila.login.enter'); ?>" /><br /><br />
</form>
