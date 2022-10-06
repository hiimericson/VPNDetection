<?php

$conn = mysqli_connect('localhost' , 'root' , '' , 'system_db');

if($conn){
    //echo "Database connection success!";
}else {
    echo "Database connection failed!";
}

?>

