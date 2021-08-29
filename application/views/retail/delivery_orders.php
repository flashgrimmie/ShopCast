<?php $this->load->view('templates/header'); ?>

    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <?php if (!isset($isactive)) { ?>
            <div class="row top10">
                <div class="col-md-12">
					<a id="import_files" href="<?php echo base_url() ?>retail/exportDoReport/N" class="btn btn-default pull-right right10 mb clearform">
						<icon class="fa fa-upload"></icon>
						&nbsp;Export
					</a>
                    <a style="margin-left:5px" href="<?php echo base_url() ?>retail/deleted_do"
                       class="btn btn-default pull-right mb clearform">
                        <i class="fa fa-trash-o"></i>
                    </a>
                    <a href="<?php echo base_url() ?>retail/create_do" class="btn btn-primary pull-right mb clearform">
                        <i class="icon-plus-sign"></i> New D.O
                    </a>
                </div>
            </div>
        <?php } ?>
        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Delivery Orders</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                            <table class="table table-bordered dataTable" id="membersTable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Deposit</th>
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
                "bPaginate": true,
                "sPaginationType": "full_numbers",
                "sAjaxSource": "<?php echo base_url() ?>retail/getDOs/<?php echo isset($isactive) ? $isactive :''?>",
                "sServerMethod": "POST",
                "aaSorting": [[0, 'desc']],
                "aoColumns": [
                    {"aaData": "0", "sType": "numeric", 'sClass': "center", },
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": null, 'sClass': "center"},
                    {"aaData": null, 'bSortable': false, 'bSearchable': false, 'sClass': "center", 'sWidth': '100px'}
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    <?php if(isset($isactive) && $isactive=='N'){?>
                    $('td:eq(4)', nRow).html(aData['6']);
                    var view_file = '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/delivery_order/' + aData['0'] + '\')"><i class="fa fa-file"></i></a>';
                    var retrieve = '<a class="btn btn-xs btn-success" href="<?php echo base_url() ?>retail/retrieve_do/' + aData['0'] + '"><i class="fa fa-undo"></i></a>';
                    $('td:eq(5)', nRow).html(view_file + retrieve);
                    <?php } else {?>
                    var payment;
                    if (aData[4]) {
                        /*  var view_invoice='<a class="btn btn-xs btn-default" href="javascript:popup(\'
                        <?php echo base_url() ?>documents/invoice/' + aData['4'] + '\')" title="View Invoice"><i class="fa fa-file"></i></a>';
                         var edit_invoice='<a class="btn btn-xs btn-default" href="
                        <?php echo base_url() ?>retail/create_invoice/' + aData['4']+'" title="Edit Invoice"><i class="fa fa-sign-in"></i></a>';*/
                        //  payment=view_invoice+edit_invoice;

                        payment = '<a href="<?php echo base_url() ?>retail/create_invoice/' + aData['4'] + '" class="btn btn-xs btn-info">pending</a>';

                    } else {

                        payment = '<a href="<?php echo base_url() ?>retail/create_do/' + aData['0'] + '" class="btn btn-xs btn-info">pending</a>';
                    }
                    $('td:eq(4)', nRow).html(payment);
                    var view_file = '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/delivery_order/' + aData['0'] + '\')"><i class="fa fa-file"></i></a>';
                    var edit = '', del = '';
                    if (!aData[4]) {
                        edit = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>retail/create_do/' + aData['0'] + '"><i class="fa fa-pencil"></i></a>';
                        del = '<a class="btn btn-xs btn-danger delete-action" data-toggle="modal" href="#modalDelete" data-href="<?php echo base_url() ?>retail/delete_do/' + aData['0'] + '"><i class="fa fa-times"></i></a>';
                    }
                    $('td:eq(5)', nRow).html(view_file + edit + del);
                    <?php }?>
                    return nRow;
                },
            });

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