<?php $this->load->view('templates/header');?>
<div class="container">
<!-- ==================== ROW ==================== -->
<div class="row">
    <div class="col-md-12">
        <!-- ==================== SPAN12 FLOATING BOX ==================== -->
        <div class="widget">
            <div class="widget-head">Create D.O</div>
            <div class="widget-content">
              	<form method="post" action="<?php echo base_url()?>purchasing/save_do" data-validate="parsley">
                	<input type="hidden" id="invoice_id" name="invoice_id" value="<?php echo isset($invoice_details)?$invoice_details->do_id:''?>">
                    <div class="col-md-12 top10 clearfix">
                		<div class="widget">
                        	<div class="widget-head">
								Items				
							</div>
							<div class="widget-content">
                                	<table class="table table-bordered">
                                		<thead>
                                			<th>Stock No.</th>
                                			<th>Description</th>
                                			<th>Quantity</th>
                                			<th></th>
                                		</thead>
                                		<tbody id="i_items">
                                			<tr id="tr_newitem">
                                				<td><input id="stock_num" type="text" class="form-control" placeholder="Search by Stock No." /></td>
                                				<td>
                                					<input id="description" autocomplete="off" class="form-control" type="text" placeholder="Search by item description" />
                                					<div class="selectHolder" id="selectHolderDesc"></div>
                                				</td>
                                				<td><input class="inputsmall form-control" id="item_qty" type="text" /></td>
                                				<td class="actions">
                                					<input type="hidden" id="item_id">
                                					<a class="btn btn-xs btn-success" id="additem" role="button">
                                          				<i class="fa fa-check"></i>
                                        			</a>
                                        		</td>
                                			</tr>
                                			<?php if(isset($invoice_items)){
                                					foreach($invoice_items as $i_item):?>
                                					<tr class="iitems" id="iitem_<?php echo $i_item->iitem_id?>">
														<td><?php echo $i_item->stock_num?></td>
														<td><?php echo $i_item->description?></td>
														<td><?php echo $i_item->quantity?></td>
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
                        <div class="form-group col-md-12 clearfix">
                        	<div class="form-group col-md-3 clearfix">
	                        	<label class="control-label" for="due_date">Select Outlet</label>
								<div class="controls">
									<select name="outlet_id" class="form-control">
										<?php foreach($outlets as $outlet) {?>
											<option value="<?php echo $outlet->outlet_id?>" <?php echo isset($invoice_details->outlet_id) && $invoice_details->outlet_id==$outlet->outlet_id ? 'selected' : '' ?>><?php echo $outlet->name?></option>
										<?php }?>
									</select>
								</div>
							</div>
                        </div>
                        <p class='error'></p>
	                    <div class="pull-right submit form-group">
		                    <input type="submit" class="btn btn-default" name="save" value="Save">
		                    <input type="submit" class="btn btn-success" name="publish" value="Send">
	                    </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- ==================== END OF SPAN12 FLOATING BOX ==================== --> 
    </div>
</div>
<!-- ==================== END OF ROW ==================== -->
</div>
<script>
$(document).ready(function(){

	$('.submit :submit').click(function(){
		if(!($('#i_items tr').length>1))
		{
			$('.error').html('You have to select at least one item!');
			return false;
		}
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

	$('#description').keyup(function(){
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
		  	$('#stock_num').val(data.stock_num);
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
				  url: "<?php echo base_url() ?>purchasing/addDOItem/",
				  data: {invoice_id:$('#invoice_id').val(),item_id:$('#item_id').val(),qty:$('#item_qty').val()}
				}).done(function( msg ) {
					if(msg=="All fields are mandatory!")
					{
						$('.error').html('All fields are mandatory!');
						return false;
					}
					else{
						var result=JSON.parse(msg);
						$('#i_items').append(result.content);
						$('#invoice_id').val(result.do_id);
					  	$('#tr_newitem input').val('');
				    }
				});

		} else {
			$('.error').html('Please select an item.');
		}
		
	});

	$('body').on('click','.deleteitem',function(){
		var id=$(this).attr('id');
		var arr=id.split('_');
		$.ajax({
		  url: "<?php echo base_url() ?>purchasing/deleteDoItem/"+arr[1],
		}).done(function( remove_id ) {
			$('#iitem_'+remove_id).remove();
		});
	});

});

</script>
<?php $this->load->view('templates/footer');?>