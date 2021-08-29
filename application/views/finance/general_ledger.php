<?php $this->load->view('templates/header');?>
<style>
  b.col-lg-3 {
    padding-left:50px;
  }
  .form-group {
    margin-bottom: 5px;
  }
  h5{
    font-weight:bold;
  }
  .ui-datepicker-calendar{
    display:none;
  }
</style>
<div class="container">
  <div class="row top10"> 
      <div class="col-md-12">
        <div class="col-md-6">
          <form style="padding-left:0" class="col-md-6" id="select_date" method="get" action="<?php echo base_url()?>finance/general_ledger">
            <input type="text" name="date" value="<?php echo $this->input->get('date')?>" class="form-control datepicker" placeholder="Select the date" autocomplete="off" />
          </form>
          <a id="submit_form" class="btn btn-primary">Go</a>
        </div>
        <div class="col-md-6">
            <a href="javascript:popup('<?php echo base_url()?>documents/general_ledger?date=<?php echo $this->input->get('date') ? $this->input->get('date') : date('Y-m')?>')" role="button" class="btn btn-primary pull-right mb clearform" >
              <i class="fa fa-print"></i> Print Preview
            </a>
            <a href="javascript:popup('<?php echo base_url()?>documents/balance_sheet?date=<?php echo $this->input->get('date') ? $this->input->get('date') : date('Y-m')?>')" role="button" class="btn btn-default pull-right right10 mb clearform" >
              <i class="fa fa-file"></i> Balance Sheet
            </a>
        </div>
      </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="widget">
          <div class="widget-head">
            <div class="pull-left">General Ledger</div>
            <div class="clearfix"></div>
          </div>

          <div class="widget-content">
            <div class="padd">
             
              <!-- Profile form -->
              <div class="form profile">
                <form class="form-horizontal">
                <h5>SALES:</h5>
                <div class="form-group">
                  <b class="col-lg-3">Cash Sales Total:&nbsp;</b>
                  <div class="col-lg-6"><?php echo format_price($cash_sales)?></div>
                </div>
                <div class="form-group">
                  <b class="col-lg-3">Invoices Total:&nbsp;</b>
                  <div class="col-lg-6"><?php echo format_price($invoices)?></div>
                </div>
                <div class="form-group">
                  <b class="col-lg-3">Partial Payments:&nbsp;</b>
                  <div class="col-lg-6"><?php echo !empty($partial_payments)? format_price($partial_payments): '$0.00';?></div>
                </div>
                <div class="form-group">
                  <b class="col-lg-3">Inter Outlet D.O Total:&nbsp;</b>
                  <div class="col-lg-6"><?php echo format_price($delivery_orders)?></div>
                </div>
                <div class="form-group">
                  <b class="col-lg-3">Less:&nbsp;Sales Return:&nbsp;</b>
                  <div class="col-lg-6"><?php echo format_price($returned_sales)?></div>
                </div>
                <div class="form-group">
                  <h5 class="col-lg-3">Total:&nbsp;Net Sales</h5>
                  <div class="col-lg-3 text-right"><?php echo format_price($net_sales)?></div>
                </div>
                <div class="form-group">
                  <h5 class="col-lg-3">Less:&nbsp;Cost of Sales</h5>
                  <div class="col-lg-3 text-right"><?php echo format_price($cost_sales)?></div>
                </div>
                <div class="form-group">
                  <h5 class="col-lg-3">GROSS PROFIT</h5>
                  <div class="col-lg-3 text-right"><?php echo format_price($gross_profit)?></div>
                </div>
                <div class="form-group">
                  <h5 class="col-lg-3">Less:&nbsp;Expenses</h5>
                  <div class="col-lg-3 text-right"><?php echo format_price($purchase_total+$one_time_total+$recurring_total)?></div>
                </div>
        <div class="form-group">
                  <h5 class="col-lg-3">Petty&nbsp;Cash</h5>
                  <div class="col-lg-3 text-right"><a href="<?php echo base_url()?>finance/petty_cash?date=<?php echo $this->input->get('date')?>"><?php echo $petty_cash;?></a></div>
                </div>
        
                <?php foreach($recurring_fields as $value) :?>
                  <div class="form-group">
                    <b class="col-lg-3"><?php echo humanize($value)?>:&nbsp;</b>
                    <div class="col-lg-6"><?php echo format_price($recurring_expences->$value)?></div>
                  </div>
                <?php endforeach;?>
                <?php foreach($one_time_expences as $value) :?>
                  <div class="form-group">
                    <b class="col-lg-3"><?php echo $value->name?>:&nbsp;</b>
                    <div class="col-lg-6"><?php echo format_price($value->value)?></div>
                  </div>
                <?php endforeach;?>
                <?php foreach($outlet_expenses as $value) :?>
                  <div class="form-group">
                    <b class="col-lg-3"><?php echo $value->name?>:&nbsp;</b>
                    <div class="col-lg-6"><?php echo format_price($value->value)?></div>
                  </div>
                <?php endforeach;?>
                <div class="form-group">
                  <h5 class="col-lg-3">NETT PROFIT:</h5>
                  <div class="col-lg-3 text-right"><?php echo format_price($net_profit)?></div>
                </div>
               </form>
             </div>
           </div>
        </div>
     </div>  
  </div>
</div>
</div>
<script>
  $(document).ready(function(){
    $('#submit_form').click(function(){
      $('#select_date').submit();
    });

  $('.datepicker').datepicker({
   changeMonth: true,
   changeYear: true,
   dateFormat: 'yy-mm',
   showButtonPanel: true,
   onClose: function() {
     var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
     var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
     $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
     }
   });
  })
</script>
<?php $this->load->view('templates/footer');?>