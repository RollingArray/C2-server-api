<?php

//namespace C2;
error_reporting(~0); ini_set('display_errors', 1);

//settings
require_once __DIR__.'/settings.php';

//lib
require_once __DIR__.'/app/lib/BaseAPI.class.php';
require_once __DIR__.'/app/lib/BaseDatabaseAPI.class.php';
require_once __DIR__.'/app/lib/DBAccessLib.class.php';
require_once __DIR__.'/app/lib/UtilityLib.class.php';
require_once __DIR__.'/app/lib/ValidationLib.class.php';
require_once __DIR__.'/app/lib/MessageLib.class.php';
require_once __DIR__.'/app/lib/SessionLib.class.php';
require_once __DIR__.'/app/lib/EmailLib.class.php';
require_once __DIR__.'/app/lib/NotificationLib.class.php';
require_once __DIR__.'/app/lib/JWTLib.class.php';

//controllers
require_once __DIR__.'/app/controllers/UserController.php';
require_once __DIR__.'/app/controllers/CommunityController.php';

$UtilityLib = new C2\Utility\UtilityLib($settings);
// //instances
$DBAccessLib = new C2\Database\DBAccessLib($settings);

$EmailLib = new C2\Email\EmailLib($settings);

$NotificationLib = new C2\Notification\NotificationLib($settings);

$passedData = array(
  "user_email"=>'ranjoy.sen85@gmail.com',
  "user_full_name"=>'Ranjoy Sen'
);

echo $EmailLib->sendTestEmail($DBAccessLib, $UtilityLib, $passedData);

?>

