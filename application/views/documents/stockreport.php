<html>
	<head>
		<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>css/bootstrap.min.css" >
		<meta charset="UTF-8">
	</head>
	<body>
	<h1><?php echo $stockreport[0]->name ?></h1>
	<h2><?php echo $stockreport[0]->location ?></h2>
	<h4>Inventory Control</h4>
	<table class="table table-condensed">
		<tr>
			<td class="span2"><b>#</b></td>
			<td class="span2"><b>Item Description</b></td>
			<td class="span2"><b>Quantity</b></td>
			<td class="span2"><b>Cost Price</b></td>
			<td class="span2"><b>Sell Price</b></td>
		</tr>
		<?php foreach ($stockreport as $key => $invoice) { ?>
			<tr>
				<td><?php echo $key+1; ?></td>
				<td><?php echo $invoice->description; ?></td>
				<td><?php echo $invoice->qty; ?></td>
				<td><?php echo format_price($invoice->cost_price); ?></td>
				<td><?php echo format_price($invoice->sell_price); ?></td>
			</tr>
		<?php	} ?>

	</table>

	</body>
</html>