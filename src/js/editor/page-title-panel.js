( function ( wp ) {
	const { registerPlugin } = wp.plugins;
	const { PluginDocumentSettingPanel } = wp.editPost;
	const {
		TextControl,
		TextareaControl,
		ToggleControl,
		SelectControl,
		Button,
		BaseControl,
	} = wp.components;
	const { useSelect, useDispatch } = wp.data;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const BUTTON_MODIFIER_OPTIONS = [
		{ label: __( 'None', 'chw' ), value: '' },
		{ label: __( 'Arrow', 'chw' ), value: 'arrow' },
		{ label: __( 'Phone', 'chw' ), value: 'phone' },
		{ label: __( 'Download', 'chw' ), value: 'download' },
	];

	/**
	 * Small helper: a labelled group heading with a divider above it.
	 */
	function sectionHeading( label ) {
		return el(
			'div',
			{ className: 'chw-masthead-section-heading' },
			el( 'hr', { className: 'chw-masthead-divider' } ),
			el( 'strong', null, label )
		);
	}

	const MastheadPanel = function () {
		const postType = useSelect( function ( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );

		const meta = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		}, [] );

		const { editPost } = useDispatch( 'core/editor' );

		if ( postType !== 'page' && postType !== 'post' ) {
			return null;
		}

		function setMeta( key, value ) {
			editPost( { meta: { [ key ]: value } } );
		}

		const buttons = Array.isArray( meta.masthead_buttons ) ? meta.masthead_buttons : [];
		const stats = Array.isArray( meta.masthead_stats ) ? meta.masthead_stats : [];

		/**
		 * Generic immutable repeater update helpers.
		 */
		function updateRow( metaKey, rows, index, patch ) {
			const next = rows.map( function ( row, i ) {
				return i === index ? Object.assign( {}, row, patch ) : row;
			} );
			setMeta( metaKey, next );
		}

		function addRow( metaKey, rows, blank ) {
			setMeta( metaKey, rows.concat( [ blank ] ) );
		}

		function removeRow( metaKey, rows, index ) {
			setMeta(
				metaKey,
				rows.filter( function ( row, i ) {
					return i !== index;
				} )
			);
		}

		function moveRow( metaKey, rows, index, dir ) {
			const target = index + dir;
			if ( target < 0 || target >= rows.length ) {
				return;
			}
			const next = rows.slice();
			const tmp = next[ index ];
			next[ index ] = next[ target ];
			next[ target ] = tmp;
			setMeta( metaKey, next );
		}

		/**
		 * CTA buttons repeater rows.
		 */
		const buttonRows = buttons.map( function ( button, index ) {
			return el(
				'div',
				{ key: 'btn-' + index, className: 'chw-masthead-repeater-row' },
				el(
					'div',
					{ className: 'chw-masthead-repeater-row__head' },
					el(
						'span',
						null,
						( index === 0 ? __( 'Primary Button', 'chw' ) : __( 'Button', 'chw' ) ) + ' ' + ( index + 1 )
					),
					el(
						'div',
						{ className: 'chw-masthead-repeater-row__actions' },
						el( Button, {
							icon: 'arrow-up-alt2',
							label: __( 'Move up', 'chw' ),
							disabled: index === 0,
							onClick: function () {
								moveRow( 'masthead_buttons', buttons, index, -1 );
							},
						} ),
						el( Button, {
							icon: 'arrow-down-alt2',
							label: __( 'Move down', 'chw' ),
							disabled: index === buttons.length - 1,
							onClick: function () {
								moveRow( 'masthead_buttons', buttons, index, 1 );
							},
						} ),
						el( Button, {
							icon: 'trash',
							isDestructive: true,
							label: __( 'Remove', 'chw' ),
							onClick: function () {
								removeRow( 'masthead_buttons', buttons, index );
							},
						} )
					)
				),
				el( TextControl, {
					label: __( 'Text', 'chw' ),
					value: button.text || '',
					onChange: function ( value ) {
						updateRow( 'masthead_buttons', buttons, index, { text: value } );
					},
				} ),
				el( TextControl, {
					label: __( 'URL', 'chw' ),
					value: button.url || '',
					onChange: function ( value ) {
						updateRow( 'masthead_buttons', buttons, index, { url: value } );
					},
				} ),
				el( SelectControl, {
					label: __( 'Icon', 'chw' ),
					value: button.modifier || '',
					options: BUTTON_MODIFIER_OPTIONS,
					onChange: function ( value ) {
						updateRow( 'masthead_buttons', buttons, index, { modifier: value } );
					},
				} ),
				el( ToggleControl, {
					label: __( 'Open in new tab', 'chw' ),
					checked: !! button.new_tab,
					onChange: function ( value ) {
						updateRow( 'masthead_buttons', buttons, index, { new_tab: value } );
					},
				} )
			);
		} );

		/**
		 * Stats repeater rows.
		 */
		const statRows = stats.map( function ( stat, index ) {
			return el(
				'div',
				{ key: 'stat-' + index, className: 'chw-masthead-repeater-row' },
				el(
					'div',
					{ className: 'chw-masthead-repeater-row__head' },
					el( 'span', null, __( 'Stat', 'chw' ) + ' ' + ( index + 1 ) ),
					el(
						'div',
						{ className: 'chw-masthead-repeater-row__actions' },
						el( Button, {
							icon: 'arrow-up-alt2',
							label: __( 'Move up', 'chw' ),
							disabled: index === 0,
							onClick: function () {
								moveRow( 'masthead_stats', stats, index, -1 );
							},
						} ),
						el( Button, {
							icon: 'arrow-down-alt2',
							label: __( 'Move down', 'chw' ),
							disabled: index === stats.length - 1,
							onClick: function () {
								moveRow( 'masthead_stats', stats, index, 1 );
							},
						} ),
						el( Button, {
							icon: 'trash',
							isDestructive: true,
							label: __( 'Remove', 'chw' ),
							onClick: function () {
								removeRow( 'masthead_stats', stats, index );
							},
						} )
					)
				),
				el( TextControl, {
					label: __( 'Value', 'chw' ),
					value: stat.value || '',
					onChange: function ( value ) {
						updateRow( 'masthead_stats', stats, index, { value: value } );
					},
				} ),
				el( TextControl, {
					label: __( 'Label', 'chw' ),
					value: stat.label || '',
					onChange: function ( value ) {
						updateRow( 'masthead_stats', stats, index, { label: value } );
					},
				} )
			);
		} );

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'chw-masthead',
				title: __( 'Masthead', 'chw' ),
				className: 'chw-masthead-panel',
			},

			// Headline + eyelash + description.
			el( TextControl, {
				label: __( 'Headline', 'chw' ),
				help: __( 'Overrides the page title in the masthead. Wrap text in <span></span> for the orange accent.', 'chw' ),
				value: meta.custom_page_title || '',
				onChange: function ( value ) {
					setMeta( 'custom_page_title', value );
				},
			} ),

			el( TextControl, {
				label: __( 'Eyelash Text', 'chw' ),
				value: meta.masthead_eyelash_text || '',
				onChange: function ( value ) {
					setMeta( 'masthead_eyelash_text', value );
				},
			} ),

			el( TextareaControl, {
				label: __( 'Eyelash Icon (paste SVG)', 'chw' ),
				value: meta.masthead_eyelash_icon || '',
				rows: 2,
				onChange: function ( value ) {
					setMeta( 'masthead_eyelash_icon', value );
				},
			} ),

			el( TextareaControl, {
				label: __( 'Description', 'chw' ),
				help: __( 'Paragraph shown below the headline.', 'chw' ),
				value: meta.masthead_description || '',
				rows: 3,
				onChange: function ( value ) {
					setMeta( 'masthead_description', value );
				},
			} ),

			// CTA buttons.
			sectionHeading( __( 'CTA Buttons', 'chw' ) ),
			el( BaseControl, { help: __( 'The first button is styled as primary; the rest are outline.', 'chw' ) }, buttonRows ),
			el(
				Button,
				{
					variant: 'secondary',
					className: 'chw-editor-add-button',
					onClick: function () {
						addRow( 'masthead_buttons', buttons, { text: '', url: '', modifier: '', new_tab: false } );
					},
				},
				__( 'Add Button', 'chw' )
			),

			// Secondary CTA.
			sectionHeading( __( 'Secondary CTA', 'chw' ) ),
			el( TextControl, {
				label: __( 'Lead-in Text', 'chw' ),
				value: meta.masthead_secondary_cta_text || '',
				onChange: function ( value ) {
					setMeta( 'masthead_secondary_cta_text', value );
				},
			} ),
			el( TextControl, {
				label: __( 'Button Text', 'chw' ),
				value: meta.masthead_secondary_cta_button_text || '',
				onChange: function ( value ) {
					setMeta( 'masthead_secondary_cta_button_text', value );
				},
			} ),
			el( TextControl, {
				label: __( 'Button URL', 'chw' ),
				value: meta.masthead_secondary_cta_button_url || '',
				onChange: function ( value ) {
					setMeta( 'masthead_secondary_cta_button_url', value );
				},
			} ),
			el( TextareaControl, {
				label: __( 'Button Icon (paste SVG)', 'chw' ),
				value: meta.masthead_secondary_cta_button_icon || '',
				rows: 2,
				onChange: function ( value ) {
					setMeta( 'masthead_secondary_cta_button_icon', value );
				},
			} ),

			// Stats.
			sectionHeading( __( 'Stats', 'chw' ) ),
			el( ToggleControl, {
				label: __( 'Show stats', 'chw' ),
				checked: !! meta.masthead_show_stats,
				onChange: function ( value ) {
					setMeta( 'masthead_show_stats', value );
				},
			} ),
			meta.masthead_show_stats
				? el(
						Fragment,
						null,
						statRows,
						el(
							Button,
							{
								variant: 'secondary',
								className: 'chw-editor-add-button',
								onClick: function () {
									addRow( 'masthead_stats', stats, { value: '', label: '' } );
								},
							},
							__( 'Add Stat', 'chw' )
						)
				  )
				: null,

			// Territory representative CTA box.
			sectionHeading( __( 'Territory Rep CTA Box', 'chw' ) ),
			el( ToggleControl, {
				label: __( 'Show rep CTA box', 'chw' ),
				checked: !! meta.masthead_rep_box_enabled,
				onChange: function ( value ) {
					setMeta( 'masthead_rep_box_enabled', value );
				},
			} ),
			meta.masthead_rep_box_enabled
				? el(
						Fragment,
						null,
						el( TextControl, {
							label: __( 'Eyebrow', 'chw' ),
							value: meta.masthead_rep_box_eyebrow || '',
							onChange: function ( value ) {
								setMeta( 'masthead_rep_box_eyebrow', value );
							},
						} ),
						el( TextControl, {
							label: __( 'Heading', 'chw' ),
							value: meta.masthead_rep_box_heading || '',
							onChange: function ( value ) {
								setMeta( 'masthead_rep_box_heading', value );
							},
						} ),
						el( TextareaControl, {
							label: __( 'Text', 'chw' ),
							value: meta.masthead_rep_box_text || '',
							rows: 2,
							onChange: function ( value ) {
								setMeta( 'masthead_rep_box_text', value );
							},
						} ),
						el( TextControl, {
							label: __( 'Button Text', 'chw' ),
							value: meta.masthead_rep_box_button_text || '',
							onChange: function ( value ) {
								setMeta( 'masthead_rep_box_button_text', value );
							},
						} ),
						el( TextControl, {
							label: __( 'Button URL', 'chw' ),
							value: meta.masthead_rep_box_button_url || '',
							onChange: function ( value ) {
								setMeta( 'masthead_rep_box_button_url', value );
							},
						} ),
						el( TextControl, {
							label: __( 'Phone', 'chw' ),
							value: meta.masthead_rep_box_phone || '',
							onChange: function ( value ) {
								setMeta( 'masthead_rep_box_phone', value );
							},
						} )
				  )
				: null,

			// Global content toggles.
			sectionHeading( __( 'Global Content', 'chw' ) ),
			el( ToggleControl, {
				label: __( 'Show legal text', 'chw' ),
				help: __( 'Pulls the global legal text set in the Customizer.', 'chw' ),
				checked: !! meta.masthead_show_legal,
				onChange: function ( value ) {
					setMeta( 'masthead_show_legal', value );
				},
			} ),
			el( ToggleControl, {
				label: __( 'Show "As Seen In" logos', 'chw' ),
				help: __( 'Pulls the global logos set in the Customizer.', 'chw' ),
				checked: !! meta.masthead_show_as_seen_in,
				onChange: function ( value ) {
					setMeta( 'masthead_show_as_seen_in', value );
				},
			} ),
			el( ToggleControl, {
				label: __( 'Show award seal', 'chw' ),
				help: __( 'Pulls the global award seal set in the Customizer.', 'chw' ),
				checked: !! meta.masthead_show_award_seal,
				onChange: function ( value ) {
					setMeta( 'masthead_show_award_seal', value );
				},
			} ),

			postType === 'page'
				? el(
						Fragment,
						null,
						sectionHeading( __( 'Visibility', 'chw' ) ),
						el( ToggleControl, {
							label: __( 'Disable masthead', 'chw' ),
							help: __( 'Hides the masthead on this page. Off by default for new pages.', 'chw' ),
							checked: !! meta.masthead_disabled,
							onChange: function ( value ) {
								setMeta( 'masthead_disabled', value );
							},
						} )
				  )
				: null
		);
	};

	registerPlugin( 'chw-masthead', {
		render: MastheadPanel,
	} );
} )( window.wp );
