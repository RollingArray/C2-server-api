<?php

namespace C2;

class EmailController extends BaseAPI
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

    //emailTrackUpdate
    public function emailTrackUpdate()
    {
      $uri = $_SERVER["REQUEST_URI"];
      $uriArray = explode('/', $uri);
      $email_track_id =  end($uriArray);

      $passedData = array(
        "email_track_id"=>$email_track_id
      );

      $validator = $this->UtilityLib->dataValidator($this->ValidationLib, $this->MessageLib, $passedData);

      //if input validated
      if($validator['success'])
      {
        $this->DBAccessLib->updateEmailTrack($passedData);
      }      
      else
      {
        //
      }
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
?>
