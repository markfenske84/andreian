( function ( $ ) {
	'use strict';

	function syncValue( $control ) {
		const rows = [];

		$control.find( '.andreian-social-links-control__item' ).each( function () {
			const $item = $( this );
			const platform = $item.find( '.andreian-social-links-control__platform' ).val() || '';
			const url = $item.find( '.andreian-social-links-control__url' ).val() || '';

			if ( ! platform && ! url ) {
				return;
			}

			rows.push( { platform: platform, url: url } );
		} );

		$control
			.find( '.andreian-social-links-control__value' )
			.val( JSON.stringify( rows ) )
			.trigger( 'change' );
	}

	function initControl() {
		const $control = $( this );

		if ( $control.data( 'andreianSocialLinksInit' ) ) {
			return;
		}

		$control.data( 'andreianSocialLinksInit', true );

		const $list = $control.find( '.andreian-social-links-control__list' );

		$list.sortable( {
			handle: '.andreian-social-links-control__handle',
			update: function () {
				syncValue( $control );
			},
		} );

		$control.on( 'click', '.andreian-social-links-control__add', function ( event ) {
			event.preventDefault();
			const markup = $control.find( '.andreian-social-links-control__row-template' ).html();
			$list.append( markup );
		} );

		$control.on( 'click', '.andreian-social-links-control__remove', function ( event ) {
			event.preventDefault();
			$( this ).closest( '.andreian-social-links-control__item' ).remove();
			syncValue( $control );
		} );

		$control.on(
			'change keyup input',
			'.andreian-social-links-control__platform, .andreian-social-links-control__url',
			function () {
				syncValue( $control );
			}
		);
	}

	$( document ).on( 'ready', function () {
		$( '.andreian-social-links-control' ).each( initControl );
	} );

	if ( window.wp && wp.customize ) {
		wp.customize.bind( 'ready', function () {
			$( '.andreian-social-links-control' ).each( initControl );
		} );
	}
} )( jQuery );
