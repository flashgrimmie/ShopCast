<?php
define('BASEPATH', "");
include "../../application/config/database.php";
$con = mysqli_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password'],$db['default']['database']);

if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_errno());
} 

if ($_GET['doitems_id']) {
	$sql="delete FROM `do_items` where do_id = " . $_GET['do_id'];
	$result=mysqli_query($con,$sql);
}

if ($_GET['do_id']) {
	$sql="delete FROM `delivery_orders` where do_id = " . $_GET['do_id'];
	$result=mysqli_query($con,$sql);
}

if ($_GET['csitems_id']) {
	$sql="delete FROM `cs_items` where cs_id = " . $_GET['cs_id'];
	$result=mysqli_query($con,$sql);
}

if ($_GET['cs_id']) {
	$sql="delete FROM `cash_sales` where cs_id = " . $_GET['cs_id'];
	$result=mysqli_query($con,$sql);
}

if ($_GET['invoiceitems_id']) {
	$sql="delete FROM `i_items` where invoice_id = " . $_GET['invoice_id'];
	$result=mysqli_query($con,$sql);
}

if ($_GET['invoice_id']) {
	$sql="delete FROM `invoices` where invoice_id = " . $_GET['invoice_id'];
	$result=mysqli_query($con,$sql);
}

if ($_GET['top5invoice']) {
	$sql = "delete from `invoices` where invoice_id = (select invoice_id from (select invoice_id from `invoices` order by invoice_id limit 5,1) as t)";
	$result=mysqli_query($con,$sql);	
}

if ($_GET['randominvoice']) {
	$sql = "delete from `invoices` where invoice_id = (select invoice_id from (select invoice_id from `invoices` order by invoice_id limit " . rand(1,20) . ",1) as t)";
	$result=mysqli_query($con,$sql);	
}

if ($_GET['top5cs']) {
	$sql = "delete from `cash_sales` where cs_id = (select cs_id from (select cs_id from `cash_sales` order by cs_id limit 5,1) as t)";
	$result=mysqli_query($con,$sql);	
}

if ($_GET['randomcs']) {
	$sql = "delete from `cash_sales` where cs_id = (select cs_id from (select cs_id from `cash_sales` order by cs_id limit " . rand(1,20) . ",1) as t)";
	$result=mysqli_query($con,$sql);	
}
?>