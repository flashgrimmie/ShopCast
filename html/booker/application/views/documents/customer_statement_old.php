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
			<h1>MGK Autoparts services <?php echo date('Y',strtotime($info['date_issue']))?></h1>
			<h4>NO. 10-11, BLOCK E.<br/> GROUND FLOOR, KG JUNJUNGAN<br/> BH3123, NEGARA BRUNEI DARUSSALAM</h4>
			<h4>TEL: 2640115/116 FAX: 2640117</h4>
		</div>
		</div>
		<div class="col-md-12 text-center top10"><h2>Statement of A/C as at <?php echo date('d/m/Y')?></h2></div>
		
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
			<?php foreach($items as $item):?>
					<tr>
						<td><?php echo date('d/m/Y',strtotime($item->date));?></td>
						<td><?php echo $item->description;?></td>
						<td></td>
						<td><?php echo format_price($item->amount);?></td>
						<td></td>
						<td><?php echo format_price($item->balance)?></td>
					</tr>
			<?php endforeach;?>
			</table>
		</div>
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