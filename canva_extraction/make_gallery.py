import os

files = [f for f in os.listdir('assets_hires') if f.endswith('.jpg') or f.endswith('.png')]
files.sort(key=lambda x: os.path.getsize(os.path.join('assets_hires', x)), reverse=True)

html = "<html><body style='font-family:sans-serif;display:flex;flex-wrap:wrap;gap:15px;background:#eee;padding:20px;'>"
for f in files[:35]:
    sz = os.path.getsize(os.path.join('assets_hires', f))
    html += f"<div style='background:white;padding:10px;border-radius:8px;max-width:320px;'><img src='assets_hires/{f}' style='max-width:300px;max-height:200px;object-fit:cover;border-radius:6px;'><p style='font-size:12px;margin:5px 0;'><b>{f}</b><br>{sz:,} bytes</p></div>"
html += "</body></html>"

with open('gallery.html', 'w', encoding='utf-8') as out:
    out.write(html)
print("Created gallery.html")
