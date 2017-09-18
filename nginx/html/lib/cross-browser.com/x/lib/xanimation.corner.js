// xAnimation.corner r2, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

xAnimation.prototype.corner = function(e,c,x,y,t,a,b,oe) // needs more testing!
{
  var i = this;
  i.x2 = x; i.y2 = y; // end point
  // start point
  var ex = xLeft(e), ey = xTop(e);
  var ew = xWidth(e), eh = xHeight(e);
  i.cornerStr = c.toLowerCase();
  switch (i.cornerStr) {
    case 'nw': i.x1=ex; i.y1=ey; break;
    case 'sw': i.x1=ex; i.y1=ey+eh; break;
    case 'ne': i.x1=ex+ew; i.y1=ey; break;
    case 'se': i.x1=ex+ew; i.y1=ey+eh; break;
    default: /*alert('invalid cornerStr');*/ return;
  }
  i.init(e,t,h,h,oe,a,b);
  i.run();
  function h(i) { // onRun and onTarget
    var e = i.e, x = Math.round(i.x), y = Math.round(i.y);
    var nwx = xLeft(e), nwy = xTop(e); // nw point
    var sex = nwx + xWidth(e), sey = nwy + xHeight(e); // se point
    switch (i.cornerStr) {
      case 'nw': e.style.left=x+'px'; e.style.top=y+'px'; xResizeTo(e, sex-x, sey-y); break;
      case 'sw': e.style.left=x+'px'; xWidth(e,sex-x); xHeight(e,y-nwy); break;
      case 'ne': xWidth(e,x-nwx); e.style.top=y+'px'; xHeight(e,sey-y); break;
      case 'se': xWidth(e,x-nwx); xHeight(e,y-nwy); break;
    }
  }
};
