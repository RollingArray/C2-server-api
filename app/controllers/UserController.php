<?php


namespace C2;

class UserController extends BaseAPI
{
    protected $User;
    protected $DBAccessLib;
    protected $UtilityLib;
    protected $ValidationLib;
    protected $MessageLib;
    protected $JWTLib;
    protected $EmailLib;

    public function __construct($settings) {
        parent::__construct($settings);
        $this->DBAccessLib = new Database\DBAccessLib($settings); // create a new object, class db()
        $this->UtilityLib = new Utility\UtilityLib($settings);
        $this->ValidationLib = new Validation\ValidationLib();
        $this->MessageLib = new Message\MessageLib($settings);
        $this->JWTLib = new JWT\JWTLib($settings);
        $this->EmailLib = new Email\EmailLib($settings);
    }

    public function test()
    {
      $responseData = $this->MessageLib->successMessageFormat($this->settings['successMessage']['SERVER_REACHABLE']);
      $passedData = array(
        "user_full_name"=>'Ranjoy Sen',
        "user_email"=>'ranjoy.sen85@gmail.com', 
        );
      $this->EmailLib->sendTestEmail($this->DBAccessLib, $this->UtilityLib, $passedData);
      echo json_encode($responseData);
    }

    public function signUp()
    {
        $responseData = null;
        //echo parent::getPostData();
        $postData = parent::getPostData();
        
        $user_email = parent::sanitizeInput($postData->userEmail);
        $user_id = $this->UtilityLib->generateId('');
        $user_first_name = parent::sanitizeInput($postData->userFirstName);
        $user_last_name = parent::sanitizeInput($postData->userLastName);
        $user_status = "INACTIVE";
        $domain_status = "ACTIVE";//
        $user_verification_code = $this->UtilityLib->generateVerificationCode();
        $user_full_name = ucfirst($user_first_name)." ".ucfirst($user_last_name);
        
        $passedData = array(
                "user_id"=>$user_id, 
                "user_email"=>$user_email,
                "user_first_name"=>$user_first_name, 
                "user_last_name"=>$user_last_name,
                "user_status"=>$user_status,
                "user_verification_code" => $user_verification_code
            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            // is user present
            $userPresent = $this->DBAccessLib->ifExistingUser($passedData);
            if ($userPresent) 
            {
                $responseData = $this->MessageLib->errorMessageFormat('USER_ALREADY_EXIST', $this->settings['errorMessage']['USER_ALREADY_EXIST']);
            } 
            else 
            {
                //insert new user
                $userInserted = $this->DBAccessLib->insertNewUser($passedData);
                 if($userInserted)
                {
                    $passedData = array(
                        "user_full_name"=>$user_full_name,
                        "user_email"=>$user_email, 
                        "user_verification_code"=>$user_verification_code
                    );

                    //send email
                    $this->EmailLib->sendSignUpVerificationCode($this->DBAccessLib, $this->UtilityLib, $passedData);

                    $responseData = $this->MessageLib->successMessageFormat($this->settings['successMessage']['ACTIVATE_ACCOUNT']);
                }
                else
                {
                    $responseData = $this->MessageLib->errorMessageFormat('USER_NOT_CREATED', $this->settings['errorMessage']['USER_NOT_CREATED']);
                }       
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }  

    //resendActivationCode
    public function resendActivationCode()
    {
        $responseData = null;
        //echo parent::getPostData();
        $postData = parent::getPostData();
        $user_email = parent::sanitizeInput($postData->userEmail);
        $user_status = "INACTIVE";//
        $user_verification_code = $this->UtilityLib->generateVerificationCode();

        $passedData = array(
                "user_email"=>$user_email,
                "user_status"=>$user_status,
                "user_verification_code"=>$user_verification_code,
            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            // is user present
            $userPresent = $this->DBAccessLib->ifExistingUser($passedData);
            if ($userPresent) 
            {
                //ifUserAccountActivationCodeRegenerated
                $ifUserAccountActivationCodeRegenerated = $this->DBAccessLib->regenerateUserAccountActivationCode($passedData);
                    
                if ($ifUserAccountActivationCodeRegenerated) 
                {
                    $responseData = $this->MessageLib->successMessageFormat($this->settings['successMessage']['VERIFICATION_CODE_REGENERATED']);
                    
                    //send email
                    //user activated email
                    $getUserDetailsByEmail = $this->UtilityLib->getUserDetailsByEmail($this->DBAccessLib, $passedData); ;
                    $user_full_name = $getUserDetailsByEmail['userFirstName']." ".$getUserDetailsByEmail['userLastName'];
                    $passedData = array(
                      "user_full_name"=>$user_full_name,
                      "user_email"=>$user_email, 
                      "user_verification_code"=>$user_verification_code
                    );

                    $this->EmailLib->sendSignUpVerificationCode($this->DBAccessLib, $this->UtilityLib, $passedData);                    
                } 
                else 
                {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_VERIFICATION_CODE', $this->settings['errorMessage']['NO_VERIFICATION_CODE']);
                } 
            } 
            else 
            {
                $responseData = $this->MessageLib->errorMessageFormat('NO_USER_FOUND', $this->settings['errorMessage']['NO_USER_FOUND']);      
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }

        echo json_encode($responseData);
    }

    /**
	 * signIn
	 *
	 * @return void
	 */
	public function signIn()
	{
		$responseData = array();
		$postData = parent::getPostData();
		$userEmail = parent::sanitizeInput($postData->userEmail);
		$userVerificationCode = parent::sanitizeInput($postData->userVerificationCode);
        $userLoginType = parent::sanitizeInput($postData->userLoginType);
		$userIp = $_SERVER['REMOTE_ADDR'];
		$usePlatform = $_SERVER['HTTP_USER_AGENT'];

		if ($userIp == '::1') {
			$userIp = '127.0.0.1'; //::1 is the loopback address in IPv6. Think of it as the IPv6 version of 127.0.0.1
		}

		$passedData = array(
			"user_email" => $userEmail,
			"user_verification_code" => $userVerificationCode,
			"user_platform" => $usePlatform,
			"user_ip" => $userIp,
		);

		$validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

		//if input validated
		if ($validator['success']) {
			// is user present
			$userPresent = $this->DBAccessLib->ifExistingUser($passedData);

			if ($userPresent) {
				$getUserDetailsByEmail = $this->DBAccessLib->getUserDetailsByEmail($passedData); ;
                $userId = $getUserDetailsByEmail['userId'];
                $passedData = array(
                    "user_email"=>$userEmail, 
                    "user_verification_code"=>$userVerificationCode
                );

				//if Verification Code Valid
				$ifVerificationCodeValid = $this->DBAccessLib->ifVerificationCodeValid($passedData);
				if ($ifVerificationCodeValid) {

					// nullify verification code
					$passedData = array(
						"user_email" => $userEmail,
					);

					$activateUserAccount = $this->DBAccessLib->activateUserAccount($passedData);

					if ($activateUserAccount) {
						//sessionServer, insert new session 
						$tokenId = $this->JWTLib->createNewToken($userId);
						//var_dump($tokenId);               
						if ($tokenId == "NO_SESSION") {
							$responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
						} else {
							$passedData = array(
								"user_id"=>$userId,
                                "user_platform"=>$usePlatform,
                                "user_login_type"=>$userLoginType,
                                "user_ip" => $userIp
							);

							$this->DBAccessLib->logUserSession($passedData);

                            $data['userId'] = $getUserDetailsByEmail['userId'];
                            $data['userEmail'] = $getUserDetailsByEmail['userEmail'];
							$data['userFirstName'] = $getUserDetailsByEmail['userFirstName'];
                            $data['userLastName'] = $getUserDetailsByEmail['userLastName'];
							$responseData = $this->JWTLib->sendBackToClient($tokenId, $userId, 'data', $data);

						}
					} else {
						$responseData = $this->MessageLib->errorMessageFormat('ACCOUNT_NOT_ACTIVATED', $this->settings['errorMessage']['ACCOUNT_NOT_ACTIVATED']);
					}
				} else {
					$responseData = $this->MessageLib->errorMessageFormat('VERIFICATION_CODE_NO_MATCH', $this->settings['errorMessage']['VERIFICATION_CODE_NO_MATCH']);
				}
			} else {
				$responseData = $this->MessageLib->errorMessageFormat('NO_USER_FOUND', $this->settings['errorMessage']['NO_USER_FOUND']);
			}
		} else {
			$responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
		}
		echo json_encode($responseData);
	}

    //getSignedInUserDetails
    public function getSignedInUserDetails()
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
                $userDetailsByUserId = $this->UtilityLib->getUserDetailsByUserId($this->DBAccessLib, $passedData); 
                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $userDetailsByUserId);
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

    //searchUserDetailsBySearchText
    public function searchUserDetailsBySearchText()
    {
        $responseData = array();
        
        //get post gata
        $postData = parent::getPostData();
        $user_id = parent::sanitizeInput($postData->userId);
        $search_key = parent::sanitizeInput($postData->searchKey);
        $token = parent::getAuthorizationSessionObject();

        //
        $passedData = array(
                "user_id"=>$user_id,
                "search_key"=>$search_key
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
                $data = $this->UtilityLib->getUserDetailsBySearchText($this->DBAccessLib, $passedData); 
                $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'data', $data);
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

    //updateUserProfile
    public function updateUserProfile()
    {
        $responseData = null;
        //echo parent::getPostData();
        $postData = parent::getPostData();
        $user_email = parent::sanitizeInput($postData->userEmail);
        $user_id = parent::sanitizeInput($postData->userId);
        $user_first_name = parent::sanitizeInput($postData->userFirstName);
        $user_last_name = parent::sanitizeInput($postData->userLastName);
        $token = parent::getAuthorizationSessionObject();

        $passedData = array(
                "user_id"=>$user_id, 
                "user_email"=>$user_email,
                "user_first_name"=>$user_first_name, 
                "user_last_name"=>$user_last_name
            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            $activeUser = $this->JWTLib->checkSessionUser($token, $user_id);                

            //activeUser
            if($activeUser)
            {
                //update user
                $updatedUserProfile = $this->DBAccessLib->updatedUserProfile($passedData);
                if ($updatedUserProfile)
                {
                    
                    $message = $this->settings['successMessage']['SUCCESS_USER_UPDATE'];
                    $responseData = $this->JWTLib->sendBackToClient($token, $user_id, 'message', $message);
                }
                else
                {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_USER_UPDATE', $this->settings['errorMessage']['NO_USER_UPDATE']);
                }
            } 
            else 
            {
                $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['NO_USER_FOUND']);
            }
        }
        else
        {
            $responseData = $this->MessageLib->errorMessageFormat('INVALID_INPUT', $validator['error']);
        }
        echo json_encode($responseData);
    }  

    //logout
    public function logout()
    {
        $responseData = array();
        
        //get post data
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
                $responseData['success'] = true;
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
