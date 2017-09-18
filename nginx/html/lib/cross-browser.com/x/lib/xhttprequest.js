// xHttpRequest r4, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xHttpRequest() // object prototype
{
  // Private Properties
  var _i = this; // instance object
  var _r = null; // XMLHttpRequest object
  var _t = null; // timer
  var _f = null; // callback function
  var _x = false; // XML response pending
  var _o = null; // user data object passed to _f
  // Public Properties
  _i.OK = 0;
  _i.NOXMLOBJ = 1;
  _i.REQERR = 2;
  _i.TIMEOUT = 4;
  _i.RSPERR = 8;
  _i.NOXMLCT = 16;
  _i.status = _i.OK;
  _i.busy = false;
  // Private Event Listeners
  function _oc() // onReadyStateChange
  {
    if (_r.readyState == 4) {
      if (_t) { clearTimeout(_t); }
      if (_r.status != 200) _i.status = _i.RSPERR;
      if (_x) {
        var ct = _r.getResponseHeader('Content-Type');
        if (ct && ct.indexOf('xml') == -1) { _i.status |= _i.NOXMLCT; }
      }
      if (_f) _f(_r, _i.status, _o);
      _i.busy = false;
    }
  }
  function _ot() // onTimeout
  {
    _r.onreadystatechange = function(){};
    _r.abort();
    _i.status |= _i.TIMEOUT;
    if (_f) _f(_r, _i.status, _o);
    _i.busy = false;
  }
  // Public Method
  this.send = function(m, u, d, t, r, x, o, f)
  {
    if (!_r || _i.busy) { return false; }
    m = m.toUpperCase();
    if (m != 'POST') {
      if (d) {
        d = '?' + d;
        if (r) { d += '&' + r + '=' + Math.round(10000*Math.random()); }
      }
      else { d = ''; }
    }
    _x = x;
    _o = o;
    _f = f;
    _i.busy = true;
    _i.status = _i.OK;
    if (t) { _t = setTimeout(_ot, t); }
    try {
      if (m == 'GET') {
        _r.open(m, u + d, true);
        d = null;
        _r.setRequestHeader('Cache-Control', 'no-cache');
        var ct = 'text/' + (x ? 'xml':'plain');
        if (_r.overrideMimeType) {_r.overrideMimeType(ct);}
        _r.setRequestHeader('Content-Type', ct);
      }
      else if (m == 'POST') {
        _r.open(m, u, true);
        _r.setRequestHeader('Method', 'POST ' + u + ' HTTP/1.1');
        _r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      }
      else {
        _r.open(m, u + d, true);
        d = null;
      }
      _r.onreadystatechange = _oc;
      _r.send(d);
    }
    catch(e) {
      if (_t) { clearTimeout(_t); }
      _f = null;
      _i.busy = false;
      _i.status = _i.REQERR;
      _i.error = e;
      return false;
    }
    return true;
  };
  // Constructor Code
  try { _r = new XMLHttpRequest(); }
  catch (e) { try { _r = new ActiveXObject('Msxml2.XMLHTTP'); }
  catch (e) { try { _r = new ActiveXObject('Microsoft.XMLHTTP'); }
  catch (e) { _r = null; }}}
  if (!_r) { _i.status = _i.NOXMLOBJ; }
}
