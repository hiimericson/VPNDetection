<?php

include "dbcon.php";
$date = date("Y-m-d h:i:sa"); 
   
//$IP_ADDRESS = '209.35.167.243'; # Manual IP Address
$IP_ADDRESS = $_SERVER['REMOTE_ADDR']; //($_SERVER['REMOTE_ADDR'] == NULL ? NULL : $_SERVER['REMOTE_ADDR']);

// Input VPNAPI.IO API Key
// Create an account to get a free API Key
// Free API keys has a limit of 1000/requests per a day
$API_KEY = "78325ee171c942d5920a30218edec583";
// API URL
$API_URL = 'https://vpnapi.io/api/' . $IP_ADDRESS . '?key=' . $API_KEY;
// Fetch 
$response = file_get_contents($API_URL);
// Decode JSON response
$response = json_decode($response);
// Check if IP Address is VPN
if(isset($response->security->vpn)) {  
	// Add code here for any IP Address that is a VPN
	// echo $IP_ADDRESS, " is a VPN.\n";
    $sql = "INSERT INTO `tbl_data`(`ip_address`,`date`,`vpn`, `status`) VALUES ('$IP_ADDRESS','$date','Yes','Denied')";
    $result = mysqli_query($conn, $sql);
    echo "<div class='popup' id='popup'>";
    echo "<img src='img/warning.png' alt='Warning!'>";
    echo "<h2>Warning! Access Denied</h2>";
    echo "<p>You have been denied for accessing this website because you are using VPN. IP ADDRESS : $IP_ADDRESS</p>";
    echo "<button type ='button' onclick='closePopup()'>OK</button>";
    echo" </div>";
    exit;

    } else{
	// Add code here for any IP Address that is not a VPN
	//echo $IP_ADDRESS, " is not a VPN.\n";
    $sql = "INSERT INTO `tbl_data` (`ip_address`,`date`,`vpn`,`status`) VALUES ('$IP_ADDRESS', '$date','No','Accepted')";
    $result = mysqli_query($conn, $sql);
    }
?>
