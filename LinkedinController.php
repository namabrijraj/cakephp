<?php
/**
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email'); 
//App::import('Vendor', 'Linkedin',array('file'=>'process'.DS.'process.php'));
App::import('Vendor', 'Linkedin', array('file' => 'Linkedin/oauth_client.php'));
/** 
 * Static content controller 
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */

class LinkedinController extends AppController {


  public function process(){

if (isset($_GET["oauth_problem"]) && $_GET["oauth_problem"] <> "") {
  // in case if user cancel the login. redirect back to home page.
  $_SESSION["err_msg"] = $_GET["oauth_problem"];
  header("location:index.php");
  exit;
}


$baseURL = Configure::read('base_url').'/';
$callbackURL = Configure::read('base_url').'/Linkedin/process';
$linkedinApiKey = '81d97wl9r9j1lz';/*client_id*/
$linkedinApiSecret = 'Mva0zHkh5dVcBctJ';/*secret id*/
$linkedinScope = 'r_basicprofile r_emailaddress';

$client = new oauth_client_class;

$client->debug = false;
$client->debug_http = true;
$client->redirect_uri = $callbackURL;

$client->client_id = $linkedinApiKey;
$application_line = __LINE__;
$client->client_secret = $linkedinApiSecret;
//echo 'heloo'; die();
if (strlen($client->client_id) == 0 || strlen($client->client_secret) == 0)
  die('Please go to LinkedIn Apps page https://www.linkedin.com/secure/developer?newapp= , '.
      'create an application, and in the line '.$application_line.
      ' set the client_id to Consumer key and client_secret with Consumer secret. '.
      'The Callback URL must be '.$client->redirect_uri).' Make sure you enable the '.
      'necessary permissions to execute the API calls your application needs.';

/* API permissions
 */
$client->scope = $linkedinScope;
if (($success = $client->Initialize())) {
  if (($success = $client->Process())) {
    if (strlen($client->authorization_error)) {
      $client->error = $client->authorization_error;
      $success = false;
    } elseif (strlen($client->access_token)) {
      $success = $client->CallAPI(
          'http://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name,location,picture-url,public-profile-url,formatted-name)', 
          'GET', array(
            'format'=>'json'
          ), array('FailOnAccessError'=>true), $user);
    }
  }
  $success = $client->Finalize($success);
}
if ($client->exit) exit;
if ($success) {
  pr($user); exit();
  //  $user_id = $db->checkUser($user);
 // $_SESSION['loggedin_user_id'] = $user_id;
  //$_SESSION['user'] = $user;
} else {
   //$_SESSION["err_msg"] = $client->error;
}
//header("location:index.php");
//exit;
  }



}

