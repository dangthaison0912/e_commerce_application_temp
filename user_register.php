<?php
session_start();
if (isset($_SESSION["user"])) {
    echo 'Vui lòng đăng xuất để đăng ký tài khoản! <a href="user_logout.php">Đăng xuất</a>';
    exit();
}
?>
<?php
include "storescripts/connect_to_mysql.php";
$db_host = "localhost";
// Place the username for the MySQL database here
$db_username = "sontest";
// Place the password for the MySQL database here
$db_pass = "123";
// Place the name for the MySQL database here
$db_name = "son";
$con = mysqli_connect("$db_host","$db_username","$db_pass","$db_name");

if (isset($_POST['username'])) {

  $user = mysqli_real_escape_string($con,$_POST['username']);
	$password1 = mysqli_real_escape_string($con,$_POST['password1']);
	$password2 = mysqli_real_escape_string($con,$_POST['password2']);
	// See if that user name is an identical match to another product in the system
	$sql = mysqli_query($con,"SELECT id FROM admin WHERE username='$user' LIMIT 1");
	$productMatch = mysqli_num_rows($sql); // count the output amount
  if ($productMatch > 0) {
		echo 'Tên đăng nhập đã được sử dụng, <a href="user_register.php">bấm vào đây</a>';
		exit();
	}
  if (strcmp($password1, $password2) !== 0) {
    echo 'Mật khẩu không trùng khớp! <a href="user_register.php">bấm vào đây</a>';
    exit();
  }
	// Add this product into the database now
	$sql = mysqli_query($con,"INSERT INTO admin (username, password, admin, last_log_date)
        VALUES('$user', '$password1', '0', now())") or die (mysqli_error($con));

	header("location: index.php");
  exit();
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
    &darr; Tạo tài khoản mới &darr;
    </h3>
    <form action="user_register.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
    <table width="90%" border="0" cellspacing="0" cellpadding="6">
      <tr>
        <td width="20%" align="right">Tên đăng nhập</td>
        <td width="80%"><label>
          <input name="username" type="text" id="username" size="64" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Mật khẩu</td>
        <td><label>
          <input name="password1" type="password" id="password1" size="12" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Nhập lại mật khẩu</td>
        <td><label>
          <input name="password2" type="password" id="password2" size="12">
        </label></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><label>
          <input type="submit" name="button" id="button" value="Đăng ký" />
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
