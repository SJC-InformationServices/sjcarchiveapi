$date = (Get-Date).ToString("yyyy-MM-dd HH-mm")
$src  = "C:\Users\kevin_000\Documents\DevWorkSpaces\storedd\"
$outfile = "C:\Users\kevin_000\Documents\DevWorkSpaces\deploy\deploy_v2-" + $date + ".zip"
$ex = @("lib","install",".vscode","createDeploy.ps1","index.1.html","node_modules","powershell","index.html")

Get-ChildItem $src | where {$_.Name -notin $ex} | Compress-Archive -DestinationPath $outfile -Update -CompressionLevel Fastest 