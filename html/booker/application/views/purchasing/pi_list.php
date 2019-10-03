<?php $this->load->view('templates/header'); ?>

    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <div class="row top10">
            <div class="col-md-12">
                <a href="<?php echo base_url() ?>purchasing/create_pi" class="btn btn-primary pull-right mb clearform">
                    <i class="icon-plus-sign"></i> New P.I
                </a>
            </div>
        </div>
        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Purchase Invoices</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                            <table class="table table-bordered dataTable" id="membersTable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Receiving Date</th>
                                    <th>Invoicing Date</th>
                                    <th>Payment Date</th>
                                    <th>Invoice No.</th>
                                    <th>Price</th>
                                    <th>Supplier</th>
                                    <th>Payment Status</th>
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
    <script>
        $(document).ready(function () {
            var oTable = $('#membersTable').dataTable({
                "bServerSide": true,
                "sPaginationType": "full_numbers",
                "sAjaxSource": "<?php echo base_url() ?>purchasing/getPIs",
                "sServerMethod": "POST",
                "aoColumns": [
                    {"aaData": "0", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": "4", 'sClass': "center"},
                    {"aaData": "5", 'sClass': "center"},
                    {"aaData": "6", 'sClass': "center"},
                    {"aaData": null, 'sClass': "center"},
                    {"aaData": null, 'bSortable': false, 'bSearchable': false, 'sClass': "center", 'sWidth': '100px'}
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var btn_class = 'btn-info';
                    if (aData[7] == 'paid') {
                        btn_class = 'btn-success';
                    }
                    var payment = '<a class="btn btn-xs ' + btn_class + '" id="paymentstatus_' + aData['0'] + '">' + aData['7'] + '</a>';
                    $('td:eq(7)', nRow).html(payment);
                    var view_file = '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/purchase_invoice/' + aData['0'] + '\')"><i class="fa fa-file"></i></a>';
                    var edit = '';
                    if (aData[8] != '1') {
                        edit = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>purchasing/create_pi/' + aData['0'] + '"><i class="fa fa-pencil"></i></a>';
                    }
                    $('td:eq(8)', nRow).html(view_file + edit);
                    return nRow;
                }
            });

            if (window.location.hash) {
                var doc = window.location.hash.substring(1);
                popup('<?php echo base_url() ?>documents/purchase_invoice/' + doc);
            }

            $('body').on('click', '[id*="paymentstatus_"]', function () {
                var id = $(this).attr('id');
                var arr = id.split('_');
                $.ajax({
                    url: "<?php echo base_url() ?>purchasing/payment_status/",
                    type: "POST",
                    data: {id: arr[1]}
                }).done(function (msg) {
                    var json = JSON.parse(msg);
                    $('#' + id).removeClass(json.old);
                    $('#' + id).addClass(json.new);
                    $('#' + id).html(json.status);
                });
            });
        });
    </script>
    <style type="text/css">
        .mainbar .container {
            padding-bottom: 50px;
        }
    </style>

<?php $this->load->view('templates/footer'); ?>