<!DOCTYPE HTML>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script type="text/javascript">
		// Lets use jquery since its very cross platform
		$(document).ready(function(){
			$('#apicall').change(function(){
				activeOption = document.getElementById("sel").selectedIndex;
				document.getElementById("div"+activeOption).style.display = "block";
			});
		});
	</script>
<style type="text/css">
	.error {color: #FF0000;}
</style>
</head>
<body>

<?php	

/* Use this if putting in your WHMCS directory 

if(file_exists("init.php")) {
	// Always use require once to avoid conflicts
	require_once("init.php");
} elseif(file_exists("../init.php")) {
	// Same as above
	require_once("../init.php");
} else {
	// Die, it was required
	die("Init Not Found");
/* */

// define vars and set to empty values
$userErr = null;
$passErr = null;
$apiurlErr = null;
$user = null;
$apikey = null;
$apiurl = null;

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (empty($_POST["user"]))
		{$userErr = "User is required";}
	else {$user = test_input ($_POST["user"]);}
	if (empty($_POST["pass"]))
		{$passErr = "Password is required";}
	else {$pass = test_input ($_POST["pass"]);}
	if (empty($_POST["apikey"]))
		{$apikey = "";}
	else {$apikey = test_input ($_POST["apikey"]);}
	if (empty($_POST["apiurl"]))
		{$apiurlErr = "API URL is required";}
	else {$apiurl = test_input ($_POST["apiurl"]);}
	if (empty($_POST["apicall"])) 
		{$apicall = "";}
	else {$apicall = test_input ($_POST["apicall"]);}
}

function test_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

?>

<h2>API TEST VALIDATION</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	User: <input type="text" name="user">
	<span class="error">* <?php echo $userErr;?></span>
	<br><br>
	Pass: <input type="password" name="pass">
	<span class="error">* <?php echo $passErr;?></span>
	<br><br>
	API Key: <input type="text" name="apikey">
	<br><br>
	API URL: <input type="text" name="apiurl">
	<span class="error">* <?php echo $apiurlErr;?></span>
	<br><br>
	API CALL: <select id="apicall"> <!-- Make sure to keep them alphabetic just cause lol -->
		<option value = "" selected>-- Select A Call --</option>
		<option value="addclient">AddClient</opton>
		<option value="getadmindetails">GetAdminDetails</option>
		<option value="getclients">GetClients</option>
	</select>
	<br><br>
	<div id="addclient" style="display:none">Client First Name: </div>
	<div id="addclient" style="display:none">Client Last Name: </div>
	<input type="submit" name="submit" value="Test The API">
</form>


<?php
if ($userErr == null) {
	die;
	// No reason to continue if we had an error
}
echo "<h2>Your Values:</h2>";
echo $user;
echo "<br>";
echo $apikey;
echo "<br>";
echo $apiurl;
//echo "<br>"; // Commented this out, seems redundant when the stuff below shows it as well
//echo $apicall;
?>

<?php
if ($apicall = "getclients") {
	
	$url = "$apiurl";
	//$apikey = "$apikey";  // I will uncomment once I fix this

	$postfields = array();
	$postfields["username"] = $user;
	$postfields["password"] = md5($pass);
	$postfields["action"] = "getclients";
	$postfields["responsetype"] = "json";

	$query_string = "";
	foreach ($postfields as $k => $v) {
	$query_string .= "$k=".urlencode($v)."&";
} }

if ($apicall = "getadmindetails") {
	
	$url = "$apiurl";
	//$apikey = "$apikey"; // I will uncomment once I fix this

	$postfields = array();
	$postfields["username"] = $user;
	$postfields["password"] = md5($pass);
	$postfields["action"] = "getadmindetails";
	$postfields["responsetype"] = "json";

	$query_string = "";
	foreach ($postfields as $k => $v) {
	$query_string .= "$k".urlencode($v)."&";
} }




$ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt($ch, CURLOPT_POST, 1);
 curl_setopt($ch, CURLOPT_TIMEOUT, 30);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 $jsondata = curl_exec($ch);
 if (curl_error($ch)) die("Connection Error: ".curl_errno($ch).' - '.curl_error($ch));
 curl_close($ch);
 
 $arr = json_decode($jsondata); # Decode JSON String
 
 print_r($arr); # Output XML Response as Array

 echo "<textarea rows=50 cols=100>Request: ".print_r($postfields,true);
 echo "\nResponse: ".htmlentities($jsondata). "\n\nArray: ".print_r($arr,true);
 echo "</textarea>";

 ?>


</body>
</head>
