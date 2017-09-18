// xAnimation.arc r2, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

xAnimation.prototype.arc = function(e,xr,yr,a1,a2,t,a,b,oe)
{
  var i = this;
  i.x1 = a1 * (Math.PI / 180); i.x2 = a2 * (Math.PI / 180); // start and end angles
  var x0 = xLeft(e) + (xWidth(e) / 2); var y0 = xTop(e) + (xHeight(e) / 2); // start point
  i.xc = x0 - (xr * Math.cos(i.x1)); i.yc = y0 - (yr * Math.sin(i.x1)); // arc center point
  i.xr = xr; i.yr = yr; // ellipse radii
  i.init(e,t,h,h,oe,a,b);
  i.run();
  function h(i) { // onRun and onTarget
    i.e.style.left = (Math.round(i.xr * Math.cos(i.x) + i.xc - (xWidth(i.e) / 2))) + 'px';
    i.e.style.top = (Math.round(i.yr * Math.sin(i.x) + i.yc - (xHeight(i.e) / 2))) + 'px';
  }
};
