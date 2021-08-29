<?php $this->load->view('templates/head')?>
<body>
<header>
<div class="navbar navbar-fixed-top bs-docs-nav" role="banner">
  
    <div class="container">
      <!-- Menu button for smallar screens -->
      <div class="navbar-header">
		  <button class="navbar-toggle btn-navbar" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse"><span>Menu</span></button>
      <a href="#" class="pull-left menubutton hidden-xs"><i class="fa fa-bars"></i></a>
		  <!-- Site name for smallar screens -->
		  <a href="<?php echo base_url()?>" class="navbar-brand">Shop<span class="bold">Cast</span></a>
		</div>

      <!-- Navigation starts -->
      <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">         
        
        <!-- Links -->
        <ul class="nav navbar-nav pull-right">
          <li class="dropdown pull-right user-data">            
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
              Hello <span class="bold"><?php echo $this->session->userdata('name')?>&nbsp;(<?php echo $this->session->userdata('outlet_name')?>)</span> <b class="caret"></b>              
            </a>
            
            <!-- Dropdown menu -->
            <ul class="dropdown-menu">
              <li><a href="<?php echo base_url()?>system_setup/profile"><i class="fa fa-user"></i> Profile</a></li>
              <li><a href="<?php echo base_url() ?>login/logout"><i class="fa fa-key"></i> Logout</a></li>
            </ul>
          </li>


          <li class="dropdown dropdown-big leftonmobile">
            <a href="#" id="refresh" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-refresh"></i></a>
          </li>

          <li class="dropdown dropdown-big leftonmobile">
            
              <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                <i class="fa fa-shopping-cart"></i>
                  <span class="label label-info numitems" <?php echo !$this->session->userdata('cart') ? 'style="display:none"':'' ?>><?php echo $this->session->userdata('cart') ? sizeof($this->session->userdata('cart')) : '0' ?></span> 
              </a>

                <ul class="dropdown-menu itemsincart">
                  <li class="dropdown-header padless">
                    <!-- Heading - h5 -->
                    <h5><i class="fa fa-shopping-cart"></i> Cart</h5>
                    <!-- Use hr tag to add border -->                   
                  </li>
                  <?php if($this->session->userdata('cart')){ 
                    
                    foreach($this->session->userdata('cart') as $key=>$cart) {  ?>
                      <li  data-rem="<?php echo $key?>">
                          <!-- List item heading h6 -->
                          <h6><a href="#"><?php echo $cart['description'] ?></a>
                          <span class="pull-right removefromcart"><i class="fa fa-times"></i></span> 
                          <span class="label label-warning pull-right"><?php echo format_price($cart['cost_price']) ?></span></h6>
                          <div class="clearfix"></div>
                          <hr>
                      </li>

                    <?php } ?>
                  <li>
                    <div class="drop-foot">
                      <a href="<?php echo base_url('inventory/cartorder') ?>">Check Out</a>
                    </div>
                  </li> 
                  <?php } else { ?> 
                  <li>
                    <div class="drop-foot">
                      <a href="<?php echo base_url('inventory/cartorder') ?>">Your cart currently is empty</a>
                    </div>
                  </li> 
                  <?php } ?>                                  
                </ul>
                
            </li>



            <li class="dropdown dropdown-big leftonmobile">
            
              <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                <i class="fa fa-file"></i>
                  <span class="label label-info" <?php echo !isset($incomingPurchases[0]) ? 'style="display:none"':'' ?>><?php echo isset($incomingPurchases) ? sizeof($incomingPurchases) : '0' ?></span> 
              </a>

                <ul class="dropdown-menu itemsincart">
                  <li class="dropdown-header padless">
                    <!-- Heading - h5 -->
                    <h5><i class="fa fa-file"></i> Purchase Orders</h5>
                    <!-- Use hr tag to add border -->                   
                  </li>
                  <?php if(isset($incomingPurchases[0])){ 
                    
                    foreach($incomingPurchases as $key=>$notif) { ?>
                      <li  data-rem="<?php echo $key?>">
                          <!-- List item heading h6 -->
                          <h6><a href="javascript:popup('<?php echo base_url().'documents/inner_purchase_order/'.$notif->po_id ?>')"><?php echo $notif->name ?></a>
                          <span class="label label-warning pull-right"><?php echo format_date($notif->date) ?></span></h6>
                          <div class="clearfix"></div>
                          <hr>
                      </li>

                    <?php } ?>
                  <li>
                    <div class="drop-foot">
                      <a href="<?php echo base_url('purchasing/incommingpo') ?>">View All</a>
                    </div>
                  </li> 
                  <?php } else { ?> 
                  <li >
                      <!-- List item heading h6 -->
                      <h6><a href="#">No new orders</a></h6>
                      <div class="clearfix"></div>
                      <hr>
                  </li>
                  <li>
                      <div class="drop-foot">
                        <a href="<?php echo base_url('purchasing/incommingpo') ?>">View All</a>
                      </div>
                  </li> 
                  <?php } ?>                                  
                </ul>
                
            </li> 


            <li class="dropdown dropdown-big leftonmobile">
            
              <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                <i class="fa fa-truck"></i>
                  <span class="label label-info" <?php echo !isset($outgoingDeliveries[0]) ? 'style="display:none"':'' ?>><?php echo isset($outgoingDeliveries) ? sizeof($outgoingDeliveries) : '0' ?></span> 
              </a>

                <ul class="dropdown-menu itemsincart">
                  <li class="dropdown-header padless">
                    <!-- Heading - h5 -->
                    <h5><i class="fa fa-truck"></i> Delivery Orders</h5>
                    <!-- Use hr tag to add border -->                   
                  </li>
                  <?php if(isset($outgoingDeliveries[0])){ 
                    
                    foreach($outgoingDeliveries as $key=>$notif) { ?>
                      <li  data-rem="<?php echo $key?>">
                          <!-- List item heading h6 -->
                          <h6><a href="javascript:popup('<?php echo base_url().'documents/delivery_order/'.$notif->do_id ?>')"><?php echo $notif->name ?></a>
                          <span class="label label-warning pull-right"><?php echo format_date($notif->date) ?></span></h6>
                          <div class="clearfix"></div>
                          <hr>
                      </li>

                    <?php } ?>
                  <li>
                    <div class="drop-foot">
                      <a href="<?php echo base_url('purchasing/incomingDelivery') ?>">View All</a>
                    </div>
                  </li> 
                  <?php } else { ?> 
                  <li >
                      <!-- List item heading h6 -->
                      <h6><a href="#">No new orders</a></h6>
                      <div class="clearfix"></div>
                      <hr>
                  </li>
                  <li>
                      <div class="drop-foot">
                        <a href="<?php echo base_url('purchasing/incomingDelivery') ?>">View All</a>
                      </div>
                  </li> 
                  <?php } ?>                                  
                </ul>
                
            </li> 


        </ul>
      </nav>

    </div>
  </div>
</header>
<!-- Main content starts -->

<div class="content">

  	<!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-dropdown"><a href="#">Navigation</a></div>
        <!-- If the main navigation has sub navigation, then add the class "has_sub" to "li" of main navigation. -->
        <ul id="nav">
          <!-- Main menu with font awesome icon -->
          <li><a href="<?php echo base_url()?>" class="open"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
          <li class="has_sub"><a href="#" class="<?php echo isset($active) && $active=='purchasing' ? 'open' : ''?>"><i class="fa fa-truck"></i> <span>Purchasing</span><span class="pull-right"><i class="fa fa-chevron-<?php echo isset($active) && $active=='purchasing' ? 'down' : 'left'?>"></i></span></a>
            <ul>
              <li><a class="<?php echo isset($subactive) && $subactive=='po' ? 'active' : ''?>" href="<?php echo base_url()?>purchasing/purchase_orders">Purchase Orders</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='iopo' ? 'active' : ''?>" href="<?php echo base_url()?>inventory/cartorderview">Inter Outlet Purchase Orders</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='pi' ? 'active' : ''?>" href="<?php echo base_url()?>purchasing/purchase_invoices">Purchase Invoices</a></li>
			  <li><a class="<?php echo isset($subactive) && $subactive=='rpi' ? 'active' : ''?>" href="<?php echo base_url()?>purchasing/purchase_returns">Debit Notes</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='draft' ? 'active' : ''?>" href="<?php echo base_url()?>purchasing/draft_transactions">Draft Transactions</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='do' ? 'active' : ''?>" href="<?php echo base_url()?>purchasing/delivery_orders">Delivery Orders</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='piitems_record' ? 'active' : ''?>" href="<?php echo base_url()?>purchasing/piitems_record">PI Items Record</a></li>
            </ul>
          </li>    

                                    
          
          <li class="has_sub"><a href="#" class="<?php echo isset($active) && $active=='inventory' ? 'open' : ''?>"><i class="fa fa-table"></i> <span>Inventory</span><span class="pull-right"><i class="fa fa-chevron-<?php echo isset($active) && $active=='inventory' ? 'down' : 'left'?>"></i></span></a>
            <ul>
              <li><a class="<?php echo isset($subactive) && $subactive=='inventory' ? 'active' : ''?>" href="<?php echo base_url()?>inventory">Inventory</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='stock_list' ? 'active' : ''?>" href="<?php echo base_url()?>inventory/stocklist">Stock List</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='stock_take' ? 'active' : ''?>" href="<?php echo base_url()?>inventory/stock_take">Stock Take</a></li>
			  <li><a class="<?php echo isset($subactive) && $subactive=='stock_balance' ? 'active' : ''?>" href="<?php echo base_url()?>inventory/stock_balance">Stock Balance</a></li>             
              <li><a class="<?php echo isset($subactive) && $subactive=='item_report' ? 'active' : ''?>" href="<?php echo base_url()?>inventory/itemReport">Item Report</a></li>
            </ul>
          </li> 
          
          <li class="has_sub"><a href="#" class="<?php echo isset($active) && $active=='retail' ? 'open' : ''?>"><i class="fa fa-shopping-cart"></i> <span>Retail</span><span class="pull-right"><i class="fa fa-chevron-<?php echo isset($active) && $active=='retail' ? 'down' : 'left'?>"></i></span></a>
            <ul>
              <li><a class="<?php echo isset($subactive) && $subactive=='invoices' ? 'active' : ''?>" href="<?php echo base_url()?>retail/invoices">Invoices</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='do' ? 'active' : ''?>" href="<?php echo base_url()?>retail/delivery_orders">Delivery Orders</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='so' ? 'active' : ''?>" href="<?php echo base_url()?>retail/sales_orders">Sales Orders</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='cs' ? 'active' : ''?>" href="<?php echo base_url()?>retail/cash_sales">Cash Sales</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='returned' ? 'active' : ''?>" href="<?php echo base_url()?>retail/returned_goods">Returned Goods</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='draft' ? 'active' : ''?>" href="<?php echo base_url()?>retail/draft_transactions">Draft Transactions</a></li>
			  <li><a class="<?php echo isset($subactive) && $subactive=='cn' ? 'active' : ''?>" href="<?php echo base_url()?>retail/credit_notes">Credit Notes</a></li>
		<li><a class="<?php echo isset($subactive) && $subactive=='returnsnotes' ? 'active' : ''?>" href="<?php echo base_url()?>retail/returns_notes">Cash Sales Return</a></li>

            </ul>
          </li> 
          
          <li class="has_sub"><a href="#" class="<?php echo isset($active) && $active=='setup' ? 'open' : ''?>"><i class="fa fa-cogs"></i> <span>System Setup</span><span class="pull-right"><i class="fa fa-chevron-<?php echo isset($active) && $active=='setup' ? 'down' : 'left'?>"></i></span></a>
            <ul>
              <li><a class="<?php echo isset($subactive) && $subactive=='suppliers' ? 'active' : ''?>" href="<?php echo base_url()?>system_setup/suppliers">Suppliers</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='customers' ? 'active' : ''?>" href="<?php echo base_url()?>system_setup/customers">Customers</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='categories' ? 'active' : ''?>" href="<?php echo base_url()?>system_setup/categories">Categories</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='outlets' ? 'active' : ''?>" href="<?php echo base_url()?>system_setup/outlets">Outlets</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='users' ? 'active' : ''?>" href="<?php echo base_url()?>system_setup/users">Users</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='user_permissions' ? 'active' : ''?>" href="<?php echo base_url()?>system_setup/user_permissions">User Permissions</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='invsnapshots' ? 'active' : ''?>" href="<?php echo base_url()?>system_setup/InvSnapShots">Inventory Snapshots</a></li>
            </ul>
          </li>
          
          <li class="has_sub"><a href="#" class="<?php echo isset($active) && $active=='finance' ? 'open' : ''?>"><i class="fa fa-bar-chart-o"></i> <span>Financial Reporting</span><span class="pull-right"><i class="fa fa-chevron-<?php echo isset($active) && $active=='finance' ? 'down' : 'left'?>"></i></span></a>
            <ul>
              <li><a class="<?php echo isset($subactive) && $subactive=='general_ledger' ? 'active' : ''?>" href="<?php echo base_url()?>finance/general_ledger">General Ledger</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='daily' ? 'active' : ''?>" href="<?php echo base_url()?>finance/daily_balance">Daily Balance</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='petty_cash' ? 'active' : ''?>" href="<?php echo base_url()?>finance/petty_cash">Petty Cash</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='onetime' ? 'active' : ''?>" href="<?php echo base_url()?>finance/oneTimeExpenses">One Time Expenses</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='recurring' ? 'active' : ''?>" href="<?php echo base_url()?>finance/recurring_expenses">Recurring Expenses</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='stock_purchases' ? 'active' : ''?>" href="<?php echo base_url()?>finance/stockPurchases">Stock Purchases</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='other_purchases' ? 'active' : ''?>" href="<?php echo base_url()?>finance/otherPurchases">Other Purchases</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='debtors' ? 'active' : ''?>" href="<?php echo base_url()?>finance/debtors">Debtors</a></li>
              <li><a class="<?php echo isset($subactive) && $subactive=='creditors' ? 'active' : ''?>" href="<?php echo base_url()?>finance/creditors">Creditors</a></li>
            </ul>
          </li>
        </ul>
    </div>
    <!-- Sidebar ends -->
        <!-- Main bar -->
    <div class="mainbar">
      
      <!-- Page heading -->
      <div class="page-head">
        <h2 class="pull-left"><?php echo $headline?></h2>
        <div class="clearfix"></div>
        <!-- Breadcrumb -->
        <div class="bread-crumb">
          <a href="#"><i class="fa fa-home"></i> Home</a> 
          <!-- Divider -->
          <?php foreach($breadcrumbs as $key=>$value):?>
            <span class="divider">/</span> 
            <a href="<?php echo $value?>" class="bread-current"><?php echo $key?></a>
          <?php endforeach;?>
        </div>
        
        <div class="clearfix"></div>

      </div>

      <?php if($this->session->flashdata('error') || $this->session->flashdata('success')) { ?>
      <!-- Page heading ends -->
      <div class="container">

        <div class="top10 col-md-12">
          <?php if($this->session->flashdata('error')) {?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error')?></div>
          <?php }?>
          <?php if($this->session->flashdata('success')) {?>
            <div class="alert alert-success col-md-12"><?php echo $this->session->flashdata('success')?></div>
          <?php }?>
        </div>

      </div>

      <?php }?>
