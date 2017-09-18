// xAnimation.opacity r2, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

xAnimation.prototype.opacity = function(e,o,t,a,b,oe)
{
  var i = this;
  i.x1 = xOpacity(e); i.x2 = o; // start and target opacity
  i.init(e,t,h,h,oe,a,b);
  i.run();
  function h(i) {xOpacity(i.e, i.x);} // onRun and onTarget
};
