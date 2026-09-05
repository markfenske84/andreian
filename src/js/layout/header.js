document.addEventListener('DOMContentLoaded', function () {
	function insertToggleButton(menuItem) {
		if (menuItem.querySelector('.toggle-button')) {
			return;
		}

		const button = document.createElement('button');
		button.type = 'button';
		button.classList.add('toggle-button');
		button.setAttribute('aria-expanded', 'false');
		button.innerHTML =
			'<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m6 9 6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path></svg>';

		const link = menuItem.querySelector('a');
		if (link) {
			button.setAttribute('aria-label', `Toggle submenu for ${link.textContent.trim()}`);
		}

		const subMenu = menuItem.querySelector('.sub-menu');
		if (subMenu) {
			menuItem.insertBefore(button, subMenu);
		}
	}

	function bindDropdownToggle(menuItem) {
		const button = menuItem.querySelector('.toggle-button');
		const subMenu = menuItem.querySelector('.sub-menu');
		if (!button || !subMenu) {
			return;
		}

		button.addEventListener('click', function (event) {
			event.preventDefault();
			const isOpen = subMenu.classList.toggle('show');
			menuItem.classList.toggle('open', isOpen);
			button.setAttribute('aria-expanded', String(isOpen));
		});
	}

	document.querySelectorAll('#mobile-offcanvas .menu-item-has-children').forEach(function (item) {
		insertToggleButton(item);
		bindDropdownToggle(item);
	});

	document.querySelectorAll('.site-header .menu-item-has-children').forEach(function (item) {
		insertToggleButton(item);
		bindDropdownToggle(item);
	});

	const mobileOffcanvas = document.querySelector('#mobile-offcanvas');
	const openToggle = document.querySelector(
		'.site-header__navigation .mobile-offcanvas-toggle'
	);
	const closeToggle = mobileOffcanvas
		? mobileOffcanvas.querySelector('.mobile-offcanvas-toggle')
		: null;
	const searchOverlay = document.querySelector('#site-search-overlay');
	const searchToggle = document.querySelector('.site-search-toggle');
	const searchClose = searchOverlay
		? searchOverlay.querySelector('.site-search-overlay__close')
		: null;
	const searchField = searchOverlay
		? searchOverlay.querySelector('.search-field')
		: null;
	const searchPageRegions = document.querySelectorAll(
		'.site-header, #content, .site-footer, #mobile-offcanvas'
	);

	function prefersReducedMotion() {
		return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function setMenuState(isOpen) {
		if (!mobileOffcanvas || !openToggle) {
			return;
		}

		openToggle.setAttribute('aria-expanded', String(isOpen));
		document.body.classList.toggle('mobile-offcanvas-open', isOpen);

		if (isOpen) {
			mobileOffcanvas.hidden = false;
			window.requestAnimationFrame(function () {
				window.requestAnimationFrame(function () {
					mobileOffcanvas.classList.add('open');
				});
			});

			if (closeToggle) {
				closeToggle.focus();
			}

			return;
		}

		mobileOffcanvas.classList.remove('open');

		if (prefersReducedMotion()) {
			mobileOffcanvas.hidden = true;
			openToggle.focus();
			return;
		}

		mobileOffcanvas.addEventListener(
			'transitionend',
			function onMenuClose(event) {
				if (event.target !== mobileOffcanvas) {
					return;
				}

				mobileOffcanvas.hidden = true;
				mobileOffcanvas.removeEventListener('transitionend', onMenuClose);
			}
		);
		openToggle.focus();
	}

	if (openToggle) {
		openToggle.addEventListener('click', function () {
			setMenuState(true);
		});
	}

	if (closeToggle) {
		closeToggle.addEventListener('click', function () {
			setMenuState(false);
		});
	}

	function setSearchState(isOpen) {
		if (!searchOverlay || !searchToggle) {
			return;
		}

		searchOverlay.hidden = !isOpen;
		document.body.classList.toggle('site-search-open', isOpen);
		searchToggle.setAttribute('aria-expanded', String(isOpen));
		searchPageRegions.forEach(function (region) {
			region.toggleAttribute('inert', isOpen);
		});

		if (isOpen && searchField) {
			searchField.focus();
		} else if (!isOpen) {
			searchToggle.focus();
		}
	}

	if (searchToggle) {
		searchToggle.addEventListener('click', function () {
			setSearchState(true);
		});
	}

	if (searchClose) {
		searchClose.addEventListener('click', function () {
			setSearchState(false);
		});
	}

	document.addEventListener('click', function (event) {
		if (!mobileOffcanvas || mobileOffcanvas.hidden) {
			if (
				searchOverlay &&
				!searchOverlay.hidden &&
				(event.target === searchOverlay ||
					event.target === searchOverlay.querySelector('.site-search-overlay__inner'))
			) {
				setSearchState(false);
			}
			return;
		}
		if (
			event.target === mobileOffcanvas
		) {
			setMenuState(false);
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Tab' && searchOverlay && !searchOverlay.hidden) {
			const focusable = Array.from(
				searchOverlay.querySelectorAll(
					'button:not([disabled]), input:not([disabled]), a[href]'
				)
			);
			const first = focusable[0];
			const last = focusable[focusable.length - 1];

			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		}

		if (event.key === 'Escape' && searchOverlay && !searchOverlay.hidden) {
			setSearchState(false);
			return;
		}

		if (event.key === 'Escape' && mobileOffcanvas && !mobileOffcanvas.hidden) {
			setMenuState(false);
		}
	});

	const stickyNav = document.querySelector('.site-header__navigation');
	const headerBrand = document.querySelector('.site-header__brand');

	if (stickyNav && headerBrand) {
		const spacer = document.createElement('div');
		spacer.className = 'site-header__nav-spacer';
		spacer.setAttribute('aria-hidden', 'true');
		stickyNav.after(spacer);

		let lastScrollY = window.scrollY;
		let ticking = false;
		const scrollDelta = 6;

		function adminBarOffset() {
			const value = getComputedStyle(stickyNav).getPropertyValue('--admin-bar-height').trim();
			return parseFloat(value) || 0;
		}

		function syncNavHeight() {
			const height = stickyNav.offsetHeight;
			spacer.style.height = height + 'px';
			document.documentElement.style.setProperty('--nav-bar-height', height + 'px');
			document.documentElement.style.setProperty(
				'--header-height',
				headerBrand.offsetHeight + height + 'px'
			);
		}

		function updateStickyNav() {
			const currentScrollY = Math.max(0, window.scrollY);
			const delta = currentScrollY - lastScrollY;
			const overlaysOpen =
				document.body.classList.contains('site-search-open') ||
				document.body.classList.contains('mobile-offcanvas-open');

			if (overlaysOpen) {
				lastScrollY = currentScrollY;
				ticking = false;
				return;
			}

			const pastBrand = headerBrand.getBoundingClientRect().bottom <= adminBarOffset();

			if (!pastBrand) {
				stickyNav.classList.remove('is-stuck', 'is-scroll-hidden');
				spacer.classList.remove('is-active');
			} else {
				syncNavHeight();
				stickyNav.classList.add('is-stuck');
				spacer.classList.add('is-active');

				if (delta > scrollDelta) {
					stickyNav.classList.add('is-scroll-hidden');
				} else if (delta < -scrollDelta) {
					stickyNav.classList.remove('is-scroll-hidden');
				}
			}

			lastScrollY = currentScrollY;
			ticking = false;
		}

		window.addEventListener(
			'scroll',
			function () {
				if (!ticking) {
					window.requestAnimationFrame(updateStickyNav);
					ticking = true;
				}
			},
			{ passive: true }
		);

		window.addEventListener('resize', function () {
			syncNavHeight();
			updateStickyNav();
		});

		syncNavHeight();
		updateStickyNav();
	}
});
