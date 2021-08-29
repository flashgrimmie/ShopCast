<?php $this->load->view('templates/head_pdf'); ?>
<body>
<?php
//echo '<pre>'.print_r( $customer, true ).'</pre>';
?>
<div class="container" style="background-color: #ffffff">
<?php
if (empty($items)) {
    ?>
    <div class="row">
        <div class="col-md-12 text-center">
           <h1><?php echo $outlet->name; ?><?php //echo date('Y',strtotime($info['date_issue']))?></h1>
            <h4><?php echo $outlet->address1; ?><br/> <?php echo $outlet->address2; ?></h4>
            <h4>Tel: <?php echo $outlet->contact; ?></h4>
        </div>
        <div class="col-md-12">
            <table class="top50">
                <tr>
                    <td class="col-md-6">
                        <table border="0" class="info_table">
                            <tbody>
                            <tr>
                                <td>
                                    <div><b><?php echo $customer['name']; ?></b></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div><b><?php echo trim($customer['address']); ?></b></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div><b><?php echo trim($customer['address_1']); ?></b></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!--<div>TEL: <?php /*echo $customer['phone'] */ ?></div>
                        <div>FAX: <?php /*echo $customer['fax'] */ ?></div>-->
                        <table border="0" class="info_table">
                            <tbody>
                            <tr>
                                <td class="text-right">&nbsp;</td>
                                <td class="text-left">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="text-right"><b>ATTN:&nbsp;</b></td>
                                <td class="text-left"><?php echo trim($customer['attention']); ?></td>
                            </tr>
                            <tr>
                                <td class="text-right"><b>TEL:&nbsp;</b></td>
                                <td class="text-left"><?php echo $customer['phone'] ?></td>
                            </tr>
                            <tr>
                                <td class="text-right"><b>FAX:&nbsp;</b></td>
                                <td class="text-left"><?php echo $customer['fax'] ?></td>
                            </tr>

                            </tbody>
                        </table>
                    </td>
                    <td class="col-md-6 text-right" style="float: right">
                        <table border="0" class="info_table">
                            <tbody>
                            <tr>
                                <td class="text-right"><b>Cash Sales Return ID:&nbsp;</b></td>
                                <td class="text-left"><?php echo CSR . $cn_no; ?></td>
                            </tr>
                            <tr>
                                <td class="text-right"><b>DATE:&nbsp;</b></td>
                                <td class="text-left"> <?php echo format_date($date_issue) ?></td>
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
$g_total = 0;
for ($page = 1, $page_total = ceil(count($items) / 10); $page <= $page_total; $page++) {
    ?>
    <!-- Page content -->
    <style type="text/css">
        body {
            background-color: #ffffff;
        }

        td.text-right {
            text-align: right
        }

        .text-center {
            text-align: center
        }

        .table td {
            border-left: 1px solid #000000;
            border-right: 1px solid #000000;
            border-bottom: 0;
            padding: 5px 5px !important;
            font-size: 12px;
        }

        td.text-left {
            text-align: left
        }

        .info_table, .info_table td {
            border: none !important;
        }

        .table th {
            border: 1px solid #000000;
            padding: 10px 5px !important;
            font-size: 12px;
            vertical-align: middle;
        }

        .table tbody tr:last-child td {
            border-bottom: 1px solid #000000 !important;
        }
    </style>
    <div class="row">
        <div class="col-md-12 text-center">
            <h1><?php echo $outlet->name; ?><?php //echo date('Y',strtotime($info['date_issue']))?></h1>
            <h4><?php echo $outlet->address1; ?><br/> <?php echo $outlet->address2; ?></h4>
            <h4>Tel: <?php echo $outlet->contact; ?></h4>
        </div>
        <div class="col-md-12">
            <table class="top50">
                <tr>
                    <td class="col-md-6">
                        <table border="0" class="info_table">
                            <tbody>
                            <tr>
                                <td>
                                    <div><b><?php echo $customer['name']; ?></b></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div><b><?php echo trim($customer['address']); ?></b></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div><b><?php echo trim($customer['address_1']); ?></b></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!--<div>TEL: <?php /*echo $customer['phone'] */ ?></div>
                        <div>FAX: <?php /*echo $customer['fax'] */ ?></div>-->
                        <table border="0" class="info_table">
                            <tbody>
                            <tr>
                                <td class="text-right">&nbsp;</td>
                                <td class="text-left">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="text-right"><b>ATTN:&nbsp;</b></td>
                                <td class="text-left"><?php echo trim($customer['attention']); ?></td>
                            </tr>
                            <tr>
                                <td class="text-right"><b>TEL:&nbsp;</b></td>
                                <td class="text-left"><?php echo $customer['phone'] ?></td>
                            </tr>
                            <tr>
                                <td class="text-right"><b>FAX:&nbsp;</b></td>
                                <td class="text-left"><?php echo $customer['fax'] ?></td>
                            </tr>

                            </tbody>
                        </table>
                    </td>
                    <td class="col-md-6 text-right" style="float: right">
                        <table border="0" class="info_table">
                            <tbody>
                            <tr>
                                <td class="text-right"><b>Cash Sales Return ID:&nbsp;</b></td>
                                <td class="text-left"><?php echo CSR . $cn_no; ?></td>
                            </tr>

                            <!-- <tr>
                                <td class="text-right"><b>CREDIT NOTE NO:&nbsp;</b></td>
                                <td class="text-left"><?php echo OUTLET_PREFIX . $cn_no; ?></td>
                            </tr> -->
                            
                            <tr>
                                <td class="text-right"><b>DATE:&nbsp;</b></td>
                                <td class="text-left"> <?php echo format_date($date_issue) ?></td>
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
            <table class="table top10" border="0" style="text-align:left;">
                <tr>
                    <th width="70"><b>Item No.</b></th>
                    <th width="120"><b>Stock Code</b></th>
                    <th width="230"><b>Description</b></th>
                    <th class="text-center"><b>Unit</b></th>
                    <th class="text-center"><b>Quantity</b></th>
                    <th class="text-center"><b>Discount %</b></th>
                    <!--<td><b>Markup($)</b></td>-->
                    <th class="text-right"><b>Total Cost</b></th>
                    <!--<td><b>Note</b></td>-->
                </tr>

                <?php
                $style = '';
                for ($i = ($page * 10) - 10; $i <= ($page * 10) - 1; $i++) {

                    if (($i == ($page * 10) - 1) || ($i == count($items) - 1))
                        $style = 'style="border-bottom: 1px solid #000000"';

                    if (!empty($items[$i])) {
                        ?>
                        <tr>
                            <td <?php echo $style; ?>><?php echo $i + 1; ?></td>
                            <td <?php echo $style; ?> align="left"><?php echo $items[$i]['stock_num']; ?></td>
                            <td <?php echo $style; ?> align="left"><?php echo $items[$i]['description']; ?></td>
                            <td class="text-right" <?php echo $style; ?>><?php echo format_price($items[$i]['price']); ?></td>
                            <td class="text-center" <?php echo $style; ?>><?php echo $items[$i]['qty']; ?></td>
                            <td class="text-center" <?php echo $style; ?>><?php echo $items[$i]['discount']; ?></td>
                            <?php
                            $desc_arr = explode("-", $items[$i]['discount']);
                            $desc = $desc_arr[0];
                            $total = (($items[$i]['price'] * (1 - $desc / 100) - $items[$i]['discount_value'] + $items[$i]['markup']) * $items[$i]['qty']);
                            if (sizeof($desc_arr) > 1 && $desc_arr[1] != '') {
                                $desc = $desc_arr[1];
                                $total = ($total * (1 - $desc / 100));
                            }
                            $g_total += $total;
                            ?>
                            <td <?php echo $style; ?> class="text-right"><?php echo format_price($total); ?></td>
                        </tr>
                    <?php
                    }
                }
                ?>
            </table>
        </div>
    </div>
    <?php if ($page < $page_total && $page != $page_total) { ?>
        <div style="page-break-after:always"></div>
    <?php } ?>

<?php } ?>
<div class="row">
    <div class="col-md-6"><b>Remark:</b> <?php echo $remark; ?></div>
</div>

<div class="row">
    <div class="col-md-12 text-center top10">

        <!--<div class="text-right">Subtotal:&nbsp;<b><?php /*echo format_price($g_total) */ ?></b></div>
            <div class="text-right">Discount:&nbsp;<b>0%</b></div>-->

        <div class="text-right">
            <div class="text-right">Total:&nbsp;<b><?php echo format_price($g_total) ?></b></div>
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