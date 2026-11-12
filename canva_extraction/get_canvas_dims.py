import re

with open('canva_page2.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

scripts = re.findall(r'<script[^>]*>(.*?)</script>', content, re.DOTALL)
s5 = scripts[5]

# Let's search for all element bounding boxes in Canva coordinate space!
# In Canva, the canvas dimensions can be found. Let's find the canvas width and height:
canvas_match = re.search(r'"width":(\d+),"height":(\d+)', s5)
if canvas_match:
    print(f"Canvas size in JSON: {canvas_match.group(1)} x {canvas_match.group(2)}")

# Let's search for "canvas" or document dimensions
dim_matches = re.findall(r'"([a-zA-Z0-9_-]+)":\{"width":(\d+),"height":(\d+)\}', s5)
print("Dimension matches:", dim_matches[:10])

# Also let's find all objects with "A":y, "B":x, "D":w, "C":h in s5
all_coords = re.findall(r'\{"A":([-0-9\.]+),"B":([-0-9\.]+),"D":([-0-9\.]+),"C":([-0-9\.]+),', s5)
print(f"Total element coordinates found: {len(all_coords)}")
for i, c in enumerate(all_coords[:30]):
    print(f"  Item {i:02d}: Y={float(c[0]):.1f}, X={float(c[1]):.1f}, W={float(c[2]):.1f}, H={float(c[3]):.1f}")
