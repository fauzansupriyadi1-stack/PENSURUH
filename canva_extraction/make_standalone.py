with open('public/rendered_preview.html', 'r', encoding='utf-8') as f:
    html = f.read()

# Replace absolute http://localhost/ with relative ./
html = html.replace('http://localhost/', './')
html = html.replace('src="/images/', 'src="./images/')
html = html.replace("url('/images/", "url('./images/")

with open('public/index.html', 'w', encoding='utf-8') as f:
    f.write(html)
print('Wrote public/index.html')
