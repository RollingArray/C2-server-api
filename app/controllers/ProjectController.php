<?php

namespace C2;

class ProjectController extends BaseAPI
{
    protected $User;
    protected $DBAccessLib;
    protected $UtilityLib;
    protected $ValidationLib;
    protected $MessageLib;
    protected $SessionLib;
    protected $JWTLib;
    protected $EmailLib;

    public function __construct($settings) {
        parent::__construct($settings);
        $this->DBAccessLib = new Database\DBAccessLib($settings); // create a new object, class db()
        $this->UtilityLib = new Utility\UtilityLib($settings);
        $this->ValidationLib = new Validation\ValidationLib();
        $this->MessageLib = new Message\MessageLib($settings);
        $this->SessionLib = new Session\SessionLib($settings);
        $this->JWTLib = new JWT\JWTLib($settings);
        $this->EmailLib = new Email\EmailLib($settings);
    }

    //userProjectCrud
    public function userProjectCrud()
    {
        $responseData = null;

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = null;
        if(property_exists($postData, 'projectId'))
        {
            $project_id = parent::sanitizeInput($postData->projectId);
        }
        else
        {
            $project_id = $this->UtilityLib->generateId('');
        }

        $project_name = parent::sanitizeInput($postData->projectName);
        $project_description = parent::sanitizeInput($postData->projectDescription);
        $operation_type = parent::sanitizeInput($postData->operationType);
        $token = parent::getAuthorizationSessionObject();

        $passedData = array(
                "project_id"=>$project_id,
                "user_id"=>$user_id,
                "added_user_id"=>$user_id,
                "project_name"=>$project_name,
                "project_description"=>$project_description
            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            // is user present
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            if ($activeUser)
            {
                //create project
                if($operation_type == 'create')
                {
                    //any user creating project becomes a admin by default
                    $project_user_type_id = "PROJECTUSERTYPEID_0001"; //super admin
                    $passedData['project_user_type_id'] = $project_user_type_id;

                    //ifProjectAlreadyExist
                    $ifProjectAlreadyExist = $this->DBAccessLib->ifProjectAlreadyExist($passedData);

                    if($ifProjectAlreadyExist)
                    {
                        $responseData = $this->MessageLib->errorMessageFormat('PROJECT_EXIST', $this->settings['errorMessage']['PROJECT_EXIST']);
                    }
                    else
                    {
                        //insert new project
                        $projectCreated = $this->DBAccessLib->insertNewProject($passedData);
                        if ($projectCreated)
                        {
                            //attach project to member
                            $attachProjectToMember =  $this->DBAccessLib->addNewMemberToProject($passedData);

                            if($attachProjectToMember)
                            {

                                $message = $this->settings['successMessage']['SUCCESS_PROJECT_CREATE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message, $passedData);
                            }
                            else
                            {
                                $responseData = $this->MessageLib->errorMessageFormat('NO_PROJECT_CREATE', $this->settings['errorMessage']['NO_PROJECT_CREATE']);
                            }
                        }
                        else
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('NO_PROJECT_CREATE', $this->settings['errorMessage']['NO_PROJECT_CREATE']);
                        }
                    }
                }

                //edit project
                else if($operation_type == 'edit')
                {
                    //check If User Can do the operation
                    $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);
        
                    //check access
                    if($checkIfUserCanCRUD['crudProject'])
                    {
                        $updateProject = $this->DBAccessLib->updateProject($passedData);
                        if($updateProject)
                        {
                            $message = $this->settings['successMessage']['SUCCESS_PROJECT_UPDATED'];
                            $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                        }
                        else
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('FAIL_PROJECT_UPDATE', $this->settings['errorMessage']['FAIL_PROJECT_UPDATE']);
                        }
                    }
                    else
                    {
                        $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                    }
                }

                //delete project
                else if($operation_type == 'delete')
                {
                    //check If User Can do the operation
                    $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);
                    //echo $checkIfUserCanCRUD;

                    //check access
                    if($checkIfUserCanCRUD['crudProject'])
                    {
                        $updateProject = $this->DBAccessLib->deleteProject($passedData);
                        if($updateProject)
                        {
                            $message = $this->settings['successMessage']['SUCCESS_PROJECT_DELETE'];
                            $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                        }
                        else
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('FAIL_PROJECT_DELETE', $this->settings['errorMessage']['FAIL_PROJECT_DELETE']);
                        }
                    }
                    else
                    {
                        $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                    }
                }

            }
            else
            {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    //userProjects
    public function userProjects()
    {
        $responseData = array();

        //get post gata
        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $token = parent::getAuthorizationSessionObject();

        //
        $passedData = array(
                "user_id"=>$user_id
            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            //activeUser
            if($activeUser)
            {
                //get user details
                $getAllProjectsForUser = $this->UtilityLib->getAllProjectsForUser($this->DBAccessLib, $passedData);
                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $getAllProjectsForUser);
            }
            else
            {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    //projectMembers
    public function projectMembers()
    {
        $responseData = array();

        //get post gata
        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $token = parent::getAuthorizationSessionObject();

        //
        $passedData = array(
                "user_id"=>$user_id,
                "project_id"=>$project_id,

            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            //activeUser
            if($activeUser)
            {
                $ifProjectAccessToMember = $this->DBAccessLib->ifProjectAccessToMember($passedData);

                if($ifProjectAccessToMember)
                {
                    //get user details
                    $getAllProjectsForUser = $this->UtilityLib->getAllMembersForProject($this->DBAccessLib, $passedData);
                    $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $getAllProjectsForUser);
                }
                else
                {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_PROJECT_ACCESS_TO_MEMBER', $this->settings['errorMessage']['NO_PROJECT_ACCESS_TO_MEMBER']);
                }
                
            }
            else
            {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    //projectAssigneeCredibilityIndex
    public function projectAssigneeCredibilityIndex()
    {
        $responseData = array();

        //get post gata
        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $token = parent::getAuthorizationSessionObject();

        //
        $passedData = array(
                "user_id"=>$user_id,
                "project_id"=>$project_id,
            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            //activeUser
            if($activeUser)
            {
                $ifProjectAccessToMember = $this->DBAccessLib->ifProjectAccessToMember($passedData);

                if($ifProjectAccessToMember)
                {
                    //check If User Can do the operation
                    $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);
            
                    //check access
                    if($checkIfUserCanCRUD['viewCredibility'])
                    {
                        //get user details
                        $getAllProjectsForUser = $this->UtilityLib->getAllAssigneeCredibilityIndexForProject($this->DBAccessLib, $passedData);
                        $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $getAllProjectsForUser);
                    }
                    else
                    {
                        $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                    }
                }
                else
                {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_PROJECT_ACCESS_TO_MEMBER', $this->settings['errorMessage']['NO_PROJECT_ACCESS_TO_MEMBER']);
                }
                
            }
            else
            {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    //projectAssigneeCredibilityIndexDetails
    public function projectAssigneeCredibilityIndexDetails()
    {
        $responseData = array();

        //get post gata
        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $assignee_user_id = parent::sanitizeInput($postData->assigneeUserId);
        $token = parent::getAuthorizationSessionObject();

        //
        $passedData = array(
                "user_id"=>$user_id,
                "assignee_user_id"=>$assignee_user_id,
                "project_id"=>$project_id,
            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            //activeUser
            if($activeUser)
            {
                $ifProjectAccessToMember = $this->DBAccessLib->ifProjectAccessToMember($passedData);

                if($ifProjectAccessToMember)
                {
                    //check If User Can do the operation
                    $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);
            
                    //check access
                    if($checkIfUserCanCRUD['viewCredibility'])
                    {
                        //get user details
                        $getAllProjectsForUser = $this->UtilityLib->getAssigneeCredibilityIndexDetails($this->DBAccessLib, $passedData);
                        $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $getAllProjectsForUser);
                    }
                    else
                    {
                        $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                    }
                }
                else
                {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_PROJECT_ACCESS_TO_MEMBER', $this->settings['errorMessage']['NO_PROJECT_ACCESS_TO_MEMBER']);
                }
                
            }
            else
            {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    //projectDetails
    public function projectDetails()
    {
        $responseData = array();

        //get post gata
        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $token = parent::getAuthorizationSessionObject();
        
        //
        $passedData = array(
                "user_id"=>$user_id,
                "project_id"=>$project_id
            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            //activeUser
            if($activeUser)
            {
                $ifProjectAccessToMember = $this->DBAccessLib->ifProjectAccessToMember($passedData);

                if($ifProjectAccessToMember)
                {
                    $getProjectRawData = $this->UtilityLib->getBasicProjectDetails($this->DBAccessLib, $passedData);
                    $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $getProjectRawData);
                }
                else
                {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_PROJECT_ACCESS_TO_MEMBER', $this->settings['errorMessage']['NO_PROJECT_ACCESS_TO_MEMBER']);
                }
            }
            else
            {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    //projectRaw
    public function projectRaw()
    {
        $responseData = array();

        //get post gata
        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $rawDataKeys = parent::sanitizeInput($postData->rawDataKeys);
        $token = parent::getAuthorizationSessionObject();
        
        //
        $passedData = array(
                "user_id"=>$user_id,
                "project_id"=>$project_id
            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            //activeUser
            if($activeUser)
            {
                $ifProjectAccessToMember = $this->DBAccessLib->ifProjectAccessToMember($passedData);

                if($ifProjectAccessToMember)
                {
                    $getProjectRaw = $this->UtilityLib->getProjectRaw($this->DBAccessLib, $passedData, $rawDataKeys);
                    $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $getProjectRaw);
                }
                else
                {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_PROJECT_ACCESS_TO_MEMBER', $this->settings['errorMessage']['NO_PROJECT_ACCESS_TO_MEMBER']);
                }
            }
            else
            {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    //projectMemberCrud
    public function projectMemberCrud()
    {
        $responseData = null;

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $added_user_id = parent::sanitizeInput($postData->addedUserId);
        $project_user_type_id = parent::sanitizeInput($postData->userTypeId);
        $operation_type = parent::sanitizeInput($postData->operationType);
        $token = parent::getAuthorizationSessionObject();

        $passedData = array(
                "project_id"=>$project_id,
                "user_id"=>$user_id,
                "added_user_id"=>$added_user_id,
                "project_user_type_id"=>$project_user_type_id,
            );

        //check If User Can do the operation
        $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);
        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            // is user present
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            if ($activeUser)
            {
                if($checkIfUserCanCRUD['crudMember'])
                {
                    //add new member
                    if($operation_type == 'create')
                    {
                        
                        //ifAddedMemberAlreadySameInProject
                        $ifAddedMemberAlreadySameInProject = $this->DBAccessLib->ifAddedMemberAlreadySameInProject($passedData);

                        if($ifAddedMemberAlreadySameInProject)
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('PROJECT_MEMBER_EXIST', $this->settings['errorMessage']['PROJECT_MEMBER_EXIST']);
                        }
                        else
                        {
                            $addNewMemberToProject =  $this->DBAccessLib->addNewMemberToProject($passedData);
                            if($addNewMemberToProject)
                            {
                                $message = $this->settings['successMessage']['SUCCESS_MEMBER_CREATE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            }
                            else
                            {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_MEMBER_CREATE', $this->settings['errorMessage']['FAIL_MEMBER_CREATE']);
                            }
                        }
                    }
                    //user can not update or delete their own access
                    else
                    {
                        if($passedData['user_id'] == $passedData['added_user_id'])
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('FAIL_MEMBER_UPDATE_DELETE_ACCESS', $this->settings['errorMessage']['FAIL_MEMBER_UPDATE_DELETE_ACCESS']);
                        }
                        else
                        {
                            //edit member access rights
                            if($operation_type == 'edit')
                            {
                                $updateProjectMemberAccess = $this->DBAccessLib->updateProjectMemberAccess($passedData);
                                if($updateProjectMemberAccess)
                                {
                                    $message = $this->settings['successMessage']['SUCCESS_MEMBER_UPDATE'];
                                    $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                                }
                                else
                                {
                                    $responseData = $this->MessageLib->errorMessageFormat('FAIL_MEMBER_UPDATE', $this->settings['errorMessage']['FAIL_MEMBER_UPDATE']);
                                }
                            }
                            //delete member
                            else if($operation_type == 'delete')
                            {
                                $spIfMemberAssociatedToAnyActivityForProject = $this->DBAccessLib->spIfMemberAssociatedToAnyActivityForProject($passedData);

                                if($spIfMemberAssociatedToAnyActivityForProject)
                                {
                                    $responseData = $this->MessageLib->errorMessageFormat('PROJECT_MEMBER_TASK_EXIST', $this->settings['errorMessage']['PROJECT_MEMBER_TASK_EXIST']);
                                }
                                else
                                {
                                    $deleteProjectMember = $this->DBAccessLib->deleteProjectMember($passedData);
                                    if($deleteProjectMember)
                                    {
                                        $message = $this->settings['successMessage']['SUCCESS_MEMBER_DELETE'];
                                        $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                                    }
                                    else
                                    {
                                        $responseData = $this->MessageLib->errorMessageFormat('FAIL_MEMBER_DELETE', $this->settings['errorMessage']['FAIL_MEMBER_DELETE']);
                                    }
                                }
                            }
                        }    
                    }
                }
                else
                {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                }
            }
            else
            {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    /** */
    function projectNewMemberInvite(){
        $responseData = null;

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $project_name = parent::sanitizeInput($postData->projectName);
        $user_first_name = parent::sanitizeInput($postData->userFirstName);
        $user_last_name = parent::sanitizeInput($postData->userLastName);
        $invite_user_email = parent::sanitizeInput($postData->inviteUserEmail);
        
        $token = parent::getAuthorizationSessionObject();

        $passedData = array(
                "project_id"=>$project_id,
                "user_id"=>$user_id,
                "project_name"=>$project_name,
                "user_first_name"=>$user_first_name,
                "user_last_name"=>$user_last_name,
                "invite_user_email"=>$invite_user_email,
            );

        //check If User Can do the operation
        $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);
        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            // is user present
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            if ($activeUser)
            {
                if($checkIfUserCanCRUD['crudMember'])
                {
                    $this->EmailLib->sendNewUserInvitation($this->DBAccessLib, $this->UtilityLib, $passedData);
                    $responseData = $this->MessageLib->successMessageFormat($this->settings['successMessage']['SUCCESS_NEW_MEMBER_INVITE']);
                }
                else
                {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                }
            }
            else
            {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);   
    }

    function __destruct() {
        //echo 'The class "', __CLASS__, '" was destroyed.<br />';
        parent::__destruct();
        unset($this->DBAccessLib);
        unset($this->UtilityLib);
        unset($this->ValidationLib);
        unset($this->MessageLib);
        unset($this->SessionLib);
        unset($this->EmailLib);
        unset($this->JWTLib);
    }
}
?>
