<?php $this->load->view('templates/header'); ?>

    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <div class="row top10">
            <div class="col-md-12">
                <a href="<?php echo base_url() ?>system_setup/add_user" role="button"
                   class="btn btn-primary pull-right mb clearform">
                    <i class="icon-plus-sign"></i> Add User
                </a>
            </div>
        </div>
        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Users</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                            <table class="table table-bordered dataTable" id="membersTable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Outlet</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($users as $key => $value) { ?>
                                    <tr>
                                        <td><?php echo $key + 1 ?></td>
                                        <td><?php echo $value->name ?></td>
                                        <td><?php echo $value->username ?></td>
                                        <td><?php echo $value->email ?></td>
                                        <td><?php echo $value->type ?></td>
                                        <td><?php echo $value->outlet_name ?></td>
                                        <td class="actions">
                                            <a class="btn btn-xs btn-default edit"
                                               href="<?php echo base_url() ?>system_setup/add_user/<?php echo $value->user_id ?>"
                                               role="button">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a class="btn btn-xs btn-danger delete-action" href="#modalDelete"
                                               data-href="<?php echo base_url() ?>system_setup/delete_user/<?php echo $value->user_id ?>"
                                               role="button" data-toggle="modal">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
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
            $('.dataTable').dataTable({"sPaginationType": "full_numbers"});
        });
    </script>

<?php $this->load->view('templates/footer'); ?>