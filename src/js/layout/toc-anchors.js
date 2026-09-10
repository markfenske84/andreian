document.addEventListener('DOMContentLoaded', function () {
	const stickyNav = document.querySelector('.site-header__navigation');
	const toc = document.querySelector('.andreian-toc');

	if (!toc) {
		return;
	}

	const links = Array.from(toc.querySelectorAll('a[href*="#"]'));

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

	document.addEventListener('click', function (event) {
		const link = event.target.closest('.andreian-toc a[href*="#"]');
		if (!link || !stickyNav) {
			return;
		}

		const target = headingFromHash(link.getAttribute('href'));
		if (!target || target.getBoundingClientRect().top >= 0) {
			return;
		}

		stickyNav.classList.remove('is-scroll-hidden');
	});

	const sections = links
		.map(function (link) {
			const target = headingFromHash(link.getAttribute('href'));
			if (!target) {
				return null;
			}

			return { link: link, target: target };
		})
		.filter(Boolean);

	if (!sections.length) {
		return;
	}

	function getScrollOffset() {
		const root = document.documentElement;
		const adminBar = parseFloat(getComputedStyle(root).getPropertyValue('--admin-bar-height')) || 0;
		const navBar = parseFloat(getComputedStyle(root).getPropertyValue('--nav-bar-height')) || 46;

		return adminBar + navBar + 16;
	}

	function setActiveLink(activeLink) {
		sections.forEach(function (section) {
			section.link.classList.toggle('is-active', section.link === activeLink);
		});

		if (!activeLink) {
			return;
		}

		const sidebar = toc.closest('.post-sidebar__inner');
		if (!sidebar) {
			return;
		}

		const linkRect = activeLink.getBoundingClientRect();
		const sidebarRect = sidebar.getBoundingClientRect();

		if (linkRect.top < sidebarRect.top || linkRect.bottom > sidebarRect.bottom) {
			activeLink.scrollIntoView({ block: 'nearest', behavior: 'auto' });
		}
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

		setActiveLink(active.link);
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
