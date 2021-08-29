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
                                <th>Stock Code</th>
                                <th>Part No.</th>
                                <th>Description</th>
                                <th>Remark</th>
                                <th>Brand</th>
                                <th>Car Model</th>
                                <th>QTY</th>
                                <th>QTY on hand</th>
                                <th>Cost</th>
                                <th>Price</th>
                                <th>Pricing Info</th>
                                <th>Location</th>
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
                "sAjaxSource": "<?php echo base_url() ?>inventory/getStockLists",
                "sServerMethod": "POST",
                "aaSorting": [[0, 'asc']],
                "aoColumns": [
                    {"aaData": "0", "sSortDataType": "dom-text", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": "4", 'sClass': "center"},
                    {"aaData": "5", 'sClass': "center"},
                    {"aaData": "6", 'bSearchable': false, 'sClass': "center"},
                    {"aaData": "7", 'bSearchable': false, 'sClass': "center"},
                    {"aaData": "8", 'sClass': "center"},
                    {"aaData": "9", 'sClass': "center"},
                    {"aaData": "10", 'sClass': "center"},
                    {"aaData": "11", 'sClass': "center"},
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    $('td:eq(13)', nRow).html('<a class="btn btn-primary btn-xs btn-details" data-href="' + aData['13'] + '"><i class="fa fa-chevron-down"></i></a>');
                    return nRow;
                }
            });


            $('body').on('click', '.priceDetails', function (e) {
                var product_id = $(this).data('href');
                var table = $('#priceHistory tbody');
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url() ?>inventory/getPriceHistory/",
                    data: {product_id: product_id}

                }).done(function (response) {
                    response = $.parseJSON(response);
                    if (response.length != '0') {
                        $.each(response, function (key, val) {
                            table.html('<tr class="details_' + product_id + '"><td>' + val.date + '</td><td>' + val.cost + '</td><<tr>');

                        });
                    } else {
                        table.html('<tr><td colspan="2">The item you selected dosn\'t have any price changes.<td></tr>');
                    }

                });
            });

        });

    </script>


    <div id="modalPriceHistory" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalPriceHistory">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Price Details</h3>
                </div>
                <div class="modal-body">
                    <table id="priceHistory" class="table table-striped">
                        <thead>
                        <th>Date</th>
                        <th>Price</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
            </div>
        </div>
    </div>



<?php $this->load->view('templates/footer'); ?>