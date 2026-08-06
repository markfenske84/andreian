( function ( wp ) {
	const { addFilter } = wp.hooks;
	const { createHigherOrderComponent } = wp.compose;
	const { InspectorControls } = wp.blockEditor;
	const ToolsPanelItem =
		wp.components.ToolsPanelItem ||
		wp.components.__experimentalToolsPanelItem;
	const ToolsPanel =
		wp.components.ToolsPanel ||
		wp.components.__experimentalToolsPanel;
	const { ButtonGroup, Button } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const BLOCK_NAME = 'core/image';
	const ATTR = 'chwMonotone';

	addFilter(
		'blocks.registerBlockType',
		'chw/image-monotone-attribute',
		function ( settings, name ) {
			if ( name !== BLOCK_NAME ) {
				return settings;
			}

			const existingGetEditWrapperProps = settings.getEditWrapperProps;

			return {
				...settings,
				attributes: {
					...settings.attributes,
					[ ATTR ]: {
						type: 'string',
						default: '',
					},
				},
				getEditWrapperProps: function ( attributes ) {
					const props = existingGetEditWrapperProps
						? existingGetEditWrapperProps( attributes )
						: {};
					const monotone = attributes[ ATTR ];

					if ( monotone ) {
						props.className = (
							( props.className || '' ) +
							' has-chw-monotone-' +
							monotone
						).trim();
					}

					return props;
				},
			};
		}
	);

	const withMonotoneFilter = createHigherOrderComponent( function ( BlockEdit ) {
		return function ( props ) {
			if ( props.name !== BLOCK_NAME ) {
				return el( BlockEdit, props );
			}

			const { attributes, setAttributes, clientId } = props;
			const monotone = attributes[ ATTR ] || '';

			if ( ! ToolsPanelItem || ! ToolsPanel ) {
				return el( BlockEdit, props );
			}

			return el(
				Fragment,
				null,
				el( BlockEdit, props ),
				el(
					InspectorControls,
					{ group: 'filter' },
					el(
						ToolsPanel,
						{
							label: __( 'Monotone', 'chw' ),
							panelId: clientId,
							resetAll: function () {
								setAttributes( { [ ATTR ]: '' } );
							},
						},
						el(
							ToolsPanelItem,
							{
								panelId: clientId,
								hasValue: function () {
									return !! monotone;
								},
								label: __( 'Monotone', 'chw' ),
								onDeselect: function () {
									setAttributes( { [ ATTR ]: '' } );
								},
								isShownByDefault: true,
							},
							el(
								ButtonGroup,
								null,
								el(
									Button,
									{
										isPressed: monotone === 'normal',
										onClick: function () {
											setAttributes( { [ ATTR ]: 'normal' } );
										},
									},
									__( 'Black', 'chw' )
								),
								el(
									Button,
									{
										isPressed: monotone === 'inverted',
										onClick: function () {
											setAttributes( { [ ATTR ]: 'inverted' } );
										},
									},
									__( 'White', 'chw' )
								)
							)
						)
					)
				)
			);
		};
	}, 'withMonotoneFilter' );

	addFilter(
		'editor.BlockEdit',
		'chw/image-monotone-control',
		withMonotoneFilter
	);

	function applyMonotoneClass( props, blockType, attributes ) {
		if ( blockType.name !== BLOCK_NAME || ! attributes[ ATTR ] ) {
			return props;
		}

		const className = 'has-chw-monotone-' + attributes[ ATTR ];
		props.className = props.className
			? props.className + ' ' + className
			: className;

		return props;
	}

	addFilter(
		'blocks.getSaveContent.extraProps',
		'chw/image-monotone-save-props',
		applyMonotoneClass
	);
} )( window.wp );
