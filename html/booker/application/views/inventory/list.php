<?php $this->load->view('templates/header'); ?>

    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <div class="row top10">
            <div class="col-md-12">
                <a href="<?php echo base_url() ?>inventory/addItem" role="button" data-toggle="modal"
                   class="btn btn-primary pull-right mb clearform"> <i class="icon-plus-sign"></i>
                    Add Product
                </a>
            </div>
        </div>
        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Stock Table</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper">
                            <table id="itable" class="table table-striped table-bordered table-hover">
                                <thead>
                                <th>ISBN</th>
                                <!---<th>Part No.</th>--->
                                <th>Description</th>
                                <th>Remark</th>
                                <th>Publisher</th>
                                <th>Category</th>
                                <!---<th>Car Model</th>--->
                                <th>QTY</th>
                                <th>QTY on hand</th>
                                <th>Inv Cost</th>
                                <th>Land Cost</th>
                                <th>Price</th>
                                <th>Pricing Info</th>
                                <th>Location</th>
                                <th>Outlet</th>
                                <th>Action</th>
                                </thead>
                            </table>
                        </div>
                        <div class="widget-foot">
                            <br><br>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ==================== END OF BORDERED TABLE FLOATING BOX ==================== -->
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
            });


            var oTable = $('#itable').dataTable({
                "bServerSide": true,
                "sPaginationType": "full_numbers",
                "sAjaxSource": "<?php echo base_url() ?>inventory/getStockItems",
                "sServerMethod": "POST",
                "aaSorting": [[0, 'asc']],
                "aoColumns": [
                    {"aaData": "0", "sSortDataType": "dom-text", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1"},
                    {"aaData": "2", 'sClass': "center"},
                    {"aaData": "3", 'sClass': "center"},
                    {"aaData": "4", 'sClass': "center"},
                    {"aaData": "5", 'bSearchable': false, 'sClass': "center"},
                    {"aaData": "6", 'sClass': "center"},
                    {"aaData": "7", 'sClass': "center"},
                    {"aaData": "8", 'sClass': "center"},
                    {"aaData": "9", 'sClass': "center"},
                    {"aaData": "10", 'sClass': "center"},
                    {"aaData": "11", 'sClass': "center"},
                    {"aaData": "12", "bSortable": false, 'sClass': "center"},
                    {"aaData": "13", "bSortable": false, 'sClass': "center", 'sWidth': '90px'},
                ],
                "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    $('td:eq(9)', nRow).html("<b title='Price1: " + aData['11'] + "\n" + "Price2: " + aData['14'] + "\n" + "Price3: " + aData['15'] + "\n" + "Price4: " + aData['16'] + "\n" + "'>" + aData['9'] + "</b>");
                    $('td:eq(13)', nRow).html('<a class="btn btn-primary btn-xs btn-details" data-href="' + aData['13'] + '"><i class="fa fa-chevron-down"></i></a>');
                    return nRow;
                }
            });


            $('body').on('click', '.btn-details', function (e) {

                var product_id = $(this).data('href');
                var tabletr = $(this).parent().parent();
                var visibletr = $('.open_' + product_id);
                var iconstatus = $(this);

                $ajaxed = $('table#itable > tbody').children('tr.open_' + product_id).html();

                if (typeof $ajaxed != 'undefined') {
                    callAjax = false;
                    if (visibletr.is(':visible')) {
                        iconstatus.find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                        visibletr.hide();
                    }else{
                        visibletr.show();
                    }
                } else {
                    var callAjax = true;
                    iconstatus.find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                }

                if (callAjax) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url() ?>inventory/getStockItemDetails/",
                        data: {product_id: product_id}

                    }).done(function (response) {
                        $('tr.open_' + product_id).remove();
                        response = $.parseJSON(response);
                        $.each(response, function (key, val) {
                            if (val.outlet_id == "<?php echo $this->session->userdata('outlet_id') ?>") {
                                tabletr.after('<tr class="open_' + product_id + '"><td colspan="5"></td><td class="center">' + val.qty + '</td><td class="center">' + val.qty + '</td><td class="center">' + val.original_cost_price + '</td><td class="center">' + val.cost_price + '</td><td class="center"><b title="Price1: ' + val.price1 + "\n" + 'Price2: ' + val.price2 + "\n" + 'Price3: ' + val.price3 + "\n" + 'Price4: ' + val.price4 + "\n" + '">' + val.price1 + '<b></td><td class="center">' + val.pricing_info + '</td><td class="center">' + val.location + '</td><td class="center">' + val.name + '</td><td class="center"><a href="javascript:popup(\'<?php echo base_url() ?>inventory/showBarCode/' + product_id + '\')" class="btn btn-default btn-xs"><i class="fa fa-barcode"></i></a><a href="<?php echo base_url()."inventory/addItem/" ?>' + product_id + '" class="btn btn-default btn-xs"><i class="fa fa-edit"></i></a><a class="btn btn-danger btn-xs delete-action" href="#modalDelete" data-href="<?php echo base_url()."inventory/deleteItem/" ?>' + product_id + '" role="button" data-toggle="modal"><i class="fa fa-times"></i></a></td><tr>');
                            } else {
                                tabletr.after('<tr class="open_' + product_id + '"><td colspan="5"></td><td class="center">' + val.qty + '</td><td class="center">' + val.qty + '</td><td class="center">' + val.original_cost_price + '</td><td class="center">' + val.cost_price + '</td><td class="center"><b>' + val.price1 + '</b></td><td class="center">' + val.pricing_info + '</td><td class="center">' + val.location + '</td><td class="center">' + val.name + '</td><td class="center"><a class="btn btn-default btn-xs addToCart" data-outlet_id="' + val.outlet_id + '" data-href="' + product_id + '"><i class="fa fa-shopping-cart"></i></a><a class="btn btn-default btn-xs popupimage" href="#modalImage" role="button" data-toggle="modal" data-src="' + val.image + '"><i class="fa fa-picture-o"></i></a></td><tr>');
                            }
                        });
                    });
                }
            });

            $('body').on('click', '.addToCart', function () {
                var product_id = $(this).data('href');
                var outlet_id = $(this).data('outlet_id');
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url() ?>inventory/addToCart/",
                    data: {product_id: product_id, outlet_id: outlet_id}

                }).done(function (response) {
                    var itemsinchart = $('.numitems').html();
                    $('.numitems').show();
                    $('.numitems').html(++itemsinchart);
                    $('.itemsincart li:last').before(response);
                    //response = $.parseJSON(response);

                });

            });


        });
    </script>


<?php $this->load->view('templates/footer'); ?>