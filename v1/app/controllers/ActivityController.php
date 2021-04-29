<?php

namespace C2;

class ActivityController extends BaseAPI
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

    //activityCrud
    public function activityCrud()
    {
        $responseData = null;

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $sprint_id = parent::sanitizeInput($postData->sprintId);
        $goal_id = parent::sanitizeInput($postData->goalId);
        $assignee_user_id = parent::sanitizeInput($postData->assigneeUserId);
        $activity_name = parent::sanitizeInput($postData->activityName);
        $activity_weight = parent::sanitizeInput($postData->activityWeight);
        $activity_measurement_type = parent::sanitizeInput($postData->activityMeasurementType);
        $activity_result_type = parent::sanitizeInput($postData->activityResultType);
        $criteria_poor_value = parent::sanitizeInput($postData->criteriaPoorValue);
        $criteria_improvement_value = parent::sanitizeInput($postData->criteriaImprovementValue);
        $criteria_expectation_value = parent::sanitizeInput($postData->criteriaExpectationValue);
        $criteria_exceed_value = parent::sanitizeInput($postData->criteriaExceedValue);
        $criteria_outstanding_value = parent::sanitizeInput($postData->criteriaOutstandingValue);
        $characteristics_higher_better = parent::sanitizeInput($postData->characteristicsHigherBetter);
        
        $operation_type = parent::sanitizeInput($postData->operationType);
        $token = parent::getAuthorizationSessionObject();

        if(property_exists($postData, 'activityId'))
        {
            $activity_id = parent::sanitizeInput($postData->activityId);
        }
        else
        {
            $activity_id = $this->UtilityLib->generateId('');
        }

        $passedData = array(
                "user_id" => $user_id,    
                "activity_id" => $activity_id,
                "project_id" => $project_id,
                "sprint_id" => $sprint_id,
                "goal_id" => $goal_id,
                "assignee_user_id" => $assignee_user_id,
                "activity_name" => $activity_name,
                "activity_weight" => $activity_weight,
                "activity_measurement_type" => $activity_measurement_type,
                "activity_result_type" => $activity_result_type,
                "criteria_poor_value" => $criteria_poor_value,
                "criteria_improvement_value" => $criteria_improvement_value,
                "criteria_expectation_value" => $criteria_expectation_value,
                "criteria_exceed_value" => $criteria_exceed_value,
                "criteria_outstanding_value" => $criteria_outstanding_value,
                "characteristics_higher_better" => $characteristics_higher_better
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
                if($checkIfUserCanCRUD['crudActivity'])
                {
                    
                    //create
                    if($operation_type == 'create')
                    {
                        
                        $ifActivityAlreadyCreatedForProject = $this->DBAccessLib->ifActivityAlreadyCreatedForProject($passedData);
                        if($ifActivityAlreadyCreatedForProject)
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('ACTIVITY_EXIST', $this->settings['errorMessage']['ACTIVITY_EXIST']);
                        }
                        else
                        {
                            $insertActivity = $this->DBAccessLib->insertActivity($passedData);

                            //create new
                            if($insertActivity)
                            {
                                $message = $this->settings['successMessage']['SUCCESS_ACTIVITY_CREATE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            }
                            else
                            {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_ACTIVITY_CREATE', $this->settings['errorMessage']['FAIL_ACTIVITY_CREATE']);
                            }
                        }
                    }

                    //edit
                    else if($operation_type == 'edit')
                    {
                        // if activity already created for project
                        $ifActivityAlreadyCreatedForProject = $this->DBAccessLib->ifActivityAlreadyCreatedForProject($passedData);
                        if($ifActivityAlreadyCreatedForProject)
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('ACTIVITY_EXIST', $this->settings['errorMessage']['ACTIVITY_EXIST']);
                        }
                        else
                        {
                            //if activity already locked
                            $ifActivityAlreadyLocked = $this->DBAccessLib->ifActivityAlreadyLocked($passedData);
                            
                            if($ifActivityAlreadyLocked)
                            {
                                $responseData = $this->MessageLib->errorMessageFormat('ACTIVITY_LOCKED', $this->settings['errorMessage']['ACTIVITY_LOCKED']);
                            }
                            else{
                                $updateActivity = $this->DBAccessLib->updateActivity($passedData);

                                //update
                                if($updateActivity)
                                {
                                    $message = $this->settings['successMessage']['SUCCESS_ACTIVITY_UPDATE'];
                                    $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                                }
                                else
                                {
                                    $responseData = $this->MessageLib->errorMessageFormat('FAIL_ACTIVITY_UPDATE', $this->settings['errorMessage']['FAIL_ACTIVITY_UPDATE']);
                                }
                            }
                        }
                    }

                    //delete
                    else if($operation_type == 'delete')
                    {
                        //if activity already locked
                        $ifActivityAlreadyLocked = $this->DBAccessLib->ifActivityAlreadyLocked($passedData);
                            
                        if($ifActivityAlreadyLocked)
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('ACTIVITY_LOCKED', $this->settings['errorMessage']['ACTIVITY_LOCKED']);
                        }
                        else{
                            $updateActivity = $this->DBAccessLib->deleteActivity($passedData);

                            //update
                            if($updateActivity)
                            {
                                $message = $this->settings['successMessage']['SUCCESS_ACTIVITY_DELETE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            }
                            else
                            {
                                $responseData = $this->MessageLib->errorMessageFormat('SUCCESS_ACTIVITY_DELETE', $this->settings['errorMessage']['SUCCESS_ACTIVITY_DELETE']);
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

    //goalActivityAll
    public function goalActivityAll()
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
                    $tempRows = $this->UtilityLib->getAllActivities($this->DBAccessLib, $passedData);

                    //get user details
                    $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $tempRows);
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
