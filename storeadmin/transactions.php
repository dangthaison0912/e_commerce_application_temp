<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location: admin_login.php");
    exit();
}
// Be sure to check that this manager SESSION value is in fact in the database
$userID = preg_replace('#[^0-9]#i', '', $_SESSION["id"]); // filter everything but numbers and letters
$user = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["user"]); // filter everything but numbers and letters
$password = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["password"]); // filter everything but numbers and letters
// Run mySQL query to be sure that this person is an admin and that their password session var equals the database information
// Connect to the MySQL database
include "../storescripts/connect_to_mysql.php";
$db_host = "localhost";
// Place the username for the MySQL database here
$db_username = "sontest";
// Place the password for the MySQL database here
$db_pass = "123";
// Place the name for the MySQL database here
$db_name = "son";
$con = mysqli_connect("$db_host","$db_username","$db_pass","$db_name");

$sql = mysqli_query($con,"SELECT * FROM admin WHERE id='$userID' AND username='$user' AND password='$password' LIMIT 1"); // query the person
// ------- MAKE SURE PERSON EXISTS IN DATABASE ---------
$existCount = mysqli_num_rows($sql); // count the row nums
$edit_info = "<p><a href='http://localhost/MyOnlineStore/storeadmin/info_edit.php?adminid=$userID'>Thay đổi thông tin cá nhân</a>";
if ($existCount == 0) { // evaluate the count
	 echo "Your login session data is not on record in the database.";
   header("location: logout.php");
   exit();
}
?>
<?php
// This block grabs the whole list for viewing
$transaction_list = "";
$sql = mysqli_query($con,"SELECT * FROM transactions ORDER BY id");
$transactionCount = mysqli_num_rows($sql); // count the output amount
if ($transactionCount > 0) {
	while($row = mysqli_fetch_array($sql)){
       $id = $row["id"];
       $product_id_array = $row["product_id_array"];
       $quantity_array = $row["quantity_array"];
			 $user_name = $row["username"];
			 $amount = $row["payment_amount"];
			 $transaction_list .= "Transaction ID: $id - <strong>$user_name</strong> - Products: $product_id_array - Quantity: $quantity_array - Total: $amount &nbsp; &nbsp; &nbsp; </br>";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Store Admin Area</title>
<link rel="stylesheet" href="../style/style.css" type="text/css" media="screen" />
</head>

<body>
<div align="center" id="mainWrapper">
  <?php include_once("../template_header.php");?>
  <div id="pageContent"><br />
    <div align="left" style="margin-left:24px;">
      <h2>Danh sách các tài khoản</h2>
      <?php echo $transaction_list; ?>
      <!-- <p><a href="admin_logout.php">Đăng xuất</a> -->
    </div>
    <br />
  <br />
  <br />
  </div>
  <?php include_once("../template_footer.php");?>
</div>
</body>
</html>
