<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "shopsphere";

//storing servername, root and database in a variable
$conn = new mysqli($servername, $username, $password, $database);

// initializing connection
if($conn->connect_error) {
    die("connection failed:" .$conn->connect->error);
}
    
?>