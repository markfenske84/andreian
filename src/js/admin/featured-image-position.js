( function () {
	const DEFAULT_X = 0.5;
	const DEFAULT_Y = 0;

	function clamp( value ) {
		return Math.min( 1, Math.max( 0, value ) );
	}

	function initPicker( root ) {
		if ( ! root || root.dataset.andreianFocalReady === '1' ) {
			return;
		}

		const picker = root.querySelector( '[data-andreian-focal-picker]' );
		const handle = root.querySelector( '.andreian-focal-point__handle' );
		const inputX = root.querySelector( 'input[name="andreian_featured_focal_x"]' );
		const inputY = root.querySelector( 'input[name="andreian_featured_focal_y"]' );
		const reset = root.querySelector( '.andreian-focal-point__reset' );

		if ( ! picker || ! handle || ! inputX || ! inputY ) {
			return;
		}

		root.dataset.andreianFocalReady = '1';

		function setFocal( x, y ) {
			const nextX = clamp( x );
			const nextY = clamp( y );

			inputX.value = String( nextX );
			inputY.value = String( nextY );
			handle.style.left = nextX * 100 + '%';
			handle.style.top = nextY * 100 + '%';
		}

		function fromEvent( event ) {
			const rect = picker.getBoundingClientRect();

			if ( ! rect.width || ! rect.height ) {
				return;
			}

			setFocal(
				( event.clientX - rect.left ) / rect.width,
				( event.clientY - rect.top ) / rect.height
			);
		}

		function onPointerMove( event ) {
			fromEvent( event );
		}

		function onPointerUp( event ) {
			picker.releasePointerCapture( event.pointerId );
			picker.removeEventListener( 'pointermove', onPointerMove );
			picker.removeEventListener( 'pointerup', onPointerUp );
			picker.removeEventListener( 'pointercancel', onPointerUp );
		}

		picker.addEventListener( 'pointerdown', function ( event ) {
			if ( event.button !== 0 ) {
				return;
			}

			event.preventDefault();
			fromEvent( event );
			picker.setPointerCapture( event.pointerId );
			picker.addEventListener( 'pointermove', onPointerMove );
			picker.addEventListener( 'pointerup', onPointerUp );
			picker.addEventListener( 'pointercancel', onPointerUp );
		} );

		if ( reset ) {
			reset.addEventListener( 'click', function () {
				setFocal( DEFAULT_X, DEFAULT_Y );
			} );
		}
	}

	function initAll() {
		document.querySelectorAll( '.andreian-focal-point' ).forEach( initPicker );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}

	if ( window.jQuery ) {
		window.jQuery( document ).on( 'ajaxComplete', function ( _event, _xhr, settings ) {
			const data = settings && settings.data ? String( settings.data ) : '';

			if ( data.indexOf( 'action=set-post-thumbnail' ) !== -1 ) {
				initAll();
			}
		} );
	}
} )();
