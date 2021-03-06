<?php

namespace C2;

class SprintController extends BaseAPI
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

    //sprintCrud
    public function sprintCrud()
    {
        $responseData = null;

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $sprint_name = parent::sanitizeInput($postData->sprintName);
        $sprint_start_date = parent::sanitizeInput($postData->sprintStartDate);
        $sprint_end_date = parent::sanitizeInput($postData->sprintEndDate);
        $sprint_status = parent::sanitizeInput($postData->sprintStatus);
        
        $operation_type = parent::sanitizeInput($postData->operationType);
        $token = parent::getAuthorizationSessionObject();

        $sprint_id = null;
        if(property_exists($postData, 'sprintId'))
        {
            $sprint_id = parent::sanitizeInput($postData->sprintId);
        }
        else
        {
            $sprint_id = $this->UtilityLib->generateId('');
        }

        $passedData = array(
                "user_id"=>$user_id,
                "project_id"=>$project_id,
                "sprint_id"=>$sprint_id,
                "sprint_name"=>$sprint_name,
                "sprint_start_date"=>$sprint_start_date,
                "sprint_end_date"=>$sprint_end_date,
                "sprint_status"=>$sprint_status,
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
                 //check access
                if($checkIfUserCanCRUD['crudSprint'])
                {
                    //create
                    if($operation_type == 'create')
                    {
                        //if sprint with same name already exist
                        $ifSprintAlreadyCreatedForSameProject = $this->DBAccessLib->ifSprintAlreadyCreatedForSameProject($passedData);

                        if($ifSprintAlreadyCreatedForSameProject)
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('SPRINT_EXIST', $this->settings['errorMessage']['SPRINT_EXIST']);
                        }
                        else
                        {
                            //insert new sprint
                            $insertNewSprint = $this->DBAccessLib->insertNewSprint($passedData);

                            if($insertNewSprint)
                            {
                                //all date are store, pass back to client
                                $message = $this->settings['successMessage']['SUCCESS_SPRINT_CREATE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            }
                            else
                            {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_SPRINT_CREATE', $this->settings['errorMessage']['FAIL_SPRINT_CREATE']);
                            }
                        }
                    }

                    //edit
                    else if($operation_type == 'edit')
                    {
                        //update spint details
                        $updateSprint = $this->DBAccessLib->updateSprint($passedData);
                        if($updateSprint)
                        {
                            $message = $this->settings['successMessage']['SUCCESS_SPRINT_UPDATE'];
                            $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                        }
                        else
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('FAIL_SPRINT_UPDATE', $this->settings['errorMessage']['FAIL_SPRINT_UPDATE']);
                        }

                    }

                    //edit
                    else if($operation_type == 'start')
                    {
                        $ifActiveSprintAvailable = $this->DBAccessLib->ifActiveSprintAvailable($passedData); 
                        if($ifActiveSprintAvailable){
                            $responseData = $this->MessageLib->errorMessageFormat('ACTIVE_SPRINT_EXIST', $this->settings['errorMessage']['ACTIVE_SPRINT_EXIST']);
                        }
                        else 
                        {

                            //update spint details
                            $updateSprint = $this->DBAccessLib->updateSprint($passedData);
                            if($updateSprint)
                            {
                                $message = $this->settings['successMessage']['SUCCESS_SPRINT_ACTIVE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            }
                            else
                            {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_SPRINT_ACTIVE', $this->settings['errorMessage']['FAIL_SPRINT_ACTIVE']);
                            }
                        }
                    }

                    //edit
                    else if($operation_type == 'stop')
                    {
                        //update spint details
                        $updateSprint = $this->DBAccessLib->updateSprint($passedData);
                        if($updateSprint)
                        {
                            $message = $this->settings['successMessage']['SUCCESS_SPRINT_CLOSED'];
                            $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                        }
                        else
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('FAIL_SPRINT_CLOSED', $this->settings['errorMessage']['FAIL_SPRINT_CLOSED']);
                        }
                    }

                    //delete
                    else if($operation_type == 'delete')
                    {
                        //if active or future sprint already exist
                        $ifActivityPresentForSprint = $this->DBAccessLib->ifActivityPresentForSprint($passedData);

                        if($ifActivityPresentForSprint)
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('ACTIVITY_SPRINT_ASSOCIATION', $this->settings['errorMessage']['ACTIVITY_SPRINT_ASSOCIATION']);
                        }
                        else
                        {
                            //delete spint details
                            $deleteSprint = $this->DBAccessLib->deleteSprint($passedData);
                            if($deleteSprint)
                            {
                                $message = $this->settings['successMessage']['SUCCESS_SPRINT_DELETE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            }
                            else
                            {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_SPRINT_DELETE', $this->settings['errorMessage']['FAIL_SPRINT_DELETE']);
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

    //projectSprintAll
    public function projectSprintAll()
    {
        $responseData = array();
        $tempRows = array();

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $token = parent::getAuthorizationSessionObject();

        $passedData = array(
                "user_id" => $user_id,
                "project_id" => $project_id
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
                    if($checkIfUserCanCRUD['viewSprint'])
                    {
                        $tempRows = $this->UtilityLib->getAllSprints($this->DBAccessLib, $passedData);
                        //get user details
                        $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $tempRows);
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
