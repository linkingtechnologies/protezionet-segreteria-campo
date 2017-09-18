// xBackground r4, Copyright 2001-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xBackground(e,c,i)
{
  if(!(e=xGetElementById(e))) return '';
  var bg='';
  if(e.style) {
    if(xStr(c)) {e.style.backgroundColor=c;}
    if(xStr(i)) {e.style.backgroundImage=(i!='')? 'url('+i+')' : null;}
    bg=e.style.backgroundColor;
  }
  return bg;
}
