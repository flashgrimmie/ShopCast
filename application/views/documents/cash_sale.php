<?php $this->load->view('templates/head_pdf'); ?>

<body>
<?php //echo '<pre>' . print_r($info, true) . '</pre>'; ?>
<div class="container">

    <div class="col-md-12 text-left">
                <table>
                    <tr>
                        <td width="60%">
                            <img src="assets/img/booker.jpg" width="150" style="margin: 0px;margin-bottom: -25px;margin-left: -15px;" />
                        </td>
                        <td width="15%">&nbsp;</td>
                        <td width="25%" style="padding-top: 30px;"><h2>CASH SALE</h2></td>
                    </tr>
                </table>
                <!-- h1><?php echo $outlet->name; ?><?php //echo date('Y',strtotime($info['date_issue']))?></h1 -->
                <table>
                    <tr>
                        <td>
                            <h6>P.O.Box 2007</h6>
                            <h6>BANDAR SERI BEGAWAN BS8674</h6>
                            <h6>Negara Brunei Darussalam</h6>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <table style="margin-top: 20px; margin-bottom: 20px;">
                    <tr>
                        <td width="70%">
                            <h6><strong>Shop & Office</strong></h6>
                            <h6 style="margin-top: 0px;"><?php echo $outlet->address1; ?>,<br/> <?php echo $outlet->address2; ?>, Bandar Seri Begawan, <br /> NEGARA BRUNEI DARUSSALAM</h6>
                            <h6>Tel: <?php echo $outlet->contact; ?>, Fax: <?php echo $outlet->fax; ?></h6>
                        </td>
                        <td width="20%" class="text-right">
                            <h4><strong>No:</strong> <span style="font-size: 15px;color: red;"><?php echo $info['cs_id']; ?></span></h4> 
                        </td>
                        <td class="text-right">
                           &nbsp;
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td width="75%">&nbsp;</td>
                        <td width="5%">Date:</td>
                        <td width="20%" style="border-bottom: 1px dotted black;"><?php echo format_date($info['date'])?></td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td width="5%">M/S</td>
                        <td width="65%" style="border-bottom: 1px dotted black;">&nbsp;</td>
                        <td width="30%" >&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" width="70%" style="border-bottom: 1px dotted black;">&nbsp;</td>
                        <td width="30%" >&nbsp;</td>
                    </tr>
                </table>
            </div>

    <?php
        if( empty( $i_items ) ) { ?>
            <div class="row">
<!--                 <div class="col-md-12 text-center">
                    <div class="col-md-12 text-center">
                        <h1><?php echo $outlet->name; ?><?php //echo date('Y',strtotime($info['date_issue']))?></h1>
						<h4><?php echo $outlet->address1; ?><br/> <?php echo $outlet->address2; ?></h4>
						<h4>Tel: <?php echo $outlet->contact; ?></h4>
                    </div>
                </div> -->
                <!--<div class="col-md-12 text-center top10"><h2>Cash Sale #<?php /*echo $info['cs_id'] */ ?></h2></div>-->
<!--                 <div class="col-md-12">
                    <table class="top50">
                        <tr>

                            <td width="500">
                                <table border="0" class="info_table">
                                    <tbody>
                                    <tr>
                                        <td class="text-right"><b>Cash Sale No:&nbsp;</b></td>
                                        <td class="text-left"><?php echo $info['cs_id']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>Date:&nbsp;</b></td>
                                        <td class="text-left"><?php echo format_date($info['date']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>Page No:&nbsp;</b></td>
                                        <td class="text-left"><?php echo $page . '/' . $page_total; ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>

                        </tr>
                    </table>
                </div>
            </div> -->
        <?php
        }

        for ($page = 1, $page_total = ceil(count($i_items) / 10); $page <= $page_total; $page++) {
            ?>
            <style type="text/css">
                body { background-color: #ffffff;  }  td.text-right { text-align: right  } .text-center { text-align: center }
                .table td {  border-left: 1px solid #000000;  border-right: 1px solid #000000;  border-bottom: 0;  padding: 5px 5px !important;
                    font-size: 12px;
                }  td.text-left {  text-align: left } .info_table, .info_table td {   border: none !important;  }
                .table th{  border: 1px solid #000000 !important;  padding: 10px 5px !important;  font-size: 12px; vertical-align: middle;  }
                .table tbody tr:last-child td {  border-bottom: 1px solid #000000 !important;  }
            </style>

            <!-- div class="row">
                <div class="col-md-12 text-center top10"><h2>Cash Sale #<?php /*echo $info['cs_id'] */?></h2></div>
                <div class="col-md-12">
                    <table class="top50">
                        <tr>

                            <td width="500">
                                <table border="0" class="info_table">
                                    <tbody>
                                    <tr>
                                        <td class="text-right"><b>Cash Sale No:&nbsp;</b></td>
                                        <td class="text-left"><?php echo $info['cs_id']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>Date:&nbsp;</b></td>
                                        <td class="text-left"><?php echo format_date($info['date']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>Page No:&nbsp;</b></td>
                                        <td class="text-left"><?php echo $page . '/' . $page_total; ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>

                        </tr>
                    </table>
                </div>
            </div --> 
            <div class="row">
                <div class="col-md-12">
                    <!--<h4 class="top50">Items</h4>-->
                    <table class="table top10" border="1" style="text-align:left; margin-top: 30px;">>
                        <thead>
                            <tr>
                                <th class="text-center" width="10%"><b>QUANTITY</b></th>
<!--                                 <th width="50"><b>Item No.</b></th> -->
                                <!-- <th><b>Stock Code</b></th> -->
                                <th width="70%"><b>Description</b></th>
                                <th class="text-center"  width="20%"><b>UNIT PRICE</b></th>
                                <th class="text-center"  width="20%"><b>AMOUNT</b></th>
                                
                               <!--  <th class="text-center"><b>Discount %</b></th>
                                <th class="text-center"><b>Total Cost</b></th> -->
                            <!--     <th class="text-center"><b>Note</b></th> -->
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $style = '';
                            for ($i = ($page * 10) - 10; $i <= ($page * 10) - 1; $i++) {

                                if (($i == ($page * 10) - 1) || ($i == count($i_items) - 1))
                                    $style = 'style="border-bottom: 1px solid #000000"';

                                if (!empty($i_items[$i])) {
                                    ?>
                                    <tr>
                                        <td <?php echo $style; ?> class="text-center"><?php echo $i_items[$i]['quantity']; ?></td>
                                      <!--   <td <?php echo $style; ?> ><?php echo $i + 1; ?></td> -->
                                  <!--       <td <?php echo $style; ?> ><?php echo $i_items[$i]['stock_num']; ?></td> -->
                                        <td <?php echo $style; ?> ><?php echo $i_items[$i]['description']; ?></td>
                                        <td <?php echo $style; ?> class="text-center"><?php echo format_price($i_items[$i]['price']); ?></td>
                                        <td <?php echo $style; ?> class="text-center"><?php echo format_price($i_items[$i]['price']*$i_items[$i]['quantity']); ?></td>
                                        
                                       <!--  <td <?php echo $style; ?> class="text-center"><?php echo $i_items[$i]['discount'];?></td> -->
                                       <!--  <td <?php echo $style; ?> class="text-right"><?php
                                                $desc_arr = explode("-", $i_items[$i]['discount']);
                                                $desc = $desc_arr[0];
                                                $total = (($i_items[$i]['price'] * (1 - $desc / 100) - $i_items[$i]['discount_value'] + $i_items[$i]['markup']) * $i_items[$i]['quantity']);
                                                if (sizeof($desc_arr) > 1 && $desc_arr[1] != '') {
                                                    $desc = $desc_arr[1];
                                                    $total = ($total * (1 - $desc / 100));
                                                }
                                                echo format_price($total);
                                            ?></td> -->
                                        <!-- <td <?php echo $style; ?> ><?php echo $i_items[$i]['returned'] == 'Y' ? 'returned' : '' ?></td> -->
                                    </tr>
                                <?php
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if ($page < $page_total && $page != $page_total) { ?>
                <div style="page-break-after:always"></div>
            <?php } ?>
        <?php } ?>

    <div class="row">
        <div class="col-md-12 text-center top10">
           <!-- <div class="text-right">Subtotal:&nbsp;<b><?php /*echo format_price($info['subtotal']) */?></b></div>
            <div class="text-right">Discount:&nbsp;<b><?php /*echo $info['discount'] */?>%</b></div>-->
            <div class="text-right">Total:&nbsp;<b><?php echo format_price($info['total']) ?></b></div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center top50">
                <table>
                    <tr>
                        <td class="text-left" width="75%">
                            <div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                            <br/>

                            <div style="text-align:center;"><?php echo $outlet->name; ?></div>
                            <br/>
                        </td>
                        <td class="text-center">
                            <div class="underlined">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                            <br/>

                            <div style="text-align:center;width:100%;display:block;">Received by</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>