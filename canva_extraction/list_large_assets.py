import os

files = os.listdir('assets_hires')
files.sort(key=lambda x: os.path.getsize(os.path.join('assets_hires', x)), reverse=True)
print("Top 25 largest files in assets_hires:")
for f in files[:25]:
    sz = os.path.getsize(os.path.join('assets_hires', f))
    print(f" {f:35s} {sz:>10,d} bytes")
