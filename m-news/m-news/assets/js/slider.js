/* M-News slider: scroll-snap track + prev/next, dots/thumbs, optional autoplay. Loaded only when a slider is on the page. */
(function () {
	'use strict';

	var roots = document.querySelectorAll('[data-mnw-slider]');
	if (!roots.length) { return; }

	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	Array.prototype.forEach.call(roots, function (root) {
		var track = root.querySelector('.mnw-slider__track');
		if (!track || track.children.length < 2) { return; }

		var slides = track.children;
		var marks = root.querySelectorAll('[data-slide]');
		var behavior = reduce ? 'auto' : 'smooth';
		var timer = null;
		var visible = true;
		var ticking = false;

		function step() { return slides[1].offsetLeft - slides[0].offsetLeft; }
		function max() { return track.scrollWidth - track.clientWidth; }
		function index() { return Math.round(track.scrollLeft / step()); }

		function mark() {
			var c = index();
			Array.prototype.forEach.call(marks, function (b) {
				var on = parseInt(b.getAttribute('data-slide'), 10) === c;
				b.classList.toggle('is-active', on);
				if (on) { b.setAttribute('aria-current', 'true'); } else { b.removeAttribute('aria-current'); }
			});
		}

		function move(dir) {
			var target = track.scrollLeft + dir * step();
			if (target > max() + 2) { target = 0; }
			if (target < -2) { target = max(); }
			track.scrollTo({ left: target, behavior: behavior });
		}

		function stop() { if (timer) { clearInterval(timer); timer = null; } }
		function start() {
			var delay = parseInt(root.getAttribute('data-autoplay'), 10) || 0;
			if (!delay || reduce || timer || !visible || document.hidden) { return; }
			timer = setInterval(function () { move(1); }, delay);
		}

		root.addEventListener('click', function (e) {
			var b = e.target.closest('[data-dir],[data-slide]');
			if (!b || !root.contains(b)) { return; }
			if (b.hasAttribute('data-dir')) {
				move(parseInt(b.getAttribute('data-dir'), 10));
			} else {
				track.scrollTo({ left: parseInt(b.getAttribute('data-slide'), 10) * step(), behavior: behavior });
			}
			stop();
		});

		track.addEventListener('scroll', function () {
			if (ticking) { return; }
			ticking = true;
			requestAnimationFrame(function () { ticking = false; mark(); });
		}, { passive: true });

		['mouseenter', 'focusin', 'touchstart'].forEach(function (ev) {
			root.addEventListener(ev, stop, { passive: true });
		});
		root.addEventListener('mouseleave', start);
		document.addEventListener('visibilitychange', function () { if (document.hidden) { stop(); } else { start(); } });

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (entries) {
				visible = entries[0].isIntersecting;
				if (visible) { start(); } else { stop(); }
			}).observe(root);
		}

		mark();
		start();
	});
})();
