<html>
	<head>
		<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>css/bootstrap.min.css" >
		<meta charset="UTF-8">
	</head>
	<body>
	<h1><?php echo $dealer->name; ?></h1>
	<h4>Dealer Report</h4>
	<table class="table table-condensed">
		<tr>
			<td class="span2"><b>Invoice ID</b></td>
			<td class="span2"><b>Name</b></td>
			<td class="span3"><b>Address</b></td>
			<td class="span2"><b>Phone</b></td>
			<td class="span2"><b>Date Issue</b></td>
			<td class="span2"><b>Date Payment</b></td>
			<td class="span2"><b>Status</b></td>
			<td class="span2"><b>Total</b></td>
		</tr>
		<?php  foreach ($invoices as $key => $invoice) { ?>
		<tr>
			<td><?php echo $invoice->invoice_id; ?></td>
			<td><?php echo $invoice->name ?></td>
			<td><?php echo $invoice->address ?></td>
			<td><?php echo $invoice->phone ?></td>
			<td><?php echo $invoice->date_issue ?></td>
			<td><?php echo $invoice->date_payment ?></td>
			<td><?php echo lang($invoice->status) ?></td>
			<td><?php echo format_price($invoice->total) ?></td>
		</tr>
		<?php } ?>
	</table>

	</body>
</html>