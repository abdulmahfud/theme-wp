/* M-News main script. Vanilla, deferred. Budget: <= 10 KB gzip in total. */
(function () {
	'use strict';

	// Mobile menu / search panel toggle (opened by the header hamburger and by the bottom nav's "Cari").
	var nav = document.getElementById('mnw-nav');
	if (nav) {
		var togglers = document.querySelectorAll('.mnw-toggle,[data-mnw-nav-toggle]');
		var setOpen = function (open, focusSearch) {
			nav.classList.toggle('is-open', open);
			Array.prototype.forEach.call(togglers, function (t) {
				t.setAttribute('aria-expanded', open ? 'true' : 'false');
			});
			if (open && focusSearch) {
				var field = nav.querySelector('input[type="search"]');
				if (field) { field.focus(); }
			}
		};
		Array.prototype.forEach.call(togglers, function (t) {
			t.addEventListener('click', function (e) {
				e.preventDefault();
				setOpen(!nav.classList.contains('is-open'), t.hasAttribute('data-mnw-nav-toggle'));
			});
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) {
				setOpen(false, false);
				togglers[0].focus();
			}
		});
	}

	// Bottom nav "Bagikan": native share sheet, falling back to the same copy-link behaviour as the article share bar.
	document.addEventListener('click', function (e) {
		var b = e.target.closest ? e.target.closest('[data-mnw-share]') : null;
		if (!b) { return; }
		var data = { title: document.title, url: window.location.href };
		if (navigator.share) {
			navigator.share(data).catch(function () { /* user cancelled, ignore */ });
			return;
		}
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(data.url).then(function () {
				b.classList.add('is-copied');
				setTimeout(function () { b.classList.remove('is-copied'); }, 2000);
			});
		}
	});

	// Sticky side banners must sit below the sticky header: expose its height to CSS.
	var header = document.querySelector('.mnw-header');
	if (header && document.body.classList.contains('mnw-sticky')) {
		var setHeaderHeight = function () {
			document.documentElement.style.setProperty('--mnw-header-h', header.offsetHeight + 'px');
		};
		setHeaderHeight();
		window.addEventListener('resize', setHeaderHeight);
		if (window.ResizeObserver) { new ResizeObserver(setHeaderHeight).observe(header); }
	}

	// "Salin Link" share button.
	document.addEventListener('click', function (e) {
		var b = e.target.closest ? e.target.closest('[data-mnw-copy]') : null;
		if (!b) { return; }
		var url = b.getAttribute('data-mnw-copy');
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
