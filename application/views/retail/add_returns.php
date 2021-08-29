<?php $this->load->view('templates/header'); ?>
    <script>
        function update_total() {
            var total = 0;
            $('[id*="linetotalval_"]').each(function () {
                total += Number($(this).val());
            });
            $('#total').html("$" + roundToTwo(total));
            //format_price($('#total'));
        }
    </script>
    <div class="container">
        <div class="row">
            <?php
                if ($this->uri->segment(3) == '') {
                    $this->load->view('templates/retail_items_cn');
                }?>
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-head">
                        <div class="pull-left">Return Notes</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="widget-content">
                        <?php

                            $submit_url = base_url() . 'retail/save_cn/' . $this->uri->segment(3);
                            if ($submit_to == 'cs') {
                                $submit_url = base_url() . 'retail/save_cncs/' . $this->uri->segment(3);
                            }
                        ?>
                        <form id="cs_form" method="post" action="<?php echo $submit_url ?>">
                            <?php if (isset($msg) && $msg != '') { ?>
                                <p class="error">Wrong value for quantity! The number can't be higher than 0.</p>
                            <?php } ?>
                            <table class="table table-bordered">
                                <thead>
                                <th>Stock Code</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Discount(%)</th>
                                <th>Markup($)</th>
                                <th>Total</th>
                                <th></th>
                                </thead>
                                <tbody id="iitems">
                                <?php if (isset($i_items)) {
                                    foreach ($i_items as $item_details) {
                                        //print_r($item_details);exit;
                                        ?>
                                        <tr class="text-center" id="item_<?php echo $item_details->id ?>">
                                            <td><?php echo $item_details->stock_num ?>
                                                <input type="hidden" value="<?php echo $item_details->item_id ?>"
                                                       id="itemid_<?php echo $item_details->id ?>" name="item_id[]"/>
                                                <input type="hidden" value="<?php echo $item_details->iitem_id ?>"
                                                       id="iitemid_<?php echo $item_details->id ?>" name="iitem_id[]"/>
                                            </td>
                                            <td>
                                                <select id="customer_prices">
                                                    <option
                                                        value="<?php echo format_number($item_details->price1) ?>" <?php echo format_number($item_details->price) == format_number($item_details->price1) ? 'selected' : '' ?>>
                                                        Price1
                                                    </option>
                                                    <option
                                                        value="<?php echo format_number($item_details->price2) ?>" <?php echo format_number($item_details->price) == format_number($item_details->price2) ? 'selected' : '' ?>>
                                                        Price2
                                                    </option>
                                                    <option
                                                        value="<?php echo format_number($item_details->price3) ?>" <?php echo format_number($item_details->price) == format_number($item_details->price3) ? 'selected' : '' ?>>
                                                        Price3
                                                    </option>
                                                    <option
                                                        value="<?php echo format_number($item_details->price4) ?>" <?php echo format_number($item_details->price) == format_number($item_details->price4) ? 'selected' : '' ?>>
                                                        Price4
                                                    </option>
                                                </select>
                                                <input type="hidden"
                                                       value="<?php echo format_number($item_details->price) ?>"
                                                       id="price_<?php echo $item_details->id ?>" name="price[]"/>
                                            </td>
                                            <td><input class="form-control inputsmall" type="text"
                                                       id="quantity_<?php echo $item_details->id ?>"
                                                       value="<?php echo isset($item_details) ? $item_details->quantity : '1' ?>"
                                                       name="quantity[]"/></td>
                                            <td><input class="form-control inputsmall" type="text"
                                                       id="discount_<?php echo $item_details->id ?>"
                                                       value="<?php echo isset($item_details) ? $item_details->discount : '' ?>"
                                                       name="item_discount[]"/></td>
                                            <td>
                                                <input class="form-control inputsmall" type="hidden"
                                                       id="discountvalue_<?php echo $item_details->id ?>"
                                                       value="<?php echo isset($item_details) ? $item_details->discount_value : '' ?>"
                                                       name="discount_value[]"/>
                                                <input class="form-control inputsmall" type="text"
                                                       id="markup_<?php echo $item_details->id ?>"
                                                       value="<?php echo isset($item_details->markup) ? $item_details->markup : '' ?>"
                                                       name="markup[]"/>
                                            </td>
                                            <td><span id="linetotal_<?php echo $item_details->id ?>">
					  <?php
                          $desc_arr = explode("-", $item_details->discount);
                          $desc = $desc_arr[0];
                          if (sizeof($desc_arr) > 1 && $desc_arr[1] != '') {
                              $desc = ($desc_arr[0] + $desc_arr[1]);
                          }
                      ?>
                      <?php echo format_number((($item_details->price * (1 - $desc / 100)) - $item_details->discount_value) * $item_details->quantity) ?></span>
                                                <input type="hidden"
                                                       value="<?php echo format_number((($item_details->price * (1 - $item_details->discount / 100)) - $item_details->discount_value) * $item_details->quantity) ?>"
                                                       id="linetotalval_<?php echo $item_details->id ?>"/>
                                            </td>
                                            <td><a class="btn btn-xs btn-danger deleteitem"
                                                   id="undoreturn_<?php echo $item_details->iitem_id ?>" role="button">

                                                    <i class="fa fa-times delete"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                                <tr>
                                    <td colspan="7" <?php echo isset($i_items) ? 'style="display:none"' : '' ?>
                                        id="no-items" class="text-center">No items selected
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="padd top10">
                                <div class="control-group width60 pull-right">
                                    <label class="control-label" for="total">Total:&nbsp;<span
                                            id="total"><?php echo format_price(isset($invoice_details->total) ? $invoice_details->total : '0') ?></span></label>
                                </div>
                                <div class="clearfix"></div>
                                <div class="control-group width60">
                                    <input type="text" autocomplete="off" class="form-control"
                                           placeholder="Select Customer" id="selcustomer" style="display:inline"/>
                                    <a id="add_customer" href="#modalAddCustomer" data-toggle="modal"
                                       class="btn btn-default"><i class="fa fa-plus"></i></a>

                                    <div class="selectHolder" id="selectHolderSup"></div>
                                </div>
                                <div class="control-group width60">
                                    <address class="filled alert alert-success top10"
                                             style="<?php echo !isset($invoice_details) ? 'display:none' : '' ?>">
                                        <strong
                                            id="customer_name"><?php echo isset($invoice_details->name) ? $invoice_details->name : '' ?></strong><br/>
                                        <span
                                            id='customer_address'><?php echo isset($invoice_details->address) ? $invoice_details->address : '' ?></span><br/>
                                        <span
                                            id='customer_phone'><?php echo isset($invoice_details->phone) ? $invoice_details->phone : '' ?></span><br/>
                                        <span
                                            id='customer_email'><?php echo isset($invoice_details->email) ? $invoice_details->email : '' ?></span><br/>
                                        <span
                                            id='customer_carplate'><?php echo isset($invoice_details->car_plate) ? $invoice_details->car_plate : '' ?></span><br/>
                                        <i class="icon-map-marker pull-right"></i>
                                    </address>
                                    <input type="hidden" name="customer_id" id="customer_id"
                                           value="<?php echo isset($invoice_details->customer_id) ? $invoice_details->customer_id : '' ?>">
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="remark">Remarks</label>

                                    <div class="controls">
                                        <textarea class="form-control" rows="6"
                                                  name="remark"> <?php echo isset($invoice_details->remark) ? $invoice_details->remark : '' ?></textarea>
                                    </div>
                                    <input type="hidden" name="cn_id" value="" id="cn_id">
                                </div>
                                <div class="control-group">
                                    <p class='error'></p>
                                </div>
                                <div class="submit form-group clearfix top10">
                                    <input type="submit" class="btn btn-success pull-right" name="save" value="Save">
                                </div>
                            </div>
                            <input type="hidden" name="draft" id="draft"/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="saved" class="label label-default">Saved</div>
<?php $this->load->view('templates/add_customer') ?>
    <script>
    $(document).ready(function () {
        $('#filter_items').keyup(function () {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url() ?>retail/searchStock",
                data: {search: $(this).val()}
            }).done(function (msg) {
                $('.gallery').html(msg);
            });
        });

        <?php if(!$this->uri->segment(3)) {?>
        $('#draft').val('draft');
        setInterval(function () {
            $.ajax({
                type: "POST",
                url: $('#cs_form').attr('action'),
                data: $('#cs_form').serialize()
            }).done(function (msg) {
                //alert(msg);
                $('#cn_id').val(msg);
                $('#cs_form').attr('action', '<?php echo base_url()?>retail/save_ri/' + msg);
                if (msg > 0) {
                    $('#saved').show();
                }
            });
            $('#saved').hide();
        }, 10000);
        <?php }?>

        $('#save_customer').click(function () {
            var error = '';
            if ($('#addcus_name').val() == '') {
                error += '<p>Please fill out the customer name.</p>';
            }
            if ($('#addcus_phone').val() == '') {
                error += '<p>Please fill out the customer phone number.</p>';
            }
            $('#customer_error').html(error);
            if (error != '') {
                return false;
            }
            $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>system_setup/saveCustomerAjax",
                data: $('#form_customer').serialize()
            }).done(function (msg) {
                select_customer(msg);
                $('#modalAddCustomer').modal('hide');
            });
            return false;
        });

        $('body').on('change', '#customer_prices', function () {
            var parent = $(this).parent().parent();

            parent.find('[id*=price_]').val($(this).val());
            var sell_price = parent.find('[id*=price_]').val();
            var quantity = parent.find('[id*=quantity_]').val();
            var discount = (typeof parent.find('[id*=discount_]').val() == 'undefined') ? 0 : parent.find('[id*=discount_]').val();
            var discount_value = (typeof parent.find('[id*=discountvalue_]').val() == 'undefined') ? 0 : parent.find('[id*=discountvalue_]').val();
            var markup = (typeof parent.find('[id*=markup_]').val()) ? 0 : parent.find('[id*=markup_]').val();

            if (discount == '') {
                discount = 0 + '-';
            }

            var desc_arr = discount.split('-');

            discount = parseInt(desc_arr[0]);
            var item_total = roundToTwo((sell_price * (1 - discount / 100) - discount_value + (1 * markup)) * quantity);

            if (desc_arr.length > 1 && desc_arr[1] != '' && desc_arr[1] != '-') {

                discount = parseInt(desc_arr[1]);
                item_total = roundToTwo(item_total * (1 - discount / 100));
            }
            // var item_total=roundToTwo((sell_price*(1-discount/100)-discount_value+(1*markup))*quantity);
            parent.find('[id*=linetotal_]').html(item_total.toFixed(2));
            parent.find('[id*=linetotalval_]').val(item_total.toFixed(2));
            update_total();
        });

        $('body').on('click', '[id*="retailitem_"]', function () {
            var id = $(this).attr('id');
            var arr = id.split('_');
            $.ajax({
                type: "POST",
                url: "<?php echo base_url() ?>retail/selectInvoiceItemCN",
                data: {item_id: arr[1]}
            }).done(function (msg) {
                if (msg == 'sold_out') {
                    alert('The item is sold out!');
                    return false;
                }
                if ($('#iitems tr').length == 1) {
                    $('#no-items').hide();
                }
                $('#iitems').append(msg);
                update_total();
            });
        });
        $('body').on('click', '.deleteitem', function () {
            $(this).parent().parent().remove();
            if ($('#iitems tr').length == 1) {
                $('#no-items').show();
            }
            update_total();
        });
        $('body').on('keyup', '[id*="quantity_"],[id*="discount_"],[id*="discountvalue_"],[id*="markup_"]', function () {
            var id = $(this).attr('id');
            var arr = id.split('_');
            var parent = $(this).parent().parent();
            var sell_price = parent.find('#price_' + arr[1]).val();
            var quantity = parent.find('#quantity_' + arr[1]).val();
            var discount = (typeof parent.find('[id*=discount_]').val() == 'undefined') ? 0 : parent.find('[id*=discount_]').val();
            var discount_value = (typeof parent.find('[id*=discountvalue_]').val() == 'undefined') ? 0 : parent.find('[id*=discountvalue_]').val();
            var markup = (typeof parent.find('[id*=markup_]').val()) ? 0 : parent.find('[id*=markup_]').val();

            if (discount == '') {
                discount = 0 + '-';
            }
            var desc_arr = discount.split('-');

            discount = parseInt(desc_arr[0]);
            var item_total = roundToTwo((sell_price * (1 - discount / 100) - discount_value + (1 * markup)) * quantity);
            if (desc_arr.length > 1 && desc_arr[1] != '' && desc_arr[1] != '-') {
                discount = parseInt(desc_arr[1]);
                item_total = roundToTwo(item_total * (1 - discount / 100));
            }


            parent.find('#linetotal_' + arr[1]).html(item_total.toFixed(2));
            parent.find('#linetotalval_' + arr[1]).val(item_total.toFixed(2));
            update_total();
        });

        $('#selcustomer').keyup(function (event) {
            var customer = $('#selcustomer').val();
            $('.selectHolder').width($('#selcustomer').width() + 24);
            $('#selectHolderSup').show();
            $.ajax({
                type: "POST",
                url: "<?php echo base_url() ?>purchasing/searchCustomer",
                data: {customer: customer}
            }).done(function (msg) {
                var content = '<div><ul id="searchcustomer">';
                msg = $.parseJSON(msg);
                $.each(msg, function (key, value) {
                    content += '<li data-selected=' + value.customer_id + ' class="searchcustomeropt">' + value.name + '</li>';
                });
                content += '</ul></div>';
                $('#selectHolderSup').html(content);
            });
        });

        function select_customer(selected) {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url() ?>system_setup/getCustomersDetails/" + selected
            }).done(function (msg) {
                var data = JSON.parse(msg);
                $('.selectHolder').hide();
                $('.filled').show();
                $('#customer_name').html(data.name);
                $('#customer_email').html(data.email);
                $('#customer_address').html(data.address);
                $('#customer_phone').html(data.phone);
                $('#customer_carplate').html(data.car_plate);
                $('#customer_id').val(data.customer_id);
            });
        }

        $('body').on('click', '.searchcustomeropt', function () {
            var selected = $(this).data('selected');
            select_customer(selected);
        });
		
		
        $('.submit :submit').click(function () {
			if (!$('#customer_id').val()) {
                $('.error').html('Please select a customer.');
                return false;
            }
        });

        $('#selcustomer').keypress(function () {
            $('.error').html('');
        });

        $('#selcustomer').change(function () {
            $('.error').html('');

        });

        $('input[id^=supplier_]').click(function () {
            $('.error').html('');
        });

        $('#add_mechanic').click(function () {
            var html = '<div class="controls top10">';
            html += '<input style="width:45%;display:inline-block" type="text" class="form-control" name="mechanic[]" placeholder="Mechanic Name"/>';
            html += '&nbsp;<input style="width:45%;display:inline-block;" type="text" class="form-control" name="mechanic_charge[]" placeholder="Service Charge"/>';
            html += '&nbsp;<a class="btn btn-danger remove_mechanic"><i class="fa fa-minus"></i></a>'
            html += '</div>';
            $('#mechanics').append(html);
        });

        $('body').on('click', '.remove_mechanic', function () {
            $(this).parent().remove();
        });

        $('body').on('click', '[id*="undoreturn_"]', function () {
            var btn = $(this);
            var arr = btn.attr('id').split('_');
            var id = arr[1];
            var ajaxurl = '';
            <?php if($submit_to == 'invoice'){?>
            ajaxurl = "<?php echo base_url() ?>retail/undoreturn_iitem";
            <?php }else{?>
            ajaxurl = "<?php echo base_url() ?>retail/undoreturn_csitem";
            <?php }?>

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {id: id}
            }).done(function (msg) {

                btn.parent().parent().remove();
                $('#total').html(msg);
            });
        });

    })
    </script>
<?php $this->load->view('templates/footer') ?>