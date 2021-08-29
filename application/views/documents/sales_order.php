<?php $this->load->view('templates/head_pdf');?>
<body>
<div class="container">
	
	<div class="row">
        <div class="col-md-12 text-center">
            <h1><?php echo $outlet->name; ?><?php //echo date('Y',strtotime($info['date_issue']))?></h1>
            <h4><?php echo $outlet->address1; ?><br/> <?php echo $outlet->address2; ?></h4>
            <h4>Tel: <?php echo $outlet->contact; ?></h4>
        </div>
		<div class="col-md-12 text-center top10"><h2>Sales Order #<?php echo $info['so_id']?></h2></div>
		<div class="col-md-12">
			<table class="top50">
				<tr>
					<td width="500">
						<h4>Vendor:</h4>
						<div>Name:&nbsp;<b><?php echo $info['host_outlet']?></b></div>
						<div>Address 1:&nbsp;<b><?php echo $info['host_address1']?></b></div>
						<div>Address 2:&nbsp;<b><?php echo $info['host_address2']?></b></div>
						<div>Phone:&nbsp;<b><?php echo $info['host_contact']?></b></div>
					</td>
					<td>
						<h4>Customer:</h4>
						<div>Name:&nbsp;<b><?php echo $info['customer_name']?></b></div>
						<div>Address:&nbsp;<b><?php echo $info['customer_address']?></b></div>
						<div>Email:&nbsp;<b><?php echo $info['customer_email']?></b></div>
						<div>Phone:&nbsp;<b><?php echo $info['customer_phone']?></b></div>
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
					<td><b>Price</b></td>
					<td><b>Quantity</b></td>
					<td><b>Discount(%)</b></td>
					<td><b>Markup($)</b></td>
					<td><b>Total Cost</b></td>
					<td><b>Note</b></td>
				</tr>
			<?php foreach($i_items as $item):?>
					<tr>
						<td><?php echo $item['stock_num'];?></td>
						<td><?php echo $item['description'];?></td>
						<td><?php echo format_price($item['price']);?></td>
						<td><?php echo $item['quantity'];?></td>
						<td><?php echo $item['discount'];?></td>
						<td><?php echo $item['markup'];?></td>
						<td><?php echo format_price(($item['price']*(1-$item['discount']/100)-$item['discount_value']+$item['markup'])*$item['quantity']);?></td>
						<td><?php echo $item['returned']=='Y' ? 'returned' : ''?></td>
					</tr>
			<?php endforeach;?>
			</table>
		</div>
	</div>

<?php if($info['remark']) { ?>
	<div class="row">
		<div class="col-md-6"><b>Remark:</b> <?php echo $info['remark']?></div>
	</div>
<?php } ?>
<div class="row">
	<div class="col-md-12 text-center top10">
		<div class="text-right">Subtotal:&nbsp;<b><?php echo format_price($info['subtotal'])?></b></div>
		<div class="text-right">Discount:&nbsp;<b><?php echo $info['discount']?>%</b></div>
		<div class="text-right">Total:&nbsp;<b><?php echo format_price($info['total'])?></b></div>
		<div class="text-right">Deposit:&nbsp;<b><?php echo format_price($info['deposit'])?></b></div>
		<?php foreach($mechanics as $key=>$value): if(!$key && !$value) continue;?>
			<div class="text-right"><?php echo $key?>:&nbsp;<b><?php echo format_price($value)?></b></div>
		<?php endforeach;?>
		<div class="text-right">Total Due:&nbsp;<b><?php echo format_price($info['total']+$info['mech_total']-$info['deposit'])?></b></div>
	</div>
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