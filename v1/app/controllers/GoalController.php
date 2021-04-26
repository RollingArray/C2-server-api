<?php

namespace C2;

class GoalController extends BaseAPI
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

    //goalCrud
    public function goalCrud()
    {
        $responseData = null;

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $goal_name = parent::sanitizeInput($postData->goalName);
        $goal_description = parent::sanitizeInput($postData->goalDescription);
        
        $operation_type = parent::sanitizeInput($postData->operationType);
        $token = parent::getAuthorizationSessionObject();

        $goal_id = null;
        if(property_exists($postData, 'goalId'))
        {
            $goal_id = parent::sanitizeInput($postData->goalId);
        }
        else
        {
            $goal_id = $this->UtilityLib->generateId('');
        }

        $passedData = array(
                "user_id"=>$user_id,
                "project_id"=>$project_id,
                "goal_id"=>$goal_id,
                "goal_name"=>$goal_name,
                "goal_description"=>$goal_description,
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
                if($checkIfUserCanCRUD['crudGoal'])
                {
                    //create
                    if($operation_type == 'create')
                    {
                        //if goal with same name already exist
                        $ifGoalAlreadyCreatedForSameProject = $this->DBAccessLib->ifGoalAlreadyCreatedForSameProject($passedData);

                        if($ifGoalAlreadyCreatedForSameProject)
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('GOAL_EXIST', $this->settings['errorMessage']['GOAL_EXIST']);
                        }
                        else
                        {
                            //insert new goal
                            $insertNewGoal = $this->DBAccessLib->insertNewGoal($passedData);

                            if($insertNewGoal)
                            {
                                //all date are store, pass back to client
                                $message = $this->settings['successMessage']['SUCCESS_GOAL_CREATE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            }
                            else
                            {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_GOAL_CREATE', $this->settings['errorMessage']['FAIL_GOAL_CREATE']);
                            }
                        }
                    }

                    //edit
                    else if($operation_type == 'edit')
                    {
                        //if goal with same name already exist
                        $ifGoalAlreadyCreatedForSameProject = $this->DBAccessLib->ifGoalAlreadyCreatedForSameProject($passedData);

                        if($ifGoalAlreadyCreatedForSameProject)
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('GOAL_EXIST', $this->settings['errorMessage']['GOAL_EXIST']);
                        }
                        else
                        {
                            //update spint details
                            $updateGoal = $this->DBAccessLib->updateGoal($passedData);
                            if($updateGoal)
                            {
                                $message = $this->settings['successMessage']['SUCCESS_GOAL_UPDATE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            }
                            else
                            {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_GOAL_UPDATE', $this->settings['errorMessage']['FAIL_GOAL_UPDATE']);
                            }
                        }
                    }

                    //delete
                    else if($operation_type == 'delete')
                    {
                        //if active or future goal alredy exist
                        $ifTaskPresentForGoal = $this->DBAccessLib->ifActivityPresentForGoal($passedData);

                        if($ifTaskPresentForGoal)
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('ACTIVITY_GOAL_ASSOCIATION', $this->settings['errorMessage']['ACTIVITY_GOAL_ASSOCIATION']);
                        }
                        else
                        {
                            //delete spint details
                            $deleteGoal = $this->DBAccessLib->deleteGoal($passedData);
                            if($deleteGoal)
                            {
                                $message = $this->settings['successMessage']['SUCCESS_GOAL_DELETE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            }
                            else
                            {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_GOAL_DELETE', $this->settings['errorMessage']['FAIL_GOAL_DELETE']);
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

    //projectGoalAll
    public function projectGoalAll()
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
                    $tempRows = $this->UtilityLib->getAllGoals($this->DBAccessLib, $passedData);

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
?>
