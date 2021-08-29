<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<!-- Title and other stuffs -->
	<title><?php echo isset($itemDetails->title) ? 'ShopCast :: '.$itemDetails->title : ''?></title>
</head>
<body>
<img src="<?php echo base_url().'inventory/genBarCode/'.$itemDetails->item_id ?>"/>
<table>
<tr>
	<td>Description</td>
	<td><b><?php echo $itemDetails->description ?></b></td>
</tr>
<tr>
	<td>Stock num</td>
	<td><b><?php echo $itemDetails->stock_num ?></b></td>
</tr>
<tr>
	<td>Price</td>
	<td><b><?php echo format_price($itemDetails->cost_price) ?></b></td>
</tr>
</table>
</body>
</html>