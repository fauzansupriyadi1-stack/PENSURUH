import re
import os

with open('canva_page2.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

# In download_unique_assets.py:
# We iterated through uris and saved to assets_hires/asset_{idx}_{w}x{h}.ext
# Let's inspect which GUID corresponds to which asset_idx!
# Let's rebuild the map:
asset_matches = set(re.findall(r'https://media\.canva\.com/v2/image-resize/[^\s"\'<>]+', content))
uris = {}
for m in asset_matches:
    url = m.replace('&amp;', '&')
    width_match = re.search(r'width:(\d+)', url)
    height_match = re.search(r'height:(\d+)', url)
    w = int(width_match.group(1)) if width_match else 0
    h = int(height_match.group(1)) if height_match else 0
    uri_key = re.search(r'uri:([^?]+)', url)
    if uri_key:
        key = uri_key.group(1)
        if key not in uris or (w * h > uris[key]['w'] * uris[key]['h']):
            uris[key] = {'url': url, 'w': w, 'h': h}

guid_to_file = {}
for idx, (k, info) in enumerate(uris.items()):
    ext = '.png' if 'format:PNG' in info['url'] else '.jpg'
    fname = f"asset_{idx}_{info['w']}x{info['h']}{ext}"
    # find guid in k
    gm = re.search(r'([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})', k)
    if gm:
        guid = gm.group(1)
        guid_to_file[guid] = fname

print(f"Mapped {len(guid_to_file)} GUIDs to filenames:")
for g, fn in guid_to_file.items():
    print(f" {g} -> {fn}")
