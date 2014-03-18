<!DOCTYPE HTML>
<html>
<head>
<title>WHMCS API Test Script</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript">
        // Lets use jquery since its very cross platform
        $(document).ready(function() {
            
            $('.clientfields').hide();
            $('.clientproductfields').hide();

            $('#api').change(function () {
                if ($('#api option:selected').text() == "addclient")
                {
                    $('.clientfields').show();
                    $('.clientproductfields').hide();
                }
                else if ($('#api option:selected').text() == "getclientsproducts") 
                {
                    $('.clientfields').hide();
                    $('.clientproductfields').show();
                }
            });
        });
    </script>
<style type="text/css">
    .error {color: #FF0000;}
</style>
</head>
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
$user = $pass = $apikey = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["user"])) $userErr = "User is required";
    else $user = test_input ($_POST["user"]);
    if (empty($_POST["pass"])) $passErr = "Password is required";
    else $pass = test_input ($_POST["pass"]);
    if (!empty($_POST["apikey"])) $apikey = test_input ($_POST["apikey"]);
    if (empty($_POST["apiurl"])) $apiurlErr = "API URL is required";
    else $apiurl = (($_POST["apiurltype"]=="https") ? "https://" : "http://") .test_input ($_POST["apiurl"]);
    if (empty($_POST["apicall"])) $apicall = "";
    else $apicall = test_input ($_POST["apicall"]);
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<h2>API TEST VALIDATION</h2>
<p><span class="error">* required field.</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    User: <input type="text" name="user" value="<?php echo $user;?>">
    <span class="error">* <?php echo $userErr;?></span>
    <br><br>
    Pass: <input type="password" name="pass">
    <span class="error">* <?php echo $passErr;?></span>
    <br><br>
    API Key: <input type="text" name="apikey" value="<?php echo $apikey;?>">
    <br><br>
    API URL: <select id="apiurltype" name="apiurltype"><option value = "https">https://</option>
        <option value = "http">http://</option></select><input type="text" name="apiurl" value="<?php echo $apiurl;?>">
    <span class="error">* <?php echo $apiurlErr;?></span>
    <br><br>
    <div class="apicall">
        <label class="apicall">API CALL: </label>
        <select id="api"> <!-- Make sure to keep them alphabetic just cause lol -->
            <option value = "none" selected>-- Select A Call --</option>
            <option value = "addclient">Add Client</option>
            <option value = "getadmindetails">Get Admin Details</option>
            <option value = "getclients">Get Clients</option>
            <option value = "getclientsproducts">Get Clients Products</option>
    </div>
    <div class="clientfields">
        <label class="clientfn">Client First Name: </label>
        <input type="text" name="clientfn" class="clientfn">
        <label class="clienln">Client Last Name: </label>
        <input type="text" name="clientln" class="clientln">
    </div>
    <div class="clientproductfields">
        <p>Stuff here: <input type="text" name="stuff"></p>
    </div>
    <br><br>
    <!-- These are the additional fields for addclient API call -->
    
    <input type="submit" name="submit" value="Test The API">
</form>


<?php
echo "<h2>Your Values:</h2>";
echo $user;
echo "<br>";
echo $apikey;
echo "<br>";
echo $apiurl;
echo "<br>"; // Commented this out, seems redundant when the stuff below shows it as well
echo $api;
?>

<?php
if ($api == "getclients") {
    
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

if ($api == "getadmindetails") {
    
    $url = "$apiurl";
    
    $postfields = array();
    $postfields["username"] = $user;
    $postfields["password"] = md5($pass);
    $postfields["accesskey"] = $apikey;
    $postfields["action"] = "getadmindetails";
    $postfields["responsetype"] = "xml";

    $query_string = "";
    foreach ($postfields as $k=>$v) $query_string .= "$k".urlencode($v)."&";
}

if ($api == "addclient") {
    
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
    foreach ($postfields as $k=>$v) $query_string .="$k".urlencode($v)."&";
}

if($url) {
    #Only try curl when URL is submitted
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
}
 print_r($arr); # Output XML Response as Array

 echo "<textarea rows=50 cols=100>Request: $url\n\n".print_r($postfields,true);
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