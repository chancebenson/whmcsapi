<!DOCTYPE HTML>
<html>
<head>
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script type="text/javascript">
		// Lets use jquery since its very cross platform
		
		$(document).ready(function(){
 			$('#apicall').change(function(){
 				//activeOption = document.getElementById("#apicall option:selected");
 				activeOption = $("#apicall option:selected").val();
 				alert (activeOption);
 				document.getElementById("div"+activeOption).style.display = "block";
 			});
 		});
	</script> -->
	
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
$userErr = $passErr = $apiurlErr = "";
$user = $pass = $apikey = $apiurl = $apicall = "";

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
	API CALL: <select id="apicall"> <!-- Make sure to keep them alphabetic just cause lol -->
		<option value = "" selected>-- Select A Call --</option>
		<option value = "addclient">AddClient</opton>
		<option value = "getadmindetails">GetAdminDetails</option>
		<option value = "getclients">GetClients</option>
	</select>
	<br><br>
	<!-- These are the additional fields for addclient API call -->
	<!--<div id="clientfields" style="display:none">
		<p>Client First Name: <input type="text" name="clientfn"></p>
		<p>Client Last Name: <input type="text" name="clientln"></p>
		<p>Client Email: <input type="text" name="clientemail"></p>
		<p>Address 1: <input type="text" name="address1"></p>
		<p>City: <input type="text" name="city"></p>
		<p>State: <input type="text" name="state"></p>
		<p>PostCode: <input type="text" name="zipcode"></p>
		<p>Country: <input type="text" name="country"></p>
		<p>PhoneNumber: <input type="text" name="phone"></p>
		<p>Password: <input type="password" name="pwd2"></p>
	</div>-->
	<input type="submit" name="submit" value="Test The API">
</form>


<?php
/*if ($userErr != null) {
	die;
	// No reason to continue if we had an error
} */
echo "<h2>Your Values:</h2>";
echo $user;
echo "<br>";
echo $apikey;
echo "<br>";
echo $apiurl;
echo "<br>"; // Commented this out, seems redundant when the stuff below shows it as well
echo $apicall;
?>

<?php
if ($apicall == "getclients") {
	
	$url = "$apiurl";
	
	$postfields = array();
	$postfields["username"] = $user;
	$postfields["password"] = md5($pass);
	$postfields["accesskey"] = $apikey;
	$postfields["action"] = "getclients";
	$postfields["responsetype"] = "xml";

	$query_string = "";
	foreach ($postfields as $k=>$v) $query_string .= "$k=".urlencode($v)."&";
}

if ($apicall == "getadmindetails") {
	
	$url = "$apiurl";
	
	$postfields = array();
	$postfields["username"] = $user;
	$postfields["password"] = md5($pass);
	$postfields["accesskey"] = $apikey;
	$postfields["action"] = "getadmindetails";
	$postfields["responsetype"] = "xml";

	$query_string = "";
	foreach ($postfields as $k => $v) $query_string .= "$k".urlencode($v)."&";
}

if ($apicall == "addclient") {
	
	$url = "$apiurl";
	
	$postfields = array();
	$postfields["username"] = $user;
	$postfields["password"] = md5($pass);
	$postfields["accesskey"] = $apikey;
	$postfields["action"] = "addclient";
	$postfields["firstname"] = "$clientfn";
	$postfields["lastname"] = "$clientln";
	$postfields["email"] = "$clientemail";
	$postfields["address1"] = "$address1";
	$postfields["city"] = "$city";
	$postfields["state"] = "$state";
	$postfields["postcode"] = "$zipcode";
	$postfields["country"] = "$country";
	$postfields["phonenumber"] = "$phone";
	$postfields["password2"] = "$pwd2";
	$postfields["currency"] = "1"; // adding this manually as everyone should be on the base currency
	$postfields["responsetype"] = "xml";

	$query_string = "";
	foreach ($postfields as $k => $v) $query_string .="$k".urlencode($v)."&";
}

die("'$query_string'");

$ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt($ch, CURLOPT_POST, 1);
 curl_setopt($ch, CURLOPT_TIMEOUT, 30);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 $xml = curl_exec($ch);
 if (curl_error($ch) || !$xml) $xml = '<whmcsapi><result>error</result>'.
 	'<message>Connection Error</message><curlerror>'.
 curl_errno($ch).' - '.curl_error($ch).'</curlerror></whmcsapi>';
 curl_close($ch);

 $arr = whmcsapi_xml_parser($xml); # Parse XML
 	
 
 print_r($arr); # Output XML Response as Array

 echo "<textarea rows=50 cols=100>Request: ".print_r($postfields,true);
 echo "\nResponse: ".htmlentities($xml). "\n\nArray: ".print_r($arr,true);
 echo "</textarea>";

function whmcsapi_xml_parser($rawxml) {
 	$xml_parser = xml_parser_create();
 	xml_parse_into_struct($xml_parser, $rawxml, $vals, $index);
 	xml_parser_free($xml_parser);
 	$params = array();
 	$level = array();
 	$alreadyused = array();
 	$x=0;
 	foreach ($vals as $xml_elem) {
 	  if ($xml_elem['type'] == 'open') {
 		 if (in_array($xml_elem['tag'],$alreadyused)) {
 		 	$x++;
 		 	$xml_elem['tag'] = $xml_elem['tag'].$x;
 		 }
 		 $level[$xml_elem['level']] = $xml_elem['tag'];
 		 $alreadyused[] = $xml_elem['tag'];
 	  }
 	  if ($xml_elem['type'] == 'complete') {
 	   $start_level = 1;
 	   $php_stmt = '$params';
 	   while($start_level < $xml_elem['level']) {
 		 $php_stmt .= '[$level['.$start_level.']]';
 		 $start_level++;
 	   }
 	   $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
 	   @eval($php_stmt);
 	  }
 	}
 	return($params);
 }

 ?>


</body>

