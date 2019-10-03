<?php

class Purchasing_Model extends CI_Model
{    
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function getPurchasing($offset,$limit) {
    	$outlet_id=$this->session->userdata('outlet_id');
		$sql = 'SELECT pi.*,s.name as supplier FROM purchase_invoices pi JOIN suppliers s USING(supplier_id) JOIN users u USING(user_id) WHERE u.outlet_id='.$outlet_id.' ORDER BY date desc limit '.$offset.','.$limit; 
		$result = $this->shared_model->getQuery($sql);
		return $result;
    }

    function countPI()
	{
		$outlet_id=$this->session->userdata('outlet_id');
		$sql = 'SELECT COUNT(*) as count FROM purchase_invoices pi JOIN suppliers s USING(supplier_id) JOIN users u USING(user_id) WHERE u.outlet_id='.$outlet_id;  
		$result = $this->shared_model->getRow($sql, true);
		return $result['count']?$result['count']:0;
	}

    function getPiItems($pi_id)
    {
    	$sql='SELECT *,pi_items.id as iitem_id FROM pi_items 
    		  JOIN stock USING(item_id) 
    		  JOIN stock_outlets so USING(item_id) 
    		  WHERE pi_id="'.$pi_id.'" 
    		  AND so.outlet_id="'.$this->session->userdata('outlet_id').'"';

        $result=$this->shared_model->getQuery($sql);

        return $result;
    }

    function getDraftTransactions()
    {
      $outlet_id=$this->session->userdata('outlet_id');
      $this->load->library('datatables');
      $query='SELECT * FROM 
                (SELECT CONCAT("PO",po_id) as reference_id,date_format(date,"%D %b %Y") as datef,date,IF(supplier_id is not null,(SELECT name FROM suppliers WHERE supplier_id=purchase_orders.supplier_id),"Undefined") as customer,"Purchase Order" as type,po_id as id
                FROM purchase_orders JOIN users USING(user_id) WHERE draft="Y" and purchase_orders.active="Y" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
				UNION ALL
				SELECT CONCAT("RPI",rpi_id) as reference_id,date_format(date,"%D %b %Y") as datef,date,IF(supplier_id is not null,(SELECT name FROM suppliers WHERE supplier_id=purchase_returns.supplier_id),"Undefined") as customer,"Purchase Return" as type,rpi_id as id
                FROM purchase_returns JOIN users USING(user_id) WHERE draft="Y" and purchase_returns.active="Y" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
                UNION ALL
                SELECT CONCAT("PI",pi_id) as reference_id,date_format(date,"%D %b %Y") as datef,date,IF(supplier_id is not null,(SELECT name FROM suppliers WHERE supplier_id=purchase_invoices.supplier_id),"Undefined") as customer,"Purchase Invoice" as type,pi_id as id
                FROM purchase_invoices JOIN users USING(user_id) WHERE draft="Y" and purchase_invoices.active="Y" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
                ) as der';
      $fields=array('reference_id','datef','customer','type','id');
      $search_fields=array('reference_id','date','customer','total','type','id');
      return $this->datatables->customQuery($fields,$query,$search_fields); 
    }

    function getVoidTransactions()
    {
      $outlet_id=$this->session->userdata('outlet_id');
      $this->load->library('datatables');
      $query='SELECT * FROM 
                (SELECT CONCAT("PO",po_id) as reference_id,date_format(date,"%D %b %Y") as datef,date,IF(supplier_id is not null,(SELECT name FROM suppliers WHERE supplier_id=purchase_orders.supplier_id),"Undefined") as customer,"Purchase Order" as type,(SELECT name FROM users WHERE user_id=purchase_orders.user_id) as staff,discard_reason
                FROM purchase_orders JOIN users USING(user_id) WHERE draft="Y" and purchase_orders.active="N" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
                UNION ALL
                SELECT CONCAT("PI",pi_id) as reference_id,date_format(date,"%D %b %Y") as datef,date,IF(supplier_id is not null,(SELECT name FROM suppliers WHERE supplier_id=purchase_invoices.supplier_id),"Undefined") as customer,"Purchase Invoice" as type,(SELECT name FROM users WHERE user_id=purchase_invoices.user_id) as staff,discard_reason
                FROM purchase_invoices JOIN users USING(user_id) WHERE draft="Y" and purchase_invoices.active="N" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
                ) as der';
      $fields=array('reference_id','datef','customer','type','staff','discard_reason');
      $search_fields=array('reference_id','date','customer','type','staff','discard_reason');
      return $this->datatables->customQuery($fields,$query,$search_fields); 
    }


    function piDetails($pi_id)
    {
        $sql='SELECT * FROM purchase_invoices JOIN suppliers USING(supplier_id) WHERE pi_id="'.$pi_id.'"';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }

    function searchCustomer($term) {
		$sql = 'select * from customers where name REGEXP "'.$term.'" and active="Y"'; 
		$result = $this->shared_model->getQuery($sql);
		return $result;
    }

    function searchSupplier($term) {
		$sql = 'select * from suppliers where name REGEXP "'.$term.'" and active="Y"'; 
		$result = $this->shared_model->getQuery($sql);
		return $result;
    }

    function searchByStockNum($stock_num)
    {
    	$sql = 'SELECT * FROM stock LEFT JOIN stock_outlets USING(item_id) WHERE stock_num= "'.$stock_num.'"'; 
		$result = $this->shared_model->getRow($sql);
		return $result;
    }

    function searchByDescription($description)
	{
		$sql='SELECT * FROM stock s LEFT JOIN stock_outlets USING(item_id) WHERE s.description REGEXP "'.$description.'"';
		$result = $this->shared_model->getQuery($sql);
		return $result;
	}

	function getPIs()
	{
		$this->load->library('datatables');
        $this->datatables->select('pi_id, date, issue_date, payment_date, invoice_num, total, suppliers.name, purchase_invoices.status, purchase_invoices.published')
        ->from('purchase_invoices');
        $this->datatables->join('suppliers','purchase_invoices.supplier_id=suppliers.supplier_id');
        $this->datatables->where('purchase_invoices.active','Y');
        return $this->datatables->generate();
	}
	
	function getRPIs()
    {
        $this->load->library('datatables');
        $this->datatables->select('rpi_id, date, issue_date, invoice_num, total, suppliers.name, purchase_returns.published')
        ->from('purchase_returns');
        $this->datatables->join('suppliers','purchase_returns.supplier_id=suppliers.supplier_id');
        $this->datatables->join('users','purchase_returns.user_id=users.user_id');
        $this->datatables->where('purchase_returns.active','Y');
        $this->datatables->where('purchase_returns.draft','N');
        $this->datatables->where('users.outlet_id',$this->session->userdata('outlet_id'));
        return $this->datatables->generate();
    }

	function getDOs()
	{
		$this->load->library('datatables');
        $this->datatables->select('do_id, date, outlets.name,published')
        ->from('delivery_orders');
        $this->datatables->join('outlets','delivery_orders.outlet_id=outlets.outlet_id');
        $this->datatables->where('delivery_orders.active','Y');
        return $this->datatables->generate();
	}

	function getPOs()
	{
		$this->load->library('datatables');
        $this->datatables->select('po_id, date, suppliers.name,pi_id')
        ->from('purchase_orders');
        $this->datatables->join('suppliers','purchase_orders.supplier_id=suppliers.supplier_id');
        $this->datatables->where('purchase_orders.active','Y');
        return $this->datatables->generate();
	}

	function getDoItems($do_id)
    {
    	$sql='SELECT *,do_items.id as iitem_id FROM do_items 
    		  JOIN stock USING(item_id) 
    		  JOIN stock_outlets so USING(item_id) 
              JOIN delivery_orders do USING(do_id)
    		  WHERE do_id="'.$do_id.'" 
    		  AND so.outlet_id=(SELECT outlet_id FROM users WHERE user_id=do.user_id)';

        $result=$this->shared_model->getQuery($sql);

        return $result;
    }

    function doDetails($do_id)
    {
        $sql='SELECT * FROM delivery_orders JOIN outlets USING(outlet_id) WHERE do_id="'.$do_id.'"';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }

    function getPoItems($po_id)
    {
    	$sql='SELECT *,po_items.id as iitem_id FROM po_items 
    		  JOIN stock USING(item_id) 
    		  JOIN stock_outlets so USING(item_id) 
    		  WHERE po_id="'.$po_id.'" 
    		  AND so.outlet_id="'.$this->session->userdata('outlet_id').'"';

        $result=$this->shared_model->getQuery($sql);

        return $result;
    }
	
	 function getRPIItems($rpi_id)
    {
        $sql='SELECT *,rpi_items.id as iitem_id FROM rpi_items 
              JOIN stock USING(item_id) 
              JOIN stock_outlets so USING(item_id) 
              WHERE rpi_id="'.$rpi_id.'" 
              AND so.outlet_id="'.$this->session->userdata('outlet_id').'"';

        $result=$this->shared_model->getQuery($sql);
        //echo $this->db->last_query();

        return $result;
    }

    function poDetails($po_id)
    {
        $sql='SELECT * FROM purchase_orders JOIN suppliers USING(supplier_id) WHERE po_id="'.$po_id.'"';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }
	
	function rpiDetails($rpi_id)
    {
        $sql='SELECT * FROM purchase_returns LEFT JOIN suppliers USING(supplier_id) WHERE rpi_id="'.$rpi_id.'"';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }

    function iopoDetails($iopo_id) 
    {
        $sql='SELECT * FROM purchase_orders JOIN outlets USING(outlet_id) WHERE po_id="'.$iopo_id.'"';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }

    function getIncomingPurchaseOrders()
    {
        $sql="select * from purchase_orders JOIN users USING(user_id) JOIN outlets ON(users.outlet_id=outlets.outlet_id) where purchase_orders.outlet_id='".$this->session->userdata('outlet_id')."'";
        $result=$this->shared_model->getQuery($sql);
        return $result;
    }

     function getIncomingDeliveryOrders()
    {
        $sql="select * from delivery_orders JOIN users USING(user_id) JOIN outlets ON(users.outlet_id=outlets.outlet_id) where delivery_orders.outlet_id='".$this->session->userdata('outlet_id')."'";
        $result=$this->shared_model->getQuery($sql);
        return $result;
    }
}
?>