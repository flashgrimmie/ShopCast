<?php

class Retail_Model extends CI_Model

{    

    function __construct()

    {
        parent::__construct();
    }

    function getRetailItems($search=false)
    {
      $outlet_id=$this->session->userdata('outlet_id');
      $sql='SELECT * FROM stock JOIN stock_outlets USING(item_id) WHERE outlet_id="'.$outlet_id.'"';
      if($search) {
        $sql.=' AND (description LIKE "%'.$search.'%"
                     OR stock_num LIKE "%'.$search.'%" 
                     OR barcode LIKE "%'.$search.'%"
                     OR part_no LIKE "%'.$search.'%"
                     OR model_no LIKE "%'.$search.'%"
                     OR brand LIKE "%'.$search.'%" )';
      }
      $sql.=' LIMIT 21';
      $result=$this->shared_model->getQuery($sql);
      return $result;
    }

    function getRetailItemsTable()
    {
        $this->load->library('datatables');
        $this->datatables->select('stock_num,part_no, barcode,brand, category, description,model_no, remark, qty,sell_price, last_sell_price, stock.item_id',false)
        ->from('stock');
        $this->datatables->join('stock_outlets','stock_outlets.item_id=stock.item_id');
        $this->datatables->where('outlet_id', $this->session->userdata('outlet_id'));
        $this->datatables->where('stock.active', 1);
        return $this->datatables->generate();
    }

    function getInvoices($active='Y')
    {
    	$outlet_id=$this->session->userdata('outlet_id');
      $this->load->library('datatables');
      $this->datatables->select('invoice_id, date_issue, customers.name,customers.car_plate, total, deposit, opening_balance,partial, status')
           ->from('invoices');
      $this->datatables->join('customers','invoices.customer_id=customers.customer_id');
      $this->datatables->join('users','invoices.user_id=users.user_id');
      $this->datatables->where('invoices.active',$active);
      $this->datatables->where('users.outlet_id',$outlet_id);
      $this->datatables->add_column('8','','');
      return $this->datatables->generate();
    }

    function getDraftTransactions()
    {
      $outlet_id=$this->session->userdata('outlet_id');
      $this->load->library('datatables');
      $query='SELECT CONCAT("I",invoice_id) as reference_id,date_format(date_issue,"%D %b %Y") as datef,date_issue as date,IF(customer_id is not null,(SELECT name FROM customers WHERE customer_id=invoices.customer_id),"Undefined") as customer,total,"Invoice" as type,invoice_id as id
              FROM invoices JOIN users USING(user_id) WHERE draft="Y" AND invoices.active="Y" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
              UNION ALL
              SELECT CONCAT("RS",cs_id) as reference_id,date_format(date,"%D %b %Y") as datef,date,IF(customer_id is not null,(SELECT name FROM customers WHERE customer_id=cash_sales.customer_id),"Undefined") as customer,total,"Retail Sale" as type,cs_id as id
              FROM cash_sales JOIN users USING(user_id) WHERE draft="Y" and cash_sales.active="Y" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
              UNION ALL
              SELECT CONCAT("SO",so_id) as reference_id,date_format(date,"%D %b %Y") as datef,date,IF(customer_id is not null,(SELECT name FROM customers WHERE customer_id=sales_orders.customer_id),"Undefined") as customer,total,"Sales Order" as type,so_id as id
              FROM sales_orders JOIN users USING(user_id) WHERE draft="Y" and sales_orders.active="Y" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"';
      $fields=array('reference_id','datef','customer','total','type','id');
      $search_fields=array('reference_id','date','customer','total','type','id');
      return $this->datatables->customQuery($fields,$query,$search_fields); 
    }

    function getVoidTransactions()
    {
      $outlet_id=$this->session->userdata('outlet_id');
      $this->load->library('datatables');
      $query='SELECT CONCAT("I",invoice_id) as reference_id,date_format(date_issue,"%D %b %Y") as datef,date_issue as date,IF(customer_id is not null,(SELECT name FROM customers WHERE customer_id=invoices.customer_id),"Undefined") as customer,total,"Invoice" as type,(SELECT name FROM users WHERE user_id=invoices.user_id) as staff,discard_reason
              FROM invoices JOIN users USING(user_id) WHERE draft="Y" AND invoices.active="N" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
              UNION ALL
              SELECT CONCAT("RS",cs_id) as reference_id,date_format(date,"%D %b %Y") as datef,date,IF(customer_id is not null,(SELECT name FROM customers WHERE customer_id=cash_sales.customer_id),"Undefined") as customer,total,"Retail Sale" as type,(SELECT name FROM users WHERE user_id=cash_sales.user_id) as staff,discard_reason
              FROM cash_sales JOIN users USING(user_id) WHERE draft="Y" and cash_sales.active="N" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"
              UNION ALL
              SELECT CONCAT("SO",so_id) as reference_id,date_format(date,"%D %b %Y") as datef,date,IF(customer_id is not null,(SELECT name FROM customers WHERE customer_id=sales_orders.customer_id),"Undefined") as customer,total,"Sales Order" as type,(SELECT name FROM users WHERE user_id=sales_orders.user_id) as staff,discard_reason
              FROM sales_orders JOIN users USING(user_id) WHERE draft="Y" and sales_orders.active="N" AND users.outlet_id="'.$this->session->userdata('outlet_id').'"';
      $fields=array('reference_id','datef','customer','total','type','staff','discard_reason');
      $search_fields=array('reference_id','date','customer','total','type','staff','discard_reason');
      return $this->datatables->customQuery($fields,$query,$search_fields); 
    }

    function getCSs($active='Y')
    {
      $outlet_id=$this->session->userdata('outlet_id');
      $this->load->library('datatables');
      $this->datatables->select('cs_id, date, customers.name, total')
           ->from('cash_sales');
      $this->datatables->join('customers','cash_sales.customer_id=customers.customer_id');
      $this->datatables->join('users','cash_sales.user_id=users.user_id');
      $this->datatables->where('cash_sales.active',$active);
      $this->datatables->where('users.outlet_id',$outlet_id);
      $this->datatables->add_column('4','','');
      return $this->datatables->generate();
    }

    function getSOs($active='Y')
    {
      $outlet_id=$this->session->userdata('outlet_id');
      $this->load->library('datatables');
       $this->datatables->select('so_id, date, customers.name, total, deposit, subtotal, cs_id, invoice_id')
            ->from('sales_orders');
      $this->datatables->join('customers','sales_orders.customer_id=customers.customer_id');
      $this->datatables->join('users','sales_orders.user_id=users.user_id');
      $this->datatables->where('sales_orders.active',$active);
      $this->datatables->where('users.outlet_id',$outlet_id);
      $this->datatables->add_column('4','','');
      return $this->datatables->generate();
    }

    function getDOs($active='Y')
    {
      $outlet_id=$this->session->userdata('outlet_id');
      $this->load->library('datatables');
      $this->datatables->select('do_id, date_format(date,"%D %b %Y"), customers.name, deposit, invoice_id',false)
           ->from('delivery_orders');
      $this->datatables->join('customers','delivery_orders.customer_id=customers.customer_id');
      $this->datatables->join('users','delivery_orders.user_id=users.user_id');
      $this->datatables->where('delivery_orders.active',$active);
      $this->datatables->where('delivery_orders.customer_id is not null','',false);
      $this->datatables->where('users.outlet_id',$outlet_id);
      $this->datatables->add_column('5','','');
      return $this->datatables->generate();
    }

    function getReturnedItems()
    {
      $outlet_id=$this->session->userdata('outlet_id');
      $this->load->library('datatables');
      $query='SELECT stock_num, description, quantity, (price*(1-i_items.discount/100)-discount_value)*quantity as total_price, date_format(date_returned,"%D %b %Y") as date_returned_f,date_returned, "invoice" as type, i_items.invoice_id as receipt_id
            FROM i_items 
            JOIN stock USING(item_id)
            JOIN invoices USING(invoice_id)
            JOIN users USING(user_id)
            WHERE users.outlet_id="'.$outlet_id.'" AND i_items.returned="Y"
            UNION
            SELECT stock_num, description, quantity, (price*(1-cs_items.discount/100)-discount_value)*quantity as total_price, date_format(date_returned,"%D %b %Y") as date_returned_f,date_returned, "cash sale" as type, cs_items.cs_id as receipt_id
            FROM cs_items 
            JOIN stock USING(item_id)
            JOIN cash_sales USING(cs_id)
            JOIN users USING(user_id)
            WHERE users.outlet_id="'.$outlet_id.'" AND cs_items.returned="Y"
            UNION
            SELECT stock_num, description, quantity, (price*(1-so_items.discount/100)-discount_value)*quantity as total_price, date_format(date_returned,"%D %b %Y") as date_returned_f,date_returned, "sales order" as type, so_items.so_id as receipt_id
            FROM so_items 
            JOIN stock USING(item_id)
            JOIN sales_orders USING(so_id)
            JOIN users USING(user_id)
            WHERE users.outlet_id="'.$outlet_id.'" AND so_items.returned="Y"';
      $fields=array('stock_num', 'description', 'quantity', 'total_price', 'date_returned_f', 'type', 'receipt_id');
      $search_fields=array('stock_num', 'description', 'quantity', 'total_price', 'date_returned', 'type', 'receipt_id');
      return $this->datatables->customQuery($fields,$query,$search_fields); 
    }

    function countInvoices()
    {
        $outlet_id=$this->session->userdata('outlet_id');

        $sql='SELECT count(*) as count FROM invoices i 
              JOIN customers c USING(customer_id) 
              JOIN users u USING(user_id) 
              LEFT JOIN outlets d ON (i.dealer_id=d.outlet_id) 
              WHERE u.outlet_id="'.$outlet_id.'" AND i.active="Y"';

        $result = $this->shared_model->getRow($sql, true);
        return $result['count']?$result['count']:0;
    }

    function getInvoicesTrash($offset,$limit)
    {
      $outlet_id=$this->session->userdata('outlet_id');

        $sql='SELECT i.*,c.*,d.name as dealername FROM invoices i 
              JOIN customers c USING(customer_id) 
              JOIN users u USING(user_id) 
              LEFT JOIN outlets d ON (i.dealer_id=d.outlet_id) 
              WHERE u.outlet_id="'.$outlet_id.'" AND i.active="N" 
              ORDER BY invoice_id desc  
              LIMIT '.$offset.','.$limit;

    $result=$this->shared_model->getQuery($sql);

    return $result;
    }

     function countInvoicesTrash()
    {
        $outlet_id=$this->session->userdata('outlet_id');

        $sql='SELECT count(*) as count FROM invoices i 
              JOIN customers c USING(customer_id) 
              JOIN users u USING(user_id) 
              LEFT JOIN outlets d ON (i.dealer_id=d.outlet_id) 
              WHERE u.outlet_id="'.$outlet_id.'" AND i.active="N"';

        $result = $this->shared_model->getRow($sql, true);
        return $result['count']?$result['count']:0;
    }

    function getCashSales($offset,$limit)
    {
        $outlet_id=$this->session->userdata('outlet_id');
        $this->load->library('datatables');
        $this->datatables->select('cs_id, date, customers.name, total')
             ->from('cash_sales');
        $this->datatables->join('customers','cash_sales.customer_id=customers.customer_id');
        $this->datatables->where('cash_sales.active','Y');
        $this->datatables->add_column('4','','');
        return $this->datatables->generate();
    }

    function countCashSales()
    {
        $outlet_id=$this->session->userdata('outlet_id');

        $sql='SELECT count(*) as count FROM cash_sales i 
        JOIN customers c USING(customer_id) 
        JOIN users u USING(user_id) 
        LEFT JOIN outlets d ON (i.dealer_id=d.outlet_id) 
        WHERE u.outlet_id="'.$outlet_id.'" ORDER BY cs_id desc';

        $result = $this->shared_model->getRow($sql, true);
        return $result['count']?$result['count']:0;
    }

//proveri outlet_id (se zema od session, ne od dealer)
    function getInvoiceItems($invoice_id,$returned='N')
    {
        $sql='SELECT *,i_items.id as iitem_id FROM i_items JOIN stock USING(item_id) JOIN stock_outlets so USING(item_id) WHERE invoice_id="'.$invoice_id.'" AND returned="'.$returned.'" AND so.outlet_id="'.$this->session->userdata('outlet_id').'"';

        $result=$this->shared_model->getQuery($sql);

        return $result;
    }

    function iItemDetails($iitem_id)
    {
        $sql='SELECT *,i_items.id as iitem_id FROM i_items JOIN stock USING(item_id) JOIN stock_outlets so USING(item_id) WHERE i_items.id="'.$iitem_id.'" AND so.outlet_id="'.$this->session->userdata('outlet_id').'"';

        $result=$this->shared_model->getRow($sql);

        return $result;
    }
    function csItemDetails($iitem_id)
    {
        $sql='SELECT *,cs_items.id as iitem_id FROM cs_items JOIN stock USING(item_id) JOIN stock_outlets so USING(item_id) WHERE cs_items.id="'.$iitem_id.'" AND so.outlet_id="'.$this->session->userdata('outlet_id').'"';

        $result=$this->shared_model->getRow($sql);

        return $result;
    }
    function soItemDetails($iitem_id)
    {
        $sql='SELECT *,so_items.id as iitem_id FROM so_items JOIN stock USING(item_id) JOIN stock_outlets so USING(item_id) WHERE so_items.id="'.$iitem_id.'" AND so.outlet_id="'.$this->session->userdata('outlet_id').'"';

        $result=$this->shared_model->getRow($sql);

        return $result;
    }

    function invoiceDetails($invoice_id)
    {
        $sql='SELECT * FROM invoices JOIN customers USING(customer_id) WHERE invoice_id="'.$invoice_id.'"';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }

    function getCSItems($cs_id,$returned='N')
    {
        $sql='SELECT *,cs_items.id as iitem_id FROM cs_items JOIN stock USING(item_id) JOIN stock_outlets so USING(item_id) WHERE cs_id="'.$cs_id.'" AND returned="'.$returned.'" AND so.outlet_id="'.$this->session->userdata('outlet_id').'"';

        $result=$this->shared_model->getQuery($sql);

        return $result;
    }

    function csDetails($cs_id)
    {
        $sql='SELECT * FROM cash_sales JOIN customers USING(customer_id) WHERE cs_id="'.$cs_id.'"';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }

    function getSOItems($so_id,$returned='N')
    {
        $sql='SELECT *,so_items.id as iitem_id FROM so_items JOIN stock USING(item_id) JOIN stock_outlets so USING(item_id) WHERE so_id="'.$so_id.'" AND returned="'.$returned.'" AND so.outlet_id="'.$this->session->userdata('outlet_id').'"';

        $result=$this->shared_model->getQuery($sql);

        return $result;
    }

    function soDetails($so_id)
    {
        $sql='SELECT * FROM sales_orders JOIN customers USING(customer_id) WHERE so_id="'.$so_id.'"';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }

    function getItemDetails($item_id)
    {
        $outlet_id=$this->session->userdata('outlet_id');
        $sql='SELECT * FROM stock JOIN stock_outlets USING(item_id) WHERE item_id="'.$item_id.'" AND outlet_id="'.$outlet_id.'"';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }
    
    function doDetails($do_id)
    {
        $sql='SELECT * FROM delivery_orders JOIN customers USING(customer_id) WHERE do_id="'.$do_id.'"';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }

    function getDOItems($do_id)
    {
        $sql='SELECT *,do_items.id as iitem_id FROM do_items JOIN stock USING(item_id) JOIN stock_outlets so USING(item_id) WHERE do_id="'.$do_id.'" AND so.outlet_id="'.$this->session->userdata('outlet_id').'"';

        $result=$this->shared_model->getQuery($sql);

        return $result;
    }

    function update_total($table,$items_table,$table_id,$table_id_val)
    {
      $iitems=$this->shared_model->getQuery('select * from '.$items_table.' where '.$table_id.'='.$table_id_val);
      $subtotal=0;
      foreach($iitems as $item) {
        if($item->returned=='Y') {
          $item_total=-($item->price*(1-$item->discount/100)-$item->discount_value)*$item->quantity;
        } else {
          $item_total=($item->price*(1-$item->discount/100)-$item->discount_value)*$item->quantity;
        }
        $subtotal+=$item_total;
      }
      $idetails=$this->shared_model->getRow('select * from '.$table.' where '.$table_id.'='.$table_id_val);
      $discount=$idetails->discount;
      $total=$subtotal*(1-$discount/100);
      $this->shared_model->update($table,$table_id,$table_id_val,array('subtotal'=>$subtotal,'total'=>$total));
      return $total;
    }

    function searchByStockNum($stock_num)
    {
        $outlet_id=$this->session->userdata('outlet_id');
        $sql='SELECT * FROM stock 
              JOIN stock_outlets USING(item_id) 
              WHERE stock_num="'.$stock_num.'"
              OR barcode="'.$stock_num.'" 
              AND outlet_id="'.$outlet_id.'"
              AND active=1';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }

    function searchByDescription($description)
    {
        $outlet_id=$this->session->userdata('outlet_id');
        $sql='SELECT * FROM stock 
              JOIN stock_outlets USING(item_id) 
              WHERE outlet_id="'.$outlet_id.'"
              AND (description LIKE "%'.$description.'%"
              OR model_no LIKE "%'.$description.'%"
              OR part_no LIKE "%'.$description.'%"
              OR brand LIKE "%'.$description.'%" 
              OR remark LIKE "%'.$description.'%")
              AND active=1
              LIMIT 20'; 
        $result=$this->shared_model->getQuery($sql);
        return $result;
    }
    function searchByItemId($item_id)
    {
        $outlet_id=$this->session->userdata('outlet_id');
        $sql='SELECT * FROM stock 
              JOIN stock_outlets USING(item_id) 
              WHERE item_id="'.$item_id.'"
              AND outlet_id="'.$outlet_id.'"
              AND stock.active=1';        
        $result=$this->shared_model->getRow($sql);
        return $result;
    }
}  

?>