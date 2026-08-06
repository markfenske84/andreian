( function ( $ ) {
	'use strict';

	/**
	 * Read the current attachment IDs from a control's hidden input.
	 */
	function readIds( $input ) {
		const raw = $input.val();
		if ( ! raw ) {
			return [];
		}
		try {
			const parsed = JSON.parse( raw );
			return Array.isArray( parsed ) ? parsed.map( Number ).filter( Boolean ) : [];
		} catch ( e ) {
			return [];
		}
	}

	/**
	 * Persist IDs back to the hidden input and notify the Customizer of the change.
	 */
	function writeIds( $input, ids ) {
		$input.val( JSON.stringify( ids ) ).trigger( 'change' );
	}

	/**
	 * Render the thumbnail list for a control from an ordered list of IDs.
	 */
	function renderList( $control, ids ) {
		const $list = $control.find( '.chw-multi-image-control__list' );
		$list.empty();

		ids.forEach( function ( id ) {
			const attachment = wp.media.attachment( id );

			const $item = $(
				'<li class="chw-multi-image-control__item" data-id="' +
					id +
					'"><img alt="" /><button type="button" class="chw-multi-image-control__remove" aria-label="Remove image">&times;</button></li>'
			);
			$list.append( $item );

			// Fetch the thumbnail URL (cached after first load).
			attachment.fetch().done( function () {
				const sizes = attachment.attributes.sizes || {};
				const url =
					( sizes.thumbnail && sizes.thumbnail.url ) ||
					( sizes.medium && sizes.medium.url ) ||
					attachment.attributes.url;
				$item.find( 'img' ).attr( 'src', url );
			} );
		} );
	}

	function initControl() {
		const $control = $( this );

		if ( $control.data( 'chwMultiImageInit' ) ) {
			return;
		}
		$control.data( 'chwMultiImageInit', true );

		const $input = $control.find( '.chw-multi-image-control__value' );
		let frame;

		// Make the thumbnails sortable so order can be set.
		$control.find( '.chw-multi-image-control__list' ).sortable( {
			update: function () {
				const ids = [];
				$control.find( '.chw-multi-image-control__item' ).each( function () {
					ids.push( Number( $( this ).data( 'id' ) ) );
				} );
				writeIds( $input, ids );
			},
		} );

		// Open the media frame to add / edit the selection.
		$control.on( 'click', '.chw-multi-image-control__add', function ( e ) {
			e.preventDefault();

			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media( {
				title: 'Select Images',
				button: { text: 'Use Images' },
				library: { type: 'image' },
				multiple: true,
			} );

			frame.on( 'open', function () {
				const selection = frame.state().get( 'selection' );
				readIds( $input ).forEach( function ( id ) {
					const attachment = wp.media.attachment( id );
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				} );
			} );

			frame.on( 'select', function () {
				const ids = frame
					.state()
					.get( 'selection' )
					.map( function ( attachment ) {
						return attachment.id;
					} );
				writeIds( $input, ids );
				renderList( $control, ids );
			} );

			frame.open();
		} );

		// Remove a single image.
		$control.on( 'click', '.chw-multi-image-control__remove', function ( e ) {
			e.preventDefault();
			const removeId = Number( $( this ).closest( '.chw-multi-image-control__item' ).data( 'id' ) );
			const ids = readIds( $input ).filter( function ( id ) {
				return id !== removeId;
			} );
			writeIds( $input, ids );
			renderList( $control, ids );
		} );
	}

	$( document ).on( 'ready', function () {
		$( '.chw-multi-image-control' ).each( initControl );
	} );

	// Re-init when the control is rendered inside its section (Customizer lazy-renders).
	if ( window.wp && wp.customize ) {
		wp.customize.bind( 'ready', function () {
			$( '.chw-multi-image-control' ).each( initControl );
		} );
	}
} )( jQuery );
