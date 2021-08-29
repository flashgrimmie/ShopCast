<?php


class Finance_model extends CI_Model
{


	function addRecurringExpense($column)
	{
		$column=mysql_real_escape_string(str_replace(' ','_',strtolower($column)));
		$sql='ALTER TABLE recurring_expenses ADD COLUMN `'.$column.'` INT DEFAULT 0';
		$this->db->query($sql);
	}
	
	function recurringExpenses($month='',$year='')
	{
		$month=($month==''?'month(now())':$month);
		$year=($year==''?'year(now())':$year);
		$outlet_id=$this->session->userdata('outlet_id');
		$sql = 'SELECT * FROM recurring_expenses WHERE month(date)='.$month.' AND year(date)='.$year.' AND outlet_id="'.$outlet_id.'"'; 
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return $result;	
	}

	function recurringExpensesNew($year='')
	{
		$year=($year==''?'year(now())':$year);
		$outlet_id=$this->session->userdata('outlet_id');
		$sql = 'SELECT * FROM recurring_expenses WHERE  year(date)='.$year.' AND outlet_id="'.$outlet_id.'" order by date'; 
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return $result;	
	}

	function recurringTotal($month='',$year='')
	{
		$month=($month==''?'month(now())':$month);
		$year=($year==''?'year(now())':$year);
		$outlet_id=$this->session->userdata('outlet_id');
		$fields=$this->recurringFields();
		$sum='`'.implode('`+`',$fields).'`';
		if($sum!='``')
		{
		$sql = 'SELECT SUM('.$sum.') as total_cost FROM recurring_expenses WHERE month(date)='.$month.' AND year(date)='.$year.' AND outlet_id="'.$outlet_id.'"';
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return isset($result['total_cost'])?$result['total_cost']:false;
		}
		else return false;
	}
	
	function recurringDaily($date)
	{
		$outlet_id=$this->session->userdata('outlet_id');
		$fields=$this->recurringFields();
		$sum='`'.implode('`+`',$fields).'`';
		if($sum!='``')
		{
		$sql = 'SELECT SUM('.$sum.') as total_cost FROM recurring_expenses WHERE date(date)="'.$date.'" AND outlet_id="'.$outlet_id.'"';
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return isset($result['total_cost'])?$result['total_cost']:false;
		}
		else return false;
	}
	
	function recurringFields()
	{
		$sql='DESCRIBE recurring_expenses';
		$query=$this->db->query($sql);
		$result=$query->result_array();
		$fields=array();
		foreach($result as $field)
		if($field['Field']!='date'&&$field['Field']!='outlet_id'&&$field['Field']!='id')
		$fields[]=$field['Field'];
		return $fields;
	}
	
	function deleteRecurringExpense($column)
	{
		$column=mysql_real_escape_string($column);
		$sql='ALTER TABLE recurring_expenses DROP COLUMN `'.$column.'`';
		$query = $this->db->query($sql);
		$result=$query?true:false;
		return $result;
	}
	
	function insertRecurringExpenses($values)
	{
		$outlet_id=$this->session->userdata('outlet_id');
		$fields=$this->recurringFields();
		foreach($fields as $field)
		$insert[]='`'.$field.'`';
		$insert[]='`outlet_id`';
		$insert_columns=implode(',',$insert);
		$sql_delete='DELETE FROM recurring_expenses WHERE month(date)=month(now()) AND year(date)=year(now()) AND outlet_id='.$outlet_id;
		$sql_insert='INSERT INTO recurring_expenses('.$insert_columns.') VALUES('.$values.')';
		$this->db->query($sql_delete);
		$this->db->query($sql_insert);
	}
	
	
	function getAllPettyCash($date=false) 
	{
		
		if(!$date) {
			$date=date('Y-m');
		}
		
		$outlet_id=$this->session->userdata('outlet_id');

		$sql='SELECT COUNT(*) as total_petty_cash
            FROM petty_cash join users USING(user_id)
            WHERE date LIKE "%'. $date .'%"
			and users.user_id = petty_cash.user_id
            AND users.outlet_id =  "' . $outlet_id . '"';
		//echo $sql;	
		  $result=$this->shared_model->getRow($sql);		  
		  return $result->total_petty_cash;   
	}
	
	function getAllPettyCashDate($date=false) 
	{
		
		if(!$date) {
			$date=date('Y-m');
		}
		
		$outlet_id=$this->session->userdata('outlet_id');
		
		$this->load->library('datatables');
		//$date=date('Y-m');
        $this->datatables->select('petty_cash.name, value,users.name as staff, petty_cash.date, balance, petty_cash.id,IF (date LIKE "'.$date.'-%",1,0) as check_del',false)->from('petty_cash');
        
        $this->datatables->join('users', 'users.user_id=petty_cash.user_id');
        $this->datatables->where('users.outlet_id', $outlet_id);
		$this->datatables->where('petty_cash.date LIKE "%'. $date .'%"');
        
        return $this->datatables->generate();	
	}
	
	function getPettySum($date=false)
	{
		$outlet_id=$this->session->userdata('outlet_id');
		$this->db->select_sum('value')->from('petty_cash');
		$this->db->join('users', 'users.user_id=petty_cash.user_id');
		$this->db->where('users.outlet_id', $outlet_id);
		if(!empty($date))
		{
			$this->db->where('petty_cash.date LIKE "%'. $date .'%"');
		}
		$query=$this->db->get();
		return $query->row()->value;
	}

	function getPettyCash() {
		$outlet_id=$this->session->userdata('outlet_id');

		$this->load->library('datatables');
		$date=date('Y-m');
        $this->datatables->select('petty_cash.name, value,users.name as staff, petty_cash.date, balance, petty_cash.id,IF (date LIKE "'.$date.'-%",1,0) as check_del',false)->from('petty_cash');
        
        if($outlet_id){
            $this->datatables->join('users', 'users.user_id=petty_cash.user_id');
            $this->datatables->where('users.outlet_id', $outlet_id);
        }

        return $this->datatables->generate();

	}

	function getPettyCashHistory() {
		$outlet_id=$this->session->userdata('outlet_id');

		$this->load->library('datatables');
        $this->datatables->select('petty_cash_amounts.date,users.name as staff,petty_cash_amounts.value, balance')->from('petty_cash_amounts');
        
        if($outlet_id){
            $this->datatables->join('users', 'users.user_id=petty_cash_amounts.user_id');
            $this->datatables->where('users.outlet_id', $outlet_id);
        }

        return $this->datatables->generate();

	}

	function getDebtors()
	{
		$outlet_id=$this->session->userdata('outlet_id');

		$this->load->library('datatables');
        $query='SELECT * FROM
        		(SELECT customers.name,total,date_issue,"" as debtor_id
        		 FROM invoices JOIN users USING(user_id)
        		 JOIN customers USING(customer_id)
        		 WHERE status!="paid"
        		 AND published="1"
        		 AND outlet_id="'.$this->session->userdata('outlet_id').'"
        		 UNION ALL
        		 SELECT customers.name,amount_due as total,"Old" as date_issue,debtor_id
        		 FROM debtors JOIN users USING(user_id)
        		 JOIN customers USING(customer_id)
				 WHERE outlet_id="'.$this->session->userdata('outlet_id').'"
				 ) as der';
        $fields=array('name','total','date_issue','debtor_id');
        return $this->datatables->customQuery($fields,$query);
	}

	function getCreditors()
	{
		$outlet_id=$this->session->userdata('outlet_id');

		$this->load->library('datatables');
        $this->datatables->select('suppliers.name, total, purchase_invoices.issue_date')->from('purchase_invoices');
        $this->datatables->join('suppliers','suppliers.supplier_id=purchase_invoices.supplier_id');
        $this->datatables->join('users','purchase_invoices.user_id=users.user_id');
        $this->datatables->where('status !=', '"paid"');
        $this->datatables->where('published', '1');

        
        if($outlet_id){
            $this->datatables->where('users.outlet_id', $outlet_id);
        }

        return $this->datatables->generate();
	}

	function getOtherPurchases() {
		$outlet_id=$this->session->userdata('outlet_id');

		$this->load->library('datatables');
        $this->datatables->select('name, value, date, id, image')->from('outlet_purchases');
        
        if($outlet_id){
            $this->datatables->where('outlet_id', $outlet_id);
        }

        return $this->datatables->generate();
	}

	function getStockPurchases() {
		$outlet_id=$this->session->userdata('outlet_id');

		$this->load->library('datatables');
        $this->datatables->select('stock_num, quantity, cost, payment_date, pi_items.pi_id')->from('pi_items');
        $this->datatables->join('stock','stock.item_id=pi_items.item_id');
        $this->datatables->join('purchase_invoices','pi_items.pi_id=purchase_invoices.pi_id');
        $this->datatables->join('users','users.user_id=purchase_invoices.user_id');

        if($outlet_id){
            $this->datatables->where('outlet_id', $outlet_id);

        }
        return $this->datatables->generate();

	}


	function getOneTimeExpenses() {
		$outlet_id=$this->session->userdata('outlet_id');

		$this->load->library('datatables');
        $this->datatables->select('name, value, date, id')->from('one_time_expenses');
        
        if($outlet_id){
            $this->datatables->where('outlet_id', $outlet_id);
        }

        return $this->datatables->generate();

	}

	function getCashTotal($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(total) as s FROM cash_sales JOIN users USING(user_id) WHERE cash_sales.active="Y" AND draft = "N" and date LIKE "%'.$date.'%" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"';
		$result=$this->shared_model->getRow($sql);
		return $result->s;
	}

	function getInvoiceTotal($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(total) as s FROM invoices JOIN users USING(user_id) WHERE invoices.active="Y" AND draft = "N" and status="pending" AND date_issue LIKE "%'.$date.'%" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"';
		$regular=$this->shared_model->getRow($sql);
		return $regular->s;
	}

	function getDOTotal($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(total) as s FROM delivery_orders JOIN users USING(user_id) WHERE delivery_orders.active="Y" AND date LIKE "%'.$date.'%" AND users.outlet_id="'.$this->session->userdata('outlet_id').'" AND delivery_orders.outlet_id>0';
		$regular=$this->shared_model->getRow($sql);
		return $regular->s;
	}

	function getCashSalesReturn($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(((1-cs_items.discount/100)*price-discount_value)*quantity) as s FROM cs_items JOIN cash_sales USING(cs_id) JOIN users USING(user_id) WHERE  cash_sales.active="Y" AND date LIKE "%'.$date.'%" AND users.outlet_id="'.$this->session->userdata('outlet_id').'" AND cs_items.returned="Y"';
		$returned=$this->shared_model->getRow($sql);
		return $returned->s;
	}

	function getInvoicesReturn($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(((1-i_items.discount/100)*price-discount_value)*quantity) as s FROM i_items JOIN invoices USING(invoice_id) JOIN users USING(user_id) WHERE  invoices.active="Y" AND status="paid" AND date_payment LIKE "%'.$date.'%" AND users.outlet_id="'.$this->session->userdata('outlet_id').'" AND i_items.returned="Y"';
		$returned=$this->shared_model->getRow($sql);
		return $returned->s;
	}

	function getInvoicesCost($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(cost_price) as total_cost
			  FROM (SELECT cost_price 
					FROM stock_outlets JOIN i_items USING(item_id) JOIN invoices USING(invoice_id) JOIN users USING(user_id)
			  		WHERE  invoices.active="Y" AND status="paid" AND date_payment LIKE "%'.$date.'%" AND i_items.returned="N" AND stock_outlets.outlet_id="'.$this->session->userdata('outlet_id').'" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
			  		) as der';
		$result=$this->shared_model->getRow($sql);
		return $result->total_cost;
	}

	function getPartialPayments($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(amount) as total_cost 
			  FROM customer_payment_history
	  		  WHERE date LIKE "%'.$date.'%" AND outlet_id="'.$this->session->userdata('outlet_id').'"';
		$result=$this->shared_model->getRow($sql);
		return $result->total_cost;
	}

	function getCSCost($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(cost_price) as total_cost
			  FROM (SELECT cost_price 
					FROM stock_outlets JOIN cs_items USING(item_id) JOIN cash_sales USING(cs_id) JOIN users USING(user_id)
			  		WHERE  cash_sales.active="Y" AND date LIKE "%'.$date.'%" AND cs_items.returned="N" AND stock_outlets.outlet_id="'.$this->session->userdata('outlet_id').'" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
			  		) as der';
		$result=$this->shared_model->getRow($sql);
		return $result->total_cost;
	}

	function getOneTimeExpences($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT * FROM one_time_expenses WHERE date LIKE "%'.$date.'%" AND outlet_id="'.$this->session->userdata('outlet_id').'"';
		$result=$this->shared_model->getQuery($sql);
		return $result;
	}

	function getReccurringExpences()
	{
		$sql='DESCRIBE recurring_expenses';
		$result=$this->shared_model->getQuery($sql);
		$fields=array();
		foreach($result as $value)
		{
			if($value->Field!='id' AND $value->Field!='outlet_id' AND $value->Field!='date') {
				array_push($fields,$value->Field);
			}
		}
		return $fields;
	}

	function getReccurringExpencesValues($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT * FROM recurring_expenses WHERE date LIKE "%'.$date.'%" AND outlet_id="'.$this->session->userdata('outlet_id').'"';
		$result=$this->shared_model->getRow($sql);
		return $result;
	}

	function getOutletExpences($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT * FROM outlet_purchases WHERE date LIKE "%'.$date.'%" AND outlet_id="'.$this->session->userdata('outlet_id').'"';
		$result=$this->shared_model->getQuery($sql);
		return $result;
	}

	function getPurchaseTotal($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(value) as s FROM outlet_purchases WHERE date LIKE "%'.$date.'%" AND outlet_id="'.$this->session->userdata('outlet_id').'"';
		$result=$this->shared_model->getRow($sql);
		return $result->s;
	}

	function getOneTimeTotal($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(value) as s FROM one_time_expenses WHERE date LIKE "%'.$date.'%" AND outlet_id="'.$this->session->userdata('outlet_id').'"';
		$result=$this->shared_model->getRow($sql);
		return $result->s;
	}

	function getPurchaseInvoices($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(total) as price,suppliers.name as supplier 
			  FROM purchase_invoices 
			  JOIN suppliers USING(supplier_id) 
			  JOIN users USING(user_id)
			  WHERE date LIKE "%'.$date.'%" 
			  AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
			  GROUP BY supplier_id';
		$result=$this->shared_model->getQuery($sql);
		return $result;
	}

	function getPITotal($date=false)
	{
		if(!$date) {
			$date=date('Y-m');
		}
		$sql='SELECT SUM(total) as price
			  FROM purchase_invoices 
			  JOIN users USING(user_id)
			  WHERE date LIKE "%'.$date.'%" 
			  AND draft = "N" and users.outlet_id="'.$this->session->userdata('outlet_id').'"';
		$result=$this->shared_model->getRow($sql);
		return $result->price;
	}

	function getDailyInvoices($date=false)
	{
		if(!$date) {
			$date=date('Y-m-d');
		}
		$sql='SELECT SUM(total) as price
			  FROM invoices 
			  JOIN users USING(user_id)
			  WHERE date_issue LIKE "%'.$date.'%" 
			  AND draft = "N" and invoices.active = "Y" and users.outlet_id="'.$this->session->userdata('outlet_id').'"';
		$result=$this->shared_model->getRow($sql);
		return $result->price;
	}
	function getDailyCS($date=false)
	{
		if(!$date) {
			$date=date('Y-m-d');
		}
		$sql='SELECT SUM(total) as price
			  FROM cash_sales 
			  JOIN users USING(user_id)
			  WHERE date LIKE "%'.$date.'%" 
			  AND draft = "N" and cash_sales.active = "Y" and users.outlet_id="'.$this->session->userdata('outlet_id').'"';
		$result=$this->shared_model->getRow($sql);
		return $result->price;
	}
}