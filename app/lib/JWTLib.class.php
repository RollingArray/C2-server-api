<?php

    namespace C2\JWT;

    class JWTLib {
        
        protected $settings;
        function __construct($settings) {
            //echo 'The class "', __CLASS__, '" was initiated!<br />';
            $this->settings = $settings;
        }
    
        function __destruct() {
            //echo 'The class "', __CLASS__, '" was destroyed.<br />';
        }
    
        //header
        private function jwtHeader()
        {
            $header = [
                   'typ' => 'jwt',
                   'alg' => 'HS256'
                  ];

            $header = json_encode($header);     
            $header = base64_encode($header);

            return $header;
        }

        //payload
        private function jwtPayload($userId)
        {
            $tokenId    = base64_encode(bin2hex(random_bytes(10)));
            //var_dump($tokenId);
            $serverId = $this->settings['jwt']['serverId'];
            $clientId = $this->settings['jwt']['clientId'];
            $issuedAt   = time();
            //$expire     = $expireLimit;            
            
            $data = [
                    'iat'       => $issuedAt,         
                    'jti'       => $tokenId,         
                    'iss'       => $serverId,       
                    'userId'    => $userId,
                    'clientId'  => $clientId
                ];

            
            $payload = json_encode($data);
            $payload = base64_encode($payload);

            return $payload;
        }

        //signature
        private function jwtSignature($header, $payload)
        {
            $key = $this->settings['hashKey']['salt'];
            $algo = $this->settings['hashKey']['algo'];
            

            $headerAndPayload = $header . '.' . $payload;
            $signature = hash_hmac($algo,$headerAndPayload, $key, true);
            $signature = base64_encode($signature);

            return $signature;
        }

        //createNewToken
        public function createNewToken($user_id)
        {
            $header = $this->jwtHeader();
            $payload = $this->jwtPayload($user_id);
            $signature = $this->jwtSignature($header, $payload);

            $token = $header . '.' . $payload . '.' . $signature;

            $passedData = array(
                "user_id"=>$user_id, 
                "token"=>$token
            );

            //$this->insertSession($DBAccessLib, $passedData);

            return $token;
        }

        //insertSession
        public function insertSession($DBAccessLib, $passedData)
        {

            $ifUseHasAnySessionAny = $DBAccessLib->ifUseHasAnySessionAny($passedData);

            if($ifUseHasAnySessionAny)
            {
                $isDeleteSessionFromDB = $DBAccessLib->deleteSession($passedData);
            }
            
            $newSessionForUserInserted = $DBAccessLib->insertNewSession($passedData);
            
            return $newSessionForUserInserted;
        }

        //decodeToken
        public function decodeToken($token)
        {
            $revisedToken = null;
            $responseData = array();
            $key = $this->settings['hashKey']['salt'];
            $algo = $this->settings['hashKey']['algo'];
            
            $jwtValues = explode('.', $token);

            $receavedHeader = $jwtValues[0];
            $receavedPayload = $jwtValues[1];
            $receavedSignature = $jwtValues[2];
            
            $recievedHeaderAndPayload = $receavedHeader . '.' . $receavedPayload;
            $resultedsignature = base64_encode(hash_hmac($algo, $recievedHeaderAndPayload, $key, true));
            
            if($resultedsignature == $receavedSignature) 
            {
                
                $receavedPayLoad = json_decode(base64_decode($receavedPayload), true);
                $responseData['success'] = true;
                $responseData['receavedPayLoad'] = $receavedPayLoad;
            }
            else
            {
                $responseData['success'] = false;
            }

            //echo json_encode($responseData);
            return $responseData;

        }

        //checkSessionUser
        public function checkSessionUser($token, $user_id)
        {
            $decodedToken = $this->decodeToken($token);
            //echo json_decode($decodedToken, true);

            if($decodedToken['success'])
            {
                if($decodedToken['receavedPayLoad']['userId'] == $user_id)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        //checkIfSessionUserTokenExpired
        public function checkIfSessionUserTokenExpired($token, $user_id)
        {
            $decodedToken = $this->decodeToken($token);
            //echo json_decode($decodedToken, true);

            if($decodedToken['success'])
            {
                if($decodedToken['receavedPayLoad']['userId'] == $user_id)
                {
                    //if token expired
                    $now  = time(); //86400 = 1 day
                    $expectedExpire = $decodedToken['receavedPayLoad']['iat'] + $this->settings['jwt']['expireInSeconds'];
                    
                    if((int)$now < (int)$expectedExpire)
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
            
        public function sendBackToClient($token, $user_id, $keyName, $data, $crudReturn = NULL)
        {
          
            $ifValidToken = $this->checkIfSessionUserTokenExpired($token, $user_id);
            $revisedToken = null;
            $responseData = array();

            if($ifValidToken)
            {
                $revisedToken =  $token;

                $responseData['success'] = true;
                $responseData['tokenUpdated'] = false;
                $responseData[$keyName] = $data;
                $responseData['crudReturn'] = $crudReturn;
                $responseData['updatedLoggedInSessionId']  = $revisedToken;
            }    
            else{
              $newToken = $this->createNewToken($user_id);

              $responseData['success'] = true;
              $responseData['tokenUpdated'] = true;
              $responseData[$keyName] = $data;
              $responseData['crudReturn'] = $crudReturn;
              $responseData['updatedLoggedInSessionId']  = $newToken;
            } 
            // make it invalid          
            // else
            // {
            //     $responseData['success'] = false;
            //     $responseData['error'] = $this->settings['errorMessage']['INVALID_SESSION'];
            // }

            return $responseData;

        }
    }
?>