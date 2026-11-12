import json
import re
import urllib.request
import os

with open('canva_page2.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

# Let's inspect all image-resize URLs and their parameters
# Canva image-resize URLs typically look like:
# https://media.canva.com/v2/image-resize/format:JPG/height:100/quality:75/uri:...
# If we replace height:100, width:150 with higher resolution or request original uri!
asset_matches = set(re.findall(r'https://media\.canva\.com/v2/image-resize/[^\s"\'<>]+', content))
print(f"Total unique image-resize URLs: {len(asset_matches)}")

# Let's see what unique 'uri:' exists in these URLs
uris = {}
for m in asset_matches:
    url = m.replace('&amp;', '&')
    uri_match = re.search(r'uri:([^/]+)', url)
    width_match = re.search(r'width:(\d+)', url)
    height_match = re.search(r'height:(\d+)', url)
    w = int(width_match.group(1)) if width_match else 0
    h = int(height_match.group(1)) if height_match else 0
    uri_key = re.search(r'uri:([^?]+)', url)
    if uri_key:
        key = uri_key.group(1)
        if key not in uris or (w * h > uris[key]['w'] * uris[key]['h']):
            uris[key] = {'url': url, 'w': w, 'h': h}

print(f"Found {len(uris)} unique image objects:")
os.makedirs('assets_hires', exist_ok=True)
for idx, (k, info) in enumerate(uris.items()):
    print(f"[{idx}] {k} -> {info['w']}x{info['h']}")
    try:
        req = urllib.request.Request(info['url'], headers={'User-Agent': 'Mozilla/5.0'})
        ext = '.png' if 'format:PNG' in info['url'] else '.jpg'
        outpath = f"assets_hires/asset_{idx}_{info['w']}x{info['h']}{ext}"
        with urllib.request.urlopen(req) as resp, open(outpath, 'wb') as out:
            out.write(resp.read())
        print(f"  Saved to {outpath}")
    except Exception as e:
        print(f"  Failed: {e}")
