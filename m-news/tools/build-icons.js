/**
 * Generates the social/utility icon lines for m-news/inc/icons.php.
 *
 * Sources (both permissive, see readme.txt):
 *   - Simple Icons (CC0 1.0)  - brand glyphs, 24x24, one path each
 *   - Bootstrap Icons (MIT)   - LinkedIn (removed from Simple Icons), e-mail, link; 16x16, scaled to 24
 *
 * Usage (in any scratch folder):
 *   npm i simple-icons bootstrap-icons
 *   NODE_PATH=./node_modules node <repo>/mnews/tools/build-icons.js > icons.generated.json
 * Then copy each entry over the matching entries in inc/icons.php (kept as a static map on purpose:
 * icons are inlined into the page, so there is no runtime dependency and no extra request).
 */
const fs = require('fs');
const path = require('path');
const si = require('simple-icons');

const simple = {
	facebook: si.siFacebook, x: si.siX, instagram: si.siInstagram, youtube: si.siYoutube, tiktok: si.siTiktok,
	pinterest: si.siPinterest, whatsapp: si.siWhatsapp, telegram: si.siTelegram, line: si.siLine,
};
const bootstrap = { linkedin: 'linkedin', mail: 'envelope-fill', link: 'link-45deg' };

const out = {};
for (const [name, icon] of Object.entries(simple)) {
	out[name] = '<path fill="currentColor" stroke="none" d="' + icon.path + '"/>';
}
for (const [name, file] of Object.entries(bootstrap)) {
	const svg = fs.readFileSync(path.join(require.resolve('bootstrap-icons/package.json'), '..', 'icons', file + '.svg'), 'utf8');
	const paths = [...svg.matchAll(/<path\b[^>]*>/g)].map((m) => {
		const d = /\sd="([^"]+)"/.exec(m[0])[1];
		const rule = /fill-rule="evenodd"/.test(m[0]) ? ' fill-rule="evenodd"' : '';
		return '<path' + rule + ' d="' + d + '"/>';
	});
	out[name] = '<g transform="scale(1.5)" fill="currentColor" stroke="none">' + paths.join('') + '</g>';
}
console.log(JSON.stringify(out, null, 1));
