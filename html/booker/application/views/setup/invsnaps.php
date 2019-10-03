<?php $this->load->view('templates/header'); ?>

    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <div class="row top10">
            <div class="col-md-12">
                <a href="<?php echo base_url() ?>system_setup/create_inv_sanp/"
                   class="btn btn-primary pull-right mb clearform">
                    <i class="icon-plus-sign"></i> Take SnapShot
                </a>
            </div>
        </div>
        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Inventory SnapShot</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                            <table class="table table-bordered dataTable" id="membersTable">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Outlet</th>
                                    <th>File</th>
                                    <th>Time</th>
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
                "sAjaxSource": "<?php echo base_url() ?>system_setup/getInvSnapShots",
                "sServerMethod": "POST",
                "aoColumns": [
                    {"aaData": "0", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": null, 'bSortable': false, 'bSearchable': false, 'sClass': "center", 'sWidth': '100px'}
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var download = '<a class="btn btn-xs btn-warning" href="<?php echo base_url() ?>uploads/' + aData['2'] + '"><i class="fa fa-download"></i> &nbsp;' + aData['2'] + '</a>';
                    $('td:eq(2)', nRow).html(download);
                    return nRow;
                },
            });
        });
    </script>
<?php $this->load->view('templates/footer'); ?>