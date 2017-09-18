// xAnimation.line r2, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

xAnimation.prototype.line = function(e,x,y,t,a,b,oe)
{
  var i = this;
  i.x1 = xLeft(e); i.y1 = xTop(e); // start position
  i.x2 = Math.round(x); i.y2 = Math.round(y); // target position
  i.init(e,t,h,h,oe,a,b);
  i.run();
  function h(i) { // onRun and onTarget
    i.e.style.left = Math.round(i.x) + 'px';
    i.e.style.top = Math.round(i.y) + 'px';
  }
};
