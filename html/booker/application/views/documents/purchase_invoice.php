<?php $this->load->view('templates/head_pdf');?>
<body>
<div class="container">
	
	<div class="row">
		<div class="col-md-12 text-center">
			 <h1><?php echo $outlet->name; ?><?php //echo date('Y',strtotime($info['date_issue']))?></h1>
            <h4><?php echo $outlet->address1; ?><br/> <?php echo $outlet->address2; ?></h4>
            <h4>Tel: <?php echo $outlet->contact; ?></h4>
		</div>
		<div class="col-md-12 text-center top10"><h2>Purchase Invoice #<?php echo $info['pi_id']?></h2></div>
		<div class="col-md-12">
			<table class="top50">
				<tr>
					<td width="500">
						<h4>Vendor:</h4>
						<div>Name:&nbsp;<b><?php echo $info['supplier_name']?></b></div>
						<div>Address:&nbsp;<b><?php echo $info['supplier_address']?></b></div>
						<div>Contact Person:&nbsp;<b><?php echo $info['supplier_cp']?></b></div>
						<div>Phone:&nbsp;<b><?php echo $info['supplier_phone']?></b></div>
						<div>Email:&nbsp;<b><?php echo $info['supplier_email']?></b></div>
					</td>
					<td>
						<div>Invoice No.:&nbsp;<b><?php echo $info['invoice_num']?></b></div>
						<div>Receiving Date:&nbsp;<b><?php echo format_date($info['date'])?></b></div>
						<div>Invoicing Date:&nbsp;<b><?php echo format_date($info['issue_date'])?></b></div>
						<div>Due Date:&nbsp;<b><?php echo format_date($info['payment_date'])?></b></div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h4 class="top50">Items</h4>
			<table class="table table-bordered top10">
				<tr>
					<td><b>Stock Code</b></td>
					<td><b>Description</b></td>
					<td><b>Cost</b></td>
					<td><b>Quantity</b></td>
					<td><b>Total Cost</b></td>
				</tr>
			<?php foreach($i_items as $item):?>
					<tr>
						<td><?php echo $item->stock_num;?></td>
						<td><?php echo $item->description;?></td>
						<td><?php echo $item->cost;?></td>
						<td><?php echo $item->quantity;?></td>
						<td><?php echo $item->cost*$item->quantity;?></td>
					</tr>
			<?php endforeach;?>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6"><b>Total:</b> <?php echo format_price($info['subtotal'])?></div>
	</div>
	<div class="row">
		<div class="col-md-6"><b>Landed cost price:</b> <?php echo format_price($info['landed_cost'])?></div>
	</div>
<?php if($info['remark']) { ?>
	<div class="row">
		<div class="col-md-6"><b>Remark:</b> <?php echo $info['remark']?></div>
	</div>
<?php } ?>

	<div class="row">
		<div class="col-md-12 text-center top50">
			<table>
				<tr>
					<td class="text-center">
						<div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><br/>
						<div>Signature</div><br/>
					</td>
					<td class="text-center">
						<div class="underlined">&nbsp;&nbsp;&nbsp;<?php echo format_date($info['date'])?>&nbsp;&nbsp;&nbsp;</div><br/>
						<div>Date</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
</body>
</html>