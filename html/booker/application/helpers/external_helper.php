<?php 

if ( ! function_exists('format_price2d'))
{
	function format_price2d($number) { 
		$ci=& get_instance();
		$price= number_format($number,2,'.',',');
		return '$'.$price;
	} 
}

if ( ! function_exists('format_price'))
{
	function format_price($number) { 
		$ci=& get_instance();
		$price= number_format($number,2,'.',',');
		return '$'.$price;
	} 
}

if ( ! function_exists('format_number'))
{
	function format_number($number) { 
		$ci=& get_instance();
		$number= number_format($number,2,'.','');
		return $number;
	} 
}

if ( ! function_exists('format_date'))
{
	function format_date($date) { 
		$ci=& get_instance();
		//$date= date('jS M Y',strtotime($date));
		$date= date('d/m/Y',strtotime($date));
		return $date;
	} 
}

if ( ! function_exists('format_time'))
{
	function format_time($date) { 
		$ci=& get_instance();
		$date= date('d/m/Y h:i:s a',strtotime($date));
		return $date;
	} 
}

if ( ! function_exists('updateOutletQty'))
{
	function updateOutletQty($outlet_stock,$item_id,$outlet_id,$type) { 
		$ci=& get_instance();
	    if($type=='out')
			$update='qty=qty-'.$outlet_stock['quantity'];
		if($type=='in')
			$update='qty=qty+'.$outlet_stock['quantity'];
		else if($type=='pi') {
			$cost=($outlet_stock->cost+($outlet_stock->additional_expenses/$outlet_stock->total_qty));
			$update='qty=qty+'.$outlet_stock->quantity.',original_cost_price="'.$outlet_stock->cost.'",cost_price="'.$cost.'"';
	    	$qty=$outlet_stock->quantity;
		}
		else if($type=='do') {
			$update='qty=qty+'.$outlet_stock->quantity;
			$existing_info=$ci->shared_model->getRow('select * from stock join stock_outlets using(item_id) where item_id="'.$item_id.'" and outlet_id="'.$outlet_id.'"');
			if(!$existing_info) {
				$update.=',cost_price="'.$outlet_stock->cost_price.'",sell_price="'.$outlet_stock->sell_price.'",price1="'.$outlet_stock->price1.'",price2="'.$outlet_stock->price2.'",price3="'.$outlet_stock->price3.'",price4="'.$outlet_stock->price4.'",original_cost_price="'.$outlet_stock->original_cost_price.'"';
			}
		}
		$lookup=$ci->shared_model->Lookup('stock_outlets','id',array('item_id'=>$item_id,'outlet_id'=>$outlet_id));
		if($lookup) {
			if($_SERVER['REMOTE_ADDR']=='27.251.231.34')
			{
				$test=file_get_contents('test.txt');
				file_put_contents("test.txt",$test.'\n UPDATE stock_outlets SET '.$update.' WHERE item_id="'.$item_id.'" AND outlet_id="'.$outlet_id.'"');
				//exit;
			}
			$exists=$ci->shared_model->execute('UPDATE stock_outlets SET '.$update.' WHERE item_id="'.$item_id.'" AND outlet_id="'.$outlet_id.'"');
		} else if($type=='pi' || $type='do') {
			$update.=',item_id="'.$item_id.'", outlet_id="'.$outlet_id.'"';
			$ci->shared_model->execute('INSERT INTO stock_outlets SET '.$update);
		}
	} 
}

if( !function_exists('log_db_query')){
    function log_db_query($query ){
        // Creating Query Log file with today's date in application/logs folder
        $filepath = APPPATH . 'logs/shopcast-db-log-' . date('Y-m-d') . '.php';
        // Opening file with pointer at the end of the file
        $handle = fopen($filepath, "a+");
        // Generating SQL file
        $sql = $query . " \nExecution Time:" . date('Y-m-d H:i:s');
        fwrite($handle, $sql . "\n\n");
        fclose($handle);      // Close the file
    }
}

?>