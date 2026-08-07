( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls, useBlockProps } = wp.blockEditor;
	const {
		PanelBody,
		TextControl,
		BaseControl,
		Button,
	} = wp.components;
	const { createElement: el, Fragment, useEffect, useState } = wp.element;
	const { __ } = wp.i18n;

	const MIN_COLUMNS = 2;
	const MAX_COLUMNS = 4;
	const MIN_TABS = 1;

	const CHECK_ICON = el(
		'svg',
		{
			className: 'tabbed-comparison-table__check',
			viewBox: '0 0 24 24',
			'aria-hidden': 'true',
			focusable: 'false',
		},
		el( 'path', {
			d: 'M20 6 9 17l-5-5',
			fill: 'none',
			stroke: 'currentColor',
			'stroke-width': '2.5',
			'stroke-linecap': 'round',
			'stroke-linejoin': 'round',
		} )
	);

	function uid( prefix ) {
		return prefix + '-' + Math.random().toString( 36 ).slice( 2, 9 );
	}

	function columnVariant( index ) {
		if ( index === 0 ) {
			return 'primary';
		}
		if ( index === 1 ) {
			return 'secondary';
		}
		return 'tertiary';
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

	function duplicateRow( rows, index ) {
		const source = rows[ index ];
		if ( ! source ) {
			return rows;
		}

		const copy = Object.assign( {}, source, {
			id: uid( 'row' ),
			cells: ( Array.isArray( source.cells ) ? source.cells : [] ).map( function ( cell ) {
				return Object.assign( {}, cell );
			} ),
		} );

		const next = rows.slice();
		next.splice( index + 1, 0, copy );
		return next;
	}

	function emptyCell() {
		return { type: 'none', text: '' };
	}

	function emptyRow( columnCount ) {
		const cells = [];
		for ( let i = 0; i < columnCount; i++ ) {
			cells.push( emptyCell() );
		}
		return {
			id: uid( 'row' ),
			label: '',
			cells: cells,
		};
	}

	function emptyGroup( columnCount ) {
		const columnLabels = [];
		for ( let i = 0; i < columnCount; i++ ) {
			columnLabels.push( '' );
		}
		return {
			id: uid( 'group' ),
			title: '',
			columnLabels: columnLabels,
			rows: [ emptyRow( columnCount ) ],
		};
	}

	function emptyColumns() {
		return [
			{ id: uid( 'col' ), title: '', subtitle: '' },
			{ id: uid( 'col' ), title: '', subtitle: '' },
		];
	}

	function emptyTab() {
		const columns = emptyColumns();
		return {
			id: uid( 'tab' ),
			label: '',
			cornerLabel: '',
			columns: columns,
			groups: [ emptyGroup( columns.length ) ],
		};
	}

	function resizeColumnLabels( labels, columnCount ) {
		const next = Array.isArray( labels ) ? labels.slice( 0, columnCount ) : [];
		while ( next.length < columnCount ) {
			next.push( '' );
		}
		return next;
	}

	function resizeRowCells( cells, columnCount ) {
		const next = Array.isArray( cells ) ? cells.slice( 0, columnCount ) : [];
		while ( next.length < columnCount ) {
			next.push( emptyCell() );
		}
		return next;
	}

	function syncTabColumnCount( tab, columnCount ) {
		const columns = Array.isArray( tab.columns ) ? tab.columns.slice( 0, columnCount ) : [];
		while ( columns.length < columnCount ) {
			columns.push( { id: uid( 'col' ), title: '', subtitle: '' } );
		}

		const groups = ( Array.isArray( tab.groups ) ? tab.groups : [] ).map( function ( group ) {
			const rows = ( Array.isArray( group.rows ) ? group.rows : [] ).map( function ( row ) {
				return Object.assign( {}, row, {
					cells: resizeRowCells( row.cells, columnCount ),
				} );
			} );
			return Object.assign( {}, group, {
				columnLabels: resizeColumnLabels( group.columnLabels, columnCount ),
				rows: rows,
			} );
		} );

		return Object.assign( {}, tab, { columns: columns, groups: groups } );
	}

	function nextCellType( type ) {
		if ( type === 'none' ) {
			return 'check';
		}
		if ( type === 'check' ) {
			return 'text';
		}
		return 'none';
	}

	function tabHasContent( tab ) {
		if ( ! tab ) {
			return false;
		}
		if ( tab.label || tab.cornerLabel ) {
			return true;
		}
		const columns = Array.isArray( tab.columns ) ? tab.columns : [];
		for ( let i = 0; i < columns.length; i++ ) {
			if ( columns[ i ].title || columns[ i ].subtitle ) {
				return true;
			}
		}
		const groups = Array.isArray( tab.groups ) ? tab.groups : [];
		for ( let g = 0; g < groups.length; g++ ) {
			const group = groups[ g ];
			if ( group.title ) {
				return true;
			}
			const labels = Array.isArray( group.columnLabels ) ? group.columnLabels : [];
			for ( let l = 0; l < labels.length; l++ ) {
				if ( labels[ l ] ) {
					return true;
				}
			}
			const rows = Array.isArray( group.rows ) ? group.rows : [];
			for ( let r = 0; r < rows.length; r++ ) {
				const row = rows[ r ];
				if ( row.label ) {
					return true;
				}
				const cells = Array.isArray( row.cells ) ? row.cells : [];
				for ( let c = 0; c < cells.length; c++ ) {
					const cell = cells[ c ] || {};
					if ( cell.type === 'check' ) {
						return true;
					}
					if ( cell.type === 'text' && cell.text ) {
						return true;
					}
				}
			}
		}
		return false;
	}

	function renderCell( cell, colIndex, onCellChange ) {
		const type = cell && cell.type ? cell.type : 'none';
		const text = cell && cell.text ? cell.text : '';

		if ( type === 'check' ) {
			return el(
				'button',
				{
					type: 'button',
					className: 'tabbed-comparison-table__cell-btn -check',
					onClick: function () {
						onCellChange( { type: nextCellType( type ), text: text } );
					},
				},
				CHECK_ICON
			);
		}

		if ( type === 'text' ) {
			return el(
				'div',
				{ className: 'tabbed-comparison-table__cell-edit', contentEditable: false },
				el( RichText, {
					tagName: 'span',
					className: 'tabbed-comparison-table__cell-text',
					value: text,
					allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
					placeholder: __( 'Custom value', 'chw' ),
					onChange: function ( value ) {
						onCellChange( { type: 'text', text: value } );
					},
				} ),
				el(
					Button,
					{
						isSmall: true,
						variant: 'secondary',
						onClick: function () {
							onCellChange( { type: nextCellType( type ), text: '' } );
						},
					},
					__( 'Cycle cell', 'chw' )
				)
			);
		}

		return el(
			'button',
			{
				type: 'button',
				className: 'tabbed-comparison-table__cell-btn -empty',
				onClick: function () {
					onCellChange( { type: nextCellType( type ), text: '' } );
				},
				'aria-label': __( 'Empty cell — click to add checkmark', 'chw' ),
			},
			'—'
		);
	}

	function moveColumnInTab( tab, colIndex, dir ) {
		const target = colIndex + dir;
		const columns = Array.isArray( tab.columns ) ? tab.columns.slice() : emptyColumns();
		if ( target < 0 || target >= columns.length ) {
			return tab;
		}
		const tmpCol = columns[ colIndex ];
		columns[ colIndex ] = columns[ target ];
		columns[ target ] = tmpCol;

		const groups = ( Array.isArray( tab.groups ) ? tab.groups : [] ).map( function ( group ) {
			const labels = resizeColumnLabels( group.columnLabels, columns.length );
			const tmpLabel = labels[ colIndex ];
			labels[ colIndex ] = labels[ target ];
			labels[ target ] = tmpLabel;

			const rows = ( Array.isArray( group.rows ) ? group.rows : [] ).map( function ( row ) {
				const cells = resizeRowCells( row.cells, columns.length );
				const tmpCell = cells[ colIndex ];
				cells[ colIndex ] = cells[ target ];
				cells[ target ] = tmpCell;
				return Object.assign( {}, row, { cells: cells } );
			} );

			return Object.assign( {}, group, { columnLabels: labels, rows: rows } );
		} );

		return Object.assign( {}, tab, { columns: columns, groups: groups } );
	}

	function renderTable( tab, tabIndex, tabs, setTabs, activeTabIndex, setActiveTabIndex, showTablist ) {
		const columns = Array.isArray( tab.columns ) ? tab.columns : emptyColumns();
		const groups = Array.isArray( tab.groups ) ? tab.groups : [];
		const columnCount = columns.length;

		function setTab( patch ) {
			setTabs( updateRow( tabs, tabIndex, patch ) );
		}

		function setColumns( nextColumns ) {
			setTabs(
				updateRow( tabs, tabIndex, syncTabColumnCount( Object.assign( {}, tab, { columns: nextColumns } ), nextColumns.length ) )
			);
		}

		function setGroups( nextGroups ) {
			setTab( { groups: nextGroups } );
		}

		const groupRows = groups.map( function ( group, groupIndex ) {
			const columnLabels = Array.isArray( group.columnLabels ) ? group.columnLabels : [];
			const rows = Array.isArray( group.rows ) ? group.rows : [];

			const featureRows = rows.map( function ( row, rowIndex ) {
				const cells = Array.isArray( row.cells ) ? row.cells : [];

				return el(
					'tr',
					{
						key: row.id || 'row-' + tabIndex + '-' + groupIndex + '-' + rowIndex,
						className: 'tabbed-comparison-table__feature-row' + ( rowIndex % 2 === 0 ? ' -even' : '' ),
					},
					el(
						'th',
						{ scope: 'row', className: 'tabbed-comparison-table__feature-label' },
						el( RichText, {
							tagName: 'span',
							value: row.label || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Feature', 'chw' ),
							onChange: function ( value ) {
								const nextRows = updateRow( rows, rowIndex, { label: value } );
								setGroups( updateRow( groups, groupIndex, { rows: nextRows } ) );
							},
						} )
					),
					columns.map( function ( column, colIndex ) {
						const cell = cells[ colIndex ] || emptyCell();
						const variant = columnVariant( colIndex );

						return el(
							'td',
							{
								key: 'cell-' + rowIndex + '-' + colIndex,
								className: 'tabbed-comparison-table__cell -' + variant,
							},
							renderCell( cell, colIndex, function ( patch ) {
								const nextCells = cells.slice();
								nextCells[ colIndex ] = Object.assign( {}, cell, patch );
								const nextRows = updateRow( rows, rowIndex, { cells: nextCells } );
								setGroups( updateRow( groups, groupIndex, { rows: nextRows } ) );
							} )
						);
					} ),
					el(
						'td',
						{ className: 'tabbed-comparison-table__row-actions', contentEditable: false },
						el( Button, {
							icon: 'arrow-up-alt2',
							label: __( 'Move row up', 'chw' ),
							disabled: rowIndex === 0,
							isSmall: true,
							onClick: function () {
								const nextRows = moveRow( rows, rowIndex, -1 );
								setGroups( updateRow( groups, groupIndex, { rows: nextRows } ) );
							},
						} ),
						el( Button, {
							icon: 'arrow-down-alt2',
							label: __( 'Move row down', 'chw' ),
							disabled: rowIndex === rows.length - 1,
							isSmall: true,
							onClick: function () {
								const nextRows = moveRow( rows, rowIndex, 1 );
								setGroups( updateRow( groups, groupIndex, { rows: nextRows } ) );
							},
						} ),
						el( Button, {
							icon: 'admin-page',
							label: __( 'Duplicate row', 'chw' ),
							isSmall: true,
							onClick: function () {
								const nextRows = duplicateRow( rows, rowIndex );
								setGroups( updateRow( groups, groupIndex, { rows: nextRows } ) );
							},
						} ),
						el( Button, {
							icon: 'trash',
							isDestructive: true,
							label: __( 'Remove row', 'chw' ),
							isSmall: true,
							onClick: function () {
								const nextRows = removeRow( rows, rowIndex );
								setGroups( updateRow( groups, groupIndex, { rows: nextRows } ) );
							},
						} )
					)
				);
			} );

			return el(
				Fragment,
				{ key: group.id || 'group-' + tabIndex + '-' + groupIndex },
				el(
					'tr',
					{ className: 'tabbed-comparison-table__group-row' },
					el(
						'th',
						{ scope: 'row', className: 'tabbed-comparison-table__group-label' },
						el( RichText, {
							tagName: 'span',
							value: group.title || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Group title', 'chw' ),
							onChange: function ( value ) {
								setGroups( updateRow( groups, groupIndex, { title: value } ) );
							},
						} )
					),
					columns.map( function ( column, colIndex ) {
						const variant = columnVariant( colIndex );
						return el(
							'td',
							{
								key: 'group-col-' + groupIndex + '-' + colIndex,
								className: 'tabbed-comparison-table__group-col -' + variant,
							},
							el( RichText, {
								tagName: 'span',
								value: columnLabels[ colIndex ] || '',
								allowedFormats: [ 'core/bold', 'core/italic' ],
								placeholder: __( 'Column label', 'chw' ),
								onChange: function ( value ) {
									const nextLabels = resizeColumnLabels( columnLabels, columnCount );
									nextLabels[ colIndex ] = value;
									setGroups( updateRow( groups, groupIndex, { columnLabels: nextLabels } ) );
								},
							} )
						);
					} ),
					el( 'td', { className: 'tabbed-comparison-table__row-actions', contentEditable: false } )
				),
				featureRows,
				el(
					'tr',
					{ className: 'tabbed-comparison-table__add-row', contentEditable: false },
					el(
						'td',
						{ colSpan: columnCount + 2 },
						el(
							Button,
							{
								variant: 'secondary',
								isSmall: true,
								onClick: function () {
									const nextRows = rows.concat( [ emptyRow( columnCount ) ] );
									setGroups( updateRow( groups, groupIndex, { rows: nextRows } ) );
								},
							},
							__( 'Add row', 'chw' )
						),
						el(
							Button,
							{
								icon: 'trash',
								isDestructive: true,
								isSmall: true,
								label: __( 'Remove group', 'chw' ),
								onClick: function () {
									setGroups( removeRow( groups, groupIndex ) );
								},
							}
						)
					)
				)
			);
		} );

		return el(
			'div',
			{
				className: 'tabbed-comparison-table__scroll',
				hidden: showTablist && tabIndex !== activeTabIndex ? true : undefined,
			},
			el(
				'table',
				{
					className: 'tabbed-comparison-table__table',
					'data-columns': columnCount,
				},
				el(
					'thead',
					null,
					el(
						'tr',
						{ className: 'tabbed-comparison-table__head-row' },
						el(
							'th',
							{ scope: 'col', className: 'tabbed-comparison-table__corner' },
							el( RichText, {
								tagName: 'span',
								value: tab.cornerLabel || '',
								allowedFormats: [ 'core/bold', 'core/italic' ],
								placeholder: __( 'Corner label', 'chw' ),
								onChange: function ( value ) {
									setTab( { cornerLabel: value } );
								},
							} )
						),
						columns.map( function ( column, colIndex ) {
							const variant = columnVariant( colIndex );
							return el(
								'th',
								{
									key: column.id || 'col-' + colIndex,
									scope: 'col',
									className: 'tabbed-comparison-table__plan-head -' + variant,
								},
								el( RichText, {
									tagName: 'span',
									className: 'tabbed-comparison-table__plan-title',
									value: column.title || '',
									allowedFormats: [ 'core/bold', 'core/italic' ],
									placeholder: __( 'Plan name', 'chw' ),
									onChange: function ( value ) {
										const next = updateRow( columns, colIndex, { title: value } );
										setColumns( next );
									},
								} ),
								el( RichText, {
									tagName: 'span',
									className: 'tabbed-comparison-table__plan-subtitle',
									value: column.subtitle || '',
									allowedFormats: [ 'core/bold', 'core/italic' ],
									placeholder: __( 'Subtitle', 'chw' ),
									onChange: function ( value ) {
										const next = updateRow( columns, colIndex, { subtitle: value } );
										setColumns( next );
									},
								} ),
								el(
									'div',
									{ className: 'tabbed-comparison-table__col-actions', contentEditable: false },
									el( Button, {
										icon: 'arrow-left-alt2',
										label: __( 'Move column left', 'chw' ),
										disabled: colIndex === 0,
										isSmall: true,
										onClick: function () {
											setTabs(
												updateRow( tabs, tabIndex, moveColumnInTab( tab, colIndex, -1 ) )
											);
										},
									} ),
									el( Button, {
										icon: 'arrow-right-alt2',
										label: __( 'Move column right', 'chw' ),
										disabled: colIndex === columns.length - 1,
										isSmall: true,
										onClick: function () {
											setTabs(
												updateRow( tabs, tabIndex, moveColumnInTab( tab, colIndex, 1 ) )
											);
										},
									} ),
									columns.length > MIN_COLUMNS
										? el( Button, {
												icon: 'trash',
												isDestructive: true,
												label: __( 'Remove column', 'chw' ),
												isSmall: true,
												onClick: function () {
													setColumns( removeRow( columns, colIndex ) );
												},
										  } )
										: null
								)
							);
						} ),
						el( 'th', { className: 'tabbed-comparison-table__row-actions', contentEditable: false } )
					)
				),
				el( 'tbody', null, groupRows )
			),
			el(
				'div',
				{ className: 'tabbed-comparison-table__add', contentEditable: false },
				columns.length < MAX_COLUMNS
					? el(
							Button,
							{
								variant: 'secondary',
								isSmall: true,
								onClick: function () {
									const next = columns.concat( [ { id: uid( 'col' ), title: '', subtitle: '' } ] );
									setColumns( next );
								},
							},
							__( 'Add column', 'chw' )
					  )
					: null,
				el(
					Button,
					{
						variant: 'secondary',
						isSmall: true,
						onClick: function () {
							setTab( { groups: groups.concat( [ emptyGroup( columnCount ) ] ) } );
						},
					},
					__( 'Add group', 'chw' )
				)
			)
		);
	}

	registerBlockType( 'chw/tabbed-comparison-table', {
		title: __( 'Tabbed Comparison Table', 'chw' ),
		description: __(
			'Matrix-style plan comparison with optional tabs for regional or alternate coverage tables.',
			'chw'
		),
		category: 'choice-home-warranty',
		icon: 'editor-table',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			tabs: { type: 'array', default: [] },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const tabs = Array.isArray( attributes.tabs ) ? attributes.tabs : [];
			const blockProps = useBlockProps( { className: 'tabbed-comparison-table' } );

			const [ activeTabIndex, setActiveTabIndex ] = useState( 0 );

			useEffect( function () {
				if ( tabs.length >= MIN_TABS ) {
					return;
				}
				setAttributes( { tabs: [ emptyTab() ] } );
			}, [] );

			useEffect( function () {
				if ( activeTabIndex >= tabs.length ) {
					setActiveTabIndex( Math.max( 0, tabs.length - 1 ) );
				}
			}, [ tabs.length, activeTabIndex ] );

			function setTabs( nextTabs ) {
				setAttributes( { tabs: nextTabs } );
			}

			const showTablist = tabs.length >= 2;

			const tabButtons = showTablist
				? tabs.map( function ( tab, index ) {
						return el(
							'button',
							{
								key: tab.id || 'tab-btn-' + index,
								type: 'button',
								className: '_tab' + ( index === activeTabIndex ? ' -active' : '' ),
								onClick: function () {
									setActiveTabIndex( index );
								},
							},
							el( RichText, {
								tagName: 'span',
								value: tab.label || '',
								allowedFormats: [],
								placeholder: __( 'Tab label', 'chw' ),
								onChange: function ( value ) {
									setTabs( updateRow( tabs, index, { label: value } ) );
								},
							} )
						);
				  } )
				: null;

			const tabPanels = tabs.map( function ( tab, index ) {
				return el(
					'div',
					{
						key: tab.id || 'tab-pane-' + index,
						className:
							'tabbed-comparison-table__pane' +
							( showTablist ? ' _pane' : '' ) +
							( index === activeTabIndex ? ' -active' : '' ),
						hidden: showTablist && index !== activeTabIndex ? true : undefined,
					},
					renderTable( tab, index, tabs, setTabs, activeTabIndex, setActiveTabIndex, showTablist )
				);
			} );

			const inspectorTabRows = tabs.map( function ( tab, index ) {
				return el(
					'div',
					{ key: 'tab-set-' + index, className: 'chw-masthead-repeater-row' },
					el(
						'div',
						{ className: 'chw-masthead-repeater-row__head' },
						el( 'span', null, ( tab.label || __( 'Tab', 'chw' ) ) + ' ' + ( index + 1 ) ),
						el(
							'div',
							{ className: 'chw-masthead-repeater-row__actions' },
							el( Button, {
								icon: 'arrow-left-alt2',
								label: __( 'Move tab left', 'chw' ),
								disabled: index === 0,
								onClick: function () {
									setTabs( moveRow( tabs, index, -1 ) );
									if ( activeTabIndex === index ) {
										setActiveTabIndex( index - 1 );
									} else if ( activeTabIndex === index - 1 ) {
										setActiveTabIndex( index );
									}
								},
							} ),
							el( Button, {
								icon: 'arrow-right-alt2',
								label: __( 'Move tab right', 'chw' ),
								disabled: index === tabs.length - 1,
								onClick: function () {
									setTabs( moveRow( tabs, index, 1 ) );
									if ( activeTabIndex === index ) {
										setActiveTabIndex( index + 1 );
									} else if ( activeTabIndex === index + 1 ) {
										setActiveTabIndex( index );
									}
								},
							} ),
							tabs.length > MIN_TABS
								? el( Button, {
										icon: 'trash',
										isDestructive: true,
										label: __( 'Remove tab', 'chw' ),
										onClick: function () {
											setTabs( removeRow( tabs, index ) );
										},
								  } )
								: null
						)
					),
					el( TextControl, {
						label: __( 'Tab label', 'chw' ),
						value: tab.label || '',
						onChange: function ( value ) {
							setTabs( updateRow( tabs, index, { label: value } ) );
						},
					} ),
					el( TextControl, {
						label: __( 'Corner label', 'chw' ),
						value: tab.cornerLabel || '',
						onChange: function ( value ) {
							setTabs( updateRow( tabs, index, { cornerLabel: value } ) );
						},
					} )
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
						{ title: __( 'Tabs', 'chw' ), initialOpen: true },
						el(
							BaseControl,
							{
								help: __(
									'Tabs appear on the front end only when two or more tabs have content. Use tabs for regional or alternate coverage tables.',
									'chw'
								),
							},
							inspectorTabRows
						),
						el(
							Button,
							{
								variant: 'secondary',
								className: 'chw-editor-add-button',
								onClick: function () {
									setTabs( tabs.concat( [ emptyTab() ] ) );
									setActiveTabIndex( tabs.length );
								},
							},
							__( 'Add tab', 'chw' )
						)
					)
				),
				el(
					'section',
					blockProps,
					el(
						'div',
						{ className: 'tabbed-comparison-table__inner' },
						el(
							'div',
							{
								className:
									'tabbed-comparison-table__body' +
									( showTablist ? ' -has-tabs' : ' -single-tab' ),
							},
							showTablist
								? el(
										'div',
										{
											className: 'tabbed-comparison-table__tabs _tabs -flex',
											role: 'tablist',
										},
										tabButtons
								  )
								: null,
							el(
								'div',
								{
									className:
										'tabbed-comparison-table__panes' + ( showTablist ? ' _panes' : '' ),
								},
								tabPanels
							)
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
