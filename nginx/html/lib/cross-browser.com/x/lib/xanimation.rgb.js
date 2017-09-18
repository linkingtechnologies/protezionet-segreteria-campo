// xAnimation.rgb r2, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

xAnimation.prototype.rgb = function(e,p,v,t,a,b,oe)
{
  var i = this;
  var co = xParseColor(xGetComputedStyle(e,p));
  i.x1 = co.r; i.y1 = co.g; i.z1 = co.b; // start colors
  co = xParseColor(v);
  i.x2 = co.r; i.y2 = co.g; i.z2 = co.b; // target colors
  i.prop = xCamelize(p);
  i.init(e,t,h,h,oe,a,b);
  i.run();
  function h(i) { // onRun and onTarget
    i.e.style[i.prop] = xRgbToHex(Math.round(i.x),Math.round(i.y),Math.round(i.z));
  }
};
