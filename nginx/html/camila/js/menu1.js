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


function camilamenu_init(menuitems)
{
var parents = new Array();
var titles = new Array();
var visible = new Array();
var urls = new Array();

eval("var object = " + menuitems);

if (object == null)
    return;

for (var key1 in object) {

    var o2=object[key1];
    for (var key2 in o2) {
	    if (key2 == "parent")
	    	parents[key1]=o2[key2];
	    if (key2 == "short_title")
	    	titles[key1]=o2[key2];
	    if (key2 == "visible")
	    	visible[key1]=o2[key2];
	    if (key2 == "url")
	    	urls[key1]=o2[key2];
	}
}

domMenu_data.set('camilamenuleft', print_children(parents,titles,visible,urls,''));

domMenu_settings.set('camilamenuleft', new Hash(
    'axis', 'vertical',
    'verticalSubMenuOffsetX', -2,
    'verticalSubMenuOffsetY', -1,
    'horizontalSubMenuOffsetX', 1,
    'expandMenuArrowUrl', window['camila_messages']['CAMILA_IMG_DIR'] + 'gif/icon_submenu_arrow.gif',
    'baseUri', 'http://www.mojavelinux.com'
));

domMenu_activate('camilamenuleft', true)

}