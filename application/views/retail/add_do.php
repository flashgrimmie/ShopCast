<?php $this->load->view('templates/header');?>
<script>
  function update_total()
  {
    var total=0; 
    $('[id*="linetotalval_"]').each(function(){
      total+=Number($(this).val());
    });
    $('#total').html("$"+roundToTwo(total)); 
    //format_price($('#total'));
  }
</script>
<div class="container">
  <div class="row">
    <?php $this->load->view('templates/retail_items')?>
    <div class="col-md-6">
      <div class="widget">
        <div class="widget-head">
          <div class="pull-left">D.O Details</div>
          <div class="clearfix"></div>
        </div>
        <div class="widget-content">
          <form method="post" action="<?php echo base_url()?>retail/save_do/<?php echo $this->uri->segment(3)?>">
              <table class="table table-bordered">
                <thead>
                  <th>Stock Code</th>
                  <th>Description</th>
                  <th>Quantity</th>
                  <th></th>
                </thead>
                <tbody id="iitems">
                  <?php if(isset($i_items)) { foreach($i_items as $item_details){?>
                    <tr class="text-center" id="item_<?php echo $item_details->id?>">
                      <td><?php echo $item_details->stock_num?>
                        <input type="hidden" value="<?php echo $item_details->item_id?>" id="itemid_<?php echo $item_details->id?>" name="item_id[]"/>
                        <input type="hidden" value="<?php echo $item_details->id?>" id="iitemid_<?php echo $item_details->id?>" name="iitem_id[]"/>
                      </td>
                      <td><?php echo $item_details->description?></td>
                      <td><input class="form-control inputsmall" type="text" id="quantity_<?php echo $item_details->id?>" value="<?php echo isset($item_details) ? $item_details->quantity : '1'?>" name="quantity[]"/></td>
                      <td><a class="btn btn-xs btn-danger deleteitem" id="deleteitem_<?php echo $item_details->id?>" role="button">
                            <i class="fa fa-times delete"></i>
                          </a>
                      </td>
                    </tr>
                  <?php }} ?>
                  <tr><td colspan="7" <?php echo isset($i_items) ? 'style="display:none"' : ''?> id="no-items" class="text-center">No items selected</td></tr>
                </tbody>
              </table>
            <div class="padd top10">
              <div class="clearfix"></div>
              <div class="control-group width60">
                <input type="text" autocomplete="off" class="form-control" placeholder="Select Customer" id="selcustomer" />
                <div class="selectHolder" id="selectHolderSup"></div>
              </div>
              <div class="control-group width60">
                <address class="filled alert alert-success top10" style="<?php echo !isset($invoice_details)?'display:none':''?>">
                  <strong id="customer_name"><?php echo isset($invoice_details)?$invoice_details->name:''?></strong><br/>
                  <span id='customer_address'><?php echo isset($invoice_details)?$invoice_details->address:''?></span><br/>
                  <span id='customer_phone'><?php echo isset($invoice_details)?$invoice_details->phone:''?></span><br/>
                  <span id='customer_email'><?php echo isset($invoice_details)?$invoice_details->email:''?></span><br/>
                  
                  <i class="icon-map-marker pull-right"></i>
                </address>
                <input type="hidden" name="customer_id" id="customer_id" value="<?php echo isset($invoice_details)?$invoice_details->customer_id:''?>">
              </div>
              <div class="control-group width60">
                <label class="control-label" for="date">Date</label>
                <div class="controls">
                  <input type="text" class="datepicker form-control" name="date" value="<?php echo isset($invoice_details) ? date('m/d/Y',strtotime($invoice_details->date)) : ''?>" required />
                </div>
              </div>
              <div class="control-group width60">
                <label class="control-label" for="deposit">Deposit</label>
                <div class="controls">
                  <input type="text" class="form-control" id="deposit" name="deposit" value="<?php echo isset($invoice_details) ? $invoice_details->deposit : ''?>"/>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="remark">Remarks</label>
                <div class="controls">
                  <textarea class="form-control" rows="6" name="remark"> <?php echo isset($invoice_details) ? $invoice_details->remark : ''?></textarea>
                </div>
              </div>
              <div class="control-group">
                <p class='error'></p>
              </div>
              <div class="submit form-group clearfix top10">
                <?php if(!isset($invoice_details->invoice_id)) {?>
                  <input type="submit" class="btn btn-success pull-right" name="publish" value="Proceed to payment">
                <?php }?>
                <input type="submit" class="btn btn-default pull-right right10" name="save" value="Save">
              </div>
            </div>
          </form>
        </div>
      </div>                
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  $('#filter_items').keyup(function(){
    $.ajax({
          type: "POST",
          url: "<?php echo base_url() ?>retail/searchStock",
          data: {search:$(this).val()}
        }).done(function( msg ) {
            $('.gallery').html(msg);
        });
  });
  $('body').on('click','[id*="retailitem_"]',function(){
    var id=$(this).attr('id');
    var arr=id.split('_');
    $.ajax({
          type: "POST",
          url: "<?php echo base_url() ?>retail/selectDOItem",
          data: {item_id:arr[1]}
        }).done(function( msg ) {
            if(msg=='sold_out') {
              alert('The item is sold out!');
              return false;
            }
            if($('#iitems tr').length==1) {
              $('#no-items').hide();
            }
            $('#iitems').append(msg);
        });
  });
  $('body').on('click','.deleteitem',function(){
    $(this).parent().parent().remove();
    if($('#iitems tr').length==1) {
      $('#no-items').show();
    }
  });
  
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
  });

  $('.submit :submit').click(function(){
    if(!$('#customer_id').val())
    {
      $('.error').html('Please select a customer.');
      return false;
    } else if($('#no-items').is(':visible')) {
      $('.error').html('You have to select at least one item!');
      return false;
    } 
    else {
      $('.error').html('');
    }
  });

  $('#selcustomer').keypress(function(){
    $('.error').html('');
  });

  $('#selcustomer').change(function(){
    $('.error').html('');
    
  });

  $('input[id^=supplier_]').click(function(){
    $('.error').html('');
  });

})
</script>
<?php $this->load->view('templates/footer')?>