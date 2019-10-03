<?php $this->load->view('templates/header'); ?>
    <style type="text/css">
        .MonthDatePicker {
            display: none;
        }

        .HideTodayButton .ui-datepicker-buttonpane .ui-datepicker-current {
            visibility: hidden;
        }

        .hide-calendar .ui-datepicker-calendar {
            display: none !important;
            visibility: hidden !important
        }
    </style>
    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <div class="row top10">
            <div class="col-md-12">
                <form action="<?php echo base_url() ?>retail/invoices" style="float: left;width: 296px;" method="post">
                    <input type="text" class="form-control dtpicker" placeholder="Date From" name="date_from"
                           value="<?php if ($date_filter != '') {
                               echo date("M Y", strtotime($date_filter));
                           } else {
                               echo '';
                           } ?>"/>

                    <input type="submit" value="Go"/>
                </form>
            </div>
            <?php if (!isset($isactive)) { ?>
                <div class="col-md-12">
					<a id="import_files" href="<?php echo base_url() ?>retail/exportInvoiceReport/N" class="btn btn-default pull-right right10 mb clearform">
						<icon class="fa fa-upload"></icon>
						&nbsp;Export
					</a>
                    <a style="margin-left:5px" href="<?php echo base_url() ?>retail/deleted_invoices"
                       class="btn btn-default pull-right mb clearform">
                        <i class="fa fa-trash-o"></i>
                    </a>
                    <a href="<?php echo base_url() ?>retail/create_invoice"
                       class="btn btn-primary pull-right mb clearform">
                        <i class="icon-plus-sign"></i> New Invoice
                    </a>
                </div>
            <?php } ?>
        </div>
        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Invoices</div>
                    <div class="widget-content" style="min-height: 800px;">
                        <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                            <table class="table table-bordered dataTable" id="membersTable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Car Plate</th>
                                    <th>Total Amount</th>
                                    <th>Deposit</th>
                                    <th>Opening Balance</th>
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
    <script src="<?php echo MAIN_URL ?>assets/apps/js/jquery.dataTables.columnFilter.js"
            type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $('.dtpicker').datepicker({
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: 'MM yy',

                onClose: function () {
                    var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                },
                beforeShow: function () {
                    $(this).datepicker("hide");
                    $("#ui-datepicker-div").addClass("hide-calendar");
                    $("#ui-datepicker-div").addClass('MonthDatePicker');
                    $("#ui-datepicker-div").addClass('HideTodayButton');
                }

            });
            var oTable = $('#membersTable').dataTable({
                "bServerSide": true,
                "bPaginate": true,
                "sPaginationType": "full_numbers",
                "sAjaxSource": "<?php echo base_url() ?>retail/getInvoices/<?php echo isset($isactive) ? $isactive :''?>",
                "sServerMethod": "POST",

                "aaSorting": [[0, 'desc']],
                "fnServerParams": function (aoData) {
                    aoData.push({"name": "date_from", "value": "<?php echo $date_filter?>"});
                },

                "aoColumns": [
                    {"aaData": "0", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": "4", 'sClass': "center"},
                    {"aaData": "5", 'sClass': "center"},
                    {"aaData": "6", 'sClass': "center"},
                    {"aaData": "7", 'sClass': "center"},
                    {"aaData": null, 'sClass': "center"},
                    {"aaData": null, 'bSortable': false, 'bSearchable': false, 'sClass': "center", 'sWidth': '100px'}
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    <?php if(isset($isactive) && $isactive=='N'){?>
                    $('td:eq(8)', nRow).html(aData['9']);
                    var view_file = '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/invoice/' + aData['0'] + '\')"><i class="fa fa-file"></i></a>';
                    var retrieve = '<a class="btn btn-xs btn-success" href="<?php echo base_url() ?>retail/retrieve_invoice/' + aData['0'] + '"><i class="fa fa-undo"></i></a>';
                    $('td:eq(9)', nRow).html(view_file + retrieve);
                    <?php } else {?>
                    var btn_class = 'btn-info';
                    if (aData[8] == 'paid') {
                        btn_class = 'btn-success';
                    }
                    var payment = '<a class="btn btn-xs ' + btn_class + '" id="paymentstatus_' + aData['0'] + '">' + aData['8'] + '</a>';
                    $('td:eq(8)', nRow).html(payment);
                    var view_file = '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/invoice/' + aData['0'] + '\')"><i class="fa fa-file"></i></a>';
                    var edit = '';
					//alert(aData[1]);
					var today_date_1 = "<?php echo date("d/m/Y"); ?>";
					//alert(today_date_1);
					var parts = aData[1].split("/");
					var date1_1 = new Date(parts[1] + "/" + parts[0] + "/" + parts[2]);
					
					var parts1 = today_date_1.split("/");					
					var date2_1 = new Date(parts1[1] + "/" + parts1[0] + "/" + parts1[2]);
					
					var date1 = date1_1.getTime();
					var date2 = date2_1.getTime();
					
					var timeDiff = parseInt((date2 - date1) / (1000 * 3600 * 24));			
					
					//alert(timeDiff);
					
                    if (aData[9] != '1' && timeDiff <= 1) {
                        edit = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>retail/create_invoice/' + aData['0'] + '"><i class="fa fa-pencil"></i></a>';
                    }
                    var ret = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>retail/return_invoice/' + aData['0'] + '"><i class="fa fa-share-square-o"></i></a>';
                    var del = '<a class="btn btn-xs btn-danger delete-action" data-toggle="modal" href="#modalDelete" data-href="<?php echo base_url() ?>retail/delete_invoice/' + aData['0'] + '"><i class="fa fa-times"></i></a>';
                    $('td:eq(9)', nRow).html(view_file + ret + edit + del);
                    <?php }?>
                    return nRow;
                }
            });/*.columnFilter({
                sPlaceHolder: "head:after",
                aoColumns: [
                    null,
                    null,
                    {type: "text"},
                    {type: "text"},
                    null,
                    null,
                    null,
                    {type: "select", values: ['paid', 'pending']}
                ]
            });*/

            if (window.location.hash) {
                var doc = window.location.hash.substring(1);
                popup('<?php echo base_url() ?>documents/invoice/' + doc);
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