<?php
include("config.php");

if (isset($_GET["user"]))	{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))	{$user=$_POST["user"];}
	if (isset($_GET["accesstoken"]))	{$accesstoken=$_GET["accesstoken"];}
	elseif (isset($_POST["accesstoken"]))	{$accesstoken=$_POST["accesstoken"];}

$user=preg_replace("/\'|\"|\\\\|;| /","",$user);
$accesstoken=preg_replace("/\'|\"|\\\\|;| /","",$accesstoken);

$link = mysqli_connect($DB_ip, $DB_user, $DB_pwd, $DB_name);

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
$query = "select access_token, valid_till from accesstoken where user_id= ".$user." and status = 1";
$result = mysqli_query($link,$query);
$object = new stdClass();

$properties = array();
$i =0;
if(mysqli_num_rows($result)!=0){
	while ($row = mysqli_fetch_assoc($result)) {
		$valid_till = date("Y-m-d H:i:s",strtotime($row['valid_till']));
		$date = date("Y-m-d H:i:s",time());
		if($accesstoken == $row['access_token'] and $valid_till > $date){
		$object->returnCode = "0000";
		$object->description = "success";
		//add select statement
			$query = "select * from accesstoken where user_id = ".$user;
			$res = mysqli_query($link,$query);
			if(mysqli_num_rows($res)!=0){
			while ($row = mysqli_fetch_assoc($res)) {
				$property = new stdClass();
				$property->id = $row['access_token'];
				$property->name = $user;
				$properties[$i] = $property;
		     $object->properties = $properties;
			 $myJSON = json_encode($object);
			 $i = $i+1;
			}
			}echo $myJSON;
		}else{
		$object->returnCode = "0002";
		$object->description = "provided access token is unvalid for requested user ". $user;
		$myJSON = json_encode($object);
		echo $myJSON;
		}
	}
}else{
		$object->returnCode = "0003";
		$object->description = "no access token avaliable for requested user ". $user;
		$myJSON = json_encode($object);
		echo $myJSON;
}
?>