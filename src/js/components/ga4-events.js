(function () {
	'use strict';

	var config = window.andreian_localize || {};
	var kitFormUid = config.kit_form_uid || '';
	var kitFormId = config.kit_form_id || '';

	document.addEventListener('ckjs:submission:complete', function (event) {
		var form = event.target;
		if (!kitFormUid || !kitFormId || !form || !form.matches || !form.matches('form.formkit-form[data-uid="' + kitFormUid + '"]')) {
			return;
		}
		if (typeof window.gtag !== 'function') {
			return;
		}
		var detail = event.detail || {};
		window.gtag('event', 'generate_lead', {
			page_location: window.location.href,
			page_path: window.location.pathname,
			form_id: form.getAttribute('data-sv-form') || detail.uid || kitFormId
		});
	});

	document.addEventListener('click', function (event) {
		var target = event.target;
		var link = target && target.closest ? target.closest('a[href]') : null;
		if (!link || typeof window.gtag !== 'function') {
			return;
		}
		var url;
		try {
			url = new URL(link.href, window.location.href);
		} catch (err) {
			return;
		}
		if (url.protocol !== 'http:' && url.protocol !== 'https:') {
			return;
		}
		var host = url.hostname.toLowerCase();
		if (!/(^|\.)amazon\.[^.]+(?:\.[^.]+)*$/.test(host) && !/(^|\.)amzn\.to$/.test(host) && !/(^|\.)a\.co$/.test(host)) {
			return;
		}
		window.gtag('event', 'affiliate_click', {
			page_path: window.location.pathname,
			link_url: url.href,
			page_location: window.location.href
		});
	});
}());
