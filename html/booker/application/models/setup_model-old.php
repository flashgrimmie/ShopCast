<?php

class Setup_Model extends CI_Model
{    
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function getUsers()
	{
		$sql = 'SELECT u.*,ut.type FROM users u JOIN user_types ut  USING(type_id) where active="Y"';  
		$result = $this->shared_model->getQuery($sql);
		return $result;
	}
	
	function countUsers()
	{
		$sql = 'SELECT COUNT(*) as count FROM users JOIN user_types USING(type_id)';  
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return $result['count']?$result['count']:0;
	}
	function userDetails($id)
	{
		$id=mysql_real_escape_string($id);
		$sql='SELECT * FROM users WHERE user_id="'.$id.'"';
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return $result;
	}
	function deleteUser($id)
	{
		$id=mysql_real_escape_string($id);
		$sql='DELETE FROM users WHERE user_id="'.$id.'"';
		$query = $this->db->query($sql);
		if($query) return true;
		else return false;
	}
	function getUserTypes()
	{
		$sql = 'SELECT * FROM user_types';  
		$query = $this->db->query($sql);
		$result=$query->result_array();
		return $result;	
	}
	function getSuppliers()
	{
		$sql = 'SELECT * FROM suppliers where active="Y"'; 
		$result = $this->shared_model->getQuery($sql);
		return $result;
	}
	
	function countSuppliers()
	{
		$sql = 'SELECT COUNT(*) as count FROM suppliers';  
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return $result['count']?$result['count']:0;
	}
	function supplierDetails($id)
	{
		$id=mysql_real_escape_string($id);
		$sql='SELECT * FROM suppliers WHERE supplier_id="'.$id.'"';
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return $result;
	}
	function getOutlets($start,$limit)
	{
		$sql = 'SELECT * FROM outlets WHERE active="Y"'; 
		$result = $this->shared_model->getQuery($sql);
		return $result;
	}
	
	function countOutlets()
	{
		$sql = 'SELECT COUNT(*) as count FROM outlets';  
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return $result['count']?$result['count']:0;
	}
	function outletDetails($id)
	{
		$id=mysql_real_escape_string($id);
		$sql='SELECT * FROM outlets WHERE outlet_id="'.$id.'"';
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return $result;
	}
	function stockList($id)
	{
		$id=mysql_real_escape_string($id);
		$sql='SELECT * FROM stock_outlets JOIN stock USING(item_id) WHERE outlet_id="'.$id.'"';
		$query = $this->db->query($sql);
		$result=$query->result_array();
		return $result;
	}
	function getCustomers()
	{
		$this->load->library('datatables');
        $this->datatables->select('customer_id, name, address, email, phone,fax, car_plate')
        ->from('customers');
        $this->datatables->where('active','Y');
		$this->datatables->add_column('6','','');
        return $this->datatables->generate();
	}

	function getCustomerPayments($customer_id)
	{
		$this->load->library('datatables');
        $this->datatables->select('amount, date')
        ->from('customer_payment_history')
        ->where('outlet_id',$this->session->userdata('outlet_id'));
        return $this->datatables->generate();
	}
	
	function countCustomers()
	{
		$sql = 'SELECT COUNT(*) as count FROM customers';  
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return $result['count']?$result['count']:0;
	}
	function customerDetails($id)
	{
		$id=mysql_real_escape_string($id);
		$sql='SELECT * FROM customers c WHERE customer_id="'.$id.'"';
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return $result;
	}
	function searchCustomers($search)
	{
		$search=mysql_real_escape_string($search);
		$sql='SELECT name,phone,customer_id,IF(type="C","regular customer","dealer") as type FROM customers WHERE name REGEXP "'.$search.'" OR phone REGEXP "'.$search.'"';
		$query = $this->db->query($sql);
		$result=$query->result_array();
		return $result;
	
	}

	function getCategories()
	{
		$this->load->library('datatables');
        $this->datatables->select('category, category_description, category_id')
        ->from('categories');
        return $this->datatables->generate();
	}

	function getInvestitions() 
	{
		$sql='SELECT * FROM investitions';
		$result = $this->shared_model->getQuery($sql);
		return $result;
	}

	function investitionDetails($id)
	{
		$sql='SELECT * FROM investitions WHERE investition_id="'.$id.'"';
		$query = $this->db->query($sql);
		$result=$query->row_array();
		return $result;
	}

	function investitionsByCustomer($id) 
	{
		$sql='SELECT * FROM customers_x_investitions JOIN investitions USING (investition_id) WHERE customer_id="'.$id.'"';
		$result = $this->shared_model->getQuery($sql);
		return $result;
	}
}  
?>