/* M-Nata slots: injects ad / embed code (kept inside <template>) only when it is near the viewport
   and matches the requested device. Keeps third-party scripts out of the critical path. */
(function () {
	'use strict';

	var slots = document.querySelectorAll('[data-mn-slot]');
	if (!slots.length) { return; }

	function matches(el) {
		var m = el.getAttribute('data-media');
		return !m || (window.matchMedia && window.matchMedia(m).matches);
	}

	function load(el) {
		if (el.getAttribute('data-done')) { return; }
		el.setAttribute('data-done', '1');
		var tpl = el.querySelector('template');
		if (!tpl) { return; }
		var frag = tpl.content.cloneNode(true);
		// Scripts cloned from a template do not run; recreate them.
		Array.prototype.forEach.call(frag.querySelectorAll('script'), function (old) {
			var s = document.createElement('script');
			Array.prototype.forEach.call(old.attributes, function (a) { s.setAttribute(a.name, a.value); });
			s.text = old.text;
			old.parentNode.replaceChild(s, old);
		});
		el.appendChild(frag);
	}

	var io = ('IntersectionObserver' in window) ? new IntersectionObserver(function (entries) {
		entries.forEach(function (e) {
			if (e.isIntersecting) { io.unobserve(e.target); load(e.target); }
		});
	}, { rootMargin: '300px 0px' }) : null;

	Array.prototype.forEach.call(slots, function (el) {
		if (!matches(el)) { return; }
		if (io && el.getAttribute('data-lazy') !== '0') { io.observe(el); } else { load(el); }
	});
})();
