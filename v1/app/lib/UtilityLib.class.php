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
        return $this->generateServiceReturnDataStructure($rows);
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


    public function getProjectRaw($DBAccessLib, $passedData)
    {
        $rows = array();
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['projectAssignees'] = $this->getAllProjectUsersByType($DBAccessLib, $passedData, 'PROJECTUSERTYPEID_0002');
        $rows['projectSprints'] = $this->getAllSprintsForProject($DBAccessLib, $passedData);
        $rows['projectGoals'] = $this->getAllGoalsForProject($DBAccessLib, $passedData);

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

    //getAllActivities
    public function getAllActivities($DBAccessLib, $passedData)
    {
        $rows = array();
        $rows['projectDetails'] = $DBAccessLib->getBasicProjectDetails($passedData);
        $rows['filter'] = true;
        $rows['projectActivities'] = $this->getAllActivitiesForProject($DBAccessLib, $passedData);

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
}
