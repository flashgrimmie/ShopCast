<?php

    class Document_model extends CI_Model {

        function __construct() {
            parent::__construct();
        }

        function invoiceDetails($invoice_id) {
            $sql = 'SELECT i.*,der.*,c.name as customer_name, c.address as customer_address, c.phone as customer_phone,c.fax as customer_fax, c.email as customer_email
				FROM invoices i 
				JOIN customers c USING(customer_id) 
				JOIN (SELECT u.user_id,host.name as host_outlet, host.address1 as host_address1, host.address2 as host_address2, host.contact as host_contact FROM users u JOIN outlets host USING(outlet_id)) as der ON der.user_id=i.user_id
				WHERE invoice_id="' . $invoice_id . '"';
            $query = $this->db->query($sql);
            $result = $query->row_array();
            return $result;
        }

        function invoiceItems($invoice_id) {
            $sql = 'SELECT * FROM i_items JOIN stock USING(item_id) WHERE invoice_id=' . $invoice_id;
            $query = $this->db->query($sql);
            $result = $query->result_array();
            return $result;
        }

        function invoiceOpening($invoice_id) {
            $sql = 'SELECT * FROM invoices WHERE invoice_id=' . $invoice_id;
            $query = $this->db->query($sql);
            $result = $query->result_array();
            return $result;
        }

        function statementItems($customer_id, $date = false) {
            $where = '';
            if ($date) {
                $where = ' AND invoices.date_issue <="' . date("Y-m-d", strtotime($date)) . '"';
            }
            $sql =  'SELECT *,CONCAT("INV",invoices.invoice_id) as description,
	                        (SELECT SUM(amount) FROM customer_payments WHERE invoice_id=invoices.invoice_id AND status="paid") as credited
                      FROM invoices
                      WHERE invoices.status="pending"
                            AND invoices.active="Y"
                            AND invoices.user_id IN(SELECT user_id FROM users WHERE outlet_id="' . $this->session->userdata('outlet_id') . '")
                            AND invoices.customer_id="' . $customer_id . '" ' . $where . '
                      GROUP BY invoices.invoice_id
                      ORDER BY invoices.invoice_id ASC';
            //return $sql;
            $result = $this->shared_model->getQuery($sql);
            return $result;

        }


        function csDetails($cs_id) {
            $sql = 'SELECT i.*,der.*,c.name as customer_name, c.address as customer_address, c.phone as customer_phone, c.email as customer_email
				FROM cash_sales i 
				JOIN customers c USING(customer_id) 
				JOIN (SELECT u.user_id,host.name as host_outlet, host.address1 as host_address1, host.address2 as host_address2, host.contact as host_contact FROM users u JOIN outlets host USING(outlet_id)) as der ON der.user_id=i.user_id
				WHERE cs_id="' . $cs_id . '"';
            $query = $this->db->query($sql);
            $result = $query->row_array();
            return $result;
        }

        function cnDetails($cn_id) {
            $sql = "select * from returned_cn JOIN customers o USING(customer_id) where cn_id='" . $cn_id . "'";
            $query = $this->db->query($sql);
            $result = $query->row_array();

            return $result;
        }

        function getCnItems($cn_id) {
            $sql = "select *,returned_cn.qty as quantity from returned_cn JOIN stock USING(item_id) JOIN stock_outlets so USING(item_id) where cn_id='" . $cn_id . "'";
            $query = $this->db->query($sql);
            $result = $query->result();
            return $result;
        }

        function csItems($cs_id) {
            $sql = 'SELECT * FROM cs_items JOIN stock USING(item_id) WHERE cs_id=' . $cs_id;
            $query = $this->db->query($sql);
            $result = $query->result_array();
            return $result;
        }

        function soDetails($so_id) {
            $sql = 'SELECT i.*,der.*,c.name as customer_name, c.address as customer_address, c.phone as customer_phone, c.email as customer_email
				FROM sales_orders i 
				JOIN customers c USING(customer_id) 
				JOIN (SELECT u.user_id,host.name as host_outlet, host.address1 as host_address1, host.address2 as host_address2, host.contact as host_contact FROM users u JOIN outlets host USING(outlet_id)) as der ON der.user_id=i.user_id
				WHERE so_id="' . $so_id . '"';
            $query = $this->db->query($sql);
            $result = $query->row_array();
            return $result;
        }

        function soItems($so_id) {
            $sql = 'SELECT * FROM so_items JOIN stock USING(item_id) WHERE so_id=' . $so_id;
            $query = $this->db->query($sql);
            $result = $query->result_array();
            return $result;
        }

        function piDetails($pi_id) {
            $sql = 'SELECT pi.*,der.*,s.name as supplier_name, s.address as supplier_address, s.phone as supplier_phone, s.email as supplier_email, s.contact_person as supplier_cp
				FROM purchase_invoices pi 
				JOIN suppliers s USING(supplier_id) 
				JOIN (SELECT u.user_id,host.name as host_outlet, host.location as host_location, host.contact as host_contact FROM users u JOIN outlets host USING(outlet_id)) as der ON der.user_id=pi.user_id
				WHERE pi_id="' . $pi_id . '"';
            $query = $this->db->query($sql);
            $result = $query->row_array();
            return $result;
        }

        function piItems($pi_id) {
            $sql = 'SELECT * FROM pi_items JOIN stock USING(item_id) WHERE pi_id=' . $pi_id;
            $result = $this->shared_model->getQuery($sql);
            return $result;
        }

        function poDetails($po_id) {
            $sql = 'SELECT po.*,der.*,s.name as supplier_name, s.address as supplier_address, s.phone as supplier_phone, s.email as supplier_email, s.contact_person as supplier_cp
				FROM purchase_orders po 
				JOIN suppliers s USING(supplier_id) 
				JOIN (SELECT u.user_id,host.name as ship_outlet, host.address1 as ship_address1, host.address2 as ship_address2, host.contact as ship_contact FROM users u JOIN outlets host USING(outlet_id)) as der ON der.user_id=po.user_id
				WHERE po_id="' . $po_id . '"';
            $query = $this->db->query($sql);
            $result = $query->row_array();
            return $result;
        }


        function iopoDetails($po_id) {
            $sql = 'SELECT po.*,der.*,o.name as supplier_name, o.location as supplier_address, o.contact as supplier_phone
				FROM purchase_orders po 
				JOIN outlets o USING(outlet_id) 
				JOIN (SELECT u.user_id,host.name as ship_outlet, host.address1 as ship_address1, host.address2 as ship_address2, host.contact as ship_contact FROM users u JOIN outlets host USING(outlet_id)) as der ON der.user_id=po.user_id
				WHERE po_id="' . $po_id . '"';
            $query = $this->db->query($sql);
            $result = $query->row_array();
            return $result;
        }

        function poItems($po_id) {
            $sql = 'SELECT * FROM po_items JOIN stock USING(item_id) WHERE po_id=' . $po_id;
            $result = $this->shared_model->getQuery($sql);
            return $result;
        }

        function doDetails($do_id) {
            $outlet_id = $this->shared_model->lookup('delivery_orders', 'outlet_id', array('do_id' => $do_id));
            if ($outlet_id) {
                $sql = 'SELECT do.*,der.*,o.name as ship_outlet, o.address1 as ship_address1, o.address2 as ship_address2, o.contact as ship_contact
					FROM delivery_orders do 
					JOIN outlets o USING(outlet_id) 
					JOIN (SELECT u.user_id,host.name as host_outlet, host.address1 as host_address1, host.address2 as host_address2, host.contact as host_contact FROM users u JOIN outlets host USING(outlet_id)) as der ON der.user_id=do.user_id
					WHERE do_id="' . $do_id . '"';
            } else {
                $sql = 'SELECT do.*,der.*,o.name as ship_outlet, o.address as ship_address1, "" as ship_address2, o.phone as ship_contact
					FROM delivery_orders do 
					JOIN customers o USING(customer_id) 
					JOIN (SELECT u.user_id,host.name as host_outlet, host.address1 as host_address1, host.address2 as host_address2, host.contact as host_contact FROM users u JOIN outlets host USING(outlet_id)) as der ON der.user_id=do.user_id
					WHERE do_id="' . $do_id . '"';
            }

            $query = $this->db->query($sql);
            $result = $query->row_array();
            return $result;
        }

        function doItems($do_id) {
            $sql = 'SELECT * FROM do_items JOIN stock USING(item_id) WHERE do_id=' . $do_id;
            $result = $this->shared_model->getQuery($sql);
            return $result;
        }

        function dealerDetails($did) {
            $sql = 'SELECT * FROM  `invoices` JOIN customers USING ( customer_id ) WHERE dealer_id ="' . $did . '"';
            $result = $this->shared_model->getQuery($sql);
            return $result;
        }

        function custemerDetails($cid) {
            $sql = 'SELECT * , SUM( price * 1.18 ) AS totalprice, SUM( quantity ) AS totalqty FROM  `i_items` JOIN invoices USING ( invoice_id )  JOIN stock USING ( item_id ) JOIN customers USING (customer_id)  WHERE customer_id =' . $cid . ' GROUP BY item_id';
            $result = $this->shared_model->getQuery($sql);
            return $result;
        }

        function stockStatus($sid) {
            $sql = 'SELECT * FROM  `stock` JOIN stock_outlets USING ( item_id ) JOIN outlets USING (outlet_id) WHERE stock.active=1 AND outlet_id ="' . $sid . '"';
            $result = $this->shared_model->getQuery($sql);
            return $result;
        }

        function dealerStatus($sid) {
            $sql = 'SELECT * , SUM( quantity ) AS qty
			FROM delivery_orders
			JOIN outlets
			USING ( outlet_id ) 
			JOIN do_items
			USING ( do_id ) 
			JOIN stock
			USING ( item_id ) 
			WHERE outlet_id ="' . $sid . '"
			GROUP BY item_id';
            $result = $this->shared_model->getQuery($sql);
            return $result;
        }

        function getApproval($id) {
            $sql = 'select * from approvals JOIN invoices USING (invoice_id) JOIN customers USING (customer_id) where approval_id="' . $id . '"';
            $result = $this->shared_model->getRow($sql);
            return $result;
        }
    }

?>