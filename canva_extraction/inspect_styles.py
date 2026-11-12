import re
import json

with open('canva_page2.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

scripts = re.findall(r'<script[^>]*>(.*?)</script>', content, re.DOTALL)
s5 = scripts[5]

# Let's extract all color codes (#...) from s5
hex_colors = set(re.findall(r'#(?:[0-9a-fA-F]{3,8})\b', s5))
print("All Hex Colors in s5:")
for c in sorted(hex_colors):
    print(" ", c)

# Let's find font names in s5
# Canva font references often look like font family names or Canva font IDs
font_matches = set(re.findall(r'font-family:[^;]+|family:"([^"]+)"|"fontFamily":"([^"]+)"', s5))
print("\nFont matches:")
for fm in font_matches:
    print(" ", fm)

# Let's search for font declarations in the HTML or css
css_fonts = set(re.findall(r'@font-face\s*\{[^}]+\}', content))
print(f"\nCSS font faces: {len(css_fonts)}")
for cf in list(css_fonts)[:5]:
    print(" ", cf[:150])
