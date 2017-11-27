<?php
session_start();
if (!isset($_SESSION["user"])) {
    echo 'Vui lòng <a href="user_login.php">đăng nhập</a> trước!';
    exit();
}
// Be sure to check that this manager SESSION value is in fact in the database
$userID = preg_replace('#[^0-9]#i', '', $_SESSION["id"]); // filter everything but numbers and letters
$user = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["user"]); // filter everything but numbers and letters
$password = preg_replace('#[^A-Za-z0-9]#i', '', $_SESSION["password"]); // filter everything but numbers and letters
// Run mySQL query to be sure that this person is an admin and that their password session var equals the database information
// Connect to the MySQL database
include "storescripts/connect_to_mysql.php";
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
if (isset($_POST['username'])) {

	$uid = mysqli_real_escape_string($con,$_POST['thisID']);
  $username = mysqli_real_escape_string($con,$_POST['username']);
	$password1 = mysqli_real_escape_string($con,$_POST['password1']);
  $password2 = mysqli_real_escape_string($con,$_POST['password2']);
  if (strcmp($password1, $password2) == 0) {
    $sql = mysqli_query($con,"UPDATE admin SET username='$username', password='$password1' WHERE id='$uid'");
    header("location: index.php");
    exit();
  } else {
    echo "Mật khẩu không trùng khớp!";
  }
	// See if that product name is an identical match to another product in the system
	// $sql = mysqli_query($con,"UPDATE admin SET username='$username', password='$password' WHERE id='$uid'");

}
?>
<?php
// Gather this product's full information for inserting automatically into the edit form below on page
if (isset($_GET['userid'])) {
  $userID = $_GET['userid'];
  $sql = mysqli_query($con,"SELECT * FROM admin WHERE id='$userID' LIMIT 1");
  $userCount = mysqli_num_rows($sql);
  if ($userCount > 0) {
    while($row = mysqli_fetch_array($sql)){

     $user = $row["username"];
     $password = $row["password"];
     $lastlogdate = strftime("%b %d, %Y", strtotime($row["last_log_date"]));
    }
  } else {
    echo $userID;
    echo "Tài khoản không tồn tại.";
  exit();
  }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Inventory List</title>
<link rel="stylesheet" href="style/style.css" type="text/css" media="screen" />
</head>

<body>
<div align="center" id="mainWrapper">
  <?php include_once("template_header.php");?>
  <div id="pageContent"><br />
    <div align="right" style="margin-right:32px;"></div>
    <hr />
    <h3>
    &darr; Thông tin cá nhân &darr;
    </h3>
    <form action="admin_info_edit.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
    <table width="90%" border="0" cellspacing="0" cellpadding="6">
      <tr>
        <td width="20%" align="right">Tên người dùng</td>
        <td width="80%"><label>
          <input name="username" type="text" id="username" size="64" value="<?php echo $user; ?>" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Mật khẩu mới</td>
        <td><label>
          <input name="password1" type="password" id="password1" size="12" value="" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Nhập lại mật khẩu</td>
        <td><label>
          <input name="password2" type="password" id="password2" size="12" value="" />
        </label></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><label>
          <input name="thisID" type="hidden" value="<?php echo $userID; ?>" />
          <input type="submit" name="button" id="button" value="Cập nhật thông tin" />
        </label></td>
      </tr>
    </table>
    </form>
    <br />
  <br />
  </div>
  <?php include_once("template_footer.php");?>
</div>
</body>
</html>
