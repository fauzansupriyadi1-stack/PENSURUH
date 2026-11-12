import json
import re

with open('canva_page2.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

scripts = re.findall(r'<script[^>]*>(.*?)</script>', content, re.DOTALL)
s5 = scripts[5]

# Look for all occurrences of image URLs or http links in s5
urls = set(re.findall(r'https?://[^\s"\'\\<>\)]+', s5))
print(f"Total URLs found in s5: {len(urls)}")
image_urls = [u for u in urls if any(ext in u.lower() for ext in ['.png', '.jpg', '.jpeg', '.svg', '.webp']) or 'media' in u.lower() or 'image' in u.lower()]
print(f"Image-like URLs: {len(image_urls)}")
for u in sorted(image_urls)[:30]:
    print(" ", u)

# Also look across the entire html content
all_urls = set(re.findall(r'https?://[^\s"\'\\<>\)]+', content))
print(f"\nTotal URLs in entire HTML: {len(all_urls)}")
all_images = [u for u in all_urls if any(ext in u.lower() for ext in ['.png', '.jpg', '.jpeg', '.svg', '.webp', 'canva-media', 'canva.com/media'])]
print(f"Total image-like URLs in HTML: {len(all_images)}")
for u in sorted(all_images)[:30]:
    print(" ", u)
