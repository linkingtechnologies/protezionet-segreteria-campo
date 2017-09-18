// xStyle r1, Copyright 2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xStyle(sProp, sVal)
{
  var i, e;
  for (i = 2; i < arguments.length; ++i) {
    e = xGetElementById(arguments[i]);
    if (e.style) {
      try { e.style[sProp] = sVal; }
      catch (err) { e.style[sProp] = ''; } // ???
    }
  }
}
