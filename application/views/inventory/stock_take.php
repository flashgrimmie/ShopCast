<?php $this->load->view('templates/header'); ?>


    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <div class="row top10">
            <div class="col-md-12">
                <select id="brand_filter" class="form-control pull-left" style="width:20%">
                    <option value="">All Brands</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?php echo $brand->brand ?>"><?php echo $brand->brand ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="category_filter" class="form-control pull-left" style="width:20%">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category->category ?>"><?php echo $category->category ?></option>
                    <?php endforeach; ?>
                </select>
                <a id="generate_template" href="<?php echo base_url() ?>inventory/generate_st_template" download
                   class="btn btn-primary pull-right mb clearform">
                    Generate Template
                </a>
                <a id="import_files" class="btn btn-default pull-right right10 mb clearform">
                    <icon class="fa fa-upload"></icon>
                    &nbsp;Import Stock Take
                </a>

                <form id="upload" method="post" action="<?php echo base_url() ?>inventory/importStockTake"
                      enctype="multipart/form-data">
                    <input type="file" id="file" name="file" style="display:none"/>
                </form>
            </div>
        </div>
        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Stock Take</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper">
                            <table id="itable" class="table table-striped table-bordered table-hover">
                                <thead>
                                <th>Stock Code</th>
                                <th>Part No.</th>
                                <th>Description</th>
                                <th>Car Model</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Cost Price</th>
                                <th>Sell Price</th>
                                <th>Last Sell Price</th>
                                <th>Quantity</th>
                                <th>Stock Take</th>
                                <th>Reason</th>
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

            var oTable = $('#itable').dataTable({
                "bServerSide": true,
                "sPaginationType": "full_numbers",
                "sAjaxSource": "<?php echo base_url() ?>inventory/getStockTake",
                "sServerMethod": "POST",
                "aoColumns": [
                    {"aaData": "0", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": "4", 'sClass': "center"},
                    {"aaData": "5", 'sClass': "center"},
                    {"aaData": "6", 'sClass': "center"},
                    {"aaData": "7", 'sClass': "center"},
                    {"aaData": "8", 'sClass': "center"},
                    {"aaData": "9", 'sClass': "center"},
                    {"aaData": "10", 'sClass': "center"},
                    {"aaData": "11", 'sClass': "center"}
                ]
            });

            $('#import_files').click(function () {
                $('#file').click();
            });
            $('#file').change(function () {
                $('#upload').submit();
            });

            $('#brand_filter,#category_filter').change(function () {
                $('#generate_template').attr('href', '<?php echo base_url()?>inventory/generate_st_template?brand=' + $('#brand_filter').val() + '&category=' + $('#category_filter').val());
            })

        });
    </script>


<?php $this->load->view('templates/footer'); ?>