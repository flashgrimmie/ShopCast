<?php $this->load->view('templates/head_pdf'); ?>
<body>
<div class="container">
    <div class="col-md-12 text-center">
        <img src="assets/img/booker.jpg" width="150" />
    </div>
<?php
    if (empty($i_items)) {
        ?>
        <div class="row" style="margin-top: 0">
            <div class="col-md-12 text-center">
                <h1><?php echo $outlet->name; ?><?php //echo date('Y',strtotime($info['date_issue']))?></h1>
            <h4><?php echo $outlet->address1; ?><br/> <?php echo $outlet->address2; ?></h4>
            <h4>Tel: <?php echo $outlet->contact; ?></h4>
            <h4>Fax: <?php echo $outlet->fax; ?></h4>
            </div>
            <div class="col-md-12">
                <table class="top50">
                    <tr>
                        <td><b>TO:</b></td>
                    </tr>
                    <tr>
                        <td class="col-md-8">

                            <table border="0" class="info_table">
                                <tbody>
                                <tr>
                                    <td>
                                        <div><b><?php echo $info['customer_name']; ?></b></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div><b><?php echo trim($info['customer_address']); ?></b></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div><b><?php echo trim($info['customer_address1']); ?></b></div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table border="0" class="info_table">
                                <tbody>
                                <tr>
                                    <td class="text-right">&nbsp;</td>
                                    <td class="text-left">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>ATTN:&nbsp;</b></td>
                                    <td class="text-left"><?php echo trim($info['attention']); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>TEL:&nbsp;</b></td>
                                    <td class="text-left"><?php echo $info['customer_phone'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>FAX:&nbsp;</b></td>
                                    <td class="text-left"><?php echo $info['customer_fax'] ?></td>
                                </tr>

                                </tbody>
                            </table>

                        </td>
                        <td class="col-md-4 text-right" style="float: right">
                            <table border="0" class="info_table">
                                <tbody>
                                <tr>
                                    <td class="text-right"><b>INVOICE NO:&nbsp;</b></td>
                                    <td class="text-left"><?php echo $info['invoice_id'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>D/O N0:&nbsp;</b></td>
                                    <td class="text-left"><?php echo $info['delivery_order_no'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>DATE:&nbsp;</b></td>
                                    <td class="text-left"><?php echo format_date($info['date_issue']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>TERM:&nbsp;</b></td>
                                    <td class="text-left">60 days</td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>PAGE NO:&nbsp;</b></td>
                                    <td class="text-left"><?php echo $page . '/' . $page_total; ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    <?php
    }
        for ($page = 1, $page_total = ceil(count( $i_items) / 10); $page <= $page_total; $page++) { ?>
            <!-- Page content -->
            <style type="text/css">
                body {  background-color: #ffffff; } td.text-right { text-align: right } .text-center {  text-align: center }
                .table td {  border-left: 1px solid #000000;  border-right: 1px solid #000000; border-bottom: 0;  padding: 5px 5px !important;
                    font-size: 12px;
                } td.text-left{text-align: left} .info_table, .info_table td{ border: none !important;}
                .table th { border: 1px solid #000000; padding: 10px 5px !important; font-size: 12px; vertical-align: middle; }
                .table tbody tr:last-child td { border-bottom: 1px solid #000000 !important;  }
            </style>
            <div class="row" style="margin-top: 0">
                <div class="col-md-12 text-center">
                   <h1><?php echo $outlet->name; ?><?php //echo date('Y',strtotime($info['date_issue']))?></h1>
					<h4><?php echo $outlet->address1; ?><br/> <?php echo $outlet->address2; ?></h4>
					<h4>Tel: <?php echo $outlet->contact; ?></h4>
                </div>
                <div class="col-md-12">
                    <table class="top50">
                        <tr>
                            <td><b>TO:</b></td>
                        </tr>
                        <tr>
                            <td class="col-md-8">

                                <table border="0" class="info_table">
                                    <tbody>
                                    <tr>
                                       <td>
                                           <div><b><?php echo $info['customer_name'];  ?></b></div>
                                       </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div><b><?php echo trim($info['customer_address']); ?></b></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div><b><?php echo trim($info['customer_address1']); ?></b></div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <table border="0" class="info_table">
                                    <tbody>
                                    <tr>
                                        <td class="text-right">&nbsp;</td>
                                        <td class="text-left">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>ATTN:&nbsp;</b></td>
                                        <td class="text-left"><?php echo trim($info['attention']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>TEL:&nbsp;</b></td>
                                        <td class="text-left"><?php echo $info['customer_phone'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>FAX:&nbsp;</b></td>
                                        <td class="text-left"><?php echo $info['customer_fax'] ?></td>
                                    </tr>

                                    </tbody>
                                </table>

                            </td>
                            <td class="col-md-4 text-right" style="float: right">
                                <table border="0" class="info_table">
                                    <tbody>
                                        <tr>
                                            <td class="text-right"><b>INVOICE NO:&nbsp;</b></td>
                                            <td class="text-left"><?php echo $info['invoice_id'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><b>D/O N0:&nbsp;</b></td>
                                            <td class="text-left"><?php echo $info['delivery_order_no'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><b>DATE:&nbsp;</b></td>
                                            <td class="text-left"><?php echo format_date($info['date_issue']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><b>TERM:&nbsp;</b></td>
                                            <td class="text-left">60 days</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right"><b>PAGE NO:&nbsp;</b></td>
                                            <td class="text-left"><?php echo $page . '/' . $page_total; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!--<h4 class="top50">Items</h4>-->
                    <table class="table top10" style="text-align:left; margin-top: 30px;">
                        <thead>
                        <tr>
                            <th width="70"><b>ITEM NO.</b></th>
                            <th width="120"><b>STOCK CODE</b></th>
                            <th width="200"><b>DESCRIPTION</b></th>
                            <th class="text-center"><b>QTY</b></th>
                            <th class="text-right"><b>UNIT PRICE</b></th>
                            <th class="text-center"><b>DISCOUNT %</b></th>
                            <th width="120" class="text-center"><b>TOTAL AMOUNT</b></th>
                        </tr>
                        </thead>
                        <tbody>


                        <?php

                            foreach ($i_bal as $item) {

                                if ($item['opening_balance'] != '0') {
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td colspan="4">Opening Balance (<?php echo $item['opening_balance_desc']; ?>)
                                        </td>
                                        <td><?php echo '$' . $item['opening_balance']; ?></td>
                                    </tr>
                                <?php
                                }
                            } ?>
                        <?php
                            $style =  '';
                            for( $i = ($page*10)-10; $i <= ($page*10)-1; $i++ ){

                                if( ($i == ($page * 10) - 1)  || ($i == count( $i_items) -1 ))
                                    $style = 'style="border-bottom: 1px solid #000000"';

                                if( !empty($i_items[$i]) ){
                                    ?>
                                        <tr>
                                            <td <?php echo $style; ?>><?php echo $i + 1; ?></td>
                                            <td <?php echo $style; ?> align="left"><?php echo $i_items[$i]['stock_num']; ?></td>
                                            <td <?php echo $style; ?> align="left"><?php echo $i_items[$i]['description']; ?></td>
                                            <td <?php echo $style; ?> class="text-center"> <?php echo $i_items[$i]['quantity']; ?></td>
                                            <td <?php echo $style; ?> class="text-right"><?php echo format_price($i_items[$i]['price']); ?></td>
                                            <td <?php echo $style; ?> class="text-center"> <?php echo $i_items[$i]['discount']; ?></td>
                                            <?php
                                                $desc_arr = explode("-", $i_items[$i]['discount']);
                                                $desc = $desc_arr[0];
                                                $total = (($i_items[$i]['price'] * (1 - $desc / 100) - $i_items[$i]['discount_value'] + $i_items[$i]['markup']) * $i_items[$i]['quantity']);
                                                if (sizeof($desc_arr) > 1 && $desc_arr[1] != '') {
                                                    $desc = $desc_arr[1];
                                                    $total = ($total * (1 - $desc / 100));
                                                }

                                            ?>
                                            <td <?php echo $style; ?>class="text-right"><?php echo format_price($total); ?></td>
                                        </tr>
                                    <?php
                                }

                            }
                         ?>
                        </tbody>

                    </table>
                </div>
            </div>
            <?php if($page < $page_total && $page != $page_total ){  ?>
                <div style="page-break-after:always"></div>
            <?php } ?>
    <?php
        } // end paging loop

    ?>


    <!-- footer -->
    <?php if ($info['remark']) { ?>
        <div class="row">
            <div class="col-md-6"><b>Remark:</b> <?php echo $info['remark'] ?></div>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-12 text-center top10">
            <?php if (isset($item['opening_balance']) || $item['opening_balance'] != null) {
                ?>
                <div class="text-right">
                    Total:&nbsp;<b><?php echo str_replace('-', '', format_price($item['opening_balance'] - $info['total'])); ?></b></div>
            <?php } else { ?>
                <div class="text-right">Total:&nbsp;<b><?php echo str_replace('-', '', format_price($info['total'])); ?></b></div>
            <?php } ?>
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