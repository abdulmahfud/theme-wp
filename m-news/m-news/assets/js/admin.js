/* Admin: media picker for the "image" widget field (works for widgets added dynamically). */
(function () {
	'use strict';

	function fire(el) {
		el.dispatchEvent(new Event('input', { bubbles: true }));
		el.dispatchEvent(new Event('change', { bubbles: true }));
	}

	document.addEventListener('click', function (e) {
		var pick = e.target.closest('.mnews-image-pick');
		var clear = e.target.closest('.mnews-image-clear');
		var wrap = (pick || clear) && (pick || clear).closest('.mnews-image-field');
		if (!wrap) { return; }

		var input = wrap.querySelector('.mnews-image-id');
		var preview = wrap.querySelector('.mnews-image-preview');

		if (clear) {
			e.preventDefault();
			input.value = '';
			preview.innerHTML = '';
			fire(input);
			return;
		}

		e.preventDefault();
		if (!window.wp || !wp.media) { return; }
		var texts = window.mnewsAdmin || {};
		var frame = wp.media({
			title: texts.title || 'Select image',
			button: { text: texts.button || 'Use image' },
			library: { type: 'image' },
			multiple: false
		});
		frame.on('select', function () {
			var a = frame.state().get('selection').first().toJSON();
			var src = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
			input.value = a.id;
			preview.innerHTML = '';
			var img = document.createElement('img');
			img.src = src;
			img.alt = '';
			preview.appendChild(img);
			fire(input);
		});
		frame.open();
	});
})();

/* Admin: cascading region picker (provinsi > kab/kota > kecamatan > desa) for the weather widget.
   Lists come from an admin-ajax proxy (cached server side). Initialised lazily on first interaction. */
(function () {
	'use strict';

	var cfg = window.mnewsAdmin || {};
	var LEVELS = ['province', 'regency', 'district', 'village'];
	var ENDPOINT = { province: 'provinces', regency: 'regencies', district: 'districts', village: 'villages' };

	function load(level, parent) {
		var url = cfg.ajax + '?action=mnews_wilayah&_wpnonce=' + encodeURIComponent(cfg.nonce) + '&level=' + ENDPOINT[level] +
			(parent ? '&parent=' + encodeURIComponent(parent) : '');
		return fetch(url, { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (j) {
			if (!j || !j.success) { throw new Error('bad'); }
			return j.data;
		});
	}

	function fill(select, items, selected) {
		var first = select.options[0];
		select.innerHTML = '';
		select.appendChild(first);
		items.forEach(function (it) {
			var o = document.createElement('option');
			o.value = it.code;
			o.textContent = it.name;
			if (it.code === selected) { o.selected = true; }
			select.appendChild(o);
		});
		select.disabled = false;
	}

	function reset(selects, from) {
		for (var i = from; i < selects.length; i++) {
			var first = selects[i].options[0];
			selects[i].innerHTML = '';
			selects[i].appendChild(first);
			selects[i].disabled = true;
		}
	}

	function init(box) {
		if (box.getAttribute('data-ready')) { return; }
		box.setAttribute('data-ready', '1');

		var input = box.previousElementSibling;
		while (input && !input.classList.contains('mnews-wilayah-code')) { input = input.previousElementSibling; }
		var selects = LEVELS.map(function (l) { return box.querySelector('.mnews-wil-' + l); });
		var current = input ? input.value.trim() : '';
		var parts = /^(\d{2})\.(\d{2})\.(\d{2})\.(\d{4})$/.exec(current);
		var want = parts ? [parts[1], parts[1] + '.' + parts[2], parts[1] + '.' + parts[2] + '.' + parts[3], current] : [];

		function fail() { box.setAttribute('data-error', cfg.failed || 'Error'); }

		function levelLoad(idx, parent) {
			var placeholder = selects[idx].options[0];
			var label = placeholder.getAttribute('data-label') || placeholder.textContent;
			placeholder.setAttribute('data-label', label);
			placeholder.textContent = cfg.loading || '...';
			selects[idx].disabled = true;
			return load(LEVELS[idx], parent).then(function (items) {
				placeholder.textContent = label;
				fill(selects[idx], items, want[idx]);
				return items;
			}, function (err) {
				placeholder.textContent = label;
				throw err;
			});
		}

		selects.forEach(function (sel, idx) {
			sel.disabled = true;
			sel.addEventListener('change', function () {
				want = [];
				reset(selects, idx + 1);
				if (!sel.value) { return; }
				if (idx < 3) {
					levelLoad(idx + 1, sel.value).catch(fail);
				} else {
					input.value = sel.value;
					input.dispatchEvent(new Event('input', { bubbles: true }));
					input.dispatchEvent(new Event('change', { bubbles: true }));
				}
			});
		});

		// Load provinces, then walk down to the saved region if there is one.
		levelLoad(0, '').then(function () {
			var chain = Promise.resolve();
			for (var idx = 1; idx < 4 && want[idx - 1]; idx++) {
				(function (n) {
					chain = chain.then(function () { return levelLoad(n, want[n - 1]); });
				})(idx);
			}
			return chain;
		}).catch(fail);
	}

	function boot(e) {
		var box = e.target.closest ? e.target.closest('.mnews-wilayah') : null;
		if (!box) {
			var wrap = e.target.closest ? e.target.closest('.widget-content, .widget-inside') : null;
			box = wrap ? wrap.querySelector('.mnews-wilayah') : null;
		}
		if (box) { init(box); }
	}

	document.addEventListener('focusin', boot);
	document.addEventListener('click', boot);
})();
