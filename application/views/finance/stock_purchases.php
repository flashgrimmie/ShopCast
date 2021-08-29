<?php $this->load->view('templates/header'); ?>

    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Stock Purchases</div>
                    <div class="widget-content">

                        <table class="table table-striped table-bordered table-hover" id="itable">
                            <thead>
                            <th class="center">Part No.</th>
                            <th class="center">QTY</th>
                            <th class="center">Cost</th>
                            <th class="center">Item Total</th>
                            <th class="center">Date</th>
                            <th class="center">Action</th>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {

            var oTable = $('#itable').dataTable({
                "bServerSide": true,
                "sPaginationType": "full_numbers",
                "sAjaxSource": "<?php echo base_url() ?>finance/getStockPurchases",
                "sServerMethod": "POST",
                "aoColumns": [
                    {"aaData": "0", "sSortDataType": "dom-text", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": "4", 'sClass': "center"},
                    {"aaData": "5", 'sClass': "center"},
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    $('td:eq(5)', nRow).html('<span style="margin:4px"><a class="btn btn-xs btn-primary" href="javascript:popup(\'<?php echo base_url() ?>documents/purchase_invoice/' + aData['5'] + '\')"><i class="fa fa-file-o"></i></a></span>');
                    return nRow;
                }
            });

        });
    </script>

<?php $this->load->view('templates/footer'); ?>