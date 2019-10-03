<?php $this->load->view('templates/head_pdf');?>
<body>
<div class="container">
	
	<div class="row">
		<div class="col-md-12 text-center">			
			<h1><?php echo $info['host_outlet']?></h1>
			<h4><?php echo $info['host_address1'].' '.$info['host_address2']?></h4>
			<h4>Tel: <?php echo $info['host_contact']; ?></h4>
		</div>
		
		<div class="col-md-12">			
			<table class="top50">
				<tr>
					<td><b>To:</b></td>
				</tr>
				<tr>
					<td class="col-md-8">
						 <table border="0" class="info_table">
                                <tbody>
								 <tr>
                                     <td><b><?php echo $info['supplier_name'] ?></b></td>
                                </tr>
								<tr>                                    
                                    <td><b><?php echo $info['supplier_address'] ?></b></td>
                                </tr>
								<tr>                                    
                                    <td><b><?php echo $info['supplier_cp'] ?></b></td>
                                </tr>
								<tr>                                    
                                    <td><b><?php echo $info['supplier_phone'] ?></b></td>
                                </tr>
								<tr>                                    
                                    <td><b><?php echo $info['supplier_email'] ?></b></td>
                                </tr>
							</tbody>
						</table>					
					</td>
					<td class="col-md-4 text-right" style="float: right">
                        <table border="0" class="info_table">
                        <tbody>		
							<tr>
								<td class="text-left"><b>Debit Notes No&nbsp;&nbsp;&nbsp;:&nbsp;</b></td>
								<td class="text-left"><?php echo $info['rpi_id']; ?></td>
							</tr>
							 <tr>
								<td class="text-left"><b>Ref Invoice No&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;</b></td>
								<td class="text-left"><?php echo $info['invoice_num']; ?></td>
							</tr>
							<tr>
								<td class="text-left"><b>Invoicing Date&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;</b></td>
								<td class="text-left"><?php echo format_date($info['issue_date']); ?></td>
							</tr>
							
						</tbody>
						</table>	
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
					<td><b>Barcode</b></td>
					<td><b>Brand</b></td>
					<td><b>Category</b></td>
					<td><b>Description</b></td>
					<td><b>Model</b></td>					
					<td><b>Cost</b></td>
					<td><b>Quantity</b></td>
					<td><b>Total Cost</b></td>
				</tr>
			<?php foreach($i_items as $item):?>
					<tr>
						<td><?php echo $item->stock_num;?></td>
						<td><?php echo $item->barcode;?></td>
						<td><?php echo $item->brand;?></td>
						<td><?php echo $item->category;?></td>
						<td><?php echo $item->description;?></td>
						<td><?php echo $item->model_no;?></td>						
						<td><?php echo format_price($item->cost);?></td>
						<td class="text-center"><?php echo $item->quantity;?></td>
						<td><?php echo format_price($item->cost*$item->quantity);?></td>
					</tr>
			<?php endforeach;?>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 text-right"><b>Total:</b> <?php echo format_price($info['subtotal'],$info['currency'])?></div>
	</div>

	<div class="row">
            <div class="col-md-12 text-center top50">
                <table>
                    <tr>
                        <td class="text-left" width="75%">
                            <div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                            <br/>

                            <div style="text-align:center;"><?php echo $info['host_outlet']; ?></div>
                            <br/>
                        </td>
                        <td class="text-center">
                            <div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                            <br/>

                            <div style="text-align:center;width:100%;display:block;">Received by</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
</div>
</body>
</html>