// xAnimation.css r2, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

xAnimation.prototype.css = function(e,p,v,t,a,b,oe)
{
  var i = this;
  i.x1 = xGetComputedStyle(e,p,true); // start value
  i.x2 = v; // target value
  i.prop = xCamelize(p);
  i.init(e,t,h,h,oe,a,b);
  i.run();
  function h(i) {i.e.style[i.prop] = Math.round(i.x) + 'px';}
};
