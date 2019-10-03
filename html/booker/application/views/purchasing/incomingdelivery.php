<?php $this->load->view('templates/header');?>

<!-- ==================== WIDGETS CONTAINER ==================== -->
<div class="container">
	<!-- ==================== TABLE ROW ==================== -->
	<div class="row">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-head">Incoming Deliveries</div>
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
								<?php foreach ($delorders as $key => $value) { ?>
									<tr>
										<td><?php echo $value->do_id ?></td>
										<td><?php echo format_date($value->date) ?></td>
										<td><?php echo $value->name ?></td>
										<td class="actions">
										<?php if($value->accepted=='N') { ?>
											<a class="btn btn-xs btn-danger" href="<?php echo base_url().'purchasing/acceptDelivery/'.$value->do_id ?>">Accept</a>
										<?php } else { ?>
											<a class="btn btn-xs btn-default" href="#">Accepted</a>
										<? } ?>
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