<!DOCTYPE HTML>
<html>
<head>
	<script type="javascript">
	function selectChanged(){
	activeOption = document.getElementById("sel").selectedIndex;
	document.getElementById("div"+activeOption).style.display = "block";}
	</script>
</head>
<body>

<?php	

/* Use this if putting in your WHMCS directory

if(file_exists("init.php")) require("init.php");
elseif(file_exists("../init.php")) require("../init.php");
else echo "Init Not Found";
*/

// define vars and set to empty values
$userErr = $passErr = $apiurlErr = "";
$user = $apikey = $apiurl = "";

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
	User: <input type="text" name="user" value="<?php echo $user;?>">
	<span class="error">* <?php echo $userErr;?></span>
	<br><br>
	Pass: <input type="password" name="pass">
	<span class="error">* <?php echo $passErr;?></span>
	<br><br>
	API Key: <input type="text" name="apikey" value="<?php echo $apikey;?>">
	<br><br>
	API URL: <input type="text" name="apiurl" value="<?php echo $apiurl;?>">
	<span class="error">* <?php echo $apiurlErr;?></span>
	<br><br>
	API CALL: <select id="apicall" onchange="selectedChanged">
		<option value="getadmindetails">GetAdminDetails</option>
		<option value="getclients">GetClients</option>
	</select>
	<br><br>
<input type="submit" name="submit" value="Test The API">
</form>

<!-- Let me show whats being sent across-->
<?php
echo "<h2>Your Values:</h2>";
echo $user;
echo "<br>";
echo $apikey;
echo "<br>";
echo $apiurl;
echo "<br>";
echo $apicall;
?>

<?php
if ($apicall = "getclients") {

	$url = "$apiurl";
	
	$postfields = array();
	$postfields["username"] = $user;
	$postfields["password"] = md5($pass);
	$postfields["accesskey"] = $apikey;
	$postfields["action"] = "getclients";
	$postfields["responsetype"] = "json";

	$query_string = "";
	foreach ($postfields as $k => $v) {
		$query_string .= "$k=".urlencode($v)."&";
	} 
}

if ($apicall = "getadmindetails") {

	$url = "$apiurl";
	
	$postfields = array();
	$postfields["username"] = $user;
	$postfields["password"] = md5($pass);
	$postfields["accesskey"] = $apikey;
	$postfields["action"] = "getadmindetails";
	$postfields["responsetype"] = "json";

	$query_string = "";
	foreach ($postfields as $k=>$v) {
		$query_string .= "$k".urlencode($v)."&";
	}
}

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
