<?php $this->load->view('templates/head_pdf');?>
<style>
    @page {
      size: auto; 
      margin: 0mm;
      margin-header: 0mm;
      margin-footer: 0mm;
    }
    body,h5 {
        /*font-size: 16px;*/
    }
    h3 {
        font-size: 20px;
    }
    .top10 tr td{
        line-height:35mm;
    }
</style>
<body>
<div class="container">
    <div class="row" class="text-center">
        <div class="col-md-12">
            <h3 class="top10"><img src="assets/img/booker.jpg" width="150" style="margin: 0px;margin-bottom: -25px;margin-left: -15px;" /></h3>
            <h5><?php echo $info['host_outlet']?></h5>
            <h5><?php echo $info['host_address1'].' '.$info['host_address2']?></h5>
            <h5 style="text-align:center"><?php echo $info['host_contact']?></h5>
        </div>
    </div>
    <?php if($info['return_id']) {?>
    <div class="row" class="text-center">
            <div class="col-md-12 text-center top10"><h4>Original Receipt #<?php echo $info['return_id']?></h4></div>
    </div>
    <?php }?>
    <div class="row top10" style="border-top:3px solid #000;border-bottom:3px solid #000;">
        <div class="col-md-12">
            <h5>Receipt No:<?php echo $info['cs_id']?> <?php echo format_time($info['cs_time_stamp'])?></h5>
            <h5>Oper:<?php echo $info['staff']?>,<?php echo $info['host_outlet']?></h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table top10 text-center">
            <?php foreach($i_items as $item):
                    $price=($item['price']*(1-$item['discount']/100))*$item['quantity'];?>
                    <tr>
                        <td><?php echo $item['barcode'];?></td>
                        <td><?php echo $item['brand'];?></td>
                        <td><?php echo $item['description'];?></td>
                    </tr>
                    <tr style="padding:30px">
                        <td></td>
                        <td><?php echo $item['quantity'];?> @ <?php echo format_price($item['price']);?></td>
                        <td><?php echo format_price($item['returned']=='Y' ? -$price : $price);?></td>
                    </tr>
                    <tr <?php echo !$item['discount'] ? 'style="display:none"' : ''?>>
                        <td></td>
                        <td>Less - <?php echo format_number($item['discount']);?>%</td>
                        <td>- $<?php echo format_number($item['discount_value']);?></td>
                    </tr>
            <?php endforeach;?>
            <?php if(isset($i_additional)) foreach($i_additional as $item):?>
                    <tr>
                        <td><?php echo $item->description;?></td>
                    </tr>
                    <tr style="padding:30px">
                        <td></td>
                        <td><?php echo $item->quantity?> @ <?php echo format_number($item->price);?></td>
                        <td><?php echo format_price($item->quantity*$item->price);?></td>
                    </tr>
                    <tr <?php echo !$item->discount ? 'style="display:none"' : ''?>>
                        <td></td>
                        <td>Less - <?php echo format_number($item->discount);?>%</td>
                        <td>- $<?php echo format_number($item['discount_value']);?></td>
                    </tr>
            <?php endforeach;?>
            </table>
        </div>
    </div>

<div class="row">
    <div class="col-md-12 text-center top10">
        <table>
            <tr>
                <td>Discount % :</td>
                <td></td>
                <td class="text-right"><?php echo $info['discount']?></td>
            </tr>
            <tr>
                <td>TOTAL:</td>
                <td></td>
                <td class="text-right"><?php echo format_price2d($info['total'])?></td>
            </tr>
            <?php if($info['deposit']) {?>
            <tr>
                <td>Deposit:</td>
                <td></td>
                <td class="text-right"><?php echo format_price2d($info['deposit'])?></td>
            </tr>
            <?php }?>
            <?php if($info['discount_val']) {?>
            <tr>
                <td>Voucher:</td>
                <td></td>
                <td class="text-right"><?php echo $info['voucher']?><br/><?php echo format_price($info['discount_val'])?></td>
            </tr>
            <?php }?>
            <?php if($info['credit_amount']) {?>
            <tr>
                <td>Credit Card:</td>
                <td><?php echo $info['card_type']?></td>
                <td class="text-right"><?php echo format_price($info['credit_amount'],$info['currency'])?></td>
            </tr>
            <?php }?>
            <tr>
                <td>Cash:</td>
                <td></td>
                <td class="text-right"><?php echo format_price2d($info['tender'])?></td>
            </tr>
            <tr>
                <td>Tender:</td>
                <td></td>
                <td class="text-right"><?php echo format_price2d($info['tender'])?></td>
            </tr>
            <tr>
                <td>Change:</td>
                <td></td>
                <td class="text-right"><?php echo format_price2d($info['change'])?></td>
            </tr>
            <tr>
                <td>Remarks:</td>
                <td class="text-right"><?php echo $info['remark']?></td>
            </tr>
            <tr>
                <td>Group:</td>
                <td><?php echo $info['customer_group'] ? $info['customer_group'] : 'Non member'?></td>
            </tr>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-center top50">
            <table>
                <tr>
                    <td class="text-center">
                        <h3>THANK YOU.</h3>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">
                        GOODS ARE NOT REFUNDABLE OR RETURNABLE. PLEASE COME AGAIN.
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</body>
</html>