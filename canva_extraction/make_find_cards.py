# Let's inspect crops/03_cards.png
# Let's find where the cards are located in 03_cards.png (which is from y=850 to 1500 of canva_doc_1.png)
# In canva_doc_1.png:
# Let's find the card boundaries by analyzing pixel colors or non-white regions
import os

ps = """
Add-Type -AssemblyName System.Drawing
$cards = [System.Drawing.Bitmap]::FromFile("c:\\Users\\fauzan supriyadi\\OneDrive\\Dokumen\\PROJECT-WORKSHOP\\crops\\03_cards.png")
Write-Output "Cards crop dimensions: $($cards.Width) x $($cards.Height)"

# Sample horizontal slice at y=250 in 03_cards.png to find card boundaries
$y = 250
$inCard = $false
$cardStarts = @()
$cardEnds = @()
for ($x = 0; $x -lt $cards.Width; $x++) {
    $c = $cards.GetPixel($x, $y)
    $isWhite = ($c.R -gt 245 -and $c.G -gt 245 -and $c.B -gt 245)
    if (-not $isWhite -and -not $inCard) {
        $inCard = $true
        $cardStarts += $x
    } elseif ($isWhite -and $inCard) {
        $inCard = $false
        $cardEnds += $x
    }
}
if ($inCard) { $cardEnds += $cards.Width }

for ($i = 0; $i -lt $cardStarts.Count; $i++) {
    Write-Output "Card $($i+1): X=$($cardStarts[$i]) to $($cardEnds[$i]) (Width = $($cardEnds[$i] - $cardStarts[$i]))"
}
$cards.Dispose()
"""

with open('find_cards.ps1', 'w', encoding='utf-8') as f:
    f.write(ps)
