<?php $this->load->view('templates/header');?>

            <!-- ==================== WIDGETS CONTAINER ==================== -->
            <div class="container">
              <div class="row top10"> 
                    <div class="col-md-12">
                        <a href="<?php echo base_url()?>system_setup/add_customer/<?php echo $this->uri->segment(3)?>" class="btn btn-primary pull-right mb clearform" >
                      <i class="icon-plus-sign"></i> Add Customer
                    </a>
                    </div>
                </div>
                <!-- ==================== TABLE ROW ==================== -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">Customers</div>
                            <div class="widget-content">
                                <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                                    <table class="table table-bordered dataTable" id="membersTable">
                                      <thead>
                                        <tr>
                                          <th>#</th>
                                          <th>Name</th>
                                          <th>Address</th>
                                          <th>Email</th>
                                          <th>Phone</th>
                                          <th>Fax</th>
                                          <th>Car Plate</th>
                                          <th></th>
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
    "sAjaxSource": "<?php echo base_url() ?>system_setup/getCustomers",
    "sServerMethod": "POST",
    "aoColumns": [
      { "aaData": "0", "sType": "numeric", 'sClass': "center" },
      { "aaData": "2", 'sClass': "center" },
      { "aaData": "1", 'sClass': "center" },
      { "aaData": "3", 'sClass': "center" },
      { "aaData": "4", 'sClass': "center" },
      { "aaData": "5", 'sClass': "center" },
      { "aaData": "6", 'sClass': "center" },
      { "aaData": null, 'bSortable':false, 'bSearchable':false, 'sClass': "center", 'sWidth':'100px' }
    ],
    "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            var edit='<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>system_setup/add_customer/' + aData['0'] + '"><i class="fa fa-pencil"></i></a>';
            var del='<a class="btn btn-xs btn-danger delete-action" href="#modalDelete" data-href="<?php echo base_url() ?>system_setup/delete_customer/' + aData['0'] + '" role="button" data-toggle="modal"><i class="fa fa-times"></i></a>';
            var stat='<a title="Monthly Statement" class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/customer_statement/' + aData['0'] + '\')"><i class="fa fa-file"></i></a>';
            var payment='<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>system_setup/customer_payments/' + aData['0'] + '"><i class="fa fa-dollar"></i></a>';
            $('td:eq(7)', nRow).html(payment+stat+edit+del);
            return nRow;
        },
  });
});
</script>
<?php $this->load->view('templates/footer');?>