<?php $this->load->view('templates/header'); ?>

<?php
    $selectedDate = $this->uri->segment(4);
    $selectedDate = explode( '-', $selectedDate );

?>
    <!-- ==================== WIDGETS CONTAINER ==================== -->
    <div class="container">
        <div class="row top10">
            <div class="col-md-12">
                <a href="#modalMakePayment" data-toggle="modal" class="btn btn-primary pull-right mb clearform">
                    <i class="icon-plus-sign"></i> Make a Payment
                </a>
            </div>

        </div>

        <!-- ==================== TABLE ROW ==================== -->
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-head">Customers</div>
                    <div class="widget-content">
                        <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                            <table class="table table-bordered dataTable" id="membersTable">
                                <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Date</th>
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
    <div id="modalMakePayment" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h3 class="modal-title">Make Payment</h3>
                </div>
                <form name="make-payment" id="make-payment"
                    action="<?php echo base_url() ?>system_setup/make_payment/<?php echo $this->uri->segment(3) ?>/<?php echo $this->uri->segment(4) ?>"
                    method="post">
                    <input type="hidden" name="dp-year" value="<?php echo $selectedDate[0]; ?>">
                    <input type="hidden" name="dp-month" value="<?php echo $selectedDate[1]; ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-lg-4 control-label">Amount</label>

                            <div class="col-lg-8">
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="text" name="amount" style="min-width: 200px" placeholder="Enter Amount to pay"
                                           class="form-control">
                                </div>
                            </div>
                        </div>

                        <p>&nbsp;</p>

                        <div class="form-group">
                            <label class="col-lg-4 control-label">Select Date</label>

                            <div class="col-lg-8">
                                <input class="col-lg-8 form-control dtpicker" type="text" name="pmt_date"
                                       value="<?php echo $this->uri->segment(4) ?>">
                            </div>
                        </div>

                        <p>&nbsp;</p>

                        <div class="form-group">
                            <label class="col-lg-4 control-label">Description</label>

                            <div class="col-lg-8">

                                <input type="text" name="description" placeholder="Description for payment"
                                       class="form-control" size="">

                            </div>
                        </div>
                        <p>&nbsp;</p>
                        <div id="ajax-result"></div>
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
            var oTable = $('#membersTable').dataTable({
                "bServerSide": true,
                "sAjaxSource": "<?php echo base_url() ?>system_setup/getCustomerPayments/<?php echo $this->uri->segment(3)?>",
                "sServerMethod": "POST",
                "aaSorting": [[2, 'desc']],
                "bPaginate": true,
                "sPaginationType": "full_numbers",
                "aoColumns": [
                    {"aaData": "0", "sType": "numeric", 'sClass': "center"},
                    {"aaData": "1", 'sClass': "center"},
                    {"aaData": "2", 'sClass': "center"},
                ]
            });


        });
        $('.dtpicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });/*
        $('.dtpicker').datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'MM yy',

            onClose: function () {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                iMonth = parseInt(iMonth)+1;
                $('input[name="dp-year"]').val(iYear);
                $('input[name="dp-month"]').val(iMonth);
            },
            beforeShow: function () {
                $(this).datepicker("hide");
                $("#ui-datepicker-div").addClass("hide-calendar");
                $("#ui-datepicker-div").addClass('MonthDatePicker');
                $("#ui-datepicker-div").addClass('HideTodayButton');
            }

        });*/

        function submitForm() {
            $('div.modal-footer').css('display', 'none');
            //e.preventDefault();
            //var self = this;
            var result = false;

            amount = $('input[name="amount"]').val();
            $('div#ajax-result').html('');

            if (isNaN(amount) || amount == '' || amount <= 0) {
                $('div#ajax-result').html('<div class="alert alert-danger">Please enter valid payment amount</div>');

            } else {
                dataString = $('form[name="make-payment"]').serialize();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() ?>system_setup/getCustomerBalance/<?php echo $this->uri->segment(3)?>',
                    dataType: 'json',
                    data: dataString,
                    async:false,
                    cache: false
                }).done(function (data) {
                    if (data.result) {
                        //alert(data.amount);
                       /* if (amount > data.amount ) {
                            //alert( amount+' : '+data.amount);
                            $('input[name="amount"]').val(data.amount.toFixed(2));
                            $('div#ajax-result').html('<div class="alert alert-info">Your Balance till this month is <b>$' + data.amount.toFixed(2) + '</b>, Click "YES" to pay <b>$' + data.amount.toFixed(2) + '</b></div>');

                            //return false;
                        } else {
                            //alert(amount + ' : ' + data.amount);
                            result = true;
                            $('div#ajax-result').html('');
                            //$('form#make-payment').submit();

                        }*/
                        result = true;
                        $('div#ajax-result').html('');
                    } else {
                        $('div#ajax-result').html('<div class="alert alert-danger">No Balance B/F till this Month</div>');
                        $('div.modal-footer').css('display', 'block');
                    }
                });

            }

            return result;

            //e.unbind();
        }



    </script>
    <style>
        #ui-datepicker-div{ z-index: 99999 }
        .MonthDatePicker {
            display: none;
        }
        .HideTodayButton .ui-datepicker-buttonpane .ui-datepicker-current {
            visibility: hidden;
        }

        .hide-calendar .ui-datepicker-calendar {
            display: none !important;
            visibility: hidden !important
        }.alert{ margin-bottom: 0;}
    </style>
<?php $this->load->view('templates/footer'); ?>