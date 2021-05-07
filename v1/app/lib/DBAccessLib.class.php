<?php

namespace C2\Database;

class DBAccessLib extends BaseDatabaseAPI
{
    function __construct($settings)
    {
        parent::__construct($settings);
    }

    function __destruct()
    {
        parent::__destruct();
    }

    //email
    //insertNewEmailTrack
    public function insertNewEmailTrack($passedData)
    {
        $query = "CALL sp_insert_new_email_track(?,?,?,?)";

        $data = array(

            $passedData['email_track_id'],
            $passedData['user_email'],
            $passedData['email_subject'],
            $passedData['email_content']
        );

        return parent::executeStatement($query, $data);
    }

    //updateEmailTrack
    public function updateEmailTrack($passedData)
    {
        $query = "CALL sp_update_email_track(?)";

        $data = array(

            $passedData['email_track_id']
        );

        return parent::executeStatement($query, $data);
    }

    //ifExistingUser
    public function ifExistingUser($passedData)
    {
        $query = "CALL sp_if_existing_user(?)";

        $data = array($passedData['user_email']);

        return parent::ifRecordExist($query, $data); // 1 line selection, return 1 line
    }

    //insertNewUser
    public function insertNewUser($passedData)
    {
        $query = "CALL sp_insert_new_user(?,?,?,?,?,?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['user_first_name'],
            $passedData['user_last_name'],
            $passedData['user_password'],
            $passedData['user_email'],
            $passedData['user_status'],
            $passedData['user_security_answer_1'],
            $passedData['user_security_answer_2'],
            $passedData['user_verification_code']
        );

        return parent::executeStatement($query, $data);
    }

    //ifVerificationCodeValid
    public function ifVerificationCodeValid($passedData)
    {
        $query = "CALL sp_if_verification_code_valid(?,?)";

        $data = array(
            $passedData['user_email'],
            $passedData['user_verification_code']
        );

        return parent::getOneRecord($query, $data);
    }

    //getIfUserInactive
    public function getIfUserInactive($passedData)
    {
        $query = "CALL sp_get_if_user_inactive(?)";

        $data = array($passedData['user_email']);

        return parent::getOneRecord($query, $data);
    }

    //activateUserAccount
    public function activateUserAccount($passedData)
    {
        $query = "CALL sp_activate_user_account(?)";

        $data = array($passedData['user_email']);

        return parent::executeStatement($query, $data);
    }

    //getAllActiveUsers
    public function getAllActiveUsers()
    {
        $query = "CALL sp_get_all_active_users()";

        $data = array();

        return parent::getAllRecords($query, $data);
    }

    //getAllInactiveUsers
    public function getAllInactiveUsers()
    {
        $query = "CALL sp_get_all_inactive_users()";

        $data = array();

        return parent::getAllRecords($query, $data);
    }

    //regenerateUserAccountActivationCode
    public function regenerateUserAccountActivationCode($passedData)
    {
        $query = "CALL sp_regenerate_user_account_activation_code(?,?)";

        $data = array(
            $passedData['user_verification_code'],
            $passedData['user_email']
        );
        return parent::executeStatement($query, $data);
    }

    //generateUserPasswordResetCode
    public function generateUserPasswordResetCode($passedData)
    {
        $query = "CALL sp_generate_user_password_reset_Code(?,?)";

        $data = array(
            $passedData['user_password_reset_code'],
            $passedData['user_email']
        );

        return parent::executeStatement($query, $data);
    }

    //ifPasswordResetCodeExist
    public function ifPasswordResetCodeExist($passedData)
    {
        $query = "CALL sp_if_password_reset_code_exist(?,?)";

        $data = array(
            $passedData['user_email'],
            $passedData['user_password_reset_code']
        );

        return parent::ifRecordExist($query, $data);
    }

    //updatePassword
    public function updatePassword($passedData)
    {
        $query = "CALL sp_update_password(?,?)";

        $data = array(
            $passedData['user_password'],
            $passedData['user_email']
        );

        return parent::executeStatement($query, $data);
    }

    //ifUserIdentified
    function ifUserIdentified($passedData)
    {
        $query = "CALL sp_if_user_identified(?,?,?)";

        $data = array(
            $passedData['user_email'],
            $passedData['user_security_answer_1'],
            $passedData['user_security_answer_2']
        );

        return parent::ifRecordExist($query, $data);
    }

    //getSignInUser
    function getSignInUser($passedData)
    {
        $query = "CALL sp_get_sign_in_user(?)";

        $data = array(
            $passedData['user_email']
        );

        return parent::getOneRecord($query, $data);
    }

    //getUserDetailsById
    function getUserDetailsById($passedData)
    {
        $query = "CALL sp_get_user_details_by_id(?)";

        $data = array(
            $passedData['user_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //getUserBasicDetails
    function getUserBasicDetails($passedData)
    {
        $query = "CALL sp_get_user_basic_details(?)";

        $data = array(
            $passedData['user_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //getUserDetailsByEmail
    function getUserDetailsByEmail($passedData)
    {
        $query = "CALL sp_get_user_details_by_email(?)";

        $data = array(
            $passedData['user_email']
        );
        return parent::getOneRecord($query, $data);
    }

    //getUserDetailsBySearchText
    function getUserDetailsBySearchText($passedData)
    {
        $query = "CALL sp_get_user_details_by_search_text(?, ?)";

        $data = array(
            $passedData['user_id'],
            $passedData['search_key']
        );
        return parent::getAllRecords($query, $data);
    }

    //updatedUserProfile
    function updatedUserProfile($passedData)
    {
        $query = "CALL sp_updated_user_profile(?,?,?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['user_first_name'],
            $passedData['user_last_name'],
            $passedData['user_email'],
            $passedData['user_security_answer_1'],
            $passedData['user_security_answer_2'],
        );
        //var_dump(parent::executeStatement($query, $data));
        return parent::executeStatement($query, $data);
    }

    //getAllUsers
    public function getAllUsers($passedData)
    {
        $query = "CALL sp_get_all_users(?)";

        $data = array($passedData['community_id']);

        return parent::getAllRecords($query, $data);
    }

    //getAllAccessPrivilegeDetails
    public function getAllAccessPrivilegeDetails($passedData)
    {
        $query = "CALL sp_get_all_access_privilege_details()";

        $data = array();

        return parent::getAllRecords($query, $data);
    }

    //getUserNameByUserId
    public function getUserNameByUserId($passedData)
    {
        $query = "CALL sp_get_user_name_by_User_id(?)";

        $data = array(
            $passedData['operation_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //logUserSession
    public function logUserSession($passedData)
    {
        $query = "CALL sp_insert_new_session(?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['user_platform'],
            $passedData['user_ip'],
            $passedData['user_login_type']
        );

        return parent::executeStatement($query, $data);
    }

    //checkIfUserCanCRUDInDB
    function checkIfUserCanCRUD($passedData)
    {
        $query = "CALL sp_check_if_user_can_crud(?, ?)";

        $data = array(
            $passedData['project_id'],
            $passedData['user_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //getProjectName
    public function getProjectName($passedData)
    {
        $query = "CALL sp_get_project_name_by_project_id(?)";

        $data = array(
            $passedData['project_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //ifProjectAlreadyExist
    function ifProjectAlreadyExist($passedData)
    {
        $query = "CALL sp_if_project_already_exist(?)";

        $data = array(
            $passedData['project_name']
        );

        return parent::ifRecordExist($query, $data);
    }

    //insertNewProject
    function insertNewProject($passedData)
    {
        $query = "CALL sp_insert_new_project(?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_name'],
            $passedData['project_id'],
            $passedData['project_description']
        );

        return parent::executeStatement($query, $data);
    }

    //updateProject
    function updateProject($passedData)
    {
        $query = "CALL sp_update_project(?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_name'],
            $passedData['project_description'],
            $passedData['project_id']
        );

        return parent::executeStatement($query, $data);
    }

    //deleteProject
    function deleteProject($passedData)
    {
        $query = "CALL sp_delete_project(?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['project_name'],
        );

        return parent::executeStatement($query, $data);
    }

    //getAllProjectsForUser
    public function getAllProjectsForUser($passedData)
    {
        $query = "CALL sp_get_all_projects_for_user(?)";

        $data = array($passedData['user_id']);

        return parent::getAllRecords($query, $data);
    }

    //getBasicProjectDetails
    public function getBasicProjectDetails($passedData)
    {
        $query = "CALL sp_get_basic_project_details(?)";

        $data = array($passedData['project_id']);

        return parent::getOneRecord($query, $data);
    }

    //project member
    //getUserTypeForUserTypeId
    public function getUserTypeForUserTypeId($passedData)
    {
        $query = "CALL sp_get_user_type_for_user_type_id(?)";

        $data = array(
            $passedData['user_type_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //getUserTypeForUserAndProjectId
    public function getUserTypeForUserAndProjectId($passedData)
    {
        $query = "CALL sp_get_user_type_for_user_and_project_id(?, ?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //getMemberCountForProject
    public function getMemberCountForProject($passedData)
    {
        $query = "CALL sp_get_member_count_for_project(?)";

        $data = array(
            $passedData['project_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //ifProjectAlreadyCreatedBySameUser
    function ifMemberAlreadySameProject($passedData)
    {

        $query = "CALL sp_if_member_already_same_project(?, ?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id']
        );

        return parent::ifRecordExist($query, $data);
    }

    //ifAddedMemberAlreadySameInProject
    function ifAddedMemberAlreadySameInProject($passedData)
    {

        $query = "CALL sp_if_member_already_same_project(?, ?)";

        $data = array(
            $passedData['added_user_id'],
            $passedData['project_id']
        );

        return parent::ifRecordExist($query, $data);
    }

    //ifProjectAccessToMember
    function ifProjectAccessToMember($passedData)
    {
        $query = "CALL sp_get_project_member_association(?, ?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id']
        );

        return parent::ifRecordExist($query, $data);
    }

    //addNewMemberToProject
    function addNewMemberToProject($passedData)
    {
        $query = "CALL sp_attach_project_to_member(?, ?, ?, ?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['added_user_id'],
            $passedData['project_user_type_id'],
        );

        return parent::executeStatement($query, $data);
    }

    //updateProjectMemberAccess
    function updateProjectMemberAccess($passedData)
    {
        $query = "CALL sp_update_project_member_access(?, ?, ?, ?)";

        $data = array(
            $passedData['user_id'],
            $passedData['added_user_id'],
            $passedData['project_id'],
            $passedData['project_user_type_id'],
        );

        return parent::executeStatement($query, $data);
    }

    //deleteProjectMember
    function deleteProjectMember($passedData)
    {
        $query = "CALL sp_delete_project_member(?, ?, ?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['added_user_id']
        );

        return parent::executeStatement($query, $data);
    }

    //getAllProjectUsersByType
    public function getAllProjectUsersByType($passedData, $memberType)
    {
        $query = "CALL sp_get_all_project_users_by_type(?, ?)";

        $data = array(
            $passedData['project_id'],
            $memberType
        );

        return parent::getAllRecords($query, $data);
    }

    //getAllProjectUsers
    public function getAllProjectUsers($passedData)
    {
        $query = "CALL sp_get_all_project_users(?)";

        $data = array(
            $passedData['project_id']
        );


        return parent::getAllRecords($query, $data);
    }

    // sprint
    //updateSprint
    function updateSprint($passedData)
    {

        $query = "CALL sp_update_sprint(?, ?, ?, ?, ?, ?, ?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['sprint_id'],
            $passedData['sprint_name'],
            $passedData['sprint_start_date'],
            $passedData['sprint_end_date'],
            $passedData['sprint_status'],
        );

        return parent::executeStatement($query, $data);
    }

    //deleteSprint
    function deleteSprint($passedData)
    {

        $query = "CALL sp_delete_sprint(?, ?, ?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['sprint_id'],
        );

        return parent::executeStatement($query, $data);
    }

    //ifSprintAlreadyCreatedForSameProject
    public function ifSprintAlreadyCreatedForSameProject($passedData)
    {
        $query = "CALL sp_if_sprint_already_created_for_same_project(?, ?)";

        $data = array(
            $passedData['sprint_name'],
            $passedData['project_id']
        );

        return parent::ifRecordExist($query, $data);
    }

    //getSprintCountForProject
    public function getSprintCountForProject($passedData)
    {
        $query = "CALL sp_get_sprint_count_for_project(?)";

        $data = array(
            $passedData['project_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //insertNewSprint
    function insertNewSprint($passedData)
    {

        $query = "CALL sp_insert_new_sprint(?,?,?,?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['sprint_id'],
            $passedData['sprint_name'],
            $passedData['sprint_start_date'],
            $passedData['sprint_end_date'],
            $passedData['sprint_status']
        );

        return parent::executeStatement($query, $data);
    }

    //getNoSprintIdForProject
    function getNoSprintIdForProject($passedData)
    {
        $query = "CALL sp_get_no_sprint_id_for_project(?)";

        $data = array(
            $passedData['project_id']
        );

        //echo $query;
        return parent::getOneRecord($query, $data);
    }

    //getAllSprintsForProject
    function getAllSprintsForProject($passedData)
    {
        $query = "CALL sp_get_all_sprints_for_project(?)";

        $data = array(
            $passedData['project_id']
        );

        return parent::getAllRecords($query, $data);
    }

    //ifActivityPresentForSprint
    public function ifActivityPresentForSprint($passedData)
    {
        $query = "CALL sp_if_activity_present_for_sprint(?)";

        $data = array(
            $passedData['sprint_id']
        );

        return parent::ifRecordExist($query, $data);
    }

    //goal
    //getAllGoalsForProject
    function getAllGoalsForProject($passedData)
    {
        $query = "CALL sp_get_all_goals(?)";

        $data = array(
            $passedData['project_id']
        );

        return parent::getAllRecords($query, $data);
    }

    //ifGoalsAlreadyCreatedForSameProject
    public function ifGoalAlreadyCreatedForSameProject($passedData)
    {
        $query = "CALL sp_if_goal_already_created_for_same_project(?, ?)";

        $data = array(
            $passedData['goal_name'],
            $passedData['project_id']
        );

        return parent::ifRecordExist($query, $data);
    }

    //updateGoals
    function updateGoal($passedData)
    {

        $query = "CALL sp_update_goal(?, ?, ?, ?, ?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['goal_id'],
            $passedData['goal_name'],
            $passedData['goal_description']
        );

        return parent::executeStatement($query, $data);
    }

    //deleteGoals
    function deleteGoal($passedData)
    {

        $query = "CALL sp_delete_goal(?, ?, ?, ?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['goal_id'],
        );

        return parent::executeStatement($query, $data);
    }

    //insertNewGoals
    function insertNewGoal($passedData)
    {

        $query = "CALL sp_insert_new_goal(?,?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['goal_id'],
            $passedData['goal_name'],
            $passedData['goal_description']
        );

        return parent::executeStatement($query, $data);
    }

    //ifActivityPresentForGoal
    public function ifActivityPresentForGoal($passedData)
    {
        $query = "CALL sp_if_activity_present_for_goal(?)";

        $data = array(
            $passedData['goal_id']
        );

        return parent::ifRecordExist($query, $data);
    }

    //activity

    //ifActivityAlreadyCreatedForProject
    public function ifActivityAlreadyCreatedForProject($passedData)
    {
        $query = "CALL sp_if_activity_already_created_for_project(?, ?, ?, ?)";

        $data = array(
            $passedData['activity_name'],
            $passedData['project_id'],
            $passedData['sprint_id'],
            $passedData['assignee_user_id']
        );

        return parent::ifRecordExist($query, $data);
    }

    //getUserActivityWeighForSprint
    public function getUserActivityWeighForSprint($passedData)
    {
        $query = "CALL sp_get_user_activity_weight_for_sprint(?, ?, ?)";

        $data = array(
            $passedData['project_id'],
            $passedData['sprint_id'],
            $passedData['assignee_user_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //insertActivity
    function insertActivity($passedData)
    {
        $query = "CALL sp_insert_activity(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['activity_id'],
            $passedData['project_id'],
            $passedData['sprint_id'],
            $passedData['goal_id'],
            $passedData['assignee_user_id'],
            $passedData['activity_name'],
            $passedData['activity_weight'],
            $passedData['activity_measurement_type'],
            $passedData['activity_result_type'],
            $passedData['criteria_poor_value'],
            $passedData['criteria_improvement_value'],
            $passedData['criteria_expectation_value'],
            $passedData['criteria_exceed_value'],
            $passedData['criteria_outstanding_value'],
            $passedData['characteristics_higher_better'],
        );
        return parent::executeStatement($query, $data);
    }

    //updateActivity
    function updateActivity($passedData)
    {

        $query = "CALL sp_update_activity(?,?,?,?,?,?,?,?,?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['activity_id'],
            $passedData['activity_name'],
            $passedData['activity_weight'],
            $passedData['activity_measurement_type'],
            $passedData['activity_result_type'],
            $passedData['criteria_poor_value'],
            $passedData['criteria_improvement_value'],
            $passedData['criteria_expectation_value'],
            $passedData['criteria_exceed_value'],
            $passedData['criteria_outstanding_value'],
            $passedData['characteristics_higher_better'],
        );

        return parent::executeStatement($query, $data);
    }

    //deleteActivity
    function deleteActivity($passedData)
    {

        $query = "CALL sp_delete_activity(?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['activity_id'],
            $passedData['project_id']
        );

        return parent::executeStatement($query, $data);
    }

    //ifActivityAlreadyLocked
    public function ifActivityAlreadyLocked($passedData)
    {
        $query = "CALL sp_if_activity_already_locked(?)";

        $data = array(
            $passedData['activity_id']
        );

        return parent::ifRecordExist($query, $data);
    }

    //lockActivity
    function lockActivity($passedData)
    {

        $query = "CALL sp_lock_activity(?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['activity_id'],
            $passedData['activity_locked']
        );

        return parent::executeStatement($query, $data);
    }

    //getAllActivitiesForProject
    function getAllActivitiesForProject($passedData)
    {
        $query = "CALL sp_get_all_activities_for_project(?, ?, ?, ?)";

        $data = array(
            $passedData['project_id'],
            $passedData['sprint_id'],
            $passedData['goal_id'],
            $passedData['assignee_user_id'],
        );

        return parent::getAllRecords($query, $data);
    }

    //comment
    //insertActivityComment
    function insertActivityComment($passedData)
    {

        $query = "CALL sp_insert_comment(?,?,?,?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['activity_id'],
            $passedData['comment_id'],
            $passedData['assignee_user_id'],
            $passedData['comment_description'],
            $passedData['claimed_result_value']
        );

        return parent::executeStatement($query, $data);
    }

    //updateActivityComment
    function updateActivityComment($passedData)
    {

        $query = "CALL sp_update_comment(?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['comment_id'],
            $passedData['comment_description'],
            $passedData['claimed_result_value']
        );

        return parent::executeStatement($query, $data);
    }

    //deleteActivityComment
    function deleteActivityComment($passedData)
    {

        $query = "CALL sp_delete_comment(?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['comment_id'],
            
        );

        return parent::executeStatement($query, $data);
    }

    //reviewer

    //getActivityDetails
    public function getActivityDetails($passedData)
    {
        $query = "CALL sp_get_activity_details(?)";

        $data = array(
            $passedData['activity_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //getReviewDetails
    public function getReviewDetails($passedData)
    {
        $query = "CALL sp_get_activity_review_details(?)";

        $data = array(
            $passedData['activity_id']
        );

        return parent::getAllRecords($query, $data);
    }

    //ifAddedReviewerAlreadySameInActivity
    public function ifAddedReviewerAlreadySameInActivity($passedData)
    {
        $query = "CALL sp_if_added_reviewer_already_same_in_activity(?, ?)";

        $data = array(
            $passedData['activity_id'],
            $passedData['reviewer_user_id']
        );

        return parent::ifRecordExist($query, $data);
    }

    //ifReviewerAlreadyReviewedActivity
    public function ifReviewerAlreadyReviewedActivity($passedData)
    {
        $query = "CALL sp_if_reviewer_already_reviewed_activity(?, ?)";

        $data = array(
            $passedData['activity_id'],
            $passedData['reviewer_user_id']
        );

        return parent::getOneRecord($query, $data);
    }

    //insertActivityReviewer
    function insertActivityReviewer($passedData)
    {

        $query = "CALL sp_insert_activity_reviewer(?,?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['activity_id'],
            $passedData['activity_review_id'],
            $passedData['reviewer_user_id']
        );
        
        return parent::executeStatement($query, $data);
    }

    //updateActivityReviewer
    function updateActivityReviewer($passedData)
    {

        $query = "CALL sp_update_activity_review(?,?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['activity_review_id'],
            $passedData['achieved_result_value'],
            $passedData['reviewer_comment']
        );
        
        return parent::executeStatement($query, $data);
    }

    //deleteActivityReviewer
    function deleteActivityReviewer($passedData)
    {

        $query = "CALL sp_delete_activity_reviewer(?,?,?)";

        $data = array(
            $passedData['user_id'],
            $passedData['project_id'],
            $passedData['activity_review_id']
        );

        return parent::executeStatement($query, $data);
    }


}
