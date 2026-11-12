import os
import subprocess

# Let's inspect the files in assets_hires and identify their dimensions
files = [f for f in os.listdir('assets_hires') if f.endswith('.jpg') or f.endswith('.png')]
for f in sorted(files):
    sz = os.path.getsize(os.path.join('assets_hires', f))
    if sz > 20000: # larger than 20KB
        print(f"{f:35s} {sz:>10,d} bytes")
