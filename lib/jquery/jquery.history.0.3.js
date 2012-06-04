/*
**	history for ajax/javascript history
**		0.3 history events now setup in queue to ensure all entries reside in the history stack
**		0.2 no more FORM GET submission, straight location.href instead + hold time for iframe load
**		0.1 hidden frame + not bookmarkable + stores data for state change + allows reinstating data on forw/back hit
**	authored by Jim Palmer - released under MIT license
**  collage of ideas from Taku Sano, Mikage Sawatari, david bloom and Klaus Hartl
*/
(function($) {

	$.history = function ( store ) {

		// (initialize) create the hidden iframe if not on the root window.document.body
		if ( $(".__historyFrame").length == 0 ) {

			// set the history cursor to (-1) - this will be populated with current unix timestamp or 0 for the first screen
			$.history.cursor = $.history.intervalId = 0;
			// initialize the stack of history stored entries
			$.history.stack = {};
			// initialize the stack of loading hold flags
			$.history._loading = {};
			// initialize the queue for loading history fragments in sequence
			$.history._queue = [];

			// append to the root window.document.body without the src - uses class for toggleClass debugging - display:none doesn't work
			$("body").append('<iframe class="__historyFrame" src="/php/static/null.html" style="border:0px; width:0px; height:0px; visibility:hidden;">');

			// set the src (safari doesnt load the src if set in the append above)  + set the onLoad event for the iframe
			$('.__historyFrame').load(function () {
					// parse out the current cursor from the location/URL
					var cursor = $(this).contents().attr( $.browser.msie ? 'URL' : 'location' ).toString().split('#')[1];
					if ( cursor ) {
						// remove the cursor from the load queue
						var qPos = $.inArray( cursor, $.history._queue );
						if ( qPos > -1 )
							$.history._queue.splice( qPos, 1 );
						// flag that the iframe is done loading the new fragment id
						$.history._loading[ cursor ] = false;
					}

					// setup interval function to check for changes in "history" via iframe hash and call appropriate callback function to handle it
					$.history.intervalId = $.history.intervalId || window.setInterval(function () {
							// if any cursors in queue - load first cursor (FIFO)
							if ( $.history._queue.length > 0 && !$.history._loading[ $.history._queue[0] ] ) {
								// flag this queued cursor as loading so this interval will not load more than once
								$.history._loading[ $.history._queue[0] ] = true;
								// move the history cursor in the hidden iframe to the newest fragment identifier
								$('.__historyFrame').contents()[0].location.href =
									$('.__historyFrame').contents().attr( $.browser.msie ? 'URL' : 'location' ).toString().replace(/[\?|#]{1}(.*)$/gi, '') +
									'?' + $.history._queue[0] + '#' + $.history._queue[0];
							} else if ( $.history._queue.length == 0 ) {
								// fetch current cursor from the iframe document.URL or document.location depending on browser support
								var cursor = $(".__historyFrame").contents().attr( $.browser.msie ? 'URL' : 'location' ).toString().split('#')[1];
								// if cursors are different (forw/back hit) then reinstate data only when iframe is done loading
								if ( parseFloat($.history.cursor) >= 0 && parseFloat($.history.cursor) != ( parseFloat(cursor) || 0 ) ) {
									// set the history cursor to the current cursor
									$.history.cursor = parseFloat(cursor) || 0;
									// reinstate the current cursor data through the callback
									if ( typeof($.history.callback) == 'function' )
										$.history.callback( $.history.stack[ cursor ], cursor );
								}
							}
						}, 150);

				});

		} else {			// handle new history entries apre-initialization

			// set the current unix timestamp for our history
			$.history.cursor = (new Date()).getTime().toString();
			// add this cursor fragment id into the queue to be loaded by the checking function interval
			$.history._queue.push( $.history.cursor );
			// insert into the stack with current cursor
			$.history.stack[ $.history.cursor ] = store;

		}
			
	}

	// pre-initialize the history functionality - if you include this plugin this will be loaded as a singleton at time of the root window.onLoad
	$(document).ready( function () { $.history(); } );

})(jQuery);