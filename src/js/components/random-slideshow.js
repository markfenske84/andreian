document.addEventListener( 'DOMContentLoaded', function () {
	const slideshows = document.querySelectorAll( '.andreian-random-slideshow' );

	slideshows.forEach( function ( slideshow ) {
		initRandomSlideshow( slideshow );
	} );
} );

function initRandomSlideshow( slideshow ) {
	const slides = Array.from( slideshow.querySelectorAll( '.andreian-random-slideshow__slide' ) );
	const dots = Array.from( slideshow.querySelectorAll( '.andreian-random-slideshow__dot' ) );
	const prevButton = slideshow.querySelector( '.andreian-random-slideshow__button--prev' );
	const nextButton = slideshow.querySelector( '.andreian-random-slideshow__button--next' );
	const liveRegion = slideshow.querySelector( '.andreian-random-slideshow__live' );
	const autoplayMs = parseInt( slideshow.dataset.autoplay, 10 ) || 6000;
	const prefersReducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	if ( slides.length <= 1 ) {
		return;
	}

	let currentIndex = slides.findIndex( function ( slide ) {
		return slide.classList.contains( 'is-active' );
	} );

	if ( currentIndex < 0 ) {
		currentIndex = 0;
	}

	let timerId = null;

	function setSlide( nextIndex ) {
		currentIndex = ( nextIndex + slides.length ) % slides.length;

		slides.forEach( function ( slide, index ) {
			const isActive = index === currentIndex;
			slide.classList.toggle( 'is-active', isActive );
			slide.setAttribute( 'aria-hidden', isActive ? 'false' : 'true' );
		} );

		dots.forEach( function ( dot, index ) {
			const isActive = index === currentIndex;
			dot.classList.toggle( 'is-active', isActive );
			dot.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
		} );

		if ( liveRegion ) {
			const heading = slides[ currentIndex ].querySelector( '.andreian-card__title' );
			liveRegion.textContent = heading ? heading.textContent.trim() : '';
		}
	}

	function nextSlide() {
		setSlide( currentIndex + 1 );
	}

	function prevSlide() {
		setSlide( currentIndex - 1 );
	}

	function stopAutoplay() {
		if ( timerId ) {
			window.clearInterval( timerId );
			timerId = null;
		}
	}

	function startAutoplay() {
		stopAutoplay();

		if ( prefersReducedMotion || autoplayMs <= 0 ) {
			return;
		}

		timerId = window.setInterval( nextSlide, autoplayMs );
	}

	if ( prevButton ) {
		prevButton.addEventListener( 'click', function () {
			prevSlide();
			startAutoplay();
		} );
	}

	if ( nextButton ) {
		nextButton.addEventListener( 'click', function () {
			nextSlide();
			startAutoplay();
		} );
	}

	dots.forEach( function ( dot ) {
		dot.addEventListener( 'click', function () {
			const targetIndex = parseInt( dot.dataset.slideIndex, 10 );

			if ( Number.isInteger( targetIndex ) ) {
				setSlide( targetIndex );
				startAutoplay();
			}
		} );
	} );

	slideshow.addEventListener( 'mouseenter', stopAutoplay );
	slideshow.addEventListener( 'mouseleave', startAutoplay );
	slideshow.addEventListener( 'focusin', stopAutoplay );
	slideshow.addEventListener( 'focusout', startAutoplay );

	startAutoplay();
}
