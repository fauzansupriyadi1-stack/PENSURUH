import re
import json

with open('canva_page2.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

scripts = re.findall(r'<script[^>]*>(.*?)</script>', content, re.DOTALL)
s5 = scripts[5]

# Let's search for text nodes and their properties
# Canva document schema has objects with 'a' containing 'C' (text runs) or 'A' (text strings)
# Let's find all text blocks and extract them with their styles
text_blocks = []
# Match patterns like: "a":{"C":{"A":["..."]}...}
# Or let's inspect regex for "A":[...]
for m in re.finditer(r'\{[^{}]*"A":\s*(\[[^\]]+\])[^{}]*\}', s5):
    try:
        data = m.group(0)
        text_blocks.append(data)
    except:
        pass

print(f"Found {len(text_blocks)} simple text blocks")

# Let's also do a broader search for any Indonesian words or UI words in s5
pattern = r'"([A-Za-z0-9\s\.\,\-\:\>\<\?\!\@\#\%\(\)\/\+]{3,100})"'
matches = re.findall(pattern, s5)
relevant = [m for m in matches if any(w in m.lower() for w in [
    'pensuruh', 'suruh', 'bisa tawar', 'customer service', 'nyruh', 'fleksibel',
    'review', 'about', 'pricing', 'contact', 'login', 'register', 'mitra',
    'garansi', 'jaminan', 'booking', 'terpercaya', 'panduan'
])]
print(f"Found {len(relevant)} relevant text fragments:")
for r in set(relevant):
    print(" -", r)
