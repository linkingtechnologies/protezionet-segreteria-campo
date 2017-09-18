// xTableHeaderFixed r1, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xTableHeaderFixed(fixedContainerId, fixedTableClass, fakeBodyId, tableBorder, thBorder)
{
  // Private Property
  var tables = [];
  // Private Event Listener
  function onEvent(e) // handles scroll and resize events
  {
    e = e || window.event;
    var r = e.type == 'resize' ? true : false;
    for (var i = 0; i < tables.length; ++i) {
      scroll(tables[i], r);
    }
  }
  // Private Methods
  function scroll(t, bResize)
  {
    if (!t) { return; }
    var fhc = xGetElementById(fixedContainerId); // for IE6
    var fh = xGetElementById(t.fixedHeaderId);
    var thead = t.tHead;
    var st, sl, thy = xPageY(thead);
    /*@cc_on
    @if (@_jscript_version == 5.6) // IE6
    st = xGetElementById(fakeBodyId).scrollTop;
    sl = xGetElementById(fakeBodyId).scrollLeft;
    @else @*/
    st = xScrollTop();
    sl = xScrollLeft();
    /*@end @*/
    var th = xHeight(t);
    var tw = xWidth(t);
    var ty = xPageY(t);
    var tx = xPageX(t);
    var fhh = xHeight(fh);
    if (bResize) {
      xWidth(fh, tw + 2*tableBorder);
      var th1 = xGetElementsByTagName('th', t);
      var th2 = xGetElementsByTagName('th', fh);
      for (var i = 0; i < th1.length; ++i) {
        xWidth(th2[i], xWidth(th1[i]) + thBorder);
      }
    }
    xLeft(fh, tx - sl);
    if (st <= thy || st > ty + th - fhh) {
      if (fh.style.visibility != 'hidden') {
        fh.style.visibility = 'hidden';
        fhc.style.visibility = 'hidden'; // for IE6
      }
    }
    else {
      if (fh.style.visibility != 'visible') {
        fh.style.visibility = 'visible';
        fhc.style.visibility = 'visible'; // for IE6
      }
    }
  }
  function init()
  {
    var i, tbl, h, t, con;
    if (null == (con = xGetElementById(fixedContainerId))) {
      con = document.createElement('div');
      con.id = fixedContainerId;
      document.body.appendChild(con);
    }
    for (i = 0; i < tables.length; ++i) {
      tbl = tables[i];
      h = tbl.tHead;
      if (h) {
        t = document.createElement('table');
        t.className = fixedTableClass;
        t.appendChild(h.cloneNode(true));
        t.id = tbl.fixedHeaderId = 'xtfh' + i;
        con.appendChild(t);
      }
      else {
        tables[i] = null; // ignore tables with no thead
      }
    }
    con.style.visibility = 'hidden'; // for IE6
  }
  // Public Method
  this.unload = function()
  {
    for (var i = 0; i < tables.length; ++i) {
      tables[i] = null;
    }
  };
  // Constructor Code
  var i, j, lst;
  if (arguments.length > 5) { // we've been passed a list of IDs and/or Element objects
    i = 5;
    lst = arguments;
  }
  else { // make a list of all tables
    i = 0;
    lst = xGetElementsByTagName('table');
  }
  for (j = 0; i < lst.length; ++i, ++j) {
    tables[j] = xGetElementById(lst[i]);
  }
  init();
  onEvent({type:'resize'});
  /*@cc_on
  @if (@_jscript_version == 5.6) // IE6
  xAddEventListener(fakeBodyId, 'scroll', onEvent, false);
  @else @*/
  xAddEventListener(window, 'scroll', onEvent, false);
  /*@end @*/
  xAddEventListener(window, 'resize', onEvent, false);
} // end xTableHeaderFixed
