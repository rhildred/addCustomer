// Copyright (c) 2010 TrendMedia Technologies, Inc., Brian McNitt.
// All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************

var InfiniteRotator = function(sClassName, itemInterval) {
	//initial fade-in time (in milliseconds)
	var initialFadeIn = 1000;

	//cross-fade time (in milliseconds)
	var fadeTime = 2500;

	//count number of items
	var numberOfItems = $('.' + sClassName).length;

	//set current item
	var currentItem = 0;

	//loop through the items
	var infiniteLoop = setInterval(function() {
		// .stop true makes it not save up events
		$('.' + sClassName).eq(currentItem).stop(true, true).fadeOut(fadeTime);

		if (currentItem == numberOfItems - 1) {
			currentItem = 0;
		} else {
			currentItem++;
		}
		// find out if there is an anchor tag in the current item, and if so swap it for the img tag
		var dCur = $('.' + sClassName).eq(currentItem);
		var aCur = dCur.find('a');
		if (aCur) {
			var img = $('<img id="dynamic">');
			//Equivalent: $(document.createElement('img'))
			img.attr('src', aCur.attr('href'));
			img.attr('class', 'leftSwapPic');
			img.appendTo(dCur);
			aCur.remove();
		}
		$('.' + sClassName).eq(currentItem).fadeIn(fadeTime);

	}, itemInterval);

}