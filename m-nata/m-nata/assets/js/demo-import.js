/* M-Nata demo import screen: runs the import one small AJAX step at a time and shows progress. Admin only. */
(function () {
	'use strict';

	var cfg = window.mnataDemo;
	var root = document.getElementById('mnata-demo');
	if (!cfg || !root) { return; }

	var form = document.getElementById('mnata-demo-form');
	var runBtn = document.getElementById('mnata-demo-run');
	var removeBtn = document.getElementById('mnata-demo-remove');
	var box = document.getElementById('mnata-demo-progress');
	var bar = box.querySelector('.mnata-demo__bar');
	var fill = bar.querySelector('span');
	var status = document.getElementById('mnata-demo-status');
	var log = document.getElementById('mnata-demo-log');

	function setProgress(done, total) {
		var pct = total ? Math.round((done / total) * 100) : 0;
		fill.style.width = pct + '%';
		bar.setAttribute('aria-valuenow', String(pct));
	}

	function addLog(text, isError) {
		var li = document.createElement('li');
		li.textContent = text;
		if (isError) { li.className = 'is-error'; }
		log.appendChild(li);
		log.scrollTop = log.scrollHeight;
	}

	function options() {
		var out = {};
		Array.prototype.forEach.call(form.querySelectorAll('input[type=checkbox]'), function (c) {
			if (c.checked) { out[c.name] = '1'; }
		});
		return out;
	}

	function call(step, extra) {
		var fd = new FormData();
		fd.append('action', 'mnata_demo');
		fd.append('_wpnonce', cfg.nonce);
		fd.append('step', step);
		var opts = options();
		Object.keys(opts).forEach(function (k) { fd.append(k, opts[k]); });
		Object.keys(extra || {}).forEach(function (k) { fd.append(k, extra[k]); });
		return fetch(cfg.ajax, { method: 'POST', body: fd, credentials: 'same-origin' }).then(function (r) {
			return r.json().catch(function () { throw new Error('HTTP ' + r.status); });
		}).then(function (j) {
			if (!j || !j.success) { throw new Error(j && j.data && j.data.message ? j.data.message : 'Error'); }
			return j.data;
		});
	}

	function busy(on) {
		runBtn.disabled = on;
		if (removeBtn) { removeBtn.disabled = on; }
		Array.prototype.forEach.call(form.querySelectorAll('input'), function (i) { i.disabled = on; });
	}

	function fail(err) {
		status.textContent = cfg.text.failed + ' ' + err.message;
		addLog(cfg.text.failed + ' ' + err.message, true);
		busy(false);
	}

	function start() {
		box.hidden = false;
		log.innerHTML = '';
		setProgress(0, 1);
		busy(true);
	}

	function runImport() {
		var opts = options();
		var wantPosts = !!opts['opts[content]'];
		var wantWidgets = !!opts['opts[widgets]'];
		start();

		var total = 0;
		var done = 0;
		var steps = [];

		steps.push({ key: 'media', run: function () { return call('media').then(function (d) { addLog(d.message); }); } });
		steps.push({ key: 'structure', run: function () { return call('structure').then(function (d) { addLog(d.message); }); } });

		var chain = call('init').then(function (d) {
			total = d.total;
		});

		return chain.then(function () {
			var count = (wantPosts ? total : 0);
			var all = 2 + count + (wantWidgets ? 1 : 0) + 2;
			var tick = function () { done += 1; setProgress(done, all); };

			var p = Promise.resolve();
			steps.forEach(function (s) {
				p = p.then(function () { status.textContent = cfg.text[s.key]; return s.run(); }).then(tick);
			});

			if (wantPosts) {
				var loop = function (i) {
					if (i >= total) { return Promise.resolve(); }
					status.textContent = cfg.text.post + ' (' + (i + 1) + '/' + total + ')';
					return call('post', { index: i }).then(function (r) {
						addLog((r.skipped ? '= ' : '+ ') + r.title);
						tick();
						return loop(i + 1);
					});
				};
				p = p.then(function () { return loop(0); });
			}
			if (wantWidgets) {
				p = p.then(function () { status.textContent = cfg.text.widgets; return call('widgets'); }).then(function (d) { addLog(d.message); tick(); });
			}
			p = p.then(function () { status.textContent = cfg.text.settings; return call('settings'); }).then(function (d) { addLog(d.message); tick(); });
			p = p.then(function () { status.textContent = cfg.text.finish; return call('finish'); }).then(function (d) { addLog(d.message); tick(); });
			return p;
		}).then(function () {
			setProgress(1, 1);
			status.textContent = cfg.text.done;
			busy(false);
		}).catch(fail);
	}

	function runRemove() {
		if (!window.confirm(cfg.text.confirm)) { return; }
		start();
		status.textContent = cfg.text.remove;
		var n = 0;
		var loop = function () {
			return call('remove').then(function (d) {
				n += 1;
				setProgress(n, n + (d.remaining ? 1 : 0));
				if (d.remaining) { return loop(); }
			});
		};
		loop().then(function () {
			setProgress(1, 1);
			status.textContent = cfg.text.removed;
			addLog(cfg.text.removed);
			busy(false);
		}).catch(fail);
	}

	runBtn.addEventListener('click', runImport);
	if (removeBtn) { removeBtn.addEventListener('click', runRemove); }
})();
