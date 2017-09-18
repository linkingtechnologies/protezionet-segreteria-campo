// xFenster r15, Copyright 2004-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xFenster(clientId, iniTitle, iniUrl, iniX, iniY, iniW, iniH, enMove, enResize, enMaxRes, enClose, fnMove, fnResize, fnMaxRes, fnClose, fnFocus)
{
  var me = this;

  // perhaps should be parameters, must correspond with css, M = conPadding, B = conBorder
  var M = 1, B = 1;
  var M2 = 2*M, B2 = 2*B;

  // Public Methods

  me.paint = function(dw, dh)
  {
    me.conW += dw;
    me.conH += dh;
    xResizeTo(me.con, me.conW, me.conH);
    /*@cc_on
    @if (@_jscript_version <= 5.7)
      xMoveTo(me.tbar, M, M);
      xWidth(me.tbar, me.conW - M2 - B2);
      xLeft(me.sbar, M);
      xWidth(me.sbar, me.conW - M2 - B2);
      xTop(me.sbar, me.conH - xHeight(me.sbar) - M - B2);
    @end @*/
    xMoveTo(me.client, M, M + me.tbar.offsetHeight);
    xResizeTo(me.client, me.conW - M2 - B2, me.conH - me.tbar.offsetHeight - me.sbar.offsetHeight - M2 - B2);
  };
  me.focus = function(e) // don't use 'this' here
  {
    if (!fnFocus || fnFocus(me)) {
      me.con.style.zIndex = xFenster.nextZ++;
      if (xFenster.focused) {
        xFenster.focused.tbar.className = 'xfTBar';
        xFenster.focused.sbar.className = 'xfSBar';
      }
      me.tbar.className = 'xfTBarF';
      me.sbar.className = 'xfSBarF';
      xFenster.focused = me;
    }
  };
  me.href = function(s)
  {
    var h = '';
    if (isIFrame) {
      if (me.client.contentWindow) {
        if (s) {me.client.contentWindow.location = s;}
        h = me.client.contentWindow.location.href;
      }
      // this needs more testing now that Safari/Win is available
      else if (typeof me.client.src == 'string') { // for Apollo when iframe exists in html
        if (s) {me.client.src = s;}
        h = me.client.src;
      }
    }
    return h;
  };
  me.hide = function(e) // don't use 'this' here
  {
    var i, o = xFenster.instances, z = 0, hz = 0, f = null;
    if (!fnClose || fnClose(me)) {
      me.con.style.display = 'none';
      xStopPropagation(e);
      if (me == xFenster.focused) {
        for (i in o) {
          if (o.hasOwnProperty(i) && o[i].con.style.display != 'none' && o[i] != me) {
            z = parseInt(o[i].con.style.zIndex);
            if (z > hz) {
              hz = z;
              f = o[i];
            }
          }
        }
        if (f) {f.focus();}
      }
    }
  };
  me.show = function()
  {
    me.con.style.display = 'block';
    me.focus();
  };
  me.status = function(s)
  {
    if (s) {me.sbar.firstChild.data = s;}
    return me.sbar.firstChild.data;
  };
  me.title = function(s)
  {
    if (s) {me.tbar.firstChild.data = s;}
    return me.tbar.firstChild.data;
  };

  // Private Event Listeners

  function dragStart()
  {
    var i, o = xFenster.instances;
    if (isIFrame) {
      for (i in o) {
        if (o.hasOwnProperty(i)) {
          o[i].client.style.visibility = 'hidden';
        }
      }
    }
    else { me.focus(); }
  }
  function dragEnd()
  {
    var i, o = xFenster.instances;
    if (isIFrame) {
      for (i in o) {
        if (o.hasOwnProperty(i)) {
          o[i].client.style.visibility = 'visible';
        }
      }
    }
  }
  function barDrag(e, mdx, mdy)
  {
    var x = xLeft(me.con) + mdx;
    var y = xTop(me.con) + mdy;
    if (!fnMove || fnMove(me, x, y)) {
      xMoveTo(me.con, x, y);
    }
  }
  function resDrag(e, mdx, mdy)
  {
    if (!fnResize || fnResize(me, me.client.offsetWidth + mdx, me.client.offsetHeight + mdy)) { 
      me.paint(mdx, mdy);
    }
  }
  function maxClick()
  {
    var w, h;
    if (me.maximized) {
      w = rW;
      h = rH;
    }
    else {
      w = xClientWidth() - 2;
      h = xClientHeight() - 2;
    }
    if (!fnMaxRes || fnMaxRes(me, w - M2 - B2, h - me.tbar.offsetHeight - me.sbar.offsetHeight - M2 - B2)) {
      if (me.maximized) { // restore
        xMoveTo(me.con, rX, rY);
      }
      else { // maximize
        rW = me.con.offsetWidth;
        rH = me.con.offsetHeight;
        rX = me.con.offsetLeft;
        rY = me.con.offsetTop;
        xMoveTo(me.con, xScrollLeft(), xScrollTop());
      }
      me.maximized = !me.maximized;
      me.conW = w;
      me.conH = h;
      me.paint(0, 0);
    }
  }

  // Constructor Code

  // public properties
  me.con = null;  // outermost container
  me.tbar = null; // title bar
  me.sbar = null; // status bar
  me.rbtn = null; // resize icon
  me.mbtn = null; // max/restore icon
  me.cbtn = null; // close icon
  me.maximized = false;
  me.client = xGetElementById(clientId);

  if (!me.client) {
    me.client = document.createElement(typeof iniUrl == 'string' ? 'iframe' : 'div');
    me.client.id = clientId;
  }
  me.client.className += ' xfClient';
  me.client.style.display = 'block';

  // private properties
  var rX, rY, rW, rH; // "restore" values
  var isIFrame = me.client.nodeName.toLowerCase() == 'iframe';

  xFenster.instances[clientId] = me;

  // create elements
  me.con = document.createElement('div');
  me.con.className = 'xfCon';
  if (enResize) {
    me.rbtn = document.createElement('div');
    me.rbtn.className = 'xfRIco';
    me.rbtn.title = 'Resize';
  }
  if (enMaxRes) {
    me.mbtn = document.createElement('div');
    me.mbtn.className = 'xfMIco';
    me.mbtn.title = 'Maximize/Restore';
  }
  if (enClose) {
    me.cbtn = document.createElement('div');
    me.cbtn.className = 'xfCIco';
    me.cbtn.title = 'Close';
  }
  me.tbar = document.createElement('div');
  me.tbar.className = 'xfTBar';
  if (enMove) {
    me.tbar.title = 'Drag to Move';
    if (enMaxRes) me.tbar.title += ', ';
  }
  if (enMaxRes) me.tbar.title += 'Double-Click to Maximize/Restore';
  me.tbar.appendChild(document.createTextNode(iniTitle));
  me.sbar = document.createElement('div');
  me.sbar.className = 'xfSBar';
  me.sbar.innerHTML = '&nbsp;'; // me.sbar.appendChild(document.createTextNode(' '));
  // append elements
  me.con.appendChild(me.tbar);
  if (enMaxRes) me.tbar.appendChild(me.mbtn);
  if (enClose) me.tbar.appendChild(me.cbtn);
  me.con.appendChild(me.client);
  me.con.appendChild(me.sbar);
  if (enResize) me.sbar.appendChild(me.rbtn);
  document.body.appendChild(me.con);
  // final initializations
  me.conW = iniW;
  me.conH = iniH;
  if (isIFrame) { me.href(iniUrl); }
  xMoveTo(me.con, iniX, iniY);
  me.paint(0, 0);
  if (enMove) xEnableDrag(me.tbar, dragStart, barDrag, dragEnd);
  if (enResize) xEnableDrag(me.rbtn, dragStart, resDrag, dragEnd);
  if (isIFrame) {
    me.con.onmousedown = me.focus;
    me.client.name = clientId;
  }
  else { me.con.onclick = me.focus; }// don't like this but can't use onmousedown here - it prevents dragging thumbnail on native scrollbar!
  if (enMaxRes) me.mbtn.onclick = me.tbar.ondblclick = maxClick;
  if (enClose) {
    me.cbtn.onclick = me.hide;
    me.cbtn.onmousedown = xStopPropagation;
  }
  me.con.style.visibility = 'visible';
  me.focus();
  xAddEventListener(window, 'unload',
    function () {
      me.con.onmousedown = me.con.onclick = null;
      if (me.mbtn) me.mbtn.onclick = me.tbar.ondblclick = null;
      if (me.cbtn) me.cbtn.onclick = me.cbtn.onmousedown = null;
      xFenster.instances[clientId] = null;
      me = null;
    }, false
  );
  xAddEventListener(window, 'resize',
    function () {
      if (me.maximized) {
        xResizeTo(me.con, 100, 100); // ensure fenster isn't causing scrollbars
        xMoveTo(me.con, xScrollLeft(), xScrollTop());
        me.conW = xClientWidth() - 2;
        me.conH = xClientHeight() - 2;
        me.paint(0, 0);
      }
    }, false
  ); 
} // end xFenster object prototype

// xFenster static properties
xFenster.nextZ = 100;
xFenster.focused = null;
xFenster.instances = {};
