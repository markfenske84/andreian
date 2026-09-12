( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, useBlockProps, useBlockEditingMode } = wp.blockEditor;
	const {
		Button,
		Modal,
		PanelBody,
		RangeControl,
		SelectControl,
		Spinner,
		TextControl,
		ToggleControl,
	} = wp.components;
	const { createElement: el, Fragment, useState } = wp.element;
	const { useSelect } = wp.data;
	const { __ } = wp.i18n;
	const ServerSideRender = wp.serverSideRender;

	const layouts = [
		{ value: 'grid', label: __( 'Grid', 'andreian' ) },
		{ value: 'list', label: __( 'List', 'andreian' ) },
		{ value: 'full', label: __( 'Full', 'andreian' ) },
		{ value: 'hero-tiles', label: __( 'Hero Tiles', 'andreian' ) },
		{ value: 'category-tiles', label: __( 'Category Tiles', 'andreian' ) },
	];

	const attributes = {
		categoryId: { type: 'number', default: 0 },
		categorySlug: { type: 'string', default: '' },
		postsToShow: { type: 'number', default: 9 },
		layout: { type: 'string', default: 'grid' },
		orderBy: { type: 'string', default: 'date' },
		order: { type: 'string', default: 'DESC' },
		selectedPostIds: { type: 'array', default: [] },
		offset: { type: 'number', default: 0 },
		excludePostIds: { type: 'array', default: [] },
		prioritizeFirstImage: { type: 'boolean', default: false },
		showExcerpt: { type: 'boolean', default: false },
		showSidebar: { type: 'boolean', default: false },
		showRandomSidebar: { type: 'boolean', default: false },
		sidebarPostsToShow: { type: 'number', default: 9 },
		sidebarTitle: { type: 'string', default: 'Random' },
		excludeFeaturedPosts: { type: 'boolean', default: false },
		showArchiveLink: { type: 'boolean', default: false },
		archiveLinkLabel: { type: 'string', default: '' },
		archiveLinkUrl: { type: 'string', default: '' },
	};

	function getSlotCount( settings ) {
		if ( settings.layout === 'full' ) {
			return 1;
		}

		if ( settings.layout === 'hero-tiles' ) {
			return 8;
		}

		return Math.min( 15, Math.max( 1, parseInt( settings.postsToShow, 10 ) || 9 ) );
	}

	function normalizeSelectedPostIds( ids, count ) {
		const source = Array.isArray( ids ) ? ids : [];
		const next = [];

		for ( let index = 0; index < count; index++ ) {
			const id = parseInt( source[ index ], 10 );
			next.push( Number.isInteger( id ) && id > 0 ? id : 0 );
		}

		return next;
	}

	function decodeTitle( html ) {
		if ( ! html ) {
			return '';
		}

		const stripped = String( html ).replace( /<[^>]+>/g, '' );
		const doc = new DOMParser().parseFromString( stripped, 'text/html' );

		return doc.documentElement.textContent || '';
	}

	function LayoutPreview( props ) {
		return el(
			Button,
			{
				className:
					'andreian-layout-choice' +
					( props.selected ? ' is-selected' : '' ),
				onClick: props.onClick,
				'aria-pressed': props.selected,
			},
			el(
				'span',
				{
					className:
						'andreian-layout-choice__diagram andreian-layout-choice__diagram--' +
						props.value,
					'aria-hidden': true,
				},
				el( 'span' ),
				el( 'span' ),
				el( 'span' ),
				el( 'span' ),
				el( 'span' )
			),
			el( 'span', { className: 'andreian-layout-choice__label' }, props.label )
		);
	}

	function CustomSlotPicker( props ) {
		const settings = props.settings;
		const setAttributes = props.setAttributes;
		const slotCount = getSlotCount( settings );
		const selectedIds = normalizeSelectedPostIds( settings.selectedPostIds, slotCount );
		const [ activeSlot, setActiveSlot ] = useState( null );
		const [ search, setSearch ] = useState( '' );
		const selectedPosts = useSelect(
			function ( select ) {
				return selectedIds.map( function ( postId ) {
					if ( ! postId ) {
						return null;
					}

					const post = select( 'core' ).getEntityRecord( 'postType', 'post', postId );
					const media =
						post && post.featured_media
							? select( 'core' ).getMedia( post.featured_media )
							: null;

					return {
						id: postId,
						title: post && post.title ? post.title.rendered : '',
						image:
							media && media.media_details && media.media_details.sizes && media.media_details.sizes.medium
								? media.media_details.sizes.medium.source_url
								: media
									? media.source_url
									: '',
					};
				} );
			},
			[ selectedIds.join( ',' ) ]
		);
		const searchResults = useSelect(
			function ( select ) {
				if ( activeSlot === null ) {
					return null;
				}

				return select( 'core' ).getEntityRecords( 'postType', 'post', {
					per_page: 20,
					status: 'publish',
					search: search,
					orderby: search ? 'relevance' : 'date',
					order: 'desc',
				} );
			},
			[ activeSlot, search ]
		);

		function assignPost( postId ) {
			const next = selectedIds.slice();
			next[ activeSlot ] = postId;
			setAttributes( { selectedPostIds: next } );
			setActiveSlot( null );
			setSearch( '' );
		}

		return el(
			Fragment,
			null,
			el(
				'p',
				{ className: 'andreian-custom-slots__help' },
				__( 'Click a tile to choose the post for that spot.', 'andreian' )
			),
			el(
				'div',
				{
					className:
						'andreian-posts andreian-posts--' +
						settings.layout +
						' andreian-custom-slots',
				},
				selectedIds.map( function ( postId, index ) {
					const post = selectedPosts[ index ];

					return el(
						'button',
						{
							key: 'slot-' + index,
							type: 'button',
							className:
								'andreian-card andreian-card--' +
								settings.layout +
								' andreian-custom-slot' +
								( postId ? '' : ' is-empty' ),
							onClick: function () {
								setActiveSlot( index );
								setSearch( '' );
							},
						},
						post && post.image
							? el(
									'span',
									{ className: 'andreian-card__image-link' },
									el( 'img', { src: post.image, alt: '' } )
							  )
							: el( 'span', { className: 'andreian-custom-slot__image' } ),
						el(
							'span',
							{ className: 'andreian-card__content' },
							el(
								'span',
								{ className: 'andreian-custom-slot__label' },
								__( 'Slot', 'andreian' ) + ' ' + ( index + 1 )
							),
							el(
								'span',
								{ className: 'andreian-card__title' },
								post && post.title
									? decodeTitle( post.title )
									: __( 'Choose post', 'andreian' )
							)
						)
					);
				} )
			),
			activeSlot !== null
				? el(
						Modal,
						{
							title:
								__( 'Choose a post for slot', 'andreian' ) +
								' ' +
								( activeSlot + 1 ),
							onRequestClose: function () {
								setActiveSlot( null );
								setSearch( '' );
							},
							className: 'andreian-custom-slot-modal',
						},
						el( TextControl, {
							label: __( 'Search posts', 'andreian' ),
							value: search,
							onChange: setSearch,
						} ),
						searchResults === null
							? el( Spinner )
							: el(
									'ul',
									{ className: 'andreian-custom-slot-modal__list' },
									searchResults.map( function ( post ) {
										return el(
											'li',
											{ key: post.id },
											el(
												Button,
												{
													variant: 'tertiary',
													onClick: function () {
														assignPost( post.id );
													},
												},
												decodeTitle( post.title.rendered )
											)
										);
									} )
							  ),
						selectedIds[ activeSlot ]
							? el(
									Button,
									{
										variant: 'link',
										onClick: function () {
											assignPost( 0 );
										},
									},
									__( 'Use automatic post for this slot', 'andreian' )
							  )
							: null
				  )
				: null
		);
	}

	function Edit( props ) {
		useBlockEditingMode( 'default' );
		const blockProps = useBlockProps( {
			className: 'andreian-post-editor',
		} );
		const settings = props.attributes;
		const setAttributes = props.setAttributes;
		const isCustom = settings.orderBy === 'custom';
		const categories = useSelect( function ( select ) {
			return select( 'core' ).getEntityRecords( 'taxonomy', 'category', {
				per_page: 100,
				orderby: 'name',
				order: 'asc',
			} );
		}, [] );
		const categoryOptions = [
			{ label: __( 'All categories', 'andreian' ), value: 0 },
		].concat(
			( categories || [] ).map( function ( category ) {
				return { label: category.name, value: category.id };
			} )
		);
		const selectedCategoryId =
			settings.categoryId ||
			( categories || [] ).reduce( function ( found, category ) {
				return category.slug === settings.categorySlug ? category.id : found;
			}, 0 );

		function updateExcludedPosts( value ) {
			setAttributes( {
				excludePostIds: value
					.split( ',' )
					.map( function ( id ) {
						return parseInt( id.trim(), 10 );
					} )
					.filter( function ( id ) {
						return Number.isInteger( id ) && id > 0;
					} ),
			} );
		}

		function syncCustomSlots( nextSettings ) {
			const count = getSlotCount( nextSettings );
			setAttributes(
				Object.assign( {}, nextSettings, {
					selectedPostIds: normalizeSelectedPostIds(
						nextSettings.selectedPostIds || settings.selectedPostIds,
						count
					),
				} )
			);
		}

		return el(
			Fragment,
			null,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Post query', 'andreian' ), initialOpen: true },
					el( SelectControl, {
						label: __( 'Category', 'andreian' ),
						value: selectedCategoryId,
						options: categoryOptions,
						onChange: function ( value ) {
							const categoryId = parseInt( value, 10 ) || 0;
							const category = ( categories || [] ).find( function ( item ) {
								return item.id === categoryId;
							} );
							setAttributes( {
								categoryId: categoryId,
								categorySlug: category ? category.slug : '',
							} );
						},
					} ),
					el( RangeControl, {
						label: __( 'Number of posts', 'andreian' ),
						value: settings.postsToShow,
						min: 1,
						max: 15,
						onChange: function ( value ) {
							const next = { postsToShow: value };
							if ( isCustom ) {
								syncCustomSlots( Object.assign( {}, settings, next ) );
							} else {
								setAttributes( next );
							}
						},
					} ),
					el( SelectControl, {
						label: __( 'Order by', 'andreian' ),
						value: settings.orderBy,
						options: [
							{ label: __( 'Date', 'andreian' ), value: 'date' },
							{ label: __( 'Title', 'andreian' ), value: 'title' },
							{ label: __( 'Random', 'andreian' ), value: 'rand' },
							{ label: __( 'Custom', 'andreian' ), value: 'custom' },
						],
						onChange: function ( value ) {
							if ( value === 'custom' ) {
								syncCustomSlots(
									Object.assign( {}, settings, { orderBy: value } )
								);
							} else {
								setAttributes( { orderBy: value } );
							}
						},
					} ),
					! isCustom && settings.orderBy !== 'rand'
						? el( SelectControl, {
								label: __( 'Order', 'andreian' ),
								value: settings.order,
								options: [
									{ label: __( 'Descending', 'andreian' ), value: 'DESC' },
									{ label: __( 'Ascending', 'andreian' ), value: 'ASC' },
								],
								onChange: function ( value ) {
									setAttributes( { order: value } );
								},
						  } )
						: null,
					! isCustom
						? el( RangeControl, {
								label: __( 'Skip posts', 'andreian' ),
								help: __( 'Useful for avoiding repeats between homepage sections.', 'andreian' ),
								value: settings.offset,
								min: 0,
								max: 40,
								onChange: function ( value ) {
									setAttributes( { offset: value } );
								},
						  } )
						: null,
					! isCustom
						? el( TextControl, {
								label: __( 'Exclude post IDs', 'andreian' ),
								help: __( 'Comma-separated IDs.', 'andreian' ),
								value: ( settings.excludePostIds || [] ).join( ', ' ),
								onChange: updateExcludedPosts,
						  } )
						: null,
					el( ToggleControl, {
						label: __( 'Show excerpts', 'andreian' ),
						checked: settings.showExcerpt,
						onChange: function ( value ) {
							setAttributes( { showExcerpt: value } );
						},
					} ),
					settings.layout === 'grid'
						? el( ToggleControl, {
								label: __( 'Show homepage sidebar', 'andreian' ),
								help: __(
									'Uses the Homepage Latest Sidebar widget area.',
									'andreian'
								),
								checked: settings.showSidebar,
								onChange: function ( value ) {
									setAttributes( { showSidebar: value } );
								},
						  } )
						: null,
					settings.layout === 'list'
						? el( ToggleControl, {
								label: __( 'Show Random sidebar', 'andreian' ),
								help: __(
									'Displays a random post slideshow with the Homepage Latest Sidebar widget area beneath it.',
									'andreian'
								),
								checked: settings.showRandomSidebar,
								onChange: function ( value ) {
									setAttributes( { showRandomSidebar: value } );
								},
						  } )
						: null,
					settings.layout === 'list' && settings.showRandomSidebar
						? el( RangeControl, {
								label: __( 'Random slides', 'andreian' ),
								value: settings.sidebarPostsToShow,
								min: 1,
								max: 15,
								onChange: function ( value ) {
									setAttributes( { sidebarPostsToShow: value } );
								},
						  } )
						: null,
					settings.layout === 'list' && settings.showRandomSidebar
						? el( TextControl, {
								label: __( 'Random sidebar title', 'andreian' ),
								value: settings.sidebarTitle || 'Random',
								onChange: function ( value ) {
									setAttributes( { sidebarTitle: value } );
								},
						  } )
						: null,
					el( ToggleControl, {
						label: __( 'Prioritize first image', 'andreian' ),
						help: __( 'Enable only for the first above-the-fold collection.', 'andreian' ),
						checked: settings.prioritizeFirstImage,
						onChange: function ( value ) {
							setAttributes( { prioritizeFirstImage: value } );
						},
					} ),
					settings.layout === 'list' && ! isCustom
						? el( ToggleControl, {
								label: __( 'Exclude featured hero posts', 'andreian' ),
								help: __(
									'Skip posts pinned as Homepage Featured. Latest posts still appear here even if they backfill the hero.',
									'andreian'
								),
								checked: settings.excludeFeaturedPosts,
								onChange: function ( value ) {
									setAttributes( { excludeFeaturedPosts: value } );
								},
						  } )
						: null,
					settings.categorySlug || settings.categoryId
						? el( ToggleControl, {
								label: __( 'Show archive link', 'andreian' ),
								checked: settings.showArchiveLink,
								onChange: function ( value ) {
									setAttributes( { showArchiveLink: value } );
								},
						  } )
						: null,
					settings.showArchiveLink
						? el( TextControl, {
								label: __( 'Archive link label', 'andreian' ),
								help: __( 'Leave blank to use “See More {category}”.', 'andreian' ),
								placeholder: __( 'See More {category}', 'andreian' ),
								value: settings.archiveLinkLabel || '',
								onChange: function ( value ) {
									setAttributes( { archiveLinkLabel: value } );
								},
						  } )
						: null,
					settings.showArchiveLink
						? el( TextControl, {
								label: __( 'Archive link URL override', 'andreian' ),
								help: __(
									'Leave blank to use the category archive URL.',
									'andreian'
								),
								value: settings.archiveLinkUrl || '',
								onChange: function ( value ) {
									setAttributes( { archiveLinkUrl: value } );
								},
						  } )
						: null
				)
			),
			el(
				'div',
				blockProps,
				el( 'h3', { className: 'andreian-post-editor__heading' }, __( 'Layout', 'andreian' ) ),
				el(
					'div',
					{ className: 'andreian-layout-picker' },
					layouts.map( function ( layout ) {
						return el( LayoutPreview, {
							key: layout.value,
							value: layout.value,
							label: layout.label,
							selected: settings.layout === layout.value,
							onClick: function () {
								if ( isCustom ) {
									syncCustomSlots(
										Object.assign( {}, settings, { layout: layout.value } )
									);
								} else {
									setAttributes( { layout: layout.value } );
								}
							},
						} );
					} )
				),
				isCustom
					? el( CustomSlotPicker, {
							settings: settings,
							setAttributes: setAttributes,
					  } )
					: ServerSideRender
						? el(
								'div',
								{
									className: 'andreian-post-editor__preview',
									onClickCapture: function ( event ) {
										if ( event.target.closest( 'a[href]' ) ) {
											event.preventDefault();
										}
									},
								},
								el( ServerSideRender, {
									block: 'andreian/post',
									attributes: settings,
									EmptyResponsePlaceholder: function () {
										return el( 'p', null, __( 'No posts match this collection.', 'andreian' ) );
									},
								} )
						  )
						: null
			)
		);
	}

	registerBlockType( 'andreian/post', {
		title: __( 'Andreian Post', 'andreian' ),
		description: __( 'Display a dynamic editorial collection of posts.', 'andreian' ),
		icon: 'grid-view',
		category: 'widgets',
		attributes: attributes,
		edit: Edit,
		save: function () {
			return null;
		},
	} );
} )( window.wp );
