<?php $this->load->view('templates/header');?>

            <!-- ==================== WIDGETS CONTAINER ==================== -->
            <div class="container">
              <div class="row top10"> 
                    <div class="col-md-12">
                        <a href="<?php echo base_url()?>purchasing/return_pi" class="btn btn-primary pull-right mb clearform" >
                      <i class="icon-plus-sign"></i> New Debit Note
                    </a>
                    </div>
                </div>
                <!-- ==================== TABLE ROW ==================== -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">Debit Notes</div>
                            <div class="widget-content">
                                <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                                    <table class="table table-bordered dataTable" id="membersTable">
                                      <thead>
                                        <tr>
                                          <th>ID</th>
                                          <th>Date</th>
                                          <th>Invoicing Date</th>
                                          <th>Invoice No.</th>
                                          <th>Total</th>
                                          <th>Supplier</th>
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
    "sAjaxSource": "<?php echo base_url() ?>purchasing/getRPIs",
    "sServerMethod": "POST",
    "aaSorting": [[1,'desc']],
    "aoColumns": [
      { "aaData": "0", "sType": "numeric", 'sClass': "center" },
      { "aaData": "1", 'sClass': "center" },
      { "aaData": "2", 'sClass': "center" },
      { "aaData": "3", 'sClass': "center" },
      { "aaData": "4", 'sClass': "center" },
      { "aaData": "5", 'sClass': "center" },
      { "aaData": null, 'bSortable':false, 'bSearchable':false, 'sClass': "center", 'sWidth':'100px' }
    ],
    "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            var view_file='<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/purchase_return/' + aData['0'] + '\')"><i class="fa fa-file"></i></a>';
            var edit='';
            if(aData[6]!='1') { 
              edit='<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>purchasing/return_pi/' + aData['0'] + '"><i class="fa fa-pencil"></i></a>';
            }
            $('td:eq(6)', nRow).html(view_file+edit);
            return nRow;
        },
  });

  if(window.location.hash) {
    var doc=window.location.hash.substring(1);
    popup('<?php echo base_url() ?>documents/purchase_return/'+doc);
  }

});
</script>

<?php $this->load->view('templates/footer');?>