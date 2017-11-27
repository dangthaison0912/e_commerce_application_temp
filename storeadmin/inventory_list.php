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
// Delete Item Question to Admin, and Delete Product if they choose
if (isset($_GET['deleteid'])) {
	echo 'Bạn có chắc muốn xoá sản phẩm với ID ' . $_GET['deleteid'] . '? <a href="inventory_list.php?yesdelete=' . $_GET['deleteid'] . '">Có</a> | <a href="inventory_list.php">Không</a>';
	exit();
}
if (isset($_GET['yesdelete'])) {
	// remove item from system and delete its picture
	// delete from database
	$id_to_delete = $_GET['yesdelete'];
	$sql = mysqli_query($con,"DELETE FROM products WHERE id='$id_to_delete' LIMIT 1") or die (mysql_error());
	// unlink the image from server
	// Remove The Pic -------------------------------------------
    $pictodelete = ("../inventory_images/$id_to_delete.jpg");
    if (file_exists($pictodelete)) {
       		    unlink($pictodelete);
    }
	header("location: inventory_list.php");
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
	header("location: inventory_list.php");
  exit();
}
?>
<?php
// This block grabs the whole list for viewing
$product_list = "";
$sql = mysqli_query($con,"SELECT * FROM products ORDER BY date_added DESC");
$productCount = mysqli_num_rows($sql); // count the output amount
if ($productCount > 0) {
	while($row = mysqli_fetch_array($sql)){
             $id = $row["id"];
			 $product_name = $row["product_name"];
			 $price = $row["price"];
			 $date_added = strftime("%b %d, %Y", strtotime($row["date_added"]));
			 $product_list .= "Product ID: $id - <strong>$product_name</strong> - $$price - <em>Added $date_added</em> &nbsp; &nbsp; &nbsp; <a href='inventory_edit.php?pid=$id'>Chỉnh sửa</a> &bull; <a href='inventory_list.php?deleteid=$id'>Xoá</a><br />";
    }
} else {
	$product_list = "Cửa hàng hiện tại ko có sản phẩm nào!";
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
    <div align="right" style="margin-right:32px;"><a href="inventory_list.php#inventoryForm">+ Thêm sản phẩm</a></div>
<div align="left" style="margin-left:24px;">
      <h2>Hàng đang bày bán</h2>
      <?php echo $product_list; ?>
    </div>
    <hr />
    <a name="inventoryForm" id="inventoryForm"></a>
    <h3>
    &darr; Đơn thêm sản phẩm mới &darr;
    </h3>
    <form action="inventory_list.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
    <table width="90%" border="0" cellspacing="0" cellpadding="6">
      <tr>
        <td width="20%" align="right">Tên sản phẩm</td>
        <td width="80%"><label>
          <input name="product_name" type="text" id="product_name" size="64" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Giá bán</td>
        <td><label>
          $
          <input name="price" type="text" id="price" size="12" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Danh mục</td>
        <td><label>
          <select name="category" id="category">
          <option value="Car">Car</option>
          </select>
        </label></td>
      </tr>
      <tr>
        <td align="right">Xuất xứ</td>
        <td><select name="origin" id="origin">
          <option value="Europe">Europe</option>
          <option value="Asia">Asia</option>
          <option value="Africa">Africa</option>
          <option value="America">America</option>
          <option value="Oceania">Oceania</option>
          </select></td>
      </tr>
      <tr>
        <td align="right">Số lượng</td>
        <td><label>
          <input name="quantity" type="number" id="quantity" size="12" min="0" max="99" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Thông tin chi tiết</td>
        <td><label>
          <textarea name="details" id="details" cols="64" rows="5"></textarea>
        </label></td>
      </tr>
      <tr>
        <td align="right">Hình ảnh minh hoạ</td>
        <td><label>
          <input type="file" name="image" id="image" />
        </label></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><label>
          <input type="submit" name="button" id="button" value="Cập nhật sản phẩm" />
        </label></td>
      </tr>
    </table>
    </form>
    <br />
  <br />
  </div>
  <?php include_once("../template_footer.php");?>
</div>
</body>
</html>
