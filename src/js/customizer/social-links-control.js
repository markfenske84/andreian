( function ( $ ) {
	'use strict';

	/**
	 * Serialize all rows in a control back to the hidden input as JSON, and
	 * notify the Customizer of the change.
	 */
	function syncValue( $control ) {
		const rows = [];
		$control.find( '.chw-social-links-control__item' ).each( function () {
			const $item = $( this );
			const platform = $item.find( '.chw-social-links-control__platform' ).val() || '';
			const url = $item.find( '.chw-social-links-control__url' ).val() || '';
			if ( ! platform && ! url ) {
				return;
			}
			rows.push( { platform: platform, url: url } );
		} );

		$control
			.find( '.chw-social-links-control__value' )
			.val( JSON.stringify( rows ) )
			.trigger( 'change' );
	}

	function initControl() {
		const $control = $( this );

		if ( $control.data( 'chwSocialLinksInit' ) ) {
			return;
		}
		$control.data( 'chwSocialLinksInit', true );

		const $list = $control.find( '.chw-social-links-control__list' );

		// Reorder via drag.
		$list.sortable( {
			handle: '.chw-social-links-control__handle',
			update: function () {
				syncValue( $control );
			},
		} );

		// Add a new row from the template.
		$control.on( 'click', '.chw-social-links-control__add', function ( e ) {
			e.preventDefault();
			const markup = $control.find( '.chw-social-links-control__row-template' ).html();
			$list.append( markup );
		} );

		// Remove a row.
		$control.on( 'click', '.chw-social-links-control__remove', function ( e ) {
			e.preventDefault();
			$( this ).closest( '.chw-social-links-control__item' ).remove();
			syncValue( $control );
		} );

		// Any change to a platform or URL updates the stored value.
		$control.on(
			'change keyup input',
			'.chw-social-links-control__platform, .chw-social-links-control__url',
			function () {
				syncValue( $control );
			}
		);
	}

	$( document ).on( 'ready', function () {
		$( '.chw-social-links-control' ).each( initControl );
	} );

	if ( window.wp && wp.customize ) {
		wp.customize.bind( 'ready', function () {
			$( '.chw-social-links-control' ).each( initControl );
		} );
	}
} )( jQuery );
