
Add-Type -AssemblyName System.Drawing
$cards = [System.Drawing.Bitmap]::FromFile("c:\Users\fauzan supriyadi\OneDrive\Dokumen\PROJECT-WORKSHOP\crops\03_cards.png")
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
