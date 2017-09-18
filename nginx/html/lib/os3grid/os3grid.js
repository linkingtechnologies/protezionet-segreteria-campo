/*
	OS3Grid v0.6

	by Fabio Rotondo - fsoft ( at ) sourceforge.net

	0.6: 	- get_col_attrs ()
		  set_row_attr ()
		  get_row_attrs ()
		  length ()

		  GLOBAL: os3grid_get_grid ()
			  os3grid_set_cell_value()

		  "render" callback passes the full_id as the second argument

*/

// ===================================================================
// GLOBAL STUFF - Init global data class
// ===================================================================
function os3_grid_global_data ()
{
	this.grid_arr = new Array ();
	this.sort_field = 0;
	this.sort_inverted = 0;
}

// ===================================================================
// PUBLIC FUNCTIONS
// ===================================================================
function os3grid_get_grid ( id ) 
{
	return _os3_grid_global_data.grid_arr [ id ];
}

function os3grid_set_cell_value ( full_id, val )
{
	var v = full_id.split ( ":" );
        var g = os3grid_get_grid ( v [ 0 ] );
	if ( ! g ) return 0
        var r = g.get_row ( v [ 2 ] );

        r [ v [ 1 ] ] = val;

	return g
}

var _os3_grid_global_data = new os3_grid_global_data ();
var _os3g_resize_cell;
var _os3g_resize_start_x = -1;

// Flag T/F to know if the current browser is the almighty bugged Internet Exploder.
var _os3g_is_ie = ( document.all != null );

// ===================================================================
// Grid Resize Functions
// ===================================================================
function grid_resize_cell_down ( id )
{
	_os3g_resize_cell = document.getElementById ( id );

	document.onmousemove = grid_resize_callback;
	if ( ! _os3g_is_ie ) 
	{
		document.captureEvents(Event.MOUSEMOVE);
	} else {
		while ( ! _os3g_resize_cell )
		{
			_os3g_resize_cell = document.getElementById ( id );
			if ( confirm ( "The buggy Internet Explorer cannot get the ID: " + id + ". Try again?\nBTW: You should really consider to switch to Mozilla Firefox (www.getfirefox.com)" ) == false ) break;
		}
	}
}

function grid_resize_cell_up ()
{
	if ( ! _os3g_resize_cell ) return;

	document.onmousemove = null;

	// later
	if ( ! _os3g_is_ie )
	{
		document.releaseEvents(Event.MOUSEMOVE);
	}

	if ( _os3g_resize_cell.old_className ) _os3g_resize_cell.className = _os3g_resize_cell.old_className;
	_os3g_resize_start_x = -1;

	// Save the new column width inside _column_width array;
	var res = _os3g_resize_cell.id.split ( "_th" );
	var name = res [ 0 ];
	var num  = res [ 1 ];

	var grid = _os3_grid_global_data.grid_arr [ name ];
	var attrs = grid.get_col_attrs ( num );

	attrs [ "os3_width" ] = _os3g_resize_cell.width;

	_os3g_resize_cell = null;
}

function grid_resize_callback ( e )
{
	var cur_x, cur_y;

	if ( ( ! _os3g_is_ie ) && ( e.pageX ) )
		cur_x = e.pageX;
	else 
		cur_x = event.x;

	if ( _os3g_resize_start_x == -1 ) 
	{
		_os3g_resize_start_x = 1;
		_os3g_resize_start_x = cur_x - _os3g_resize_cell.offsetWidth;
	}

	if ( cur_x <= _os3g_resize_start_x ) cur_x = _os3g_resize_start_x +1;

	_os3g_resize_cell.width = ( cur_x - _os3g_resize_start_x );
}

// ===================================================================
// Grid Edit Functions
// ===================================================================
function grid_edit_abort_or_blur ( input, cell_id, evt )
{
        evt = (evt) ? evt : event;

	var ccode = ( evt.charCode ) ? evt.charCode : ( ( evt.which ) ? evt.which : evt.keyCode );
        var ch = String.fromCharCode ( ccode );

	// User confirmed input by pressing "enter key"
	if ( ccode == 13 ) return input.blur ();

	// User aborted input
	if ( ccode == 27 )
	{
		var v = cell_id.split ( ":" );
		var grid = _os3_grid_global_data.grid_arr [ v [ 0 ] ];
		input.value = grid.rows [ v [ 2 ] ] [ "data" ] [ v [ 1 ] ];
		input.blur ();
	}

	return true;
}

function grid_edit_end ( input, cell_id )
{
	var v = cell_id.split ( ":" );
	var grid = _os3_grid_global_data.grid_arr [ v [ 0 ] ];
	var oldv = grid.rows [ v [ 2 ] ] [ "data" ] [ v [ 1 ] ];
	var attrs = grid.get_col_attrs ( v [ 1 ] );

	if ( oldv != input.value )
	{
		if ( attrs [ "os3_validator" ] )
			if ( attrs [ "os3_validator" ] ( input.value ) == false )
			{
				alert ( "Invalid input: " + input.value );
				return input.focus ();
			}

		grid.rows [ v [ 2 ] ] [ "data" ] [ v [ 1 ] ] = input.value;
		if ( grid.onchange ) grid.onchange ( grid, v [ 1 ], v [ 2 ], input.value );
		if ( grid.sort_on_edit ) grid.sort ();
	}

	return grid.render ();
}

function grid_cell_txt_edit ( cell )
{
	var v = cell.id.split ( ":" );
	var grid = _os3_grid_global_data.grid_arr [ v [ 0 ] ];
	var val;
	var s, el, size;
	var attrs = grid.get_col_attrs ( v [ 2 ] );
	var type = attrs [ "os3_type" ];

	if ( ! type ) type = 'str';

	val = String ( grid.rows [ v [ 2 ] ] [ "data" ] [ v [ 1 ] ] );

	s  = '<input type="text" id="grid_edit_cell" value="' + val + '" ';
	s += ' onblur="grid_edit_end ( this, \'' + cell.id + '\' )" ';
	s += ' onfocus="this.select()" ';


	if ( attrs [ "os3_chars" ] )
		s += 'onkeypress="return grid_edit_filter_chars ( event, \'' + attrs [ "os3_chars" ] + '\' )" ';

	s += ' onkeydown="grid_edit_abort_or_blur(this, \'' + cell.id + '\', event)" ';
	s += ' size="'+ val.length + '" ';
	s += ' class="g_edit_box" ';
	if ( type == 'int' ) s += ' style="text-align: right;" ';
	s += '/>';

	cell.innerHTML = s;

	el = document.getElementById ( "grid_edit_cell" );
	el.focus ();
}

function grid_edit_filter_chars ( evt, valids ) {
        evt = (evt) ? evt : event;

        if ( evt.charCode < 32 ) return true;
                                                                                                                                                   
        var ccode = ( evt.charcode ) ? evt.charcode : ( ( evt.which ) ? evt.which : evt.keycode );
        var ch = String.fromCharCode ( ccode ).toLowerCase ();

	valids = valids.toLowerCase ();

        if ( valids.indexOf ( ch ) == -1 ) return false;

        return true;
}


function grid_header_mdown ( header )
{
	if ( header.className == 'g_header_down' ) return;

	header.old_className = header.className;
	header.className = 'g_header_down';
	
}

function grid_header_mup ( header )
{
	if ( header.old_className ) header.className = header.old_className;
}

function grid_header_click ( header )
{
	var name, num, res, grid;
	
	res = header.id.split ( "_gh" );
	name = res [ 0 ];
	num  = res [ 1 ];

	grid = _os3_grid_global_data.grid_arr [ name ];

	grid.set_sort_field ( num );

	grid.sort ()
}

function grid_row_over ( row )
{
	var old_col = row.style.backgroundColor;
	var hover_col = _os3_grid_global_data.grid_arr [ row.firstChild.id.split ( ":" ) [ 0 ] ].cols [ "hover" ];

	if ( _os3g_resize_cell ) grid_resize_cell_up ();

	if ( ( row.selected ) || ( old_col == hover_col ) ) return;

	row.old_color = old_col;
	row.style.backgroundColor = hover_col;
}

function grid_row_out ( row )
{
	if ( ! row.selected ) row.style.backgroundColor = row.old_color;
}

function grid_cell_click ( cell )
{
	var v = cell.id.split ( ":" );
	var grid = _os3_grid_global_data.grid_arr [ v [ 0 ] ];
	var val;

	val = grid.rows [ v [ 2 ] ] [ "data" ] [ v [ 1 ] ];

	sel = grid._cell_click ( grid, cell, v [ 2 ], v [ 1 ], val );
	
	if ( sel )
	{
		cell.old_border = cell.style.borderColor;
		cell.style.borderColor = grid.cols [ "rowsel" + ( v [ 2 ] % 2 ) ];
	} else
		cell.style.borderColor = cell.old_border;

	cell.selected = sel;
}

// ===================================================================
// Row selection function
// ===================================================================
function grid_row_click ( cell, grid_id, row_num )
{
	var grid = _os3_grid_global_data.grid_arr [ grid_id ];
	var row = cell.parentNode;

	if ( row.selected )
	{
		row.selected = false;
		grid_row_out ( row );
	} else {
		row.selected = true;
		row.style.backgroundColor = grid.cols [ "rowsel" + ( row_num % 2 ) ];
	}

	grid.rows_selected [ row_num ] = row.selected;

	if ( grid.onrowselect ) grid.onrowselect ( grid, row_num, row.selected );
}

// ===================================================================
// Internal Functions
// ===================================================================
function os3_grid_int_sort ( a, b )
{
	var res = 0;
	var v1, v2;

	v1 = parseInt ( a [ "data" ][ _os3_grid_global_data.sort_field ] );
	v2 = parseInt ( b [ "data" ][ _os3_grid_global_data.sort_field ] );

	if  ( v1 < v2 ) res = -1;
	else if ( v1 > v2 ) res = 1;

	if ( _os3_grid_global_data.sort_inverted ) res *= -1;

	return res;
}

function os3_grid_str_sort ( a, b )
{
	var res = 0;
	var v1, v2;

	v1 = a [ "data" ][ _os3_grid_global_data.sort_field ];
	v2 = b [ "data" ][ _os3_grid_global_data.sort_field ];

	if ( v1 < v2 ) res = -1;
	else if ( v1 > v2 ) res = 1;

	if ( _os3_grid_global_data.sort_inverted ) res *= -1;

	return res;
}

function _os3g_set_headers ()
{
	this.headers = arguments;
}

function _os3g_set_sort_field ( num )
{
	if ( num == this.sort_field ) 
		this.sort_inverted = ! this.sort_inverted;
	else
	{
		this.sort_field = num;
		this.sort_inverted = false;
	}
}

function _os3g_set_cell_click ( fname )
{
	this._cell_click = fname;

	if ( this.id && this.autorender ) this.render ();
}

function _os3g_set_size ( w, h )
{
	this._width = w;
	this._height = h;
	if ( this.id && this.autorender ) this.render ();
}

function _os3g_set_scrollbars ( sbars )
{
	this._scrollbars = sbars;
	if ( this.id && this.autorender ) this.render ();
}

function _os3g_set_border ( bsize, style, color )
{
	this._border = bsize;
	if ( style ) this._border_style = style;
	if ( color ) this._border_color = color;

	if ( this.id && this.autorender ) this.render ();
}

function _os3g_set_sortable ( sortable )
{
	this._sortable = sortable ;

	if ( this.id && this.autorender ) this.render ();
}

function _os3g_set_highlight ( hl )
{
	this._row_hl = hl ;

	if ( this.id && this.autorender ) this.render ();
}

function _os3g_sort ()
{
	if ( this.sort_field == -1 ) return;

	var attrs = this.get_col_attrs ( this.sort_field );
	var ctype = attrs [ "os3_type" ];
	var sfunc;

	if ( ! ctype ) ctype = "str";
	sfunc = { "str" : os3_grid_str_sort,
	  	  "int" : os3_grid_int_sort,
	  	  "date": os3_grid_str_sort } [ ctype ];

	_os3_grid_global_data.sort_field = this.sort_field;
	_os3_grid_global_data.sort_inverted = this.sort_inverted;
	this.rows.sort ( sfunc );
	this.render ( this.id );
}

function _os3g_add_row ()
{
	var arr;

	arr = { "data" : arguments, "style" : this.current_style };

	this.rows.push ( arr );
}

function _os3g_get_str ()
{
	var t, len;
	var s = '<table class="g_table">';
	var id, td_id;
	var attrs;

	// Row selections are discarted on rendering
	this.rows_selected = new Array ();

	if ( this.headers )
	{
		s += '<tr>';
		if ( this._show_row_num ) s+= '<td><div class="g_header">&nbsp</div></td>';

		len = this.headers.length;

		for ( t = 0; t < len; t ++ )
		{
			attrs = this.get_col_attrs ( t );
		
			td_id = this.id + "_th" + t;
			id = this.id + "_gh" + t;

			s += '<td id="' + td_id + '" ';
			if ( attrs [ "os3_width" ] ) s += 'style="width: ' + attrs [ "os3_width" ] + 'px;" ';
			s +='><div id="' + id + '" class="g_header"';
			if ( this._click_cb [ t ] )
			{
				if ( this._click_cb [ t ] != -1 ) 
				{
					s += ' onclick="' + this._click_cb [ t ] +  '"';
					s += ' onmousedown="grid_header_mdown(this)"';
					s += ' onmouseup="grid_header_mup(this)"';
					// s += ' onmouseout="grid_header_mup(this)" ';
				}
			} else if ( this._sortable ) {
					s += ' onclick="grid_header_click(this)"';
					s += ' onmousedown="grid_header_mdown(this)"';
					s += ' onmouseup="grid_header_mup(this)"';
					//s += ' onmouseout="grid_header_mup(this)" ';
			}

			s += '>'+ this.headers [ t ] + "</div></td>"; 

			if ( this.resize_cols )
				s += '<td class="g_resize" onmousedown="grid_resize_cell_down(\'' + td_id + '\')" onmouseup="grid_resize_cell_up()"></td>';
		}
		s += '</tr>';
	}

	var r, i, rlen, bgc, align, hl, style, rowcol, fullrow, v;

	len = this.rows.length;
	rlen = this.rows[0]['data'].length;	// All rows must be equal size
	for ( t = 0; t < len; t ++ )
	{
		fullrow = this.rows [ t ];
		r 	= fullrow [ 'data' ];
		style	= fullrow [ 'style' ];
		rowcol  = fullrow [ 'color' ];

		if ( rowcol )
			bgc = ' bgcolor="' + rowcol + '"';
		else 
			bgc = ' bgcolor="' + this.cols [ style + ( t % 2 ) ] + '"';

		if ( this._row_hl )
			hl = ' onmouseover="grid_row_over(this)" onmouseout="grid_row_out(this)" ';
		else
			hl = '';
			
		s += '<tr ' + hl + bgc + '>';
		if ( this._show_row_num ) 
		{
			s+= '<td class="g_header"';
			if ( this._row_sel )
			{
				s += ' onmousedown="grid_header_mdown(this)"';
				s += ' onmouseup="grid_header_mup(this)"';
				s += ' onclick="grid_row_click(this,\'' + this.id + '\',' + t + ')"';
			}

			s += ' id="' + this.id + ':' + t + '"';

			s+= '>' + ( this.start_counter + t ) + '</td>';
		}

		for ( i = 0; i < rlen; i ++ )
		{
			attrs = this.get_col_attrs ( i );

			var ca = attrs [ "os3_align" ];
			var ctype = attrs [ "os3_type" ];
			var cell_id = this.id + ":" + i + ":" + t;

			if ( ca )
				align = 'align="' + ca + '"';
			else if ( ctype && ( ctype != 'str' ) )
				align = 'align="right"';
			else
				align = "";
			
			s += '<td class="g_cell" valign="top" ' + align;
			if ( this.resize_cols ) s += ' colspan="2"';
			if ( attrs [ "os3_edit" ] ) s += ' ondblclick="grid_cell_' + attrs [ "os3_edit" ] + '_edit(this)" ';
			if ( this._cell_click ) s += ' onclick="grid_cell_click(this)" ';
			s += ' id="' + cell_id + '"';
			s += '>'; 
			if ( attrs [ "os3_render" ] )
				v = attrs [ "os3_render" ] ( r [ i ], cell_id );
			else
				v = r [ i ];
			s += v;
			s += '</td>';
		}
		s += '</tr>';
	}

	s += "</table>";
	
	return s;
}

function _os3g_render ( objId )
{
	if ( objId == undefined ) objId = this.id;

	this.id = objId;
	var obj = document.getElementById ( objId );

	obj.innerHTML = this.get_str ();

	if ( this._scrollbars )
		obj.style.overflow = "auto";
	else
		obj.style.overflow = "visible";	// was "none"


	if ( this._width )  obj.style.width = this._width;
	if ( this._height ) obj.style.height = this._height;
	if ( this._border ) 
	{
		if ( this._border_style ) obj.style.border = this._border_style;
		if ( this._border_color ) obj.style.borderColor = this._border_color;
		obj.style.borderWidth = this._border + "px";
	}
	
	// Bind element to the os3_grid_array
	_os3_grid_global_data.grid_arr [ objId ] = this;

	if ( this.onrender ) this.onrender ( this );
}

function _os3g_set_row_attr ( row_num, name, val )
{
	if ( ( row_num == undefined ) || ( row_num == -1 )  ) row_num = this.rows.length -1;

	var attrs = this.get_row_attrs ( row_num );

	attrs [ name ] = val;
}

function _os3g_set_row_color ( col, row_num )
{
	if ( ( row_num == undefined ) || ( row_num == -1 )  ) row_num = this.rows.length -1;

	this.rows [ row_num ] [ 'color' ] = col;
}

function _os3g_set_row_style ( style, row_num )
{
	if ( ( row_num == undefined ) || ( row_num == -1 )  ) row_num = this.rows.length -1;
	
	this.rows [ row_num ] [ 'style' ] = style;
}

function _os3g_set_col_align ( col, align )
{
	var attrs = this.get_col_attrs ( col );
	attrs [ "os3_align" ] = align;
}

function _os3g_set_col_editable ( col, edit )
{
	var attrs = this.get_col_attrs ( col );
	attrs [ "os3_edit" ] = edit;
}


function _os3g_get_value ( x, y )
{
	return this.rows [ y ] [ x ];
}

function _os3g_set_col_valid_chars ( col, chars )
{
	var attrs = this.get_col_attrs ( col );
	attrs [ "os3_chars" ] = chars;
}

function _os3g_set_col_validation ( col, func )
{
	var attrs = this.get_col_attrs ( col );
	attrs [ "os3_validator" ] = func;
}

function _os3g_set_row_select ( rsel )
{
	this._row_sel = rsel;
	if ( this._row_sel ) this._show_row_num = true;

	if ( this.id && this.autorender ) this.render ();
}

function _os3g_show_row_num ( show )
{
	this._show_row_num = true;
	if ( this.id && this.autorender ) this.render ();
}

function _os3g_set_col_type ( col, type )
{
	var attrs = this.get_col_attrs ( col );
	attrs [ "os3_type" ] = type;
}

function _os3g_set_col_render ( col, render )
{
	var attrs = this.get_col_attrs ( col );
	attrs [ "os3_render" ] = render;
}


function _os3g_get_row ( row )
{
	return this.rows [ row ] [ "data" ];
}

function _os3g_set_click_cb ( col, callback )
{
	this._click_cb [ col ] = callback;

	if ( this.id && this.autorender ) this.render ();
}

function _os3g_set_style ( style )
{
	this.current_style = style;

	if ( this.id && this.autorender ) this.render ();
}

function _os3g_get_col_attrs ( col )
{
	var attrs = this._column_attrs [ col ];
	if ( attrs ) return ( attrs );

	return this._column_attrs [ col ] = new Array ();
}

function _os3g_get_row_attrs ( row_num )
{
	if ( ( row_num == undefined ) || ( row_num == -1 )  ) row_num = this.rows.length -1;

	var attrs = this.rows [ row_num ] [ 'os3_attrs' ];

	if ( attrs ) return attrs;

	return this.rows [ row_num ] [ 'os3_attrs' ] = new Array ();
}

function _os3g_length ()
{
	return ( this.rows.length );
}


function OS3Grid ( auto_render )
{
	// ===========================================
	// Public attribs
	// ===========================================

	this.id = 0;

	this.start_counter = 0;

	// ===========================================
	// PUBLIC FLAGS
	// ===========================================

	// Flag T/F. If True, any modification (done with set_* funcs) will immediately renderd on grid
	this.autorender = auto_render;	

	// Flag T/F. If True, grid will be re-sorted on value changes
	this.sort_on_edit = false;

	// ===========================================
	// PUBLIC CALLBACKS
	// ===========================================
	// Function to be called when data in grid changes
	this.onchange = false;

	// Function to be called after the grid redraws
	this.onrender = false;

	// Callback to be called when the user selects / deselects a row
	this.onrowselect = false;

	// Flag T/F. If True, user can resize column at runtime
	this.resize_cols = false;
	
	// ===========================================
	// PUBLIC ATTRIBUTES
	// ===========================================

	// Array rows_selected
	this.rows_selected = false;	// This array keeps track of selected rows


	// Colors
	this.cols = { "hover" 	: "#8ec4cf",
		     "rowsel0"	: "#ffa07f",
		     "rowsel1"	: "#df8c6f",
		     "normal0"	: "#ffffff",
		     "normal1"	: "#dfdfdf",
		     "error0"	: "#ff0033",
		     "error1"	: "#cc0033",
		     "warn0"	: "#ffff99",
		     "warn1"	: "#ffff66",
		     "note0"	: "#9aff9a",
		     "note1"	: "#4eee94"
		    };

	// Default style
	this.current_style = "normal";


	// =============================================================================================
	// Private Stuff - Do not directly modify these values!
	// =============================================================================================
	this.headers = 0;
	this.rows = new Array ();
	this.sort_field = -1;
	this.sort_inverted = false;
		
	this._row_style = new Array ();

	this._column_attrs = new Array ();

	// This array stores the custom click callbacks
	this._click_cb = new Array ();
	
	// Flag T/F. If True, the grid is sortable (by clicking on the headers)
	this._sortable = false;

	// Flag T/F. If True, scrollbars are used.
	this._scrollbars = false;

	// Force grid container width
	this._width = 0;

	// Force grid container height
	this._height = 0;

	// Grid container border size (in pixels)
	this._border = 0;

	// Grid container border style (solid, dashed, dotted...)
	this._border_style = 0;

	// Grid container block color
	this._border_color = 0;

	// Function callback for every cell click
	this._cell_click = 0;

	// Function callback for every row click
	this._row_click = 0;

	// Flag T/F. If True rows will be highlighted when the mouse scrolls over them.
	this._row_hl = false;

	// Flag T/F. If True rows number are shown and rows are selectable by clicking on them.
	this._show_row_num = false;

	// Flag T/F. If True rows number are shown and rows are selectable by clicking on them.
	this._row_sel = false;

	// Public methods
	this.add_row 		= _os3g_add_row;
	this.get_col_attrs	= _os3g_get_col_attrs;		
	this.get_row 		= _os3g_get_row;
	this.get_row_attrs	= _os3g_get_row_attrs;	
	this.get_str 		= _os3g_get_str;
	this.getv		= _os3g_get_value;
	this.length		= _os3g_length;			
	this.render 		= _os3g_render;
	this.set_border 	= _os3g_set_border;
	this.set_cell_click 	= _os3g_set_cell_click;
	this.set_click_cb	= _os3g_set_click_cb;
	this.set_col_align	= _os3g_set_col_align;
	this.set_col_editable	= _os3g_set_col_editable;
	this.set_col_render	= _os3g_set_col_render;
	this.set_col_type	= _os3g_set_col_type;
	this.set_col_valid_chars = _os3g_set_col_valid_chars;
	this.set_col_validation = _os3g_set_col_validation;
	this.set_headers    	= _os3g_set_headers;
	this.set_highlight	= _os3g_set_highlight;
	this.set_row_attr	= _os3g_set_row_attr;	
	this.set_row_color	= _os3g_set_row_color;
	this.set_row_select	= _os3g_set_row_select;
	this.set_row_style	= _os3g_set_row_style;
	this.set_scrollbars 	= _os3g_set_scrollbars;
	this.set_size 		= _os3g_set_size;
	this.set_sort_field 	= _os3g_set_sort_field;
	this.set_sortable	= _os3g_set_sortable;
	this.set_style		= _os3g_set_style;
	this.show_row_num	= _os3g_show_row_num;
	this.sort 		= _os3g_sort;
}
