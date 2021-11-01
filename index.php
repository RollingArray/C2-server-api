<?php

// add CORS
if (isset($_SERVER['HTTP_ORIGIN'])) {
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

	exit(0);
}

require_once __DIR__ . '/vendor/autoload.php';

//settings
require_once __DIR__ . '/settings.php';

//lib
require_once __DIR__ . '/app/lib/BaseAPI.class.php';
require_once __DIR__ . '/app/lib/BaseDatabaseAPI.class.php';
require_once __DIR__ . '/app/lib/DBAccessLib.class.php';
require_once __DIR__ . '/app/lib/UtilityLib.class.php';
require_once __DIR__ . '/app/lib/ValidationLib.class.php';
require_once __DIR__ . '/app/lib/MessageLib.class.php';
require_once __DIR__ . '/app/lib/SessionLib.class.php';
require_once __DIR__ . '/app/lib/EmailLib.class.php';
require_once __DIR__ . '/app/lib/JWTLib.class.php';

//controllers
require_once __DIR__ . '/app/controllers/UserController.php';
require_once __DIR__ . '/app/controllers/ProjectController.php';
require_once __DIR__ . '/app/controllers/SprintController.php';
require_once __DIR__ . '/app/controllers/GoalController.php';
require_once __DIR__ . '/app/controllers/ActivityController.php';
require_once __DIR__ . '/app/controllers/EmailController.php';

//php error reporting, 1 to enable, 0 to disable
error_reporting(~0);
ini_set('display_errors', $settings['api']['displayErrorDetails']);
//error_reporting(~0); ini_set('display_errors', 1);

//instances
$userController = new C2\UserController($settings);
$projectController = new C2\ProjectController($settings);
$sprintController = new C2\SprintController($settings);
$goalController = new C2\GoalController($settings);
$activityController = new C2\ActivityController($settings);
$emailController = new C2\EmailController($settings);

//route
$requestUri = $_SERVER['REQUEST_URI'];
$versionPrefix = $settings['api']['versionPrefix'];
$apiVersionV1 = $versionPrefix.'/v1/';
$apiEndpoint = substr($requestUri, strlen($apiVersionV1));
// $str = "/C2/api/v1/user/test/";
// $test = 'user\/test';
// $pattern = '/\b'.$test.'\b/';
// echo $pattern;
// echo preg_match($pattern, $str); // Outputs 1


switch (true) {
	case strpos($requestUri, 'user/test/'): {
			$userController->test();
		}
		break;

	case strpos($requestUri, 'user/sign/up/'): {
			$userController->signUp();
		}
		break;

	case strpos($requestUri, 'user/activate/code/resend/'): {
			$userController->resendActivationCode();
		}
		break;

	case strpos($requestUri, 'user/sign/in/'): {
			$userController->signIn();
		}
		break;

	case strpos($requestUri, 'user/profile/update/'): {
			$userController->updateUserProfile();
		}
		break;

	case strpos($requestUri, 'user/details/'): {
			$userController->getSignedInUserDetails();
		}
		break;

	case strpos($requestUri, 'user/search/'): {
			$userController->searchUserDetailsBySearchText();
		}
		break;

	case strpos($requestUri, 'user/logout/'): {
			$userController->logout();
		}
		break;

		// projects
	case strpos($requestUri, 'user/projects/'): {
			$projectController->userProjects();
		}
		break;

	case strpos($requestUri, 'user/project/crud/'): {
			$projectController->userProjectCrud();
		}
		break;

	case strpos($requestUri, 'project/members/'): {
			$projectController->projectMembers();
		}
		break;

	case strpos($requestUri, 'project/member/crud/'): {
			$projectController->projectMemberCrud();
		}
		break;

	case strpos($requestUri, 'project/details/'): {
			$projectController->projectDetails();
		}
		break;
	case strpos($requestUri, 'project/raw/'): {
			$projectController->projectRaw();
		}
		break;
	case strpos($requestUri, 'project/credibility/index/'): {
			$projectController->projectAssigneeCredibilityIndex();
		}
		break;
	case strpos($requestUri, 'assignee/credibility/index/details/'): {
			$projectController->projectAssigneeCredibilityIndexDetails();
		}
		break;

		//sprint
	case strpos($requestUri, 'project/sprints/'): {
			$sprintController->projectSprintAll();
		}
		break;

	case strpos($requestUri, 'project/sprint/crud/'): {
			$sprintController->sprintCrud();
		}
		break;

		//goal
	case strpos($requestUri, 'project/goals/'): {
			$goalController->projectGoalAll();
		}
		break;

	case strpos($requestUri, 'project/goal/crud/'): {
			$goalController->goalCrud();
		}
		break;

		//activity
	case strpos($requestUri, 'project/my/activities/'): {
			$activityController->allMyActivities();
		}
		break;

	case strpos($requestUri, 'project/my/reviews/'): {
			$activityController->allMyReviews();
		}
		break;

	case strpos($requestUri, 'goal/activities/'): {
			$activityController->goalActivityAll();
		}
		break;

	case strpos($requestUri, 'goal/activity/crud/'): {
			$activityController->activityCrud();
		}
		break;

		//comment
	case strpos($requestUri, 'activity/comment/crud/'): {
			$activityController->activityCommentCrud();
		}
		break;

		//review
	case strpos($requestUri, 'activity/reviewer/crud/'): {
			$activityController->activityReviewerCrud();
		}
		break;

	case strpos($requestUri, 'activity/details/'): {
			$activityController->activityReviewDetails();
		}
		break;

	case strpos($requestUri, 'review/update/'): {
			$activityController->activityReviewUpdate();
		}
		break;

	case strpos($requestUri, 'email/track/update/'): {
			$emailController->emailTrackUpdate();
		}
		break;

	default:
}
