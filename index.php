<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <!-- <link href="css/sb-admin-2.min.css" rel="stylesheet"> -->
    <title>8 Nature</title>
  </head>
  <body>
  <?php
//VPN DETECTION FILE
include ("vpndetection.php");
date_default_timezone_set('Asia/Manila');
// Database class file
include("db-class.php");

// adds "default" connection

db::addConnection('default', array(
  "HOST" => "localhost", 
  "USER" => "root", 
  "PASSWORD" => "", 
  "NAME" => "system_db"));

//includes fireweall
include("firewall.class.php");


Firewall::setTable("whitelist", "firewall_white_list");
Firewall::setTable("blacklist", "firewall_black_list");
Firewall::setTable("temporarylist", "firewall_temporary_list");
Firewall::setTable("minutelist", "firewall_minute_list");

Firewall::init(20);


require_once("dbcon.php");
session_start();
if(isset($_POST['submit'])){
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);
	
  $sql = "Select * from `tbl_user` where email='$email'";
  $result = mysqli_query($conn, $sql);
  $num = mysqli_num_rows($result);
  if ($num == 1){
      while($row=mysqli_fetch_assoc($result)){
          if (password_verify($password, $row['password'])){ 
              header("location: dashboard.php");
          } 
          else{
            echo "<div class='alert alert-warning' role='alert'>
            Wrong password!
         </div>";
          }
      }
} 
else {
  echo "<div class='alert alert-warning' role='alert'>
             User not found!
          </div>";
}
}
?>
    <div class="container">
      
      <h1>Please Login</h1>
      <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" >
        <div class="form-control">
          <input type="text" required name="email" value="">
          <label>Email</label>
        </div>

        <div class="form-control">
          <input type="password"  name="password" value="" required>
          <label>Password</label>
        </div>
        <button type="submit" class="btn" name="submit">Login</button>
        <p class="text">Don't have an account? <a href="registration.php">Register</a> </p>
      </form>
    </div>
    
	<!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
    <script src="js/script.js"></script>
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->
  </body>
</html>