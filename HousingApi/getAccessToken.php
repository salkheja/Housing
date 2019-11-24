<?php
include("config.php");

if (isset($_GET["user"]))	{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))	{$user=$_POST["user"];}
	if (isset($_GET["pwd"]))	{$pwd=$_GET["pwd"];}
	elseif (isset($_POST["pwd"]))	{$pwd=$_POST["pwd"];}

$user=preg_replace("/\'|\"|\\\\|;| /","",$user);
$pwd=preg_replace("/\'|\"|\\\\|;| /","",$pwd);

$link = mysqli_connect($DB_ip, $DB_user, $DB_pwd, $DB_name);

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
$query = "select password,username from user where id=".$user;
$result = mysqli_query($link,$query);
$object = new stdClass();
if(mysqli_num_rows($result)!=0){
while ($row = mysqli_fetch_assoc($result)) {
        if($row['password'] == $pwd){
		$date = date("Y-m-d H:i:s",time() + 2592000);
		$time = time();
		$object->accessToken = md5($pwd.$row['username'].$time);
		$object->returnCode = "0000";
		$object->description = "success";
		$query = "update accesstoken set status = 2 where user_id = ".$user;
		mysqli_query($link,$query);
		$query = "INSERT INTO `accesstoken` (`access_token`, `user_id`, `valid_till`,`status`) VALUES ('".$object->accessToken."', '".$user."', '".$date."','1')";
		mysqli_query($link,$query);
		$myJSON = json_encode($object);
		echo $myJSON;
	}else{
		$object->returnCode = "0001";
		$object->description = "failed wrong pwd";
		$myJSON = json_encode($object);
		echo $myJSON;
	}
}
}else{
		$object->returnCode = "0002";
		$object->description = "failed no user found";
		$myJSON = json_encode($object);
		echo $myJSON;
}
mysqli_close($link);
?>