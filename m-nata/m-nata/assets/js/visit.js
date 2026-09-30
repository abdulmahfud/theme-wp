/* M-Nata visit beacon (~0.4 KB): one background request per page view, never blocks rendering.
   Sends a random id kept in localStorage; honours Do Not Track. Loaded only while the visitor widget is used. */
(function () {
	'use strict';

	if (!window.mnataHit || navigator.doNotTrack === '1' || window.doNotTrack === '1') { return; }

	try {
		var id = localStorage.getItem('mnv');
		if (!id) {
			id = Math.random().toString(36).slice(2, 12) + Date.now().toString(36);
			localStorage.setItem('mnv', id);
		}
		var url = window.mnataHit + (window.mnataHit.indexOf('?') > -1 ? '&' : '?') + 'v=' + encodeURIComponent(id);
		if (navigator.sendBeacon) {
			navigator.sendBeacon(url);
		} else {
			fetch(url, { method: 'POST', keepalive: true });
		}
	} catch (e) { /* storage blocked: skip counting */ }
})();
