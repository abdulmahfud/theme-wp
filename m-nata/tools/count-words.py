"""Print the word count of every demo article (target: about 500)."""
import glob
import os

HERE = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), 'm-nata', 'demo')
for path in sorted(glob.glob(os.path.join(HERE, 'articles', '*.txt'))):
    text = open(path, encoding='utf-8').read()
    for block in text.split('### ')[1:]:
        head, _, body = block.partition('\n')
        title = head.split('|')[0].strip()
        words = len([w for w in body.replace('## ', ' ').split() if w])
        print('%-9s %4d  %s' % (os.path.basename(path)[:-4], words, title[:70]))
