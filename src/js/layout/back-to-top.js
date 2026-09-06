document.addEventListener('DOMContentLoaded', function () {
	const button = document.querySelector('.back-to-top');

	if (!button) {
		return;
	}

	const threshold = 280;
	let ticking = false;

	function setVisible(isVisible) {
		button.classList.toggle('is-visible', isVisible);
		button.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
		button.tabIndex = isVisible ? 0 : -1;
	}

	function update() {
		ticking = false;
		setVisible(window.scrollY > threshold);
	}

	function onScroll() {
		if (ticking) {
			return;
		}

		ticking = true;
		window.requestAnimationFrame(update);
	}

	update();
	window.addEventListener('scroll', onScroll, { passive: true });

	button.addEventListener('click', function () {
		const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		setVisible(false);
		window.scrollTo({
			top: 0,
			behavior: reduceMotion ? 'auto' : 'smooth',
		});
	});
});
