/**
 * reCAPTCHA v3 token for the post comment form.
 */
(function () {
	var config = window.andreianRecaptcha;

	if (!config || !config.siteKey) {
		return;
	}

	var form = document.getElementById('commentform');

	if (!form) {
		return;
	}

	var tokenInput = form.querySelector('input[name="g-recaptcha-response"]');

	if (!tokenInput) {
		return;
	}

	var submitting = false;

	function primeRecaptcha() {
		if (typeof grecaptcha === 'undefined') {
			return;
		}

		grecaptcha.ready(function () {
			grecaptcha.execute(config.siteKey, { action: config.action }).then(function (token) {
				tokenInput.value = token;
			});
		});
	}

	primeRecaptcha();

	form.addEventListener('submit', function (event) {
		if (submitting) {
			return;
		}

		event.preventDefault();

		if (typeof grecaptcha === 'undefined') {
			submitting = true;
			form.submit();
			return;
		}

		grecaptcha.ready(function () {
			grecaptcha
				.execute(config.siteKey, { action: config.action })
				.then(function (token) {
					tokenInput.value = token;
					submitting = true;
					form.submit();
				})
				.catch(function () {
					submitting = true;
					form.submit();
				});
		});
	});
})();
