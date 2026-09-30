"""Build dist/<slug>-<version>.zip from the theme folder (forward slashes, folder root inside the zip)."""
import os
import re
import sys
import zipfile

ROOT = os.path.dirname(os.path.abspath(__file__))
THEME = os.path.join(ROOT, 'm-nata')

with open(os.path.join(THEME, 'style.css'), encoding='utf-8') as fh:
    version = re.search(r'^Version:\s*(\S+)', fh.read(), re.M).group(1)

out_dir = os.path.join(ROOT, 'dist')
os.makedirs(out_dir, exist_ok=True)
out = os.path.join(out_dir, 'm-nata-%s.zip' % version)

with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as z:
    for folder, _, files in os.walk(THEME):
        for name in sorted(files):
            path = os.path.join(folder, name)
            arc = os.path.relpath(path, ROOT).replace(os.sep, '/')
            z.write(path, arc)

with zipfile.ZipFile(out) as z:
    names = z.namelist()
    raw = sum(i.file_size for i in z.infolist()) // 1024
    print('%s: %d files, %d KB raw, %d KB zip' % (os.path.basename(out), len(names), raw, os.path.getsize(out) // 1024))
    print('backslash in names:', any(chr(92) in n for n in names))
    sys.exit(0)
