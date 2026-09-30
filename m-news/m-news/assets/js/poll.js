/* M-News polling: client-side vote state (localStorage) so cached HTML never bakes in a per-visitor answer. */
(function () {
	'use strict';

	var cards = document.querySelectorAll('[data-mnw-poll]');
	if (!cards.length || !window.mnewsPollApi) { return; }

	var STORE = 'mnwPollVotes';

	function readStore() {
		try { return JSON.parse(window.localStorage.getItem(STORE) || '{}'); } catch (e) { return {}; }
	}
	function writeStore(store) {
		try { window.localStorage.setItem(STORE, JSON.stringify(store)); } catch (e) { /* private mode, ignore */ }
	}

	function showResults(card, counts, votedKey) {
		var total = 0;
		Object.keys(counts).forEach(function (k) { total += counts[k]; });
		card.classList.add('is-voted');
		Array.prototype.forEach.call(card.querySelectorAll('.mnw-poll__opt'), function (btn) {
			var key = btn.getAttribute('data-option');
			var votes = counts[key] || 0;
			var pct = total > 0 ? Math.round((votes / total) * 100) : 0;
			btn.setAttribute('disabled', 'disabled');
			btn.querySelector('.mnw-poll__opt-bar').style.setProperty('--mnw-pct', pct + '%');
			btn.querySelector('.mnw-poll__opt-pct').textContent = pct + '%';
			btn.classList.toggle('is-picked', key === votedKey);
		});
	}

	function vote(card, key) {
		var id = card.getAttribute('data-poll-id');
		var body = 'poll_id=' + encodeURIComponent(id) + '&option=' + encodeURIComponent(key);
		fetch(window.mnewsPollApi + '/poll-vote', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body
		}).then(function (r) { return r.json(); }).then(function (data) {
			if (!data || !data.counts) { return; }
			var store = readStore();
			store[id] = data.voted;
			writeStore(store);
			showResults(card, data.counts, data.voted);
		}).catch(function () { /* offline: leave the buttons as they were */ });
	}

	Array.prototype.forEach.call(cards, function (card) {
		var id = card.getAttribute('data-poll-id');
		var store = readStore();
		if (store[id]) {
			// Already voted on this browser: fetch fresh totals and show results immediately, no click needed.
			fetch(window.mnewsPollApi + '/poll-results/' + encodeURIComponent(id))
				.then(function (r) { return r.json(); })
				.then(function (data) { if (data && data.counts) { showResults(card, data.counts, store[id]); } })
				.catch(function () { /* keep the cached snapshot already rendered */ });
			return;
		}
		card.addEventListener('click', function (e) {
			var btn = e.target.closest ? e.target.closest('.mnw-poll__opt') : null;
			if (!btn || !card.contains(btn) || card.classList.contains('is-voted')) { return; }
			vote(card, btn.getAttribute('data-option'));
		});
	});
})();
