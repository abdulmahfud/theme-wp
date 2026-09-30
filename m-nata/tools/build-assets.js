/**
 * Minifies the theme's CSS and JS into *.min.css / *.min.js next to the sources.
 * The theme automatically serves the .min files (unless SCRIPT_DEBUG is on); keep them committed.
 *
 * Usage (needs csso + terser installed in any folder):
 *   npm i csso terser
 *   NODE_PATH=./node_modules node <repo>/tools/build-assets.js
 */
const fs = require('fs');
const path = require('path');
const csso = require('csso');
const { minify } = require('terser');

const assets = path.join(__dirname, '..', 'm-nata', 'assets');

(async () => {
	const report = [];

	const css = path.join(assets, 'css', 'main.css');
	const cssOut = csso.minify(fs.readFileSync(css, 'utf8'), { restructure: true, comments: false }).css;
	fs.writeFileSync(css.replace(/\.css$/, '.min.css'), cssOut);
	report.push(['css/main.css', fs.statSync(css).size, Buffer.byteLength(cssOut)]);

	const jsDir = path.join(assets, 'js');
	for (const file of fs.readdirSync(jsDir)) {
		if (!file.endsWith('.js') || file.endsWith('.min.js')) { continue; }
		const src = fs.readFileSync(path.join(jsDir, file), 'utf8');
		const out = await minify(src, { compress: { passes: 2 }, mangle: true, format: { comments: false } });
		if (out.error) { throw out.error; }
		fs.writeFileSync(path.join(jsDir, file.replace(/\.js$/, '.min.js')), out.code);
		report.push(['js/' + file, Buffer.byteLength(src), Buffer.byteLength(out.code)]);
	}

	report.forEach((r) => console.log(r[0].padEnd(22), String(r[1]).padStart(7), '->', String(r[2]).padStart(7), 'bytes'));
})().catch((e) => { console.error(e); process.exit(1); });
