// xAddEventListener2, Copyright 2001-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xAddEventListener2(e,eT,eL,cap) // original implementation
{
  if(!(e=xGetElementById(e))) return;
  eT=eT.toLowerCase();
  if (e==window && !e.opera && !document.all) { // simulate resize and scroll events for all except Opera and IE
    if(eT=='resize') { e.xPCW=xClientWidth(); e.xPCH=xClientHeight(); e.xREL=eL; xResizeEvent(); return; }
    if(eT=='scroll') { e.xPSL=xScrollLeft(); e.xPST=xScrollTop(); e.xSEL=eL; xScrollEvent(); return; }
  }
  if(e.addEventListener) e.addEventListener(eT,eL,cap||false);
  else if(e.attachEvent) e.attachEvent('on'+eT,eL);
  else e['on'+eT]=eL;
}
// called only from the above
function xResizeEvent()
{
  if (window.xREL) setTimeout('xResizeEvent()', 250);
  var w=window, cw=xClientWidth(), ch=xClientHeight();
  if (w.xPCW != cw || w.xPCH != ch) { w.xPCW = cw; w.xPCH = ch; if (w.xREL) w.xREL(); }
}
function xScrollEvent()
{
  if (window.xSEL) setTimeout('xScrollEvent()', 250);
  var w=window, sl=xScrollLeft(), st=xScrollTop();
  if (w.xPSL != sl || w.xPST != st) { w.xPSL = sl; w.xPST = st; if (w.xSEL) w.xSEL(); }
}
