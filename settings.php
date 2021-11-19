<?php

namespace C2;

/**
 * © Rolling Array https://rollingarray.co.in/
 *
 * long description for the file
 *
 * @summary short description for the file
 * @author code@rollingarray.co.in
 *
 * Created at     : 2021-04-21 10:04:44 
 * Last modified  : 2021-11-03 21:09:24
 */

require_once __DIR__ . '/app/lib/DotEnvLib.class.php';

//dot env instances, load environment variables
$dotEnvLib = new DotEnv\DotEnvLib(__DIR__.'/.env');
$dotEnvLib->load();

$settings = [

	// environment specific configurations
	'api'=> [
		'host' => getenv('API_HOST')
	],
	'hashKey' => [
		'salt' => getenv('HASH_KEY_SALT'),
		'method' => getenv('HASH_KEY_METHOD'),
		'algo' => getenv('HASH_KEY_ALGO')
	],

	'jwt' => [
		'clientId' => getenv('JWT_CLIENT_ID'),
		'serverId' => getenv('JWT_SERVER_ID'),
		'expireInSeconds' => getenv('JWT_EXPIRE_IN_SECONDS'),
	],

	'db' => [
		'host' => getenv('DB_HOST'),
		'username' => getenv('DB_USERNAME'),
		'password' => getenv('DB_PASSWORD'),
		'database' => getenv('DATABASE'),
		'port' => getenv('DB_PORT'),
	],

	'email' => [
		'smtpHostIp' => getenv('SMTP_HOST_IP'),
		'port' => getenv('SMTP_PORT'),
		'smtpUsername' => getenv('SMTP_USERNAME'),
		'smtpPassword' => getenv('SMTP_PASSWORD'),
		'supportEmail' => getenv('SMTP_SUPPORT'),
		'prettyEmailName' => 'C2 noreply',
		'appName' => 'C2',
		'appTagLine' => 'Bring Equality In Diverse Workforce',
		'emailTrack' => 'C2/api/v1/email/track/update'
	],

	// Monolog settings
	'logger' => [
		'name' => 'C2',
		'path' => 'logs/logs.log',
	],

	//validation rules
	'validationRules' => [
		//user
		'user_id' => '/^[a-zA-Z0-9]{10,50}/',
		'added_user_id' => '/^[a-zA-Z0-9]{10,50}/',
		'assignee_user_id' => '/^[a-zA-Z0-9]{10,50}/',
		'filter_user_id' => '/^[a-zA-Z0-9]{10,50}/',
		'searchable_user_id' => '/^[a-zA-Z0-9]{30,50}/',
		'user_email' => '/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/',
		'user_first_name' => '/^[a-zA-Z]{3,200}$/',
		'user_last_name' => '/^[a-zA-Z]{3,200}$/',
		'user_security_answer_1' => '/^[a-zA-Z ]{3,200}$/',
		'user_security_answer_2' => '/^[a-zA-Z ]{3,200}$/',
		'user_password' => '/^[a-zA-Z0-9 \[\]()-.,;:\/_\-\s\S]{3,200}$/',
		'user_status' => '/(INACTIVE|ACTIVE)/',
		'user_type' => '/(NATIVE|FB|ADMIN|VOL)/',
		'user_verification_code' => '/([a-zA-Z0-9]{5,30}\s*)+/',
		'user_password_reset_code' => '/([a-zA-Z0-9]{4,30}\s*)+/',
		'user_login_type' => '/(IN_APP_LOGIN|FRESH_LOGIN)/',
		'user_ip' => '/^[0-9\.]{7,15}/',
		'user_device_id' => '/[0-9A-Za-z\_:-]/',
		'user_device_status' => '/(INACTIVE|ACTIVE)/',
		'user_platform' => '/^[a-zA-Z0-9 \()-.,;:&\/_\-\n]{5,200}$/',
		'logged_in_session_id' => '/^SESSION_[a-zA-Z0-9]{10,20}/',
		'email_track_id' =>  '/^EMAILTRACK_[a-zA-Z0-9]{10,50}/',
		'search_key' => '/^[a-zA-Z0-9 \[\]()-.,;:!&@#%$^\/_\-\n]{2,400}$/',
		'project_user_type_id' => '/^PROJECTUSERTYPEID_[0-9]{4}/',
		
		//project
		'project_id' => '/^[a-zA-Z0-9]{10,50}/',
		'project_name' => '/([a-zA-Z]{3,30}\s*)+/',
		'project_description' => '/^[a-zA-Z0-9 \[\]()-.,;:!&@#%$^\/_\-\n]{5,400}$/',

		//sprint
		'sprint_id' => '/^[a-zA-Z0-9]{10,50}|None$/',
		'sprint_name' => '/^[a-zA-Z0-9 \[\]()-.,;:!&@#%$^\/_\-\n]{5,200}$/',
		'sprint_start_date' => '/^\d{4}[-]\d{2}[-]\d{2}$/',
		'sprint_end_date' => '/^\d{4}[-]\d{2}[-]\d{2}$/',
		'sprint_status' => '/(ACTIVE|CLOSED|FUTURE)/',

		//goal
		'goal_id' => '/^[a-zA-Z0-9]{10,50}/',
		'goal_name' => '/([a-zA-Z]{3,30}\s*)+/',
		'goal_description' => '/^[a-zA-Z0-9 \[\]()-.,;:!&@#%$^\/_\-\n]{5,400}$/',

		//activity
		'activity_id' => '/^[a-zA-Z0-9]{10,50}/',
		'activity_name'=> '/^[a-zA-Z0-9 \[\]()-.,;:!&@#%$^\/_\-\n]{5,400}$/',
		'activity_weight' => '/^[0-9]*$/',
		'activity_weight_delta' => '/^-?[0-9]\d*$/',
		'activity_measurement_type' => '/(NUM|BOOL)/',
		'criteria_poor_value' => '/^[0-9]*$/',
		'criteria_improvement_value' => '/^[0-9]*$/',
		'criteria_expectation_value' => '/^[0-9]*$/',
		'criteria_exceed_value' => '/^[0-9]*$/',
		'criteria_outstanding_value' => '/^[0-9]*$/',
		'characteristics_higher_better' => '/(0|1)/',
		'activity_result_type' => '/^[a-zA-Z0-9 \[\]()-.,;:!&@#%$^\/_\-\n]{1,50}$/',

		//comment
		'comment_id' => '/^[a-zA-Z0-9]{10,50}/',
		'comment_description' => '/^[a-zA-Z0-9 \[\]()-.,;:!&@#%$^\/_\-\n]{5,1000}$/',
		'claimed_result_value' => '/^[0-9]*$/',

		//review
		'activity_review_id' =>  '/^[a-zA-Z0-9]{10,50}/',
		'reviewer_user_id' => '/^[a-zA-Z0-9]{10,50}/',
		'achieved_result_value' => '/^[0-9]*$/',
		'reviewer_comment' => '/^[a-zA-Z0-9 \[\]()-.,;:!&@#%$^\/_\-\n]{5,1000}$/',
	],
	'errorValidationMessage' => [
		'user_id' => 'Invalid user id',
		'added_user_id' => 'Invalid added user id',
		'assignee_user_id' => 'Invalid assignee user id',
		'filter_user_id' => 'Invalid filter user id',
		'searchable_user_id' => 'Invalid searchable user id',
		'user_email' => 'Invalid user email',
		'user_first_name' => 'Invalid user first name',
		'user_last_name' => 'Invalid user last name ',
		'user_security_answer_1' => 'Invalid security answer 1',
		'user_security_answer_2' => 'Invalid security answer 2',
		'user_password' => 'Invalid user password',
		'user_status' => 'Invalid user status',
		'user_type' => 'Invalid user type',
		'user_verification_code' => 'Invalid verification code',
		'user_password_reset_code' => 'Invalid password reset code',
		'user_login_type' => 'Invalid login type',
		'user_ip' => 'Invalid ip',
		'user_platform' => 'Invalid platform',
		'logged_in_session_id' => 'Invalid session',
		'email_track_id' =>  'Invalid email track id',
		'search_key' => 'Invalid search key',
		'project_user_type_id' => 'Invalid user type',
		
		//project
		'project_id' => 'Invalid project id',
		'project_name' => 'Invalid project name',
		'project_description' => 'Invalid project description',
		
		//sprint
		'sprint_id' => 'Invalid sprint id',
		'sprint_name' => 'Invalid sprint name',
		'sprint_start_date' => 'Invalid sprint start date',
		'sprint_end_date' => 'Invalid sprint end date',
		'sprint_status' => 'Invalid sprint status',

		//goal
		'goal_id' => 'Invalid goal id',
		'goal_name' => 'Invalid goal name',
		'goal_description' => 'Invalid goal description',

		//activity
		'activity_id'=> 'Invalid activity id',
		'activity_name'=> 'Invalid measurement purpose',
		'activity_weight'=> 'Invalid weight value',
		'activity_weight_delta' => 'Invalid weight delta value',
		'activity_measurement_type' => 'Invalid activity type measurement type',
		'criteria_poor_value' => 'Invalid measurement criteria poor value',
		'criteria_improvement_value' => 'Invalid measurement criteria improvement value',
		'criteria_expectation_value' => 'Invalid measurement criteria expectation value',
		'criteria_exceed_value' => 'Invalid measurement criteria exceed value',
		'criteria_outstanding_value' => 'Invalid measurement criteria outstanding value',
		'characteristics_higher_better' => 'Invalid characteristics higher better value',
		'activity_result_type' => 'Invalid activity result type',

		//comment
		'comment_id' => 'Invalid comment id',
		'comment_description' => 'Invalid comment description',
		'claimed_result_value' => 'Invalid claimed result value',

		//review
		'activity_review_id' =>  'Invalid reviewer user id',
		'reviewer_user_id' => 'Invalid reviewer user id',
		'achieved_result_value' => 'Invalid achieved result',
		'reviewer_comment' => 'Invalid reviewer comment',
	],
	'errorMessage' => [
		//user
		'USER_NOT_CREATED' => 'User could not be created',
		'USER_ALREADY_EXIST' => 'User already present with same email',
		'ACCOUNT_NOT_ACTIVATED' => 'Your account could not be activated, please try again',
		'ACCOUNT_INACTIVE' => 'Your account is inactive',
		'VERIFICATION_CODE_NO_MATCH' => 'Verification code did not match',
		'NO_USER_FOUND' => 'No user found with this email',
		'NO_VERIFICATION_CODE' => 'We could not send activation code in your registered email, please try again',
		'ACCOUNT_ALREADY_ACTIVE' => 'Your account is already active, please sign in',
		'NO_PASSWORD_RESET_CODE' => 'We could not send password reset code in your registered email, please try again',
		'NO_PASSWORD_UPDATE' => 'Password could not be updated, please try again',
		'PASSWORD_RESET_CODE_NOT_FOUND' => 'Password reset code did not match, please re-check',
		'USER_VERIFICATION_FAILED' => 'Unfortunately your answers to the security question did not match, give another try',
		'INVALID_SESSION' => 'Oh No !! it seems your authorization token is no longer valid, please sign-in again',
		'NO_USER_UPDATE' => 'User details could not be updated',
		'NO_USER_DEVICE' => 'Your device could not register',
		'NO_SESSION' => 'No session user found',
		'GENERIC_ERROR' => 'Operation could not be completed, please try again',
		'ALREADY_REGISTEDED' => 'already registered',
		'ALREADY_DEVICE_REGISTERED' => 'This device is already registered to receive notifications',
		'REGISTERED' => 'registered, password is login@123',
		'NO_REGISTEDED' => 'could not register',
		'FAILED_VALIDATION' => 'Failed registration due to data validation, please try again with correct data format',
		'FAIL_LOG_OUT' => 'Could not sign out, please try again',
		'NO_ACCESS' => 'You do not have access for this operation, please contact project admin',
		
		//project
		'PROJECT_EXIST' => 'Looks like project already exist with same name, please give a new name',
		'FAIL_PROJECT_CREATE' => 'Project count not be created, please try again',
		'FAIL_PROJECT_UPDATE' => 'Project details could not update, please try again',
		'FAIL_PROJECT_DELETE' => 'Project could not delete, please try again',
		'PROJECT_MEMBER_EXIST' => 'Looks like the member you are trying to add to the project already exist',
		'NO_PROJECT_ACCESS_TO_MEMBER' => 'You do not have access to this project',
		'NO_SECTION_ACCESS_TO_MEMBER' => 'You do not have access to this',
		'FAIL_MEMBER_CREATE' => 'User could not be added, please try again',
		'FAIL_MEMBER_UPDATE_DELETE_ACCESS' => 'You cant update of delete yourself',
		'FAIL_MEMBER_UPDATE' => 'User role could not be updated, please try again',
		'PROJECT_MEMBER_TASK_EXIST' => 'We found activities for this user, user can not be deleted',
		'FAIL_MEMBER_DELETE'  => 'User could not be deleted, please try again',
		
		//sprint
		'SPRINT_EXIST' => 'Sprint already exist with same name for this project, please give a new name',
		'FAIL_SPRINT_CREATE' => 'Sprint could not be created, please try again',
		'FAIL_SPRINT_UPDATE' => 'Sprint could not be updated, please try again',
		'FAIL_SPRINT_DELETE' => 'Sprint could not be deleted, please try again',
		'ACTIVE_SPRINT_EXIST' => 'More than 1 active sprint will not be allowed',
		'FAIL_SPRINT_ACTIVE' => 'Sprint could not be activated, please try again',
		'FAIL_SPRINT_CLOSED' => 'Sprint could not be closed, please try again',

		//goal
		'GOAL_EXIST' => 'Goal already exist with same name for this project, please give a new name',
		'FAIL_GOAL_CREATE' => 'Goal could not be created, please try again',
		'FAIL_GOAL_UPDATE' => 'Goal could not be updated, please try again',
		'FAIL_GOAL_DELETE' => 'Goal could not be deleted, please try again',

		//activity
		'ACTIVITY_EXIST' => 'Activity already exist with same name, please give a new name',
		'ACTIVITY_LOCKED' => 'This activity is already locked',
		'FAIL_ACTIVITY_CREATE' => 'Activity could not be created, please try again',
		'FAIL_ACTIVITY_UPDATE' => 'Activity could not be updated, please try again',
		'FAIL_ACTIVITY_DELETE' => 'Activity could not be deleted, please try again',
		'ACTIVITY_SPRINT_ASSOCIATION' => 'There are activities associated with this sprint, sprint can not be deleted',
		'ACTIVITY_GOAL_ASSOCIATION' => 'There are activities associated with this goal, goal can not be deleted',
		'WEIGHT_EXCEED' => 'Total activity weight for an assignee in a sprint can not exceed beyond 100%',

		//goal
		'NO_SAME_USER' => 'Comments can be submitted by only assignee',
		'FAIL_COMMENT_ADD' => 'Comment could not be added, please try again',
		'FAIL_COMMENT_UPDATE' => 'Comment could not be updated, please try again',
		'FAIL_COMMENT_DELETE' => 'Comment could not be deleted, please try again',

		//review
		'REVIEW_EXIST' => 'Reviewer has reviewed activities, can not be deleted',
		'FAIL_REVIEWER_ADD' => 'Reviewer could not be added, please try again',
		'FAIL_REVIEWER_DELETE' => 'Reviewer could not be deleted, please try again',
		'REVIEWER_EXIST' => 'The reviewer is already added',
		'FAIL_REVIEW_ADD' => 'Review could not be added, please try again',
		'FAIL_REVIEW_LOCK' => 'Review could not be locked, please try again',
		'FAIL_REVIEW_UNLOCK' => 'Review could not be unlocked, please try again',
	],
	'successMessage' => [
		//user
		'SERVER_REACHABLE' => 'Server reachable',
		'ACTIVATE_ACCOUNT' => 'We have send you the activation code in your registered email, please use the code to activate your account',
		'SUCCESS_SIGNUP_WITH_DOMAIN' => 'Happy to get you registered, you are the domain controller. Please sign in',
		'SUCCESS_SIGNUP_WITHOUT_DOMAIN' => 'Happy to get you registered. Please sign in',
		'ACCOUNT_ACTIVATED' => 'Your account has been activated, please sign in',
		'VERIFICATION_CODE_REGENERATED' => 'We have send you the activation code in your registered email, please check',
		'PASSWORD_RESET_CODE_GENERATED' => 'We have send you the password rest code in your registered email, please check',
		'PASSWORD_UPDATED' => 'Password updated, please sign in',
		'USER_VERIFIED' => 'Thank you for your verification, please update your password',
		'SUCCESS_IN_APP_LOGIN' => 'Delighted to get you back, you can proceed with your context now !',
		'SUCCESS_LOGIN' => 'Welcome to C2',
		'SUCCESS_USER_UPDATE' => 'User details updated',
		
		//project
		'SUCCESS_PROJECT_CREATE' => 'Project created successfully',
		'SUCCESS_PROJECT_UPDATED' => 'Project details updated successfully',
		'SUCCESS_PROJECT_DELETE' => 'Project deleted successfully',
		'SUCCESS_MEMBER_CREATE' => 'User has been added to the project successfully',
		'SUCCESS_MEMBER_UPDATE' => 'User role has been updated successfully',
		'SUCCESS_MEMBER_DELETE' => 'User deleted successfully form the project',
		
		//sprint
		'SUCCESS_SPRINT_CREATE' => 'Sprint created successfully. You might want to you may want to attach this sprint against goal activities for a member',
		'SUCCESS_SPRINT_UPDATE' => 'Sprint updated successfully',
		'SUCCESS_SPRINT_DELETE' => 'Sprint deleted successfully',
		'SUCCESS_SPRINT_ACTIVE' => 'Sprint activated successfully',
		'SUCCESS_SPRINT_CLOSED' => 'Sprint closed successfully',

		//goal
		'SUCCESS_GOAL_CREATE' => 'Goal created successfully. You might want to attach this goal against an activity to achieve this goal for a assignee',
		'SUCCESS_GOAL_UPDATE' => 'Goal updated successfully',
		'SUCCESS_GOAL_DELETE' => 'Goal deleted successfully',

		//activity
		'SUCCESS_ACTIVITY_CREATE' => 'Activity created successfully. You might want to reviews to review this',
		'SUCCESS_ACTIVITY_UPDATE' => 'Activity updated successfully',
		'SUCCESS_ACTIVITY_DELETE' => 'Activity deleted successfully',

		//comment
		'SUCCESS_COMMENT_ADD' => 'Comment added successfully',
		'SUCCESS_COMMENT_UPDATE' => 'Comment updated successfully',
		'SUCCESS_COMMENT_DELETE' => 'Comment deleted successfully',

		//review
		'SUCCESS_REVIEWER_ADD' => 'Reviewer added to this activity successfully. We recommend to add multiple reviewer to get unbiased opinion',
		'SUCCESS_REVIEWER_DELETE' => 'Reviewer deleted from this activity successfully',
		'SUCCESS_REVIEW_ADD' => 'Review added successfully',
		'SUCCESS_REVIEW_LOCK' => 'Review locked successfully',
		'SUCCESS_REVIEW_UNLOCK' => 'Review unlocked successfully',
	],
];
?>
