<?php

session_start();
if (isset($_SESSION["user"])) {
    header("location: index.php");
    exit();
}
?>
<?php
// Parse the log in form if the user has filled it out and pressed "Log In"
$db_host = "localhost";
// Place the username for the MySQL database here
$db_username = "sontest";
// Place the password for the MySQL database here
$db_pass = "123";
// Place the name for the MySQL database here
$db_name = "son";
$con = mysqli_connect("$db_host","$db_username","$db_pass","$db_name");

if (isset($_POST["username"]) && isset($_POST["password"])) {

	  $user = preg_replace('#[^A-Za-z0-9]#i', '', $_POST["username"]); // filter everything but numbers and letters
    $password = preg_replace('#[^A-Za-z0-9]#i', '', $_POST["password"]); // filter everything but numbers and letters
    // Connect to the MySQL database
    include "storescripts/connect_to_mysql.php";
    $sql = mysqli_query($con,"SELECT id FROM admin WHERE username='$user' AND password='$password' LIMIT 1"); // query the person
    // ------- MAKE SURE PERSON EXISTS IN DATABASE ---------
    $existCount = mysqli_num_rows($sql); // count the row nums
    if ($existCount == 1) { // evaluate the count
	     while($row = mysqli_fetch_array($sql)){
             $id = $row["id"];
		 }
		 $_SESSION["id"] = $id;
		 $_SESSION["user"] = $user;
		 $_SESSION["password"] = $password;
		 header("location: cart.php");
         exit();
    } else {
		echo 'Thông tin tài khoản hoặc mật khẩu không đúng! Thử lại <a href="user_login.php">tại đây</a>';
		exit();
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>User Log In </title>
<link rel="stylesheet" href="style/style.css" type="text/css" media="screen" />
</head>

<body>
<div align="center" id="mainWrapper">
  <?php include_once("template_header.php");?>
  <div id="pageContent"><br />
    <div align="left" style="margin-left:24px;">
      <h2>Vui lòng đăng nhập!</h2>
      <form id="form1" name="form1" method="post" action="user_login.php">
        Tên người dùng:<br />
          <input name="username" type="text" id="username" size="40" />
        <br /><br />
        Mật khẩu:<br />
       <input name="password" type="password" id="password" size="40" />
       <br />
       <br />
       <br />

         <input type="submit" name="button" id="button" value="Đăng nhập" />

      </form>
      <p>&nbsp; </p>
    </div>
    <br />
  <br />
  <br />
  </div>
  <?php include_once("template_footer.php");?>
</div>
</body>
</html>
