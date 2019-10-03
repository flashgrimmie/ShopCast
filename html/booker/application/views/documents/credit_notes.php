<?php $this->load->view('templates/head_pdf');?>
<body>
<div class="container">
	
	<div class="row">
		<div class="col-md-12 text-center">
			<h1>MGK Autoparts services <?php echo date('Y',strtotime($info['date_issue']))?></h1>
			<h4>NO. 10-11, BLOCK E.<br/> GROUND FLOOR, KG JUNJUNGAN<br/> BH3123, NEGARA BRUNEI DARUSSALAM</h4>
			<h4>TEL: 2640115/116 FAX: 2640117</h4>
		</div>
		<div class="col-md-12">
		
			<table class="top50">
				<tr>
					<td width="550">
						<div><b><?php echo $info['customer_name']?></b></div>
						<div><b><?php echo $info['customer_address']?></b></div>
						<div>&nbsp;</div>
						<div>TEL: <?php echo $info['customer_phone']?></div>
						<div>FAX: <?php echo $info['customer_fax']?></div>
					</td>
					<td>
						<div><h3><b>Credit Note</b></h3></div>
						<div>NO: #<?php echo $info['cn_no']?></div>
						<div>DATE: <?php echo format_date($info['date_issue'])?></div>
						
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<h4 class="top50">Items</h4>
			<table class="table  top10" border="0">
				<tr>
					<td><b>Inv no.</b></td>
					<td><b>S.Code</b></td>
					<td width="180"><b>Description</b></td>
					<td align="center"><b>Unit</b></td>
					<td align="center"><b>Quantity</b></td>
					<td align="center"><b>Discount</b></td>
					<!--<td><b>Markup($)</b></td>-->
					<td align="center"><b>Total Cost</b></td>
					<!--<td><b>Note</b></td>-->
				</tr>
			<?php
			$sub_total=0;
			$final_total=0;
			 foreach($i_items as $item):
			
			?>
					<tr>
						<td><?php echo $info['cn_no']?></td>
						<td align="left"><?php echo $item->stock_num;?></td>
						<td align="left" width="180"><?php echo $item->description;?></td>
						<td align="center"><?php echo format_price($item->price);?></td>
						<td align="center"><?php echo $item->quantity;?></td>
						<?php
							$desc_arr=explode("-",$item->discount);
							$desc=$desc_arr[0];
							$sub_total= $sub_total +($item->price*$item->quantity);
							$total=(($item->price*(1-$desc/100)-$item->discount_value)*$item->quantity);
							if(sizeof($desc_arr) >1 && $desc_arr[1]!='')
							{
								$desc=$desc_arr[1];
								$total=($total*(1-$desc/100));
								
							}
							$final_total=$final_total+$total;
						?>
						
						<?php 
							$desc_arr=explode("-",$item->discount);
							$descount_amount1=$desc_arr[0];
							$descount1=($item->price*$item->quantity)-$total;
						?>
						<td align="center"><?php echo $descount1?></td>
						
						
						<td align="center"><?php echo format_price($total);?></td>
					
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
		<div class="text-right">Subtotal:&nbsp;<b><?php echo format_price($sub_total)?></b></div>
		<div class="text-right">Discount:&nbsp;<b><?php echo $info['discount']?>%</b></div>
		<div class="text-right">Total:&nbsp;<b><?php echo format_price($final_total)?></b></div>
		<!--<div class="text-right">Deposit:&nbsp;<b><?php echo format_price($info['deposit'])?></b></div>
		<?php foreach($mechanics as $key=>$value): if(!$key && !$value) continue;?>
			<div class="text-right"><?php echo $key?>:&nbsp;<b><?php echo $value ? format_price($value) : ''?></b></div>
		<?php endforeach;?>
		<div class="text-right">Total Due:&nbsp;<b><?php echo format_price($info['total']+$info['mech_total']-$info['deposit']-$info['partial'])?></b></div>-->
	</div>
	<div class="row">
		<div class="col-md-12 text-center top50">
			<table>
				<tr>
					<td class="text-left" width="75%">
						<div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><br/>
						<div style="text-align:center;">MGK Autoparts services</div><br/>
					</td>
					<td class="text-center">
						<div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><br/>
						<div style="text-align:center;width:100%;display:block;">Received by</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
</body>
</html>