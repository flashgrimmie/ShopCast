<?php $this->load->view('templates/header');?>

            <!-- ==================== WIDGETS CONTAINER ==================== -->
            <div class="container">
			<?php if (!isset($isactive)) { ?>
				<div class="row top10">
					<div class="col-md-12">
						<a id="import_files" href="<?php echo base_url() ?>retail/exportVoidTrans" class="btn btn-default pull-right right10 mb clearform">
							<icon class="fa fa-upload"></icon>
							&nbsp;Export
						</a>						
					</div>
				</div>
			<?php } ?>
                <!-- ==================== TABLE ROW ==================== -->
                <div class="row pbottom30">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">Void Transactions</div>
                            <div class="widget-content">
                                <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                                    <table class="table table-bordered dataTable" id="membersTable">
                                      <thead>
                                        <tr>
                                          <th>ID</th>
                                          <th>Date</th>
                                          <th>Customer</th>
                                          <th>Total Amount</th>
                                          <th>Type</th>
                                          <th>Staff</th>
                                          <th>Reason</th>
                                        </tr>
                                      </thead>
                                    </table> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ==================== END OF BORDERED TABLE FLOATING BOX ==================== --> 
              </div>
<script>
$(document).ready(function(){
  var oTable = $('#membersTable').dataTable({
    "bServerSide": true,
    "sAjaxSource": "<?php echo base_url() ?>retail/getVoidTransactions",
    "sServerMethod": "GET",
    "aoColumns": [
      { "aaData": "0", "sType": "numeric", 'sClass': "center" },
      { "aaData": "1", 'sClass': "center" },
      { "aaData": "2", 'sClass': "center" },
      { "aaData": "3", 'sClass': "center" },
      { "aaData": "4", 'sClass': "center" },
      { "aaData": "5", 'sClass': "center" },
      { "aaData": "6", 'sClass': "center" },
    ],
  });
});
</script>
<?php $this->load->view('templates/footer');?>