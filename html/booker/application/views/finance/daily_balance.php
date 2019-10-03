<?php $this->load->view('templates/header');?>
<style>
  .form-group {
    margin-bottom: 5px;
  }
  h5{
    font-weight:bold;
  }
</style>
<div class="container">
  <div class="row top10"> 
      <div class="col-md-12">
        <div class="col-md-6">
          <form style="padding-left:0" class="col-md-6" id="select_date" method="get" action="<?php echo base_url()?>finance/daily_balance">
            <input type="text" name="date" value="<?php echo $this->input->get('date')?>" class="form-control datepicker" placeholder="Select the date" autocomplete="off" />
          </form>
          <a id="submit_form" class="btn btn-primary">Go</a>
        </div>
      </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="widget">
          <div class="widget-head">
            <div class="pull-left">Daily Balance</div>
            <div class="clearfix"></div>
          </div>

          <div class="widget-content">
            <div class="padd">
             
              <!-- Profile form -->
              <div class="form profile">
                <form class="form-horizontal">
                <div class="form-group">
                  <b class="col-lg-3">Invoices:</b>
                  <div class="col-lg-6"><?php echo format_price($invoices)?></div>
                </div>

                <div class="form-group">
                    <b class="col-lg-3">Credit Note:&nbsp;</b>
                    <div class="col-lg-6"><?php echo format_price($returned_cn)?></div>
                </div>

                <div class="form-group">
                  <b class="col-lg-3">Cash Sales:&nbsp;</b>
                  <div class="col-lg-6"><?php echo format_price($cash_sales)?></div>
                </div>

                <div class="form-group">
                    <b class="col-lg-3">Cash sale Return:&nbsp;</b>
                    <div class="col-lg-6"><?php echo format_price($returned_cs)?></div>
                </div>

                <div class="form-group">
                  <b class="col-lg-3">Partial Payments:&nbsp;</b>
                  <div class="col-lg-6"><?php echo format_price($partial_payments)?></div>
                </div>
                <div class="form-group">
                  <b class="col-lg-3">Inter Outlet D.O Total:&nbsp;</b>
                  <div class="col-lg-6"><?php echo format_price($delivery_orders)?></div>
                </div>

                <div class="form-group">
                  <h5 class="col-lg-3">Total Sale:</h5>
                  <div class="col-lg-3"><?php echo format_price($net_profit)?></div>
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
     dateFormat: 'yy-mm-dd'
     });
  })
</script>
<?php $this->load->view('templates/footer');?>