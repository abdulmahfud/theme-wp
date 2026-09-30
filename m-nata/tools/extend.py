"""Helper for building the demo articles: insert extra paragraphs before the closing paragraph of matching articles.

Usage: python extend.py <category> <json-file>
The json file maps a title prefix to a list of paragraphs. Kept in the repo so the demo set can be re-tuned.
"""
import json
import os
import sys

HERE = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), 'm-nata', 'demo')
category, spec = sys.argv[1], sys.argv[2]
extra_map = json.load(open(spec, encoding='utf-8'))

path = os.path.join(HERE, 'articles', category + '.txt')
blocks = open(path, encoding='utf-8').read().split('### ')
out = [blocks[0]]
for block in blocks[1:]:
    head, _, body = block.partition('\n')
    paras = body.strip().split('\n\n')
    for prefix, extra in extra_map.items():
        if head.startswith(prefix):
            paras = paras[:-1] + extra + paras[-1:]
    out.append(head + '\n' + '\n\n'.join(paras) + '\n')
open(path, 'w', encoding='utf-8', newline='').write('### '.join(out))
print('ok', category)
