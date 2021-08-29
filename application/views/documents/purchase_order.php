<?php $this->load->view('templates/head_pdf');?>
<body>
<div class="container">
	
	<div class="row">
		<div class="col-md-12 text-center">
			 <h1><?php echo $outlet->name; ?><?php //echo date('Y',strtotime($info['date_issue']))?></h1>
            <h4><?php echo $outlet->address1; ?><br/> <?php echo $outlet->address2; ?></h4>
            <h4>Tel: <?php echo $outlet->contact; ?></h4>
		</div>
		<!--<div class="col-md-12 text-center top10"><h2>Purchase Order #<?php echo $info['po_id']?></h2></div>-->
		<div class="col-md-12">	
			<table class="top50" border="0">				
				 <tr>
					<td class="col-md-8" width="70%">
						<table border="0" class="info_table">
							<tbody>
								<tr>								
									<td class="text-left"><b>Name:&nbsp;&nbsp;</b></td>
									<td class="text-left"><?php if(isset($info['supplier_name'])){ ?>
										<?php echo $info['supplier_name'];?>
									<?php } ?>	</td>								
									
								</tr>
								
								<tr>
									<td class="text-left"><b>Address:&nbsp;&nbsp;</b></td>
									<td class="text-left">
										<?php if(isset($info['supplier_address'])){ ?>
											<?php echo $info['supplier_address']?>
										<?php } ?>									
									</td>
								</tr>
								
								<tr>
									<td class="text-left"><b>Contact Person:&nbsp;&nbsp;</b></td>
									<td class="text-left">
										<?php if(isset($info['supplier_cp'])){ ?>
											<?php echo $info['supplier_cp']?>
										<?php } ?>								
									</td>
								</tr>
								
								<tr>
									<td class="text-left"><b>Phone:&nbsp;&nbsp;</b></td>
									<td class="text-left">
										<?php if(isset($info['supplier_phone'])){ ?>
											<?php echo $info['supplier_phone']?>
										<?php } ?>
							
									</td>
								</tr>
								
								<tr>
									<td class="text-left"><b>Email:&nbsp;&nbsp;</b></td>
									<td class="text-left">
										<?php if(isset($info['supplier_email'])){ ?>
											<?php echo $info['supplier_email']?>
										<?php } ?>							
									</td>
								</tr>
								<tr>
									<td class="text-left"><b>Purchase Order #:</b></td>
									<td class="text-left">&nbsp;&nbsp;<?php echo $info['po_id']?></td>
								</tr>
						    </tbody>
                        </table>						
					</td>
					<td class="col-md-4 text-right" style="float: right" width="30%">
						<table border="0" class="info_table">
							<tbody>																
								<tr>
									<td class="text-left"><b>Date:</b></td>
									<td class="text-left">&nbsp;&nbsp;<?php echo format_date($info['date'])?></td>
								</tr>
								
								<tr>
									<td>&nbsp;&nbsp; </td>
									<td>&nbsp;&nbsp; </td>
								</tr>
								
								<!--<tr>
									<td class="text-left"><b>Address 1:</b></td>
									<td class="text-left">&nbsp;&nbsp;<?php echo $info['ship_address1']?></td>
								</tr>
								
								<tr>
									<td class="text-left"><b>Address 2:</b></td>
									<td class="text-left">&nbsp;&nbsp;<?php echo $info['ship_address2']?></td>
								</tr>
								
								<tr>
									<td class="text-left"><b>Contact:</b></td>
									<td class="text-left">&nbsp;&nbsp;<?php echo $info['ship_contact']?></td> 
								</tr> -->
								
							 </tbody>
						</table>	
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h4 class="top50"><b>Items</b></h4>
			<table class="table table-bordered top10">
				<tr>
					<td><b>Stock Code</b></td>
					<td><b>Description</b></td>
					<td><b>Quantity</b></td>
				</tr>
			<?php foreach($i_items as $item):?>
					<tr>
						<td><?php echo $item->stock_num;?></td>
						<td><?php echo $item->description;?></td>
						<td><?php echo $item->quantity;?></td>
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
		<div class="col-md-12 text-center top50">
			<table>
				<tr>
					<td class="text-left">
						<div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><br/>
						<div>Signature</div><br/>
					</td>
					<!--<td class="text-right">
						<div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo format_date($info['date'])?>&nbsp;&nbsp;&nbsp;</div><br/>
						<div>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
					</td> -->
				</tr>
			</table>
		</div>
	</div>
</div>
</body>
</html>	