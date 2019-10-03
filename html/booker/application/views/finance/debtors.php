<?php $this->load->view('templates/header'); ?>


<!-- ==================== WIDGETS CONTAINER ==================== -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a class="btn btn-primary top10 pull-right" href="<?php echo base_url('finance/add_debtor'); ?>">Add
                Existing Debt</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-head">Debtors List</div>
                <div class="widget-content">

                    <table class="table table-striped table-bordered table-hover" id="itable">
                        <thead>
                        <th class="center">Customer</th>
                        <th class="center">Amount Due</th>
                        <th class="center">Date</th>
                        <th class="center">Action</th>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {

        var oTable = $('#itable').dataTable({
            "bServerSide": true,
            "sPaginationType": "full_numbers",
            "sAjaxSource": "<?php echo base_url() ?>finance/getDebtors",
            "sServerMethod": "GET",
            "aaSorting": [[0, 'asc']],
            "aoColumns": [
                {"aaData": "0", "sSortDataType": "dom-text", "sType": "numeric", 'sClass': "center"},
                {"aaData": "1", 'sClass': "center"},
                {"aaData": "2", 'sClass': "center"},
                {"aaData": null, 'bSortable': false, 'bSearchable': false, 'sClass': "center"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                if (aData[3] != '') {
                    var edit = '<a class="btn btn-xs btn-default" href="<?php echo base_url() ?>finance/add_debtor/' + aData[3] + '"><i class="fa fa-pencil"></i></a>';
                    $('td:eq(3)', nRow).html(edit);
                }
                return nRow;
            },
        });

    });
</script>



<?php $this->load->view('templates/footer'); ?>
