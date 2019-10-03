<?php $this->load->view('templates/header'); ?>


    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Stock Table</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper">
                            <table id="itable" class="table table-striped table-bordered table-hover">
                                <thead>
                                <th>PI Id</th>
                                <th>Item Id</th>
                                <th>barcode</th>
                                <th>Part No</th>
                                <th>Description</th>
                                <th>Cost</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Supplier</th>
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
                "sAjaxSource": "<?php echo base_url() ?>purchasing/get_piitems",
                "sServerMethod": "POST",
                "aaSorting": [[0, 'asc']],
                "aoColumns": [
                    {"aaData": "0", "sSortDataType": "dom-text", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": "4", 'sClass': "center"},
                    {"aaData": "5", 'sClass': "center"},
                    {"aaData": "6", 'sClass': "center"},
                    {"aaData": "7", 'sClass': "center"},
                    {"aaData": "8", 'sClass': "center"}
                ]
            });

        });

    </script>




<?php $this->load->view('templates/footer'); ?>