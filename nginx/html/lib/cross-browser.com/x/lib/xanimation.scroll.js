// xAnimation.scroll r2, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

xAnimation.prototype.scroll = function(e,x,y,t,a,b,oe)
{
  var i = this;
  i.init(e);
  i.win = i.e.nodeType==1 ? false:true;
  i.x1 = xScrollLeft(i.e, i.win); i.y1 = xScrollTop(i.e, i.win); // start position
  i.x2 = Math.round(x); i.y2 = Math.round(y); // target position
  i.init(e,t,h,h,oe,a,b);
  i.run();
  function h(i) { // onRun and onTarget
    var x = Math.round(i.x), y = Math.round(i.y);
    if (i.win) i.e.scrollTo(x, y);
    else { i.e.scrollLeft = x; i.e.scrollTop = y; }
  }
};
