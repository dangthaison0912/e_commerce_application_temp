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

$sqlCommand = "CREATE TABLE products (
		 		 id int(11) NOT NULL auto_increment,
				 product_name varchar(255) NOT NULL,
		 		 price varchar(16) NOT NULL,
				 details text NOT NULL,
				 category varchar(16) NOT NULL,
				 origin varchar(16) NOT NULL,
		 		 date_added date NOT NULL,
		 		 PRIMARY KEY (id),
		 		 UNIQUE KEY product_name (product_name)
		 		 ) ";
if (mysqli_query($con,$sqlCommand)){
    echo "Your products table has been created successfully!";
} else {
    echo "CRITICAL ERROR: products table has not been created.";
}
?>
