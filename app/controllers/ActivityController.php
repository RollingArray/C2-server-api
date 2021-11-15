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

    public function __construct($settings)
    {
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

        if (property_exists($postData, 'activityId')) {
            $activity_id = parent::sanitizeInput($postData->activityId);
        } else {
            $activity_id = $this->UtilityLib->generateId('');
        }

        if (property_exists($postData, 'activityWeightDelta')) {
            $activity_weight_delta = parent::sanitizeInput($postData->activityWeightDelta);
        } else {
            $activity_weight_delta = 0;
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
            "activity_weight_delta" => $activity_weight_delta,
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
        if ($validator['success']) {
            // is user present
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            if ($activeUser) {
                //check access
                if ($checkIfUserCanCRUD['crudActivity']) {

                    //create
                    if ($operation_type == 'create') {

                        $ifActivityAlreadyCreatedForProject = $this->DBAccessLib->ifActivityAlreadyCreatedForProject($passedData);
                        if ($ifActivityAlreadyCreatedForProject) {
                            $responseData = $this->MessageLib->errorMessageFormat('ACTIVITY_EXIST', $this->settings['errorMessage']['ACTIVITY_EXIST']);
                        } else {
                            $ifUserActivityWeighExceedForSprint = $this->UtilityLib->ifUserActivityWeighExceedForSprint($this->DBAccessLib, $passedData, $operation_type);

                            if ($ifUserActivityWeighExceedForSprint) {
                                $responseData = $this->MessageLib->errorMessageFormat('WEIGHT_EXCEED', $this->settings['errorMessage']['WEIGHT_EXCEED']);
                            } else {
                                $insertActivity = $this->DBAccessLib->insertActivity($passedData);

                                //create new
                                if ($insertActivity) {
                                    $message = $this->settings['successMessage']['SUCCESS_ACTIVITY_CREATE'];
                                    $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                                } else {
                                    $responseData = $this->MessageLib->errorMessageFormat('FAIL_ACTIVITY_CREATE', $this->settings['errorMessage']['FAIL_ACTIVITY_CREATE']);
                                }
                            }
                        }
                    }

                    //edit
                    else if ($operation_type == 'edit') {
                        //if activity already locked
                        $ifActivityAlreadyLocked = $this->DBAccessLib->ifActivityAlreadyLocked($passedData);

                        if ($ifActivityAlreadyLocked) {
                            $responseData = $this->MessageLib->errorMessageFormat('ACTIVITY_LOCKED', $this->settings['errorMessage']['ACTIVITY_LOCKED']);
                        } else {
                            $ifUserActivityWeighExceedForSprint = $this->UtilityLib->ifUserActivityWeighExceedForSprint($this->DBAccessLib, $passedData, $operation_type);

                            if ($ifUserActivityWeighExceedForSprint) {
                                $responseData = $this->MessageLib->errorMessageFormat('WEIGHT_EXCEED', $this->settings['errorMessage']['WEIGHT_EXCEED']);
                            } else {
                                $updateActivity = $this->DBAccessLib->updateActivity($passedData);

                                //update
                                if ($updateActivity) {
                                    $message = $this->settings['successMessage']['SUCCESS_ACTIVITY_UPDATE'];
                                    $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                                } else {
                                    $responseData = $this->MessageLib->errorMessageFormat('FAIL_ACTIVITY_UPDATE', $this->settings['errorMessage']['FAIL_ACTIVITY_UPDATE']);
                                }
                            }
                        }
                    }

                    //delete
                    else if ($operation_type == 'delete') {
                        //if activity already locked
                        $ifActivityAlreadyLocked = $this->DBAccessLib->ifActivityAlreadyLocked($passedData);

                        if ($ifActivityAlreadyLocked) {
                            $responseData = $this->MessageLib->errorMessageFormat('ACTIVITY_LOCKED', $this->settings['errorMessage']['ACTIVITY_LOCKED']);
                        } else {
                            $updateActivity = $this->DBAccessLib->deleteActivity($passedData);

                            //update
                            if ($updateActivity) {
                                $message = $this->settings['successMessage']['SUCCESS_ACTIVITY_DELETE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            } else {
                                $responseData = $this->MessageLib->errorMessageFormat('SUCCESS_ACTIVITY_DELETE', $this->settings['errorMessage']['SUCCESS_ACTIVITY_DELETE']);
                            }
                        }
                    }
                } else {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                }
            } else {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        } else {
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

        // if filter properties exist
        if (property_exists($postData, 'sprintId') && property_exists($postData, 'goalId') && property_exists($postData, 'assigneeUserId')) {
            $sprint_id = parent::sanitizeInput($postData->sprintId);
            $goal_id = parent::sanitizeInput($postData->goalId);
            $assignee_user_id = parent::sanitizeInput($postData->assigneeUserId);

            $passedData = array(
                "user_id" => $user_id,
                "project_id" => $project_id,
                "sprint_id" => $sprint_id,
                "goal_id" => $goal_id,
                "assignee_user_id" => $assignee_user_id,
            );
    
            $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);
    
            //if input validated
            if ($validator['success']) {
                $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);
    
                //activeUser
                if ($activeUser) {
                    $ifProjectAccessToMember = $this->DBAccessLib->ifProjectAccessToMember($passedData);
    
                    if ($ifProjectAccessToMember) 
                    {
                        //check If User Can do the operation
                        $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);
                
                        //check access
                        if($checkIfUserCanCRUD['viewActivity'])
                        {
                            $tempRows = $this->UtilityLib->getAllActivities($this->DBAccessLib, $passedData);
    
                            //get user details
                            $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $tempRows);
                        }
                        else
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                        }
                    } 
                    else {
                        $responseData = $this->MessageLib->errorMessageFormat('NO_PROJECT_ACCESS_TO_MEMBER', $this->settings['errorMessage']['NO_PROJECT_ACCESS_TO_MEMBER']);
                    }
                } 
                else {
                    $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
                }
            } 
            else {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
            }
    
            echo json_encode($responseData);

        } 

        // if filter properties does not exist
        else {
            $passedData = array(
                "user_id" => $user_id,
                "project_id" => $project_id,
            );
    
            $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);
    
            //if input validated
            if ($validator['success']) {
                $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);
    
                //activeUser
                if ($activeUser) {
                    $ifProjectAccessToMember = $this->DBAccessLib->ifProjectAccessToMember($passedData);
    
                    if ($ifProjectAccessToMember) {

                        //check If User Can do the operation
                        $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);
                
                        if($checkIfUserCanCRUD['viewActivity'])
                        {
                            $tempRows = $this->UtilityLib->getAllActivitiesWithoutFilter($this->DBAccessLib, $passedData);
    
                            //get user details
                            $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $tempRows);
                        }
                        else
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                        }

                        
                    } else {
                        $responseData = $this->MessageLib->errorMessageFormat('NO_PROJECT_ACCESS_TO_MEMBER', $this->settings['errorMessage']['NO_PROJECT_ACCESS_TO_MEMBER']);
                    }
                } else {
                    $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
                }
            } else {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
            }
    
            echo json_encode($responseData);
        }
    }

    //allMyActivities
    public function allMyActivities()
    {
        $responseData = array();
        $tempRows = array();

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $assignee_user_id = parent::sanitizeInput($postData->assigneeUserId);
        $token = parent::getAuthorizationSessionObject();

        $passedData = array(
            "user_id" => $user_id,
            "project_id" => $project_id,
            "assignee_user_id" => $assignee_user_id,
        );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);
    
            //if input validated
            if ($validator['success']) {
                $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);
    
                //activeUser
                if ($activeUser) {
                    $ifProjectAccessToMember = $this->DBAccessLib->ifProjectAccessToMember($passedData);
    
                    if ($ifProjectAccessToMember) {
                        $tempRows = $this->UtilityLib->getAllMyActivities($this->DBAccessLib, $passedData);
    
                        //get user details
                        $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $tempRows);
                    } else {
                        $responseData = $this->MessageLib->errorMessageFormat('NO_PROJECT_ACCESS_TO_MEMBER', $this->settings['errorMessage']['NO_PROJECT_ACCESS_TO_MEMBER']);
                    }
                } else {
                    $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
                }
            } else {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
            }
    
            echo json_encode($responseData);
    }

    //allMyReviews
    public function allMyReviews()
    {
        $responseData = array();
        $tempRows = array();

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $reviewer_user_id = parent::sanitizeInput($postData->reviewerUserId);
        $token = parent::getAuthorizationSessionObject();

        $passedData = array(
            "user_id" => $user_id,
            "project_id" => $project_id,
            "reviewer_user_id" => $reviewer_user_id,
        );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);
    
            //if input validated
            if ($validator['success']) {
                $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);
    
                //activeUser
                if ($activeUser) {
                    $ifProjectAccessToMember = $this->DBAccessLib->ifProjectAccessToMember($passedData);
    
                    if ($ifProjectAccessToMember) {
                        $tempRows = $this->UtilityLib->getAllMyReviews($this->DBAccessLib, $passedData);
    
                        //get user details
                        $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $tempRows);
                    } else {
                        $responseData = $this->MessageLib->errorMessageFormat('NO_PROJECT_ACCESS_TO_MEMBER', $this->settings['errorMessage']['NO_PROJECT_ACCESS_TO_MEMBER']);
                    }
                } else {
                    $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
                }
            } else {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
            }
    
            echo json_encode($responseData);
    }

    //activityCommentCrud
    public function activityCommentCrud()
    {
        $responseData = null;

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $activity_id = parent::sanitizeInput($postData->activityId);
        $assignee_user_id = parent::sanitizeInput($postData->assigneeUserId);
        $claimed_result_value = parent::sanitizeInput($postData->claimedResultValue);
        $comment_description = parent::sanitizeInput($postData->commentDescription);
        
        $operation_type = parent::sanitizeInput($postData->operationType);
        $token = parent::getAuthorizationSessionObject();

        if (property_exists($postData, 'commentId')) {
            $comment_id = parent::sanitizeInput($postData->commentId);
        } else {
            $comment_id = $this->UtilityLib->generateId('');
        }

        $passedData = array(
            "user_id" => $user_id,
            "project_id" => $project_id,
            "activity_id" => $activity_id,
            "comment_id" => $comment_id,
            "assignee_user_id" => $assignee_user_id,
            "comment_description" => $comment_description,
            "claimed_result_value" => $claimed_result_value
        );

        //check If User Can do the operation
        $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if ($validator['success']) {
            // is user present
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            if ($activeUser) {

                //if comment not by same user
                if ($assignee_user_id != $user_id) {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_SAME_USER', $this->settings['errorMessage']['NO_SAME_USER']);
                } else {
                    //check access
                    if ($checkIfUserCanCRUD['crudComment']) {
                        //create
                        if ($operation_type == 'create') {
                            $insertActivityComment = $this->DBAccessLib->insertActivityComment($passedData);

                            //create new
                            if ($insertActivityComment) {
                                $message = $this->settings['successMessage']['SUCCESS_COMMENT_ADD'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            } else {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_COMMENT_ADD', $this->settings['errorMessage']['FAIL_COMMENT_ADD']);
                            }
                        }

                        //edit
                        else if ($operation_type == 'edit') {
                            $updateActivityComment = $this->DBAccessLib->updateActivityComment($passedData);

                            //create new
                            if ($updateActivityComment) {
                                $message = $this->settings['successMessage']['SUCCESS_COMMENT_UPDATE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            } else {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_COMMENT_UPDATE', $this->settings['errorMessage']['FAIL_COMMENT_UPDATE']);
                            }
                        }

                        //delete
                        else if ($operation_type == 'delete') {
                            $deleteActivityComment = $this->DBAccessLib->deleteActivityComment($passedData);

                            //create new
                            if ($deleteActivityComment) {
                                $message = $this->settings['successMessage']['SUCCESS_COMMENT_DELETE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            } else {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_COMMENT_DELETE', $this->settings['errorMessage']['FAIL_COMMENT_DELETE']);
                            }
                        }
                    } else {
                        $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                    }
                }
            } else {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        } else {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    //activityReviewerCrud
    public function activityReviewerCrud()
    {
        $responseData = null;

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $activity_id = parent::sanitizeInput($postData->activityId);
        $reviewer_user_id = parent::sanitizeInput($postData->reviewerUserId);
        
        $operation_type = parent::sanitizeInput($postData->operationType);
        $token = parent::getAuthorizationSessionObject();

        if (property_exists($postData, 'activityReviewId')) {
            $activity_review_id = parent::sanitizeInput($postData->activityReviewId);
        } else {
            $activity_review_id = $this->UtilityLib->generateId('');
        }

        $passedData = array(
            "user_id" => $user_id,
            "project_id" => $project_id,
            "activity_id" => $activity_id,
            "activity_review_id" => $activity_review_id,
            "reviewer_user_id" => $reviewer_user_id,
        );

        //check If User Can do the operation
        $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if ($validator['success']) {
            // is user present
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            if ($activeUser) {

                //check access
                if ($checkIfUserCanCRUD['crudReviewer']) {
                    //create
                    if ($operation_type == 'create') {

                        //ifAddedReviewerAlreadySameInActivity
                        $ifAddedReviewerAlreadySameInActivity = $this->DBAccessLib->ifAddedReviewerAlreadySameInActivity($passedData);

                        if($ifAddedReviewerAlreadySameInActivity)
                        {
                            $responseData = $this->MessageLib->errorMessageFormat('REVIEWER_EXIST', $this->settings['errorMessage']['REVIEWER_EXIST']);
                        }
                        else
                        {
                            $insertActivityReviewer = $this->DBAccessLib->insertActivityReviewer($passedData);

                            //create new
                            if ($insertActivityReviewer) {
                                $message = $this->settings['successMessage']['SUCCESS_REVIEWER_ADD'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            } else {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_REVIEWER_ADD', $this->settings['errorMessage']['FAIL_REVIEWER_ADD']);
                            }
                        }
                    }

                    //delete
                    else if ($operation_type == 'delete') {

                        $ifReviewerAlreadyReviewedActivity = $this->DBAccessLib->ifReviewerAlreadyReviewedActivity($passedData)['achievedResultValue'];
                        
                        if ($ifReviewerAlreadyReviewedActivity) {
                            $responseData = $this->MessageLib->errorMessageFormat('REVIEW_EXIST', $this->settings['errorMessage']['REVIEW_EXIST']);
                        } 
                        else {
                            $deleteActivityReviewer = $this->DBAccessLib->deleteActivityReviewer($passedData);

                            //create new
                            if ($deleteActivityReviewer) {
                                $message = $this->settings['successMessage']['SUCCESS_REVIEWER_DELETE'];
                                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                            } else {
                                $responseData = $this->MessageLib->errorMessageFormat('FAIL_REVIEWER_DELETE', $this->settings['errorMessage']['FAIL_REVIEWER_DELETE']);
                            }
                        }
                    }
                } else {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                }
            } else {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        } else {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    //activityReviewUpdate
    public function activityReviewUpdate()
    {
        $responseData = null;

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $activity_id = parent::sanitizeInput($postData->activityId);
        $reviewer_user_id = parent::sanitizeInput($postData->reviewerUserId);
        $achieved_result_value = parent::sanitizeInput($postData->achievedResultValue);
        $reviewer_comment = parent::sanitizeInput($postData->reviewerComment);
        $activity_review_id = parent::sanitizeInput($postData->activityReviewId);
        $token = parent::getAuthorizationSessionObject();

        $passedData = array(
            "user_id" => $user_id,
            "project_id" => $project_id,
            "activity_id" => $activity_id,
            "activity_review_id" => $activity_review_id,
            "reviewer_user_id" => $reviewer_user_id,
            "achieved_result_value" => $achieved_result_value,
            "reviewer_comment" => $reviewer_comment
        );

        //check If User Can do the operation
        $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if ($validator['success']) {
            // is user present
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            if ($activeUser) {

                //check access
                if ($checkIfUserCanCRUD['crudReview']) {

                    //$insertActivityReviewer = $this->DBAccessLib->updateActivityReviewer($passedData);
                    $updateActivityReview = $this->UtilityLib->updateActivityReview($this->DBAccessLib, $passedData);

                    //create new
                    if ($updateActivityReview) {
                        $updateActivityReviewPerformance = $this->UtilityLib->updateActivityReviewPerformance($this->DBAccessLib, $passedData); 
                        $message = $this->settings['successMessage']['SUCCESS_REVIEW_ADD'];
                        $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                    } else {
                        $responseData = $this->MessageLib->errorMessageFormat('FAIL_REVIEW_ADD', $this->settings['errorMessage']['FAIL_REVIEW_ADD']);
                    }
                } else {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                }
            } else {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        } else {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    //activityReviewDetails
    public function activityReviewDetails()
    {
        $responseData = array();

        //get post gata
        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $activity_id = parent::sanitizeInput($postData->activityId);
        $token = parent::getAuthorizationSessionObject();

        //
        $passedData = array(
            "user_id" => $user_id,
            "project_id" => $project_id,
            "activity_id" => $activity_id,
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
                $getAllProjectsForUser = $this->UtilityLib->getActivityReviewDetails($this->DBAccessLib, $passedData);
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

    //activityReviewUpdate
    public function lockActivityReview()
    {
        $responseData = null;

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $activity_review_id = parent::sanitizeInput($postData->activityReviewId);
        $token = parent::getAuthorizationSessionObject();

        $passedData = array(
            "user_id" => $user_id,
            "project_id" => $project_id,
            "activity_review_id" => $activity_review_id
        );

        //check If User Can do the operation
        $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if ($validator['success']) {
            // is user present
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            if ($activeUser) {

                //check access
                if ($checkIfUserCanCRUD['crudReviewLock']) {

                    $lockActivityReview = $this->DBAccessLib->lockActivityReview($passedData);
                    
                    //create new
                    if ($lockActivityReview) {
                        $message = $this->settings['successMessage']['SUCCESS_REVIEW_LOCK'];
                        $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                    } else {
                        $responseData = $this->MessageLib->errorMessageFormat('FAIL_REVIEW_LOCK', $this->settings['errorMessage']['FAIL_REVIEW_LOCK']);
                    }
                } else {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                }
            } else {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        } else {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    //activityReviewUpdate
    public function unlockActivityReview()
    {
        $responseData = null;

        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $project_id = parent::sanitizeInput($postData->projectId);
        $activity_review_id = parent::sanitizeInput($postData->activityReviewId);
        $token = parent::getAuthorizationSessionObject();

        $passedData = array(
            "user_id" => $user_id,
            "project_id" => $project_id,
            "activity_review_id" => $activity_review_id
        );

        //check If User Can do the operation
        $checkIfUserCanCRUD = $this->UtilityLib->checkIfUserCanCRUD($this->DBAccessLib, $passedData);

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if ($validator['success']) {
            // is user present
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);

            if ($activeUser) {

                //check access
                if ($checkIfUserCanCRUD['crudReviewLock']) {

                    $lockActivityReview = $this->DBAccessLib->unlockActivityReview($passedData);
                    
                    //create new
                    if ($lockActivityReview) {
                        $message = $this->settings['successMessage']['SUCCESS_REVIEW_UNLOCK'];
                        $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                    } else {
                        $responseData = $this->MessageLib->errorMessageFormat('FAIL_REVIEW_UNLOCK', $this->settings['errorMessage']['FAIL_REVIEW_UNLOCK']);
                    }
                } else {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_ACCESS', $this->settings['errorMessage']['NO_ACCESS']);
                }
            } else {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
            }
        } else {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }
    
    function __destruct()
    {
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
