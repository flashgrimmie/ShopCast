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
          <div class="pull-left">S.O Details</div>
          <div class="clearfix"></div>
        </div>
        <div class="widget-content">
          <form id="cs_form" method="post" action="<?php echo base_url()?>retail/save_so/<?php echo $this->uri->segment(3)?>">
              <table class="table table-bordered">
                <thead>
                  <th>Stock Code</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Discount(%)</th>
                  <th>Markup($)</th>
                  <th>Total</th>
                  <th></th>
                </thead>
                <tbody id="iitems">
                  <?php if(isset($i_items)) { foreach($i_items as $item_details){?>
                    <tr class="text-center" id="item_<?php echo $item_details->id?>">
                      <td><?php echo $item_details->stock_num?>
                        <input type="hidden" value="<?php echo $item_details->item_id?>" id="itemid_<?php echo $item_details->id?>" name="item_id[]"/>
                        <input type="hidden" value="<?php echo $item_details->id?>" id="iitemid_<?php echo $item_details->id?>" name="iitem_id[]"/>
                      </td>
                      <td>
                        <select id="customer_prices">
                          <option value="<?php echo format_number($item_details->price1)?>" <?php echo format_number($item_details->price)==format_number($item_details->price1) ? 'selected' : ''?>>Price1</option>
                          <option value="<?php echo format_number($item_details->price2)?>" <?php echo format_number($item_details->price)==format_number($item_details->price2) ? 'selected' : ''?>>Price2</option>
                          <option value="<?php echo format_number($item_details->price3)?>" <?php echo format_number($item_details->price)==format_number($item_details->price3) ? 'selected' : ''?>>Price3</option>
                          <option value="<?php echo format_number($item_details->price4)?>" <?php echo format_number($item_details->price)==format_number($item_details->price4) ? 'selected' : ''?>>Price4</option>
                        </select>
                        <input type="hidden" value="<?php echo format_number($item_details->price)?>" id="price_<?php echo $item_details->id?>" name="price[]"/>
                      </td>
                      <td><input class="form-control inputsmall" type="text" id="quantity_<?php echo $item_details->id?>" value="<?php echo isset($item_details) ? $item_details->quantity : '1'?>" name="quantity[]"/></td>
                      <td><input class="form-control inputsmall" type="text" id="discount_<?php echo $item_details->id?>" value="<?php echo isset($item_details) ? $item_details->discount : ''?>" name="item_discount[]"/></td>
                      <td>
                        <input class="form-control inputsmall" type="hidden" id="discountvalue_<?php echo $item_details->id?>" value="<?php echo isset($item_details) ? $item_details->discount_value : ''?>" name="discount_value[]"/>
                        <input class="form-control inputsmall" type="text" id="markup_<?php echo $item_details->id?>" value="<?php echo isset($item_details->markup) ? $item_details->markup : ''?>" name="markup[]"/>
                      </td>
                      <td><span id="linetotal_<?php echo $item_details->id?>"><?php echo format_number((($item_details->price*(1-$item_details->discount/100))-$item_details->discount_value)*$item_details->quantity)?></span>
                        <input type="hidden" value="<?php echo format_number((($item_details->price*(1-$item_details->discount/100))-$item_details->discount_value)*$item_details->quantity)?>" id="linetotalval_<?php echo $item_details->id?>"/>
                      </td>
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
              <div class="control-group width60 pull-right">
                <label class="control-label" for="total">Total:&nbsp;<span id="total"><?php echo format_price(isset($invoice_details->total) ? $invoice_details->total : '0')?></span></label>
              </div>
              <div class="clearfix"></div>
              <div class="control-group width60">
                <input type="text" autocomplete="off" class="form-control" placeholder="Select Customer" id="selcustomer" style="display:inline" />
                <a id="add_customer" href="#modalAddCustomer" data-toggle="modal" class="btn btn-default"><i class="fa fa-plus"></i></a>
                <div class="selectHolder" id="selectHolderSup"></div>
              </div>
              <div class="control-group width60">
                <address class="filled alert alert-success top10" style="<?php echo !isset($invoice_details)?'display:none':''?>">
                  <strong id="customer_name"><?php echo isset($invoice_details->name)?$invoice_details->name:''?></strong><br/>
                  <span id='customer_address'><?php echo isset($invoice_details->address)?$invoice_details->address:''?></span><br/>
                  <span id='customer_phone'><?php echo isset($invoice_details->phone)?$invoice_details->phone:''?></span><br/>
                  <span id='customer_email'><?php echo isset($invoice_details->email)?$invoice_details->email:''?></span><br/>
                  
                  <i class="icon-map-marker pull-right"></i>
                </address>
                <input type="hidden" name="customer_id" id="customer_id" value="<?php echo isset($invoice_details->customer_id)?$invoice_details->customer_id:''?>">
              </div>
              <div class="control-group width60 top10">
                <label class="control-label" for="date">Discount</label>
                <input type="text" value="<?php echo isset($invoice_details->discount) ? $invoice_details->discount : ''?>" class="form-control" name="discount" id="discount" />
              </div>
              <div class="control-group width60">
                <label class="control-label" for="date">Date</label>
                <div class="controls">
                  <input type="text" class="datepicker form-control" name="date" value="<?php echo isset($invoice_details->date) ? date('m/d/Y',strtotime($invoice_details->date)) : ''?>" required />
                </div>
              </div>
              <div class="control-group width60">
                <label class="control-label" for="deposit">Deposit</label>
                <div class="controls">
                  <input type="text" class="form-control" name="deposit" value="<?php echo isset($invoice_details->deposit) ? $invoice_details->deposit : ''?>"/>
                </div>
              </div>
              <div class="control-group top10" id="mechanics">
                 <?php if(isset($mechanics) && is_array($mechanics)) { $i=0;
                  foreach($mechanics as $key=>$value):?>
                  <div class="controls top10">
                    <input style="width:45%;display:inline-block" type="text" class="form-control" name="mechanic[]" value="<?php echo $key?>" placeholder="Mechanic Name"/>
                    <input style="width:45%;display:inline-block;" type="text" class="form-control" name="mechanic_charge[]" value="<?php echo $value?>" placeholder="Service Charge"/>
                    <?php if($i==0){?>
                      <a id="add_mechanic" class="btn btn-default"><i class="fa fa-plus"></i></a>
                    <?php } else {?>
                      <a class="btn btn-danger remove_mechanic"><i class="fa fa-minus"></i></a>
                    <?php }?>
                  </div>
                  <?php $i++;
                    endforeach; } else {?>
                  <div class="controls">
                    <input style="width:45%;display:inline-block" type="text" class="form-control" name="mechanic[]" value="" placeholder="Mechanic Name"/>
                    <input style="width:45%;display:inline-block;" type="text" class="form-control" name="mechanic_charge[]" value="" placeholder="Service Charge"/>
                    <a id="add_mechanic" class="btn btn-default"><i class="fa fa-plus"></i></a>
                  </div>
                <?php }?>
              </div>
              <div class="control-group top10">
                <label class="control-label" for="remark">Remarks</label>
                <div class="controls">
                  <textarea class="form-control" rows="6" name="remark"> <?php echo isset($invoice_details->remark) ? $invoice_details->remark : ''?></textarea>
                </div>
              </div>
              <div class="control-group width60">
                <label class="control-label" for="remark">Payment Method</label>
                <div class="controls">
                  <select class="form-control" name="payment_method" id="payment_method">
                    <option value="">Select payment method</option>
                    <option value="cs" <?php echo isset($invoice_details->cs_id) ? 'selected' : ''?>>Cash Sale</option>
                    <option value="invoice" <?php echo isset($invoice_details->invoice_id) ? 'selected' : ''?>>Invoice</option>
                  </select>
                </div>
              </div>
              <div class="control-group">
                <p class='error'></p>
              </div>
              <div class="submit form-group clearfix top10">
                <?php if(!isset($invoice_details->invoice_id) && !isset($invoice_details->cs_id)) {?>
                  <input type="submit" class="btn btn-success pull-right" name="publish" id="publish" value="Proceed to payment">
                <?php }?>
                <input type="submit" class="btn btn-default pull-right right10" name="save" value="Save">
              </div>
            </div>
            <input type="hidden" name="draft" id="draft"/>
          </form>
        </div>
      </div>                
    </div>
  </div>
</div>
<div id="saved" class="label label-default">Saved</div>
<?php $this->load->view('templates/add_customer')?>
<script>
$(document).ready(function(){

  $('#publish').click(function(){
    if($('#payment_method').val()!='cs' && $('#payment_method').val()!='invoice') {
      $('.error').html('Please select the payment method.');
      return false;
    }
  });

  <?php if(!$this->uri->segment(3)) {?>
    $('#draft').val('draft');
    setInterval(function(){
      $.ajax({
        type: "POST",
        url: $('#cs_form').attr('action'),
        data: $('#cs_form').serialize()
      }).done(function( msg ) {
          $('#cs_form').attr('action','<?php echo base_url()?>retail/save_so/'+msg);
          if(msg>0) {
            $('#saved').show();
          }
      });
      $('#saved').hide();
    },10000);
  <?php }?>

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

  $('body').on('change','#customer_prices',function(){
    var parent=$(this).parent().parent();
    console.log($(this).val());
    parent.find('[id*=price_]').val($(this).val());
    var sell_price=parent.find('[id*=price_]').val();
    var quantity=parent.find('[id*=quantity_]').val();
    var discount=parent.find('[id*=discount_]').val();
    var discount_value=parent.find('[id*=discountvalue_]').val();
    var markup=parent.find('[id*=markup_]').val();
    var item_total=roundToTwo((sell_price*(1-discount/100)-discount_value+(1*markup))*quantity);
    parent.find('[id*=linetotal_]').html(item_total);
    parent.find('[id*=linetotalval_]').val(item_total);
    update_total();
  });

  $('#payment_method').change(function(){
    if($('.error').html().indexOf('payment method')!=-1) {
      $('.error').html('');
    }
  });

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
          url: "<?php echo base_url() ?>retail/selectInvoiceItem",
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
            update_total();
        });
  });
  $('body').on('click','.deleteitem',function(){
    $(this).parent().parent().remove();
    if($('#iitems tr').length==1) {
      $('#no-items').show();
    }
    update_total();
  });
  $('body').on('keyup','[id*="quantity_"],[id*="discount_"],[id*="discountvalue_"],[id*="markup_"]',function(){
    var id=$(this).attr('id');
    var arr=id.split('_');
    var parent=$(this).parent().parent();
    var sell_price=parent.find('#price_'+arr[1]).val();
    var quantity=parent.find('#quantity_'+arr[1]).val();
    var discount=parent.find('#discount_'+arr[1]).val();
    var discount_value=parent.find('#discountvalue_'+arr[1]).val();
    var markup=parent.find('#markup_'+arr[1]).val();
    var item_total=roundToTwo((sell_price*(1-discount/100)-discount_value+(1*markup))*quantity);
    parent.find('#linetotal_'+arr[1]).html(item_total);
    parent.find('#linetotalval_'+arr[1]).val(item_total);
    update_total();
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

  $('body').on('click','.searchcustomeropt', function (){
    var selected = $(this).data('selected');
    select_customer(selected);
  });

  $('.submit :submit').click(function(){
    if(!$('#customer_id').val())
    {
      $('.error').html('Please select a customer.');
      return false;
    } else if($('#no-items').is(':visible'))
    {
      $('.error').html('You have to select at least one item!');
      return false;
    } else {
      if($('.error').html().indexOf('payment method')<0) {
        $('.error').html('');
      }
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

  $('#add_mechanic').click(function(){
    var html='<div class="controls top10">';
    html+='<input style="width:45%;display:inline-block" type="text" class="form-control" name="mechanic[]" placeholder="Mechanic Name"/>';
    html+='&nbsp;<input style="width:45%;display:inline-block;" type="text" class="form-control" name="mechanic_charge[]" placeholder="Service Charge"/>';
    html+='&nbsp;<a class="btn btn-danger remove_mechanic"><i class="fa fa-minus"></i></a>'
    html+='</div>';     
    $('#mechanics').append(html);    
  });

  $('body').on('click','.remove_mechanic',function(){
    $(this).parent().remove();
  });
})
</script>
<?php $this->load->view('templates/footer')?>