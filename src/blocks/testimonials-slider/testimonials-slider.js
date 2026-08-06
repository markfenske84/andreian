document.querySelectorAll( '.testimonials-slider' ).forEach( ( section ) => {
	if ( typeof Swiper === 'undefined' ) {
		return;
	}

	const swiperEl = section.querySelector( '.swiper' );
	const prevEl = section.querySelector( '.testimonials-slider__prev' );
	const nextEl = section.querySelector( '.testimonials-slider__next' );
	const navEl = section.querySelector( '.testimonials-slider__nav' );

	if ( ! swiperEl || ! prevEl || ! nextEl ) {
		return;
	}

	const slideCount = swiperEl.querySelectorAll( '.swiper-wrapper > .swiper-slide' ).length;
	const enableLoop = slideCount > 1;

	if ( ! enableLoop && navEl ) {
		navEl.hidden = true;
		return;
	}

	new Swiper( swiperEl, {
		loop: enableLoop,
		loopedSlides: slideCount,
		slidesPerView: 1,
		spaceBetween: 24,
		breakpoints: {
			768: {
				slidesPerView: 2,
			},
			992: {
				slidesPerView: 3,
			},
		},
		navigation: {
			prevEl,
			nextEl,
		},
		a11y: {
			enabled: true,
			prevSlideMessage: 'Previous testimonial',
			nextSlideMessage: 'Next testimonial',
		},
	} );
} );
