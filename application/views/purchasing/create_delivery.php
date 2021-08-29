<?php $this->load->view('templates/header');?>

<!-- ==================== WIDGETS CONTAINER ==================== -->
<div class="container">
	<form name="forma" method="post" action="<?php echo base_url() ?>purchasing/saveDelivery/<?php echo $this->uri->segment(3) ?>">
	<!-- ==================== TABLE ROW ==================== -->
	<div class="row">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-content">
					<div id="datatable_wrapper" class="dataTables_wrapper">
						<table id="itable" class="table table-striped table-bordered table-hover">
							<thead>
								<th style="width:15%">Serial No.</th>
								<th>Description</th>
								<th>Quantity</th>
								<th>Action</th>
							</thead>
							<tbody>
								<?php foreach ($invoice_items as $key => $value) { ?>
									<tr>
										
										<td><?php echo $value->stock_num ?></td>
										<td><?php echo $value->description ?></td>
										<td style="width:100px">
											<input type="text" class="form-control" value="<?php echo $value->quantity ?>" name="quantity[]"/>
										</td>
										<input type="hidden" value="<?php echo $value->item_id ?>" name="item_id[]"/>
										<td style="width:100px" class="actions">
											<a class="btn btn-xs btn-danger btn-default" href="<?php echo base_url().'purchasing/create_iopo/'.$value->po_id ?>"><i class="fa fa-times"></i></a>
										</td>
									</tr>
								<?php } ?>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">
			<div class="padd">
				<div class="span3">
					<label for="deposit">Deposit:</label>
					<input type="text" class="span3 form-control" name="deposit" id="deposit" value="0">
				</div>
				
				<div class="span3">
					<label for="status">Status:</label> 
					<select name="status" class="span3 form-control" id="status">
						<option selected="selected" value="paid">Paid</option>
						<option value="confirmed">Confirmed</option>
						<option value="pending">Pending</option>
					</select>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4 pull-right">
			<div class="padd pull-right">
				<input type="submit" value="Save" class="btn btn-success"/>
			</div>
		</div>
	</div>
	</form>
</div>



<?php $this->load->view('templates/footer');?>