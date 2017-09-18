// xHttpRequest2 r2, Copyright 2006-2007 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

/* I suggest you use xHttpRequest instead of this implementation.
   This one has not been updated since I did more testing and
   updating on the other. This one is interesting for its use of
   the XML island for IE - but that is all.
*/

function xHttpRequest2() // object prototype
{
  // Public Properties
  this.xmlDoc = null;
  this.busy = false;
  this.err = {};
  // Private Properties
  var _i = this; // instance object
  var _r = null; // XMLHttpRequest object
  var _t = null; // timer
  var _f = null; // callback function
  /*@cc_on var _x = null; @*/ // xml element for IE
  // Private Event Listeners
  function _oc() // onReadyStateChange
  {
    if (_r.readyState == 4) {
      if (_t) { clearTimeout(_t); }
      _i.busy = false;
      if (_f) {
        if (_i.xmlDoc == 1 && _r.status == 200) {
          /*@cc_on
          @if (@_jscript_version < 5.9) // IE (this is a guess - need a better check here)
          if (!_x) {
            _x = document.createElement('xml');
            document.body.appendChild(_x);
          }
          _x.XMLDocument.loadXML(_r.responseText);
          _i.xmlDoc = _x.XMLDocument;
          @else @*/
          _i.xmlDoc = _r.responseXML;
          /*@end @*/
        }
        _f(_i, _r);
      } // end if (_f)
    }
  }
  function _ot() // onTimeout
  {
    _i.err.name = 'Timeout';
    _r.abort();
    _i.busy = false;
    if (_f) _f(_i, null);
  }
  // Public Method
  this.send = function(m, u, d, t, r, x, f)
  {
    if (!_r) { return false; }
    if (_i.busy) {
      _i.err.name = 'Busy';
      return false;
    }
    m = m.toUpperCase();
    if (m != 'POST') {
      if (d) {
        d = '?' + d;
        if (r) { d += '&' + r + '=' + Math.round(10000*Math.random()); }
      }
      else { d = ''; }
    }
    _f = f;
    _i.xmlDoc = null;
    _i.err.name = _i.err.message = '';
    _i.busy = true;
    if (t) { _t = setTimeout(_ot, t); }
    try {
      if (m == 'GET') {
        _r.open(m, u + d, true);
        d = null;
        _r.setRequestHeader('Cache-Control', 'no-cache'); // this doesn't prevent caching in IE
        if (x) {
          if (_r.overrideMimeType) { _r.overrideMimeType('text/xml'); }
          _r.setRequestHeader('Content-Type', 'text/xml');
          _i.xmlDoc = 1; // indicate to _oc that xml is expected
        }
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
      _i.err.name = e.name;
      _i.err.message = e.message;
      return false;
    }
    return true;
  };
  // Constructor Code
  try { _r = new XMLHttpRequest(); }
  catch (e) { try { _r = new ActiveXObject('Msxml2.XMLHTTP'); }
  catch (e) { try { _r = new ActiveXObject('Microsoft.XMLHTTP'); }
  catch (e) { _r = null; }}}
  if (!_r) { _i.err.name = 'Unsupported'; }
}
