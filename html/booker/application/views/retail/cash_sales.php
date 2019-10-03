<?php $this->load->view('templates/header'); ?>

    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <?php if (!isset($isactive)) { ?>
            <div class="row top10">
                <div class="col-md-12">
					<a id="import_files" href="<?php echo base_url() ?>retail/exportCSReport/N" class="btn btn-default pull-right right10 mb clearform">
						<icon class="fa fa-upload"></icon>
						&nbsp;Export
					</a>				
                    <a style="margin-left:5px" href="<?php echo base_url() ?>retail/deleted_cs"
                       class="btn btn-default pull-right mb clearform">
                        <i class="fa fa-trash-o"></i>
                    </a>
                    <a href="<?php echo base_url() ?>retail/create_cs" class="btn btn-primary pull-right mb clearform">
                        <i class="icon-plus-sign"></i> New CS
                    </a>
                </div>
            </div>
        <?php } ?>
        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Cash Sales</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                            <table class="table table-bordered dataTable" id="membersTable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Remark</th>
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
                "sAjaxSource": "<?php echo base_url() ?>retail/getCSs/<?php echo isset($isactive) ? $isactive :''?>",
                "sServerMethod": "POST",
                "aaSorting": [[0, 'desc']],
                "aoColumns": [
                    {"aaData": "0", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": null, 'bSortable': false, 'bSearchable': false, 'sClass': "center", 'sWidth': '100px'}
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
					if(aData[4]=='Y') { 
						$('td', nRow).parent().css('backgroundColor', 'pink');
					}
                    <?php if(isset($isactive) && $isactive=='N'){?>
                    var view_file = '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/cash_sale/' + aData['0'] + '\')"><i class="fa fa-file"></i></a>';
                    var view_file_v2 = '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/cash_sale_v2/' + aData['0'] + '\')"><i class="fa fa-print"></i></a>';
                    var retrieve = '<a class="btn btn-xs btn-success" href="<?php echo base_url() ?>retail/retrieve_cs/' + aData['0'] + '"><i class="fa fa-undo"></i></a>';
                    $('td:eq(4)', nRow).html(view_file + view_file_v2 + retrieve);
                    <?php } else {?>
                    var view_file = (aData[4]!='Y') ? '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/cash_sale/' + aData['0'] + '\')"><i class="fa fa-file"></i></a>': '';
                    var view_file_v2 = (aData[4]!='Y') ? '<a class="btn btn-xs btn-default" href="javascript:popup(\'<?php echo base_url() ?>documents/cash_sale_v2/' + aData['0'] + '\')"><i class="fa fa-print"></i></a>': '';
                    var edit = '';
                    if (aData[4] == 'Y') {
                        edit = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>retail/create_cs/' + aData['0'] + '"><i class="fa fa-pencil"></i></a>';
                    }
                    //var edit = '';
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
                    
                    if (timeDiff <= 1) {
                        edit = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>retail/create_cs/' + aData['0'] + '"><i class="fa fa-pencil"></i></a>';
                    }
                    var ret = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>retail/return_cs/' + aData['0'] + '"><i class="fa fa-share-square-o"></i></a>';
                    var del = '<a class="btn btn-xs btn-danger delete-action" data-toggle="modal" href="#modalDelete" data-href="<?php echo base_url() ?>retail/delete_cs/' + aData['0'] + '"><i class="fa fa-times"></i></a>';
                    $('td:eq(4)', nRow).html(view_file + view_file_v2 + ret + edit + del);
                    <?php }?>
                    return nRow;
                }
            });

            if (window.location.hash) {
                var doc = window.location.hash.substring(1);
                popup('<?php echo base_url() ?>documents/cash_sale/' + doc);
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