<?php $this->load->view('templates/header');?>

            <!-- ==================== WIDGETS CONTAINER ==================== -->
            <div class="container">
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
                                          <th>Supplier</th>
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
    "sAjaxSource": "<?php echo base_url() ?>purchasing/getVoidTransactions",
    "sServerMethod": "GET",
    "aoColumns": [
      { "aaData": "0", "sType": "numeric", 'sClass': "center" },
      { "aaData": "1", 'sClass': "center" },
      { "aaData": "2", 'sClass': "center" },
      { "aaData": "3", 'sClass': "center" },
      { "aaData": "4", 'sClass': "center" },
      { "aaData": "5", 'sClass': "center" },
    ],
  });
});
</script>
<?php $this->load->view('templates/footer');?>