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

    //getAllProjectAdministrators
    public function getAllProjectAdministrators($passedData){
        $query = "CALL sp_get_all_project_administrators(?)";

          $data = array(
                          $passedData['project_id']
                      );
                    

          return parent::getAllRecords($query, $data);
      }

      //getAllProjectMembers
      public function getAllProjectMembers($passedData){
        $query = "CALL sp_get_all_project_members(?)";

          $data = array(
                          $passedData['project_id']
                      );

          return parent::getAllRecords($query, $data);
      }

      //getAllProjectUsers
    public function getAllProjectUsers($passedData){
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

            $query = "CALL sp_delete_sprint(?, ?, ?, ?)";

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
}
