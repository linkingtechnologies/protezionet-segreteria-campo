// xEnableDrag r6, Copyright 2002-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

var _xDrgMgr = {ele:null, mm:false};
function xEnableDrag(id,fS,fD,fE,x1,y1,x2,y2)
{
  var el = xGetElementById(id);
  if (el) {
    el.xDraggable = true;
    el.xODS = fS;
    el.xOD = fD;
    el.xODE = fE;
    el.xREC = null;
    if (xDef(x1,y1,x2,y2)) {el.xREC = {x1:x1,y1:y1,x2:x2,y2:y2};}
    xAddEventListener(el, 'mousedown', _xOMD, false);
    if (!_xDrgMgr.mm) {
      _xDrgMgr.mm = true;
      xAddEventListener(document, 'mousemove', _xOMM, false);
    }
  }
}
function _xOMD(e) // drag start
{
  var ev = new xEvent(e);
//  if (ev.button != 0) return;
  var t = ev.target;
  while(t && !t.xDraggable) { t = t.offsetParent; }
  if (t) {
    xPreventDefault(e);
    t.xDPX = ev.pageX;
    t.xDPY = ev.pageY;
    _xDrgMgr.ele = t;
    xAddEventListener(document, 'mouseup', _xOMU, false);
    if (t.xODS) { t.xODS(t, ev.pageX, ev.pageY, ev); }
  }
}
function _xOMM(e) // drag
{
  var ev = new xEvent(e);
  if (_xDrgMgr.ele) {
    xPreventDefault(e);
    var b = true, el = _xDrgMgr.ele;
    var dx = ev.pageX - el.xDPX;
    var dy = ev.pageY - el.xDPY;
    el.xDPX = ev.pageX;
    el.xDPY = ev.pageY;
    if (el.xREC) { 
      var r = el.xREC, x = xPageX(el) + dx, y = xPageY(el) + dy;
      var b = (x >= r.x1 && x+xWidth(el) <= r.x2 && y >= r.y1 && y+xHeight(el) <= r.y2);
    }
    if (el.xOD) { el.xOD(el, dx, dy, b, ev); }
    else if (b) { xMoveTo(el, xLeft(el) + dx, xTop(el) + dy); }
  }
}
function _xOMU(e) // drag end
{
  if (_xDrgMgr.ele) {
    xPreventDefault(e);
    xRemoveEventListener(document, 'mouseup', _xOMU, false);
    if (_xDrgMgr.ele.xODE) {
      var ev = new xEvent(e);
      _xDrgMgr.ele.xODE(_xDrgMgr.ele, ev.pageX, ev.pageY, ev);
    }
    _xDrgMgr.ele = null;
  }
}
