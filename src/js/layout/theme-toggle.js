document.addEventListener('DOMContentLoaded', function () {
	const storageKey = 'andreian-theme';
	const toggle = document.querySelector('.theme-toggle');

	if (!toggle) {
		return;
	}

	function currentTheme() {
		return document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
	}

	function applyTheme(theme) {
		document.documentElement.setAttribute('data-theme', theme);

		try {
			localStorage.setItem(storageKey, theme);
		} catch (error) {}

		const nextLabel =
			theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
		toggle.setAttribute('aria-label', nextLabel);
		toggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
	}

	applyTheme(currentTheme());

	toggle.addEventListener('click', function () {
		applyTheme(currentTheme() === 'dark' ? 'light' : 'dark');
	});
});
