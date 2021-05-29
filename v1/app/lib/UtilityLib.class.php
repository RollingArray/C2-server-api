<?php

namespace C2\Utility;

class UtilityLib
{

    protected $settings = null;

    //__construct
    function __construct($settings)
    {
        //echo 'The class "', __CLASS__, '" was initiated!<br />';
        $this->settings = $settings;
    }

    //__destruct
    function __destruct()
    {
        //echo 'The class "', __CLASS__, '" was destroyed.<br />';

    }

    //dataValidator
    public function dataValidator($validationLib, $messageLib, $passedData)
    {
        $responseData = array();

        foreach ($passedData as $key => $value) {
            if ($value) {
                $validationKey = $key;

                $isValid = $validationLib->validateInputValueFormat($this->settings['validationRules'][$validationKey], $value);
                if ($isValid) {
                    $responseData['success'] = true;
                } else {
                    $responseData['success'] = false;
                    $responseData['error'] = $this->settings['errorValidationMessage'][$validationKey]; //$key
                    return $responseData;
                }
            } else {
                $responseData['success'] = true;
            }
        }


        return $responseData;
    }

    //generateUserId
    public function generateUserId($user_email)
    {
        return "USER_" . md5($user_email);
    }

    //generateDomainId
    public function generateDomainId()
    {
        return uniqid('DOMAIN_');
    }

    //generateUserId
    public function generateId($prependString)
    {
        return uniqid($prependString);
    }

    //generateVerificationCode
    public function generateVerificationCode()
    {
        return bin2hex(openssl_random_pseudo_bytes(4));
    }

    //generatePasswordResetCode
    public function generatePasswordResetCode()
    {
        return bin2hex(openssl_random_pseudo_bytes(2));
    }

    //generateKeyValueStructure
    private function generateKeyValueStructure($data)
    {
        $tempRows = array();
        foreach ($data as $key => $value) {
            //$keyName = $this->extractKeyName($key);
            $tempRows[$key] = $value;
        }

        return $tempRows;
    }

    //generateServiceReturnDataStructure
    private function generateServiceReturnDataStructure($passedData)
    {
        $responseData = array();

        //echo "$passedData".json_encode($passedData);

        if ($passedData) {
            $responseData['success'] = true;
            $responseData['data'] = $passedData;
        } else {
            $responseData['success'] = false;
        }


        return $responseData;
    }

    //encrypt
    public function encrypt(string $data)
    {

        $salt = $this->settings['hashKey']['SALT'];
        $algo = $this->settings['hashKey']['ALGO'];
        $method = $this->settings['hashKey']['METHOD'];
        $key = hash($algo, $salt);

        $ivSize = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivSize);

        $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);

        // For storage/transmission, we simply concatenate the IV and cipher text
        $encrypted = base64_encode($iv . $encrypted);

        return $encrypted;
    }

    //decrypt
    public function decrypt(string $data)
    {
        $salt = $this->settings['hashKey']['SALT'];
        $algo = $this->settings['hashKey']['ALGO'];
        $method = $this->settings['hashKey']['METHOD'];
        $key = hash($algo, $salt);

        $data = base64_decode($data);
        $ivSize = openssl_cipher_iv_length($method);
        $iv = substr($data, 0, $ivSize);
        $data = openssl_decrypt(substr($data, $ivSize), $method, $key, OPENSSL_RAW_DATA, $iv);

        return $data;
    }

    //hasedString
    public function hasedString($string)
    {
        $text = $string;
        $salt = $this->settings['hashKey']['SALT'];
        //$hashedString =  trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));

        $cipher = "aes-128-gcm";
        //$key = "tusil";
        if (in_array($cipher, openssl_get_cipher_methods())) {
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $hashedString = openssl_encrypt($text, $cipher, $salt, $options = 0, $iv, $tag);
        }

        return $hashedString;
    }

    //originalString
    function originalString($ciphertext)
    {
        $salt = $this->settings['hashKey']['SALT'];

        $cipher = "aes-128-gcm";
        if (in_array($cipher, openssl_get_cipher_methods())) {
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $originalString = openssl_decrypt($ciphertext, $cipher, $salt, $options = 0, $iv, $tag);
        }

        //$originalString = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));

        return $originalString;
    }

    // user
    //getAllInactiveUsers
    public function getAllInactiveUsers($DBAccessLib, $EmailLib)
    {
        $rows = $DBAccessLib->getAllInactiveUsers();
        $tempRows = array();
        foreach ($rows as $eachData) {
            $userFullName = $eachData['userFirstName'] . " " . $eachData['userLastName'];
            $email_track_id = $this->generateId('EMAILTRACK_');
            $passedData = array(
                "user_full_name" => $userFullName,
                "user_email" => $eachData['userEmail'],
                "user_verification_code" => $eachData['userVerificationCode'],
                "email_track_id" => $email_track_id
            );
            $eachData['email'] = $EmailLib->massInactiveSendSignUpVerificationCode($DBAccessLib, $passedData);
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    //getAllRegisteredUsers
    public function getAllRegisteredUsers($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getAllRegisteredUsers($passedData);
        $tempRows = array();
        $responseData = array();

        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    //getUserDetailsByUserId
    public function getUserDetailsByUserId($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getUserDetailsById($passedData);
        return $this->generateKeyValueStructure($rows);
    }

    //getUserDetailsByEmail
    public function getUserDetailsByEmail($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getUserDetailsByEmail($passedData);
        return $this->generateKeyValueStructure($rows);
    }

    //getUserDetailsBySearchText
    public function getUserDetailsBySearchText($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getUserDetailsBySearchText($passedData);
        $tempRows = array();

        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    // community access
    //checkIfUserCanCRUD
    public function checkIfUserCanCRUD($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->checkIfUserCanCRUD($passedData);
        return $this->generateKeyValueStructure($rows);
    }
    // project
    //getAllAccessPrivilegeDetails
    public function getAllAccessPrivilegeDetails($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getAllAccessPrivilegeDetails($passedData);
        return $this->generateServiceReturnDataStructure($rows);
    }

    //getAllProjectsForUser
    public function getAllProjectsForUser($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getAllProjectsForUser($passedData);
        $rows = $DBAccessLib->getAllProjectsForUser($passedData);
        $tempRows = array();
        foreach ($rows as $eachData) {
            $passedData = array(
                "user_id"=>$passedData['user_id'],
                "project_id"=>$eachData['projectId'],
            );
            $eachData['projectAdministrator'] = $this->getAllProjectUsersByType($DBAccessLib, $passedData, 'PROJECTUSERTYPEID_0001');
            $eachData['projectAssignees'] = $this->getAllProjectUsersByType($DBAccessLib, $passedData, 'PROJECTUSERTYPEID_0002');
            $eachData['projectReviewers'] = $this->getAllProjectUsersByType($DBAccessLib, $passedData, 'PROJECTUSERTYPEID_0003');

            $tempRows[] = $this->generateKeyValueStructure($eachData);

        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    //getAllMembersForProject
    public function getAllMembersForProject($DBAccessLib, $passedData)
    {
        $rows = array();
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['projectAdministrator'] = $this->getAllProjectUsersByType($DBAccessLib, $passedData, 'PROJECTUSERTYPEID_0001');
        $rows['projectAssignees'] = $this->getAllProjectUsersByType($DBAccessLib, $passedData, 'PROJECTUSERTYPEID_0002');
        $rows['projectReviewers'] = $this->getAllProjectUsersByType($DBAccessLib, $passedData, 'PROJECTUSERTYPEID_0003');

        return $this->generateKeyValueStructure($rows);
    }

    
    //getAllAssigneeCredibilityIndexForProject
    public function getAllAssigneeCredibilityIndexForProject($DBAccessLib, $passedData)
    {
        $rows = array();
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['projectAssignees'] = $this->getAllAssigneeCredibilityIndex($DBAccessLib, $passedData, 'PROJECTUSERTYPEID_0002');
        
        return $this->generateKeyValueStructure($rows);
    }

    //getAssigneeCredibilityIndexDetails
    public function getAssigneeCredibilityIndexDetails($DBAccessLib, $passedData)
    {
        $rows = array();
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['credibilityScoreDetails'] = $DBAccessLib->getAssigneeCredibilityScore($passedData);
        $rows['projectReviewDetails'] = $this->getAllReviewsForAssigneeForProject($DBAccessLib,$passedData);
        return $this->generateKeyValueStructure($rows);
    }

    //getAllReviewsForAssigneeForProject
    public function getAllReviewsForAssigneeForProject($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getAllReviewsForAssigneeForProject($passedData);
        $tempRows = array();
        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }




    public function getProjectRaw($DBAccessLib, $passedData, $rawDataKeys)
    {
        $rows = array();
        
        foreach($rawDataKeys as $value)
        {
            //project basic details
            if($value == 'projectDetails')
            {
                $rows['projectDetails'] = $this->getBasicProjectDetails($DBAccessLib, $passedData);
            }

            //project sprints
            if($value == 'projectSprints')
            {
                $rows['projectSprints'] = $this->getAllSprintsForProject($DBAccessLib, $passedData);
            }

            //project goals
            if($value == 'projectGoals')
            {
                $rows['projectGoals'] = $this->getAllGoalsForProject($DBAccessLib, $passedData);
            }

            //project administrator
            if($value == 'projectAdministrator')
            {
                $rows['projectAdministrator'] = $this->getAllProjectUsersByType($DBAccessLib, $passedData, 'PROJECTUSERTYPEID_0001');
            }

            //project assignees
            if($value == 'projectAssignees')
            {
                $rows['projectAssignees'] = $this->getAllProjectUsersByType($DBAccessLib, $passedData, 'PROJECTUSERTYPEID_0002');
            }

            //project reviewers
            if($value == 'projectReviewers')
            {
                $rows['projectReviewers'] = $this->getAllProjectUsersByType($DBAccessLib, $passedData, 'PROJECTUSERTYPEID_0003');
            }
        }

        return $this->generateKeyValueStructure($rows);
    }



    //getAllProjectUsers
    public function getAllProjectUsers($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getAllProjectUsers($passedData);
        $tempRows = array();
        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    //getAllProjectUsersByType
    public function getAllProjectUsersByType($DBAccessLib, $passedData, $memberType)
    {
        $rows = $DBAccessLib->getAllProjectUsersByType($passedData, $memberType);
        $tempRows = array();
        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    //getAllAssigneeCredibilityIndex
    public function getAllAssigneeCredibilityIndex($DBAccessLib, $passedData, $memberType)
    {
        $rows = $DBAccessLib->getAllAssigneeCredibilityIndex($passedData, $memberType);
        $tempRows = array();
        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    //getBasicProjectDetails
    public function getBasicProjectDetails($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getBasicProjectDetails($passedData);
        return $this->generateServiceReturnDataStructure($rows);
    }

    //sprint


    //getAllMembers
    public function getAllSprints($DBAccessLib, $passedData)
    {
        $rows = array();
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['projectSprints'] = $this->getAllSprintsForProject($DBAccessLib, $passedData);

        return $this->generateKeyValueStructure($rows);
    }

    //getAllSprintsForProject
    public function getAllSprintsForProject($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getAllSprintsForProject($passedData);
        $tempRows = array();

        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    //goal
    //getAllGoals
    public function getAllGoals($DBAccessLib, $passedData)
    {
        $rows = array();
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['projectGoals'] = $this->getAllGoalsForProject($DBAccessLib, $passedData);

        return $this->generateKeyValueStructure($rows);
    }

    //getAllGoalsForProject
    public function getAllGoalsForProject($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getAllGoalsForProject($passedData);
        $tempRows = array();

        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    // activity
    //getActivityReviewDetails
    public function getActivityReviewDetails($DBAccessLib, $passedData)
    {
        $rows = array();
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['activityDetails'] = $DBAccessLib->getActivityDetails($passedData);
        $rows['reviewDetails'] = $this->getReviewDetails($DBAccessLib, $passedData);

        return $this->generateKeyValueStructure($rows);
    }

    //getReviewDetails
    public function getReviewDetails($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getReviewDetails($passedData);
        $tempRows = array();

        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    //getAllActivities
    public function getAllActivities($DBAccessLib, $passedData)
    {
        $rows = array();
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['filter'] = true;
        $rows['projectActivities'] = $this->getAllActivitiesForProject($DBAccessLib, $passedData);

        return $this->generateKeyValueStructure($rows);
    }

    //getAllMyActivities
    public function getAllMyActivities($DBAccessLib, $passedData)
    {
        $rows = array();
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['projectActivities'] = $this->getMyActivitiesForProject($DBAccessLib, $passedData);

        return $this->generateKeyValueStructure($rows);
    }

    //getAllMyReviews
    public function getAllMyReviews($DBAccessLib, $passedData)
    {
        $rows = array();
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['projectActivities'] = $this->getMyReviewsForProject($DBAccessLib, $passedData);

        return $this->generateKeyValueStructure($rows);
    }

    //getAllActivitiesWithoutFilter
    public function getAllActivitiesWithoutFilter($DBAccessLib, $passedData)
    {
        $rows = array();
        
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['filter'] = false;
        
        return $this->generateKeyValueStructure($rows);
    }

    //getAllActivitiesForProject
    public function getAllActivitiesForProject($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getAllActivitiesForProject($passedData);
        $tempRows = array();

        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    //getMyReviewsForProject
    public function getMyReviewsForProject($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getMyReviewsForProject($passedData);
        $tempRows = array();

        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    //getMyActivitiesForProject
    public function getMyActivitiesForProject($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getMyActivitiesForProject($passedData);
        $tempRows = array();

        foreach ($rows as $eachData) {
            $tempRows[] = $this->generateKeyValueStructure($eachData);
        }

        return $this->generateServiceReturnDataStructure($tempRows);
    }

    

    //ifUserActivityWeighExceedForSprint
    public function ifUserActivityWeighExceedForSprint($DBAccessLib, $passedData, $operation_type)
    {
        $newWeight = 0;
        $rows = $DBAccessLib->getUserActivityWeighForSprint($passedData);
        $sumActivityWeight = $rows['sumActivityWeight'];


        if ($operation_type == 'edit') {
            $newWeight = $passedData["activity_weight_delta"];
        } else {
            $newWeight = $passedData["activity_weight"];
        }

        $totalWeight = $sumActivityWeight + $newWeight;

        //echo $totalWeight;

        if ($totalWeight <= 100) {
            return false;
        } else {
            return true;
        }
    }

    //review
    //updateActivityReview
    public function updateActivityReview($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getActivityPerformanceCalculationFacts($passedData);
        $activityWeight = $rows['activityWeight'];
        $characteristicsHigherBetter = $rows['characteristicsHigherBetter'];
        $criteriaPoorValue = $rows['criteriaPoorValue'];
        $criteriaOutstandingValue = $rows['criteriaOutstandingValue'];
        $achievedResultValue = $passedData['achieved_result_value'];

        $top = 0;
        $bottom = 0;
        if($characteristicsHigherBetter == 1){
            $top = ((int)$achievedResultValue - $criteriaPoorValue);  
            $bottom = ((int)$criteriaOutstandingValue - (int)$criteriaPoorValue); 
        }
        else{
            $top = ((int)$criteriaPoorValue - (int)$achievedResultValue); 
            $bottom = ((int)$criteriaPoorValue - (int)$criteriaOutstandingValue); 
        }

        //performance in %
        $performance = ($top / $bottom);
        $performanceInPercentage = $performance * 100;
        
        //weighted performance in %
        $weightedPerformances = $performanceInPercentage * (int)$activityWeight;
        $weightedPerformancesPercentage = $weightedPerformances / 100;
        
        $data = array(
            "user_id" => $passedData['user_id'],
            "project_id" => $passedData['project_id'],
            "activity_id" => $passedData['activity_id'],
            "activity_review_id" => $passedData['activity_review_id'],
            "reviewer_user_id" => $passedData['reviewer_user_id'],
            "achieved_result_value" => $passedData['achieved_result_value'],
            "performance_value" => round($performanceInPercentage, 2),
            "weighted_performance_value" => round($weightedPerformancesPercentage,2),
            "reviewer_comment" => $passedData['reviewer_comment'],
        );
        $updateActivityReview =  $DBAccessLib->updateActivityReview($data);

        if($updateActivityReview){
            $updateActivityReviewPerformance = $this->updateActivityReviewPerformance($DBAccessLib, $passedData);

            if($updateActivityReviewPerformance){
                return $this->updateUserCredibilityScore($DBAccessLib, $passedData);
            }
        }
    }
    
    /**
     * update activity review performance
     *
     * @param  mixed $DBAccessLib
     * @param  mixed $passedData
     * @return void
     */
    public function updateActivityReviewPerformance($DBAccessLib, $passedData)
    {
        $rows = $DBAccessLib->getAllReviewsForActivity($passedData);
        $weightedPerformancesPercentage = array();

        foreach ($rows as $eachData) {
            $weightedPerformancesPercentage[] = $eachData['weightedPerformanceValue'];
        }

        $numberOfElements = sizeof($weightedPerformancesPercentage); 
        $activityReviewPerformance = $this->findMedian($weightedPerformancesPercentage, $numberOfElements);

        $data = array(
            "user_id" => $passedData['user_id'],
            "activity_id" => $passedData['activity_id'],
            "activity_review_performance" => $activityReviewPerformance
        );
        return $DBAccessLib->updateActivityReviewPerformance($data);
    }

    /**
     * update user credibility score
     *
     * @param  mixed $DBAccessLib
     * @param  mixed $passedData
     * @return void
     */
    public function updateUserCredibilityScore($DBAccessLib, $passedData)
    {
        //get all activityA for assignee
        $activityAssignee = $DBAccessLib->getActivityAssignee($passedData);
        $assigneeUserId = $activityAssignee['assigneeUserId'];
        $projectId = $passedData['project_id'];

        //get all activities performance for assignee
        $data = array(
            "assignee_user_id" => $assigneeUserId
        );
        $rows = $DBAccessLib->getAllActivityPerformanceForAssignee($data);

        // calculate credibility score
        $allWight = 0;
		$allTotalWeightedPerformancesMean = 0;

        foreach ($rows as $eachData) {
            $activityWeight = $eachData['activityWeight'];
            $activityReviewPerformance = $eachData['activityReviewPerformance'];

            if($activityReviewPerformance != null){
                $allWight = $allWight + $activityWeight;
			    $allTotalWeightedPerformancesMean = $allTotalWeightedPerformancesMean + $activityReviewPerformance;
            }
        }

        //user credibility - percentage
		$userCredibilityScore = ($allTotalWeightedPerformancesMean / $allWight) * 100;

        $data = array(
            "user_id" => $passedData['user_id'],
            "assignee_user_id" => $assigneeUserId,
            "project_id" => $projectId,
            "user_credibility_score" => $userCredibilityScore
        );
        return $DBAccessLib->updateUserCredibilityScore($data);
    }

    /**
     * find median
     *
     * @param  mixed $elements
     * @param  mixed $numberOfElements
     * @return void
     */
    function findMedian(&$elements, $numberOfElements) 
        { 
            // First we sort the array 
            sort($elements); 
        
            // check for even case 
            if ($numberOfElements % 2 != 0) 
            return (double)$elements[$numberOfElements / 2]; 
            
            return (double)($elements[($numberOfElements - 1) / 2] + 
                            $elements[$numberOfElements / 2]) / 2.0; 
        } 
}
