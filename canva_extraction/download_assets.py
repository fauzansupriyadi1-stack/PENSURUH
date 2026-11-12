import urllib.request
import re
import urllib.parse
import os

with open('canva_page2.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

# Let's find all preview URLs or document images
matches = re.findall(r'https://media\.canva\.com/v2/document-image/[^\s"\'<>]+', content)
print(f"Found {len(matches)} document-image URLs")
for i, m in enumerate(matches):
    url = m.replace('&amp;', '&')
    print(f"URL {i}: {url[:120]}...")
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'})
        with urllib.request.urlopen(req) as resp, open(f'canva_doc_{i}.png', 'wb') as out:
            data = resp.read()
            out.write(data)
            print(f"  Saved canva_doc_{i}.png ({len(data)} bytes)")
    except Exception as e:
        print(f"  Error: {e}")

# Also check for individual assets/images in the document
asset_matches = re.findall(r'https://media\.canva\.com/v2/image-resize/[^\s"\'<>]+', content)
print(f"Found {len(asset_matches)} image-resize URLs")
for i, m in enumerate(asset_matches[:10]):
    url = m.replace('&amp;', '&')
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'})
        with urllib.request.urlopen(req) as resp, open(f'canva_asset_{i}.jpg', 'wb') as out:
            data = resp.read()
            out.write(data)
            print(f"  Saved canva_asset_{i}.jpg ({len(data)} bytes)")
    except Exception as e:
        print(f"  Error: {e}")
