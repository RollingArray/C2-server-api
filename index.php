<?php

/**
 * © Rolling Array https://rollingarray.co.in/
 *
 * long description for the file
 *
 * @summary Bootstrap api route
 * @author code@rollingarray.co.in
 *
 * Created at     : 2021-04-21 10:04:44 
 * Last modified  : 2021-11-03 19:49:46
 */

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

//settings
require_once __DIR__ . '/settings.php';

//controllers
require_once __DIR__ . '/app/controllers/UserController.php';
require_once __DIR__ . '/app/controllers/ProjectController.php';
require_once __DIR__ . '/app/controllers/SprintController.php';
require_once __DIR__ . '/app/controllers/GoalController.php';
require_once __DIR__ . '/app/controllers/ActivityController.php';
require_once __DIR__ . '/app/controllers/EmailController.php';

//php error reporting, 1 to enable, 0 to disable
error_reporting(~0);
ini_set('display_errors', 0);

//instances
$userController = new C2\UserController($settings);
$projectController = new C2\ProjectController($settings);
$sprintController = new C2\SprintController($settings);
$goalController = new C2\GoalController($settings);
$activityController = new C2\ActivityController($settings);
$emailController = new C2\EmailController($settings);

//route
$requestUri = $_SERVER['REQUEST_URI'];
$apiVersion = 'v1/';
			
// check each route ans pass on to the controller
switch (true) {
	case strpos($requestUri, $apiVersion.'user/test/'): {
			$userController->test();
		}
		break;

	case strpos($requestUri, $apiVersion.'user/sign/up/'): {
			$userController->signUp();
		}
		break;

	case strpos($requestUri, $apiVersion.'user/activate/code/resend/'): {
			$userController->resendActivationCode();
		}
		break;

	case strpos($requestUri, $apiVersion.'user/sign/in/'): {
			$userController->signIn();
		}
		break;

	case strpos($requestUri, $apiVersion.'user/profile/update/'): {
			$userController->updateUserProfile();
		}
		break;

	case strpos($requestUri, $apiVersion.'user/details/'): {
			$userController->getSignedInUserDetails();
		}
		break;

	case strpos($requestUri, $apiVersion.'user/search/'): {
			$userController->searchUserDetailsBySearchText();
		}
		break;

	case strpos($requestUri, $apiVersion.'user/logout/'): {
			$userController->logout();
		}
		break;

		// projects
	case strpos($requestUri, $apiVersion.'user/projects/'): {
			$projectController->userProjects();
		}
		break;

	case strpos($requestUri, $apiVersion.'user/project/crud/'): {
			$projectController->userProjectCrud();
		}
		break;

	case strpos($requestUri, $apiVersion.'project/members/'): {
			$projectController->projectMembers();
		}
		break;

	case strpos($requestUri, $apiVersion.'project/member/crud/'): {
			$projectController->projectMemberCrud();
		}
		break;
	
	case strpos($requestUri, $apiVersion.'project/new/member/invite/'): {
			$projectController->projectNewMemberInvite();
		}
		break;

	case strpos($requestUri, $apiVersion.'project/details/'): {
			$projectController->projectDetails();
		}
		break;
	case strpos($requestUri, $apiVersion.'project/raw/'): {
			$projectController->projectRaw();
		}
		break;
	case strpos($requestUri, $apiVersion.'project/credibility/index/'): {
			$projectController->projectAssigneeCredibilityIndex();
		}
		break;
	case strpos($requestUri, $apiVersion.'assignee/credibility/index/details/'): {
			$projectController->projectAssigneeCredibilityIndexDetails();
		}
		break;

		//sprint
	case strpos($requestUri, $apiVersion.'project/sprints/'): {
			$sprintController->projectSprintAll();
		}
		break;

	case strpos($requestUri, $apiVersion.'project/sprint/crud/'): {
			$sprintController->sprintCrud();
		}
		break;

		//goal
	case strpos($requestUri, $apiVersion.'project/goals/'): {
			$goalController->projectGoalAll();
		}
		break;

	case strpos($requestUri, $apiVersion.'project/goal/crud/'): {
			$goalController->goalCrud();
		}
		break;

		//activity
	case strpos($requestUri, $apiVersion.'project/my/activities/'): {
			$activityController->allMyActivities();
		}
		break;

	case strpos($requestUri, $apiVersion.'project/my/reviews/'): {
			$activityController->allMyReviews();
		}
		break;

	case strpos($requestUri, $apiVersion.'goal/activities/'): {
			$activityController->goalActivityAll();
		}
		break;

	case strpos($requestUri, $apiVersion.'goal/activity/crud/'): {
			$activityController->activityCrud();
		}
		break;
	
	case strpos($requestUri, $apiVersion.'activity/lock/'): {
			$activityController->lockActivity();
		}
		break;

	case strpos($requestUri, $apiVersion.'activity/unlock/'): {
			$activityController->unlockActivity();
		}
		break;

		//comment
	case strpos($requestUri, $apiVersion.'activity/comment/crud/'): {
			$activityController->activityCommentCrud();
		}
		break;

		//review
	case strpos($requestUri, $apiVersion.'activity/reviewer/crud/'): {
			$activityController->activityReviewerCrud();
		}
		break;

	case strpos($requestUri, $apiVersion.'activity/details/'): {
			$activityController->activityReviewDetails();
		}
		break;

	case strpos($requestUri, $apiVersion.'review/update/'): {
			$activityController->activityReviewUpdate();
		}
		break;
	
	case strpos($requestUri, $apiVersion.'review/lock/'): {
			$activityController->lockActivityReview();
		}
		break;

	case strpos($requestUri, $apiVersion.'review/unlock/'): {
			$activityController->unlockActivityReview();
		}
		break;

	case strpos($requestUri, $apiVersion.'email/track/update/'): {
			$emailController->emailTrackUpdate();
		}
		break;

	default:
}
