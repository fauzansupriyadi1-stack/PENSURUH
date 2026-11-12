import os

# We can read JPEG/PNG headers or sample bytes to see colors, or use PowerShell System.Drawing
# Let's generate a powershell script to sample color at top-left, center, bottom-right
ps_script = """
Add-Type -AssemblyName System.Drawing
$files = @(
    "asset_29_1500x1000.jpg",
    "asset_37_1280x720.jpg",
    "asset_19_900x601.jpg",
    "asset_22_800x534.jpg",
    "asset_69_800x533.jpg",
    "asset_70_640x480.jpg",
    "asset_33_800x450.jpg",
    "asset_18_736x1472.png",
    "asset_41_400x800.png",
    "asset_30_1200x1200.png",
    "asset_23_735x490.png",
    "asset_0_650x434.png"
)

foreach ($f in $files) {
    $path = "assets_hires\\$f"
    if (Test-Path $path) {
        $bmp = [System.Drawing.Bitmap]::FromFile((Resolve-Path $path).Path)
        $cCenter = $bmp.GetPixel([int]($bmp.Width/2), [int]($bmp.Height/2))
        $cTop = $bmp.GetPixel([int]($bmp.Width/2), [int]($bmp.Height*0.1))
        $cBottom = $bmp.GetPixel([int]($bmp.Width/2), [int]($bmp.Height*0.9))
        Write-Output "$f | Center: R=$($cCenter.R) G=$($cCenter.G) B=$($cCenter.B) | Top: R=$($cTop.R) G=$($cTop.G) B=$($cTop.B) | Bottom: R=$($cBottom.R) G=$($cBottom.G) B=$($cBottom.B)"
        $bmp.Dispose()
    }
}
"""

with open('identify_images.ps1', 'w', encoding='utf-8') as out:
    out.write(ps_script)
