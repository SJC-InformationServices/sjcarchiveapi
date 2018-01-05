Import-Module AWSPowerShell
$date = get-date -format yyyy-MM-dd-THHmmss
$datestr = $date.ToString()
$file = "deploy\archive-"+$datestr+".zip"
$cmd = git archive -v -o $file --format=zip HEAD
#Write-S3Object -BucketName elasticbeanstalk-us-east-1-813784960196 -File $file