
Add-Type -AssemblyName System.Drawing

$src = [System.Drawing.Bitmap]::FromFile("c:\Users\fauzan supriyadi\OneDrive\Dokumen\PROJECT-WORKSHOP\canva_doc_1.png")
$outDir = "c:\Users\fauzan supriyadi\OneDrive\Dokumen\PROJECT-WORKSHOP\public\images"
if (!(Test-Path $outDir)) { New-Item -ItemType Directory -Force -Path $outDir | Out-Null }

function Save-Crop($name, $x, $y, $w, $h) {
    $rect = New-Object System.Drawing.Rectangle($x, $y, $w, $h)
    $crop = $src.Clone($rect, $src.PixelFormat)
    $dest = Join-Path $outDir $name
    $crop.Save($dest, [System.Drawing.Imaging.ImageFormat]::Png)
    $crop.Dispose()
    Write-Output "Saved $dest ($w x $h)"
}

# 1. Hero Image (the entire rounded hero banner with courier and customer)
# In canva_doc_1.png, hero starts below navbar around y=68 to y=575, x=45 to 979 (width ~934, height ~507)
Save-Crop "hero_banner.png" 45 68 934 507

# 2. Hero "Vidio Panduan Order" badge
# Located inside hero on bottom right
Save-Crop "video_badge.png" 665 400 295 125

# 3. Hero "Daftar Sekarang" button
# Located on hero bottom left
Save-Crop "hero_daftar_btn.png" 110 470 162 48

# 4. About Cleaners image (cleaners mopping floor)
# Starts around y=630, x=130, w=210, h=150
Save-Crop "about_cleaners.png" 128 625 212 150

# 5. Carousel Cards:
# Card 1: Hijab woman grocery delivery
Save-Crop "card_1_delivery.png" 47 1028 77 305

# Card 2: Plant watering
Save-Crop "card_2_plants.png" 392 1028 245 305

# Card 3: Couch chat
Save-Crop "card_3_chat.png" 647 1028 245 305

# Card 4: Courier peeking
Save-Crop "card_4_courier.png" 902 1028 75 305

# 6. Pricing Left Image (Woman in hijab receiving money/grocery)
Save-Crop "pricing_image.png" 115 1430 266 320

# 7. 100% Di Jamin Terpercaya Badge
Save-Crop "badge_100_percent.png" 115 1775 266 75

# 8. Booking Suruh Banner
Save-Crop "booking_banner.png" 392 1775 508 75

# 9. Footer full bar
Save-Crop "footer_bar.png" 30 2038 964 125

# 10. Entire Page Background Gradient Glow (bottom area)
Save-Crop "page_bottom_glow.png" 0 2160 1024 400

$src.Dispose()
