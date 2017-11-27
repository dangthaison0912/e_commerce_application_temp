<?php
// Script Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<?php
// Connect to the MySQL database
include "storescripts/connect_to_mysql.php";
$dynamicList = "";
$db_host = "localhost";
// Place the username for the MySQL database here
$db_username = "sontest";
// Place the password for the MySQL database here
$db_pass = "123";
// Place the name for the MySQL database here
$db_name = "son";
$con = mysqli_connect("$db_host","$db_username","$db_pass","$db_name");

$sql = mysqli_query($con,"SELECT * FROM products ORDER BY date_added");
$productCount = mysqli_num_rows($sql); // count the output amount
$horizontalList = "";
if ($productCount > 0) {
	while($row = mysqli_fetch_array($sql)){
             $id = $row["id"];
			 $product_name = $row["product_name"];
			 $price = $row["price"];
			 $quantity = $row["quantity"];
			 $date_added = strftime("%b %d, %Y", strtotime($row["date_added"]));
			 $dynamicList .= '<table width="100%" border="0" cellspacing="0" cellpadding="6">
        <tr>
          <td width="25%" valign="top"><a href="product.php?id=' . $id . '"><img style="border:#666 1px solid;" src="inventory_images/' . $id . '.jpg" alt="' . $product_name . '" width="260" height="130" border="1" /></a></td>
          <td width="75%" valign="top">' . $product_name . '<br />
            $' . $price . '<br /> Còn ' . $quantity . ' chiếc <br />
            <a href="product.php?id=' . $id . '">Xem chi tiết</a></td>

        </tr>
      	</table>';
    }
} else {
	$dynamicList = "Hiện tại cửa hàng không bán gì";
}

mysqli_close($con);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Store Home Page</title>
<link rel="stylesheet" href="style/style.css" type="text/css" media="screen" />
</head>
<body>
<div align="center" id="mainWrapper">
  <?php include_once("template_header.php");?>
  <div id="pageContent">
  <table width="100%" border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td width="100%" valign="top"><h3> <center>Những mẫu xe mới nhất </center></h3>
      <p>
				<ul id="menu">
				<?php echo $dynamicList; ?><br />
				</ul>
        </p>
      <p><br />
      </p></td>
  </tr>
</table>

  </div>
  <?php include_once("template_footer.php");?>
</div>
</body>
</html>
