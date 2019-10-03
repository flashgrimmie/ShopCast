<?php $this->load->view('templates/header'); ?>

    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">

        <div class="row top10">
            <div class="col-md-4 pull-right">
                <div class="pull-right">
                    <a href="#addOneTime" role="button" data-toggle="modal" class="btn btn-primary">
                        <i class="icon-plus-sign"></i> Add One Time Expense
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">One Time Expenses</div>
                    <div class="widget-content">

                        <table class="table table-striped table-bordered table-hover" id="itable">
                            <thead>
                            <th class="center">Type</th>
                            <th class="center">Cost</th>
                            <th class="center">Date</th>
                            <th class="center">Action</th>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="addOneTime" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addOneTime">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Add One TIme</h3>
                </div>


                <form class="form-horizontal" method="post" action="<?php echo base_url() ?>finance/insert_one_time">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Type</label>

                            <div class="col-lg-8">
                                <input type="text" name="name" placeholder="Enter Purchase Type" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Cost</label>

                            <div class="col-lg-8">
                                <input type="text" name="value" placeholder="Enter Purchase Cost" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <input class="btn btn-danger" type="submit" name="submit" value="Yes">

                        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            var oTable = $('#itable').dataTable({
                "bServerSide": true,
                "sPaginationType": "full_numbers",
                "sAjaxSource": "<?php echo base_url() ?>finance/getOneTimeExpenses",
                "sServerMethod": "POST",
                "aaSorting": [[0, 'asc']],
                "aoColumns": [
                    {"aaData": "0", "sSortDataType": "dom-text", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"}
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    $('td:eq(3)', nRow).html('<a class="btn btn-danger btn-xs delete-action" href="#modalDelete" role="button" data-toggle="modal" data-href="<?php echo base_url() ?>finance/delete_one_time/' + aData['3'] + '"><i class="fa fa-times"></i></a>');
                    return nRow;
                }
            });

        });
    </script>


<?php $this->load->view('templates/footer'); ?>