// xParentNode 1, Copyright 2005-2007 Olivier Spinelli
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xParentNode( ele, n )
{
  while(ele&&n--){ele=ele.parentNode;}
  return ele;
}
