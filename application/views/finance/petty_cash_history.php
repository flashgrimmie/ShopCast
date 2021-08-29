<?php $this->load->view('templates/header');?>

<!-- ==================== WIDGETS CONTAINER ==================== -->
<div class="container">

	<div class="row">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-head">Petty Cash</div>
				<div class="widget-content">

                    <table class="table table-striped table-bordered table-hover" id="itable">
                        <thead>
                            <th class="center">Date</th>
                            <th class="center">Staff</th>
                            <th class="center">Amount</th>
                            <th class="center">Balance (for transfering)</th>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>   
</div> 
<script>
$(document).ready(function(){

 
  var oTable = $('#itable').dataTable({
    "bServerSide": true,
    "sAjaxSource": "<?php echo base_url() ?>finance/getPettyCashHistory",
    "sServerMethod": "POST",
    "aaSorting": [[0, 'asc']],
    "aoColumns": [
      { "aaData": "0", "sSortDataType": "dom-text", "sType": "numeric", 'sClass': "center" },
      { "aaData": "1", 'sClass': "center" },
      { "aaData": "2", 'sClass': "center" },
      { "aaData": "3", 'sClass': "center" },
    ]
	});

});
</script>


<?php $this->load->view('templates/footer');?>