<?php
session_start(); // Start session first thing in script
// if (!isset($_SESSION["user"])) {
//     header("location: user_login.php");
//     exit();
// }
// Script Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
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
?>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 1 (if user attempts to add something to the cart from the product page)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['pid'])) {
    $pid = $_POST['pid'];
	$wasFound = false;
	$i = 0;
	// If the cart session variable is not set or cart array is empty
	if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1) {
	    // RUN IF THE CART IS EMPTY OR NOT SET
      $quantity = 1;
      $item_id = $_POST['pid'];
      $sql = mysqli_query($con,"SELECT * FROM products WHERE id='$item_id' LIMIT 1");
      $row = mysqli_fetch_array($sql);
      $quantity_instore = $row['quantity'];
      if ($quantity > $quantity_instore) {
        $quantity = $quantity_instore;
      }
		$_SESSION["cart_array"] = array(0 => array("item_id" => $pid, "quantity" => $quantity));
	} else {
		// RUN IF THE CART HAS AT LEAST ONE ITEM IN IT
		foreach ($_SESSION["cart_array"] as $each_item) {
		      $i++;
		      while (list($key, $value) = each($each_item)) {
				  if ($key == "item_id" && $value == $pid) {
					  // That item is in cart already so let's adjust its quantity using array_splice()
            $item_id = $each_item['item_id'];
            $quantity_incart = $each_item['quantity'] + 1;
            $sql = mysqli_query($con,"SELECT * FROM products WHERE id='$item_id' LIMIT 1");
            $row = mysqli_fetch_array($sql);
            $quantity_instore = $row['quantity'];
            if ($quantity_incart > $quantity_instore) {
              $quantity_incart = $quantity_instore;
            }
					  array_splice($_SESSION["cart_array"], $i-1, 1, array(array("item_id" => $pid, "quantity" => $quantity_incart)));
					  $wasFound = true;
				  } // close if condition
		      } // close while loop
	       } // close foreach loop
		   if ($wasFound == false) {
         $quantity = 1;
         $item_id = $_POST['pid'];
         $sql = mysqli_query($con,"SELECT * FROM products WHERE id='$item_id' LIMIT 1");
         $row = mysqli_fetch_array($sql);
         $quantity_instore = $row['quantity'];
         if ($quantity > $quantity_instore) {
           $quantity = 0;
         }
			   array_push($_SESSION["cart_array"], array("item_id" => $pid, "quantity" => $quantity));
		   }
	}
	header("location: cart.php");
    exit();
}
?>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 2 (if user chooses to empty their shopping cart)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_GET['cmd']) && $_GET['cmd'] == "emptycart") {
    unset($_SESSION["cart_array"]);
}
?>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 3 (if user chooses to adjust item quantity)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['item_to_adjust']) && $_POST['item_to_adjust'] != "") {
    // execute some code
	$item_to_adjust = $_POST['item_to_adjust'];
	$quantity = $_POST['quantity'];
	$quantity = preg_replace('#[^0-9]#i', '', $quantity); // filter everything but numbers
	if ($quantity >= 100) { $quantity = 99; }
	if ($quantity < 1) { $quantity = 1; }
	if ($quantity == "") { $quantity = 1; }
	$i = 0;
	foreach ($_SESSION["cart_array"] as $each_item) {
		      $i++;
		      while (list($key, $value) = each($each_item)) {
				  if ($key == "item_id" && $value == $item_to_adjust) {
					  // That item is in cart already so let's adjust its quantity using array_splice()
            $item_id = $each_item['item_id'];
            $sql = mysqli_query($con,"SELECT * FROM products WHERE id='$item_id' LIMIT 1");
            $row = mysqli_fetch_array($sql);
            $quantity_instore = $row['quantity'];
            if ($quantity > $quantity_instore) {
              $quantity = $quantity_instore;
            }
					  array_splice($_SESSION["cart_array"], $i-1, 1, array(array("item_id" => $item_to_adjust, "quantity" => $quantity)));
				  } // close if condition
		      } // close while loop
	} // close foreach loop
}
?>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 4 (if user wants to remove an item from cart)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['index_to_remove']) && $_POST['index_to_remove'] != "") {
    // Access the array and run code to remove that array index
 	$key_to_remove = $_POST['index_to_remove'];
	if (count($_SESSION["cart_array"]) <= 1) {
		unset($_SESSION["cart_array"]);
	} else {
		unset($_SESSION["cart_array"]["$key_to_remove"]);
		sort($_SESSION["cart_array"]);
	}
}
?>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 5  (render the cart for the user to view on the page)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$cartOutput = "";
$cartTotal = "";
$cartTotal2 = "";
$pp_checkout_btn = '';
$product_id_array = '';
if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1) {
    $cartOutput = "<h2 align='center'>Giỏ hàng của bạn đang trống!</h2>";
} else {
	// Start PayPal Checkout Button
	$pp_checkout_btn .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_cart">
    <input type="hidden" name="upload" value="1">
    <input type="hidden" name="business" value="you@youremail.com">';
	// Start the For Each loop
	$i = 0;
    foreach ($_SESSION["cart_array"] as $each_item) {
		$item_id = $each_item['item_id'];
		$sql = mysqli_query($con,"SELECT * FROM products WHERE id='$item_id' LIMIT 1");
		while ($row = mysqli_fetch_array($sql)) {
			$product_name = $row["product_name"];
			$price = $row["price"];
			$details = $row["details"];
		}
		$pricetotal = $price * $each_item['quantity'];
		$cartTotal = $pricetotal + $cartTotal;
    $cartTotal2 = $pricetotal + $cartTotal2;
		setlocale(LC_MONETARY, "en_US");
        $pricetotal = money_format("%10.2n", $pricetotal);
		// Dynamic Checkout Btn Assembly
		$x = $i + 1;
		$pp_checkout_btn .= '<input type="hidden" name="item_name_' . $x . '" value="' . $product_name . '">
        <input type="hidden" name="amount_' . $x . '" value="' . $price . '">
        <input type="hidden" name="quantity_' . $x . '" value="' . $each_item['quantity'] . '">  ';
		// Create the product array variable
		$product_id_array .= "$item_id-".$each_item['quantity'].",";
		// Dynamic table row assembly
		$cartOutput .= "<tr>";
		$cartOutput .= '<td><a href="product.php?id=' . $item_id . '">' . $product_name . '</a><br /><img src="inventory_images/' . $item_id . '.jpg" alt="' . $product_name. '" width="100" height="50" border="1" /></td>';
		$cartOutput .= '<td>' . $details . '</td>';
		$cartOutput .= '<td>$' . $price . '</td>';
		$cartOutput .= '<td><form action="cart.php" method="post">
		<input name="quantity" type="text" value="' . $each_item['quantity'] . '" size="1" maxlength="2" />
		<input name="adjustBtn' . $item_id . '" type="submit" value="change" />
		<input name="item_to_adjust" type="hidden" value="' . $item_id . '" />
		</form></td>';
		//$cartOutput .= '<td>' . $each_item['quantity'] . '</td>';
		$cartOutput .= '<td>' . $pricetotal . '</td>';
		$cartOutput .= '<td><form action="cart.php" method="post"><input name="deleteBtn' . $item_id . '" type="submit" value="X" /><input name="index_to_remove" type="hidden" value="' . $i . '" /></form></td>';
		$cartOutput .= '</tr>';
		$i++;
    }
	setlocale(LC_MONETARY, "en_US");
    $cartTotal = money_format("%10.2n", $cartTotal);
	$cartTotal = "<div style='font-size:18px; margin-top:12px;' align='right'>Tổng giỏ hàng : ".$cartTotal." USD</div>";
    // Finish the Paypal Checkout Btn
	$pp_checkout_btn .= '<input type="hidden" name="custom" value="' . $product_id_array . '">
	<input type="hidden" name="notify_url" value="https://www.yoursite.com/storescripts/my_ipn.php">
	<input type="hidden" name="return" value="https://www.yoursite.com/checkout_complete.php">
	<input type="hidden" name="rm" value="2">
	<input type="hidden" name="cbt" value="Return to The Store">
	<input type="hidden" name="cancel_return" value="https://www.yoursite.com/paypal_cancel.php">
	<input type="hidden" name="lc" value="US">
	<input type="hidden" name="currency_code" value="USD">
	<input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif" name="submit" alt="Make payments with PayPal - its fast, free and secure!">
	</form>';
}
?>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Section 6  (checkout the order and clear the cart)
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"]) < 1) {
    $cartOutput = "<h2 align='center'>Giỏ hàng của bạn đang trống!</h2>";
} else if (isset($_GET['cmd']) && $_GET['cmd'] == "checkout") {
  if (!isset($_SESSION["user"])) {
      header("location: user_login.php");
      exit();
  } else {
  $i = 0;
  $product_id_array = "";
  $quantity_array = "";
  foreach ($_SESSION["cart_array"] as $each_item) {
          $product_id = $each_item['item_id'];
          $product_id_array .= $each_item['item_id'];
          $product_quantity = $each_item['quantity'];
          $quantity_array .= $each_item['quantity'];
          $sql = mysqli_query($con,"SELECT * FROM products WHERE id='$item_id' LIMIT 1");
          $row = mysqli_fetch_array($sql);
          $quantity_instore = $row['quantity'];
          $left = $quantity_instore - $product_quantity;
          $product_id .= " ";
          $quantity_array .= " ";
          $sql = mysqli_query($con,"UPDATE products SET quantity='$left' WHERE id='$product_id'");
  }
  $user = $_SESSION["user"];
  $sql = mysqli_query($con,"INSERT INTO transactions (product_id_array, quantity_array, username, payment_amount)
        VALUES('$product_id_array', '$quantity_array', '$user', '$cartTotal2')") or die (mysqli_error($con));
  unset($_SESSION["cart_array"]);
  echo 'Thanh toán thành công! Tiếp tục mua hàng <a href="index.php">tại đây</a>';
  exit();
}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Your Cart</title>
<link rel="stylesheet" href="style/style.css" type="text/css" media="screen" />
</head>
<body>
<div align="center" id="mainWrapper">
  <?php include_once("template_header.php");?>
  <div id="pageContent">
    <div style="margin:24px; text-align:left;">

    <br />
    <table width="100%" border="1" cellspacing="0" cellpadding="6">
      <tr>
        <td width="18%" bgcolor="#C5DFFA"><strong>Sản phẩm</strong></td>
        <td width="45%" bgcolor="#C5DFFA"><strong>Thông tin</strong></td>
        <td width="10%" bgcolor="#C5DFFA"><strong>Giá</strong></td>
        <td width="13%" bgcolor="#C5DFFA"><strong>Số lượng</strong></td>
        <td width="11%" bgcolor="#C5DFFA"><strong>Tổng</strong></td>
        <td width="2%" bgcolor="#C5DFFA"><strong>Xoá</strong></td>
      </tr>
     <?php echo $cartOutput; ?>
     <!-- <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr> -->
    </table>
    <?php echo $cartTotal; ?>
    <br />
<br />
<?php //echo $pp_checkout_btn; ?>
    <br />
    <br />
    <p><a href="cart.php?cmd=checkout">Thanh toán</a>
    <p><a href="cart.php?cmd=emptycart">Xoá giỏ hàng</a>
    <!-- <p><a href="user_logout.php">Đăng xuất</a> -->
    </div>
   <br />
  </div>
  <?php include_once("template_footer.php");?>
</div>
</body>
</html>
