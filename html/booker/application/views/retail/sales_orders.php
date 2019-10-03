<?php $this->load->view('templates/header'); ?>

    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <?php if (!isset($isactive)) { ?>
            <div class="row top10">
                <div class="col-md-12">
                    <a style="margin-left:5px" href="<?php echo base_url() ?>retail/deleted_so"
                       class="btn btn-default pull-right mb clearform">
                        <i class="fa fa-trash-o"></i>
                    </a>
                    <a href="<?php echo base_url() ?>retail/create_so" class="btn btn-primary pull-right mb clearform">
                        <i class="icon-plus-sign"></i> New S.O
                    </a>
                </div>
            </div>
        <?php } ?>
        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Sales Orders</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                            <table class="table table-bordered dataTable" id="membersTable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>Deposit</th>
                                    <th>Amount Due</th>
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
                "sAjaxSource": "<?php echo base_url() ?>retail/getSOs/<?php echo isset($isactive) ? $isactive : ''?>",
                "sServerMethod": "POST",
                "aaSorting": [[0, 'desc']],
                "aoColumns": [
                    {"aaData": "0", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": "4", 'sClass': "center"},
                    {"aaData": "5", 'sClass': "center"},
                    {"aaData": null, 'sClass': "center"},
                    {"aaData": null, 'bSortable': false, 'bSearchable': false, 'sClass': "center", 'sWidth': '100px'}
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    <?php if(isset($isactive) && $isactive=='N'){?>
                    $('td:eq(6)', nRow).html(aData['6']);
                    var view_file = '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/sales_order/' + aData['0'] + '\')"><i class="fa fa-file"></i></a>';
                    var retrieve = '<a class="btn btn-xs btn-success" href="<?php echo base_url() ?>retail/retrieve_so/' + aData['0'] + '"><i class="fa fa-undo"></i></a>';
                    $('td:eq(7)', nRow).html(view_file + retrieve);
                    <?php } else {?>
                    var payment;
                    if (aData[6]) {
                        var view_cs = '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/cash_sale/' + aData['6'] + '\')" title="View Cash Sale"><i class="fa fa-file"></i></a>';
                        var edit_cs = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>retail/create_cs/' + aData['6'] + '" title="Edit Cash Sale"><i class="fa fa-sign-in"></i></a>';
                        payment = view_cs + edit_cs;
                    } else if (aData[7]) {
                        var view_invoice = '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/invoice/' + aData['7'] + '\')" title="View Invoice"><i class="fa fa-file"></i></a>';
                        var edit_invoice = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>retail/create_invoice/' + aData['7'] + '" title="Edit Invoice"><i class="fa fa-sign-in"></i></a>';
                        payment = view_invoice + edit_invoice;
                    } else {
                        payment = '<a href="<?php echo base_url() ?>retail/create_so/' + aData['0'] + '" class="btn btn-xs btn-info">pending</a>';
                    }
                    $('td:eq(6)', nRow).html(payment);
                    var view_file = '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/sales_order/' + aData['0'] + '\')"><i class="fa fa-file"></i></a>';
                    var edit = '', del = '', ret = '';
                    if (!aData[7] && !aData[6]) {
                        edit = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>retail/create_so/' + aData['0'] + '"><i class="fa fa-pencil"></i></a>';
                        del = '<a class="btn btn-xs btn-danger delete-action" data-toggle="modal" href="#modalDelete" data-href="<?php echo base_url() ?>retail/delete_so/' + aData['0'] + '"><i class="fa fa-times"></i></a>';
                        ret = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>retail/return_so/' + aData['0'] + '"><i class="fa fa-share-square-o"></i></a>';
                    }
                    $('td:eq(7)', nRow).html(view_file + ret + edit + del);
                    <?php }?>
                    return nRow;
                },
            });

            if (window.location.hash) {
                var doc = window.location.hash.substring(1);
                popup('<?php echo base_url() ?>documents/sales_order/' + doc);
            }

            $('body').on('click', '[id*="paymentstatus_"]', function () {
                return false;
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

<?php $this->load->view('templates/footer'); ?>