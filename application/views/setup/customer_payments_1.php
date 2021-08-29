<?php $this->load->view('templates/header');?>

            <!-- ==================== WIDGETS CONTAINER ==================== -->
            <div class="container">
              <div class="row top10"> 
                    <div class="col-md-12">
                        <a href="#modalMakePayment" data-toggle="modal" class="btn btn-primary pull-right mb clearform" >
                      <i class="icon-plus-sign"></i> Make a Payment
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
                                          <th>Amount</th>
										  <th>Description</th>
                                          <th>Date</th>
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
    <div id="modalMakePayment" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h3 class="modal-title">Make Payment</h3>
          </div>
          <form action="<?php echo base_url()?>system_setup/make_payment/<?php echo $this->uri->segment(3)?>" method="post">
            <div class="modal-body">
                  <div class="form-group">
                    <label class="col-lg-4 control-label">Amount</label>
                    <div class="col-lg-8">
                      <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" name="amount" placeholder="Enter Amount to pay" class="form-control">
                      </div>
                    </div>
                  </div>
				  <p>&nbsp;</p>
				    <div class="form-group">
					   <label class="col-lg-4 control-label">Description</label>
					  <div class="col-lg-8">
					   
						  <input type="text" name="description" placeholder="Description for payment" class="form-control" size="">
						
					  </div>
					</div>  
				  
                  <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
              <input class="btn btn-danger" type="submit" name="submit" value="Yes">
              <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
         </form>
        </div>
      </div>
    </div>
<script>
$(document).ready(function(){
  var oTable = $('#membersTable').dataTable({
    "bServerSide": true,
    "sAjaxSource": "<?php echo base_url() ?>system_setup/getCustomerPayments/<?php echo $this->uri->segment(3)?>",
    "sServerMethod": "POST",
    "aaSorting": [[1,'desc']],
    "aoColumns": [
      { "aaData": "0", "sType": "numeric", 'sClass': "center" },
      { "aaData": "1", 'sClass': "center" },
	  { "aaData": "2", 'sClass': "center" },
    ]
  });
});
</script>
<?php $this->load->view('templates/footer');?>