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

$sqlCommand = "CREATE TABLE transactions (
		 		 id int(11) NOT NULL auto_increment,
				 product_id_array varchar(255) NOT NULL,
		 		 usernameid varchar(255) NOT NULL,
				 username varchar(255) NOT NULL,
				 payment_date varchar(255) NOT NULL,
				 payment_amount varchar(255) NOT NULL,
		 		 txn_id varchar(255) NOT NULL,
		 		 PRIMARY KEY (id),
		 		 UNIQUE KEY txn_id (id)
		 		 ) ";
if (mysqli_query($con,$sqlCommand)){
    echo "Your transactions table has been created successfully!";
} else {
    echo "CRITICAL ERROR: transactions table has not been created.";
}
?>
