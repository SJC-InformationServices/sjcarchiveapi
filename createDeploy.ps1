$date = (Get-Date).ToString("yyyy-MM-dd")
$src  = "C:\Users\kevin_000\Documents\DevWorkSpaces\storedd\"
$outfile = "C:\Users\kevin_000\Documents\DevWorkSpaces\deploy-" + $date + ".zip"

Compress-Archive -Path $src -DestinationPath $outfile -Force -CompressionLevel Fastest

