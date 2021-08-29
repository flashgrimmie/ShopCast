<?php $this->load->view('templates/head_pdf');?>
<body>
<div class="container">
	
	<div class="row">
		<div class="col-md-12 text-left">
                <img src="assets/img/booker.jpg" width="150" style="margin: 0px;margin-bottom: -20px;margin-left: -15px;" />
                <!-- h1><?php echo $outlet->name; ?><?php //echo date('Y',strtotime($info['date_issue']))?></h1 -->
                <table>
                    <tr>
                        <td>
                            <h6>P.O.Box 2007</h6>
                            <h6>BANDAR SERI BEGAWAN BS8674</h6>
                            <h6>Negara Brunei Darussalam</h6>
                        </td>
                    </tr>
                </table>
                <table style="margin-top: 10px; margin-bottom: 20px;">
                    <tr>
                        <td width="75%">
                            <h6><strong>Shop & Office</strong></h6>
                            <h6 style="margin-top: 0px;"><?php echo $outlet->address1; ?>,<br/> <?php echo $outlet->address2; ?>, Bandar Seri Begawan, <br /> NEGARA BRUNEI DARUSSALAM</h6>
                            <h6>Tel: <?php echo $outlet->contact; ?>, Fax: <?php echo $outlet->fax; ?></h6>
                        </td>
                        <td class="text-left" width="25%">
                           <h3><strong>INVOICE<br> NO:</strong>&nbsp;
                            <span style="color: red;"><?php echo $info['do_id'] ?></span>
                            </h3>
                        </td>
                    </tr>
                </table>
                <table>
	                <tr>
	                    <td width="14%">
	                        <h5>DELIVER TO: </h5>
	                    </td>
	                    <td width="50%" style="border-bottom: 1px dotted black;">
	                        <?php echo $info['ship_outlet']?>
	                    </td>
	                    <td width="36%" class="text-right" style="padding-right: 25px;">
	                        &nbsp;
	                    </td>
	                </tr>
	                <tr>
	                    <td width="14%">
	                        &nbsp;
	                    </td>
	                    <td width="50%" style="border-bottom: 1px dotted black;">
	                        <?php echo $info['host_address1']?>
	                    </td>
	                    <td width="36%">
	                        &nbsp;
	                    </td>
	                </tr>
	                <tr>
	                    <td width="14%">
	                        &nbsp;
	                    </td>
	                    <td width="50%" style="border-bottom: 1px dotted black;">
	                        <?php echo $info['host_contact']?>
	                    </td>
	                    <td width="36%" class="text-right" style="padding-right: 25px;">
	                        Date: <?php echo format_date($info['date']) ?>
	                    </td>
	                </tr>
            	</table>
         </div>
    </div>
<!-- 		<div class="col-md-12">
			<table class="top50">
				<tr>
					<td width="500">
						<h4>Vendor:</h4>
						<div>Name:&nbsp;<b><?php echo $info['host_outlet']?></b></div>
						<div>Address 1:&nbsp;<b><?php echo $info['host_address1']?></b></div>
						<div>Address 2:&nbsp;<b><?php echo $info['host_address2']?></b></div>
						<div>Contact:&nbsp;<b><?php echo $info['host_contact']?></b></div>
					</td>
					<td>&nbsp;</td>
					<td>
						<h4>Ship To:</h4>
						<div>Name:&nbsp;<b><?php echo $info['ship_outlet']?></b></div>
						<div>Address 1:&nbsp;<b><?php echo $info['ship_address1']?></b></div>
						<div>Address 2:&nbsp;<b><?php echo $info['ship_address2']?></b></div>
						<div>Contact:&nbsp;<b><?php echo $info['ship_contact']?></b></div>
						<div>DO No.:&nbsp;<b><?php echo $info['do_id']?></b></div>
					</td>
				</tr>
			</table> 
		</div>-->
	</div>
	<div class="row">
		<div class="col-md-12">
			<table class="top20">
        		<tr>
        			<td>
        				Please receive the following, our official invoice to follow.
        			</td>
        		</tr>
        	</table>
			<table class="table top10" border="1">
				<tr>
					<th width="10%" class="text-center">QTY</th>
					<th width="60%">DESCRIPTION</th>
					<th width="10%">ISBN</th>
					<th width="10%">UNIT PRICE</th>
					<th width="10%" colspan="2" class="text-center">PRICE</th>
				</tr>
				<?php var_dump($i_items); ?>
			<?php foreach($i_items as $item):?>
					<tr>
						<td class="text-center"><?php echo $item->quantity;?></td>
						<td class="text-center"><?php echo $item->description;?></td>
						<td class="text-center"><?php echo $item->barcode;?></td>
						<td class="text-center"><?php echo $item->price;?></td>
						<td class="text-center" colspan="2">$<?php echo $item->price * $item->quantity; count($item); ?></td>
						<!-- <td class="text-center"><?php echo $item->quantity;?></td> -->
					</tr>
			<?php endforeach;?>

			<?php if(count($i_items) == 0){ ?>
					<tr>
						<td colspan="4" class="text-center">Empty!</td>
					</tr>
				<?php } ?>
			</table>
		</div>
	</div>
<!-- <?php if($info['remark']) { ?>
	<div class="row">
		<div class="col-md-6"><b>Remark:</b> <?php echo $info['remark']?></div>
	</div>
<?php } ?> -->

	<div class="row">
		<div class="col-md-12 text-center top50">
			<table>
				<tr>
					<td width="15%">
						<strong>Received By</strong>
					</td>
					<td style="border-bottom: 1px dotted black" width="30%">
						&nbsp;
					</td>
					<td width="10%">&nbsp;</td>
					<td class="text-center" width="15%">
						<strong>Authorised By</strong>
					</td>
					<td  style="border-bottom: 1px dotted black" width="30%">
						&nbsp;
					</td>
				</tr>
			</table>
			<table class="col-md-12 text-left top20">
				<tr>
					<td width="15%">
						<strong>I/C NO:</strong>
					</td>
					<td style="border-bottom: 1px dotted black;" width="30%">
						&nbsp;
					</td>
					<td width="55%">&nbsp;</td>
				</tr>
			</table>
			<table class="col-md-12 text-left top20">
				<tr>
					<td width="15%">
						<strong>Date</strong>
					</td>
					<td style="border-bottom: 1px dotted black;" width="30%">
						<?php echo format_date($info['date'])?>
					</td>
					<td width="55%">&nbsp;</td>
				</tr>
			</table>
			<table class="col-md-12 text-left top20">
				<tr>
					<td width="15%">
						<strong>Stamp:</strong>
					</td>
					<td style="border-bottom: 1px dotted black;" width="30%">
						&nbsp;
					</td>
					<td width="55%">&nbsp;</td>
				</tr>
			</table>
					</div>
		<div>
		</div>
	</div>
</div>
</body>
</html>