/** $Id: domMenu.js 2399 2006-10-22 20:09:10Z dallen $ */
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
 * Title: DOM Menu Library
 * Version: 0.3.7
 *
 * Summary:
 * A widget library for creating dynamic "popout" menus on webpages.  The menu can
 * either be horizontal or vertical, and can open in either direction.  It has
 * both edge detection and obstruction detection (for browsers that cannot
 * hide select boxes or flash animations).  The styles for the menu items are
 * controlled entirely through CSS. The menus are created and destroyed using
 * the DOM.  Menu configuration is done using a custom Hash() class.
 *
 * Dependency: domLib.js version 0.72
 *
 * Maintainer: Dan Allen <dan@mojavelinux.com>
 * Contributors:
 * 		Jason Rust <jrust@rustyparts.com>
 *
 * License: Apache 2.0
 * However, if you use this library, you earn the position of official bug
 * reporter :) Please post questions or problem reports to the newsgroup:
 *
 *   http://groups.google.com/group/dom-menu
 *
 * If you are doing this for commercial work, perhaps you could send me a few
 * Starbucks Coffee gift dollars or PayPal bucks to encourage future
 * developement (NOT REQUIRED).  E-mail me for my snail mail address.
 *
 * Homepage: http://www.mojavelinux.com/projects/dommenu/
 *
 * Freshmeat Project: http://freshmeat.net/projects/dommenu/?topic_id=92
 *
 * Updated: $Date: 2006-10-22 16:09:10 -0400 (Sun, 22 Oct 2006) $
 *
 * Supported Browsers:
 * Mozilla (Gecko), IE 5.0+, IE on Mac, Safari, Konqueror, Opera 7+
 *
 * If there is a non-negative click open delay, then any uri of the element will be ignored
 *
 * The alternate contents for a hover element are treated by creating to <span> wrapper elements
 * and then alternating the display of them.  This avoids the need for innerHTML, which can
 * do nasty things to the browsers.  If <span> turns out to be a bad choice for tags, then a
 * non-HTML element can be used instead.
 *
 * Dev Notes:
 * - added cellSpacing = 0 for domLib_isMacIE (two places)
 * - seems that Safari and Firefox share an offset problem of menu under parent (pmp example)
 * - must use createTextNode() to add the "\n" that is required for Mac to
 *   render the appendChild element (two places); this might be the solution for
 *   the sub menus as well
 * - Safari seems to have a problem with offsetTop if a descendent of body has a margin; solution
 *   is to use padding on the body
 */

// }}}
// {{{ settings (editable)

var domMenu_data = new Hash();
var domMenu_settings = new Hash();

domMenu_settings.set('global', new Hash(
	'menuBarClass', 'domMenu_menuBar',
	'menuElementClass', 'domMenu_menuElement',
	'menuElementHoverClass', 'domMenu_menuElementHover',
	'menuElementActiveClass', 'domMenu_menuElementHover',
	'subMenuBarClass', 'domMenu_subMenuBar',
	'subMenuElementClass', 'domMenu_subMenuElement',
	'subMenuElementHoverClass', 'domMenu_subMenuElementHover',
	'subMenuElementActiveClass', 'domMenu_subMenuElementHover',
	'subMenuElementHeadingClass', 'domMenu_subMenuElementHeading',
	'subMenuTargetFrame', false,
	'targetDocumentXOrigin', 0,
	'targetDocumentYOrigin', 0,
	'menuBarWidth', '100%',
	'subMenuMinWidth', 'inherit',
	'distributeSpace', true,
	'axis', 'horizontal',
	'verticalExpand', 'south',
	'horizontalExpand', 'east',
	'expandMenuArrowUrl', 'arrow.gif',
	'subMenuWidthCorrection', 0,
	'verticalSubMenuOffsetY', 0,
	'verticalSubMenuOffsetX', 0,
	'horizontalSubMenuOffsetX', 0,
	'horizontalSubMenuOffsetY', 0,
	'screenPadding', 0,
	'openMouseoverMenuDelay', 300,
	'openMousedownMenuDelay', -1,
	'closeMouseoutMenuDelay', 800,
	'closeClickMenuDelay', -1,
	'openMouseoverSubMenuDelay', 300,
	'openClickSubMenuDelay', -1,
	'closeMouseoutSubMenuDelay', 300,
	'closeClickSubMenuDelay', -1,
	'baseZIndex', 100,
	'baseUri', ''
));

// }}}
// {{{ globals (DO NOT EDIT)

/**
 * The data for the menu is stored here, loaded from an external file
 * @hash domMenu_data
 */
var domMenu_data;

var domMenu_selectElements;
var domMenu_scrollbarWidth = 14;
var domMenu_eventTo = domLib_isIE ? 'toElement' : 'relatedTarget';
var domMenu_eventFrom = domLib_isIE ? 'fromElement' : 'relatedTarget';

var domMenu_activeElement = new Hash();

/**
 * Array of hashes listing the timouts currently running for opening/closing menus
 * @array domMenu_timeouts
 */
var domMenu_timeouts = [];
domMenu_timeouts['open'] = new Hash();
domMenu_timeouts['close'] = new Hash();

/**
 * Style to use for a link pointer, which is different between Gecko and IE
 * @var domMenu_pointerStyle
 */
var domMenu_pointerStyle = domLib_isIE ? 'hand' : 'pointer';

// }}}
// {{{ domMenu_activate()

function domMenu_activate(in_containerId, in_disableWarning)
{
	var container;
	var data;

	// make sure we can use the menu system
	if (!domLib_useLibrary)
	{
		if (!in_disableWarning)
		{
				alert('domMenu: Browser not supported.  Menu will be disabled.');
		}

		return;
	}

	// make sure that this is a valid menu, 
	// and that the menu actually has data
	if (!(container = document.getElementById(in_containerId)) || 
		!(data = domMenu_data.get(in_containerId)) ||
		data.numericLength == 0) {
		if (!in_disableWarning) {
				alert('domMenu: Menu failed to load.');
		}

		return;
	}

	if (domLib_isIE && window.attachEvent) {
		window.attachEvent('onunload', domMenu_unloadEventCache);
	}

	// start with the global settings and merge in the local changes
	if (!domMenu_settings.has(in_containerId)) {
		domMenu_settings.set(in_containerId, new Hash());
	}

	var settings = domMenu_settings.get(in_containerId);
	for (var i in domMenu_settings.get('global').elementData) {
		if (!settings.has(i)) {
			settings.set(i, domMenu_settings.get('global').get(i));
		}
	}

	// populate the zero level element
	container.data = new Hash(
		'parentElement', false,
		'numChildren', data.numericLength,
		'childElements', new Hash(),
		'level', 0,
		'index', 1
	);
	
	// if we choose to distribute either height or width, determine ratio of each cell
	var distributeRatio = Math.round(100/container.data.get('numChildren')) + '%';
	
	// the first menu is the rootMenu, which is a child of the zero level element
	var rootMenu = document.createElement('div');
	rootMenu.id = in_containerId + '-0';
	rootMenu.className = settings.get('menuBarClass');
	container.data.set('subMenu', rootMenu);

	var rootMenuTable = rootMenu.appendChild(document.createElement('table'));
	if (domLib_isKonq || domLib_isMacIE) {
		rootMenuTable.cellSpacing = 0;
	}

	rootMenuTable.style.border = 0;
	rootMenuTable.style.borderCollapse = 'collapse';
	rootMenuTable.style.width = settings.get('menuBarWidth');
	var rootMenuTableBody = rootMenuTable.appendChild(document.createElement('tbody'));

	var numSiblings = container.data.get('numChildren');
	for (var index = 1; index <= numSiblings; index++) {
		// create a row the first time if horizontal or each time if vertical
		if (index == 1 || settings.get('axis') == 'vertical') {
			var rootMenuTableRow = rootMenuTableBody.appendChild(document.createElement('tr'));
		}

		// create an instance of the root level menu element
		var rootMenuTableCell = rootMenuTableRow.appendChild(document.createElement('td'));
		rootMenuTableCell.style.padding = 0;
		rootMenuTableCell.id = in_containerId + '-' + index;
		// add element to list of parent children
		container.data.get('childElements').set(rootMenuTableCell.id, rootMenuTableCell);

		// assign the settings to the root level element
		// NOTE: this is a problem if two menus are using the same data
		rootMenuTableCell.data = data.get(index);
		rootMenuTableCell.data.merge(new Hash(
			'basename', in_containerId,
			'parentElement', container,
			'numChildren', rootMenuTableCell.data.numericLength,
			'childElements', new Hash(),
			'offsets', new Hash(),
			'level', container.data.get('level') + 1,
			'index', index
		));

		// assign the styles
		rootMenuTableCell.style.cursor = 'default';
		if (settings.get('axis') == 'horizontal') {
			if (settings.get('distributeSpace')) {
				rootMenuTableCell.style.width = distributeRatio;
			}
		}

		// Needed for when the text wraps
		rootMenuTableCell.style.verticalAlign = 'top';

		var rootElement = rootMenuTableCell.appendChild(document.createElement('div'));
		rootElement.className = settings.get('menuElementClass');
		// fill in the menu element contents
		var spanElement = rootElement.appendChild(document.createElement('span'));
		// can't use createTextNode() because there might be img tags in the contents
		spanElement.innerHTML = rootMenuTableCell.data.get('contents').replace(/\/\/\//, settings.get('baseUri'));
		// add hover contents if needed
		if (rootMenuTableCell.data.has('contentsHover')) {
			spanElement = rootElement.appendChild(document.createElement('span'));
			spanElement.style.display = 'none';
			spanElement.innerHTML = rootMenuTableCell.data.get('contentsHover').replace(/\/\/\//, settings.get('baseUri'));
		}

		// MacIE has to have a newline at the end or else it barfs
		// additionally, it MUST be added using createTextNode() or IE will crash!
		if (domLib_isMacIE) {
			rootMenuTableCell.appendChild(document.createTextNode("\n"));
		}

		// attach the events
		rootMenuTableCell.onmouseover = domMenu_openMenuOnmouseoverHandler;
		rootMenuTableCell.onmouseout = domMenu_closeMenuHandler;

		if (settings.get('openMousedownMenuDelay') >= 0 && rootMenuTableCell.data.get('numChildren')) {
			rootMenuTableCell.onmousedown = domMenu_openMenuOnmousedownHandler;
			// cancel mouseup so that it doesn't propogate to global mouseup event
			rootMenuTableCell.onmouseup = domLib_cancelBubble;
			if (domLib_isIE) {
				rootMenuTableCell.ondblclick = domMenu_openMenuOnmousedownHandler;
			}
		}
		else if (rootMenuTableCell.data.get('uri')) {
			rootMenuTableCell.style.cursor = domMenu_pointerStyle;
			rootMenuTableCell.onclick = domMenu_resolveLinkHandler;
		}

		// prevent highlighting of text
		if (domLib_isIE) {
			rootMenuTableCell.onselectstart = makeFalse; 
		}

		rootMenuTableCell.oncontextmenu = makeFalse; 
	}
	
	// add the menu rootMenu to the zero level element
	rootMenu = container.appendChild(rootMenu);

	if (domLib_detectObstructionsEnabled) {
		// even though most cases the top level menu does not go away, it could
		// if this menu system is used by another process
		domLib_detectObstructions(rootMenu, false, false);
	}
}

// }}}
// {{{ domMenu_activateSubMenu()

function domMenu_activateSubMenu(in_parentElement)
{
	// NOTE: submenus not supported in MacIE because of problems using
	// appendChild on document.body
	if (domLib_isMacIE) {
		return;
	}

	// see if submenu already exists
	if (in_parentElement.data.has('subMenu')) {
		domMenu_toggleSubMenu(in_parentElement, 'visible');
		return;
	}

	var settings = domMenu_settings.get(in_parentElement.data.get('basename'));

	var targetDoc = document;
	var targetFrame = settings.get('subMenuTargetFrame');
	if (targetFrame) {
		targetDoc = targetFrame.document;
	}

	// build the submenu
	var menu = targetDoc.createElement('div');
	menu.id = in_parentElement.id + '-0';
	menu.className = settings.get('subMenuBarClass');
	menu.style.zIndex = settings.get('baseZIndex');
	menu.style.position = 'absolute';
	// position the menu in the upper left corner hidden so that we can work on it
	menu.style.visibility = 'hidden';
	menu.style.top = 0;
	menu.style.left = 0;

	in_parentElement.data.set('subMenu', menu);

	var menuTable = menu.appendChild(targetDoc.createElement('table'));
	// ** opera wants to make absolute tables width 100% **
	if (domLib_isOpera) {
		menuTable.style.width = '1px';
		menuTable.style.whiteSpace = 'nowrap';
	}

	if (domLib_isKonq || domLib_isMacIE) {
		menuTable.cellSpacing = 0;
	}

	menuTable.style.border = 0;
	menuTable.style.borderCollapse = 'collapse';
	var menuTableBody = menuTable.appendChild(targetDoc.createElement('tbody'));

	var numSiblings = in_parentElement.data.get('numChildren');
	for (var index = 1; index <= numSiblings; index++) {
		var dataIndex = in_parentElement.data.get('level') == 1 && settings.get('verticalExpand') == 'north' && settings.get('axis') == 'horizontal' ? numSiblings + 1 - index : index;
		var menuTableCell = menuTableBody.appendChild(targetDoc.createElement('tr')).appendChild(targetDoc.createElement('td'));
		menuTableCell.style.padding = 0;
		menuTableCell.id = in_parentElement.id + '-' + dataIndex;

		// add element to list of parent children
		in_parentElement.data.get('childElements').set(menuTableCell.id, menuTableCell);

		// assign the settings to nth level element
		menuTableCell.data = in_parentElement.data.get(dataIndex);
		menuTableCell.data.merge(new Hash(
			'basename', in_parentElement.data.get('basename'),
			'parentElement', in_parentElement,
			'numChildren', menuTableCell.data.numericLength,
			'childElements', new Hash(),
			'offsets', new Hash(),
			'level', in_parentElement.data.get('level') + 1,
			'index', index
		));
		
		// assign the styles
		menuTableCell.style.cursor = 'default';
		
		var element = menuTableCell.appendChild(targetDoc.createElement('div')); 
		var outerElement = element;
		outerElement.className = settings.get('subMenuElementClass'); 

		if (menuTableCell.data.get('numChildren')) {
			element = outerElement.appendChild(targetDoc.createElement('div'));
			element.style.backgroundImage = 'url(' + settings.get('expandMenuArrowUrl') + ')';
			element.style.backgroundRepeat = 'no-repeat';
			if (settings.get('horizontalExpand') == 'east') {
				element.style.backgroundPosition = 'right center';
				element.style.paddingRight = '12px';
			}
			else {
				element.style.backgroundPosition = 'left center';
				element.style.paddingLeft = '12px';
			}
		}

		// fill in the menu item contents
		if (domLib_isMacIE) {
			// we don't support images in sub-menu elements in MacIE because in order for
			// the menu to work consistently the data has to be added with createTextNode()
			element.appendChild(targetDoc.createTextNode(menuTableCell.data.get('contents')));
			// MacIE has to have a newline and it has to be added with createTextNode!
			menuTableCell.appendChild(targetDoc.createTextNode("\n"));
		}
		else {
			element.innerHTML = menuTableCell.data.get('contents');
		}

		// attach the events
		menuTableCell.onmouseover = domMenu_openSubMenuOnmouseoverHandler;
		menuTableCell.onmouseout = domMenu_closeMenuHandler;

		if (settings.get('openClickSubMenuDelay') >= 0 && menuTableCell.data.get('numChildren')) {
			menuTableCell.onmousedown = domMenu_openSubMenuOnclickHandler;
			menuTableCell.onmouseup = domLib_cancelBubble;
			if (domLib_isIE) {
				menuTableCell.ondblclick = domMenu_openSubMenuOnclickHandler;
			}
		}
		else if (menuTableCell.data.get('uri')) {
			menuTableCell.style.cursor = domMenu_pointerStyle;
			menuTableCell.onclick = domMenu_resolveLinkHandler;
		}
		else if (!menuTableCell.data.get('numChildren')) {
			outerElement.className += ' ' + settings.get('subMenuElementHeadingClass');
		}

		// prevent highlighting of text
		if (domLib_isIE) {
			menuTableCell.onselectstart = makeFalse;
		}

		menuTableCell.oncontextmenu = makeFalse;
	}

	menu = targetDoc.body.appendChild(menu);
	domMenu_toggleSubMenu(in_parentElement, 'visible');
}

// }}}
// {{{ domMenu_changeActivePath()

/**
 * Close the old active path up to the new active element
 * and return the value of the new active element (or the same if unchanged)
 * NOTE: If the new active element is not set (false), the top level is assumed
 *
 * @return mixed new active element or false if not set
 */
function domMenu_changeActivePath(in_newActiveElement, in_oldActiveElement, in_closeDelay)
{
	// protect against crap
	if (!in_oldActiveElement && !in_newActiveElement) {
		return false;
	}

	// cancel open timeouts since we know we are opening something different now
	for (var i in domMenu_timeouts['open'].elementData) {
		domLib_clearTimeout(domMenu_timeouts['open'].get(i));
	}

	// grab some info about this menu system...will this ever be null?
	var basename = in_oldActiveElement ? in_oldActiveElement.data.get('basename') : in_newActiveElement.data.get('basename');
	var settings = domMenu_settings.get(basename);

	// build the old active path and unhighlight previously selected element, if appropriate
	var oldActivePath = new Hash();
	if (in_oldActiveElement) {
		var tmp_newActiveLevel = in_newActiveElement ? in_newActiveElement.data.get('level') : -1;
		var tmp_oldActivePathElement = in_oldActiveElement;
		do {
			// NOTE: using set() causes IE to lag and leaves behind highlighted artifacts!
			oldActivePath.elementData[tmp_oldActivePathElement.id] = tmp_oldActivePathElement; 
			// unhighlight if sibling of new element, even if it has open submenus
			if (tmp_newActiveLevel >= 0 && tmp_oldActivePathElement.data.get('level') == tmp_newActiveLevel) {
				domMenu_toggleHighlight(tmp_oldActivePathElement, false);
			}
		} while ((tmp_oldActivePathElement = tmp_oldActivePathElement.data.get('parentElement')) && tmp_oldActivePathElement.id != basename);

		// unhighlight element immediately if no submenu (or submenu is closed)
		if (!in_oldActiveElement.data.get('subMenu') || in_oldActiveElement.data.get('subMenu').style.visibility == 'hidden') {
			domMenu_toggleHighlight(in_oldActiveElement, false);
		}
	}

	// build the new path and...(explain me!)
	var newActivePath = new Hash();
	var intersectPoint;
	if (in_newActiveElement) {
		var actualActiveElement = in_newActiveElement;
		window.status = in_newActiveElement.data.get('statusText') + ' ';

		// in the event we have no old active element, just highlight new one and return
		// without setting the new active element (handled later)
		if (!in_oldActiveElement) {
			domLib_clearTimeout(domMenu_timeouts['close'].get(in_newActiveElement.id));
			domMenu_toggleHighlight(in_newActiveElement, true);
			return false;
		}
		// if the new element is in the path of the old element, then pretend event is
		// on the old active element
		else if (oldActivePath.has(in_newActiveElement.id)) {
			in_newActiveElement = in_oldActiveElement;
		}

		var tmp_newActivePathElement = in_newActiveElement;
		do {
			// if we have met up with the old active path, then record merge point
			if (!intersectPoint && oldActivePath.has(tmp_newActivePathElement.id)) {
				intersectPoint = tmp_newActivePathElement;
			}

			newActivePath.set(tmp_newActivePathElement.id, tmp_newActivePathElement); 
			domLib_clearTimeout(domMenu_timeouts['close'].get(tmp_newActivePathElement.id));
			// FIXME: this is ugly!
			if (tmp_newActivePathElement != in_oldActiveElement || actualActiveElement == in_oldActiveElement) {
				domMenu_toggleHighlight(tmp_newActivePathElement, true);
			}
		} while ((tmp_newActivePathElement = tmp_newActivePathElement.data.get('parentElement')) && tmp_newActivePathElement.id != basename);

		// if we move to the child of the old active element
		if (in_newActiveElement.data.get('parentElement') == in_oldActiveElement) {
			return in_newActiveElement;
		}
		// if the new active element is in the old active path
		else if (in_newActiveElement == in_oldActiveElement) {
			return in_newActiveElement;
		}

		// find the sibling element
		var intersectSibling;
		if (intersectPoint && oldActivePath.length > 0) {
			for (var i in oldActivePath.elementData) {
				if (oldActivePath.get(i).data.get('parentElement') == intersectPoint) {
					intersectSibling = oldActivePath.get(i);
					break;
				}
			}
		}

		var isRootLevel = in_newActiveElement.data.get('level') == 1 ? true : false;
		var closeDelay = isRootLevel ? settings.get('closeMouseoutMenuDelay') : settings.get('closeMouseoutSubMenuDelay');
	}
	else {
		var isRootLevel = false;
		var closeDelay = settings.get('closeMouseoutMenuDelay');
		window.status = window.defaultStatus;
	}

	// override the close delay with that passed in
	if (typeof(in_closeDelay) != 'undefined') {
		closeDelay = in_closeDelay;
	}

	// if there is an intersect sibling, then we need to work from there up to 
	// preserve the active path
	if (intersectSibling) {
		// only if this is not the root level to we allow the scheduled close
		// events to persist...otherwise we close immediately
		if (!isRootLevel) {
			// toggle the sibling highlight (only one sibling highlighted at a time)
			domMenu_toggleHighlight(intersectSibling, false);
		}
		// we are moving to another top level menu
		// FIXME: clean this up
		else {
			// add lingering menus outside of old active path to active path
			for (var i in domMenu_timeouts['close'].elementData) {
				if (!oldActivePath.has(i)) {
					var tmp_element = document.getElementById(i);
					if (tmp_element.data.get('basename') == basename) {
						oldActivePath.set(i, tmp_element);
					}
				}
			}
		}
	}

	// schedule the old active path to be closed
	for (var i in oldActivePath.elementData) {
		if (newActivePath.has(i)) {
			continue;
		}

		// make sure we don't double schedule here
		domLib_clearTimeout(domMenu_timeouts['close'].get(i));

		if (isRootLevel) {
			domMenu_toggleHighlight(oldActivePath.get(i), false); 
			domMenu_toggleSubMenu(oldActivePath.get(i), 'hidden');
		}
		else {
			domMenu_timeouts['close'].set(i, domLib_setTimeout(domMenu_closeMenuCallback, closeDelay, [oldActivePath.get(i), basename]));
		}
	}
	
	return in_newActiveElement;
}

// }}}
// {{{ domMenu_deactivate()

function domMenu_deactivate(in_basename, in_delay)
{
	if (!in_delay) {
		in_delay = 0;
	}

	domMenu_changeActivePath(false, domMenu_activeElement.get(in_basename), in_delay);
}

// }}}
// {{{ domMenu_openEvent()

/**
 * Handle the mouse event to open a menu
 *
 * When an event is received to open the menu, this function is
 * called, handles reinitialization of the menu state and sets
 * a timeout interval for opening the submenu (if one exists)
 */
function domMenu_openEvent(in_this, in_event, in_delayType)
{
	if (domLib_isGecko) {
		window.getSelection().removeAllRanges();
	}

	// setup the cross-browser event object and target
	var eventObj = domLib_isIE ? event : in_event;

	// ensure the event is from the correct frame
	if (domLib_isIE && in_this.data.get('level') > 1) {
		var targetFrame = domMenu_settings.get(in_this.data.get('basename')).get('subMenuTargetFrame');
		if (targetFrame) {
			eventObj = targetFrame.event;
		}
	}

	var currentTarget = domLib_isIE ? in_this : eventObj.currentTarget;
	var basename = currentTarget.data.get('basename');
	var settings = domMenu_settings.get(basename);

	// if we are moving amoungst DOM children of the same element, just ignore event
	if (eventObj.type != 'mousedown' && domMenu_getElement(eventObj[domMenu_eventFrom], basename) == currentTarget) {
		return;
	}

	// if we click on an open menu, close it
	if (eventObj.type == 'mousedown' && domMenu_activeElement.get(basename)) {
		domMenu_changeActivePath(false, domMenu_activeElement.get(basename), currentTarget.data.get('level') == 1 ? settings.get('closeClickMenuDelay') : settings.get('closeClickSubMenuDelay'));
		return;
	}

	// if this element has children, popup the child menu
	if (currentTarget.data.get('numChildren')) {
		// the top level menus have no delay when moving between them
		// so activate submenu immediately
		if (currentTarget.data.get('level') == 1 && domMenu_activeElement.get(basename)) {
			// ** I place changeActivePath() call here so the hiding of selects does not flicker **
			// THOUGHT: instead I could tell changeActivePath to clear select ownership but not
			// toggle visibility....hmmm....
			domMenu_activateSubMenu(currentTarget);
			// clear the active path and initialize the new one
			domMenu_activeElement.set(basename, domMenu_changeActivePath(currentTarget, domMenu_activeElement.get(basename)));
		}
		else {
			// clear the active path and initialize the new one
			domMenu_activeElement.set(basename, domMenu_changeActivePath(currentTarget, domMenu_activeElement.get(basename)));
			domMenu_timeouts['open'].set(currentTarget.id, domLib_setTimeout(domMenu_openMenuCallback, settings.get(in_delayType), [currentTarget, basename]));
		}
	}
	else {
		// clear the active path and initialize the new one
		domMenu_activeElement.set(basename, domMenu_changeActivePath(currentTarget, domMenu_activeElement.get(basename)));
	}
}

// }}}
// {{{ domMenu_closeEvent()

/**
 * Handle the mouse event to close a menu
 *
 * When an mouseout event is received to close the menu, this function is
 * called, sets a timeout interval for closing the menu.
 */
function domMenu_closeEvent(in_this, in_event)
{
	// setup the cross-browser event object and target
	var eventObj = domLib_isIE ? event : in_event;

	// ensure the event is from the correct frame
	if (domLib_isIE && in_this.data.get('level') > 1) {
		var targetFrame = domMenu_settings.get(in_this.data.get('basename')).get('subMenuTargetFrame');
		if (targetFrame) {
			eventObj = targetFrame.event;
		}
	}

	var currentTarget = domLib_isIE ? in_this : eventObj.currentTarget;
	var basename = currentTarget.data.get('basename');
	var relatedTarget = domMenu_getElement(eventObj[domMenu_eventTo], basename);

	// if the related target is not a menu element then we left the menu system
	// at this point (or cannot discern where we are in the menu)
	if (domMenu_activeElement.get(basename)) {
		if (!relatedTarget) {
			domMenu_changeActivePath(false, domMenu_activeElement.get(basename));
		}
	}
	// we are highlighting the top level, but menu is not yet 'active'
	else {
		if (currentTarget != relatedTarget) {
			domLib_clearTimeout(domMenu_timeouts['open'].get(currentTarget.id));
			domMenu_toggleHighlight(currentTarget, false);
		}
	}
}	

// }}}
// {{{ domMenu_getElement()

function domMenu_getElement(in_object, in_basename)
{
	while (in_object) {
		try {
			if (in_object.id && in_object.id.search(new RegExp('^' + in_basename + '(\\[[0-9]\\])*\\[[1-9]\\]$')) == 0) {
				return in_object;
			}
			else {
				in_object = in_object.parentNode;
			}
		}
		catch(e) {
			return false;
		}
	}
	
	return false;
}

// }}}
// {{{ domMenu_correctEdgeBleed()

function domMenu_correctEdgeBleed(in_width, in_height, in_x, in_y, in_padding, in_axis)
{
	// Gecko and IE swaps values of clientHeight, clientWidth properties when
	// in standards compliance mode from documentElement to document.body
	var doc = ((domLib_standardsMode && (domLib_isIE || domLib_isGecko)) ? document.documentElement : document.body);

	var pageHeight = domLib_isKHTML ? window.innerHeight : doc.clientHeight;
	var pageYOffset = domLib_isIE ? doc.scrollTop : window.pageYOffset;
	var pageXOffset = domLib_isIE ? doc.scrollLeft : window.pageXOffset;
	
	if (in_axis == 'horizontal') {
		var bleedRight = (in_x - pageXOffset) + in_width - (doc.clientWidth - in_padding);
		var bleedLeft = (in_x - pageXOffset) - in_padding;
		// we are bleeding off the right, move menu to stay on page
		if (bleedRight > 0) {
			in_x -= bleedRight;
		}

		// we are bleeding to the left, move menu over to stay on page
		// we don't want an 'else if' here, because if it doesn't fit we will bleed off the right
		if (bleedLeft < 0) {
			in_x += bleedLeft;
		}
	}
	else {
		var bleedTop = (in_y - pageYOffset) - in_padding;
		var bleedBottom = (in_y - pageYOffset) + in_height - (pageHeight - in_padding);
		// if we are bleeding off the bottom, move menu to stay on page
		if (bleedBottom > 0) {
			in_y -= bleedBottom;
		}

		// if we are bleeding off the top, move menu down
		// we don't want an 'else if' here, because if we just can't fit it, bleed off the bottom
		if (bleedTop < 0) {
			in_y += bleedTop;
		}
	}
	
	return [in_x, in_y];
}

// }}}
// {{{ domMenu_toggleSubMenu()

function domMenu_toggleSubMenu(in_parentElement, in_style)
{
	var subMenu = in_parentElement.data.get('subMenu');
	if (subMenu && subMenu.style.visibility != in_style) {
		var settings = domMenu_settings.get(in_parentElement.data.get('basename'));
		var isFirstLevelSub = in_parentElement.data.get('level') == 1;
		var targetOtherDoc = isFirstLevelSub && settings.get('subMenuTargetFrame');
		var prefix = isFirstLevelSub ? 'menu' : 'subMenu';
		var className = settings.get(prefix + 'ElementClass');
		// :BUG: this is a problem if submenus click to open, then it won't
		// have the right class when you click to close
		if (in_style == 'visible') {
			className += ' ' + settings.get(prefix + 'Element' + (in_style == 'visible' ? 'Active' : 'Hover') + 'Class');
		}

		in_parentElement.firstChild.className = className;
		
		// position our submenu
		if (in_style == 'visible') {
			var tmp_offsets = domLib_getOffsets(in_parentElement);
			if (isFirstLevelSub) {
				tmp_offsets.set('top', tmp_offsets.get('top') + settings.get('verticalSubMenuOffsetY'));
				tmp_offsets.set('bottom', tmp_offsets.get('bottom') + settings.get('verticalSubMenuOffsetY'));
				tmp_offsets.set('left', tmp_offsets.get('left') + settings.get('verticalSubMenuOffsetX'));
				tmp_offsets.set('right', tmp_offsets.get('right') + settings.get('verticalSubMenuOffsetX'));
			}

			// reposition if there was a change in the parent position/size
			if (!in_parentElement.data.get('offsets').compare(tmp_offsets)) {
				in_parentElement.data.set('offsets', tmp_offsets);

				var xCoor, yCoor;
				if (isFirstLevelSub && settings.get('axis') == 'horizontal') {
					xCoor = tmp_offsets.get('left');
					// expand north
					if (settings.get('verticalExpand') == 'north') {
						if (targetOtherDoc) {
							yCoor = subMenu.offsetHeight;
						}
						else {
							yCoor = tmp_offsets.get('top') - subMenu.offsetHeight - settings.get('verticalSubMenuOffsetY');
						}
					}
					// expand south
					else {
						if (targetOtherDoc) {
							yCoor = settings.get('targetDocumentYOrigin');
						}
						else {
							yCoor = tmp_offsets.get('bottom');
						}
					}
				}
				else {
					yCoor = tmp_offsets.get('top') + settings.get('horizontalSubMenuOffsetY');
					// expand east
					if (settings.get('horizontalExpand') == 'east') {
						if (targetOtherDoc) {
							xCoor = settings.get('targetDocumentXOrigin');
						}
						else {
							xCoor = tmp_offsets.get('right') + settings.get('horizontalSubMenuOffsetX');
						}
					}
					// expand west
					else {
						xCoor = tmp_offsets.get('left') - subMenu.offsetWidth - settings.get('horizontalSubMenuOffsetX');
					}

					if (!targetOtherDoc && (domLib_isOpera || domLib_isSafari)) {
						var marginLeft = parseInt(domLib_getComputedStyle(document.body, 'margin-left'));
						xCoor -= marginLeft;
						var marginTop = parseInt(domLib_getComputedStyle(document.body, 'margin-top'));
						yCoor -= marginTop;
					}
				}

				var minWidth = settings.get('subMenuMinWidth');
				var renderedWidth = subMenu.offsetWidth;
				if (minWidth == 'inherit') {
					minWidth = in_parentElement.offsetWidth + settings.get('subMenuWidthCorrection');
				}
				else if (minWidth == 'auto') {
					minWidth = renderedWidth;
				}

				if (domLib_isKonq) {
					// change with width of the first cell
					subMenu.firstChild.firstChild.firstChild.firstChild.style.width = Math.max(minWidth, renderedWidth) + 'px';
				}
				else {
					// change the width of the table
					subMenu.firstChild.style.width = Math.max(minWidth, renderedWidth) + 'px';
				}
				
				var coordinates = domMenu_correctEdgeBleed(subMenu.offsetWidth, subMenu.offsetHeight, xCoor, yCoor, settings.get('screenPadding'), settings.get('axis'));
				subMenu.style.left = coordinates[0] + 'px';
				subMenu.style.top = coordinates[1] + 'px';

				// ** if we inherit, it is necessary to check the parent element width again **
				if (settings.get('axis') == 'horizontal' && settings.get('subMenuMinWidth') == 'inherit') {
					subMenu.firstChild.style.width = Math.max(in_parentElement.offsetWidth + settings.get('subMenuWidthCorrection'), renderedWidth) + 'px';
				}
			}
		}

		// force konqueror to change the styles
		if (domLib_isKonq) {
			in_parentElement.firstChild.style.display = 'none';
			in_parentElement.firstChild.style.display = '';
		}

		subMenu.style.visibility = in_style;
		if (domLib_detectObstructionsEnabled) {
			domLib_detectObstructions(subMenu, (in_style == 'hidden'), true);
		}
	}
}

// }}}
// {{{ domMenu_toggleHighlight()

function domMenu_toggleHighlight(in_element, in_status)
{
	// if this is a heading, don't change the style
	if (!in_element.data.get('numChildren') && !in_element.data.get('uri')) {
		return;
	}

	var settings = domMenu_settings.get(in_element.data.get('basename'));
	var prefix = in_element.data.get('level') == 1 ? 'menu' : 'subMenu';
	var className = settings.get(prefix + 'ElementClass');
	var highlightElement = in_element.firstChild;

	var pseudoClass;
	if (in_status) {
		if (in_element.data.has('subMenu') && in_element.data.get('subMenu').style.visibility == 'visible') {
			pseudoClass = 'Active';
		}
		else if (in_element.data.get('numChildren') || in_element.data.get('uri')) {
			pseudoClass = 'Hover';
		}
	}

	if (pseudoClass) {
		className += ' ' + settings.get(prefix + 'Element' + pseudoClass + 'Class');
		// if we are changing to hover, change the alt contents (only change if needs it)
		if (highlightElement.childNodes.length == 2) {
			//alert(highlightElement.lastChild);
		}
		if (highlightElement.childNodes.length == 2 && highlightElement.lastChild.style.display == 'none') {
			highlightElement.firstChild.style.display = 'none';
			highlightElement.lastChild.style.display = '';
		}
	}
	else {
		// if we are changing to non-hover, change the alt contents (only change if needs it)
		if (highlightElement.childNodes.length == 2 && highlightElement.firstChild.style.display == 'none') {
			highlightElement.lastChild.style.display = 'none';
			highlightElement.firstChild.style.display = '';
		}
	}

	highlightElement.className = className;

	// force konqueror to change the styles
	if (domLib_isKonq) {
		highlightElement.style.display = 'none';
		highlightElement.style.display = '';
	}
}

// }}}
// {{{ domMenu_resolveLink()

function domMenu_resolveLink(in_this, in_event)
{
	var eventObj = domLib_isIE ? event : in_event;
	var currentTarget = domLib_isIE ? in_this : eventObj.currentTarget;
	var basename = currentTarget.data.get('basename');

	// close the menu system immediately when we resolve the uri
	domMenu_changeActivePath(false, domMenu_activeElement.get(basename), 0);

	var uri = currentTarget.data.get('uri');
	if (uri) {
		window.status = 'Resolving Link...';
		// if a baseUri is specified and the link begins with '/', prepend baseUri
		if (uri.charAt(0) == '/' && domMenu_settings.get(basename).get('baseUri').length > 0) {
			uri = domMenu_settings.get(basename).get('baseUri') + uri;
		}

		if (uri.indexOf('javascript: ') == 0) {
			eval(uri.substring(12));
		}
		// open in current window
		else if (!currentTarget.data.get('target') || currentTarget.data.get('target') == '_self') {
			window.location = uri;
		}
		// open in new window
		else {
			window.open(uri, currentTarget.data.get('target'));
		}
	}
}

// }}}
// {{{ domMenu_unloadEventCache()

// We try and get rid of all circular references by avoiding the use of inner functions
// However, some are still left, so we run this function for IE
// @see http://groups.google.com/groups?hl=en&lr=&ie=UTF-8&oe=UTF-8&selm=bcslfd%24ahl%241%248300dec7%40news.demon.co.uk
function domMenu_unloadEventCache()
{
	var clearElementProps = ['data', 'onmouseover', 'onmouseout', 'onmousedown', 
		'onmouseup', 'ondblclick', 'onclick', 'onselectstart', 'oncontextmenu'];
	var el;
	for (var d = document.all.length; d--;) {
		el = document.all[d];
		for (var c = clearElementProps.length; c--;) {
			el[clearElementProps[c]] = null;
		}
	}
}

// }}}
// {{{ event handling methods

// Functions which are attached to events instead of using inner functions to
// avoid memory leaks.
function domMenu_openMenuOnmouseoverHandler(in_event) {
	domMenu_openEvent(this, in_event, 'openMouseoverMenuDelay');
}

function domMenu_openMenuOnmousedownHandler(in_event) {
	domMenu_openEvent(this, in_event, 'openMousedownMenuDelay');
}

function domMenu_openSubMenuOnmouseoverHandler(in_event) {
	domMenu_openEvent(this, in_event, 'openMouseoverSubMenuDelay');
}

function domMenu_openSubMenuOnclickHandler(in_event) {
	domMenu_openEvent(this, in_event, 'openClickSubMenuDelay');
}

function domMenu_resolveLinkHandler(in_event) {
	domMenu_resolveLink(this, in_event);
}

function domMenu_closeMenuHandler(in_event) {
	domMenu_closeEvent(this, in_event);
}

// }}}
// {{{ callback methods

function domMenu_closeMenuCallback(argv) 
{
	domMenu_toggleHighlight(argv[0], false); 
	domMenu_toggleSubMenu(argv[0], 'hidden');
	// if this is the top level, then the menu is being deactivated
	if (argv[0].data.get('level') == 1) {
		domMenu_activeElement.set(argv[1], false);
	}
}

function domMenu_openMenuCallback(argv)
{
	if (!domMenu_activeElement.get(argv[1])) { 
		domMenu_activeElement.set(argv[1], argv[0]); 
	} 

	domMenu_activateSubMenu(argv[0]);
}

// }}}
