
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css" />
    <title>Register</title>
</head>

<?php 
require_once("dbcon.php");
if(isset($_POST['submit'])){
		$firstName = $_POST['first_name'];
		$lastName = $_POST['last_name'];
		$email 	= $_POST['email'];
		$password = $_POST['password'];
		
		$options = array("cost"=>4);
		$hashPassword = password_hash($password,PASSWORD_BCRYPT,$options);
		
		$sql = "INSERT INTO `tbl_user`(`first_name`, `last_name`, `email`, `password`) VALUE ('".$firstName."', '".$lastName."', '".$email."','".$hashPassword."')";
		$result = mysqli_query($conn, $sql);
		if($result)
		{
			echo "<div class='alert alert-success' role='alert'>
            Register Successfully saved!
          </div>";
		}
	}
?>
<body>

<div class="container">
      
      <h1>Please Register </h1>
      <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" >
        <div class="form-control">
          <input type="text" required name="first_name" value="">
          <label>First name</label>
        </div>
        <div class="form-control">
          <input type="text" required name="last_name" value="">
          <label>Last name</label>
        </div>

        <div class="form-control">
          <input type="text" required name="email" value="">
          <label>Email</label>
        </div>

        <div class="form-control">
          <input type="password"  name="password" value="" required>
          <label>Password</label>
        </div>
        <button type="submit" class="btn" name="submit">Register</button>
        <p class="text">Already have an account? <a href="index.php">Log-in</a> </p>
      </form>
    </div>





    <script src="js/script.js"></script>
    <script src="vendor/jquery/jquery.min.js"></script>
</body>

</html>

















