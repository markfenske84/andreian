( function ( wp ) {
	const { registerPlugin } = wp.plugins;
	const { useSelect } = wp.data;
	const { createElement: el, Fragment, useState, useEffect, createPortal } = wp.element;

	const globals = window.chwMastheadGlobals || {};

	function rawHtml( html, tag, className ) {
		return el( tag || 'span', {
			className: className || undefined,
			dangerouslySetInnerHTML: { __html: html },
		} );
	}

	/**
	 * Build the masthead preview markup from post meta + global content.
	 * Mirrors src/components/inner-masthead.php so the editor matches the front end.
	 */
	function buildMasthead( meta, bg, fallbackTitle ) {
		const buttons = Array.isArray( meta.masthead_buttons ) ? meta.masthead_buttons : [];
		const stats = Array.isArray( meta.masthead_stats ) ? meta.masthead_stats : [];
		const title = meta.custom_page_title || fallbackTitle || '';

		const children = [];

		// Eyelash.
		if ( meta.masthead_eyelash_text || meta.masthead_eyelash_icon ) {
			children.push(
				el(
					'div',
					{ key: 'eyelash', className: 'masthead__eyelash _eyebrow -on-dark' },
					meta.masthead_eyelash_icon
						? rawHtml( meta.masthead_eyelash_icon, 'span', '_eyebrow__icon _text -primary' )
						: null,
					meta.masthead_eyelash_text
						? el( 'span', { className: '_eyebrow__label' }, meta.masthead_eyelash_text )
						: null
				)
			);
		}

		// Headline.
		children.push(
			el( 'h1', {
				key: 'title',
				className: 'masthead__title',
				dangerouslySetInnerHTML: { __html: title },
			} )
		);

		// Description.
		if ( meta.masthead_description ) {
			children.push(
				el( 'p', { key: 'desc', className: 'masthead__description' }, meta.masthead_description )
			);
		}

		// CTA buttons.
		if ( buttons.length ) {
			children.push(
				el(
					'div',
					{ key: 'buttons', className: 'masthead__buttons' },
					buttons.map( function ( button, index ) {
						let cls = '_button ' + ( index === 0 ? '-primary' : '-outline' );
						if ( button.modifier ) {
							cls += ' -' + button.modifier;
						}
						return el(
							'a',
							{ key: 'b' + index, className: cls, href: button.url || '#' },
							button.text || ''
						);
					} )
				)
			);
		}

		// Secondary CTA.
		if ( meta.masthead_secondary_cta_text || meta.masthead_secondary_cta_button_text ) {
			children.push(
				el(
					'div',
					{ key: 'secondary', className: 'masthead__secondary-cta' },
					meta.masthead_secondary_cta_text
						? rawHtml( meta.masthead_secondary_cta_text, 'span', 'masthead__secondary-cta-text' )
						: null,
					meta.masthead_secondary_cta_button_text
						? el(
								'a',
								{
									className: '_button -outline -small',
									href: meta.masthead_secondary_cta_button_url || '#',
								},
								meta.masthead_secondary_cta_button_icon
									? rawHtml( meta.masthead_secondary_cta_button_icon, 'span', 'masthead__button-icon' )
									: null,
								el( 'span', null, meta.masthead_secondary_cta_button_text )
						  )
						: null
				)
			);
		}

		// Stats.
		if ( meta.masthead_show_stats && stats.length ) {
			children.push(
				el(
					'div',
					{ key: 'stats', className: 'masthead__stats' },
					stats.map( function ( stat, index ) {
						return el(
							'div',
							{ key: 's' + index, className: 'masthead__stat' },
							el( 'span', { className: 'masthead__stat-value' }, stat.value || '' ),
							el( 'span', { className: 'masthead__stat-label' }, stat.label || '' )
						);
					} )
				)
			);
		}

		// Legal text (global).
		if ( meta.masthead_show_legal && globals.legalText ) {
			children.push(
				el( 'div', {
					key: 'legal',
					className: 'masthead__legal',
					dangerouslySetInnerHTML: { __html: globals.legalText },
				} )
			);
		}

		const contentCol = el( 'div', { className: 'masthead__content' }, children );

		// Territory rep CTA box.
		let repBox = null;
		if ( meta.masthead_rep_box_enabled ) {
			repBox = el(
				'aside',
				{ className: 'masthead__rep-box' },
				meta.masthead_rep_box_eyebrow
					? el( 'span', { className: 'masthead__rep-box-eyebrow _eyebrow -plain _text -primary' }, meta.masthead_rep_box_eyebrow )
					: null,
				meta.masthead_rep_box_heading
					? el( 'h2', { className: 'masthead__rep-box-heading' }, meta.masthead_rep_box_heading )
					: null,
				meta.masthead_rep_box_text
					? el( 'p', { className: 'masthead__rep-box-text' }, meta.masthead_rep_box_text )
					: null,
				meta.masthead_rep_box_button_text
					? el(
							'a',
							{ className: '_button -primary', href: meta.masthead_rep_box_button_url || '#' },
							meta.masthead_rep_box_button_text
					  )
					: null,
				meta.masthead_rep_box_phone
					? el(
							'a',
							{ className: '_button -outline -secondary', href: 'tel:' + meta.masthead_rep_box_phone },
							meta.masthead_rep_box_phone
					  )
					: null
			);
		}

		// Award seal (global content, per-page toggle).
		let award = null;
		if ( meta.masthead_show_award_seal && globals.award && ( globals.award.title || globals.award.image ) ) {
			award = el(
				'div',
				{ className: 'masthead__award' },
				globals.award.image
					? el( 'img', { className: 'masthead__award-img', src: globals.award.image, alt: '' } )
					: null,
				el(
					'div',
					{ className: 'masthead__award-text' },
					globals.award.eyebrow
						? el( 'span', { className: 'masthead__award-eyebrow _eyebrow -plain _text -muted' }, globals.award.eyebrow )
						: null,
					globals.award.title
						? el( 'strong', { className: 'masthead__award-title' }, globals.award.title )
						: null,
					globals.award.source
						? el( 'span', { className: 'masthead__award-source' }, globals.award.source )
						: null
				)
			);
		}

		// As Seen In (global content, per-page toggle).
		let asSeenIn = null;
		if ( meta.masthead_show_as_seen_in && globals.asSeenInLogos && globals.asSeenInLogos.length ) {
			asSeenIn = el(
				'div',
				{ className: 'masthead__as-seen-in' },
				globals.asSeenInLabel
					? el( 'span', { className: 'masthead__as-seen-in-label' }, globals.asSeenInLabel )
					: null,
				el(
					'div',
					{
						className: 'masthead__as-seen-in-logos',
						'data-count': globals.asSeenInLogos.length,
					},
					globals.asSeenInLogos.map( function ( logo, index ) {
						return el( 'img', { key: 'l' + index, src: logo.url, alt: logo.alt || '' } );
					} )
				)
			);
		}

		const footer = ( award || asSeenIn )
			? el( 'div', { className: 'masthead__footer _container' }, award, asSeenIn )
			: null;

		return el(
			'section',
			{
				className: 'masthead -editable -has-image',
				style: bg ? { backgroundImage: "url('" + bg + "')" } : undefined,
			},
			el( 'div', { className: 'masthead__overlay' } ),
			el(
				'div',
				{ className: '-inner _container' },
				contentCol,
				repBox
			),
			footer
		);
	}

	const MastheadPreview = function () {
		const [ target, setTarget ] = useState( null );

		const data = useSelect( function ( select ) {
			const editor = select( 'core/editor' );
			const postType = editor.getCurrentPostType();
			const meta = editor.getEditedPostAttribute( 'meta' ) || {};
			const title = editor.getEditedPostAttribute( 'title' ) || '';
			const featuredId = editor.getEditedPostAttribute( 'featured_media' );
			const media = featuredId ? select( 'core' ).getMedia( featuredId ) : null;

			return {
				postType: postType,
				meta: meta,
				title: title,
				bg: media && media.source_url ? media.source_url : globals.defaultBackground,
			};
		}, [] );

		// Locate the editor canvas (iframe-aware) and inject a mount point above the content.
		// Polls on an interval so it recovers if the canvas iframe reloads (e.g. toggling
		// the code editor) without busy-looping every animation frame.
		useEffect( function () {
			function locate() {
				const iframe = document.querySelector( 'iframe[name="editor-canvas"]' );
				const doc = iframe && iframe.contentDocument ? iframe.contentDocument : document;
				const root =
					doc.querySelector( '.is-root-container' ) ||
					doc.querySelector( '.block-editor-block-list__layout' );

				if ( ! root || ! root.parentNode ) {
					return;
				}

				let container = doc.getElementById( 'chw-masthead-preview-root' );
				if ( ! container || ! container.isConnected ) {
					container = doc.createElement( 'div' );
					container.id = 'chw-masthead-preview-root';
					container.className = 'chw-masthead-preview-root';
					root.parentNode.insertBefore( container, root );
				}

				setTarget( function ( prev ) {
					return prev === container ? prev : container;
				} );
			}

			locate();
			const interval = window.setInterval( locate, 750 );

			return function () {
				window.clearInterval( interval );
			};
		}, [] );

		if ( ! target || ( data.postType !== 'page' && data.postType !== 'post' ) ) {
			return null;
		}

		if ( data.postType === 'page' && data.meta.masthead_disabled ) {
			return createPortal( null, target );
		}

		return createPortal(
			buildMasthead( data.meta, data.bg, data.title ),
			target
		);
	};

	registerPlugin( 'chw-masthead-preview', {
		render: MastheadPreview,
	} );
} )( window.wp );
