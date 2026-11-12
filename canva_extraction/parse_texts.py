import re
import json

with open('canva_page2.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

scripts = re.findall(r'<script[^>]*>(.*?)</script>', content, re.DOTALL)
s5 = scripts[5]

# Let's inspect objects in s5
# Canva document model usually has pages, rows, elements.
# In s5, let's find all text arrays: "A": ["..."]
# and their surrounding object
matches = re.finditer(r'\{[^{}]*"A":\s*(\[[^\]]+\])', s5)
elements = []
for m in matches:
    # let's look at the context around it (say 300 chars before and 300 chars after)
    start = max(0, m.start() - 200)
    end = min(len(s5), m.end() + 200)
    snippet = s5[start:end]
    elements.append(snippet)

print(f"Total text blocks found: {len(elements)}")
with open('extracted_text_blocks.txt', 'w', encoding='utf-8') as out:
    for idx, e in enumerate(elements):
        out.write(f"--- Block {idx} ---\n{e}\n\n")

print("Saved to extracted_text_blocks.txt")
