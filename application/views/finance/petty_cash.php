<?php $this->load->view('templates/header');?>
<!-- ==================== WIDGETS CONTAINER ==================== -->
<div class="container">

	<div class="row top10">
		<div class="col-md-3">
			<form action="<?php echo base_url() ?>finance/petty_cash" method="post">
				<select name="month" >
					<option selected value=''>--Select Month--</option>
					<option value='<?php echo date('Y').'-';?>01'>Janaury</option>
					<option value='<?php echo date('Y').'-';?>02'>February</option>
					<option value='<?php echo date('Y').'-';?>03'>March</option>
					<option value='<?php echo date('Y').'-';?>04'>April</option>
					<option value='<?php echo date('Y').'-';?>05'>May</option>
					<option value='<?php echo date('Y').'-';?>06'>June</option>
					<option value='<?php echo date('Y').'-';?>07'>July</option>
					<option value='<?php echo date('Y').'-';?>08'>August</option>
					<option value='<?php echo date('Y').'-';?>09'>September</option>
					<option value='<?php echo date('Y').'-';?>10'>October</option>
					<option value='<?php echo date('Y').'-';?>11'>November</option>
					<option value='<?php echo date('Y').'-';?>12'>December</option>
				</select>
				<input type="submit" class="btn btn-success input-sm" value="Go"/>
			</form>
		</div>
		<div class="col-md-3">
			<h4><a href="<?php echo base_url()?>finance/petty_cash_history">Available Amount: <?php echo format_price(isset($petty_cash_amount->balance) ? $petty_cash_amount->balance : 0)?></a></h4>
		</div>
		<div class="col-md-6 pull-right">
			<div class="pull-right">
				<a href="#addOneTime" role="button" data-toggle="modal" class="btn btn-primary">
	                <i class="icon-plus-sign"></i> Add Expenses
	            </a>
				<?php $query=$this->dba->get_like_records('petty_cash_amounts','',array('date'=>date('Y-m')),true);
					  if($query){ ?>
							<a role="button" data-toggle="modal" class="btn btn-primary" id="check">
								<i class="icon-plus-sign"></i> Set Petty Cash Amount
							</a>
				<?php } else{ ?>
	            <a href="#addPettyCash" role="button" data-toggle="modal" class="btn btn-primary">
	                <i class="icon-plus-sign"></i> Set Petty Cash Amount
	            </a>
				<?php } ?>

				<?php $query=$this->dba->get_like_records('petty_cash_amounts','',array('date'=>date('Y-m')),true);
				if(!$query){ ?>
				<a  role="button" data-toggle="modal" class="btn btn-primary"  id="top_up_petty_cash">
					<i class="icon-plus-sign"></i> Top up petty cash
				</a>
				<?php } else{ ?>
					<a href="#topUpPettyCash" role="button" data-toggle="modal" class="btn btn-primary">
						<i class="icon-plus-sign"></i> Top up petty cash
					</a>
				<?php } ?>

        	</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-head">Petty Cash</div>
				<div class="widget-content">

                    <table class="table table-striped table-bordered table-hover" id="itable">
                        <thead>
                            <th class="center">Type</th>
                            <th class="center">Cost</th>
                            <th class="center">Staff</th>
                            <th class="center">Date</th>
                            <th class="center">Action</th>
                        </thead>
						<tfoot>
						<tr>
							<th class="center">Expense Total</th>
							<th class="center" id="total"></th>
							<th class="center">Full Total</th>
							<th class="center"><?php echo isset($cash_total)? $cash_total:''; ?></th>
							<th class="center"></th>
						</tr>
						</tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>   
</div> 


<div id="addOneTime" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addOneTime">
    <div class="modal-dialog">
        <div class="modal-content">
	        <div class="modal-header">
	            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	            <h3 class="modal-title">Add Petty Cash</h3>
	        </div>
	
	        
		        <form class="form-horizontal" method="post" action="<?php echo base_url()?>finance/insert_petty_cash">
			        <div class="modal-body">
			        	<div class="form-group">
							<label class="col-lg-2 control-label">Type</label>
							<div class="col-lg-8">
								<input type="text" name="name" placeholder="Enter Expences Description" class="form-control"></div>
						</div>

						<div class="form-group">
							<label class="col-lg-2 control-label">Cost</label>
							<div class="col-lg-8">
								<div class="input-group">
								  <span class="input-group-addon">$</span>
								  <input type="text" name="value" placeholder="Enter Amount" class="form-control">
								</div>
							</div>
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

<div id="addPettyCash" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addPettyCash">
    <div class="modal-dialog">
        <div class="modal-content">
	        <div class="modal-header">
	            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	            <h3 class="modal-title">Add Petty Cash</h3>
	        </div>
	
	        
		        <form class="form-horizontal" method="post" action="<?php echo base_url()?>finance/setPettyCashAmount">
			        <div class="modal-body">
						<h4 style="color:#0a0">Currently Available: <?php echo format_price(isset($petty_cash_amount->balance) ? $petty_cash_amount->balance : 0)?></h4>
						<br/>
						<div class="form-group">
							<label class="col-lg-4 control-label">Amount</label>
							<div class="col-lg-8">
								<div class="input-group">
								  <span class="input-group-addon">$</span>
	 							  <input type="text" name="value" value="<?php echo isset($petty_cash_amount->value) ? $petty_cash_amount->value : ''?>" placeholder="Enter Petty Cash Amount" class="form-control">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Payment Type</label>
							<div class="col-lg-8">
								<select name="payment_type" class="form-control">
									<option value="cash">Cash</option>
									<option value="cheque">Cheque</option>
								</select>
							</div>
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
<div id="topUpPettyCash" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="topUpPettyCash">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h3 class="modal-title">Add Top Up Value</h3>
				</div>


				<form class="form-horizontal" method="post" action="<?php echo base_url()?>finance/topUpPettyCash">
					<div class="modal-body">
						<h4 style="color:#0a0">Currently Available: <?php echo format_price(isset($petty_cash_amount->balance) ? $petty_cash_amount->balance : 0)?></h4>
						<br/>
						<div class="form-group">
							<label class="col-lg-4 control-label">Amount</label>
							<div class="col-lg-8">
								<div class="input-group">
									<span class="input-group-addon">$</span>
									<input type="text" name="value" placeholder="Enter Top Up Value" class="form-control">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Payment Type</label>
							<div class="col-lg-8">
								<select name="payment_type" class="form-control">
									<option value="cash">Cash</option>
									<option value="cheque">Cheque</option>
								</select>
							</div>
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


<script>
$(document).ready(function(){

	$('.dtpicker').datepicker({
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true,
			dateFormat: 'MM yy',

			onClose: function () {
				var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
				var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
				$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
			},
			beforeShow: function () {
				$(this).datepicker("hide");
				$("#ui-datepicker-div").addClass("hide-calendar");
				$("#ui-datepicker-div").addClass('MonthDatePicker');
				$("#ui-datepicker-div").addClass('HideTodayButton');
			}

	});
  var total=0;
  var oTable = $('#itable').dataTable({
    "bServerSide": true,
    "sAjaxSource": "<?php echo base_url() ?>finance/getPettyCash",
    "sServerMethod": "POST",
	"sPaginationType":"full_numbers",
    "aaSorting": [[3, 'desc']],
	"fnServerParams": function (aoData) {
		  aoData.push({"name": "date", "value": "<?php echo $date_filter;?>"});
	  },
    "aoColumns": [
      { "aaData": "0", "sSortDataType": "dom-text", "sType": "numeric", 'sClass': "center" },
      { "aaData": "1", 'sClass': "center" },
      { "aaData": "2", 'sClass': "center" },
      { "aaData": "3", 'sClass': "center" },
      { "aaData": "4", 'sClass': "center" },
    ],
    "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
    		var html='';
		//console.log(aData);
		console.log(aData);
			total = parseFloat(total)+parseFloat(aData[9]);
	     	$('#total').html(total);

    		if(aData[6]==1) {
    			html='<a class="btn btn-danger btn-xs delete-action" href="#modalDelete" role="button" data-toggle="modal" data-href="<?php echo base_url() ?>finance/delete_petty_cash/' + aData['5'] + '"><i class="fa fa-times"></i></a>';
    		}
            $('td:eq(4)', nRow).html(html);
            return nRow;
        }
	});
	total=0;
});
</script>


<script>
	$(document).ready(function(){
		$('#check').click(function(){
			alert('You have already setuped the petty cash amount. To Topup your amount please press the top up button. Thank you!!');
		});

		$('#top_up_petty_cash').click(function(){
			alert('Please set up petty cash first in order to top up. Thank you!!');
		});

	});
</script>

<?php $this->load->view('templates/footer');?>