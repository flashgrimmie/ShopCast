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
    <div class="col-md-6" style="width:590px;">
      <div class="widget">
        <div class="widget-head">
          <div class="pull-left">C.S Details</div>
          <div class="clearfix"></div>
        </div>
        <div class="widget-content">
          <form method="post" id="cs_form" action="<?php echo base_url()?>retail/save_cs/<?php echo $this->uri->segment(3)?>">
              <table class="table table-bordered">
                <thead>
                  <th>Stock Code</th>
				  <th>Description</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Discount(%)</th>
                  <!--- <th>MarkUp ($)</th> --->
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
					  <td><?php echo $item_details->description?> </td>
					  <input type="hidden" value="<?php echo $item_details->description ?>" id="desc_<?php echo $item_details->id?>" name="desc[]"/>
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
                        <input class="form-control inputsmall" type="hidden" id="discountvalue_<?php echo $item_details->id?>" name="discount_value[]"/>
                       <!--- <input class="form-control inputsmall" type="text" id="markup_<?php echo $item_details->id?>" value="<?php echo isset($item_details->markup) ? $item_details->markup : ''?>" name="markup[]"/>--->
                      </td>
                      <td><span id="linetotal_<?php echo $item_details->id?>"><?php echo format_number((($item_details->price*(1-$item_details->discount/100))-$item_details->discount_value+$item_details->markup)*$item_details->quantity)?></span>
                       <!--- <input type="hidden" value="<?php echo format_number((($item_details->price*(1-$item_details->discount/100))-$item_details->discount_value+$item_details->markup)*$item_details->quantity)?>" id="linetotalval_<?php echo $item_details->id?>"/>--->
                        <input type="hidden" value="<?php echo format_number((($item_details->price*(1-$item_details->discount/100))-$item_details->discount_value)*$item_details->quantity)?>" id="linetotalval_<?php echo $item_details->id?>"/>
                      </td>
                      <input type="hidden" value="<?php echo $item_details->location_id; ?>" name="locations[]" id="locations">
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
              <!--<div class="control-group width60">
                <input type="text" autocomplete="off" class="form-control" placeholder="Select Customer" id="selcustomer" style="display: inline;" />
                <a id="add_customer" href="#modalAddCustomer" data-toggle="modal" class="btn btn-default"><i class="fa fa-plus"></i></a>
                <div class="selectHolder" id="selectHolderSup"></div>
              </div>-->
              <div class="control-group width60">
                <!--<address class="filled alert alert-success top10" style="<?php /*echo !isset($invoice_details)?'display:none':''*/?>">
                  <strong id="customer_name"><?php /*echo isset($invoice_details->name)?$invoice_details->name:''*/?></strong><br/>
                  <span id='customer_address'><?php /*echo isset($invoice_details->address)?$invoice_details->address:''*/?></span><br/>
                  <span id='customer_phone'><?php /*echo isset($invoice_details->phone)?$invoice_details->phone:''*/?></span><br/>
                  <span id='customer_email'><?php /*echo isset($invoice_details->email)?$invoice_details->email:''*/?></span><br/>
                  
                  <i class="icon-map-marker pull-right"></i>
                </address>-->
                <input type="hidden" name="customer_id" id="customer_id" value="0">
              <!--</div>-->
              <div class="control-group width60 top10">
                <label class="control-label" for="date">Discount</label>
                <input type="text" value="<?php echo isset($invoice_details->discount) ? $invoice_details->discount : ''?>" class="form-control" name="discount" id="discount" />
              </div>
              <div class="control-group width60">
                <label class="control-label" for="date">Date</label>
                <div class="controls">
                  <input type="text" class="datepicker form-control" name="date" value="<?php echo isset($invoice_details->date) ? date('m/d/Y',strtotime($invoice_details->date)) : date('m/d/Y')?>" required />
                </div>
              </div>
              <div class="control-group width60 top10">
                <label class="control-label" for="date">Payment Type</label>
                <select id="payment_type" name="payment_type" class="form-control">
                  <option value="cash" <?php echo isset($invoice_details->payment_type) && $invoice_details->payment_type=='cash' ? 'selected' : ''?>>Cash</option>
                  <option value="credit_card" <?php echo isset($invoice_details->payment_type) && $invoice_details->payment_type=='credit_card' ? 'selected' : ''?>>Credit Card</option>
                  <option value="cheque" <?php echo isset($invoice_details->payment_type) && $invoice_details->payment_type=='cheque' ? 'selected' : ''?>>Cheque</option>
                </select>
              </div>
              <div class="control-group width60 top10" id="cardtype_info" <?php echo !isset($invoice_details->payment_type) || $invoice_details->payment_type!='credit_card' ? 'style="display:none"' : ''?>>
                <label class="control-label" for="date">Card Type</label>
                <select id="card_type" name="card_type" class="form-control">
                  <option value="visa" <?php echo isset($invoice_details->card_type) && $invoice_details->card_type=='visa' ? 'selected' : ''?>>VISA</option>
                  <option value="master" <?php echo isset($invoice_details->card_type) && $invoice_details->card_type=='master' ? 'selected' : ''?>>Master Card</option>
                </select>
              </div>
              <div class="control-group width60 top10" id="approvalcode_info" <?php echo !isset($invoice_details->payment_type) || $invoice_details->payment_type!='credit_card' ? 'style="display:none"' : ''?>>
                <label class="control-label" for="date">Approval Code</label>
                <input type="text" id="approval_code" class="form-control" name="approval_code" value="<?php echo isset($invoice_details->approval_code) ? $invoice_details->approval_code : ''?>"/>
              </div>
              <div class="control-group width60 top10" id="checknumber_info" <?php echo !isset($invoice_details->payment_type) || $invoice_details->payment_type!='cheque' ? 'style="display:none"' : ''?>>
                <label class="control-label" for="date">Cheque Number</label>
                <input type="text" id="check_num" class="form-control" name="check_num" value="<?php echo isset($invoice_details->check_num) ? $invoice_details->check_num : ''?>"/>
              </div>
              <div class="control-group">
                <label class="control-label" for="remark">Remarks</label>
                <div class="controls">
                  <textarea class="form-control" rows="6" name="remark"> <?php echo isset($invoice_details->remark) ? $invoice_details->remark : ''?></textarea>
                </div>
              </div>
              <div class="control-group">
                <p class='error'></p>
              </div>
              <div class="submit form-group clearfix top10">
				<input type="button" class="btn btn-success pull-right" style="margin-left:10px;" name="savedraft" id="savedraft" value="Save">
				<input type="submit" id="btnCompleteSales" class="btn btn-success pull-right" name="save" value="Complete Sales">
				<input type="button" id="btnCompleting" class="btn btn-success pull-right" name="completing" value="Completing..." style="display:none;">
              </div>
            </div>
            <input type="hidden" name="draft" id="draft" value="<?php echo ((isset($invoice_details->draft) && ($invoice_details->draft <> 'N')) || !isset($invoice_details->draft)) ? 'draft' : ''; ?>"/>
          </form>
        </div>
      </div>                
    </div>
  </div>
</div>
<?php $this->load->view('templates/add_customer')?>
<div id="saved" class="label label-default">Saved</div>
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

	$('body').on('click', '#savedraft', function () {
		$('#draft').val('draft');
		autosave();
	});

    

	autosave = function () {
		$.ajax({
			type: "POST",
			url: $('#cs_form').attr('action'),
			data: $('#cs_form').serialize()
		}).done(function (msg) {
			$('#cs_form').attr('action', '<?php echo base_url()?>retail/save_cs/' + msg);
			if (msg > 0) {
				$('#saved').show();
			}
		});
		$('#saved').hide();
	}

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
    var parent = $(this).parent().parent();

    parent.find('[id*=price_]').val($(this).val());
    var sell_price = parent.find('[id*=price_]').val();
    var quantity = parent.find('[id*=quantity_]').val();
    var discount = (typeof parent.find('[id*=discount_]').val() == 'undefined') ? 0 : parent.find('[id*=discount_]').val();
    var discount_value = (typeof parent.find('[id*=discountvalue_]').val() == 'undefined') ? 0 : parent.find('[id*=discountvalue_]').val();

    // var markup = (typeof parent.find('[id*=markup_]').val()) ? 0 : parent.find('[id*=markup_]').val();

    if (discount == '') {
      discount = 0 + '-';
    }

    var desc_arr = discount.split('-');

    discount = parseInt(desc_arr[0]);
    // var item_total = roundToTwo((sell_price * (1 - discount / 100) - discount_value + (1 * markup)) * quantity);
    var item_total = roundToTwo((sell_price * (1 - discount / 100) - discount_value ) * quantity);

    if (desc_arr.length > 1 && desc_arr[1] != '' && desc_arr[1] != '-') {

      discount = parseInt(desc_arr[1]);
      item_total = roundToTwo(item_total * (1 - discount / 100));
    }
    // var item_total=roundToTwo((sell_price*(1-discount/100)-discount_value+(1*markup))*quantity);
    parent.find('[id*=linetotal_]').html(item_total.toFixed(2));
    parent.find('[id*=linetotalval_]').val(item_total.toFixed(2));
    update_total();
  });

//  Second Discount value
  $('#discount').change(function(){

      var total=$(document).find('[id*=linetotalval_]').val();
      var current_disc=$(this).val();
      var item_total=(current_disc/100)*total;
      var total=roundToTwo(total-item_total);
      $('#total').text(total);

  });

  $('body').on('click','[id*="retailitem_"]',function(){
    var id=$(this).attr('id');
     var location_id=$(this).find('.location_id').val();
    if(location_id=="undefined" || location_id=='')
    {
      alert('please Update the Item Location in order to make Create S.O Thank you !!!');
      return false;
    }
    var arr=id.split('_');
    $.ajax({
          type: "POST",
          url: "<?php echo base_url() ?>retail/selectInvoiceItem",
          data: {item_id:arr[1],location:location_id}
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
  $('body').on('keyup','[id*="quantity_"],[id*="discount_"],[id*="discountvalue_"]',function(){
    var id = $(this).attr('id');
    var arr = id.split('_');
    var parent = $(this).parent().parent();
    var sell_price = parent.find('#price_' + arr[1]).val();
    var quantity = parent.find('#quantity_' + arr[1]).val();
    var discount = (typeof parent.find('[id*=discount_]').val() == 'undefined') ? 0 : parent.find('[id*=discount_]').val();
    var discount_value = (typeof parent.find('[id*=discountvalue_]').val() == 'undefined') ? 0 : parent.find('[id*=discountvalue_]').val();
    // var markup = (typeof parent.find('[id*=markup_]').val()) ? 0 : parent.find('[id*=markup_]').val();

    if (discount == '') {
      discount = 0 + '-';
    }
    var desc_arr = discount.split('-');

    discount = parseInt(desc_arr[0]);
    // var item_total = roundToTwo((sell_price * (1 - discount / 100) - discount_value + (1 * markup)) * quantity);
    var item_total = roundToTwo((sell_price * (1 - discount / 100) - discount_value ) * quantity);
    if (desc_arr.length > 1 && desc_arr[1] != '' && desc_arr[1] != '-') {
      discount = parseInt(desc_arr[1]);
      item_total = roundToTwo(item_total * (1 - discount / 100));
    }


    parent.find('#linetotal_' + arr[1]).html(item_total.toFixed(2));
    parent.find('#linetotalval_' + arr[1]).val(item_total.toFixed(2));
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

  $('body').on('click','.searchcustomeropt', function (){
    var selected = $(this).data('selected');
    select_customer(selected);
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
      $('.error').html('');
		document.getElementById('btnCompleteSales').style['display']='none';
		document.getElementById('btnCompleting').style['display']='inline';
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

  $('#payment_type').change(function(){
    if($(this).val()=='credit_card') {
      $('#checknumber_info').hide();
      $('#cardtype_info').show();
      $('#approvalcode_info').show();
    } else if($(this).val()=='cheque') {
      $('#cardtype_info').hide();
      $('#approvalcode_info').hide();
      $('#checknumber_info').show();
    } else {
      $('#cardtype_info').hide();
      $('#approvalcode_info').hide();
      $('#checknumber_info').hide();
    }
  });
})
</script>
<?php $this->load->view('templates/footer')?>