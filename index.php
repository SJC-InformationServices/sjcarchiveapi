<!doctype html>
<html lang="en">
<head>
</head>
<body>
<?php
require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/vendor/redbean/rb.php";
echo "So it begins! ";
$storedd = new \storedd\modules\log();
var_dump($storedd);
?>
</body>
</html>