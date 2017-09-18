// xEnableDrop r2, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xEnableDrop(id,fD)
{
  var e = xGetElementById(id);
  if (e) {
    e.xODp = fD;
    if (!_xDrgMgr.drops) {
      _xDrgMgr.drops = new Array();
    }
    _xDrgMgr.drops[_xDrgMgr.drops.length] = e;
    if (!_xDrgMgr.omu) {
      _xDrgMgr.omu = _xOMU;
      _xOMU = _xOMU2;
    }
  }
}
function _xOMU2(e) // this over-rides _xOMU in xenabledrag.js
{
  var i, z, hz = 0, he = null;
  e = new xEvent(e);
  for (i = 0; i < _xDrgMgr.drops.length; ++i) {
    if (xHasPoint(_xDrgMgr.drops[i], e.pageX, e.pageY)) {
      z = parseInt(_xDrgMgr.drops[i].style.zIndex) || 0;
      if (z >= hz) {
        hz = z;
        he = _xDrgMgr.drops[i];
      } 
    }
  }
  var ele = _xDrgMgr.ele;
  _xDrgMgr.omu(e); // dragEnd event
  if (he && he.xODp) {
    he.xODp(ele, e.pageX, e.pageY); // drop event
  }
}
