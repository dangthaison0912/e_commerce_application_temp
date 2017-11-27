<?php
// Connect to the MySQL database
require "connect_to_mysql.php";

$db_host = "localhost";
// Place the username for the MySQL database here
$db_username = "sontest";
// Place the password for the MySQL database here
$db_pass = "123";
// Place the name for the MySQL database here
$db_name = "son";
$con = mysqli_connect("$db_host","$db_username","$db_pass","$db_name");

$sqlCommand = "CREATE TABLE admin (
		 		 id int(11) NOT NULL auto_increment,
				 username varchar(24) NOT NULL,
		 		 password varchar(24) NOT NULL,
		 		 last_log_date date NOT NULL,
		 		 PRIMARY KEY (id),
		 		 UNIQUE KEY username (username)
		 		 ) ";
if (mysqli_query($con,$sqlCommand)){
    echo "Your admin table has been created successfully!";
} else {
    echo "CRITICAL ERROR: admin table has not been created.";
}
?>
