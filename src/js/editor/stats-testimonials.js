( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } = wp.blockEditor;
	const { PanelBody, BaseControl, Button } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const blockData = window.chwStatsTestimonialsData || {};

	const MAX_STATS = 4;
	const MAX_TESTIMONIALS = 3;

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

	function rowActions( rows, index, onChange, className, removeLabel ) {
		return el(
			'div',
			{ className: className, contentEditable: false },
			el( Button, {
				icon: 'arrow-left-alt2',
				label: __( 'Move left', 'chw' ),
				disabled: index === 0,
				onClick: function () {
					onChange( moveRow( rows, index, -1 ) );
				},
			} ),
			el( Button, {
				icon: 'arrow-right-alt2',
				label: __( 'Move right', 'chw' ),
				disabled: index === rows.length - 1,
				onClick: function () {
					onChange( moveRow( rows, index, 1 ) );
				},
			} ),
			el( Button, {
				icon: 'trash',
				isDestructive: true,
				label: removeLabel,
				onClick: function () {
					onChange( removeRow( rows, index ) );
				},
			} )
		);
	}

	function starPreview() {
		const stars = [];
		for ( let i = 0; i < 5; i++ ) {
			stars.push(
				el(
					'svg',
					{
						key: 'star-' + i,
						className: 'stats-testimonials__star',
						viewBox: '0 0 24 24',
						fill: 'currentColor',
						'aria-hidden': true,
						focusable: false,
					},
					el( 'path', {
						d: 'M12 2l2.95 5.98 6.6.96-4.77 4.65 1.13 6.57L12 17.02l-5.91 3.1 1.13-6.57L2.45 8.94l6.6-.96L12 2z',
					} )
				)
			);
		}
		return el(
			'div',
			{ className: 'stats-testimonials__stars', contentEditable: false },
			stars
		);
	}

	registerBlockType( 'chw/stats-testimonials', {
		title: __( 'Stats and Testimonials', 'chw' ),
		description: __( 'A full-width section over a grayscale background image with a stats card and testimonial cards.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'awards',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			backgroundImageId: { type: 'number', default: 0 },
			backgroundImageUrl: { type: 'string', default: '' },
			stats: { type: 'array', default: [] },
			testimonials: { type: 'array', default: [] },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const stats = Array.isArray( attributes.stats ) ? attributes.stats : [];
			const testimonials = Array.isArray( attributes.testimonials ) ? attributes.testimonials : [];
			const backgroundUrl = attributes.backgroundImageUrl || blockData.defaultBackground || '';

			const blockProps = useBlockProps( { className: 'stats-testimonials' } );

			function setStats( rows ) {
				setAttributes( { stats: rows } );
			}

			function setTestimonials( rows ) {
				setAttributes( { testimonials: rows } );
			}

			// --- Stats items -------------------------------------------------
			const statItems = stats.map( function ( stat, index ) {
				return el(
					'div',
					{ key: stat.id || 'stat-' + index, className: 'stats-testimonials__stat' },
					el( 'span', { className: 'stats-testimonials__stat-bar', 'aria-hidden': true } ),
					el(
						'div',
						{ className: 'stats-testimonials__stat-body' },
						el( RichText, {
							tagName: 'div',
							className: 'stats-testimonials__stat-value',
							value: stat.value || '',
							allowedFormats: [],
							placeholder: __( '2.4M+', 'chw' ),
							onChange: function ( value ) {
								setStats( updateRow( stats, index, { value: value } ) );
							},
						} ),
						el( RichText, {
							tagName: 'div',
							className: 'stats-testimonials__stat-label',
							value: stat.label || '',
							allowedFormats: [],
							placeholder: __( 'Homes covered', 'chw' ),
							onChange: function ( value ) {
								setStats( updateRow( stats, index, { label: value } ) );
							},
						} ),
						rowActions( stats, index, setStats, 'stats-testimonials__stat-actions', __( 'Remove stat', 'chw' ) )
					)
				);
			} );

			// --- Testimonial items ------------------------------------------
			const testimonialItems = testimonials.map( function ( testimonial, index ) {
				return el(
					'figure',
					{ key: testimonial.id || 'testimonial-' + index, className: 'stats-testimonials__card' },
					el( RichText, {
						tagName: 'blockquote',
						className: 'stats-testimonials__quote',
						value: testimonial.quote || '',
						allowedFormats: [ 'core/bold', 'core/italic' ],
						placeholder: __( 'Share a customer quote.', 'chw' ),
						onChange: function ( value ) {
							setTestimonials( updateRow( testimonials, index, { quote: value } ) );
						},
					} ),
					el(
						'figcaption',
						{ className: 'stats-testimonials__card-foot' },
						el(
							'div',
							{ className: 'stats-testimonials__author' },
							el( RichText, {
								tagName: 'div',
								className: 'stats-testimonials__author-name',
								value: testimonial.name || '',
								allowedFormats: [],
								placeholder: __( 'Name', 'chw' ),
								onChange: function ( value ) {
									setTestimonials( updateRow( testimonials, index, { name: value } ) );
								},
							} ),
							el( RichText, {
								tagName: 'div',
								className: 'stats-testimonials__author-location',
								value: testimonial.location || '',
								allowedFormats: [],
								placeholder: __( 'Location', 'chw' ),
								onChange: function ( value ) {
									setTestimonials( updateRow( testimonials, index, { location: value } ) );
								},
							} )
						),
						starPreview()
					),
					rowActions( testimonials, index, setTestimonials, 'stats-testimonials__card-actions', __( 'Remove testimonial', 'chw' ) )
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
						{ title: __( 'Background', 'chw' ), initialOpen: true },
						el(
							BaseControl,
							{ help: __( 'Shown in grayscale behind the section. Defaults to bg-01.webp when none is selected.', 'chw' ) },
							el(
								MediaUploadCheck,
								null,
								el( MediaUpload, {
									onSelect: function ( media ) {
										setAttributes( {
											backgroundImageId: media.id,
											backgroundImageUrl: media.url,
										} );
									},
									allowedTypes: [ 'image' ],
									value: attributes.backgroundImageId,
									render: function ( obj ) {
										return el(
											Fragment,
											null,
											el(
												Button,
												{ variant: 'secondary', onClick: obj.open },
												attributes.backgroundImageUrl
													? __( 'Replace background image', 'chw' )
													: __( 'Select background image', 'chw' )
											),
											attributes.backgroundImageUrl
												? el(
														Button,
														{
															variant: 'tertiary',
															isDestructive: true,
															onClick: function () {
																setAttributes( {
																	backgroundImageId: 0,
																	backgroundImageUrl: '',
																} );
															},
														},
														__( 'Use default', 'chw' )
												  )
												: null
										);
									},
								} )
							)
						)
					)
				),

				el(
					'section',
					blockProps,
					el( 'div', {
						className: 'stats-testimonials__bg',
						'aria-hidden': true,
						style: backgroundUrl ? { backgroundImage: 'url(' + backgroundUrl + ')' } : undefined,
					} ),
					el( 'div', { className: 'stats-testimonials__overlay', 'aria-hidden': true } ),
					el(
						'div',
						{ className: 'stats-testimonials__inner _container' },
						el(
							'div',
							{ className: 'stats-testimonials__stats', 'data-count': stats.length || 1 },
							statItems
						),
						stats.length < MAX_STATS
							? el(
									'div',
									{ className: 'stats-testimonials__add', contentEditable: false },
									el(
										Button,
										{
											variant: 'secondary',
											onClick: function () {
												setStats( stats.concat( [ {
													id: uid( 'stat' ),
													value: '',
													label: '',
												} ] ) );
											},
										},
										__( 'Add stat', 'chw' )
									)
							  )
							: null,
						el(
							'div',
							{
								className: 'stats-testimonials__cards-wrap',
								'data-count': testimonials.length || 1,
							},
							el(
								'div',
								{
									className: 'stats-testimonials__cards',
									'data-count': testimonials.length || 1,
								},
								testimonialItems
							)
						),
						testimonials.length < MAX_TESTIMONIALS
							? el(
									'div',
									{ className: 'stats-testimonials__add', contentEditable: false },
									el(
										Button,
										{
											variant: 'secondary',
											onClick: function () {
												setTestimonials( testimonials.concat( [ {
													id: uid( 'testimonial' ),
													quote: '',
													name: '',
													location: '',
												} ] ) );
											},
										},
										__( 'Add testimonial', 'chw' )
									)
							  )
							: null
					)
				)
			);
		},

		save: function () {
			return null;
		},
	} );
} )( window.wp );
