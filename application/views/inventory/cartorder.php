<?php $this->load->view('templates/header');?>

<div class="container">

	<div class="row">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-head">Outlets Purchase Orders</div>
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
							<?php foreach($porders as $po) { ?>
								<tr>
									<td><?php echo $po->po_id ?></td>
									<td><?php echo format_date($po->date) ?></td>
									<td><?php echo $po->name ?></td>
									<td class="actions">
									<a class="btn btn-xs btn-default" href="<?php echo base_url().'purchasing/create_iopo/'.$po->po_id ?>"><i class="fa fa-pencil"></i></a>

									<a class="btn btn-xs btn-default" href="javascript:popup('<?php echo base_url().'documents/inner_purchase_order/'.$po->po_id ?>')"><i class="fa fa-file"></i></a>

									<a class="btn btn-xs btn-danger btn-xs delete-action" data-href="<?php echo base_url().'inventory/deleteInterPurchase/'.$po->po_id ?>"  href="#modalDelete" role="button" data-toggle="modal" ><i class="fa fa-times"></i></a>

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


</div>
<?php $this->load->view('templates/footer');?>