<html>
	<head>
		<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>css/bootstrap.min.css" >
		<meta charset="UTF-8">
	</head>
	<body>
	<?php  if(isset($cus_invoices[0])) { ?>
		<h1><?php echo $cus_invoices[0]->name ?></h1>
		<h2><?php echo $cus_invoices[0]->address ?></h2>
		<h4>Sales</h4>
	
		<table class="table table-condensed">
			<tr>
				<td class="span2"><b>#</b></td>
				<td class="span2"><b>Item Description</b></td>
				<td class="span2"><b>Total Quantity</b></td>
				<td class="span2"><b>Total Payment</b></td>
			</tr>

			<?php foreach ($cus_invoices as $key => $invoice) { ?>
				<tr>
					<td><?php echo $key+1; ?></td>
					<td><?php echo $invoice->description; ?></td>
					<td><?php echo $invoice->totalqty; ?></td>
					<td><?php echo format_price($invoice->totalprice); ?></td>
				</tr>
			<?php	} ?>

		</table>
	<?php 	} else { ?>
		<h2>There is no content for this customer</h2>
	<?php  } ?>

	</body>
</html>