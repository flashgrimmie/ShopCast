<?php $this->load->view('templates/header'); ?>

    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <a href="<?php echo base_url() ?>retail/void_transactions" class="btn btn-default pull-right top10">Void
                    Transactions</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Draft Transactions</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                            <table class="table table-bordered dataTable" id="membersTable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Customer/Supplier</th>
                                    <th>Total Amount</th>
                                    <th>Type</th>
                                    <th></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ==================== END OF BORDERED TABLE FLOATING BOX ==================== -->
    </div>
    <div id="modalVoidTransaction" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="void_transaction" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title">Void Draft Transaction</h3>
                    </div>
                    <div class="modal-body">
                        <div class="control-group top10">
                            <label class="control-label" for="date">Reason</label>
                            <textarea name="reason" class="form-control" rows="6" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" type="submit">Void</button>
                            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            $('body').on('click', '.void_transaction', function () {
                $('#void_transaction').attr('action', $(this).data('href'));
            })

            var oTable = $('#membersTable').dataTable({
                "bServerSide": true,
                "sPaginationType": "full_numbers",
                "sAjaxSource": "<?php echo base_url() ?>retail/getDraftTransactions",
                "sServerMethod": "GET",
                "aaSorting": [[1, 'desc']],
                "aoColumns": [
                    {"aaData": "0", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": "4", 'sClass': "center"},
                    {"aaData": null, 'bSortable': false, 'bSearchable': false, 'sClass': "center", 'sWidth': '100px'}
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var doc;
                    if (aData[4] == 'Invoice') {
                        doc = "invoice";
                    } else if (aData[4] == 'Retail Sale') {
                        doc = "cs";
                    } else if (aData[4] == "Sales Order") {
                        doc = "so";
                    }
                    else if (aData[4] == "Purchase Order") {
                        doc = "po";
                    }
                    var proceed_file = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>retail/create_' + doc + '/' + aData['5'] + '"><i class="fa fa-edit"></i></a>';
                    var discard_file = '<a class="btn btn-xs btn-danger void_transaction" href="#modalVoidTransaction" data-toggle="modal" data-href="<?php echo base_url() ?>retail/discard_' + doc + '/' + aData['5'] + '">Void</a>';
                    $('td:eq(5)', nRow).html(proceed_file + discard_file);
                    return nRow;
                },
            });
        });
    </script>
<?php $this->load->view('templates/footer'); ?>