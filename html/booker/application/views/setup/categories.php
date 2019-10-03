<?php $this->load->view('templates/header'); ?>

    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <div class="row top10">
            <div class="col-md-12">
                <a href="<?php echo base_url() ?>system_setup/add_category/<?php echo $this->uri->segment(3) ?>"
                   class="btn btn-primary pull-right mb clearform">
                    <i class="icon-plus-sign"></i> Add Category
                </a>
            </div>
        </div>
        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Categories</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                            <table class="table table-bordered dataTable" id="membersTable">
                                <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Category Description</th>
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
                "sAjaxSource": "<?php echo base_url() ?>system_setup/getCategories",
                "sServerMethod": "POST",
                "aoColumns": [
                    {"aaData": "0", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": null, 'bSortable': false, 'bSearchable': false, 'sClass': "center", 'sWidth': '100px'}
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    var edit = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>system_setup/add_category/' + aData['2'] + '"><i class="fa fa-pencil"></i></a>';
                    var del = '<a class="btn btn-xs btn-danger delete-action" href="#modalDelete" data-href="<?php echo base_url() ?>system_setup/delete_category/' + aData['2'] + '" role="button" data-toggle="modal"><i class="fa fa-times"></i></a>';
                    $('td:eq(2)', nRow).html(edit + del);
                    return nRow;
                },
            });
        });
    </script>
<?php $this->load->view('templates/footer'); ?>