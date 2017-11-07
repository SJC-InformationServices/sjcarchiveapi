$date = (Get-Date).ToString("yyyy-MM-dd HH-mm")
$src  = "C:\Users\kevin_000\Documents\DevWorkSpaces\storedd\"
$outfile = "C:\Users\kevin_000\Documents\DevWorkSpaces\deploy\deploy_v2-" + $date + ".zip"
$ex = @("lib","install",".vscode")
Remove-Item -path "C:\Users\kevin_000\Documents\DevWorkSpaces\deploy\*" -Filter *deploy*.zip -whatif
Get-ChildItem $src | where {$_.Name -notin $ex} | Compress-Archive -DestinationPath $outfile -Update -CompressionLevel Fastest 


