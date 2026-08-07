document.querySelectorAll( '.tabbed-comparison-table' ).forEach( function ( block ) {
	var tablist = block.querySelector( '[role="tablist"]' );
	if ( ! tablist ) {
		return;
	}

	var tabs = tablist.querySelectorAll( '[role="tab"]' );
	var panes = block.querySelectorAll( '[role="tabpanel"]' );

	if ( ! tabs.length || ! panes.length ) {
		return;
	}

	function activateTab( tab ) {
		var paneId = tab.getAttribute( 'aria-controls' );
		var pane = paneId ? block.querySelector( '#' + paneId ) : null;

		tabs.forEach( function ( t ) {
			t.classList.remove( '-active' );
			t.setAttribute( 'aria-selected', 'false' );
			t.setAttribute( 'tabindex', '-1' );
		} );

		panes.forEach( function ( p ) {
			p.classList.remove( '-active' );
			p.setAttribute( 'hidden', '' );
		} );

		tab.classList.add( '-active' );
		tab.setAttribute( 'aria-selected', 'true' );
		tab.setAttribute( 'tabindex', '0' );

		if ( pane ) {
			pane.classList.add( '-active' );
			pane.removeAttribute( 'hidden' );
		}
	}

	function focusTabByIndex( index ) {
		if ( index < 0 || index >= tabs.length ) {
			return;
		}
		var tab = tabs[ index ];
		activateTab( tab );
		tab.focus();
	}

	tabs.forEach( function ( tab, index ) {
		tab.addEventListener( 'click', function () {
			activateTab( tab );
		} );

		tab.addEventListener( 'keydown', function ( event ) {
			var targetIndex = index;

			if ( event.key === 'ArrowRight' ) {
				targetIndex = index + 1 >= tabs.length ? 0 : index + 1;
			} else if ( event.key === 'ArrowLeft' ) {
				targetIndex = index - 1 < 0 ? tabs.length - 1 : index - 1;
			} else if ( event.key === 'Home' ) {
				targetIndex = 0;
			} else if ( event.key === 'End' ) {
				targetIndex = tabs.length - 1;
			} else {
				return;
			}

			event.preventDefault();
			focusTabByIndex( targetIndex );
		} );
	} );
} );
