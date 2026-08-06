( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, BaseControl, Button, TextControl, Spinner } = wp.components;
	const { createElement: el, Fragment, useState, useEffect } = wp.element;
	const { __ } = wp.i18n;
	const apiFetch = wp.apiFetch;

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

	function getInitial( name ) {
		const trimmed = ( name || '' ).trim();
		if ( ! trimmed ) {
			return '';
		}
		return trimmed.charAt( 0 ).toUpperCase();
	}

	function stripHtml( html ) {
		const div = document.createElement( 'div' );
		div.innerHTML = html || '';
		return div.textContent || div.innerText || '';
	}

	function getLocationFromPost( post ) {
		if ( ! post || ! post._embedded || ! post._embedded[ 'wp:term' ] ) {
			return '';
		}

		const termGroups = post._embedded[ 'wp:term' ];
		for ( let i = 0; i < termGroups.length; i++ ) {
			const terms = termGroups[ i ];
			if ( ! Array.isArray( terms ) ) {
				continue;
			}
			const locationTerms = terms.filter( function ( term ) {
				return term && term.taxonomy === 'location';
			} );
			if ( locationTerms.length ) {
				return locationTerms.map( function ( term ) {
					return term.name;
				} ).join( ', ' );
			}
		}

		return '';
	}

	function sortPostsByIds( posts, ids ) {
		const map = {};
		posts.forEach( function ( post ) {
			map[ post.id ] = post;
		} );
		return ids.map( function ( id ) {
			return map[ id ];
		} ).filter( Boolean );
	}

	function starPreview() {
		const stars = [];
		for ( let i = 0; i < 5; i++ ) {
			stars.push(
				el(
					'svg',
					{
						key: 'star-' + i,
						className: 'testimonials-slider__star',
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
			{
				className: 'testimonials-slider__stars',
				role: 'img',
				'aria-label': __( 'Rated 5 out of 5 stars', 'chw' ),
				contentEditable: false,
			},
			stars
		);
	}

	function testimonialCard( post, key ) {
		const name = stripHtml( post.title && post.title.rendered ? post.title.rendered : '' );
		const location = getLocationFromPost( post );
		const initial = getInitial( name );
		const quoteHtml = post.content && post.content.rendered ? post.content.rendered : '';

		return el(
			'figure',
			{
				key: key,
				className: 'testimonials-slider__card',
				contentEditable: false,
			},
			starPreview(),
			quoteHtml
				? el( 'div', {
					className: 'testimonials-slider__quote',
					dangerouslySetInnerHTML: { __html: quoteHtml },
				} )
				: null,
			el(
				'figcaption',
				{ className: 'testimonials-slider__author' },
				initial
					? el(
						'div',
						{ className: 'testimonials-slider__avatar', 'aria-hidden': true },
						el( 'span', null, initial )
					)
					: null,
				el(
					'div',
					{ className: 'testimonials-slider__author-text' },
					name
						? el( 'div', { className: 'testimonials-slider__name' }, name )
						: null,
					location
						? el( 'div', { className: 'testimonials-slider__location' }, location )
						: null
				)
			)
		);
	}

	function TestimonialsSliderEdit( props ) {
		const attributes = props.attributes;
		const setAttributes = props.setAttributes;
		const testimonialIds = Array.isArray( attributes.testimonialIds ) ? attributes.testimonialIds : [];

		const searchState = useState( '' );
		const searchQuery = searchState[ 0 ];
		const setSearchQuery = searchState[ 1 ];

		const resultsState = useState( [] );
		const searchResults = resultsState[ 0 ];
		const setSearchResults = resultsState[ 1 ];

		const searchingState = useState( false );
		const isSearching = searchingState[ 0 ];
		const setIsSearching = searchingState[ 1 ];

		const postsState = useState( [] );
		const selectedPosts = postsState[ 0 ];
		const setSelectedPosts = postsState[ 1 ];

		const loadingState = useState( false );
		const isLoadingPosts = loadingState[ 0 ];
		const setIsLoadingPosts = loadingState[ 1 ];

		const blockProps = useBlockProps( { className: 'testimonials-slider' } );

		useEffect( function () {
			if ( ! testimonialIds.length ) {
				setSelectedPosts( [] );
				return;
			}

			let cancelled = false;
			setIsLoadingPosts( true );

			apiFetch( {
				path: '/wp/v2/testimonial?include=' + testimonialIds.join( ',' ) + '&per_page=100&_embed=wp:term',
			} )
				.then( function ( posts ) {
					if ( cancelled ) {
						return;
					}
					setSelectedPosts( sortPostsByIds( posts, testimonialIds ) );
				} )
				.catch( function () {
					if ( ! cancelled ) {
						setSelectedPosts( [] );
					}
				} )
				.finally( function () {
					if ( ! cancelled ) {
						setIsLoadingPosts( false );
					}
				} );

			return function () {
				cancelled = true;
			};
		}, [ testimonialIds.join( ',' ) ] );

		useEffect( function () {
			if ( ! searchQuery || searchQuery.length < 2 ) {
				setSearchResults( [] );
				return;
			}

			let cancelled = false;
			const timer = window.setTimeout( function () {
				setIsSearching( true );
				apiFetch( {
					path: '/wp/v2/testimonial?search=' + encodeURIComponent( searchQuery ) + '&per_page=20&status=publish',
				} )
					.then( function ( posts ) {
						if ( ! cancelled ) {
							setSearchResults( posts );
						}
					} )
					.catch( function () {
						if ( ! cancelled ) {
							setSearchResults( [] );
						}
					} )
					.finally( function () {
						if ( ! cancelled ) {
							setIsSearching( false );
						}
					} );
			}, 300 );

			return function () {
				cancelled = true;
				window.clearTimeout( timer );
			};
		}, [ searchQuery ] );

		function setTestimonialIds( ids ) {
			setAttributes( { testimonialIds: ids } );
		}

		function addTestimonial( post ) {
			if ( ! post || ! post.id || testimonialIds.indexOf( post.id ) !== -1 ) {
				return;
			}
			setTestimonialIds( testimonialIds.concat( [ post.id ] ) );
			setSearchQuery( '' );
			setSearchResults( [] );
		}

		function selectedList() {
			if ( ! testimonialIds.length ) {
				return el(
					'p',
					{ style: { margin: 0, fontSize: '12px', color: '#757575' } },
					__( 'No testimonials selected.', 'chw' )
				);
			}

			return testimonialIds.map( function ( id, index ) {
				const post = selectedPosts.find( function ( item ) {
					return item.id === id;
				} );
				const title = post && post.title && post.title.rendered
					? stripHtml( post.title.rendered )
					: __( 'Testimonial #%d', 'chw' ).replace( '%d', String( id ) );

				return el(
					'div',
					{ key: 'selected-' + id, className: 'testimonials-slider__selected-item' },
					el( 'span', { className: 'testimonials-slider__selected-title' }, title ),
					el(
						'div',
						{ className: 'testimonials-slider__selected-actions' },
						el( Button, {
							icon: 'arrow-up-alt2',
							label: __( 'Move up', 'chw' ),
							disabled: index === 0,
							onClick: function () {
								setTestimonialIds( moveRow( testimonialIds, index, -1 ) );
							},
						} ),
						el( Button, {
							icon: 'arrow-down-alt2',
							label: __( 'Move down', 'chw' ),
							disabled: index === testimonialIds.length - 1,
							onClick: function () {
								setTestimonialIds( moveRow( testimonialIds, index, 1 ) );
							},
						} ),
						el( Button, {
							icon: 'trash',
							isDestructive: true,
							label: __( 'Remove testimonial', 'chw' ),
							onClick: function () {
								setTestimonialIds( removeRow( testimonialIds, index ) );
							},
						} )
					)
				);
			} );
		}

		function searchResultsList() {
			if ( ! searchQuery || searchQuery.length < 2 ) {
				return null;
			}

			if ( isSearching ) {
				return el( Spinner, null );
			}

			if ( ! searchResults.length ) {
				return el(
					'p',
					{ style: { margin: '0.5rem 0 0', fontSize: '12px', color: '#757575' } },
					__( 'No testimonials found.', 'chw' )
				);
			}

			return el(
				'div',
				{ className: 'testimonials-slider__search-results' },
				searchResults.map( function ( post ) {
					const title = stripHtml( post.title && post.title.rendered ? post.title.rendered : '' );
					const isSelected = testimonialIds.indexOf( post.id ) !== -1;

					return el(
						'button',
						{
							key: 'result-' + post.id,
							type: 'button',
							className: 'testimonials-slider__search-result',
							disabled: isSelected,
							onClick: function () {
								addTestimonial( post );
							},
						},
						isSelected ? title + ' ' + __( '(added)', 'chw' ) : title
					);
				} )
			);
		}

		function canvasPreview() {
			if ( isLoadingPosts && testimonialIds.length ) {
				return el( Spinner, null );
			}

			if ( ! selectedPosts.length ) {
				return el(
					'div',
					{ className: 'testimonials-slider__empty' },
					__( 'Select testimonials from the block sidebar to display them here.', 'chw' )
				);
			}

			return el(
				'div',
				{ className: 'testimonials-slider__preview-cards' },
				selectedPosts.map( function ( post ) {
					return testimonialCard( post, 'preview-' + post.id );
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
					{ title: __( 'Testimonials', 'chw' ), initialOpen: true },
					el(
						BaseControl,
						{ label: __( 'Search testimonials', 'chw' ) },
						el( TextControl, {
							value: searchQuery,
							onChange: setSearchQuery,
							placeholder: __( 'Search by name…', 'chw' ),
						} ),
						searchResultsList()
					),
					el(
						BaseControl,
						{ label: __( 'Selected testimonials', 'chw' ) },
						selectedList()
					)
				)
			),
			el(
				'section',
				blockProps,
				el(
					'div',
					{ className: '_container' },
					canvasPreview()
				)
			)
		);
	}

	registerBlockType( 'chw/testimonials-slider', {
		title: __( 'Testimonials Slider', 'chw' ),
		description: __( 'A carousel of testimonial posts with star ratings and author details.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'format-quote',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			testimonialIds: { type: 'array', default: [] },
		},
		edit: TestimonialsSliderEdit,
		save: function () {
			return null;
		},
	} );
}( window.wp ) );
