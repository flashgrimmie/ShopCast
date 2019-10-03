<?php $this->load->view('templates/header');?>

<!-- ==================== WIDGETS CONTAINER ==================== -->
<div class="container">
	<!-- ==================== TABLE ROW ==================== -->
	<div class="row">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-head">Incoming purchase orders</div>
				<div class="widget-content">
					<div id="datatable_wrapper" class="dataTables_wrapper">
						<table id="itable" class="table table-striped table-bordered table-hover">
							<thead>
								<th>ID</th>
								<th>Date</th>
								<th>Outlet</th>
								<th>Action</th>
							</thead>
							<tbody>
								<?php foreach ($notifications as $key => $value) { ?>
									<tr>
										<td><?php echo $value->po_id ?></td>
										<td><?php echo format_date($value->date) ?></td>
										<td><?php echo $value->name ?></td>
										<td class="actions">
											<a class="btn btn-xs btn-default" href="javascript:popup('<?php echo base_url().'documents/inner_purchase_order/'.$value->po_id ?>')"><i class="fa fa-file"></i></a>
											<?php if(!in_array($value->po_id, $deliveries)) { ?>
											<a class="btn btn-xs btn-default" href="<?php echo base_url().'purchasing/create_delivery/'.$value->po_id ?>"><i class="fa fa-truck"></i></a>
											<?php } ?>
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
	<!-- ==================== END OF BORDERED TABLE FLOATING BOX ==================== -->
</div>


<?php $this->load->view('templates/footer');?>
