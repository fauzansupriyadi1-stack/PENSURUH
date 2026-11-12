import re
import json

with open('canva_page2.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

scripts = re.findall(r'<script[^>]*>(.*?)</script>', content, re.DOTALL)
s5 = scripts[5]

# Let's extract all "A": [...] text strings and any adjacent "M": "#hex", "G": "...px", "I": "bold", etc.
# In Canva JSON:
# "A": ["string\n", ...]
# "G": font size (e.g. "37.3333px")
# "M": font color (e.g. "#0471fc", "#000000", "#ffffff", "#f3c903")
# "I": "bold"
# "c": "center" / "left" / "right"
# Coordinates: "B": left/x or top/y, "A": top/y or left/x, "D": width, "C": height

# Let's find each element object: {"A":..., "B":..., "C":..., "D":..., "_":"...", "a":{...}}
raw_blocks = re.findall(r'(\{"A":[-0-9\.]+,"B":[-0-9\.]+,"D":[-0-9\.]+,"C":[-0-9\.]+,"_":"[^"]+","a":\{.*?"A":\[.*?\].*?\}\})', s5)
print(f"Direct element matches: {len(raw_blocks)}")

# Let's do a more robust regex that grabs the element and its text
items = []
pattern = re.compile(r'"_":"(?P<id>[^"]+)",(?P<props>.*?"a":\{.*?"A":(?P<text>\[.*?\]).*?\})', re.DOTALL)
# Or let's scan for "A":["..."] and walk backward to find the element coordinates
for m in re.finditer(r'"A":(\["(?:[^"\\]|\\.)*"\])', s5):
    # m is text array
    text_json = m.group(1)
    try:
        text_list = json.loads(text_json)
        text_str = "".join(text_list).strip()
    except:
        text_str = text_json
    
    # context around
    start = max(0, m.start() - 250)
    end = min(len(s5), m.end() + 250)
    ctx = s5[start:end]
    
    # colors
    colors = re.findall(r'"M":"(#[0-9a-fA-F]{3,8})"', ctx)
    fonts = re.findall(r'"G":"([^"]+)"', ctx)
    bold = '"I":"bold"' in ctx
    align = re.findall(r'"c":"(center|left|right|justify)"', ctx)
    
    # coords
    coord_m = re.search(r'"A":(?P<y>[-0-9\.]+),"B":(?P<x>[-0-9\.]+),"D":(?P<w>[-0-9\.]+),"C":(?P<h>[-0-9\.]+)', ctx)
    coords = coord_m.groupdict() if coord_m else {}
    
    items.append({
        'text': text_str,
        'colors': list(set(colors)),
        'fonts': list(set(fonts)),
        'bold': bold,
        'align': align[0] if align else 'inherit',
        'coords': coords
    })

# Print unique non-empty items sorted by Y coordinate if available
print(f"\n--- Extracted Texts ({len(items)}) ---")
unique_items = []
seen = set()
for it in items:
    k = (it['text'], str(it['coords']))
    if k not in seen and it['text']:
        seen.add(k)
        unique_items.append(it)

# Sort by Y coordinate
def get_y(it):
    try:
        return float(it['coords'].get('y', 0))
    except:
        return 0

unique_items.sort(key=get_y)

for i, it in enumerate(unique_items):
    print(f"[{i:02d}] Y={it['coords'].get('y')} X={it['coords'].get('x')} W={it['coords'].get('w')} H={it['coords'].get('h')}")
    print(f"     Text: {repr(it['text'])}")
    print(f"     Color: {it['colors']} | Font: {it['fonts']} | Bold: {it['bold']} | Align: {it['align']}")
