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


function camila_os3grid_init(g, content_id) {
    g.set_scrollbars(true);
    g.set_border(1, 'solid', '#cccccc');
    g.show_row_num(true);
    g.set_row_select(true);
    g.set_highlight(true);
    g.start_counter = 1;
    g.set_sortable(true);

    for (i=0; i<xGetElementsByTagName("input").length; i++) {
        if (xGetElementsByTagName("input")[i].name == content_id)
            xGetElementsByTagName("input")[i].id=xGetElementsByTagName("input")[i].name;
    }

    var a = xGetElementById(content_id).parentNode.id;
    xAddEventListener(a, 'submit', camila_os3grid_save2, false);
}

function camila_os3grid_save2() {
    for (i=0; i < xGetElementsByTagName("div").length; i++) {
        var name = xGetElementsByTagName("div")[i].id;
        if ((name.lastIndexOf("_os3grid") > 0) && (name.lastIndexOf("_os3grid") == name.length-8)) {
            camila_os3grid_save(name, false);
        }
    }
    return false;
}

function camila_os3grid_col_click_cb(header, grid_id) {

    if (camila_shiftMode()) {
        grid = os3grid_get_grid(grid_id);
        res = header.id.split ( "_gh" );
	num  = res [ 1 ];
        var newVal = prompt('', grid.headers[num]);
        if (newVal) {
            grid.headers[num] = newVal;
            grid.render();
        }
        camila_shiftModeStatus = false;
    } else {
        grid_header_click(header);
    }
}

function camila_os3grid_save(grid_id, submit) {
    grid = os3grid_get_grid(grid_id);

    var l = grid.length();
    var t, i, j;
    var data, csv;
    csv = '';

    for (j = 0; j < grid.headers.length; j++) {
        if (j == (grid.headers.length - 1))
           csv = csv + "\"" + grid.headers[j].replace("\"","\\\"") + "\"\n";
        else
           csv = csv + "\"" + grid.headers[j].replace("\"","\\\"") + "\",";
    }

    for ( t = 0; t < l; t++ ) {
        data = grid.get_row(t);
        for (i = 0; i < data.length; i++) {
            if (i == (data.length - 1))
               csv = csv + "\"" + data[i].replace("\"","\\\"") + "\"\n";
            else
               csv = csv + "\"" + data[i].replace("\"","\\\"") + "\",";
        }
    }

    var a = xGetElementById(grid_id.substr(0,grid_id.length-8));
    a.value = csv;

    if (submit) {
        a.parentNode.submit();
    }

}


function camila_os3grid_delrows(grid_id) {
    grid = os3grid_get_grid(grid_id);

    var l = grid.length();

    var t, l, count;
    count = 0;

    for ( t = 0; t < l; t++ ) {
        if (grid.rows_selected [t])
            count++;
    }

    if (count == 0) {
        alert('Selezionare almeno una riga!');
    } else {
            if (confirm('Confermi l\'eliminazione delle righe selezionate?')) {
            for ( t = 0; t < l; t++ ) {
                if (grid.rows_selected [t])
                    grid.rows.splice(t, 1);
            }
            grid.render();
        }
    }
}


function camila_switch_class(element, class_name, lock_state) {
    var lockChanged = false;

    if (typeof(lock_state) != "undefined" && element != null) {
        element.classLock = lock_state;
        lockChanged = true;
    }

    if (element != null && (lockChanged || !element.classLock)) {
        element.oldClassName = element.className;
        element.className = class_name;
    }
};
	

function camila_restore_and_switch_class(element, class_name) {
    if (element != null && !element.classLock) {
        camila_restore_class(element);
        camila_switch_class(element, class_name);
    }
};


function camila_restore_class(element) {
    if (element != null && element.oldClassName && !element.classLock) {
        element.className = element.oldClassName;
        element.oldClassName = null;
    }
};
