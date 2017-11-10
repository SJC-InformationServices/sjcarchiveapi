$date = (Get-Date).ToString("yyyy-MM-dd HH-mm")
$src  = "C:\Users\kevin_000\Documents\DevWorkSpaces\storedd"
$outfile = "C:\Users\kevin_000\Documents\DevWorkSpaces\deploy\archive\"
$ex = @("index.html","index.php","src")
$deployfile = "C:\Users\kevin_000\Documents\DevWorkSpaces\deploy\archive-" + $date + ".zip";
Get-ChildItem $src -Recurse | 
Where-Object {$_.FullName -like "*.php" -or $_.FullName -like "*.htaccess"} | 
ForEach-Object{
    $PathArray = $_.FullName.Replace($src,"").ToString().Split('\') 
    $Folder = $outfile
    for ($i=1; $i -lt $PathArray.length-1; $i++) {
        $Folder += "\" + $PathArray[$i]
        if (!(Test-Path $Folder)) {
            New-Item -ItemType directory -Path $Folder
        }
    }   
    $NewPath = Join-Path $outfile $_.FullName.Replace($src,"")
    Copy-Item $_.FullName -Destination $NewPath -Force
}
Compress-Archive -Path C:\Users\kevin_000\Documents\DevWorkSpaces\deploy\archive\* -DestinationPath $deployfile