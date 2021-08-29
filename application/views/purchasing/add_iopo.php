<?php $this->load->view('templates/header');?>
<div class="container">

                <!-- ==================== ROW ==================== -->
                <div class="row">
                    <div class="col-md-12">
                        <!-- ==================== SPAN12 FLOATING BOX ==================== -->
                        <div class="widget">
                            <div class="widget-head">Create Internal Outlet Purchase Order</div>
                            <div class="widget-content">
                              	<form method="post" action="<?php echo base_url()?>purchasing/save_iopo">
	                                <div class="col-md-12 clearfix">
	                                	<div class="col-md-4">
	                                		<address class="filled alert alert-success top10" style="<?php echo !isset($invoice_details)?'display:none':''?>">
			                                  <strong id="customer_name">
			                       		<?php echo isset($invoice_details->name)?$invoice_details->name:''?>
			                       				</strong>

			                       				<br/>
			                                  
			                                  <span id='customer_address'>
			                                  	<?php echo isset($invoice_details->location)?$invoice_details->location:''?></span><br/>
			                                  <span id='customer_phone'><?php echo isset($invoice_details->contact)?$invoice_details->contact:''?></span><br/>
			                                  <span id='customer_email'><?php echo isset($invoice_details->email)?$invoice_details->email:''?></span><br/>
			                                  
			                                  <i class="icon-map-marker pull-right"></i>
			                                </address>
	                                	</div>

				                        <input type="hidden" id="outlet_id" name="outlet_id" value="<?php echo isset($invoice_details->outlet_id)?$invoice_details->outlet_id:''?>">

	                                </div>
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
				                                			<?php if(isset($invoice_items)){
				                                					foreach($invoice_items as $i_item):?>
				                                					<tr class="iitems" id="iitem_<?php echo $i_item->iitem_id?>">
																		<td><?php echo $i_item->stock_num?></td>
																		<td><?php echo $i_item->description?></td>
																		<td style="width: 100px;"><input type="text" class="form-control" name='quantity[]' value="<?php echo $i_item->quantity?>"/></td>
																		</td>
																		<td class="actions">
																			<a class="btn btn-xs btn-danger deleteitem" id="deleteitem_<?php echo $i_item->iitem_id?>" role="button">
																                <i class="fa fa-times delete"></i>
																            </a>
																        </td>
																	</tr>
															<input type="hidden" name='id[]' value="<?php echo $i_item->iitem_id?>"/>
				                                			<?php 	endforeach;
				                                				  } ?>
				                                		</tbody>
				                                	</table>	
				                            </div>
				                        </div>
			                            <p class='error'></p>
			                            
					                    <div class="pull-right submit form-group">
						                    <input type="submit" class="btn btn-success" name="save" value="Save">&nbsp;
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

	$('body').on('click','.deleteitem',function(){
		var id=$(this).attr('id');
		var arr=id.split('_');
		$.ajax({
		  url: "<?php echo base_url() ?>purchasing/deletePoItem/"+arr[1],
		}).done(function( remove_id ) {
			$('#iitem_'+remove_id).remove();
		});
	});

});

</script>
<?php $this->load->view('templates/footer');?>