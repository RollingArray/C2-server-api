<?php
//php error reporting, 1 to emable, 0 to disable
error_reporting(~0); ini_set('display_errors', 1);
//error_reporting(~0); ini_set('display_errors', 1);
require_once __DIR__.'/vendor/autoload.php';

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
require_once __DIR__.'/app/lib/JWTLib.class.php';

//controllers
require_once __DIR__.'/app/controllers/UserController.php';
require_once __DIR__.'/app/controllers/ProjectController.php';
require_once __DIR__.'/app/controllers/SprintController.php';
require_once __DIR__.'/app/controllers/GoalController.php';
require_once __DIR__.'/app/controllers/ActivityController.php';

//instances
$userController = new C2\UserController($settings);
$projectController = new C2\ProjectController($settings);
$sprintController= new C2\SprintController($settings);
$goalController= new C2\GoalController($settings);
$activityController= new C2\ActivityController($settings);

//route
switch($_GET['route'])
{
    case 'test':
    {
        $userController->test();
    }
    break;

    case 'signUp':
    {
        $userController->signUp();
    }
    break;

    case 'activateUserAccount':
    {
        $userController->activateUserAccount();
    }
    break;
    case 'resendActivationCode':
    {
        $userController->resendActivationCode();
    }
    break;
    case 'generatePasswordResetCode':
    {
        $userController->generatePasswordResetCode();
    }
    break;

    case 'signIn':
    {
        $userController->signIn();
    }
    break;

    case 'updateUserProfile':
    {
        $userController->updateUserProfile();
    }
    break;

    case 'updatePassword':
    {
        $userController->updatePassword();
    }
    break;

    case 'getSignedInUserDetails':
    {
        $userController->getSignedInUserDetails();
    }
    break;

    case 'searchUserDetailsBySearchText':
    {
        $userController->searchUserDetailsBySearchText();
    }
    break;

    case 'logout':
    {
        $userController->logout();
    }
    break;

    // projects
    case 'userProjects':
    {
        $projectController->userProjects();
    }
    break;

    case 'userProjectCrud':
    {
        $projectController->userProjectCrud();
    }
    break;

    case 'projectMembers':
    {
        $projectController->projectMembers();
    }
    break;

    case 'projectMemberCrud':
    {
        $projectController->projectMemberCrud();
    }
    break;

    case 'projectDetails':
    {
        $projectController->projectDetails();
    }
    break;

    // case 'userProjectLogBook':
    // {
    //     $projectController->userProjectLogBook();
    // }
    // break;

    //sprint
    case 'projectSprintAll':
    {
        $sprintController->projectSprintAll();
    }
    break;

    case 'sprintCrud':
    {
        $sprintController->sprintCrud();
    }
    break;

    //goal
    case 'projectGoalAll':
    {
        $goalController->projectGoalAll();
    }
    break;

    case 'goalCrud':
    {
        $goalController->goalCrud();
    }
    break;

    //activity
    case 'projectActivity':
    {
        $activityController->goalActivityAll();
    }
    break;

    case 'activityCrud':
    {
        $activityController->activityCrud();
    }
    break;


    default:
        //
}
