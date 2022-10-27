/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2022 Umberto Bresciani

   Camila PHP Framework is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Camila PHP Framework is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Camila PHP Framework; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */


// 'Block' Tiny Encryption Algorithm
//
// Algorithm: David Wheeler & Roger Needham, Cambridge University Computer Lab
//            http://www.cl.cam.ac.uk/ftp/papers/djw-rmn/djw-rmn-tea.html (1994)
//            http://www.cl.cam.ac.uk/ftp/users/djw3/xtea.ps (1997)
//            http://www.cl.cam.ac.uk/ftp/users/djw3/xxtea.ps (1998)
//
// JavaScript implementation: Chris Veness, Movable Type Ltd: www.movable-type.co.uk
//
//
// encrypt: Use 128 bits (16 chars) of string 'key' to encrypt string 'val'
//          (note key & val must be strings not string objects)
//
// Return encrypted text as string
//
function encrypt(val, key)
{
    // 'escape' val so chars outside ISO-8859-1 work in single-byte packing, but keep spaces as
    // spaces (not '%20') so encrypted text doesn't grow too long!
    var v = strToLongs(escape(val).replace(/%20/g,' '));
    var k = strToLongs(key.slice(0,16));
    var n = v.length;

    if (n == 0) return("");  // nothing to encrypt
    if (n == 1) v[n++] = 0;  // algorithm doesn't work for n<2 so fudge by adding nulls

    // TEA routine as per Wheeler & Needham, Oct 1998

    var z = v[n-1], y = v[0], delta = 0x9E3779B9;
    var mx, e, q = Math.floor(6 + 52/n), sum = 0;

    while (q-- > 0) {  // 6 + 52/n operations gives between 6 & 32 mixes on each word
        sum += delta;
        e = sum>>>2 & 3;
        for (var p = 0; p < n-1; p++) {
            y = v[p+1];
            mx = (z>>>5 ^ y<<2) + (y>>>3 ^ z<<4) ^ (sum^y) + (k[p&3 ^ e] ^ z)
            z = v[p] += mx;
        }
        y = v[0];
        mx = (z>>>5 ^ y<<2) + (y>>>3 ^ z<<4) ^ (sum^y) + (k[p&3 ^ e] ^ z)
        z = v[n-1] += mx;
    }

    // note: unsigned right-shift '>>>' is used in place of original '>>', due to lack of 
    // 'unsigned' type declaration in JavaScript (thanks to Karsten Kraus @ swr3 for this)

    return esc(longsToStr(v));
}

function strToLongs(s) {  // convert string to array of longs, each containing 4 chars
    // note chars must be within ISO-8859-1 (with Unicode code-point < 256) to fit 4/long
    var l = new Array(Math.ceil(s.length/4))
    for (var i=0; i<l.length; i++) {
        // note little-endian encoding - endianness is irrelevant as long as 
        // it is the same in longsToStr() 
        l[i] = s.charCodeAt(i*4) + (s.charCodeAt(i*4+1)<<8) + 
               (s.charCodeAt(i*4+2)<<16) + (s.charCodeAt(i*4+3)<<24);
    }
    return l;  // note running off the end of the string generates nulls since 
}              // bitwise operators treat NaN as 0

function longsToStr(l) {  // convert array of longs back to string
    var a = new Array(l.length);
    for (var i=0; i<l.length; i++) {
        a[i] = String.fromCharCode(l[i] & 0xFF, l[i]>>>8 & 0xFF, 
                                   l[i]>>>16 & 0xFF, l[i]>>>24 & 0xFF);
    }
    return a.join('');  // use Array.join() rather than repeated string appends for efficiency
}

function esc(str) {  // escape null and control chars which might cause problems in loading encrypted texts
    return str.replace(/[\0\n\v\f\r!]/g, function(c) { return '!' + c.charCodeAt(0) + '!'; });
}

function unesc(str) {  // unescape potentially problematic nulls and control characters
    return str.replace(/!\d\d?!/g, function(c) { return String.fromCharCode(c.slice(1,-1)); });
}

/**
 * Sets a Cookie with the given name and value.
 *
 * name       Name of the cookie
 * value      Value of the cookie
 * [expires]  Expiration date of the cookie (default: end of current session)
 * [path]     Path where the cookie is valid (default: path of calling document)
 * [domain]   Domain where the cookie is valid
 *              (default: domain of calling document)
 * [secure]   Boolean value indicating if the cookie transmission requires a
 *              secure transmission
 */
function setCookie(name, value, expires, path, domain, secure)
{
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

var keyStr = "ABCDEFGHIJKLMNOP" +
            "QRSTUVWXYZabcdef" +
            "ghijklmnopqrstuv" +
            "wxyz0123456789+/" +
            "=";

function encode64(input) {
  var output = "";
  var chr1, chr2, chr3 = "";
  var enc1, enc2, enc3, enc4 = "";
  var i = 0;

  do {
      chr1 = input.charCodeAt(i++);
      chr2 = input.charCodeAt(i++);
      chr3 = input.charCodeAt(i++);

      enc1 = chr1 >> 2;
      enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
      enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
      enc4 = chr3 & 63;

      if (isNaN(chr2)) {
        enc3 = enc4 = 64;
      } else if (isNaN(chr3)) {
        enc4 = 64;
      }

      output = output + 
        keyStr.charAt(enc1) + 
        keyStr.charAt(enc2) + 
        keyStr.charAt(enc3) + 
        keyStr.charAt(enc4);
      chr1 = chr2 = chr3 = "";
      enc1 = enc2 = enc3 = enc4 = "";
  } while (i < input.length);

  return output;
}

function store() {
  if (!confirm("Verra' ora memorizzato su questo computer il codice numerico\ndi accesso al sistema codificandolo con una password.\nSuccessivamente per accedere bastera' introdurre la password\nscelta al posto del codice numerico.\nProcedere con la memorizzazione?"))
    return;
  var pass1=prompt("Digitare il codice numerico:","");
  var pass2=prompt("Digitare la password:","");
  var todayDate = largeExpDate = new Date ();
  largeExpDate.setTime(todayDate.getTime() + 365 * 24 * 3600 * 1000);
  setCookie("camila_lbox",encode64(encrypt(pass1, pass2)), largeExpDate);
  location.reload();
}
