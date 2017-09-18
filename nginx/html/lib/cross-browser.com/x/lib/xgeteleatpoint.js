// xGetEleAtPoint r2, Copyright 2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xGetEleAtPoint(x, y)
{
  var he = null, z, hz = 0;
  var i, list = xGetElementsByTagName('*');
  for (i = 0; i < list.length; ++i) {
    if (xHasPoint(list[i], x, y)) {
      z = parseInt(list[i].style.zIndex);
      z = z || 0;
      if (z >= hz) {
        hz = z;
        he = list[i];
      } 
    }
  }
  return he;
}
