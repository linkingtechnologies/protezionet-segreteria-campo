// xAnimation.size r2, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

xAnimation.prototype.size = function(e,w,h,t,a,b,oe)
{
  var i = this;
  i.x1 = xWidth(e); i.y1 = xHeight(e); // start size
  i.x2 = Math.round(w); i.y2 = Math.round(h); // target size
  i.init(e,t,o,o,oe,a,b);
  i.run();
  function o(i) { xWidth(i.e, Math.round(i.x)); xHeight(i.e, Math.round(i.y)); } // onRun and onTarget
};
