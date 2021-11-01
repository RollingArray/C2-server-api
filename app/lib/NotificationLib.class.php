<?php

    namespace C2\Notification;

    class NotificationLib {
    	
      function __construct($settings) {
          $this->settings = $settings;
      }
    
      function __destruct() {
            //
      }

      
      function sendPushNotificationAndroid($deviceIds,$message){

        //API URL of FCM
        $url = $this->settings['notification']['FCM_URL'];
    
        //api_key available in:  
        $api_key = $this->settings['notification']['FCM_SERVER_KEY'];
                    
        $arrayToSend = array (
          'registration_ids' => $deviceIds,
          'data' => array (
                  "message" => $message
          )
        );
        
        $json = json_encode($arrayToSend);
    
        //header includes Content type and api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$api_key
        );
                    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
      }

      function sendPushNotificationIOS($deviceId,$message){

        //API URL of FCM
        $url = $this->settings['notification']['FCM_URL'];
    
        //api_key available in:  
        $api_key = $this->settings['notification']['FCM_SERVER_KEY'];
                
        $notification = array(
          'title' =>$message , 
          'text' => '', 
          'sound' => 'default',
        );
        $arrayToSend = array(
          'to' => $deviceId, 
          'notification' => $notification,
          'priority'=>'high'
        );
        $json = json_encode($arrayToSend);
    
        //header includes Content type and api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$api_key
        );
                    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
      }
    }
