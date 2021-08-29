<?php $this->load->view('templates/header'); ?>


<!-- ==================== WIDGETS CONTAINER ==================== -->
<div class="container">

    <div class="row">
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-head">Creditors List</div>
                <div class="widget-content">

                    <table class="table table-striped table-bordered table-hover" id="itable">
                        <thead>
                        <th class="center">Supplier</th>
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
            "sAjaxSource": "<?php echo base_url() ?>finance/getCreditors",
            "sServerMethod": "POST",
            "aaSorting": [[0, 'asc']],
            "aoColumns": [
                {"aaData": "0", 'sType': 'numeric', 'sClass': "center"},
                {"aaData": "1", 'sClass': "center"},
                {"aaData": "2", 'sClass': "center", 'sType': 'eu_date'},
                {"mData": [0], 'sClass': "center"}
            ]
        });

    });
</script>



<?php $this->load->view('templates/footer'); ?>
