/** $Id: domLib.js 2398 2006-10-22 20:08:09Z dallen $ */
// {{{ license

/*
 * Copyright 2002-2005 Dan Allen, Mojavelinux.com (dan.allen@mojavelinux.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// }}}
// {{{ intro

/**
 * Title: DOM Library Core
 * Version: 0.72
 *
 * Summary:
 * A set of commonly used functions that make it easier to create javascript
 * applications that rely on the DOM.
 *
 * Updated: $Date: 2006-10-22 16:08:09 -0400 (Sun, 22 Oct 2006) $
 *
 * Maintainer: Dan Allen <dan.allen@mojavelinux.com>
 * Maintainer: Jason Rust <jrust@rustyparts.com>
 *
 * License: Apache 2.0
 */

// }}}
// {{{ browser detection

// Formal browser detect library, courtesy of QuirksMode (http://www.quirksmode.org/js/detect.html)
var BrowserDetect = {
	init: function () {
		this.engine = "unknown engine";
		this.browser = this.searchString(this.dataBrowser) || "unknown browser";
		this.version = this.searchVersion(navigator.userAgent)
			|| this.searchVersion(navigator.appVersion)
			|| "unknown version";
		this.OS = this.searchString(this.dataOS) || "unknown OS";
		this.mode = (document.compatMode && document.compatMode == 'CSS1Compat' ? 'Strict' : 'Quirks');
	},
	searchString: function (data) {
		for (var i = 0; i < data.length; i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1) {
					if (data[i].engine) {
						this.engine = data[i].engine;
					}
					return data[i].identity;
				}	
			}
			else if (dataProp) {
				if (data[i].engine) {
					this.engine = data[i].engine;
				}
				return data[i].identity;
			}		
		}
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) {
			return;
		}

		return parseFloat(dataString.substring(index + this.versionSearchString.length + 1));
	},
	dataBrowser: [
		{
			string: navigator.userAgent,
			subString: "OmniWeb",
			versionSearch: "OmniWeb/",
			identity: "OmniWeb",
			engine: "WebCore"
		},
		{
			// khtml browsers may use string "khtml, like gecko"
			string: navigator.vendor,
			subString: "Apple",
			identity: "Safari",
			engine: "KHTML"
		},
		{
			prop: window.opera,
			identity: "Opera",
			engine: "Presto"
		},
		{
			string: navigator.vendor,
			subString: "iCab",
			identity: "iCab",
			engine: "iCab"
		},
		{
			// khtml browsers may use string "khtml, like gecko"
			string: navigator.vendor,
			subString: "KDE",
			identity: "Konqueror",
			engine: "KHTML"
		},
		{
			string: navigator.userAgent,
			subString: "Firefox",
			identity: "Firefox",
			engine: "Gecko"
		},
		{
			string: navigator.vendor,
			subString: "Camino",
			identity: "Camino",
			engine: "Gecko"
		},
		{
			string: navigator.userAgent,
			subString: "Netscape",
			identity: "Netscape",
			engine: "Gecko"
		},
		{
			string: navigator.userAgent,
			subString: "MSIE",
			identity: "Explorer",
			versionSearch: "MSIE",
			engine: "Trident"
		},
		{
			string: navigator.userAgent,
			subString: "Gecko",
			identity: "Mozilla",
			versionSearch: "rv",
			engine: "Gecko"
		},
		{
			string: navigator.userAgent,
			subString: "Mozilla",
			identity: "Netscape",
			versionSearch: "Mozilla"
		}
	],
	dataOS : [
		{
			string: navigator.platform,
			subString: "Win",
			identity: "Windows"
		},
		{
			string: navigator.platform,
			subString: "Mac",
			identity: "Mac"
		},
		{
			string: navigator.platform,
			subString: "Linux",
			identity: "Linux"
		}
	]
};

BrowserDetect.init();

// }}}
// {{{ global constants (DO NOT EDIT)

// -- Browser Identity Flags --
var domLib_isMac = BrowserDetect.OS == 'Mac';
var domLib_isWin = BrowserDetect.OS == 'Windows';
var domLib_isGecko = BrowserDetect.engine == 'Gecko';
var domLib_isOpera = BrowserDetect.browser == 'Opera';
var domLib_isSafari = BrowserDetect.browser == 'Safari';
var domLib_isKonq = BrowserDetect.browser == 'Konqueror';
var domLib_isKHTML = BrowserDetect.engine == 'KHTML';
var domLib_isIE = BrowserDetect.browser == 'Explorer';
var domLib_isIE50 = (domLib_isIE && BrowserDetect.version == 5);
var domLib_isIE5 = (domLib_isIE && BrowserDetect.version >= 5 && BrowserDetect.version < 6);
var domLib_isMacIE = (domLib_isIE && domLib_isMac);

// -- Browser Abilities --
var domLib_standardsMode = BrowserDetect.mode == 'Strict';
var domLib_useLibrary = ((domLib_isOpera && BrowserDetect.version >= 7) || domLib_isKHTML ||
	(domLib_isIE && BrowserDetect.version >= 5) || domLib_isGecko || domLib_isMacIE || document.defaultView);
// fixed in Konq3.2
var domLib_hasBrokenTimeout = (domLib_isMacIE || (domLib_isKonq && BrowserDetect.version >= 3.2));
var domLib_canFade = (domLib_isGecko || domLib_isIE || domLib_isSafari || domLib_isOpera);
var domLib_canDrawOverSelect = ((domLib_isIE && BrowserDetect.version >= 7) || domLib_isMac || domLib_isOpera || domLib_isGecko);
var domLib_canDrawOverFlash = (domLib_isMac || domLib_isWin);
var domLib_detectObstructionsEnabled = true;

// -- Event Variables --
var domLib_eventTarget = domLib_isIE ? 'srcElement' : 'currentTarget';
var domLib_eventButton = domLib_isIE ? 'button' : 'which';
var domLib_eventTo = domLib_isIE ? 'toElement' : 'relatedTarget';
var domLib_stylePointer = domLib_isIE ? 'hand' : 'pointer';
// NOTE: a bug exists in Opera that prevents maxWidth from being set to 'none', so we make it huge
var domLib_styleNoMaxWidth = domLib_isOpera ? '10000px' : 'none';
var domLib_hidePosition = '-1000px';
var domLib_scrollbarWidth = 14;
var domLib_autoId = 1;
var domLib_zIndex = 100;

// -- Detection --
var domLib_collisionElements;
var domLib_collisionsCached = false;

var domLib_timeoutStateId = 0;
var domLib_timeoutStates = new Hash();

// }}}
// {{{ DOM enhancements

if (!document.ELEMENT_NODE)
{
	document.ELEMENT_NODE = 1;
	document.ATTRIBUTE_NODE = 2;
	document.TEXT_NODE = 3;
	document.DOCUMENT_NODE = 9;
	document.DOCUMENT_FRAGMENT_NODE = 11;
}

function domLib_clone(obj)
{
	var copy = {};
	for (var i in obj)
	{
		var value = obj[i];
		try
		{
			if (value != null && typeof(value) == 'object' && value != window && !value.nodeType)
			{
				copy[i] = domLib_clone(value);
			}
			else
			{
				copy[i] = value;
			}
		}
		catch(e)
		{
			copy[i] = value;
		}
	}

	return copy;
}

// }}}
// {{{ class Hash()

function Hash()
{
	this.length = 0;
	this.numericLength = 0; 
	this.elementData = [];
	for (var i = 0; i < arguments.length; i += 2)
	{
		if (typeof(arguments[i + 1]) != 'undefined')
		{
			this.elementData[arguments[i]] = arguments[i + 1];
			this.length++;
			if (arguments[i] == parseInt(arguments[i])) 
			{
				this.numericLength++;
			}
		}
	}
}

// using prototype as opposed to inner functions saves on memory 
Hash.prototype.get = function(in_key)
{
	if (typeof(this.elementData[in_key]) != 'undefined') {
		return this.elementData[in_key];
	}

	return null;
}

Hash.prototype.set = function(in_key, in_value)
{
	if (typeof(in_value) != 'undefined')
	{
		if (typeof(this.elementData[in_key]) == 'undefined')
		{
			this.length++;
			if (in_key == parseInt(in_key)) 
			{
				this.numericLength++;
			}
		}

		return this.elementData[in_key] = in_value;
	}

	return false;
}

Hash.prototype.remove = function(in_key)
{
	var tmp_value;
	if (typeof(this.elementData[in_key]) != 'undefined')
	{
		this.length--;
		if (in_key == parseInt(in_key)) 
		{
			this.numericLength--;
		}

		tmp_value = this.elementData[in_key];
		delete this.elementData[in_key];
	}

	return tmp_value;
}

Hash.prototype.size = function()
{
	return this.length;
}

Hash.prototype.has = function(in_key)
{
	return typeof(this.elementData[in_key]) != 'undefined';
}

Hash.prototype.find = function(in_obj)
{
	for (var tmp_key in this.elementData) 
	{
		if (this.elementData[tmp_key] == in_obj) 
		{
			return tmp_key;
		}
	}

	return null;
}

Hash.prototype.merge = function(in_hash)
{
	for (var tmp_key in in_hash.elementData) 
	{
		if (typeof(this.elementData[tmp_key]) == 'undefined') 
		{
			this.length++;
			if (tmp_key == parseInt(tmp_key)) 
			{
				this.numericLength++;
			}
		}

		this.elementData[tmp_key] = in_hash.elementData[tmp_key];
	}
}

Hash.prototype.compare = function(in_hash)
{
	if (this.length != in_hash.length) 
	{
		return false;
	}

	for (var tmp_key in this.elementData) 
	{
		if (this.elementData[tmp_key] != in_hash.elementData[tmp_key]) 
		{
			return false;
		}
	}
	
	return true;
}

// }}}
// {{{ domLib_isDescendantOf()

function domLib_isDescendantOf(in_object, in_ancestor, in_bannedTags)
{
	if (in_object == null)
	{
		return false;
	}

	if (in_object == in_ancestor)
	{
		return true;
	}

	if (typeof(in_bannedTags) != 'undefined' &&
		(',' + in_bannedTags.join(',') + ',').indexOf(',' + in_object.tagName + ',') != -1)
	{
		return false;
	}

	while (in_object != document.documentElement)
	{
		try
		{
			if ((tmp_object = in_object.offsetParent) && tmp_object == in_ancestor)
			{
				return true;
			}
			else if ((tmp_object = in_object.parentNode) == in_ancestor)
			{
				return true;
			}
			else
			{
				in_object = tmp_object;
			}
		}
		// in case we get some wierd error, assume we left the building
		catch(e)
		{
			return false;
		}
	}

	return false;
}

// }}}
// {{{ domLib_detectObstructions()

/**
 * For any given target element, determine if elements on the page
 * are colliding with it that do not obey the rules of z-index.
 */
function domLib_detectObstructions(in_object, in_recover, in_useCache)
{
	// the reason for the cache is that if the root menu is built before
	// the page is done loading, then it might not find all the elements.
	// so really the only time you don't use cache is when building the
	// menu as part of the page load
	if (!domLib_collisionsCached)
	{
		var tags = [];

		if (!domLib_canDrawOverFlash)
		{
			tags[tags.length] = 'object';
		}

		if (!domLib_canDrawOverSelect)
		{
			tags[tags.length] = 'select';
		}

		domLib_collisionElements = domLib_getElementsByTagNames(tags, true);
		domLib_collisionsCached = in_useCache;
	}

	// if we don't have a tip, then unhide selects
	if (in_recover)
	{
		for (var cnt = 0; cnt < domLib_collisionElements.length; cnt++)
		{
			var thisElement = domLib_collisionElements[cnt];

			if (!thisElement.hideList)
			{
				thisElement.hideList = new Hash();
			}

			thisElement.hideList.remove(in_object.id);
			if (!thisElement.hideList.length)
			{
				domLib_collisionElements[cnt].style.visibility = 'visible';
				if (domLib_isKonq)
				{
					domLib_collisionElements[cnt].style.display = '';
				}
			}
		}

		return;
	}
	else if (domLib_collisionElements.length == 0)
	{
		return;
	}

	// okay, we have a tip, so hunt and destroy
	var objectOffsets = domLib_getOffsets(in_object);

	for (var cnt = 0; cnt < domLib_collisionElements.length; cnt++)
	{
		var thisElement = domLib_collisionElements[cnt];

		// if collision element is in active element, move on
		// WARNING: is this too costly?
		if (domLib_isDescendantOf(thisElement, in_object))
		{
			continue;
		}

		// konqueror only has trouble with multirow selects
		if (domLib_isKonq &&
			thisElement.tagName == 'SELECT' &&
			(thisElement.size <= 1 && !thisElement.multiple))
		{
			continue;
		}

		if (!thisElement.hideList)
		{
			thisElement.hideList = new Hash();
		}

		var selectOffsets = domLib_getOffsets(thisElement); 
		var center2centerDistance = Math.sqrt(Math.pow(selectOffsets.get('leftCenter') - objectOffsets.get('leftCenter'), 2) + Math.pow(selectOffsets.get('topCenter') - objectOffsets.get('topCenter'), 2));
		var radiusSum = selectOffsets.get('radius') + objectOffsets.get('radius');
		// the encompassing circles are overlapping, get in for a closer look
		if (center2centerDistance < radiusSum)
		{
			// tip is left of select
			if ((objectOffsets.get('leftCenter') <= selectOffsets.get('leftCenter') && objectOffsets.get('right') < selectOffsets.get('left')) ||
			// tip is right of select
				(objectOffsets.get('leftCenter') > selectOffsets.get('leftCenter') && objectOffsets.get('left') > selectOffsets.get('right')) ||
			// tip is above select
				(objectOffsets.get('topCenter') <= selectOffsets.get('topCenter') && objectOffsets.get('bottom') < selectOffsets.get('top')) ||
			// tip is below select
				(objectOffsets.get('topCenter') > selectOffsets.get('topCenter') && objectOffsets.get('top') > selectOffsets.get('bottom')))
			{
				thisElement.hideList.remove(in_object.id);
				if (!thisElement.hideList.length)
				{
					thisElement.style.visibility = 'visible';
					if (domLib_isKonq)
					{
						thisElement.style.display = '';
					}
				}
			}
			else
			{
				thisElement.hideList.set(in_object.id, true);
				thisElement.style.visibility = 'hidden';
				if (domLib_isKonq)
				{
					thisElement.style.display = 'none';
				}
			}
		}
	}
}

// }}}
// {{{ domLib_getOffsets()

function domLib_getOffsets(in_object, in_preserveScroll)
{
	if (typeof(in_preserveScroll) == 'undefined') {
		in_preserveScroll = false;
	}

	var originalObject = in_object;
	var originalWidth = in_object.offsetWidth;
	var originalHeight = in_object.offsetHeight;
	var offsetLeft = 0;
	var offsetTop = 0;

	while (in_object)
	{
		offsetLeft += in_object.offsetLeft;
		offsetTop += in_object.offsetTop;
		in_object = in_object.offsetParent;
		// consider scroll offset of parent elements
		if (in_object && !in_preserveScroll)
		{
			offsetLeft -= in_object.scrollLeft;
			offsetTop -= in_object.scrollTop;
		}
	}

	// MacIE misreports the offsets (even with margin: 0 in body{}), still not perfect
	if (domLib_isMacIE) {
		offsetLeft += 10;
		offsetTop += 10;
	}

	return new Hash(
		'left',	offsetLeft,
		'top', offsetTop,
		'right', offsetLeft + originalWidth,
		'bottom', offsetTop + originalHeight,
		'leftCenter', offsetLeft + originalWidth/2,
		'topCenter', offsetTop + originalHeight/2,
		'radius', Math.max(originalWidth, originalHeight) 
	);
}

// }}}
// {{{ domLib_setTimeout()

function domLib_setTimeout(in_function, in_timeout, in_args)
{
	if (typeof(in_args) == 'undefined')
	{
		in_args = [];
	}

	if (in_timeout == -1)
	{
		// timeout event is disabled
		return 0;
	}
	else if (in_timeout == 0)
	{
		in_function(in_args);
		return 0;
	}

	// must make a copy of the arguments so that we release the reference
	var args = domLib_clone(in_args);

	if (!domLib_hasBrokenTimeout)
	{
		return setTimeout(function() { in_function(args); }, in_timeout);
	}
	else
	{
		var id = domLib_timeoutStateId++;
		var data = new Hash();
		data.set('function', in_function);
		data.set('args', args);
		domLib_timeoutStates.set(id, data);

		data.set('timeoutId', setTimeout('domLib_timeoutStates.get(' + id + ').get(\'function\')(domLib_timeoutStates.get(' + id + ').get(\'args\')); domLib_timeoutStates.remove(' + id + ');', in_timeout));
		return id;
	}
}

// }}}
// {{{ domLib_clearTimeout()

function domLib_clearTimeout(in_id)
{
	if (!domLib_hasBrokenTimeout)
	{
		if (in_id > 0) {
			clearTimeout(in_id);
		}
	}
	else
	{
		if (domLib_timeoutStates.has(in_id))
		{
			clearTimeout(domLib_timeoutStates.get(in_id).get('timeoutId'))
			domLib_timeoutStates.remove(in_id);
		}
	}
}

// }}}
// {{{ domLib_getEventPosition()

function domLib_getEventPosition(in_eventObj)
{
	var eventPosition = new Hash('x', 0, 'y', 0, 'scrollX', 0, 'scrollY', 0);

	// IE varies depending on standard compliance mode
	if (domLib_isIE)
	{
		var doc = (domLib_standardsMode ? document.documentElement : document.body);
		// NOTE: events may fire before the body has been loaded
		if (doc)
		{
			eventPosition.set('x', in_eventObj.clientX + doc.scrollLeft);
			eventPosition.set('y', in_eventObj.clientY + doc.scrollTop);
			eventPosition.set('scrollX', doc.scrollLeft);
			eventPosition.set('scrollY', doc.scrollTop);
		}
	}
	else
	{
		eventPosition.set('x', in_eventObj.pageX);
		eventPosition.set('y', in_eventObj.pageY);
		eventPosition.set('scrollX', in_eventObj.pageX - in_eventObj.clientX);
		eventPosition.set('scrollY', in_eventObj.pageY - in_eventObj.clientY);
	}

	return eventPosition;
}

// }}}
// {{{ domLib_cancelBubble()

function domLib_cancelBubble(in_event)
{
	var eventObj = in_event ? in_event : window.event;
	eventObj.cancelBubble = true;
}

// }}}
// {{{ domLib_getIFrameReference()

function domLib_getIFrameReference(in_frame)
{
	if (domLib_isGecko || domLib_isIE)
	{
		return in_frame.frameElement;
	}
	else
	{
		// we could either do it this way or require an id on the frame
		// equivalent to the name
		var name = in_frame.name;
		if (!name || !in_frame.parent)
		{
			return null;
		}

		var candidates = in_frame.parent.document.getElementsByTagName('iframe');
		for (var i = 0; i < candidates.length; i++)
		{
			if (candidates[i].name == name)
			{
				return candidates[i];
			}
		}

		return null;
	}
}

// }}}
// {{{ domLib_getElementsByClass()

function domLib_getElementsByClass(in_class)
{
	var elements = domLib_isIE5 ? document.all : document.getElementsByTagName('*');	
	var matches = [];	
	var cnt = 0;
	for (var i = 0; i < elements.length; i++)
	{
		if ((" " + elements[i].className + " ").indexOf(" " + in_class + " ") != -1)
		{
			matches[cnt++] = elements[i];
		}
	}

	return matches;
}

// }}}
// {{{ domLib_getElementsByTagNames()

function domLib_getElementsByTagNames(in_list, in_excludeHidden)
{
	var elements = [];
	for (var i = 0; i < in_list.length; i++)
	{
		var matches = document.getElementsByTagName(in_list[i]);
		for (var j = 0; j < matches.length; j++)
		{
			// skip objects that have nested embeds, or else we get "flashing"
			if (matches[j].tagName == 'OBJECT' && domLib_isGecko)
			{
				var kids = matches[j].childNodes;
				var skip = false;
				for (var k = 0; k < kids.length; k++)
				{
					if (kids[k].tagName == 'EMBED')
					{
						skip = true;
						break;
					}
				}
				if (skip) continue;
			}

			// NOTE: could use a special "ignore" style class
			if (in_excludeHidden && domLib_getComputedStyle(matches[j], 'visibility') == 'hidden')
			{
				continue;
			}

			elements[elements.length] = matches[j];	
		}
	}

	return elements;
}

// }}}
// {{{ domLib_getComputedStyle()

function domLib_getComputedStyle(in_obj, in_property)
{
	if (domLib_isIE)
	{
		var humpBackProp = in_property.replace(/-(.)/, function (a, b) { return b.toUpperCase(); });
		return eval('in_obj.currentStyle.' + humpBackProp);
	}
	// getComputedStyle() is broken in konqueror, so let's go for the style object
	else if (domLib_isKonq)
	{
		//var humpBackProp = in_property.replace(/-(.)/, function (a, b) { return b.toUpperCase(); });
		return eval('in_obj.style.' + in_property);
	}
	else
	{
		return document.defaultView.getComputedStyle(in_obj, '').getPropertyValue(in_property);
	}
}

// }}}
// {{{ makeTrue()

function makeTrue()
{
	return true;
}

// }}}
// {{{ makeFalse()

function makeFalse()
{
	return false;
}

// }}}
