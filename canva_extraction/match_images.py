import re
import json

with open('canva_page2.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

scripts = re.findall(r'<script[^>]*>(.*?)</script>', content, re.DOTALL)
s5 = scripts[5]

# Find all image elements in s5
# Canva image elements usually look like:
# {"A":y,"B":x,"D":w,"C":h,"_":"...","a":{"url":... or "uri":... or "media":...}}
# Or let's search for "ifs%3A%2F%2FM%2F([0-9a-f\-]+)" and look at the context around it
matches = re.finditer(r'ifs(?:%3A%2F%2FM%2F|://M/)([0-9a-f\-]+)', s5)
found_guids = {}
for m in matches:
    guid = m.group(1)
    start = max(0, m.start() - 300)
    end = min(len(s5), m.end() + 300)
    ctx = s5[start:end]
    coord_m = re.search(r'"A":(?P<y>[-0-9\.]+),"B":(?P<x>[-0-9\.]+),"D":(?P<w>[-0-9\.]+),"C":(?P<h>[-0-9\.]+)', ctx)
    coords = coord_m.groupdict() if coord_m else {}
    if guid not in found_guids:
        found_guids[guid] = []
    found_guids[guid].append(coords)

print(f"Unique image GUIDs in s5: {len(found_guids)}")
for g, coord_list in found_guids.items():
    print(f"GUID: {g}")
    for c in coord_list:
        if c:
            print(f"  Pos: Y={c.get('y')} X={c.get('x')} W={c.get('w')} H={c.get('h')}")
