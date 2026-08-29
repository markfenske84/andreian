document.addEventListener('DOMContentLoaded', function () {
	function applyHeaderHeight(height) {
		const value = `${height}px`;
		document.documentElement.style.setProperty('--header-height', value);
		document.body.style.setProperty('--header-height', value);
	}

	function setHeaderHeight() {
		const header = document.getElementById('main-header');
		if (!header || !header.classList.contains('-position-fixed')) {
			return;
		}
		applyHeaderHeight(header.offsetHeight);
	}

	setHeaderHeight();
	window.addEventListener('resize', setHeaderHeight);
	window.addEventListener('load', setHeaderHeight);

	const mobileMenuLayout =
		window.andreian_localize && window.andreian_localize.mobile_menu_layout
			? window.andreian_localize.mobile_menu_layout
			: 'dropdown';
	const isPanelLayout = mobileMenuLayout === 'panel';

	function insertToggleButton(menuItem, direction) {
		if (menuItem.querySelector('.toggle-button')) {
			return;
		}

		const button = document.createElement('button');
		button.type = 'button';
		button.classList.add('toggle-button');
		button.textContent = direction === 'right' ? '›' : '▼';

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
			subMenu.classList.toggle('show');
			menuItem.classList.toggle('open');
		});
	}

	document.querySelectorAll('#mobile-offcanvas .menu-item-has-children').forEach(function (item) {
		insertToggleButton(item, isPanelLayout ? 'right' : 'down');
		if (!isPanelLayout) {
			bindDropdownToggle(item);
		}
	});

	document.querySelectorAll('.site-header .menu-item-has-children').forEach(function (item) {
		insertToggleButton(item, 'down');
		bindDropdownToggle(item);
	});

	const mobileOffcanvas = document.querySelector('#mobile-offcanvas');
	document.querySelectorAll('.mobile-offcanvas-toggle').forEach(function (toggle) {
		toggle.addEventListener('click', function () {
			if (!mobileOffcanvas) {
				return;
			}
			mobileOffcanvas.classList.toggle('open');
			document.body.classList.toggle('mobile-offcanvas-open');
		});
	});

	document.addEventListener('click', function (event) {
		if (!mobileOffcanvas) {
			return;
		}
		if (
			!event.target.closest('.mobile-offcanvas-toggle') &&
			!event.target.closest('#mobile-offcanvas')
		) {
			mobileOffcanvas.classList.remove('open');
			document.body.classList.remove('mobile-offcanvas-open');
		}
	});
});
