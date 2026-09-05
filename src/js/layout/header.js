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
	const searchPanel = document.querySelector('#site-search-panel');
	const searchToggle = document.querySelector('.site-search-toggle');
	const searchField = searchPanel ? searchPanel.querySelector('.search-field') : null;
	let syncNavHeight = function () {};

	function syncAdminBarHeight() {
		const adminBar = document.getElementById('wpadminbar');
		const height = adminBar ? Math.round(adminBar.getBoundingClientRect().height) : 0;
		document.documentElement.style.setProperty('--admin-bar-height', height + 'px');
	}

	syncAdminBarHeight();
	window.addEventListener('resize', syncAdminBarHeight);

	if (typeof ResizeObserver !== 'undefined') {
		const adminBar = document.getElementById('wpadminbar');
		if (adminBar) {
			new ResizeObserver(syncAdminBarHeight).observe(adminBar);
		}
	}

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

	function setSearchState(isOpen, options) {
		if (!searchPanel || !searchToggle) {
			return;
		}

		const restoreFocus = !options || options.restoreFocus !== false;
		searchPanel.classList.toggle('is-open', isOpen);
		searchPanel.setAttribute('aria-hidden', String(!isOpen));
		searchPanel.toggleAttribute('inert', !isOpen);
		document.body.classList.toggle('site-search-open', isOpen);
		searchToggle.setAttribute('aria-expanded', String(isOpen));
		searchToggle.setAttribute(
			'aria-label',
			isOpen
				? searchToggle.getAttribute('data-close-label') || 'Close search'
				: searchToggle.getAttribute('data-open-label') || 'Open search'
		);

		if (isOpen && searchField) {
			searchField.focus();
		} else if (!isOpen && restoreFocus) {
			searchToggle.focus();
		}

		syncNavHeight();
	}

	if (searchToggle) {
		searchToggle.addEventListener('click', function () {
			setSearchState(!searchPanel.classList.contains('is-open'), { restoreFocus: false });
		});
	}

	document.addEventListener('click', function (event) {
		if (mobileOffcanvas && !mobileOffcanvas.hidden && event.target === mobileOffcanvas) {
			setMenuState(false);
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape' && searchPanel && searchPanel.classList.contains('is-open')) {
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

		syncNavHeight = function () {
			const height = stickyNav.offsetHeight;
			spacer.style.height = height + 'px';
			document.documentElement.style.setProperty('--nav-bar-height', height + 'px');
			document.documentElement.style.setProperty(
				'--header-height',
				headerBrand.offsetHeight + height + 'px'
			);
		};

		function updateStickyNav() {
			const currentScrollY = Math.max(0, window.scrollY);
			const delta = currentScrollY - lastScrollY;
			if (document.body.classList.contains('mobile-offcanvas-open')) {
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

		if (typeof ResizeObserver !== 'undefined') {
			new ResizeObserver(function () {
				syncNavHeight();
			}).observe(stickyNav);
		}
	}
});
