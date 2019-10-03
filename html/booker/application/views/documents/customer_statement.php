<?php $this->load->view('templates/head_pdf'); ?>
<style type="text/css">
    body {
        font-size: 13px;
        line-height: 24px;
        color: #000;
        border-top: 0px solid #eee;
        background: #ffffff;
        padding-top: 43px;
        font-family: 'Open Sans', sans-serif;
        -webkit-font-smoothing: antialiased;
    }
    td.text-right{ text-align: right }
    .top10 tr th {
        border-bottom: 1px solid #000;
    }
ol.ol-notes {
    counter-reset: item;
    line-height: 110%;        
}
li.li-notes {
    display: block;
    margin-left: 2em;
}
li.li-notes:before {
    display: inline-block;
    content: counter(item) ") ";
    counter-increment: item;
    width: 2em;
    margin-left: -2em;
}

#footer {
   position:absolute;
   bottom:0;
   width:100%;
   height:60px;     
}
</style>
<body>
<div class="container">

<div class="row">
    <div class="col-md-12 text-center">
        <div class="col-md-12 text-center">
	
            <h1><?php echo $outlet->name; ?><?php //echo date('Y',strtotime($info['date_issue']))?></h1>
            <h4><?php echo $outlet->address1; ?><br/> <?php echo $outlet->address2; ?></h4>
            <h4>Tel: <?php echo $outlet->contact; ?></h4>
        </div>
    </div>

    <div class="col-md-12 text-center top10"><h2>Statement of accounts as at <?php echo date('d/m/Y', strtotime($statement_date)) ?></h2></div>

    <div class="col-md-12">

        <table class="top50">
            <tr>
                <td width="500">

                    <div style="line-height:24px;"><b><?php echo $customer_details->name ?></b></div>
                    <div><b><?php echo $customer_details->address; ?></b></div>
                    <div><b><?php echo $customer_details->address_1; ?></b></div>
                    <div style="line-height:24px;"><b><?php echo $customer_details->phone ?></b></div>
                </td>
                <td>
                    <div style="line-height:24px; font-family:'Open Sans',sans-serif;">A/C CODE:&nbsp;<b><?php echo $customer_details->customer_id; ?></b></div>
                    <div style="line-height:24px; font-family:'Open Sans',sans-serif;">PAGE:&nbsp;<b>1</b></div>
                    <div style="line-height:24px; font-family:'Open Sans',sans-serif;">PRINT DATE:&nbsp;<b><?php echo date('d/m/Y') ?></b></div>
                    <div style="line-height:24px; font-family:'Open Sans',sans-serif;">CURRENCY:&nbsp;<b>B$</b>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
<div style="height:20px;display:block;clear:both;">&nbsp;</div>
<div class="row">
    <div class="col-md-12">
        <table class="table top10 text-left" style="text-align:left;">
            <tr>
                <th width="120"><b>Date</b></th>
                <th width="300"><b>Description</b></th>
                <th width="80"><b>Rate</b></th>
                <th width="100" class="text-right"><b>Debit</b></th>
                <th width="100" class="text-right"><b>Credit</b></th>
                <th width="100" class="text-right"><b>Balance</b></th>
            </tr>

            <?php

                $statementDate = new DateTime( $statement_date );

                // Calculate total debit of Previous Months and current Month
                $prevTotalDebit = 0.00;
                $curTotalDebit = 0.00;
                $monthly_arr = array();
				$trcount = 0;

                foreach( $items as $item ){
                    $dateIssue =  new DateTime( $item->date_issue );
                    if( $dateIssue->format('m-Y') == $statementDate->format('m-Y') ){
//                        if ($item->opening_balance > 0) {
                        if ($item->opening_balance <> 0) {
                            if ($item->discount > 0)
                                $curTotalDebit += $item->opening_balance - ($item->subtotal - $item->total);
                            else
                                $curTotalDebit += $item->opening_balance;
                        } else {
                            $curTotalDebit += $item->total;
                        }
                    }else{
//                        if ($item->opening_balance > 0) {
                        if ($item->opening_balance <> 0) {
                            if ($item->discount > 0)
                                $prevTotalDebit += $item->opening_balance - ($item->subtotal - $item->total);
                            else
                                $prevTotalDebit += $item->opening_balance;
                        } else {
                            $prevTotalDebit += $item->total;
                        }
                    }
                }

                // Calculate total credit of Previous month and Current Month
                $prevTotalCredit = 0.00;
                $curTotalCredit = 0.00;
                foreach( $payments as  $payment ){
                    $paymentDate = new DateTime( $payment->date );
                    if( $paymentDate->format('m-Y') == $statementDate->format('m-Y')){
                        $curTotalCredit += $payment->amount;
                    }else{
                        $prevTotalCredit += $payment->amount;
                    }
                }

                // balance brought forward
                $balBF = 0.00;
                
                $prevTotalCredit=format_number($prevTotalCredit);
                $prevTotalDebit=format_number($prevTotalDebit);

                $balBF = $prevTotalDebit - $prevTotalCredit;

                // Brought Forward balance
                echo '<tr>
                            <td></td>
                            <td>Brought Forward Balance</td>
                            <td></td> <td></td>  <td></td>
                            <td  class="text-right">' . format_price($balBF) . '</td>
                        </tr>';

                //$TotalCredit = -$curTotalCredit + $balBF;
                $TotalCredit =  $balBF;


                foreach ($items as $item) {
                    $credit = 0;
                    $total = 0;
                    $description = 'INV'.$item->invoice_id;
                    $dateIssue = new DateTime($item->date_issue);
                    if ($dateIssue->format('m-Y') == $statementDate->format('m-Y')) {

                        if ($item->opening_balance != 0){
                            $total = $item->opening_balance;
                            $description = $item->opening_balance_desc;
                        }else {
                            $total = $item->total;
                        }

                        if ($TotalCredit < 0 ) {
                           // echo $TotalCredit . ' <> '. $total.' <br>';
                            $compare = -$TotalCredit;
                            if ($compare > $total)
                                $credit = $total;
                            else
                                $credit = $compare;
                            $TotalCredit += $total;

                        } else {
                            $TotalCredit += $total;
                        }

                        if ($total < 0) {
                            $credit = -$total;
                            $total = 0;
                        }
                        $credit = $credit != 0 ? format_price($credit) : '';
                        echo '<tr>
                                    <td>' . date('d/m/Y', strtotime($item->date_issue)) . '</td>
                                    <td>'.$description.'</td>
                                    <td></td>
                                    <td class="text-right">' . format_price($total) . '</td>
                                    <td class="text-right">' . $credit . '</td>
                                    <td class="text-right">' . format_price( $TotalCredit ) . '</td>
                        </tr>';
						$trcount++;
                    }
                } //end foreach
                //$TotalCredit = -$curTotalCredit + $balBF;
                if ($curTotalCredit > 0) {
                    $i = 1;
                    $tot = count($payments);
                    foreach ($payments as $payment) {
                        $paymentDate = new DateTime($payment->date);
                        if ($paymentDate->format('m-Y') == $statementDate->format('m-Y')) {
                            $TotalCredit = $TotalCredit - $payment->amount;
                            echo '<tr>
                                    <td>' . date('d/m/Y', strtotime($payment->date)) . '</td>
                                    <td>' . $payment->description . '</td>
                                    <td></td>
                                    <td>&nbsp;</td>
                                    <td class="text-right">' . format_price($payment->amount) . '</td>';
                            //if ($i == $tot)
                                echo '<td  class="text-right">' . format_price($TotalCredit) . '</td>';
                           // else
                                //echo '<td>&nbsp;</td>';
                            echo '</tr>';
							$trcount++;
                        }
                        $i++;
                    }
                }
            ?>
        </table>
	
    </div>
</div>
<?php

if($trcount == 0)
{
	echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
}
elseif($trcount <= 3)
{
	echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>";	
}
elseif($trcount >= 3 && $trcount <= 6){
	 echo "<br><br><br><br><br><br><br><br><br>";	
}
elseif($trcount > 6 && $trcount <= 8){
	 echo "<br><br><br><br><br><br><br>";	 
}
elseif($trcount > 8 && $trcount <= 12)
{
	echo "<br><br><br><br><br>";		
}elseif($trcount > 12 && $trcount <= 14)
{
	echo "<br><br><br>";		
}elseif($trcount > 14)
{
	echo "<br><br>";		
}
		

?>
<div class="row text-center">
    <div class="col-md-12 text-center">
        <?php

            $total_payment = 0;
            foreach ($payments as $payment) {
                $total_payment += $payment->amount;
            }

            $keys = array();
            $monthly_arr = array();
            $amount = 0;

            foreach ($items as $item) {
                //if( $item->opening_balance > 0 )
                if( $item->opening_balance != 0 )
                    $amount = $item->opening_balance;
                else
                    $amount = $item->total;

                $keys = array_keys($monthly_arr);


                if (in_array(date('m-Y', strtotime($item->date_issue)), $keys)) {

                    $monthly_arr[date('m-Y', strtotime($item->date_issue))] += $amount;
                } else {
                    //logic for showing months in which there were no transactions - START
                    if (count($keys) > 0) {
                       $latest_month = $keys[count($keys) - 1];
                       $mon_year = explode('-', $latest_month);
                       $mon = $mon_year[0];
                       $year = $mon_year[1];
                       for ($ctr = $mon + 1; 1;  $ctr++) {
                          if ($ctr > 12) {
                             $ctr = 1;
                             $year++;
                          }
                          $new_month = "" . sprintf("%02d", $ctr) . "-" . $year;
                          if ($new_month == date('m-Y', strtotime($item->date_issue))) {
                             break;
                          } else {
                           //  $monthly_arr[$new_month] = "0.00";
                          }
                       }
                    }
                    //logic for showing months in which there were no transactions - END
                    $monthly_arr[date('m-Y', strtotime($item->date_issue))] = $amount;
                }
            }
       // print_r($monthly_arr['01-2016']);

            $keys = array_keys($monthly_arr);
			
            if (count($keys) > 0) {
                $latest_month = $keys[count($keys) - 1];
                $st_month = $statementDate->format('m-Y');

               $mon_year = explode('-', $latest_month);
               $mon = $mon_year[0];
               $year = $mon_year[1];

			   if ($st_month != $latest_month) {
				   for ($ctr = $mon + 1; 1;  $ctr++) {
					  if ($ctr > 12) {
						 $ctr = 1;
						  $year++;
					  }
					  $new_month = "" . sprintf("%02d", $ctr) . "-" . $year;
					  if ($new_month == $st_month) {
						 $monthly_arr[$new_month] = "0.00";
						 break;
					  } else {
						 $monthly_arr[$new_month] = "0.00";
					  }
				   }
			   }
			}
			
            $monthly_payment = array();
            foreach ($payments as $payment) {
                $keys = array_keys($monthly_payment);

                if (in_array(date('m-Y', strtotime($payment->date)), $keys)) {
                    $monthly_payment[date('m-Y', strtotime($payment->date))] += $payment->amount;

                } else {
                    $monthly_payment[date('m-Y', strtotime($payment->date))] = $payment->amount;
                }
				
            }

//        echo "<pre>";
//        print_r($monthly_payment);
//        echo $total_payment."<br/>";
//        foreach ($monthly_arr as $key => $month) {
//                $total_payment=$total_payment - $month;
//                echo $total_payment.'<br/>';
//        }
//        foreach ($monthly_arr as $key => $month) {
//        $i++;
//        //                            if($total_payment >= $month ){
//        if ($month>=0) {
//
//        if ($total_payment >= $month) {
//        $total_payment = $total_payment - $month;
//        $month = 0;
//
//        }elseif ($total_payment <0)
//        {
//            $month = $month + $total_payment;
//            $total_payment = 0;
//        }
//        elseif ($total_payment >=0){
//        //                                    for the first month i have change the statement let see whats happen in second month
//            $total_payment.'the total payment deduction from last month <br/>';
//            $month = $month - $total_payment+75;
//            $total_payment = 0;
//        }
//        }elseif ($month<0){
//        $total_payment = $total_payment - $month;
//        $month = 0;
//         }
//        }
//
//        print_r($monthly_arr);
//        exit();
        ?>	
		    
        <table style="width: 730px;">
            <tr>
                <?php
					$i = 0;
					$j = 0;
                $counter=count($monthly_arr);
                $check_against_check=0;

                if($counter > 12)
                {
                    $check=$counter-12;
                }
                    foreach ($monthly_arr as $key => $month) {
							$i++;
//                            if($total_payment >= $month ){
                            if($month>=0) {

								if ($total_payment >= $month) {


                                    $total_payment = $total_payment - $month;
									$month = 0;


								}elseif($total_payment <0)
								{
									$month = $month + $total_payment;
									$total_payment = 0;										
								}								
								elseif($total_payment >=0){
//                                    for the first month i have change the statement let see whats happen in second month
									$month = $month - $total_payment;
									$total_payment = 0;
								}
							}elseif($month<0){

								 $total_payment = $total_payment - $month;

								$month = 0;
							}

                    $check_against_check++;
                    if($check >= $check_against_check)
                    {
                        $counter--;
                        continue;
                    }
		        else
		            {
                $counter--;
			$j++;
?>
						<td align="center" style="width:120px;">
						<table border="1" style="width:120px;">
						<tr>
						 <th style="text-align:center;"><b><?php echo date('M y', strtotime('01-' . $key)); ?></b></th>
							</tr>
							<tr>
								<td style="text-align:center;"><?php echo format_price($month); ?></td>
							</tr>
						</table>
                        </td>
<?php
		}
?>
                            <?php
                            if ($j % 12 == 0 || $j == 6)
							{
                                echo "</tr><tr>";
							}
                       // }
                    }

                ?>
            </tr>
        </table>
    </div>
</div>
<?php

/*    // echo '<pre>' . print_r($monthly_arr, true) . '</pre>';
*/?>
<br/><br/>

<div style="line-height: 110%; display:block; float:left; width:7%; position: absolute; bottom: 0px;">NOTES:</div><div style="line-height: 110%; display:block; float:left; width:93%; position: absolute; bottom: 0px;">
                    <ol class="ol-notes"><li class="li-notes">YOUR PROMPT SETTLEMENT WOULD BE APPRECIATED. IN EVENT OF ANY DISCREPANCY, KINDLY NOTIFY THE ACCOUNTS DEPARTMENT WITHOUT ANY DELAY.</li>
                               <li>PAYMENTS RECIEVED AFTER THE END OF MONTH WILL BE CREDITED ON THE NEXT MONTH'S STATEMENT.</li>
			       <li>E. & O. E.</li>
			       <li>ALL CHEQUE MUST BE CROSSED AND MADE PAYABLE TO "MGK AUTOPARTS SERVICES"</li></ol></div>


</body>
</html>
							