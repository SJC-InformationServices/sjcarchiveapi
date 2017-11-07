$date = (Get-Date).ToString("yyyy-MM-dd")
$src  = "C:\Users\kevin_000\Documents\DevWorkSpaces\storedd\"
$outfile = "C:\Users\kevin_000\Documents\DevWorkSpaces\deploy_v2-" + $date + ".zip"
$ex = @("lib","install",".vscode")
Remove-Item $outfile
Get-ChildItem $src | where {$_.Name -notin $ex} | Compress-Archive -DestinationPath $outfile -Update -CompressionLevel Fastest 


