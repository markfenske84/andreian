( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, useBlockProps } = wp.blockEditor;
	const { TextControl, TextareaControl, Button } = wp.components;
	const { createElement: el, useState } = wp.element;
	const { __ } = wp.i18n;

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

	function reorderRows( rows, from, to ) {
		if ( from === null || to === null || from === to || from < 0 || to < 0 ) {
			return rows;
		}
		const next = rows.slice();
		const moved = next.splice( from, 1 )[ 0 ];
		next.splice( to, 0, moved );
		return next;
	}

	// Truthy tokens for the bulk editor's optional third column.
	const NEW_TAB_TOKENS = [ 'new', 'new-tab', 'newtab', '_blank', 'blank', 'true', 'yes' ];

	// Serialize links to "Label | URL | new" lines (URL/new omitted when empty).
	function serializeBulk( rows ) {
		return rows
			.map( function ( link ) {
				const parts = [ link.label || '' ];
				if ( link.url || link.new_tab ) {
					parts.push( link.url || '' );
				}
				if ( link.new_tab ) {
					parts.push( 'new' );
				}
				return parts.join( ' | ' );
			} )
			.join( '\n' );
	}

	// Parse bulk text into link rows, reusing existing ids by position.
	function parseBulk( text, existing ) {
		const lines = ( text || '' ).split( '\n' );
		const rows = [];
		let kept = 0;

		lines.forEach( function ( raw ) {
			if ( ! raw.trim() ) {
				return;
			}
			const cells = raw.split( '|' ).map( function ( cell ) {
				return cell.trim();
			} );
			const label = cells[ 0 ] || '';
			if ( ! label ) {
				return;
			}
			const url = cells[ 1 ] || '';
			const flag = ( cells[ 2 ] || '' ).toLowerCase();
			const prev = existing[ kept ];
			rows.push( {
				id: prev && prev.id ? prev.id : uid( 'link' ),
				label: label,
				url: url,
				new_tab: NEW_TAB_TOKENS.indexOf( flag ) !== -1,
			} );
			kept++;
		} );

		return rows;
	}

	registerBlockType( 'chw/linkbank-section', {
		title: __( 'Linkbank Section', 'chw' ),
		description: __( 'A plain eyelash, dual-color heading, and intro paragraph beside an unlimited list of links displayed four per row.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'admin-links',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			eyebrowText: { type: 'string', default: '' },
			heading: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			links: { type: 'array', default: [] },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const links = Array.isArray( attributes.links ) ? attributes.links : [];

			const blockProps = useBlockProps( { className: 'linkbank-section' } );

			const modeState = useState( 'list' );
			const mode = modeState[ 0 ];
			const setMode = modeState[ 1 ];

			const bulkState = useState( '' );
			const bulkText = bulkState[ 0 ];
			const setBulkText = bulkState[ 1 ];

			const dragState = useState( null );
			const dragIndex = dragState[ 0 ];
			const setDragIndex = dragState[ 1 ];

			const overState = useState( null );
			const overIndex = overState[ 0 ];
			const setOverIndex = overState[ 1 ];

			const draggableState = useState( null );
			const draggableIndex = draggableState[ 0 ];
			const setDraggableIndex = draggableState[ 1 ];

			function setLinks( rows ) {
				setAttributes( { links: rows } );
			}

			function resetDrag() {
				setDragIndex( null );
				setOverIndex( null );
				setDraggableIndex( null );
			}

			function enterBulk() {
				setBulkText( serializeBulk( links ) );
				setMode( 'bulk' );
			}

			function onBulkChange( value ) {
				setBulkText( value );
				setLinks( parseBulk( value, links ) );
			}

			const toolbar = el(
				'div',
				{ className: 'linkbank-section__links-toolbar', contentEditable: false },
				el(
					Button,
					{
						variant: mode === 'list' ? 'primary' : 'secondary',
						isSmall: true,
						onClick: function () {
							setMode( 'list' );
						},
					},
					__( 'List', 'chw' )
				),
				el(
					Button,
					{
						variant: mode === 'bulk' ? 'primary' : 'secondary',
						isSmall: true,
						onClick: enterBulk,
					},
					__( 'Bulk edit', 'chw' )
				),
				el(
					'span',
					{ className: 'linkbank-section__links-count' },
					links.length + ' ' + ( links.length === 1 ? __( 'link', 'chw' ) : __( 'links', 'chw' ) )
				)
			);

			const linkItems = links.map( function ( link, index ) {
				let itemClass = 'linkbank-section__link-item';
				if ( dragIndex === index ) {
					itemClass += ' is-dragging';
				}
				if ( overIndex === index && dragIndex !== index ) {
					itemClass += ' is-drop-target';
				}

				return el(
					'li',
					{
						key: link.id || 'link-' + index,
						className: itemClass,
						draggable: draggableIndex === index,
						onDragStart: function ( e ) {
							setDragIndex( index );
							if ( e.dataTransfer ) {
								e.dataTransfer.effectAllowed = 'move';
							}
						},
						onDragOver: function ( e ) {
							e.preventDefault();
							if ( overIndex !== index ) {
								setOverIndex( index );
							}
						},
						onDrop: function ( e ) {
							e.preventDefault();
							setLinks( reorderRows( links, dragIndex, index ) );
							resetDrag();
						},
						onDragEnd: function () {
							resetDrag();
						},
					},
					el(
						'div',
						{
							className: 'linkbank-section__drag-handle',
							contentEditable: false,
							onMouseDown: function () {
								setDraggableIndex( index );
							},
							onMouseUp: function () {
								setDraggableIndex( null );
							},
						},
						el( Button, {
							icon: 'move',
							label: __( 'Drag to reorder', 'chw' ),
							tabIndex: -1,
						} )
					),
					el( TextControl, {
						className: 'linkbank-section__field -label',
						label: __( 'Link label', 'chw' ),
						hideLabelFromVision: true,
						placeholder: __( 'Label', 'chw' ),
						value: link.label || '',
						onChange: function ( value ) {
							setLinks( updateRow( links, index, { label: value } ) );
						},
					} ),
					el( TextControl, {
						className: 'linkbank-section__field -url',
						label: __( 'Link URL', 'chw' ),
						hideLabelFromVision: true,
						placeholder: __( 'https:// or /path', 'chw' ),
						value: link.url || '',
						onChange: function ( value ) {
							setLinks( updateRow( links, index, { url: value } ) );
						},
					} ),
					el( Button, {
						className: 'linkbank-section__newtab',
						icon: 'external',
						isPressed: !! link.new_tab,
						label: link.new_tab ? __( 'Opens in new tab', 'chw' ) : __( 'Open in new tab', 'chw' ),
						onClick: function () {
							setLinks( updateRow( links, index, { new_tab: ! link.new_tab } ) );
						},
					} ),
					el( Button, {
						className: 'linkbank-section__remove',
						icon: 'trash',
						isDestructive: true,
						label: __( 'Remove link', 'chw' ),
						onClick: function () {
							setLinks( removeRow( links, index ) );
						},
					} )
				);
			} );

			const listView = el(
				'div',
				{ className: 'linkbank-section__list-view' },
				el( 'ul', { className: 'linkbank-section__editor-list' }, linkItems ),
				el(
					'div',
					{ className: 'linkbank-section__add', contentEditable: false },
					el(
						Button,
						{
							variant: 'secondary',
							onClick: function () {
								setLinks( links.concat( [ {
									id: uid( 'link' ),
									label: '',
									url: '',
									new_tab: false,
								} ] ) );
							},
						},
						__( 'Add link', 'chw' )
					)
				)
			);

			const bulkView = el(
				'div',
				{ className: 'linkbank-section__bulk', contentEditable: false },
				el( TextareaControl, {
					label: __( 'One link per line — Label | URL | new', 'chw' ),
					help: __( 'Separate fields with "|". The URL is optional. Add "new" as a third field to open that link in a new tab.', 'chw' ),
					value: bulkText,
					rows: Math.min( 24, Math.max( 8, links.length + 2 ) ),
					onChange: onBulkChange,
				} )
			);

			return el(
				'section',
				blockProps,
				el(
					'div',
					{ className: 'linkbank-section__inner' },
					el(
						'div',
						{ className: 'linkbank-section__intro _text -align-left' },
						el( RichText, {
							tagName: 'span',
							className: '_eyebrow -plain linkbank-section__eyebrow',
							value: attributes.eyebrowText || '',
							allowedFormats: [],
							placeholder: __( 'Where we serve', 'chw' ),
							onChange: function ( value ) {
								setAttributes( { eyebrowText: value } );
							},
						} ),
						el( RichText, {
							tagName: 'h2',
							className: 'linkbank-section__heading _text -secondary',
							value: attributes.heading || '',
							allowedFormats: [ 'core/bold', 'core/text-color' ],
							placeholder: __( 'Coverage in 49 states.', 'chw' ),
							onChange: function ( value ) {
								setAttributes( { heading: value } );
							},
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'linkbank-section__text _text -muted _text-size -lg',
							value: attributes.description || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Find your state and see local plan options.', 'chw' ),
							onChange: function ( value ) {
								setAttributes( { description: value } );
							},
						} )
					),
					el(
						'div',
						{ className: 'linkbank-section__links-editor' },
						toolbar,
						mode === 'bulk' ? bulkView : listView
					)
				)
			);
		},

		save: function () {
			return null;
		},
	} );
} )( window.wp );
