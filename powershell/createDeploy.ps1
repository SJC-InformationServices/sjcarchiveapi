Import-Module AWSPowerShell
Set-DefaultAWSRegion -Region us-east-1

$date = get-date -format yyyy-MM-dd-HHmmss
$datestr = $date.ToString()
$file = "deploy\archive-"+$datestr+".zip"
$cmd = git archive -v -o $file --format=zip HEAD
Write-S3Object -BucketName sjcarchivefiles-dev -File $file
New-EBApplicationVersion -ApplicationName SJC_Archive -VersionLabel $file -SourceBuildInformation_SourceType Zip -SourceBuildInformation_SourceRepository S3 -SourceBuildInformation_SourceLocation sjcarchivefiles-dev/$file -Process 0
#Update-EbApplicationVersion -ApplicationName SJC_Archive -VersionLabel $file