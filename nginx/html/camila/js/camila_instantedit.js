//inspired by a script by http://www.yvoschaap.com

var camila_inline_object;
var urlBase = "update.php";
var formVars = "";
var changing = false;
//var enterK = false;

//XMLHttpRequest class function
function datosServidor() {
};

datosServidor.prototype.iniciar = function() {
    try {
        // Mozilla / Safari
        this._xh = new XMLHttpRequest();
    } catch (e) {
        // Explorer
        var _ieModelos = new Array(
            'MSXML2.XMLHTTP.5.0',
            'MSXML2.XMLHTTP.4.0',
            'MSXML2.XMLHTTP.3.0',
            'MSXML2.XMLHTTP',
            'Microsoft.XMLHTTP'
        );

        var success = false;
        for (var i=0;i < _ieModelos.length && !success; i++) {
            try {
                this._xh = new ActiveXObject(_ieModelos[i]);
                success = true;
            } catch (e) {
            }
        }

        if ( !success ) {
            return false;
        }

        return true;
    }
}

datosServidor.prototype.ocupado = function() {
    estadoActual = this._xh.readyState;
    return (estadoActual && (estadoActual < 4));
}

datosServidor.prototype.procesa = function() {
    if (this._xh.readyState == 4 && this._xh.status == 200) {
        this.procesado = true;
    }
}

datosServidor.prototype.enviar = function(urlget,datos) {
    if (!this._xh) {
        this.iniciar();
    }

    if (!this.ocupado()) {
        this._xh.open("GET",urlget,false);
        this._xh.send(datos);
        if (this._xh.readyState == 4 && this._xh.status == 200) {
            return this._xh.responseText;
        }
    }
    return false;
}




function fieldEnter(campo, evt, idfld) {
	evt = (evt) ? evt : window.event;

	if (evt.keyCode == 13 && campo.value != "" && campo.type!="textarea") {
//		elem = document.getElementById( idfld );
//		remotos = new datosServidor;
//		nt = remotos.enviar(urlBase + "?fieldname=" +encodeURI(elem.id)+ "&content="+encodeURI(campo.value)+"&"+formVars,"");
		//remove glow
//		noLight(elem);
//		elem.innerHTML = nt;
//		changing = false;


		fieldBlur(campo, idfld);
		
//		alert ('ss');

		//noLight(elem);
//		enterK = true;

		return false;
	} else {
		return true;
	}
}


function fieldBlur(campo, idfld) {
    if (enterK)
        return false;

    enterK = true;


    //if (campo.value!="") {
        elem = document.getElementById(idfld);

        var url = camila_inline_script + "camila_inline&" + camila_inline_object["name"] + "=" + escape(campo.value) + "&time=" + new Date().getTime();

        for (var key in camila_inline_object) {

            if (key!='name' && key!='value');
                url = url + "&" + key + "=" + escape(camila_inline_object[key]);
        }

//        alert(url);
        remotos = new datosServidor;
        //nt = remotos.enviar(urlBase + "?fieldname=" +escape(elem.id)+ "&content="+escape(urlencode(campo.value))+"&"+formVars,"");
		//alert(url);
        nt = remotos.enviar(url,"");
//alert(nt);
        eval("var result = " + nt);
        
        if (result['result'] == "OK") {
            if (result['type'] == 'select') {
                //if (result['value'] != '-')
                    elem.innerHTML = symbolsToEntities(result['options'][result['value']]);
                //else
                //    elem.innerHTML = symbolsToEntities(campo.value);        
            }
            else
                elem.innerHTML = symbolsToEntities(result['value']);
        } else {
            alert(result['error_desc']);
            elem.innerHTML = camila_inline_object['value'];
        }
	
        changing = false;
        return false;
    //}



//    if (campo.value == camila_inline_object['value']) {
//        changing = false;
        elem = document.getElementById(idfld);

        if (camila_inline_object['type'] == 'select') {
            elem.innerHTML = symbolsToEntities(camila_inline_object['options'][campo.value]);
        } else
            elem.innerHTML = symbolsToEntities(campo.value);

//        return false;
//    }


}

function camila_changeBool(idfld, value, imgsrc) {

        elem = document.getElementById(idfld);

        var url = camila_inline_script + "camila_inline&" + camila_inline_object["name"] + "=" + escape(value) + "&time=" + new Date().getTime();

        for (var key in camila_inline_object) {

            if (key!='name' && key!='value');
                url = url + "&" + key + "=" + escape(camila_inline_object[key]);
        }

        //alert(url);
        remotos = new datosServidor;
        //nt = remotos.enviar(urlBase + "?fieldname=" +escape(elem.id)+ "&content="+escape(urlencode(campo.value))+"&"+formVars,"");
        nt = remotos.enviar(url,"");
        eval("var result = " + nt);
        //alert(nt);
        if (result['result'] == "OK") {
            if (result['type'] == 'select') {
                //if (result['value'] != '-')
                    var html = "<img src=\""+imgsrc+"\" alt=\"\" style=\"vertical-align:middle; border-style:none\" />";
                    elem.innerHTML = html;
                //else
                //    elem.innerHTML = symbolsToEntities(campo.value);        
            }
            else
                elem.innerHTML = symbolsToEntities(result['value']);
        } else {
            alert(result['error_desc']);
            elem.innerHTML = camila_inline_object['value'];
        }

        changing = false;
        return false;
}


function camila_editBox(actualParent) {
    if (changing)
        return;

    var changingBool = false;

    actual = xFirstChild(actualParent, 'span');

    enterK = false;
    var field = actual.id.substr(0, actual.id.indexOf("__cf__"));
    //alert(field);
    var url = camila_inline_script + camila_inline[actual.id.substr(actual.id.indexOf("__cf__"))] + "&camila_inline&camila_inline_field=" + field;

    //alert (url);
    remotos = new datosServidor;
    nt = remotos.enviar(url);

    //alert(nt);
    eval("camila_inline_object = " + nt);

    if (camila_inline_object == null)
        alert('null object...');

    if (camila_inline_object['result'] != 'OK')
        return;

    //alert(actual.nodeName+' '+changing);
    if(!changing){
        var html = '';
        if (camila_inline_object['type']=='text')
            html = "<input id=\""+ actual.id +"_field\" size=\""+ camila_inline_object['size'] +"\" maxlength=\""+ camila_inline_object['maxlength'] +"\" type=\"text\" value=\"" + camila_inline_object['value'].replace(/[\"]/g,"&quot;") + "\" onkeypress=\"return fieldEnter(this,event,'" + actual.id + "')\" onfocus=\"highLight(this);\" onblur=\"noLight(this); return fieldBlur(this,'" + actual.id + "');\" />";

        if (camila_inline_object['type']=='textarea')
            html = "<textarea id=\""+ actual.id +"_field\" rows=\"10\" cols=\"80\" onkeypress=\"return fieldEnter(this,event,'" + actual.id + "')\" onfocus=\"highLight(this);\" onblur=\"noLight(this); return fieldBlur(this,'" + actual.id + "');\">"+ camila_inline_object['value'].replace(/[\"]/g,"&quot;") +"</textarea>";
	//actual.innerHTML = "<tex style=\"width: "+width+"px; height: "+height+"px;\"  return fieldBlur(this,'" + actual.id + "');\">" + actual.innerHTML + "</textarea>";
    
	if (camila_inline_object['type']=='select') {
            if (field.substr(0,8) == 'cf_bool_') {
                changingBool = true;
                var imgsrc = "";
                var val = "";

                for (var key in camila_inline_object['options']) {
                    if (key == camila_inline_object['value'])
                        html += "";
                    else {
                        imgsrc = "../../camila/images/png/"+field+"_" + key + ".png";
                        val = key;
                        html += "<img src=\""+imgsrc+"\" alt=\"\" style=\"vertical-align:middle; border-style:none\" />";
                    }
                }
                html += '';
                camila_changeBool(actual.id, val, imgsrc);
                changing = false;
            } else {
                html = "<select id=\""+ actual.id +"_field\" onkeypress=\"return fieldEnter(this,event,'" + actual.id + "')\" onfocus=\"highLight(this);\" onblur=\"noLight(this); return fieldBlur(this,'" + actual.id + "');\" />";

                for (var key in camila_inline_object['options']) {
                    if (key == camila_inline_object['value'])
                        html += "<option value=\"" + key.replace(/[\"]/g,"&quot;") + "\" selected=\"selected\">" + camila_inline_object['options'][key] + "</option>";
                    else
                        html += "<option value=\"" + key.replace(/[\"]/g,"&quot;") + "\">" + camila_inline_object['options'][key] + "</option>";
                }
                html += '</select>';
            }



        }
        actual.innerHTML = html;

//            actual.innerHTML = "<input id=\""+ actual.id +"_field\" size=\""+ camila_inline_object['size'] +"\" maxlength=\""+ camila_inline_object['maxlength'] +"\" type=\"text\" value=\"" + camila_inline_object['value'] + "\" onkeypress=\"return fieldEnter(this,event,'" + actual.id + "')\" onfocus=\"highLight(this);\" onblur=\"noLight(this); return fieldBlur(this,'" + actual.id + "');\" />";

        if (!changingBool)
            changing = true;
    }

    actual.firstChild.focus();	
}


function camila_inline_editbox_init(){

    tips = xGetElementsByClassName("cf_editText");
    for (i = 0; i < tips.length; ++i) {
        xParent(tips[i],false).onclick = function () { camila_editBox(this); }
        tips[i].style.cursor = "pointer";
        tips[i].style.display = "block";
        //tips[i].title = window['camila_messages']['clicktoedit'];
    }

}

//crossbrowser load function
function addEvent(elm, evType, fn, useCapture)
{
  if (elm.addEventListener){
    elm.addEventListener(evType, fn, useCapture);
    return true;
  } else if (elm.attachEvent){
    var r = elm.attachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Please upgrade your browser to use full functionality on this page");
  }
}


function highLight(span){
    //span.parentNode.style.border = "2px solid #D1FDCD";
    //span.parentNode.style.padding = "0";
    span.style.border = "1px solid #54CE43";          
}


function noLight(span){
    //span.parentNode.style.border = "0px";
    //span.parentNode.style.padding = "2px";
    span.style.border = "0px";   
}

//sets post/get vars for update
function setVarsForm(vars){
    formVars  = vars;
}

function symbolsToEntities(sText) {
    var sNewText = "";
    var iLen = sText.length;
    for (i=0; i<iLen; i++) {
        iCode = sText.charCodeAt(i);
        sNewText += (iCode > 256? "&#" + iCode + ";": sText.charAt(i));
    }
    return sNewText;
}

function uni2ent2ndTry(srcTxt) {
   var entTxt = '';
   var c, hi, lo;
   var len = 0;
   for (var i=0, code; code=srcTxt.charCodeAt(i); i++) {
      // need to convert to HTML entity
      if (code > 255) {
         // values in this range are surrogate pairs
         if (0xD800 <= code && code <= 0xDBFF) {
            hi = code;
            lo = srcTxt.charCodeAt(i+1);
            lo &= 0x03FF;
            hi &= 0x03FF;
            hi = hi << 10;
            code = (lo + hi) + 0x10000;
         }
         // wrap it up as a Hex entity
         c = "&#x" + code.toString(16).toUpperCase() + ";";
      }
      // smaller values can be used raw
      else {
         c = srcTxt.charAt(i);
      }
      entTxt += c;
   }
   return entTxt;
}

function addSlashes (str) {
    // Escapes single quote, double quotes and backslash characters in a string with backslashes  
    // 
    // version: 908.406
    // discuss at: http://phpjs.org/functions/addslashes
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +   improved by: marrtins
    // +   improved by: Nate
    // +   improved by: Onno Marsman
    // +   input by: Denny Wardhana
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: addslashes("kevin's birthday");
    // *     returns 1: 'kevin\'s birthday'
 
    return (str+'').replace(/([\\"'])/g, "\\$1").replace(/\u0000/g, "\\0");
}

function urlencode (str) {
    // URL-encodes string  
    // 
    // version: 908.2210
    // discuss at: http://phpjs.org/functions/urlencode
    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: travc
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Lars Fischer
    // +      input by: Ratheous
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // %          note 1: This reflects PHP 5.3/6.0+ behavior
    // *     example 1: urlencode('Kevin van Zonneveld!');
    // *     returns 1: 'Kevin+van+Zonneveld%21'
    // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
    // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
    // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
    // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'
    var hexStr = function (dec) {
        return '%' + dec.toString(16).toUpperCase();
    };

    var ret = '',
            unreserved = /[\w.-]/; // A-Za-z0-9_.- // Tilde is not here for historical reasons; to preserve it, use rawurlencode instead
    str = (str+'').toString();

    for (var i = 0, dl = str.length; i < dl; i++) {
        var ch = str.charAt(i);
        if (unreserved.test(ch)) {
            ret += ch;
        }
        else {
            var code = str.charCodeAt(i);
            // Reserved assumed to be in UTF-8, as in PHP
            if (code === 32) {
                ret += '+'; // %20 in rawurlencode
            }
            else if (code < 128) { // 1 byte
                ret += hexStr(code);
            }
            else if (code >= 128 && code < 2048) { // 2 bytes
                ret += hexStr((code >> 6) | 0xC0);
                ret += hexStr((code & 0x3F) | 0x80);
            }
            else if (code >= 2048 && code < 65536) { // 3 bytes
                ret += hexStr((code >> 12) | 0xE0);
                ret += hexStr(((code >> 6) & 0x3F) | 0x80);
                ret += hexStr((code & 0x3F) | 0x80);
            }
            else if (code >= 65536) { // 4 bytes
                ret += hexStr((code >> 18) | 0xF0);
                ret += hexStr(((code >> 12) & 0x3F) | 0x80);
                ret += hexStr(((code >> 6) & 0x3F) | 0x80);
                ret += hexStr((code & 0x3F) | 0x80);
            }
        }
    }
    return ret;
}


function camila_inline_update_selected(field,value)
{

if (value == '')
{
 value = window.prompt("Inserire il nuovo valore:","");

 if (value == '' || value == null)
{
alert('Operazione non eseguita!');
return;
}

}


    var mySplitResult = camila_selectedIds.split(",");

    for(j = 0; j < mySplitResult.length; j++){

        if (mySplitResult[j] != "") {
//            camila_inline_update_by_id(mySplitResult[i],field,value);


    var id = mySplitResult[j];
    var url = camila_inline_script + camila_inline[id] + "&camila_inline&camila_inline_field=" + field;

    remotos = new datosServidor;
    nt = remotos.enviar(url);

    eval("camila_inline_object = " + nt);

    if (camila_inline_object == null)
        alert('null object...');

    if (camila_inline_object['result'] != 'OK')
        return;

        var url = camila_inline_script + "camila_inline&" + camila_inline_object["name"] + "=" + escape(value) + "&time=" + new Date().getTime();

        for (var key in camila_inline_object) {

            if (key!='name' && key!='value');
                url = url + "&" + key + "=" + escape(camila_inline_object[key]);
        }

         elem = document.getElementById(field+id);

        nt = remotos.enviar(url,"");
        eval("var result = " + nt);
        
        if (result['result'] == "OK") {
            if (result['type'] == 'select') {
                    elem.innerHTML = symbolsToEntities(result['options'][result['value']]);
            }
            else
                elem.innerHTML = symbolsToEntities(result['value']);
        } else {
            alert(result['error_desc']);
            elem.innerHTML = camila_inline_object['value'];
        }



          }
    }

}
