<?php $this->load->view('templates/header');?>
	    <!-- Matter -->

	    <div class="matter">
        <div class="container">

          <!-- Today status. jQuery Sparkline plugin used. -->

          <div class="row top10">
            <div class="col-md-12 text-center">
              <a href="<?php echo base_url()?>retail/create_invoice">
                <div class="btn btn-primary right10">
                  <h2><i class="fa fa-file fa-white"></i></h2>
                  <p>New Invoice</p>
                </div>
              </a>

              <a href="<?php echo base_url()?>retail/create_cs">
                <div class="btn btn-primary right10">
                  <h2><i class="fa fa-shopping-cart fa-white"></i></h2>
                  <p>New Cash Sale</p>
                </div>
              </a>

              <a href="<?php echo base_url()?>retail/create_so">
                <div class="btn btn-primary right10">
                  <h2><i class="fa fa-list-alt fa-white"></i></h2>
                  <p>New Sales Order</p>
                </div>
              </a>

              <a href="<?php echo base_url()?>purchasing/create_pi">
                <div class="btn btn-primary right10">
                  <h2><i class="fa fa-file-text fa-white"></i></h2>
                  <p>New Purchase Invoice</p>
                </div>
              </a>

              <a href="<?php echo base_url()?>purchasing/create_do">
                <div class="btn btn-primary right10">
                  <h2><i class="fa fa-truck fa-white"></i></h2>
                  <p>New Delivery Order</p>
                </div>
              </a>
            </div>
          </div>

          <!-- Today status ends -->
          
          <!-- Dashboard Graph starts -->
          <div class="row top50">
            <div class="col-md-8 portlets">

              <!-- Widget -->
              <div class="widget white headless footless">
                <!-- Widget head -->
                <div class="widget-head">
                  <div class="pull-left">Sales History for <?php echo date('Y')?></div>
                  <div class="clearfix"></div>
                </div>              
                <div class="widget-content">
                    <div id="graph1"></div>
                </div>
              </div>
            </div>
            <div class="col-md-4 portlets">

              <!-- Widget -->
              <div class="widget white headless footless">
                <!-- Widget content -->
                <div class="widget-head">
                  <div class="pull-left">Sales History for <?php echo date('F Y')?></div>
                  <div class="clearfix"></div>
                </div>
                <div class="widget-content">
                    <div id="graph2"></div>
                </div>
                <!-- Widget ends -->

              </div>
              <!-- Widget -->
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 portlets">

              <!-- Widget -->
              <div class="widget">
                <!-- Widget head -->
                <div class="widget-head">
                  <div class="pull-left"><i class="fa fa-bar-chart"></i> Income History for <?php echo date('Y')?></div>
                  <div class="widget-icons pull-right">
                    <a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a>
                    <a href="#" class="wsettings"><i class="fa fa-wrench"></i></a>  
                    <a href="#" class="wclose"><i class="fa fa-times"></i></a>
                  </div>  
                  <div class="clearfix"></div>
                </div>              

                <!-- Widget content -->
                <div class="widget-content">
                  <div class="padd">

                    <div id="bar-chart2"></div>

                  </div>
                </div>
                <!-- Widget ends -->

              </div>
              
            </div>

          </div>
          <!-- Dashboard graph ends -->
        </div>
		  </div>

		<!-- Matter ends -->
<script>
$(document).ready(function(){

var graph1 = function(){
      $("#graph1").html("");
      var tax_data = [
         <?php foreach($line_info as $period=>$value){?>
         {"period": "<?php echo date('Y').'-'.str_pad($period,2,0,STR_PAD_LEFT)?>", "invoices": "<?php echo $value['invoices']?>", "cash_sales": "<?php echo $value['cash_sales']?>", "sales_orders": "<?php echo $value['sales_orders']?>", "delivery_orders": "<?php echo $value['delivery_orders']?>"},
         <?php }?>
      ];
      
      Morris.Line({
        element: 'graph1',
        data: tax_data,
        xkey: 'period',
        xLabels: 'month',
        hideHover: 'auto',
        ykeys: ['invoices', 'cash_sales', 'sales_orders', 'delivery_orders'],
        labels: ['Invoices', 'Cash Sales', 'Sales Orders', 'Delivery Orders']
      });
    }

 var graph2 = function(){
      $("#graph2").html("");
      Morris.Donut({
        element: 'graph2',
        data: [
        <?php foreach($pie_info as $value) {?>
          {label: "<?php echo $value['label']?>", value: <?php echo $value['num']?>},
        <?php }?>
        ],
        hideHover: 'auto',
        colors: [
        <?php foreach($pie_info as $value) {?>
          "<?php echo $value['color']?>",
        <?php }?>
        ]
      });
    }

    var graph3 = function(){
      $("#bar-chart2").html("");
      Morris.Bar({
        element: 'bar-chart2',
        data: [
        <?php foreach($bar_info as $period=>$value){?>
          {"period": "<?php echo date('Y').'-'.str_pad($period,2,0,STR_PAD_LEFT)?>", "invoices": "<?php echo $value['invoices']?>", "cash_sales": "<?php echo $value['cash_sales']?>", "sales_orders": "<?php echo $value['sales_orders']?>"},
        <?php }?>
        ],
        xkey: 'period',
        ykeys: ['invoices', 'cash_sales', 'sales_orders'],
        labels: ['Invoices', 'Cash Sales', 'Sales Orders'],
        hideHover: 'auto',
        barColors: [ "#56626B","#F07C6C", "#999"]
      });
    
    }
   
    // Init Charts
    graph1();
    graph2();
    graph3();
    // Resize Second Chart on page resize
    $(window).resize(debounce(graph1,200));
    $(window).resize(debounce(graph2,200));
    $(window).resize(debounce(graph3,200));

});

</script>
<?php $this->load->view('templates/footer');?>