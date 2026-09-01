(function () {
	'use strict';

	function fallbackCopy(value) {
		var field = document.createElement('textarea');

		field.value = value;
		field.setAttribute('readonly', '');
		field.style.position = 'fixed';
		field.style.opacity = '0';
		document.body.appendChild(field);
		field.select();

		var copied = document.execCommand('copy');
		document.body.removeChild(field);

		return copied ? Promise.resolve() : Promise.reject();
	}

	document.querySelectorAll('.post-share__copy').forEach(function (button) {
		button.addEventListener('click', function () {
			var status = button.closest('.post-share').querySelector('.post-share__status');
			var copy = navigator.clipboard && window.isSecureContext
				? navigator.clipboard.writeText(button.dataset.copyUrl).catch(function () {
					return fallbackCopy(button.dataset.copyUrl);
				})
				: fallbackCopy(button.dataset.copyUrl);

			copy
				.then(function () {
					status.textContent = button.dataset.copySuccess;
					button.classList.add('is-copied');
				})
				.catch(function () {
					status.textContent = button.dataset.copyError;
				});
		});
	});
}());
