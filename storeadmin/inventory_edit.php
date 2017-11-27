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
// Parse the form data and add inventory item to the system
if (isset($_POST['product_name'])) {

	$pid = mysqli_real_escape_string($con,$_POST['thisID']);
    $product_name = mysqli_real_escape_string($con,$_POST['product_name']);
	$price = mysqli_real_escape_string($con,$_POST['price']);
  $quantity = mysqli_real_escape_string($con,$_POST['quantity']);
	$category = mysqli_real_escape_string($con,$_POST['category']);
	$origin = mysqli_real_escape_string($con,$_POST['origin']);
	$details = mysqli_real_escape_string($con,$_POST['details']);
	// See if that product name is an identical match to another product in the system
	$sql = mysqli_query($con,"UPDATE products SET product_name='$product_name', price='$price', quantity='$quantity', details='$details', category='$category', origin='$origin' WHERE id='$pid'");
	if ($_FILES['image']['tmp_name'] != "") {
	    // Place image in the folder
	    $newname = "$pid.jpg";
	    move_uploaded_file($_FILES['image']['tmp_name'], "../inventory_images/$newname");
	}
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
	$product_list = "You have no products listed in your store yet";
}
?>
<?php
// Gather this product's full information for inserting automatically into the edit form below on page
if (isset($_GET['pid'])) {
	$targetID = $_GET['pid'];
    $sql = mysqli_query($con,"SELECT * FROM products WHERE id='$targetID' LIMIT 1");
    $productCount = mysqli_num_rows($sql); // count the output amount
    if ($productCount > 0) {
	    while($row = mysqli_fetch_array($sql)){

			 $product_name = $row["product_name"];
			 $price = $row["price"];
       $quantity = $row["quantity"];
			 $category = $row["category"];
			 $origin = $row["origin"];
			 $details = $row["details"];
			 $date_added = strftime("%b %d, %Y", strtotime($row["date_added"]));
        }
    } else {
	    echo "Sản phẩm không tồn tại";
		exit();
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Inventory List</title>
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
    &darr; Thông tin sản phẩm &darr;
    </h3>
    <form action="inventory_edit.php" enctype="multipart/form-data" name="myForm" id="myform" method="post">
    <table width="90%" border="0" cellspacing="0" cellpadding="6">
      <tr>
        <td width="20%" align="right">Tên sản phẩm</td>
        <td width="80%"><label>
          <input name="product_name" type="text" id="product_name" size="64" value="<?php echo $product_name; ?>" />
        </label></td>
      </tr>
      <tr>
        <td align="right">Giá bán</td>
        <td><label>
          $
          <input name="price" type="text" id="price" size="12" value="<?php echo $price; ?>" />
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
          <option value="<?php echo $origin; ?>"><?php echo $origin; ?></option>
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
          <input name="quantity" type="number" id="quantity" size="12" min="0" max="99" value="<?php echo $quantity; ?>"/>
        </label></td>
      </tr>
      <tr>
        <td align="right">Thông tin chi tiết</td>
        <td><label>
          <textarea name="details" id="details" cols="64" rows="5"><?php echo $details; ?></textarea>
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
          <input name="thisID" type="hidden" value="<?php echo $targetID; ?>" />
          <input type="submit" name="button" id="button" value="Cập nhật thông tin" />
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
