/**
 * DomMenuBuilder : wrapper for dommenu,
 * (http://www.mojavelinux.com/projects/dommenu/)
 * construct it in a more accessible way.
 * Do what you want with it, except render it
 * more ugly and inneficient (is it possible ? ;o) )
 * @date 10/2005
 * @author Alexandre SIMON (alex at zybar.net) http://alex.zybar.net
 * @depends dommenu-0.3.5
 */

var DomMenuBuilder_tmpHash = null;

/**
 Notes :
 * IE :
   - Element implementation does not define hasAttribute() 
     or hasAttributes() (DOM Level 2) methods
 * Firefox :
   - Firefox is good; use Firefox.
*/

function DomMenuBuilder() {
  this.settings = null;
};

DomMenuBuilder.prototype.replace = function(containerId) {
  this.parse(containerId);
  this.ulElem.style.display = 'none';
  this.install(this.containerId);
  this.containerId = null;
  this.ulElem = null;
  DomMenuBuilder_tmpHash = null;
};

DomMenuBuilder.prototype.setSettings = function(hash) {
  this.settings = hash;
};

DomMenuBuilder.prototype.parse = function(containerId) {
  var contElem = document.getElementById(containerId);
  if (contElem == null) {
    alert('could not find '+containerId);
    return false;
  }
  var uls = contElem.getElementsByTagName('ul');
  if (uls.length == 0) {
    alert('could not find any ul tag in '+containerId);
    return false;
  }
  var ulElem = uls.item(0);
  eval("DomMenuBuilder_tmpHash = new Hash("+this.walkUL(ulElem)+")");
  this.ulElem = ulElem;
  this.containerId = containerId;
  return true;
};

DomMenuBuilder.prototype.install = function(containerId) {
  domMenu_data.set(containerId, DomMenuBuilder_tmpHash);
  if (this.settings != null) domMenu_settings.set(containerId, this.settings);
  domMenu_activate(containerId);
};

DomMenuBuilder.prototype.walkUL = function(ul) {
  var hashDef = '';
  if (ul.hasChildNodes()) {
    var liIndex = 0;
    for (var i = 0; i < ul.childNodes.length; i++) {
      var childHashDef = '';
      var li = ul.childNodes.item(i);
      if (li.nodeType == 1 && 'li' == li.tagName.toLowerCase()) {
        liIndex++;
	var liText = '';
	var liLink = null;
	if (li.hasChildNodes()) {
          for (var j = 0; j < li.childNodes.length; j++) {
            var node = li.childNodes.item(j);
	    switch (node.nodeType)
	    {
              case 1: // ELEMENT
                if ('ul' == node.tagName.toLowerCase()) {
                  childHashDef += this.walkUL(node);
		} else {
                  if ('a' == node.tagName.toLowerCase()) {
		    if (node.getAttribute('href') != null) { // hasAttribute not supported by IE
                      if (liLink != null) { // FIXME duplicate : what to do ?
		      }  else {
                        liLink = node.getAttribute('href');
		      }
		    }
		    // TODO : Other attributes ?
		  }
		  liText += this.elementToText(node);
		}
		break;
	      case 3: // TEXT
		liText += node.nodeValue;
		break;
	    }
	  }
	}
	if (liIndex > 1) {
          hashDef += ",\n";
	} else {
          hashDef += "\n";
	}
	hashDef += liIndex+", new Hash('contents', '"+liText.replace(/^\s*(.*?)\s*$/g, "$1").replace(/\'/g, "\\'")+"'";
	if (liLink != null) hashDef += ", 'uri', '"+liLink+"'";
	if (childHashDef != '') {
          hashDef += "," + childHashDef;
	}
	hashDef += ")";
      }
    }
  }
  return hashDef;
};

DomMenuBuilder.prototype.elementToText = function(elem) {
  if (document.all) {
    return elem.outerHTML;
  }
  var str = "<"+elem.tagName;
  if (elem.attributes.length > 0) { // hasAttributes not supported by IE
    str += " "; 
    for (var i = 0; i < elem.attributes.length; i++) {
      var attr = elem.attributes.item(i);
      str += attr.name + "=\"" + attr.value.replace(/\"/g, "&quot;")+"\"";
    }
  }
  if (elem.hasChildNodes()) {
    return str + ">" + elem.innerHTML + "</"+elem.tagName+">";
  } else {
    return str+"/>";
  }
};
