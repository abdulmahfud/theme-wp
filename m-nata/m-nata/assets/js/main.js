/* M-Nata main script. Vanilla, deferred. Budget: <= 10 KB gzip in total. */
(function () {
	'use strict';

	// Mobile menu toggle.
	var toggle = document.querySelector('.mn-toggle');
	var nav = document.getElementById('mn-nav');
	if (toggle && nav) {
		var setOpen = function (open) {
			nav.classList.toggle('is-open', open);
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		};
		toggle.addEventListener('click', function () {
			setOpen(!nav.classList.contains('is-open'));
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) {
				setOpen(false);
				toggle.focus();
			}
		});
	}

	// Sticky side banners must sit below the sticky header: expose its height to CSS.
	var header = document.querySelector('.mn-header');
	if (header && document.body.classList.contains('mn-sticky')) {
		var setHeaderHeight = function () {
			document.documentElement.style.setProperty('--mn-header-h', header.offsetHeight + 'px');
		};
		setHeaderHeight();
		window.addEventListener('resize', setHeaderHeight);
		if (window.ResizeObserver) { new ResizeObserver(setHeaderHeight).observe(header); }
	}

	// "Salin Link" share button.
	document.addEventListener('click', function (e) {
		var b = e.target.closest ? e.target.closest('[data-mn-copy]') : null;
		if (!b) { return; }
		var url = b.getAttribute('data-mn-copy');
		var done = function () {
			b.classList.add('is-copied');
			setTimeout(function () { b.classList.remove('is-copied'); }, 2000);
		};
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(url).then(done);
		} else {
			var t = document.createElement('textarea');
			t.value = url;
			document.body.appendChild(t);
			t.select();
			try { document.execCommand('copy'); done(); } catch (err) { /* ignore */ }
			document.body.removeChild(t);
		}
	});
})();
