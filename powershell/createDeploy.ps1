Import-Module AWSPowerShell
Set-DefaultAWSRegion -Region us-east-1

$date = get-date -format yyyy-MM-dd-HHmmss
$datestr = $date.ToString()
$name = "archive-"+$datestr+".zip"
$file = "deploy\"+$name
git add --all 
git commit -m "dev deploy-"+$name
git archive -v -o $file --format=zip HEAD
Write-S3Object -BucketName sjcarchivefiles-dev -File $file
aws elasticbeanstalk create-application-version --application-name "SJC_Archive" --version-label $name --description sjc_archive_dev --source-bundle S3Bucket="sjcarchivefiles-dev",S3Key=$name
Update-EBEnvironment -ApplicationName "SJC_Archive" -EnvironmentName "dev-sjcarchive" -VersionLabel $name 
#New-EBApplicationVersion -ApplicationName SJC_Archive -VersionLabel $name -SourceBuildInformation_SourceType Zip -SourceBuildInformation_SourceRepository S3 -SourceBuildInformation_SourceLocation sjcarchivefiles-dev/$name
