<?php $this->load->view('templates/header'); ?>

<!-- ==================== WIDGETS CONTAINER ==================== -->
<div class="container">

    <div class="row top10">
        <div class="col-md-4 pull-right">
            <div class="pull-right">
                <a href="#addOtherPurchases" role="button" data-toggle="modal" class="btn btn-primary">
                    <i class="icon-plus-sign"></i> Add Other Purchase
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-head">Other Purchases</div>
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


<div id="addOtherPurchases" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addType">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h3 class="modal-title">Add Other Purchase</h3>
            </div>


            <form class="form-horizontal" method="post" enctype='multipart/form-data'
                  action="<?php echo base_url() ?>finance/insert_purchase">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Type</label>

                        <div class="col-lg-8">
                            <input type="text" name="name" placeholder="Enter Purchase Type" class="form-control"></div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-2 control-label">Cost</label>

                        <div class="col-lg-8">
                            <input type="text" name="value" placeholder="Enter Purchase Cost" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-2 control-label">Image</label>

                        <div class="col-lg-8">
                            <input type="file" name="image" class="form-control">
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

<div id="modalImage" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalImage">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-body image">
            </div>


            <div class="modal-footer">
                <input class="btn btn-danger" type="submit" name="submit" value="Yes">

                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {

        $('body').on('click', '.popupimage', function () {
            var src = $(this).data('src');
            if (src.length != 0) {
                $('.image').html('<img src="<?php echo base_url() ?>uploads/' + src + '" style="max-width:100%;"/>');
            } else {
                $('.image').html('<p>No image avialable</p>');
            }
        })

        var oTable = $('#itable').dataTable({
            "bServerSide": true,
            "sPaginationType": "full_numbers",
            "sAjaxSource": "<?php echo base_url() ?>finance/getOtherPurchases",
            "sServerMethod": "POST",
            "aaSorting": [[0, 'asc']],
            "aoColumns": [
                {"aaData": "0", "sSortDataType": "dom-text", "sType": "numeric", 'sClass': "center"},
                {"aaData": "1", 'sClass': "center"},
                {"aaData": "2", 'sClass': "center"},
                {"aaData": "3", 'sClass': "center"}
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                $('td:eq(3)', nRow).html('<span style="margin:4px"><a class="btn btn-xs btn-primary popupimage" href="#modalImage" role="button" data-toggle="modal" data-src="' + aData['4'] + '"><i class="fa fa-picture-o"></i></a></span><span style="margin:4px"><a class="btn btn-danger btn-xs delete-action" href="#modalDelete" role="button" data-toggle="modal" data-href="<?php echo base_url() ?>finance/delete_purchase/' + aData['3'] + '"><i class="fa fa-times"></i></a></span>');
                return nRow;
            }
        });

    });
</script>


<?php $this->load->view('templates/footer'); ?>
