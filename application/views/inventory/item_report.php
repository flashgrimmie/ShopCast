<?php $this->load->view('templates/header');?>


<!-- ==================== WIDGETS CONTAINER ==================== -->
<div class="container">
	<!-- ==================== TABLE ROW ==================== -->
	<div class="row top10">
		<div class="col-md-12">
			<form action="<?php echo base_url()?>inventory/itemReport">
				<input type="text" class="form-control dtpicker" placeholder="Date From" name="date_from" value="<?php echo $this->input->get('date_from')?>"/>&nbsp;-
				<input type="text" class="form-control dtpicker" placeholder="Date To" name="date_to" value="<?php echo $this->input->get('date_to')?>"/>
				<input type="submit" value="Go"/>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-head">Item Report</div>
				<div class="widget-content">
					<div id="datatable_wrapper" class="dataTables_wrapper">
						<table id="itable" class="table table-striped table-bordered table-hover">
							<thead>
								<th>Stock Code</th>
								<th>Description</th>
								<th>Date</th>
								<th>Reference</th>
								<th>Transaction Type</th>
								<th>Items Issued</th>
								<th>Items Received</th>
								<th>Balance</th>
							</thead>
						</table>
					</div>
					<div class="widget-foot">
                      <br><br>
                      <div class="clearfix"></div> 
                    </div>
				</div>
			</div>
		</div>
	</div>
	<!-- ==================== END OF BORDERED TABLE FLOATING BOX ==================== -->
</div>

<script>
$(document).ready(function(){
  $('.dtpicker').datepicker();
 
  var oTable = $('#itable').dataTable({
    "bServerSide": true,
    "sAjaxSource": "<?php echo base_url() ?>inventory/getItemReport",
    "sPaginationType": "full_numbers",
    "fnServerParams": function ( aoData ) {
      aoData.push( { "name": "date_from", "value": "<?php echo $this->input->get('date_from')?>" },{ "name": "date_to", "value": "<?php echo $this->input->get('date_to')?>" });
	},
    "sServerMethod": "GET",
    "aoColumns": [
      { "aaData": "0", "sType": "numeric", 'sClass': "center" },
      { "aaData": "2", 'sClass': "center" },
      { "aaData": "1", 'sClass': "center" },
      { "aaData": "3", 'sClass': "center" },
      { "aaData": "5", 'sClass': "center" },
      { "aaData": "6", 'sClass': "center" },
      { "aaData": "7", 'sClass': "center" },
      { "aaData": "7", 'sClass': "center" }
    ]
  });

});
</script>


<?php $this->load->view('templates/footer');?>