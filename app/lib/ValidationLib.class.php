<?php

    namespace C2\Validation;

    class ValidationLib {
        
        function __construct() {
            //echo 'The class "', __CLASS__, '" was initiated!<br />';
        }
    
        function __destruct() {
            //echo 'The class "', __CLASS__, '" was destroyed.<br />';
        }
        
        //validateInputValueFormat
        public function validateInputValueFormat($pattern, $value)
        {
            $isValid = false;

            $isMatchPattern = preg_match(
                    $pattern, $value
            );
            if ($isMatchPattern) {
                $isValid = true;
            } else {
                $isValid = false;
            }
            return $isValid;
        }

    }
?>