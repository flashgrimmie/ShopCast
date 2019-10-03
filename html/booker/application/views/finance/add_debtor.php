<?php $this->load->view('templates/header');?>
<div class="container">
  <div class="row">
    <div class="col-md-12">

      <div class="widget">
        <div class="widget-head">
          <div class="pull-left">Customer Details</div>
          <div class="clearfix"></div>
        </div>

        <div class="widget-content">
          <div class="padd">

      <!-- Profile form -->
                    <div class="form profile">
                      <!-- Edit profile form (not working)-->
                      <form class="form-horizontal" method="post" action="<?php echo base_url()?>finance/save_debtor/<?php echo $this->uri->segment(3)?>">
                          <div class="form-group">
                            <label class="control-label col-lg-2" for="name">Select Customer</label>
                            <div class="col-lg-6">
                              <input type="text" autocomplete="off" class="form-control" placeholder="Select Customer" id="selcustomer" style="display: inline;width: 92%;" />
                              <a id="add_customer" href="#modalAddCustomer" data-toggle="modal" class="btn btn-default"><i class="fa fa-plus"></i></a>
                              <div class="selectHolder" id="selectHolderSup"></div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-lg-2" for="name">&nbsp;</label>
                            <div class="col-lg-6">
                              <address class="filled alert alert-success top10" style="<?php echo !isset($debtor)?'display:none':''?>">
                                <strong id="customer_name"><?php echo isset($debtor->name)?$debtor->name:''?></strong><br/>
                                <span id='customer_address'><?php echo isset($debtor->address)?$debtor->address:''?></span><br/>
                                <span id='customer_phone'><?php echo isset($debtor->phone)?$debtor->phone:''?></span><br/>
                                <span id='customer_email'><?php echo isset($debtor->email)?$debtor->email:''?></span><br/>
                                
                                <i class="icon-map-marker pull-right"></i>
                              </address>
                            </div>
                            <input type="hidden" name="customer_id" id="customer_id" value="<?php echo isset($debtor->customer_id)?$debtor->customer_id:''?>">
                          </div>
                          <div class="form-group">
                            <label class="control-label col-lg-2" for="name">Amount Due</label>
                            <div class="col-lg-6">
                              <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="text" name="amount_due" value="<?php echo isset($debtor->amount_due)?$debtor->amount_due:''?>" class="form-control" placeholder="Amount" required>
                              </div>
                            </div>
                          </div>
                          <!-- Buttons -->
                          <div class="form-group">
                            <!-- Buttons -->
                            <div class="col-lg-6 col-lg-offset-2">
                              <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                          </div>
                        </form>
                       </div>
                   </div>
                </div>
             </div>  
          </div>
        </div>
      </div>
      <?php $this->load->view('templates/add_customer')?>
<script>
  $(document).ready(function(){
    function select_customer(selected) 
    {
      $.ajax({
        type: "POST",
        url: "<?php echo base_url() ?>system_setup/getCustomersDetails/"+selected
      }).done(function( msg ) {
        var data=JSON.parse(msg);
        $('.selectHolder').hide();
        $('.filled').show();
          $('#customer_name').html(data.name);
          $('#customer_email').html(data.email);
          $('#customer_address').html(data.address);
          $('#customer_phone').html(data.phone);
          $('#customer_id').val(data.customer_id);
      });
    }
      $('#selcustomer').keyup(function(event) {
        var customer = $('#selcustomer').val();
        $('.selectHolder').width($('#selcustomer').width()+24);
        $('#selectHolderSup').show();
        $.ajax({
              type: "POST",
              url: "<?php echo base_url() ?>purchasing/searchCustomer",
              data: {customer:customer}
            }).done(function( msg ) {
                var content='<div><ul id="searchcustomer">';
                msg = $.parseJSON(msg);
                $.each(msg,function(key,value) {
                  content+='<li data-selected='+value.customer_id+' class="searchcustomeropt">'+value.name+'</li>';
                });
                content+='</ul></div>';
                $('#selectHolderSup').html(content);
            });
      });

      $('body').on('click','.searchcustomeropt', function (){
        var selected = $(this).data('selected');
        select_customer(selected);
      });

      $('#save_customer').click(function(){
        var error='';
        if($('#addcus_name').val()=='') {
          error+='<p>Please fill out the customer name.</p>';
        }
        if($('#addcus_phone').val()=='') {
          error+='<p>Please fill out the customer phone number.</p>';
        }
        $('#customer_error').html(error);
        if(error!='') {
          return false;
        }
        $.ajax({
          type: "POST",
          url: "<?php echo base_url()?>system_setup/saveCustomerAjax",
          data: $('#form_customer').serialize()
        }).done(function( msg ) {
          select_customer(msg);
          $('#modalAddCustomer').modal('hide');
        });
        return false;
      });
  })
</script>
<?php $this->load->view('templates/footer');?>