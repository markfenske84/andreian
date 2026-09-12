document.addEventListener('DOMContentLoaded', function () {
	const stickyNav = document.querySelector('.site-header__navigation');
	const tocs = Array.from(document.querySelectorAll('.andreian-toc'));
	const drawer = document.querySelector('[data-andreian-sidebar-drawer]');
	const sidebar = document.getElementById('andreian-post-sidebar');
	const entryContent = document.querySelector('.single-article .entry-content');
	const mobileQuery = window.matchMedia('(max-width: 992px)');

	function headingFromHash(href) {
		if (!href) {
			return null;
		}

		const hashIndex = href.indexOf('#');
		if (hashIndex === -1) {
			return null;
		}

		const id = decodeURIComponent(href.slice(hashIndex + 1));
		if (!id) {
			return null;
		}

		return document.getElementById(id);
	}

	function getScrollOffset() {
		const root = document.documentElement;
		const adminBar = parseFloat(getComputedStyle(root).getPropertyValue('--admin-bar-height')) || 0;
		const navBar = parseFloat(getComputedStyle(root).getPropertyValue('--nav-bar-height')) || 46;

		return adminBar + navBar + 16;
	}

	function scrollToHeading(target, href) {
		if (!target) {
			return;
		}

		if (stickyNav) {
			stickyNav.classList.remove('is-scroll-hidden');
		}

		const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		const top = target.getBoundingClientRect().top + window.scrollY - getScrollOffset();

		window.scrollTo({
			top: Math.max(0, top),
			behavior: reduceMotion ? 'auto' : 'smooth',
		});

		if (href) {
			history.pushState(null, '', href);
		}
	}

	function setDrawerOpen(isOpen) {
		if (!drawer) {
			return;
		}

		const tab = drawer.querySelector('.andreian-sidebar-drawer__tab');
		const backdrop = drawer.querySelector('.andreian-sidebar-drawer__backdrop');

		if (!mobileQuery.matches) {
			isOpen = false;
		}

		drawer.classList.toggle('is-open', isOpen);
		document.body.classList.toggle('andreian-sidebar-drawer-open', isOpen);

		if (tab) {
			tab.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			tab.setAttribute(
				'aria-label',
				isOpen ? tab.dataset.closeLabel : tab.dataset.openLabel
			);
		}

		if (backdrop) {
			backdrop.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
		}

		if (sidebar) {
			if (mobileQuery.matches) {
				sidebar.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
				sidebar.toggleAttribute('inert', !isOpen);

				if (isOpen) {
					sidebar.setAttribute('role', 'dialog');
					sidebar.setAttribute('aria-modal', 'true');
				} else {
					sidebar.removeAttribute('role');
					sidebar.removeAttribute('aria-modal');
				}
			} else {
				sidebar.removeAttribute('aria-hidden');
				sidebar.removeAttribute('inert');
				sidebar.removeAttribute('role');
				sidebar.removeAttribute('aria-modal');
			}
		}
	}

	if (drawer) {
		const tab = drawer.querySelector('.andreian-sidebar-drawer__tab');
		const backdrop = drawer.querySelector('.andreian-sidebar-drawer__backdrop');

		if (tab) {
			tab.addEventListener('click', function () {
				setDrawerOpen(!drawer.classList.contains('is-open'));
			});
		}

		if (backdrop) {
			backdrop.addEventListener('click', function () {
				setDrawerOpen(false);
			});
		}

		document.addEventListener('keydown', function (event) {
			if ('Escape' === event.key && drawer.classList.contains('is-open')) {
				setDrawerOpen(false);
			}
		});

		setDrawerOpen(false);

		if (typeof mobileQuery.addEventListener === 'function') {
			mobileQuery.addEventListener('change', function () {
				setDrawerOpen(false);
			});
		} else if (typeof mobileQuery.addListener === 'function') {
			mobileQuery.addListener(function () {
				setDrawerOpen(false);
			});
		}
	}

	document.addEventListener('click', function (event) {
		const link = event.target.closest('.andreian-toc a[href*="#"]');
		if (!link) {
			return;
		}

		const href = link.getAttribute('href');
		const target = headingFromHash(href);
		const inMobileDrawer = Boolean(drawer && mobileQuery.matches && link.closest('#andreian-post-sidebar'));

		if (inMobileDrawer) {
			event.preventDefault();
			setDrawerOpen(false);
			scrollToHeading(target, href);
			return;
		}

		if (!stickyNav || !target || target.getBoundingClientRect().top >= 0) {
			return;
		}

		stickyNav.classList.remove('is-scroll-hidden');
	});

	function updateDrawerVisibility() {
		if (!drawer || !entryContent) {
			return;
		}

		const pastContent = entryContent.getBoundingClientRect().bottom <= getScrollOffset();

		drawer.classList.toggle('is-past-content', pastContent);

		if (pastContent && drawer.classList.contains('is-open')) {
			setDrawerOpen(false);
		}
	}

	if (!tocs.length) {
		if (drawer) {
			updateDrawerVisibility();
			window.addEventListener('scroll', updateDrawerVisibility, { passive: true });
			window.addEventListener('resize', updateDrawerVisibility, { passive: true });
		}

		return;
	}

	const links = tocs.flatMap(function (toc) {
		return Array.from(toc.querySelectorAll('a[href*="#"]'));
	});

	const sections = [];
	const seenTargets = new Set();

	links.forEach(function (link) {
		const target = headingFromHash(link.getAttribute('href'));
		if (!target || seenTargets.has(target)) {
			return;
		}

		seenTargets.add(target);
		sections.push({ target: target, id: target.id });
	});

	let lastActiveId = '';

	function setActiveSection(activeId) {
		let activeLink = null;

		links.forEach(function (link) {
			const href = link.getAttribute('href');
			const hashIndex = href.indexOf('#');
			const id = hashIndex === -1 ? '' : decodeURIComponent(href.slice(hashIndex + 1));
			const isActive = id === activeId;
			link.classList.toggle('is-active', isActive);
			if (isActive && !activeLink) {
				activeLink = link;
			}
		});

		if (!activeLink) {
			return;
		}

		window.requestAnimationFrame(function () {
			const sidebarInner = activeLink.closest('.post-sidebar__inner');
			if (!sidebarInner || !sidebarInner.offsetParent) {
				return;
			}

			const linkRect = activeLink.getBoundingClientRect();
			const sidebarRect = sidebarInner.getBoundingClientRect();

			if (linkRect.top < sidebarRect.top || linkRect.bottom > sidebarRect.bottom) {
				activeLink.scrollIntoView({ block: 'nearest', behavior: 'auto' });
			}
		});
	}

	function updateActiveSection() {
		if (!sections.length) {
			return;
		}

		const offset = getScrollOffset();
		let active = sections[0];

		for (let i = 0; i < sections.length; i++) {
			if (sections[i].target.getBoundingClientRect().top <= offset) {
				active = sections[i];
			} else {
				break;
			}
		}

		if (active.id === lastActiveId) {
			return;
		}

		lastActiveId = active.id;
		setActiveSection(active.id);
	}

	let ticking = false;

	function onScroll() {
		if (ticking) {
			return;
		}

		ticking = true;
		window.requestAnimationFrame(function () {
			ticking = false;
			updateActiveSection();
			updateDrawerVisibility();
		});
	}

	updateActiveSection();
	updateDrawerVisibility();
	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll, { passive: true });
});
