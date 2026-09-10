document.addEventListener('DOMContentLoaded', function () {
	const stickyNav = document.querySelector('.site-header__navigation');
	const tocs = Array.from(document.querySelectorAll('.andreian-toc'));
	const drawer = document.querySelector('[data-andreian-toc-drawer]');

	if (!tocs.length) {
		return;
	}

	const links = tocs.flatMap(function (toc) {
		return Array.from(toc.querySelectorAll('a[href*="#"]'));
	});

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

		const tab = drawer.querySelector('.andreian-toc-drawer__tab');
		const panel = drawer.querySelector('.andreian-toc-drawer__panel');
		const backdrop = drawer.querySelector('.andreian-toc-drawer__backdrop');

		drawer.classList.toggle('is-open', isOpen);
		document.body.classList.toggle('andreian-toc-drawer-open', isOpen);

		if (tab) {
			tab.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			tab.setAttribute(
				'aria-label',
				isOpen ? tab.dataset.closeLabel : tab.dataset.openLabel
			);
		}

		if (panel) {
			panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
		}

		if (backdrop) {
			backdrop.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
		}
	}

	if (drawer) {
		const tab = drawer.querySelector('.andreian-toc-drawer__tab');
		const backdrop = drawer.querySelector('.andreian-toc-drawer__backdrop');

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
	}

	document.addEventListener('click', function (event) {
		const link = event.target.closest('.andreian-toc a[href*="#"]');
		if (!link) {
			return;
		}

		const href = link.getAttribute('href');
		const target = headingFromHash(href);
		const drawerLink = link.closest('.andreian-toc--drawer');

		if (drawerLink) {
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

	if (!sections.length) {
		return;
	}

	function setActiveSection(activeId) {
		links.forEach(function (link) {
			const href = link.getAttribute('href');
			const hashIndex = href.indexOf('#');
			const id = hashIndex === -1 ? '' : decodeURIComponent(href.slice(hashIndex + 1));
			link.classList.toggle('is-active', id === activeId);
		});

		links
			.filter(function (link) {
				const href = link.getAttribute('href');
				const hashIndex = href.indexOf('#');
				const id = hashIndex === -1 ? '' : decodeURIComponent(href.slice(hashIndex + 1));
				return id === activeId;
			})
			.forEach(function (activeLink) {
				const sidebar = activeLink.closest('.post-sidebar__inner');
				if (!sidebar || !sidebar.offsetParent) {
					return;
				}

				const linkRect = activeLink.getBoundingClientRect();
				const sidebarRect = sidebar.getBoundingClientRect();

				if (linkRect.top < sidebarRect.top || linkRect.bottom > sidebarRect.bottom) {
					activeLink.scrollIntoView({ block: 'nearest', behavior: 'auto' });
				}
			});
	}

	function updateActiveSection() {
		const offset = getScrollOffset();
		let active = sections[0];

		for (let i = 0; i < sections.length; i++) {
			if (sections[i].target.getBoundingClientRect().top <= offset) {
				active = sections[i];
			} else {
				break;
			}
		}

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
		});
	}

	updateActiveSection();
	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll, { passive: true });
});
