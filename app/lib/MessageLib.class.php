<?php

    namespace C2\Message;

    class MessageLib {
        private $validationRules;

        function __construct($settings) {
            //echo 'The class "', __CLASS__, '" was initiated!<br />';
            $this->validationRules = $settings;
        }
    
        function __destruct() {
            //echo 'The class "', __CLASS__, '" was destroyed.<br />';
        }

        //successSignUpWithDomain
        public function successMessageFormat($message)
        {
            $responseData = array();
            $responseData['success'] = true;
            $responseData['message'] = $message;

            return $responseData;
        }

        //errorPasswordResetCodeFormat
        public function errorMessageFormat($errorCode, $message)
        {
            $responseData = array();
            $errorData = array();
            
            $errorData['errorCode']  = $errorCode;
            $errorData['message']    = $message;
            
            $responseData['success'] = false;
            $responseData['error'] = $errorData;

            //echo json_encode($responseData);

            return $responseData;
        }
    }
?>