#!/usr/bin/php
<?php
require_once('RabbitMQ/path.inc');
require_once('RabbitMQ/get_host_info.inc');
require_once('RabbitMQ/rabbitMQLib.inc');
ini_set('RabbitMQ/testRabbitMQ.ini','1');



function doLogin($username,$password)
{
    //Initiate connection with DB
    $db= mysqli_connect("127.0.0.1",'Admin','letsgetanA','projctdb');	
	 if(mysqli_connect_error() ){
		 $msg="Data base could not be reached" .PHP_EOL;
		 return $msg; 
	 }
    $username=cleanseInput($username,$db);
    $password=cleanseInput($password,$db);

    // check Username
    $Q="select* from testtable where usrname='$username'";
    $dbQuery=mysqli_query($db,$Q) or die (mysqli_error($db));
   //checks tho see
    if (mysqli_num_rows($dbQuery) == 0) {
	    echo 'No username found';
	    return FALSE;
    }
    $record=mysqli_fetch_array($dbQuery);
    $recordPW=$record['password'];

    if (password_verify($password,$recordPW) == FALSE ){
    	echo "Username and Password combination is invalid" . PHP_EOL;
	return FALSE;
    }

    echo "Authentication success" .PHP_EOL;
    mysqli_close($db);
    return TRUE;
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

function cleanseInput($input,$db){
	
	$input=mysqli_real_escape_string($db,$input);
	$input= trim($input);
	return($input);

}
$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

