<?php




require_once __DIR__.'/vendor/autoload.php';

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
require_once __DIR__.'/app/lib/JWTLib.class.php';

    
//controllers
require_once __DIR__.'/app/controllers/UserController.php';

//instances
$userController = new C2\UserController($settings);

$userController->signUp();

?>