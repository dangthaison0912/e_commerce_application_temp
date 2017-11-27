<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location: admin_login.php");
    exit();
}
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
if ($existCount == 0) { // evaluate the count
	 echo "Your login session data is not on record in the database.";
     exit();
}
?>
<?php
// Script Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<?php
// Parse the form data and add inventory item to the system
if (isset($_POST['user_name'])) {

	$uid = mysqli_real_escape_string($con,$_POST['thisID']);
  $user_name = mysqli_real_escape_string($con,$_POST['user_name']);
	$admin = mysqli_real_escape_string($con,$_POST['admin']);
	// See if that product name is an identical match to another product in the system
	$sql = mysqli_query($con,"UPDATE admin SET username='$user_name', admin='$admin' WHERE id='$uid'");
	header("location: user_list.php");
    exit();
}
?>
<?php
// Delete Item Question to Admin, and Delete Product if they choose
if (isset($_GET['deleteid'])) {
	echo 'Bạn có chắc muốn xoá sản phẩm với ID ' . $_GET['deleteid'] . '? <a href="user_list.php?yesdelete=' . $_GET['deleteid'] . '">Có</a> | <a href="user_list.php">Không</a>';
	exit();
}
if (isset($_GET['yesdelete'])) {
	// remove item from system and delete its picture
	// delete from database
	$id_to_delete = $_GET['yesdelete'];
	$sql = mysqli_query($con,"DELETE FROM admin WHERE id='$id_to_delete' LIMIT 1") or die (mysql_error());
	// unlink the image from server
	// Remove The Pic -------------------------------------------
    // $pictodelete = ("../inventory_images/$id_to_delete.jpg");
    // if (file_exists($pictodelete)) {
    //    		    unlink($pictodelete);
    // }
	header("location: user_list.php");
    exit();
}
?>
<?php
// Parse the form data and add inventory item to the system
if (isset($_POST['product_name'])) {

    $product_name = mysqli_real_escape_string($con,$_POST['product_name']);
	$price = mysqli_real_escape_string($con,$_POST['price']);
  $quantity = mysqli_real_escape_string($con,$_POST['quantity']);
	$category = mysqli_real_escape_string($con,$_POST['category']);
	$origin = mysqli_real_escape_string($con,$_POST['origin']);
	$details = mysqli_real_escape_string($con,$_POST['details']);
	// See if that product name is an identical match to another product in the system
	$sql = mysqli_query($con,"SELECT id FROM products WHERE product_name='$product_name' LIMIT 1");
	$productMatch = mysqli_num_rows($sql); // count the output amount
    if ($productMatch > 0) {
		echo 'Sản phẩm đã tồn tại trong cửa hàng, <a href="inventory_list.php">bấm vào đây</a>';
		exit();
	}
	// Add this product into the database now
	$sql = mysqli_query($con,"INSERT INTO products (product_name, price, quantity, details, category, origin, date_added)
        VALUES('$product_name','$price', '','$details','$category','$origin',now())") or die (mysqli_error($con));
     $pid = mysqli_insert_id($con);
	// Place image in the folder
	$newname = "$pid.jpg";
  // if (is_uploaded_file($_FILES['image']['tmp_name'])) {
  //   echo "Uploaded";
  // }
	move_uploaded_file( $_FILES['image']['tmp_name'], "../inventory_images/$newname");
	header("location: user_list.php");
  exit();
}
?>
<?php
// This block grabs the whole list for viewing
$user_list = "";
$sql = mysqli_query($con,"SELECT * FROM admin ORDER BY id");
$userCount = mysqli_num_rows($sql); // count the output amount
if ($userCount > 0) {
	while($row = mysqli_fetch_array($sql)){
       $id = $row["id"];
			 $user_name = $row["username"];
			 $admin = $row["admin"];
       $user_list .= "User ID: $id - <strong>$user_name</strong> - $admin - &nbsp; &nbsp; &nbsp; <a href='user_edit.php?uid=$id'>Chỉnh sửa</a> &bull; <a href='user_list.php?deleteid=$id'>Xoá</a></br>";
}
}
?>
<?php
// Gather this product's full information for inserting automatically into the edit form below on page
if (isset($_GET['uid'])) {
	$targetID = $_GET['uid'];
    $sql = mysqli_query($con,"SELECT * FROM admin WHERE id='$targetID' LIMIT 1");
    $userCount = mysqli_num_rows($sql); // count the output amount
    if ($userCount > 0) {
	    while($row = mysqli_fetch_array($sql)){

			 $user_name = $row["username"];
			 $admin = $row["admin"];
        }
    } else {
	    echo "Người dùng không tồn tại";
		exit();
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Danh sách kho hàng</title>
<link rel="stylesheet" href="../style/style.css" type="text/css" media="screen" />
</head>

<body>
<div align="center" id="mainWrapper">
  <?php include_once("../template_header.php");?>
  <div id="pageContent"><br />
<div align="left" style="margin-left:24px;">
      <h2>Danh sách các tài khoản</h2>
      <?php echo $user_list; ?>
    </div>
    <hr />
    <h3>
    &darr; Thông tin người dùng &darr;
    </h3>
    <form action="user_edit.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
    <table width="90%" border="0" cellspacing="0" cellpadding="6">
      <tr>
        <td width="20%" align="right">Tên người dùng</td>
        <td width="80%"><label>
          <input name="user_name" type="text" id="user_name" size="64" value="<?php echo $user_name; ?>" />
        </label></td>
      </tr>
      <tr>
        <td width="20%" align="right">Quyền admin</td>
        <td width="80%"><label>
          <input name="admin" type="text" id="admin" size="64" value="<?php echo $admin; ?>" />
        </label></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><label>
          <input name="thisID" type="hidden" value="<?php echo $targetID; ?>" />
          <input type="submit" name="button" id="button" value="Cập nhật thông tin" />
        </label></td>
      </tr>
  </div>
  <?php include_once("../template_footer.php");?>
</div>
</body>
</html>
