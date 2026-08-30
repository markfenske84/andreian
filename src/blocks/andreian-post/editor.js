( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const {
		Button,
		PanelBody,
		RangeControl,
		SelectControl,
		TextControl,
		ToggleControl,
	} = wp.components;
	const { createElement: el, Fragment } = wp.element;
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
		offset: { type: 'number', default: 0 },
		excludePostIds: { type: 'array', default: [] },
		prioritizeFirstImage: { type: 'boolean', default: false },
		showExcerpt: { type: 'boolean', default: false },
		showSidebar: { type: 'boolean', default: false },
		showRandomSidebar: { type: 'boolean', default: false },
		sidebarPostsToShow: { type: 'number', default: 9 },
		sidebarTitle: { type: 'string', default: 'Random' },
	};

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

	function Edit( props ) {
		const blockProps = useBlockProps( {
			className: 'andreian-post-editor',
		} );
		const settings = props.attributes;
		const setAttributes = props.setAttributes;
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
							setAttributes( { postsToShow: value } );
						},
					} ),
					el( SelectControl, {
						label: __( 'Order by', 'andreian' ),
						value: settings.orderBy,
						options: [
							{ label: __( 'Date', 'andreian' ), value: 'date' },
							{ label: __( 'Title', 'andreian' ), value: 'title' },
							{ label: __( 'Random', 'andreian' ), value: 'rand' },
						],
						onChange: function ( value ) {
							setAttributes( { orderBy: value } );
						},
					} ),
					settings.orderBy !== 'rand'
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
					el( RangeControl, {
						label: __( 'Skip posts', 'andreian' ),
						help: __( 'Useful for avoiding repeats between homepage sections.', 'andreian' ),
						value: settings.offset,
						min: 0,
						max: 40,
						onChange: function ( value ) {
							setAttributes( { offset: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Exclude post IDs', 'andreian' ),
						help: __( 'Comma-separated IDs.', 'andreian' ),
						value: ( settings.excludePostIds || [] ).join( ', ' ),
						onChange: updateExcludedPosts,
					} ),
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
					} )
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
								setAttributes( { layout: layout.value } );
							},
						} );
					} )
				),
				ServerSideRender
					? el( ServerSideRender, {
							block: 'andreian/post',
							attributes: settings,
							EmptyResponsePlaceholder: function () {
								return el( 'p', null, __( 'No posts match this collection.', 'andreian' ) );
							},
					  } )
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
