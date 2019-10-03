<?php $this->load->view('templates/header');?>
<div class="container">
                <!-- ==================== ROW ==================== -->
                <div class="row">
                    <div class="col-md-12">
                        <!-- ==================== SPAN12 FLOATING BOX ==================== -->
                        <div class="widget">
                            <div class="widget-head">Create P.I</div>
                            <div class="widget-content">
          <form id="cs_form" method="post" action="<?php echo base_url()?>purchasing/save_pi" data-validate="parsley">
	                                <div class="col-md-12 clearfix">
	                                	<div class="col-md-6 top10">
	                                		<input type="text" autocomplete="off" class="form-control" placeholder="Select Supplier" id="selcustomer" />
	                                		<div class="selectHolder" id="selectHolderSup"></div>
	                                	</div>

	                                	<div class="col-md-5 pull-right form-horizontal">
			                                	<div class="control-group">
													<label class="control-label">Invoice Number</label>
													<div class="controls">
													    <input type="text" class="form-control" name="invoice_num" value="<?php echo  isset($invoice_details->invoice_num)?$invoice_details->invoice_num:''?>" data-required="true"/>
													</div>
												</div>
										</div>

	                                </div>
	                                <div class="col-md-12 clearfix">
	                                	<div class="col-md-4">
	                                		<address class="filled alert alert-success top10" style="<?php echo !isset($invoice_details)?'display:none':''?>">
			                                  <strong id="customer_name"><?php echo isset($invoice_details->name)?$invoice_details->name:''?></strong><br/>
			                                  <span id='customer_address'><?php echo isset($invoice_details->address)?$invoice_details->address:''?></span><br/>
			                                  <span id='customer_phone'><?php echo isset($invoice_details->phone)?$invoice_details->phone:''?></span><br/>
			                                  <span id='customer_email'><?php echo isset($invoice_details->email)?$invoice_details->email:''?></span><br/>
			                                  
			                                  <i class="icon-map-marker pull-right"></i>
			                                </address>
	                                	</div>


	                                	<div class="col-md-5 pull-right form-horizontal">
												  <div class="control-group">
												    <label class="control-label" for="due_date">Invoicing Date</label>
												    <div class="controls">
												      <input type="text" class="datepicker form-control" name="issue_date" value="<?php echo  isset($invoice_details->issue_date)?$invoice_details->issue_date:''?>" data-required="true"/>
												    </div>
												  </div>
				                                <input type="hidden" id="invoice_id" name="invoice_id" value="<?php echo isset($invoice_details->pi_id)?$invoice_details->pi_id:''?>">
				                                <input type="hidden" id="supplier_id" name="supplier_id" value="<?php echo isset($invoice_details->supplier_id)?$invoice_details->supplier_id:''?>">

	                                	</div>
	                                </div>
                                	<div class="col-md-12 top10 clearfix">
                                		<div class="widget">
		                                	<div class="widget-head">
												Items				
											</div>
											<div class="widget-content">
				                                	<table class="table table-bordered">
				                                		<thead>
				                                			<th>Description</th>
				                                			<th>Cost</th>
				                                			<th>Quantity</th>
				                                			<th>Total Cost</th>
				                                			<th></th>
				                                		</thead>
				                                		<tbody id="i_items">
				                                			<tr id="tr_newitem">
				                                				<td>
				                                					<input id="description" autocomplete="off" class="form-control" type="text" placeholder="Search by item description" />
				                                					<div class="selectHolder" id="selectHolderDesc"></div>
				                                				</td>
				                                				<td><input class="inputsmall form-control" id="item_cost" type="text" /></td>
				                                				<td><input class="inputsmall form-control" id="item_qty" type="text" /></td>
				                                				<td id="item_total"></td>
				                                				<td class="actions">
				                                					<input type="hidden" id="item_id">
				                                					<input type="hidden" id="item_total_val">
				                                					<a class="btn btn-xs btn-success" id="additem" role="button">
				                                          				<i class="fa fa-check"></i>
				                                        			</a>
				                                        		</td>
				                                			</tr>
				                                			<?php if(isset($invoice_items)){
				                                					foreach($invoice_items as $i_item):?>
				                                					<tr class="iitems" id="iitem_<?php echo $i_item->iitem_id?>">
																		<td><?php echo $i_item->description?></td>
																		<td><?php echo format_price($i_item->cost)?></td>
																		<td><?php echo $i_item->quantity?></td>
																		<td><?php echo format_price($i_item->quantity*$i_item->cost)?>
																		<input type="hidden" class="item_price" value="<?php echo $i_item->quantity*$i_item->cost?>"/>
																		</td>
																		<td class="actions">
																			<a class="btn btn-xs btn-danger deleteitem" id="deleteitem_<?php echo $i_item->iitem_id?>" role="button">
																                <i class="fa fa-times delete"></i>
																            </a>
																        </td>
																	</tr>
				                                			<?php 	endforeach;
				                                				  } ?>
				                                		</tbody>
				                                	</table>	
				                            </div>
				                        </div>
			                            <p class='error'></p>
			                            <div class="form-horizontal col-md-12 clearfix pull-right">
				                            <div class="control-group pull-left">
							                    <label class="checkbox-inline">
					                              <input type="checkbox" name="publish" value="1"> Received
					                            </label>
						                    </div>
			                            	<div class="control-group pull-right">
			                            		<label class="control-label">Total: <span id="subtotal"><?php echo isset($invoice_details->subtotal)?format_price($invoice_details->subtotal):''?></span></label><br/>
			                            	</div>
			                            	<div class="clearfix"></div>
				                        	<div class="control-group pull-right top10">
				                        		<input type="text" class="form-control dtpicker" name="additional_expenses" id="additional_expenses" placeholder="Expenses" value="<?php echo isset($invoice_details->additional_expenses) ? $invoice_details->additional_expenses : ''?>"/>
				                        		<span class="btn btn-default add_on"><b>$</b></span>
				                        	</div>
				                        	<div class="clearfix"></div>
				                        	<div class="control-group pull-right">
				                        		<h4>Landed cost price: <span id="landed_cost"><?php echo isset($invoice_details->landed_cost) ? format_price($invoice_details->landed_cost,'BND') : ''?></span></h4>
				                        	</div>
	                        			</div>
					                    <div class="pull-right submit form-group">
						                    <input type="submit" class="btn btn-success" id="submit" name="save" value="Save">
					                    </div>
	                                </div>
	                                <input type="hidden" name="draft" id="draft"/>
	                            </form>
                            </div>
                        </div>
                        <!-- ==================== END OF SPAN12 FLOATING BOX ==================== --> 

                    </div>

                </div>
                <!-- ==================== END OF ROW ==================== -->
               

</div>
<div id="saved" class="label label-default">Saved</div>
<script>
$(document).ready(function(){
	$('#selcustomer').keyup(function(event) {
		var customer = $('#selcustomer').val();
		$('.selectHolder').width($('#selcustomer').width()+24);
		$('#selectHolderSup').show();
		$.ajax({
	        type: "POST",
	        url: "<?php echo base_url() ?>purchasing/searchSupplier",
	        data: {customer:customer}
	      }).done(function( msg ) {
	      		var content='<div><ul id="searchcustomer">';
	      		msg = $.parseJSON(msg);
	      		$.each(msg,function(key,value) {
	      			content+='<li data-selected='+value.supplier_id+' class="searchcustomeropt">'+value.name+'</li>';
	      		});
	      		content+='</ul></div>';
	         	$('#selectHolderSup').html(content);
	      });
	});

	<?php if(!$this->uri->segment(3)) {?>
    $('#draft').val('draft');
	    setInterval(function(){
	      $.ajax({
	        type: "POST",
	        url: $('#cs_form').attr('action'),
	        data: $('#cs_form').serialize()
	      }).done(function( msg ) {
	          if(msg>0) {
	            $('#saved').show();
	          }
	      });
	      $('#saved').hide();
	    },10000);
	  <?php }?>

	$('.submit :submit').click(function(){

		if(!$('#supplier_id').val())
		{
			$('.error').html('Please select a supplier.');
			return false;
		}
		if( $('#i_items tr.iitems').length == 0 )
		{
			$('.error').html('You have to select at least one item!');
			return false;
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

	$('body').on('click','.searchcustomeropt', function (){
		var selected = $(this).data('selected');
		$.ajax({
		  type: "POST",
		  url: "<?php echo base_url() ?>system_setup/getSuppliersDetails/"+selected
		}).done(function( msg ) {
			var data=JSON.parse(msg);
			$('.selectHolder').hide();
			$('.filled').show();
		  	$('#customer_name').html(data.name);
		  	$('#customer_email').html(data.email);
		  	$('#customer_address').html(data.address);
	  		$('#customer_phone').html(data.phone);
	  		$('#supplier_id').val(data.supplier_id);
		});
	});

	$('#stock_num').change(function(){
		var stocknum=$(this).val();
		if(stocknum){
			$.ajax({
			  type: "POST",
			  url: "<?php echo base_url() ?>retail/searchByStockNum/"+stocknum
			}).done(function( msg ) {
				var data=JSON.parse(msg);
				$('#item_id').val(data.item_id);
			  	$('#description').val(data.description);
			  	$('#item_cost').val(data.cost_price);
			  	$('#item_sell').val(data.sell_price);
			  	$('#item_cost_val').val(data.cost_price);
			});
		}
	});

	$('#stock_num').click(function(){
		$('#selectHolderDesc').hide();
	});

	$('#description').keypress(function(e){
		
		if(e.which == 13){

			e.preventDefault();

			var description=$(this).val();
			if(description){
				$('.selectHolder').width($('#description').width()+24);
				$('#selectHolderDesc').show();
					$.ajax({
			            type: "POST",
			            url: "<?php echo base_url() ?>retail/searchByDescription/",
			            data: {description:description}
			          }).done(function( msg ) {
			            $('#searchdesc').html(msg);
						$('#modalSearchStock').modal('show');
			        });
		    }
		}
	});

	$('body').on('click','.searchdescopt', function (){
		var selected = $(this).data('selected');
		$.ajax({
		  type: "POST",
		  url: "<?php echo base_url() ?>retail/searchByItemId/",
		  data: {selected:selected}
		}).done(function( msg ) {
			var data=JSON.parse(msg);
			$('#modalSearchStock').modal('hide');
			$('#item_id').val(data.item_id);
		  	$('#description').val(data.description);
		  	$('#item_cost').val(data.cost_price);
		  	$('#item_cost_val').val(data.cost_price);
		});
	});

	$('#additem').click(function(){
		var item_id = $('#item_id').val();
		if(item_id!='') {
			$('.error').html('');
			$.ajax({
				  type: "POST",
				  url: "<?php echo base_url() ?>purchasing/addPIItem/",
				  data: {invoice_id:$('#invoice_id').val(),item_id:$('#item_id').val(),qty:$('#item_qty').val(),item_cost:$('#item_cost').val()}
				}).done(function( msg ) {
                    var result = JSON.parse(msg);
					if( result.res )
					{
                        var result = JSON.parse(msg);
                        $('#i_items').append(result.content);
                        $('#invoice_id').val(result.pi_id);
                        update_total();
                        $('#tr_newitem input').val('');
                        $('#item_total').html('');
                        $('#item_total_val').val('');
					}
					else{
                        $('.error').html( result.msg );
                        return false;
				    }
				});

		} else {
			$('.error').html('Please select an item.');
		}
		
	});


	$('#item_qty, #item_cost').change(function(){
		var item_total=($('#item_qty').val()*$('#item_cost').val());
                item_total = number_format(item_total, 2, '.', '');
		$('#item_total_val').val(item_total);
		$('#item_total').html(item_total);
//		format_price($('#item_total'));
		update_total();
	});

	$('body').on('click','.deleteitem',function(){
		var id=$(this).attr('id');
		var arr=id.split('_');
		$.ajax({
		  url: "<?php echo base_url() ?>purchasing/deletePiItem/"+arr[1],
		}).done(function( remove_id ) {
			$('#iitem_'+remove_id).remove();
			update_total();
		});
	});

	$('#additional_expenses').change(function(){
  		var total=$('#subtotal').html().replace('$','');
      	var landed_cost=+total+(1*$('#additional_expenses').val());
      	$('#landed_cost').html("$"+roundToTwo(landed_cost));
	});
});

function number_format(number, decimals, dec_point, thousands_sep) {

  number = (number + '')
    .replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}

function update_total()
{
	var subtotal=0;
	$('.item_price').each(function(){
		subtotal+=Number($(this).val());
	});
	$('#subtotal').html('$'+roundToTwo(subtotal));
	var landed_cost=+subtotal+(1*$('#additional_expenses').val());
    $('#landed_cost').html("$"+roundToTwo(landed_cost));
	//format_price($('#subtotal'));
}

</script>
<?php $this->load->view('templates/footer');?>	