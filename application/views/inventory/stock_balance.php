<?php $this->load->view('templates/header'); ?>


    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
		 <div class="row top10">
            <div class="col-md-12">
				<form action="<?php echo base_url()?>inventory/stock_balance">
				<input type="text" class="form-control dtpicker" style="display:inline;" placeholder="Date From" name="date_from" value="<?php echo $this->input->get('date_from')?>"/>&nbsp;-
				<input type="text" class="form-control dtpicker" style="display:inline;" placeholder="Date To" name="date_to" value="<?php echo $this->input->get('date_to')?>"/>
				
                <select id="brand_filter" name="brand_filter" class="form-control" style="width:20%; display:inline;">
                    <option value="">All Brands</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?php echo $brand->brand ?>"><?php echo $brand->brand ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="category_filter"  name="category_filter" class="form-control" style="width:20%; display:inline;">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category->category ?>"><?php echo $category->category ?></option>
                    <?php endforeach; ?>
                </select>
				<input type="submit" value="Go"/>
				
          <a href="<?php echo base_url() ?>inventory/export_st_balance?date_from=<?php echo $this->input->get('date_from')?>&date_to=<?php echo $this->input->get('date_to')?>&brand_filter=<?php echo $this->input->get('brand_filter')?>&category_filter=<?php echo $this->input->get('category_filter')?>" class="btn btn-default pull-right">Export</a>
					</form>
                              
            </div>
        </div>
        <!-- ==================== TABLE ROW ==================== -->

        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Stock Table</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper">
                            <table id="itable" class="table table-striped table-bordered table-hover">
                                <thead>
                                <th>Stock Code</th>
                                <th>Brand</th>
								<th>Category</th>
                                <th>Description</th>                                
                                <th>Date</th>
                                <th>QTY</th>                                
                                <th>Cost Price</th>
                                <th>Selling Price</th>  
                                </thead>
                            </table>
                        </div>
                        <div class="widget-foot">
                            <br><br>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ==================== END OF BORDERED TABLE FLOATING BOX ==================== -->
    </div>



    <script>
        $(document).ready(function () {
			$('.dtpicker').datepicker();
            var oTable = $('#itable').dataTable({
                "bServerSide": true,
				 "sServerMethod": "GET",
                "aaSorting": [[0, 'asc']],
                "sAjaxSource": "<?php echo base_url() ?>inventory/getStockBalance",
				 "fnServerParams": function ( aoData ) {
				  aoData.push( { "name": "date_from", "value": "<?php echo $this->input->get('date_from')?>" },{ "name": "date_to", "value": "<?php echo $this->input->get('date_to')?>" }, { "name": "brand_filter", "value": "<?php echo $this->input->get('brand_filter')?>" }, { "name": "category_filter", "value": "<?php echo $this->input->get('category_filter')?>" } );
				},
                "aoColumns": [
                    {"aaData": "0", "sSortDataType": "dom-text", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": "4", 'bSearchable': false, 'sClass': "center"},
                    {"aaData": "5", 'bSearchable': false, 'sClass': "center"},
                    {"aaData": "6", 'bSearchable': false, 'sClass': "center"},
                    {"aaData": "7", 'bSearchable': false, 'sClass': "center"},
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    $('td:eq(8)', nRow).html('<a class="btn btn-primary btn-xs btn-details" data-href="' + aData['8'] + '"><i class="fa fa-chevron-down"></i></a>');
                    return nRow;
                }
            });           

        });

    </script>


<?php $this->load->view('templates/footer'); ?>