<html>
	<head>
		<link type="text/css" rel="stylesheet" href="<?php echo base_url()?>css/bootstrap.min.css" >
		<meta charset="UTF-8">
	</head>
	<body>
	<h1><?php echo $cyear; ?></h1>
	<h4><?php echo lang('profit_loss')?></h4>
	<table class="table table-condensed">
		<tr>
			<td class="span2"><b><?php echo lang('month')?></b></td>
			<td class="span2"><b><?php echo lang('one_time_exenses')?></b></td>
			<td class="span2"><b><?php echo lang('recurring_exenses')?></b></td>
			<td class="span2"><b><?php echo lang('stock')?></b></td>
			<td class="span2"><b><?php echo lang('other')?></b></td>
			<td class="span2"><b><?php echo lang('total_expenses')?></b></td>
			<td class="span2"><b><?php echo lang('invoices')?></b></td>
			<td class="span2"><b><?php echo lang('total_incomes')?></b></td>
			<td class="span2"><b><?php echo lang('dash_total')?></b></td>
		</tr>
		<?php for($i=1; $i<=12; $i++) { ?>

		<tr>
			<td><?php echo $months[$i]; ?></td>
			<td><?php echo format_price($one_time[$i]) ?></td>
			<td><?php echo format_price($recurring[$i]) ?></td>
			<td><?php echo format_price($stock[$i]) ?></td>
			<td><?php echo format_price($other[$i]) ?></td>
			<td><?php echo format_price($one_time[$i]+$recurring[$i]+$stock[$i]+$other[$i]) ?></td>
			<td><?php echo format_price($invoices[$i]) ?></td>
			<td><?php echo format_price($invoices[$i]) ?></td>
			<td><?php echo format_price($total_m[$i]) ?></td>
		</tr>

		<?php	} ?>
	</table>

	</body>
</html>