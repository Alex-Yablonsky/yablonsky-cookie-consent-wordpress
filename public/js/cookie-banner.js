(function () {
	'use strict';

	var settings = window.yccSettings || {};
	var storageKey = settings.storageKey || 'yablonsky_cookie_consent';
	var root = null;
	var modalOverlay = null;

	function log(message, data) {
		if (settings.debug && window.console && window.console.log) {
			window.console.log('[YCC] ' + message, data || '');
		}
	}

	function escapeHtml(value) {
		var div = document.createElement('div');
		div.textContent = value || '';
		return div.innerHTML;
	}

	function isValidConsent(consent) {
		return !!(consent && consent.version === settings.policyVersion && typeof consent.necessary !== 'undefined');
	}

	function getStoredConsent() {
		try {
			var raw = window.localStorage ? window.localStorage.getItem(storageKey) : null;
			if (!raw) { return null; }
			var parsed = JSON.parse(raw);
			return isValidConsent(parsed) ? parsed : null;
		} catch (error) {
			return null;
		}
	}

	function setCookie(name, value, days) {
		var expires = '';
		var secure = window.location.protocol === 'https:' ? '; Secure' : '';
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			expires = '; expires=' + date.toUTCString();
		}
		document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax' + secure;
	}

	function saveConsent(consent) {
		var value = JSON.stringify(consent);
		try {
			if (window.localStorage) {
				window.localStorage.setItem(storageKey, value);
			}
		} catch (error) {
			log('localStorage save failed', error);
		}
		setCookie(storageKey, value, parseInt(settings.expiryDays || 180, 10));
	}

	function createConsent(analytics, marketing, functional) {
		return {
			version: settings.policyVersion,
			necessary: true,
			analytics: !!analytics,
			marketing: !!marketing,
			functional: !!functional,
			updated_at: new Date().toISOString()
		};
	}

	function applyConsent(consent) {
		if (typeof window.yccApplyConsent === 'function') {
			window.yccApplyConsent(consent);
		}
	}

	function saveAndApply(consent) {
		saveConsent(consent);
		applyConsent(consent);
		hideBanner();
		closeModal();
		log('Consent saved', consent);
	}

	function hideBanner() {
		var banner = document.querySelector('.ycc-banner');
		if (banner) { banner.classList.add('ycc-hidden'); }
	}

	function buildLinks() {
		var links = [];
		if (settings.privacyPolicyUrl) {
			links.push('<a href="' + escapeHtml(settings.privacyPolicyUrl) + '">' + escapeHtml(settings.labels.privacyPolicy) + '</a>');
		}
		if (settings.cookiePolicyUrl) {
			links.push('<a href="' + escapeHtml(settings.cookiePolicyUrl) + '">' + escapeHtml(settings.labels.cookiePolicy) + '</a>');
		}
		if (settings.termsUrl) {
			links.push('<a href="' + escapeHtml(settings.termsUrl) + '">' + escapeHtml(settings.labels.terms) + '</a>');
		}
		return links.length ? '<div class="ycc-links">' + links.join('') + '</div>' : '';
	}

	function renderBanner() {
		var customClass = settings.customCssClass ? ' ' + settings.customCssClass : '';
		root.innerHTML = '' +
			'<section class="ycc-banner' + customClass + '" role="region" aria-label="' + escapeHtml(settings.labels.bannerTitle) + '">' +
				'<h2 class="ycc-banner__title">' + escapeHtml(settings.labels.bannerTitle) + '</h2>' +
				'<p class="ycc-banner__message">' + escapeHtml(settings.labels.bannerMessage) + '</p>' +
				buildLinks() +
				'<div class="ycc-actions">' +
					'<button type="button" class="ycc-button ycc-button--primary" data-ycc-action="accept-all">' + escapeHtml(settings.labels.acceptAll) + '</button>' +
					'<button type="button" class="ycc-button" data-ycc-action="reject">' + escapeHtml(settings.labels.rejectNonEssential) + '</button>' +
					'<button type="button" class="ycc-button" data-ycc-action="settings">' + escapeHtml(settings.labels.settings) + '</button>' +
				'</div>' +
			'</section>';
	}

	function getToggleValue(name) {
		var input = document.querySelector('[data-ycc-toggle="' + name + '"]');
		return !!(input && input.checked);
	}

	function categoryHtml(key, locked) {
		var category = settings.categories[key] || {};
		var control = locked ?
			'<span class="ycc-locked">' + escapeHtml(settings.labels.locked) + '</span>' :
			'<label class="ycc-switch"><input type="checkbox" data-ycc-toggle="' + escapeHtml(key) + '"><span class="ycc-slider"></span></label>';
		return '' +
			'<div class="ycc-category">' +
				'<div><h3 class="ycc-category__title">' + escapeHtml(category.label) + '</h3><p class="ycc-category__description">' + escapeHtml(category.description) + '</p></div>' +
				'<div>' + control + '</div>' +
			'</div>';
	}

	function openModal() {
		closeModal();
		modalOverlay = document.createElement('div');
		modalOverlay.className = 'ycc-modal-overlay';
		modalOverlay.setAttribute('role', 'presentation');
		modalOverlay.innerHTML = '' +
			'<div class="ycc-modal" role="dialog" aria-modal="true" aria-labelledby="ycc-modal-title">' +
				'<div class="ycc-modal__header"><h2 id="ycc-modal-title" class="ycc-modal__title">' + escapeHtml(settings.labels.modalTitle) + '</h2><button type="button" class="ycc-modal__close" data-ycc-action="close" aria-label="' + escapeHtml(settings.labels.close) + '">×</button></div>' +
				'<div class="ycc-modal__body">' + categoryHtml('necessary', true) + categoryHtml('analytics', false) + categoryHtml('marketing', false) + categoryHtml('functional', false) + buildLinks() + '</div>' +
				'<div class="ycc-modal__footer"><button type="button" class="ycc-button" data-ycc-action="reject">' + escapeHtml(settings.labels.rejectNonEssential) + '</button><button type="button" class="ycc-button" data-ycc-action="save">' + escapeHtml(settings.labels.savePreferences) + '</button><button type="button" class="ycc-button ycc-button--primary" data-ycc-action="accept-all">' + escapeHtml(settings.labels.acceptAll) + '</button></div>' +
			'</div>';
		document.body.appendChild(modalOverlay);

		var stored = getStoredConsent();
		if (stored) {
			['analytics', 'marketing', 'functional'].forEach(function (name) {
				var input = document.querySelector('[data-ycc-toggle="' + name + '"]');
				if (input) { input.checked = !!stored[name]; }
			});
		}

		var firstButton = modalOverlay.querySelector('button');
		if (firstButton) { firstButton.focus(); }
	}

	function closeModal() {
		if (modalOverlay && modalOverlay.parentNode) {
			modalOverlay.parentNode.removeChild(modalOverlay);
		}
		modalOverlay = null;
	}

	function handleAction(action) {
		if (action === 'accept-all') { saveAndApply(createConsent(true, true, true)); return; }
		if (action === 'reject') { saveAndApply(createConsent(false, false, false)); return; }
		if (action === 'settings') { openModal(); return; }
		if (action === 'save') { saveAndApply(createConsent(getToggleValue('analytics'), getToggleValue('marketing'), getToggleValue('functional'))); return; }
		if (action === 'close') { closeModal(); }
	}

	function bindEvents() {
		document.addEventListener('click', function (event) {
			var target = event.target.closest('[data-ycc-action]');
			if (!target) { return; }
			event.preventDefault();
			handleAction(target.getAttribute('data-ycc-action'));
		});
		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') { closeModal(); }
		});
	}

	function init() {
		root = document.getElementById('yablonsky-cookie-consent-root');
		if (!root) {
			root = document.createElement('div');
			root.id = 'yablonsky-cookie-consent-root';
			document.body.appendChild(root);
		}
		bindEvents();
		window.yablonskyCookieConsentOpenSettings = openModal;
		var stored = getStoredConsent();
		if (stored) {
			if (!window.yccConsentApplied) {
				applyConsent(stored);
			}
			log('Stored consent found', stored);
			return;
		}
		renderBanner();
		log('Banner rendered');
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
