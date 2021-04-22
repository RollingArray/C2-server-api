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
        $user_password = parent::sanitizeInput($postData->userPassword);
        $user_security_answer_1 = parent::sanitizeInput($postData->userSecurityAnswer1);
        $user_security_answer_2 = parent::sanitizeInput($postData->userSecurityAnswer2);
        $user_status = "INACTIVE";
        $domain_status = "ACTIVE";//
        $user_verification_code = $this->UtilityLib->generateVerificationCode();
        $user_full_name = ucfirst($user_first_name)." ".ucfirst($user_last_name);
        
        $passedData = array(
                "user_id"=>$user_id, 
                "user_email"=>$user_email,
                "user_first_name"=>$user_first_name, 
                "user_last_name"=>$user_last_name,
                "user_password"=>$user_password,
                "user_security_answer_1"=>$user_security_answer_1,
                "user_security_answer_2"=>$user_security_answer_2,
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
                //encript password
                $hashPassword = $this->UtilityLib->encrypt($passedData["user_password"]);
                $passedData["user_password"] = $hashPassword;

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

    //activateUserAccount
    public function activateUserAccount()
    {
        //passed data
        $responseData = null;
    
        //passed data
        $postData = parent::getPostData();
        $user_email = parent::sanitizeInput($postData->userEmail);
        $user_verification_code = parent::sanitizeInput($postData->userActivationCode);

        $passedData = array(
            "user_email"=>$user_email,
            "user_verification_code"=>$user_verification_code
        );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            //if Verification Code Valid
            $ifVerificationCodeValid = $this->DBAccessLib->ifVerificationCodeValid($passedData);
            if ($ifVerificationCodeValid) 
            {
                //is User Activated
                $isUserActivated = $this->DBAccessLib->activateUserAccount($passedData);
                    
                if ($isUserActivated) 
                {
                    $responseData = $this->MessageLib->successMessageFormat($this->settings['successMessage']['ACCOUNT_ACTIVATED']);

                    //send email
                    $getUserDetailsByEmail = $this->UtilityLib->getUserDetailsByEmail($this->DBAccessLib, $passedData); ;
                    $user_full_name = $getUserDetailsByEmail['userFirstName']." ".$getUserDetailsByEmail['userLastName'];
                    
                    $passedData = array(
                      "user_full_name"=>$user_full_name,
                      "user_email"=>$user_email
                    );

                    //user activated email
                    $this->EmailLib->signUpSuccess($this->DBAccessLib, $this->UtilityLib, $passedData); 
                }
                else
                {
                    $responseData = $this->MessageLib->errorMessageFormat('ACCOUNT_NOT_ACTIVATED', $this->settings['errorMessage']['ACCOUNT_NOT_ACTIVATED']);
                }
            }   
            else
            {
                $responseData = $this->MessageLib->errorMessageFormat('VERIFICATION_CODE_NO_MATCH', $this->settings['errorMessage']['VERIFICATION_CODE_NO_MATCH']);
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
                //ifUserInactive
                $ifUserInactive = $this->DBAccessLib->getIfUserInactive($passedData);
                if($ifUserInactive)
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
                    $responseData = $this->MessageLib->errorMessageFormat('ACCOUNT_ALREADY_ACTIVE', $this->settings['errorMessage']['ACCOUNT_ALREADY_ACTIVE']);  
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

    //generatePasswordResetCode
    public function generatePasswordResetCode()
    {
        $responseData = null;
        //echo parent::getPostData();
        $postData = parent::getPostData();
        $user_email = parent::sanitizeInput($postData->userEmail);
        $user_password_reset_code = $this->UtilityLib->generatePasswordResetCode();

        $passedData = array(
                "user_email"=>$user_email,
                "user_password_reset_code"=>$user_password_reset_code,
            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            // is user present
            $userPresent = $this->DBAccessLib->ifExistingUser($passedData);
            if ($userPresent) 
            {
                $userPasswordRestCodeGenerated = $this->DBAccessLib->generateUserPasswordResetCode($passedData);
                if ($userPasswordRestCodeGenerated) 
                {
                    $responseData = $this->MessageLib->successMessageFormat($this->settings['successMessage']['PASSWORD_RESET_CODE_GENERATED']);
                    
                    //send email
                    //send password reset code email
                    $getUserDetailsByEmail = $this->UtilityLib->getUserDetailsByEmail($this->DBAccessLib, $passedData); ;
                    $user_full_name = $getUserDetailsByEmail['userFirstName']." ".$getUserDetailsByEmail['userLastName'];
                    
                    $passedData = array(
                      "user_full_name"=>$user_full_name,
                      "user_email"=>$user_email, 
                      "user_password_reset_code"=>$user_password_reset_code
                    );
                    $this->EmailLib->sendPasswordResetCode($this->DBAccessLib, $this->UtilityLib, $passedData);               
                } 
                else 
                {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_PASSWORD_RESET_CODE', $this->settings['errorMessage']['NO_PASSWORD_RESET_CODE']);  
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

    //updatePassword
    public function updatePassword()
    {
        $responseData = null;
        //echo parent::getPostData();
        $postData = parent::getPostData();
        $user_email = parent::sanitizeInput($postData->userEmail);
        $user_password_reset_code = parent::sanitizeInput($postData->userPasswordResetCode);
        $user_password = parent::sanitizeInput($postData->userPassword);

        $passedData = array(
                "user_email"=>$user_email,
                "user_password_reset_code"=>$user_password_reset_code,
                "user_password"=>$user_password
            );

        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            // is user present
            $userPresent = $this->DBAccessLib->ifExistingUser($passedData);
            if ($userPresent) 
            {
                //ifPasswordResetCodeExist
                $ifPasswordResetCodeExist = $this->DBAccessLib->ifPasswordResetCodeExist($passedData);
                if ($ifPasswordResetCodeExist) 
                {
                    //encript password
                    $hashPassword = $this->UtilityLib->encrypt($passedData["user_password"]);
                    $passedData["user_password"] = $hashPassword;

                    //isUserPasswordUpdated
                    $isUserPasswordUpdated = $this->DBAccessLib->updatePassword($passedData); 
                    if($isUserPasswordUpdated)
                    {
                        $responseData = $this->MessageLib->successMessageFormat($this->settings['successMessage']['PASSWORD_UPDATED']);                    
                    } 
                    else 
                    {
                        $responseData = $this->MessageLib->errorMessageFormat('NO_PASSWORD_UPDATE', $this->settings['errorMessage']['NO_PASSWORD_UPDATE']);  
                    }               
                } 
                else 
                {
                    $responseData = $this->MessageLib->errorMessageFormat('PASSWORD_RESET_CODE_NOT_FOUND', $this->settings['errorMessage']['PASSWORD_RESET_CODE_NOT_FOUND']);  
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
        $user_email = parent::sanitizeInput($postData->userEmail);
        $user_password = parent::sanitizeInput($postData->userPassword);
        $user_login_type = parent::sanitizeInput($postData->userLoginType);
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $user_platform = $_SERVER['HTTP_USER_AGENT'];

        if($user_ip == '::1')
        {
            $user_ip = '127.0.0.1'; //::1 is the loopback address in IPv6. Think of it as the IPv6 version of 127.0.0.1
        }

        $passedData = array(
                "user_email"=>$user_email,
                "user_password"=>$user_password,
                "user_login_type"=>$user_login_type,
                "user_platform" => $user_platform,
                "user_ip" => $user_ip,
            );
        
        $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

        //if input validated
        if($validator['success'])
        {
            // is user present
            $userPresent = $this->DBAccessLib->ifExistingUser($passedData);
            
            if ($userPresent) 
            {
              //ifUserInactive
              $ifUserInactive = $this->DBAccessLib->getIfUserInactive($passedData);
              if(!$ifUserInactive)
              {
                $getSignInUser = $this->DBAccessLib->getSignInUser($passedData);

                $signInUserId = $getSignInUser['userId'];
                $signInUserPassword = $getSignInUser['userPassword'];
                
                $hashPassword = $this->UtilityLib->encrypt($passedData["user_password"]);
                
                $decriptPassword = $this->UtilityLib->decrypt($signInUserPassword);
                
                //var_dump($decriptPassword);
                
                if ($passedData["user_password"] == $decriptPassword) 
                {
                  
                    //sessionServer, insert new session 
                    $tokenId = $this->JWTLib->createNewToken($signInUserId); 
                    //var_dump($tokenId);               
                    if($tokenId == "NO_SESSION")
                    {
                        $responseData = $this->MessageLib->errorMessageFormat('INVALID_SESSION', $this->settings['errorMessage']['INVALID_SESSION']);
                    }
                    else
                    {
                        $passedData = array(
                            "user_id"=>$signInUserId,
                            "user_platform"=>$user_platform,
                            "user_login_type"=>$user_login_type,
                            "user_ip" => $user_ip
                        );

                        $this->DBAccessLib->logUserSession($passedData);
                        
                        $responseData['success'] = true;
                        if($user_login_type == 'IN_APP_LOGIN')
                        {
                            $responseData['message'] = $this->settings['successMessage']['SUCCESS_IN_APP_LOGIN']; 
                        }
                        else if($user_login_type == 'FRESH_LOGIN')
                        {
                            $responseData['message'] = $this->settings['successMessage']['SUCCESS_LOGIN']; 
                        }

                        $responseData['userId'] = $signInUserId;
                        $responseData['token']  = $tokenId;   
                    }
                } 
                else 
                {
                    $responseData = $this->MessageLib->errorMessageFormat('NO_USER_FOUND', $this->settings['errorMessage']['NO_USER_FOUND']);
                }    
              }
              else
              {
                  $responseData = $this->MessageLib->errorMessageFormat('ACCOUNT_INACTIVE', $this->settings['errorMessage']['ACCOUNT_INACTIVE']);  
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
        $user_security_answer_1 = parent::sanitizeInput($postData->userSecurityAnswer1);
        $user_security_answer_2 = parent::sanitizeInput($postData->userSecurityAnswer2);
        $token = parent::getAuthorizationSessionObject();

        $passedData = array(
                "user_id"=>$user_id, 
                "user_email"=>$user_email,
                "user_first_name"=>$user_first_name, 
                "user_last_name"=>$user_last_name,
                "user_security_answer_1"=>$user_security_answer_1,
                "user_security_answer_2"=>$user_security_answer_2
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
