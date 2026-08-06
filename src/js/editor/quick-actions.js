( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls, useBlockProps } = wp.blockEditor;
	const {
		PanelBody,
		ToggleControl,
		TextControl,
		TextareaControl,
		SelectControl,
		BaseControl,
		Button,
	} = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const ARROW_DIRECTION_OPTIONS = [
		{ label: __( 'Right (new page on this site)', 'chw' ), value: 'right' },
		{ label: __( 'Up-right (external)', 'chw' ), value: 'up-right' },
		{ label: __( 'Down (anchor on this page)', 'chw' ), value: 'down' },
	];

	const ALLOWED_ARROWS = ARROW_DIRECTION_OPTIONS.map( function ( option ) {
		return option.value;
	} );

	const ARROW_ICONS = {
		right:
			'<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>',
		'up-right':
			'<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M7 17L17 7M17 7H9M17 7v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>',
		down: '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>',
	};

	function uid( prefix ) {
		return prefix + '-' + Math.random().toString( 36 ).slice( 2, 9 );
	}

	function updateRow( rows, index, patch ) {
		return rows.map( function ( row, i ) {
			return i === index ? Object.assign( {}, row, patch ) : row;
		} );
	}

	function removeRow( rows, index ) {
		return rows.filter( function ( row, i ) {
			return i !== index;
		} );
	}

	function moveRow( rows, index, dir ) {
		const target = index + dir;
		if ( target < 0 || target >= rows.length ) {
			return rows;
		}
		const next = rows.slice();
		const tmp = next[ index ];
		next[ index ] = next[ target ];
		next[ target ] = tmp;
		return next;
	}

	function safeArrow( value ) {
		return ALLOWED_ARROWS.indexOf( value ) !== -1 ? value : 'right';
	}

	function renderArrowIcon( direction ) {
		const safe = safeArrow( direction );
		return el( 'span', {
			className: 'quick-actions__arrow -' + safe,
			'aria-hidden': true,
			dangerouslySetInnerHTML: { __html: ARROW_ICONS[ safe ] },
		} );
	}

	registerBlockType( 'chw/quick-actions', {
		title: __( 'Quick Actions', 'chw' ),
		description: __(
			'An orange eyelash header with horizontal action-link cards, four per row.',
			'chw'
		),
		category: 'choice-home-warranty',
		icon: 'admin-links',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			eyelashText: { type: 'string', default: '' },
			links: { type: 'array', default: [] },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const links = Array.isArray( attributes.links ) ? attributes.links : [];
			const blockProps = useBlockProps( { className: 'quick-actions' } );

			function setLinks( rows ) {
				setAttributes( { links: rows } );
			}

			const linkRows = links.map( function ( link, index ) {
				return el(
					'div',
					{ key: link.id || 'link-' + index, className: 'chw-masthead-repeater-row' },
					el(
						'div',
						{ className: 'chw-masthead-repeater-row__head' },
						el( 'span', null, __( 'Link', 'chw' ) + ' ' + ( index + 1 ) ),
						el(
							'div',
							{ className: 'chw-masthead-repeater-row__actions' },
							el( Button, {
								icon: 'arrow-up-alt2',
								label: __( 'Move up', 'chw' ),
								disabled: index === 0,
								onClick: function () {
									setLinks( moveRow( links, index, -1 ) );
								},
							} ),
							el( Button, {
								icon: 'arrow-down-alt2',
								label: __( 'Move down', 'chw' ),
								disabled: index === links.length - 1,
								onClick: function () {
									setLinks( moveRow( links, index, 1 ) );
								},
							} ),
							el( Button, {
								icon: 'trash',
								isDestructive: true,
								label: __( 'Remove link', 'chw' ),
								onClick: function () {
									setLinks( removeRow( links, index ) );
								},
							} )
						)
					),
					el( TextControl, {
						label: __( 'Label', 'chw' ),
						value: link.label || '',
						onChange: function ( value ) {
							setLinks( updateRow( links, index, { label: value } ) );
						},
					} ),
					el( TextareaControl, {
						label: __( 'Icon (paste SVG)', 'chw' ),
						value: link.icon || '',
						rows: 2,
						onChange: function ( value ) {
							setLinks( updateRow( links, index, { icon: value } ) );
						},
					} ),
					el( TextControl, {
						label: __( 'URL', 'chw' ),
						value: link.url || '',
						onChange: function ( value ) {
							setLinks( updateRow( links, index, { url: value } ) );
						},
					} ),
					el( ToggleControl, {
						label: __( 'Open in new tab', 'chw' ),
						checked: !! link.new_tab,
						onChange: function ( value ) {
							setLinks( updateRow( links, index, { new_tab: value } ) );
						},
					} ),
					el( SelectControl, {
						label: __( 'Arrow direction', 'chw' ),
						value: safeArrow( link.arrow_direction || 'right' ),
						options: ARROW_DIRECTION_OPTIONS,
						onChange: function ( value ) {
							setLinks( updateRow( links, index, { arrow_direction: value } ) );
						},
					} )
				);
			} );

			const visibleLinks = links.filter( function ( link ) {
				return !! link.label || !! link.url;
			} );

			const cardPreview = visibleLinks.map( function ( link, index ) {
				const arrowDir = safeArrow( link.arrow_direction || 'right' );

				return el(
					'a',
					{
						key: link.id || 'link-preview-' + index,
						className: 'quick-actions__card',
						href: link.url || '#',
						contentEditable: false,
						onClick: function ( event ) {
							event.preventDefault();
						},
					},
					link.icon
						? el( 'span', {
								className: 'quick-actions__icon',
								'aria-hidden': true,
								dangerouslySetInnerHTML: { __html: link.icon },
						  } )
						: null,
					link.label
						? el( 'span', { className: 'quick-actions__label' }, link.label )
						: null,
					renderArrowIcon( arrowDir )
				);
			} );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Links', 'chw' ), initialOpen: true },
						el( BaseControl, null, linkRows ),
						el(
							Button,
							{
								variant: 'secondary',
								className: 'chw-editor-add-button',
								onClick: function () {
									setLinks(
										links.concat( [
											{
												id: uid( 'link' ),
												label: '',
												icon: '',
												url: '',
												new_tab: false,
												arrow_direction: 'right',
											},
										] )
									);
								},
							},
							__( 'Add link', 'chw' )
						)
					)
				),
				el(
					'section',
					blockProps,
					el(
						'div',
						{ className: '_container' },
						el( RichText, {
							tagName: 'span',
							className: '_eyebrow -plain quick-actions__eyebrow',
							value: attributes.eyelashText || '',
							onChange: function ( value ) {
								setAttributes( { eyelashText: value } );
							},
							placeholder: __( 'Eyelash text…', 'chw' ),
							allowedFormats: [],
						} ),
						visibleLinks.length
							? el(
									'div',
									{
										className: 'quick-actions__grid',
										'data-count': visibleLinks.length,
										contentEditable: false,
									},
									cardPreview
							  )
							: el(
									'p',
									{ style: { margin: '1rem 0 0', opacity: 0.7 } },
									__( 'Add a link in the sidebar to get started.', 'chw' )
							  )
					)
				)
			);
		},

		save: function () {
			return null;
		},
	} );
} )( window.wp );
