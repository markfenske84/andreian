document.addEventListener('DOMContentLoaded', function () {
	const stickyNav = document.querySelector('.site-header__navigation');

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
});
