/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2012 Umberto Bresciani

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


var camila_shiftModeStatus = false; // if true the shift key is down
var camila_ctrlModeStatus = false; // if true the ctrl key is down
var camila_altModeStatus = false; // if true the alt key is down 

var camila_selectedIds = "";

function camila_waiting_box() {
    window['camila_waitingbox'].show();
    return true;
}

function camila_gva_set_response(data)
{

    var outputhtml = "<b>"+data['camila_record_count']+"</b>";
    document.getElementById(data['reqId']).innerHTML = outputhtml;


}

function camila_init(strings,splash,printmode)
{
    eval("var stringsobj = " + strings);

    if (stringsobj != null)
        window['camila_messages'] = stringsobj;


    // set textareas id for y_TextCounter.js
    for (i=0; i<xGetElementsByTagName("textarea").length; i++)
        xGetElementsByTagName("textarea")[i].id=xGetElementsByTagName("textarea")[i].name;

    // set forms name for waiting box
    for (i=0; i<xGetElementsByTagName("form").length; i++) {
	var action = xGetElementsByTagName("form")[i].action;

        //if ((action.length>=12 && action.substr(-12) == 'cf_login.php') || (action.length>=9 && action.substr(-9) == 'index.php'))
	    //xGetElementsByTagName("form")[i].className = 'camilaloginform';

	    var name = "camila_form" + i;
	    xGetElementsByTagName("form")[i].name = name;
          xGetElementsByTagName("form")[i].id=name;

        if (parseInt(window['camila_messages']['CAMILA_ERROR']) == 0 && parseInt(window['camila_messages']['CAMILA_EXPORTING']) == 0)
            xAddEventListener(name, 'submit',camila_waiting_box,false);
    }


    for (i=0; i<xGetElementsByTagName("table").length; i++) {
        xGetElementsByTagName("table")[i].id = "table" + i;
        //xtc = new xTableCursor2('table'+i, 'camilaTableCursorRow', 'camilaTableCursorCell', 'camilaTableCursorRowClk', 'camilaTableCursorCellClk');

        xtc = new xTableCursorRS('table'+i, 'camilaTableCursorRow', 'camilaTableCursorCell', 'camilaTableCursorCellClk');
    }


    //init calendar table
    if (xGetElementById('camila_calendar') != null) {
        var camila_calendarTDs = xGetElementsByTagName("td", xGetElementById('camila_calendar'));
        for (i=0; i<camila_calendarTDs.length; i++) {
            if (i<7)
                xAddClass(camila_calendarTDs[i], 'camilaCalendarHeaderCell');
            else
                xAddClass(camila_calendarTDs[i], 'camilaCalendarCell');
        }
    }

    camila_initCheckBehavior();
    camila_initCollapsible();

    xWalkUL(xFirstChild(xGetElementById('nav')),null, camila_initLinkSet);

    xAddEventListener(window, 'load',
        function () {
            camila_winOnResize(); // set initial position
            xAddEventListener(window, 'resize', camila_winOnResize, false);
            xAddEventListener(window, 'scroll', camila_winOnScroll, false);
            document.onkeydown = camila_docOnKeydown;
            document.onkeyup = camila_docOnKeyup;
        }, false
    );

    window['camila_popupwindow'] = new xWindow(
        'winMax',               // target name
        screen.width - 200,           // width
        screen.height - xOffsetTop('skin') - 100,      // height - m is a 'fudge-factor' ;-)
        100, xOffsetTop('skin'),                   // position: left, top
        0,                      // location field
        0,                      // menubar
        1,                      // resizable
        1,                      // scrollbars
        0,                      // statusbar
        0);                     // toolbar

    var lnks = document.links;
    if (lnks) {
        for (var i = 0; i < lnks.length; ++i) {
            if(lnks[i].onclick == null)
                lnks[i].onclick = camila_linkOnClick;
        }
    }

    if (parseInt(window['camila_messages']['CAMILA_ERROR']) == 0 && parseInt(window['camila_messages']['CAMILA_EXPORTING']) == 0)
        window['camila_waitingbox'] = new xPageGrey('camilawaitingbox', window['camila_messages']['pleasewait'], 'camilawaitingmsg');

    camila_inline_editbox_init();

    //new xCamilaTableMenu('camila_table_menu_image', 'camilatablemenu', 10, 'mouseover');

    if (splash) {
        xGetElementById('camilasplash').style.display = 'block';
        xAniOpac('camilasplash', 0, 3000, function(){xGetElementById('camilasplash').style.display = 'none';});
    }
	
	    var scrollContainer = $(".table-responsive");
        //bind events
//        $(".cf-table-arrow-left").click({direction: "prev"}, scrollContainer.scrollLeft());
//        $(".cf-table-arrow-right").click({direction: "next"}, scrollContainer.scrollRigth());
		
		$(".cf-table-arrow-left").click(function () { 
  var leftPos = $('.table-responsive').scrollLeft();
  $(".table-responsive").animate({scrollLeft: leftPos - 200}, 800);
});

$(".cf-table-arrow-right").click(function () { 
  var leftPos = $('.table-responsive').scrollLeft();
  $(".table-responsive").animate({scrollLeft: leftPos + 200}, 800);
});

}

function camila_autosuggest_pickvalues(url, id, srcfields, destfields, destprefix) {

    var xmlhttp;
    try {
        // Mozilla / Safari / IE7
        xmlhttp = new XMLHttpRequest();
    } catch (e) {
        // IE
        var XMLHTTP_IDS = new Array('MSXML2.XMLHTTP.5.0',
                                     'MSXML2.XMLHTTP.4.0',
                                     'MSXML2.XMLHTTP.3.0',
                                     'MSXML2.XMLHTTP',
                                     'Microsoft.XMLHTTP' );
        var success = false;
        for (var i=0;i < XMLHTTP_IDS.length && !success; i++) {
            try {
                 xmlhttp = new ActiveXObject(XMLHTTP_IDS[i]);
                 success = true;
            } catch (e) {}
        }
        if (!success) {
            throw new Error('Unable to create XMLHttpRequest.');
        }
     }

    xmlhttp.open('GET', url + 'objectid=' + id, true);

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                var jsondata = eval('(' + xmlhttp.responseText + ')');
                var sf = srcfields.split(",");
                var df = destfields.split(",");

                var jsobj = jsondata.results[0];

                for (i=0; i<df.length; i++) {

                    val = jsobj[sf[i]];
                    el = xGetElementById(destprefix + df[i]);

                    if ( (val != null) && el != null && !(el.type == 'select-one' && val == '') && (val != ''))
                        el.value = val;
                }

            }
            else
                alert('Error');
        }
    }

    xmlhttp.send(null);

}

function camila_linkOnClick() {
  var action = true;
  var url = this.href;

  if (camila_shiftMode()) {
      if (url.indexOf('?') == -1)
          window['camila_popupwindow'].load(url + '?camila_popup');
      else
          window['camila_popupwindow'].load(url + '&camila_popup');
      camila_shiftModeStatus = false;
      action = false;
  }
  
  return action;
}

function camila_docOnKeydown(ev)
{
  var e = new xEvent(ev);
  switch (e.keyCode) {
    case 16:
      camila_shiftModeStatus = true;
      break;
    case 17:
      camila_ctrlModeStatus = true;
      break;
    case 18:
      camila_altModeStatus = true;
      break;
  }

//  if (e.shiftKey) log2('onkeydown, shiftKey');
//  if (e.ctrlKey) log2('onkeydown, ctrlKey');
//  if (e.altKey) log2('onkeydown, altKey');
}

function camila_docOnKeyup(ev)
{
  var e = new xEvent(ev);
  switch (e.keyCode) {
    case 16:
      camila_shiftModeStatus = false;
      break;
    case 17:
      camila_ctrlModeStatus = false;
      break;
    case 18:
      camila_altModeStatus = false;
      break;
  }

  // these should never be true in keyup event:
//  if (e.shiftKey) log2('onkeyup, shiftKey');
//  if (e.ctrlKey) log2('onkeyup, ctrlKey');
//  if (e.altKey) log2('onkeyup, altKey');
}

function camila_shiftMode() {
    return camila_shiftModeStatus;
}

function camila_winOnResize() {
  camila_winOnScroll(); // initial slide
}

function camila_winOnScroll() {
  var floatAtBottom = true;
  var slideTime = 700;
  var y = xScrollTop();

  y= y - xOffsetTop('skin');

  if (floatAtBottom) {
    y += xClientHeight() - xHeight('camilabottomtoolbar');
  }
  xSlideTo('camilabottomtoolbar', 0, y, slideTime);
}


function camila_initCollapsible()
{
  var i, icon, headings = xGetElementsByClassName('camilacollapsibleon');
  for (i = 0; i < headings.length; i++) {
    icon = document.createElement('div');
    icon.collapsibleSection = xFirstChild(headings[i],'div');
    icon.initCollapsible = true;
    icon.onclick = camila_iconOnClick;
    icon.onclick();
    if (xGetCookie(xFirstChild(headings[i],'div').id)=='0')
        icon.onclick();
    icon.initCollapsible = false;
    icon.onmouseover = camila_iconOnMouseover;
    icon.onmouseout = camila_iconOnMouseout;
    
    headings[i].appendChild(icon);
  }
  
  headings = xGetElementsByClassName('camilacollapsibleoff');
  for (i = 0; i < headings.length; i++) {
    icon = document.createElement('div');
    icon.collapsibleSection = xFirstChild(headings[i],'div');
    icon.initCollapsible = true;
    icon.onclick = camila_iconOnClick;
    icon.onclick();
    if (!xGetCookie(xFirstChild(headings[i],'div').id)=='1')
        icon.onclick();
    icon.initCollapsible = false;
    icon.onmouseover = camila_iconOnMouseover;
    icon.onmouseout = camila_iconOnMouseout;
    //icon.originalBackGround=
    //alert(this.style.backgroundColor);
    headings[i].appendChild(icon);
  }
}

function camila_iconOnClick()
{
  var section = this.collapsibleSection;
  if (section.style.display != 'block') {
    section.style.display = 'block';
    this.className = 'camilaCollapseIcon';
    this.title = window['camila_messages']['collapse'];
    var date = new Date();
	date.setTime(date.getTime()+(365*24*60*60*1000));
    if (!this.initCollapsible)
        xSetCookie(section.id, '1', date);
  }
  else {
    section.style.display = 'none';
    this.className = 'camilaExpandIcon';
    this.title = window['camila_messages']['expand'];
    var date = new Date();
	date.setTime(date.getTime()+(365*24*60*60*1000));
    if (!this.initCollapsible)
        xSetCookie(section.id, '0', date);
  }
}

function camila_iconOnMouseover()
{
  this.collapsibleSection.originalBackgroundColor = this.collapsibleSection.style.backgroundColor;
  this.collapsibleSection.style.backgroundColor = '#cccccc';
}

function camila_iconOnMouseout()
{
  this.collapsibleSection.style.backgroundColor = this.collapsibleSection.originalBackgroundColor;
}

function camila_initLinkSet(p,li,c)
{
  var a=xFirstChild(li,'a');
  if (document.location.href.indexOf(a.href)!=-1)
  {
    li.id='linksetliselected';
    a.id='linksetaselected';
    xAddClass('linksetliselected','active');
    xAddClass('linksetaselected','active');

    //li.style.backgroundColor='#ffffff';
    //a.style.backgroundColor='#ffffff';
    //alert(a.href);
  }
}

function camila_initCheckBehavior()
{
  var i, a;

  for (i = 0; i < document.links.length; ++i) {
    a = document.links[i];
    if (a.id.indexOf('UncheckAll_') != -1) {
      a.onclick = camila_doCheckBehavior;
      a._CBNAME_ = a.id.substr(11);
      a._CBCHECKED_ = false;
    }
    else if (a.id.indexOf('CheckAll_') != -1) {
      a.onclick = camila_doCheckBehavior;
      a._CBNAME_ = a.id.substr(9);
      a._CBCHECKED_ = true;
    }
  }
}

function camila_doCheckBehavior()
{
  var i, cb = document.getElementsByTagName('input');
  var ec = 0;

  for (i = 0; i < cb.length; ++i) {
    if ((cb[i].id.indexOf(this._CBNAME_) != -1) && (cb[i].id.indexOf('camila_f') != -1))
      ec++;
  }

  for (i = 0; i < cb.length; ++i) {
    if (cb[i].id.indexOf(this._CBNAME_) != -1)
    {

        if ((cb[i].id != "camila_f[1]") && (cb[i].id != ("camila_f["+(ec).toString()+"]")) && (cb[i].id != ("camila_f["+(ec-1).toString()+"]")) && (cb[i].id != ("camila_f["+(ec-2).toString()+"]")) && (cb[i].id != ("camila_f["+(ec-3).toString()+"]")) && (cb[i].id != ("camila_f["+(ec-4).toString()+"]")) && (cb[i].id != ("camila_f["+(ec-5).toString()+"]")) && (cb[i].id != ("camila_f["+(ec-6).toString()+"]")) && (cb[i].id != ("camila_f["+(ec-7).toString()+"]")) && (cb[i].id != ("camila_f["+(ec-8).toString()+"]")))
        {
            cb[i].checked = this._CBCHECKED_;

        }
    }
  }
  return false;
}

/*
The following functions are part of X,
a Cross-Browser.com Javascript Library,
Distributed under the terms of the GNU LGPL.
Copyright 2001-2007 Michael Foster
*/

/* xCamilaTableMenu Object Prototype

  Parameters:
    triggerId   - id string of trigger element.
    menuId      - id string of menu.
    mouseMargin - integer margin around menu;
                  when mouse is outside this margin the menu is hid.
    openEvent   - string name of event on which to open menu ('click', 'mouseover', etc).
*/

function xCamilaTableMenu(triggerId, menuId, mouseMargin, openEvent)
{
  var isOpen = false;
  var trg = xGetElementById(triggerId);
  var mnu = xGetElementById(menuId);
  if (trg && mnu) {
    xAddEventListener(trg, openEvent, onOpen, false);
  }
  function onOpen()
  {
    if (!isOpen) {
      xMoveTo(mnu, xPageX(trg), xPageY(trg) + xHeight(trg));
      mnu.style.visibility = 'visible';
      xAddEventListener(document, 'mousemove', onMousemove, false);
      isOpen = true;
    }
  }
  function onMousemove(ev)
  {
    var e = new xEvent(ev);
    if (!xHasPoint(mnu, e.pageX, e.pageY, -mouseMargin) &&
        !xHasPoint(trg, e.pageX, e.pageY, -mouseMargin))
    {
      mnu.style.visibility = 'hidden';
      xRemoveEventListener(document, 'mousemove', onMousemove, false);
      isOpen = false;
    }
  }
} // end xCamilaTableMenu

function xAniOpac(sEleId, finalOpac, uTotalTime, fnOnEnd)
{
  var ele = xGetElementById(sEleId);
  var start = xOpacity(ele);
  var disp = finalOpac - start; // total displacement
  var freq = (1 / uTotalTime); // frequency
  var startTime = new Date().getTime();
  var tmr = setInterval(
    function() {
      var elapsedTime = new Date().getTime() - startTime;
      if (elapsedTime < uTotalTime) {
        var f = elapsedTime * freq;
        xOpacity(ele, f * disp + start);
      }
      else {
        xOpacity(ele, finalOpac);
        clearInterval(tmr);
        if (fnOnEnd) fnOnEnd();
      }
    }, 10
  );
}

// xSmartLoadScript r1, Copyright 2005-2007 Brendan Richards
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function camila_xSmartLoadScript(url)
{
  var loadedBefore = false;
  if (typeof(xLoadedList) != "undefined") {
  for (i=0; i<xLoadedList.length; i++) {
    if (xLoadedList[i] == url) {
      loadedBefore = true;
      break;
    }
  }
  }
  if (document.createElement && document.getElementsByTagName && !loadedBefore) {
    var s = document.createElement('script');
    var h = document.getElementsByTagName('head');
    if (s && h.length) {
      s.src = url;
      //h[0].appendChild(s);
      document.body.appendChild(s);
      if (typeof(xLoadedList) == "undefined") xLoadedList = new Array();
      xLoadedList.push(url);
    }
  }
}


// xTableCursor2 r1, gebura's enhancement of xTableCursor
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL
function xTableCursor2(tblId, rowStyle, cellStyle, rowClicStyle, cellClicStyle) // object prototype
{
  xTableIterate(tblId,
    function(obj, isRow) {
      if (!isRow) {
        obj.onmouseover = tdOver;
        obj.onmouseout = tdOut;
        obj.onclick = tdClic;
      }
    }
  );
  function tdOver(e) {
    xAddClass(this, cellStyle);
    var tr = this.parentNode;
    for (var i = 0; i < tr.cells.length; ++i) {
      if (this != tr.cells[i]) xAddClass(tr.cells[i], rowStyle);
    }
  }
  function tdOut(e) {
    xRemoveClass(this, cellStyle);
    var tr = this.parentNode;
    for (var i = 0; i < tr.cells.length; ++i) {
      xRemoveClass(tr.cells[i], rowStyle);
    }
  }

  function tdClic(e) {
    var table = this.parentNode.parentNode.rows;
    for (var i= 0;i < table.length; i++) {
      var tr = table[i];
      for (var j = 0; j < tr.cells.length; j++) {
        xRemoveClass(tr.cells[j], rowClicStyle);
        xRemoveClass(tr.cells[j], cellClicStyle);
      }
    }
    xAddClass(this, cellClicStyle);
    var tr = this.parentNode;
    for (var i = 0; i < tr.cells.length; ++i) {
      if (this != tr.cells[i]) xAddClass(tr.cells[i], rowClicStyle);
    }
  }
  this.unload = function() {
    xTableIterate(tblId, function(o) { o.onmouseover = o.onmouseout = null; });
  };
}

function xTableCursorRS(tblId, rowStyle, cellStyle, lockStyle) // object prototype
{
  var _s = {e:0,r:0,c:0};
  var _e = {e:0,r:0,c:0};
  xTableIterate(tblId,
    function(obj, isRow) {
      if (!isRow) {
        obj.onmouseover = tdOver;
        obj.onmouseout = tdOut;
        obj.onclick = tdClick;
      }
    }
  );
/*
  xGetElementById(tblId).onmousemove = function(e) {
    e = new xEvent(e);
    log(e.type + ', ' + e.target.nodeName);
  };
*/
  function tdOver() {
    xAddClass(this, cellStyle);
    var tr = this.parentNode;
    for (var i = 0; i < tr.cells.length; ++i) {
      xAddClass(tr.cells[i], rowStyle);
    }
  }
  function tdOut() {
    xRemoveClass(this, cellStyle);
    var tr = this.parentNode;
    for (var i = 0; i < tr.cells.length; ++i) {
      xRemoveClass(tr.cells[i], rowStyle);
    }
  }
  function tdClick() {
    var r, c, t = xGetElementById(tblId);
    if (!_s.e) { // range start
      camila_selectedIds = '';

      xAddClass(this, lockStyle);
      _s.e = this;
    }
    else if (!_e.e) { // range end
      camila_selectedIds = '';
      _s.r = Math.min(_s.e.parentNode.rowIndex, this.parentNode.rowIndex);
      _s.c = Math.min(_s.e.cellIndex, this.cellIndex);
      _e.r = Math.max(_s.e.parentNode.rowIndex, this.parentNode.rowIndex);
      _e.c = Math.max(_s.e.cellIndex, this.cellIndex);
      for (r = _s.r; r <= _e.r; ++r) {
        for (c = _s.c; c <= _e.c; ++c) {

          camila_selectedIds += xFirstChild(t.rows[r].cells[0]).id + ",";

          xAddClass(t.rows[r].cells[c], lockStyle);
        }
      }

      _e.e = this;
    }
    else { // range reset
      camila_selectedIds = '';

      for (r = _s.r; r <= _e.r; ++r) {
        for (c = _s.c; c <= _e.c; ++c) {
          xRemoveClass(t.rows[r].cells[c], lockStyle);
        }
      }
      _s.e = _e.e = null;
    }
  }
  this.unload = function() {
    xTableIterate(tblId, function(o) { o.onmouseover = o.onmouseout = o.onclick = null; });
  };
}


function xPageGrey(sDivClass,/* sImgUrl, sImgClass,*/ sMsg, sMsgClass)
{
  /*@cc_on
  @if (@_jscript_version < 5.5) // opacity not supported in IE until v5.5
  this.ele = null;
  @else @*/
  this.ele = document.createElement('div');
  this.ele.className = sDivClass;
  //if (sImgUrl) {
    //var img = document.createElement('img');
    //img.src = sImgUrl;
    //img.className = sImgClass;
    this.msg = document.createElement('p');
    this.msg.className = sMsgClass;
    //this.msg.appendChild(img);
    this.msg.appendChild(document.createTextNode(sMsg));
    document.body.appendChild(this.msg);
  //}
  document.body.appendChild(this.ele);
  /*@end @*/
  this.show = function()
  {
    if (this.ele) {
      var ds = xDocSize();
      xMoveTo(this.ele, 0, 0);
      //xResizeTo(this.ele, ds.w, ds.h);
      if (this.msg) {
        xMoveTo(this.msg, xScrollLeft()+(xClientWidth()-xWidth(this.msg))/2, xScrollTop()+(xClientHeight()-xHeight(this.msg))/2);
      }
    }
  };
  this.hide = function()
  {
    if (this.ele) {
      xResizeTo(this.ele, 10, 10);
      xMoveTo(this.ele, -10, -10);
      if (this.msg) {
        xMoveTo(this.msg, -xWidth(this.msg), 0);
      }
    }
  };
}

/*
 * (c)2006 Dean Edwards/Matthias Miller/John Resig
 * Special thanks to Dan Webb's domready.js Prototype extension
 * and Simon Willison's addLoadEvent
 *
 * For more info, see:
 * http://dean.edwards.name/weblog/2006/06/again/
 * http://www.vivabit.com/bollocks/2006/06/21/a-dom-ready-extension-for-prototype
 * http://simon.incutio.com/archive/2004/05/26/addLoadEvent
 * 
 * Thrown together by Jesse Skinner (http://www.thefutureoftheweb.com/)
 *
 *
 * To use: call camila_addDOMLoadEvent one or more times with functions, ie:
 *
 *    function something() {
 *       // do something
 *    }
 *    camila_addDOMLoadEvent(something);
 *
 *    camila_addDOMLoadEvent(function() {
 *        // do other stuff
 *    });
 *
 */
 
function camila_addDOMLoadEvent(func) {
   if (!window.__load_events) {
      var init = function () {
          // quit if this function has already been called
          if (arguments.callee.done) return;
      
          // flag this function so we don't do the same thing twice
          arguments.callee.done = true;
      
          // kill the timer
          if (window.__load_timer) {
              clearInterval(window.__load_timer);
              window.__load_timer = null;
          }
          
          // execute each function in the stack in the order they were added
          for (var i=0;i < window.__load_events.length;i++) {
              window.__load_events[i]();
          }
          window.__load_events = null;
      };
   
      // for Mozilla/Opera9
      if (document.addEventListener) {
          document.addEventListener("DOMContentLoaded", init, false);
      }
      
      // for Internet Explorer
      // http://javascript.html.it/articoli/leggi/2225/il-problema-di-windowonload/5/
      /*@cc_on @*/
      /*@if (@_win32)
          document.write("<script id=__ie_onload defer src=javascript:void(0)><\/script>");
          var script = document.getElementById("__ie_onload");
          script.onreadystatechange = function() {
              if (this.readyState == "complete") {
                  init(); // call the onload handler
              }
          };
      /*@end @*/
      
      // for Safari
      if (/WebKit/i.test(navigator.userAgent)) { // sniff
          window.__load_timer = setInterval(function() {
              if (/loaded|complete/.test(document.readyState)) {
                  init(); // call the onload handler
              }
          }, 10);
      }
      
      // for other browsers
      window.onload = init;
      
      // create event function stack
      window.__load_events = [];
   }
   
   // add function to event stack
   window.__load_events.push(func);
}
