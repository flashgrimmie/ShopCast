<?php $this->load->view('templates/head_pdf');?>
<style type="text/css">
	body{
	font-size: 13px;
	line-height: 24px;
	color: #000;
	border-top: 0px solid #eee;
	background:#474F57;  
	padding-top: 43px;
  font-family:'Open Sans',sans-serif;
	-webkit-font-smoothing: antialiased;
}
	.top10 tr th{
		border-bottom:1px solid #000;
	}
</style>
<body>

<div class="container">
	
	<div class="row">
		<div class="col-md-12 text-center">
			<div class="col-md-12 text-center">
			<h1>MGK Autoparts services</h1>
			<h4>NO. 10-11, BLOCK E.<br/> GROUND FLOOR, KG JUNJUNGAN<br/> BH3123, NEGARA BRUNEI DARUSSALAM</h4>
			<h4>TEL: 2640115/116 FAX: 2640117</h4>
		</div>
		</div>
		<div class="col-md-12 text-center top10"><h2>Statement of A/C as at <?php echo date('d/m/Y',strtotime($selected_date))?></h2></div>
		
		<div class="col-md-12">
		
			<table class="top50">
				<tr>
					<td width="500">
						
						<div style="line-height:24px;"><b><?php echo $customer_details->name?></b></div>
						<div style="line-height:24px;"><b><?php echo $customer_details->address?></b></div>
						<div style="line-height:24px;"><b><?php echo $customer_details->phone?></b></div>
					</td>
					<td>
						<div style="line-height:24px; font-family:'Open Sans',sans-serif;">A/C CODE:&nbsp;<b><?php echo $customer_details->customer_id;?></b></div>
						<div style="line-height:24px; font-family:'Open Sans',sans-serif;">PAGE:&nbsp;<b>1</b></div>
						<div style="line-height:24px; font-family:'Open Sans',sans-serif;">PRINT DATE:&nbsp;<b><?php echo date('d/m/Y')?></b></div>
						<div style="line-height:24px; font-family:'Open Sans',sans-serif;">CURRENCY:&nbsp;<b>B$</b></div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div style="height:20px;display:block;clear:both;">&nbsp;</div>
	<div class="row">
		<div class="col-md-12">
			<table class="table top10 text-left" style="text-align:left;">
				<tr>
					<th width="120"><b>Date</b></th>
					<th width="300"><b>Description</b></th>
					<th width="80"><b>Rate</b></th>
					<th width="100"><b>Debit</b></th>
					<th width="100"><b>Credit</b></th>
					<th width="100"><b>Balance</b></th>
				</tr>
				
				<?php
				$opening_inv=array();
				$blnc_count=0;
				foreach($items as $item)
					{
					
				 if($item->opening_balance > 0 && (strtotime($item->date_issue) <= strtotime($selected_date.' -1 month')) ) {
				 	$opening_inv[]=$item->invoice_id;
					$blnc_count+=$item->opening_balance;
				  ?>
				<tr>
					<td></td>
					<td colspan="4">Opening Balance (<?php echo $item->opening_balance_desc; ?>)</td>
					<td><?php echo format_price($item->opening_balance); ?></td>
				</tr>
				<?php }
				} ?>
			<?php
					
					$total_blnc=0;
					$credit_blnc=0;
					$debit_blnc=0;
					$monthly_arr=array();
					foreach($items as $item)
					{					//echo date('m-Y',strtotime($item->date));
						$keys=array_keys($monthly_arr);
					//	var_dump(in_array(date('m-Y',strtotime($item->date)),$keys));
						if(in_array(date('m-Y',strtotime($item->date_issue)),$keys))
						{
							$monthly_arr[date('m-Y',strtotime($item->date_issue))]=$monthly_arr[date('m-Y',strtotime($item->date_issue))]+$item->amount;
						}
						else
						{
							$monthly_arr[date('m-Y',strtotime($item->date_issue))]=$item->amount;
						}
					}
				
				 foreach($items as $item):
				 
						if(in_array($item->invoice_id,$opening_inv)){ continue;}	
				 		
				 ?>
					<tr>
						<td><?php echo date('d/m/Y',strtotime($item->date_issue));?></td>
						<td><?php echo $item->description;?></td>
						<td></td>
						<td><?php echo format_price($item->amount);?></td>
						<td><?php echo ($item->credited=='')?'':format_price($item->credited);?></td>
						<?php 
							$blnc_count+=$item->amount;
							$blnc_count-=$item->credited;
						?>
						<td><?php echo format_price($blnc_count)?></td>
						<?php
							$c_total=($item->credited=='NULL')?0:$item->credited;
							$credit_blnc=$credit_blnc+$c_total;
							$debit_blnc=$debit_blnc+$item->amount;
						?>
					</tr>
			<?php endforeach;?>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php
			
				if(sizeof($monthly_arr) > 6)
				{
					$over_six_total=0;
					$temp_arr=implode(",",$monthly_arr);
					
					$arr_size=sizeof($monthly_arr);
					$temp=explode(",",$temp_arr);
					for($i=0;$i < ($arr_size-6);$i++)
					{
						$over_six_total+=$temp[$i];
					}
					array_slice($monthly_arr, -6, 6, true);
				}
				
			 asort($monthly_arr);
				 $keys=array_keys($monthly_arr);
				$keys[sizeof($keys)-1];
				
				$s_node=date("m",strtotime($keys[0]));
				$e_node=date("m",strtotime('01-'.$keys[sizeof($keys)-1]));
			?>
			
			<table style="text-align:left;" width="200">
				<tr>
				<?php 
					$i=0;
				
				for($k=1;$k<=12;$k++)
				{
					if($k < $e_node && $k <$s_node)
					{
						$year=date("Y",strtotime('01-'.$keys[sizeof($keys)-1]));
						$monthly_arr[$k.'-'.$year]=0;
					}
					
				}
				asort($monthly_arr);
				foreach($monthly_arr as $key=>$month){
					$i++;
				?>
					<td >
						<table border="1" style="width:100%;"><tr><th ><b><?php echo date('M-Y',strtotime('01-'.$key));?></b></th></tr>
							<tr><td><?php echo format_price($month);?></td></tr>
						</table>
						</td>
					<?php if($i%6==0){?>
						</tr>
					<?php }?>
				<?php }?>
				</tr>
			</table>
			<?php
		
			?>
	</div>
	</div>
	<div class="row">
		<?php if(isset($item->opening_balance) || $item->opening_balance != NULL) {
		 ?>
		<div class="col-md-12" style="text-align:right;width:100%;"><b>Total Balance:</b> <?php echo format_price($blnc_count);?></div></div>
		<?php } else { ?>
		<div class="col-md-12" style="text-align:right;width:100%;"><b>Total Balance:</b> <?php echo format_price($blnc_count);?></div></div>
		<?php } ?>		
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
</body>
</html>
