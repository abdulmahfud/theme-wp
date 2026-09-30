"""Release gate + packager for the M-News theme.

    python tools/release.py               # production build: refuses to run with the development licence key
    python tools/release.py --allow-dev-key   # test build (e.g. to try the install/update flow before the backend is live)

Checks (any failure stops the build):
  * version in style.css == "Stable tag" in readme.txt, and it is a x.y.z version
  * every PHP file passes `php -l`
  * minified assets exist and are newer than their sources (run tools/build-assets.js first)
  * licence public key is NOT the development key (unless --allow-dev-key); licence API default is https
  * no debug leftovers (var_dump / print_r / error_log / console.log) in shipped code
  * a .pot file exists
Then writes dist/m-news-<version>.zip and dist/m-news-<version>.zip.sha256.
"""
import glob
import hashlib
import os
import re
import subprocess
import sys
import zipfile

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
THEME = os.path.join(ROOT, 'm-news')
DEV_KEY = 's0xcf+xtJWFLNCC56QBtyaoXf3r4QGrE099UfCQdNmI='
allow_dev = '--allow-dev-key' in sys.argv
errors = []


def read(rel):
    return open(os.path.join(THEME, rel), encoding='utf-8').read()


def fail(msg):
    errors.append(msg)
    print('  FAIL  ' + msg)


def ok(msg):
    print('  ok    ' + msg)


# 1. versions
version = re.search(r'^Version:\s*(\S+)', read('style.css'), re.M).group(1)
stable = re.search(r'^Stable tag:\s*(\S+)', read('readme.txt'), re.M).group(1)
if not re.match(r'^\d+\.\d+\.\d+$', version):
    fail('style.css version "%s" is not x.y.z' % version)
elif version != stable:
    fail('style.css version %s != readme.txt Stable tag %s' % (version, stable))
else:
    ok('version %s (style.css = readme.txt)' % version)

# 2. php -l
bad = 0
for path in glob.glob(os.path.join(THEME, '**', '*.php'), recursive=True):
    res = subprocess.run(['php', '-l', path], capture_output=True, text=True)
    if res.returncode != 0:
        bad += 1
        fail('php -l: ' + os.path.relpath(path, THEME))
if not bad:
    ok('php -l on all PHP files')

# 3. minified assets fresh
stale = []
for src in glob.glob(os.path.join(THEME, 'assets', 'css', '*.css')) + glob.glob(os.path.join(THEME, 'assets', 'js', '*.js')):
    if src.endswith('.min.css') or src.endswith('.min.js'):
        continue
    mini = re.sub(r'\.(css|js)$', r'.min.\1', src)
    if not os.path.exists(mini) or os.path.getmtime(mini) < os.path.getmtime(src):
        stale.append(os.path.relpath(src, THEME))
if stale:
    fail('minified files missing/stale for: ' + ', '.join(stale) + '  (run tools/build-assets.js)')
else:
    ok('minified CSS/JS are up to date')

# 4. licence config
cfg = read('inc/license/config.php')
if DEV_KEY in cfg:
    (ok if allow_dev else fail)('licence public key is the DEVELOPMENT key' + (' (allowed by --allow-dev-key)' if allow_dev else ' -> paste the production key (php artisan license:keys)'))
else:
    ok('licence public key is not the development key')
if "define( 'MNEWS_LICENSE_API', 'https://" not in cfg:
    fail('MNEWS_LICENSE_API default must be https')
else:
    ok('licence API default is https')

# 5. debug leftovers
leftovers = []
for path in glob.glob(os.path.join(THEME, '**', '*'), recursive=True):
    if not os.path.isfile(path) or path.endswith(('.min.js', '.png', '.jpg', '.pot', '.txt')):
        continue
    text = open(path, encoding='utf-8', errors='ignore').read()
    for pattern in (r'var_dump\(', r'print_r\(', r'error_log\(', r'console\.log\(', r'(?<![A-Za-z_>:])dd\('):
        if re.search(pattern, text) and not path.endswith('.min.css'):
            leftovers.append('%s: %s' % (os.path.relpath(path, THEME), pattern))
if leftovers:
    fail('debug leftovers: ' + '; '.join(leftovers[:6]))
else:
    ok('no debug leftovers')

# 6. pot
if os.path.exists(os.path.join(THEME, 'languages', 'm-news.pot')):
    ok('languages/m-news.pot present')
else:
    fail('languages/m-news.pot missing')

if errors:
    print('\nRelease blocked: %d problem(s).' % len(errors))
    sys.exit(1)

# package
out_dir = os.path.join(ROOT, 'dist')
os.makedirs(out_dir, exist_ok=True)
out = os.path.join(out_dir, 'm-news-%s.zip' % version)
with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as z:
    for folder, _, files in os.walk(THEME):
        for name in sorted(files):
            path = os.path.join(folder, name)
            z.write(path, os.path.relpath(path, ROOT).replace(os.sep, '/'))
digest = hashlib.sha256(open(out, 'rb').read()).hexdigest()
open(out + '.sha256', 'w').write('%s  %s\n' % (digest, os.path.basename(out)))
print('\nBuilt %s (%d KB)\nSHA-256 %s' % (os.path.relpath(out, ROOT), os.path.getsize(out) // 1024, digest))
