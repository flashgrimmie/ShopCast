<?php $this->load->view('templates/header');?>


<!-- ==================== WIDGETS CONTAINER ==================== -->
<div class="container">
	<div class="row top10">
		<div class="col-md-4 pull-right">
			<div class="pull-right">
				<a href="#addType" role="button" data-toggle="modal" class="btn btn-primary">
	                <i class="icon-plus-sign"></i> Add Type
	            </a>

	            <a href="#addRecExp" role="button" data-toggle="modal" class="btn btn-danger">
	                <i class="icon-plus-sign"></i> Update Expenses
	            </a>
        	</div>
		</div>
	</div>
	<!-- ==================== TABLE ROW ==================== -->
	<div class="row">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-head">Recuring Expenses</div>
				<div class="widget-content">

                    <table class="table table-striped">
                        <thead>
                            <th>Type</th>
                            <th class="center">Jan</th>
                            <th class="center">Feb</th>
                            <th class="center">Mar</th>
                            <th class="center">Apr</th>
                            <th class="center">May</th>
                            <th class="center">Jun</th>
                            <th class="center">Jul</th>
                            <th class="center">Aug</th>
                            <th class="center">Sep</th>
                            <th class="center">Oct</th>
                            <th class="center">Nov</th>
                            <th class="center">Dec</th>
                            <th class="center"></th>
                        </thead>
                    <?php for($j=0;$j<sizeof($fields);$j++) { ?>
                    <tr id="del_<?php echo $fields[$j] ?>">
                        <td><b><?php echo str_replace('_', ' ', ucwords($fields[$j])) ?></b></td>
                        <?php for($i=1;$i<=12;$i++) { ?>
                            <td class="center"><?php echo isset($recurring_exp[$i][$fields[$j]]) ? format_price($recurring_exp[$i][$fields[$j]]) : '-' ?></td>
                        <?php } ?>
                        <td class="action">
                            <a class="btn btn-danger btn-xs delete-action" href="#modalDelete" data-href="<?php echo base_url()?>finance/delete_recurring/<?php echo $fields[$j] ?>" title="Delete Item" role="button" data-toggle="modal"><i class="fa fa-times"></i></a>
                        </td>
                    </tr> 
                     <?php } ?>
                    </table>

                </div>
			</div>
		</div>
	<!-- ==================== END OF BORDERED TABLE FLOATING BOX ==================== -->
	</div>
</div>

<div id="addType" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addType">
    <div class="modal-dialog">
        <div class="modal-content">
	        <div class="modal-header">
	            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	            <h3 class="modal-title">Add Expense Type</h3>
	        </div>
	
	        
		        <form class="form-horizontal" method="post" action="<?php echo base_url()?>finance/add_recurring">
			        <div class="modal-body">
						<div class="form-group">
							<label class="col-lg-2 control-label">Name</label>
							<div class="col-lg-8">
								<input type="text" name="column" placeholder="Enter expenses name" class="form-control"></div>
						</div>
			    	</div>
		
			    <div class="modal-footer">
                 	<input class="btn btn-danger" type="submit" name="submit" value="Yes">
			    
			        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
			    </div>
		    </form>
		</div>
	</div>
</div>



<div id="addRecExp" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addRecExp">
    <div class="modal-dialog">
        <div class="modal-content">
	        <div class="modal-header">
	            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	            <h3 class="modal-title">Add Expense Type</h3>
	        </div>

	        <form class="form-horizontal" method="post" action="<?php echo base_url()?>finance/insert_recurring">
		        <div class="modal-body">

		        	<?php foreach ($fields as $key => $field) { ?>

                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?php echo str_replace('_',' ',ucfirst($field));?></label>
                            <div class="col-lg-8">
                                <input type="text" placeholder="Expenses for <?php echo $field ?>" class="form-control" name="recuring_<?php echo $key?>" value="<?php echo isset($expenses[$field]) ? $expenses[$field] :'' ?>" />
                            </div>
                    </div>

               		<?php } ?>

		        </div>

		        <div class="modal-footer">
	               	<input class="btn btn-danger" type="submit" name="submit" value="Yes">
			        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
			    </div>
			</form>
		</div>
	</div>
</div>

<?php $this->load->view('templates/footer');?>
