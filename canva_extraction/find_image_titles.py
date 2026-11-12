import re
import json

with open('canva_page2.html', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

# Look for titles or names associated with the GUIDs
guids = [
    '629fe97a-1123-4816-9051-a723aa8e2193',
    'de5caf0c-3ec6-483a-a447-58dde0b358ac',
    '5253aaee-91bf-447e-9c5d-e841868fd07f',
    '2d6ada3a-b67b-4ef4-83cb-473cf5ce61d7',
    '18934d42-9117-4b66-a699-bba9c65b6627',
    '302db99c-9d4c-40b7-94c1-7f3cc833db84',
    'bd3a9a8b-d11b-4f5c-aba9-1a3565ba3a22',
    'c1f22042-ec1b-4625-8c8e-357c3b87736d',
    '32b75703-231f-4238-8577-1cd58f9d7fa1',
    '3a288717-d362-4daa-91bb-25fda1007d7f',
    '16a084dd-7667-4861-9f59-8cee2200b911',
    'b3966cbc-504a-4bd7-a016-84763900fcbe',
    '098ba6ec-1a1e-48b4-84d3-8e7835edfc03',
    'eefc4a6e-e07d-461a-9edc-03d059b15365'
]

for g in guids:
    idx = content.find(g)
    print(f"=== GUID {g} ===")
    while idx != -1:
        snippet = content[max(0, idx - 150):min(len(content), idx + 250)]
        # look for title, name, or keywords
        title_m = re.findall(r'"title":"([^"]+)"|"name":"([^"]+)"|"alt":"([^"]+)"', snippet)
        if title_m:
            print("  Title/Alt:", title_m)
        idx = content.find(g, idx + len(g))
