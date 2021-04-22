<?php
	namespace C2\Session;
	
    class SessionLib {
		protected $settings;
		function __construct($settings) {
            //echo 'The class "', __CLASS__, '" was initiated!<br />';
			$this->settings = $settings;
        }
    
        function __destruct() {
            //echo 'The class "', __CLASS__, '" was destroyed.<br />';
        }
		
		public function getAuthorizationSessionObject($UtilityLib)
		{
			$headers = apache_request_headers();
	        return $UtilityLib->originalString($headers['Auth']);
		}

		//insertSession
		public function insertSession($DBAccessLib, $UtilityLib, $user_id)
		{
			$ifUseHasAnySessionAny = $DBAccessLib->ifUseHasAnySessionAny($user_id);

			if($ifUseHasAnySessionAny)
			{
				$isDeleteSessionFromDB = $DBAccessLib->deleteSessionFromDB($user_id);
			}
			
			//session_start();
		    $_SESSION["sessionID"] = uniqid('SESSION_');

		    $returnSessionId;

		    $newSessionForUserInserted = $DBAccessLib->insertNewSessionInDB($user_id, $_SESSION["sessionID"]);
		    if($newSessionForUserInserted)
		    {
		    	$returnSessionId = $UtilityLib->hasedString($_SESSION["sessionID"]);
		    }
		    else
			{
				$returnSessionId = "NO_SESSION";
			}

			return $returnSessionId;
		}

		//updateSession
		public function updateSession($DBAccessLib, $UtilityLib, $user_id)
		{
			
			//create session
		    session_start();
		    $_SESSION["sessionID"] = uniqid('SESSION_');

		    $returnSessionId = null;

		    $isSessionUpdate = $DBAccessLib->updateSessionInDB($user_id, $_SESSION["sessionID"]);
		    
			//check no of row effected
			if ($isSessionUpdate) {
				$returnSessionId = $UtilityLib->hasedString($_SESSION["sessionID"]);
			}
			else
			{
				$returnSessionId = "NO_SESSION";
			}

			return $returnSessionId;
		}

		//checkSessionUser
		public function checkSessionUser($DBAccessLib, $user_id, $logged_in_session_id)
		{
			//$original_string = $UtilityLib->originalString($logged_in_session_id);
	   		
	   		//passed data
			$responseData = array();
			$activeUser = false;

			$rowsEffected = $DBAccessLib->getUserSessionDetails($user_id, $logged_in_session_id);
			
			//check no of row effected
			if ($rowsEffected) 
			{
				$activeUser = true;
			}
			else
			{
				$activeUser = false;
			}
			return true;
		}

		//sendBackToClient
		public function sendBackToClient($DBAccessLib, $UtilityLib, $user_id, $keyName, $data)
		{
			$responseData = array();

			//sessionServer, update session
			$updatedcommunitypalLoggedInSessionId = $this->updateSession($DBAccessLib, $UtilityLib, $user_id); 
			
			if($updatedcommunitypalLoggedInSessionId == "NO_SESSION")
			{
				$responseData['success'] = false;
				$responseData['error'] = $this->settings['errorMessage']['INVALID_SESSION'];
			}
			else
			{
				$responseData['success'] = true;
				$responseData[$keyName] = $data;
				$responseData['updatedLoggedInSessionId']  = $updatedcommunitypalLoggedInSessionId;
			}

			return $responseData;
		}

		//destroySessionUser
		function destroySessionUser($DBAccessLib, $user_id)
		{	
			$responseData = false;

			$isDeleteSessionFromDB = $DBAccessLib->deleteSessionFromDB($user_id);

			if(!$isDeleteSessionFromDB)
			{
				$responseData['success'] = false;
			}
			else
			{
				$responseData['success'] = true;
			}

			return $responseData;
		}
	}
?>