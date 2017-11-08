$date = (Get-Date).ToString("yyyy-MM-dd HH-mm")
$src  = "C:\Users\kevin_000\Documents\DevWorkSpaces\storedd\"
$outfile = "C:\Users\kevin_000\Documents\DevWorkSpaces\deploy\deploy_v2-" + $date + ".zip"
$ex = @("lib","install",".vscode","vendor/squizlabs","createDeploy.ps1","index.1.html")

Get-ChildItem $src | where {$_.Name -notin $ex} | Compress-Archive -DestinationPath $outfile -Update -CompressionLevel Fastest 

aws s3 cp "c:\Users\kevin_000\Documents\DevWorkSpaces\storedd\index.html" s3://sjcarchivefiles-dev
aws s3 cp "c:\Users\kevin_000\Documents\DevWorkSpaces\storedd\lib\archive\css\archive.css" s3://sjcarchivefiles-dev/lib/archive/css/
aws s3 cp "c:\Users\kevin_000\Documents\DevWorkSpaces\storedd\lib\archive\js\" s3://sjcarchivefiles-dev/lib/archive/js/ --recursive
