<?php

    class Stock_Model extends CI_Model {

        function __construct() {
            parent::__construct();
        }

        public function getStockItems($outlet_id = false) {

            $this->load->library('datatables');
            $this->datatables->select('stock_num, description, remark, brand,category, SUM(qty) as qty,SUM(qty) as qty_hand,original_cost_price,cost_price,price1,pricing_info,location,  supplier_id, stock.item_id,price2,price3,price4', false)
                ->from('stock');
            $this->datatables->join('stock_outlets', 'stock_outlets.item_id=stock.item_id');
            if ($outlet_id) {
                $this->datatables->where('outlet_id', $outlet_id);
            }
            $this->datatables->group_by('stock.item_id');
            $this->datatables->edit_column('supplier_id', 'All', '');

            return $this->datatables->generate();
        }

        public function getStockLists($outlet_id = false) {

            $this->load->library('datatables');
            $this->datatables->select('stock_num, description, remark, brand,
                                        SUM(qty) as qty,SUM(qty) as qty_hand, cost_price, price1, pricing_info, location')
                ->from('stock');
            $this->datatables->join('stock_outlets', 'stock_outlets.item_id=stock.item_id');
            if ($outlet_id) {
                $this->datatables->where('outlet_id', $outlet_id);
            }
            $this->datatables->group_by('stock.item_id');
            //$this->datatables->edit_column('supplier_id', 'All', '');

            return $this->datatables->generate();
        }

        public function getStockItemDetails($id) {
            $sql = 'SELECT *,stock_outlets.location as location FROM stock_outlets JOIN outlets USING (outlet_id) WHERE item_id="' . $id . '"';
            $result = $this->shared_model->getQuery($sql);

            foreach ($result as $key => $res) {
                $result[$key]->cost_price = format_price($res->cost_price);
                $result[$key]->sell_price = format_price($res->sell_price);
                $result[$key]->price1 = format_price($res->price1);
                $result[$key]->price2 = format_price($res->price2);
                $result[$key]->price3 = format_price($res->price3);
                $result[$key]->price4 = format_price($res->price4);
            }
            return $result;
        }

        public function getItemDetails($id) {
            $sql = 'SELECT * FROM stock JOIN stock_outlets USING (item_id) WHERE item_id="' . $id . '" AND outlet_id="' . $this->session->userdata('outlet_id') . '"';
            $result = $this->shared_model->getRow($sql);
            return $result;
        }

        public function stockItems($brand = false, $category = false) {
            $sql = 'SELECT * FROM stock JOIN stock_outlets USING(item_id) WHERE outlet_id="' . $this->session->userdata('outlet_id') . '"';
            if ($brand) {
                $sql .= ' AND brand="' . $brand . '"';
            }
            if ($category) {
                $sql .= ' AND category="' . $category . '"';
            }
            $sql .= ' LIMIT 10090';

            $result = $this->shared_model->getQuery($sql);
            //print_r($result);
            //exit;
            return $result;
        }

        public function getStockTake() {
            $this->load->library('datatables');
            $this->datatables->select('stock_num,part_no, description,model_no,brand,category,cost_price,sell_price,last_sell_price, qty, stock_take, reason, date_format(date,"%D %b %Y")', false)
                ->from('stock');
            $this->datatables->join('stock_outlets', 'stock_outlets.item_id=stock.item_id');
            $this->datatables->join('stock_take', 'stock_take.item_id=stock.item_id');
            $this->datatables->where('stock_outlets.outlet_id', $this->session->userdata('outlet_id'));
            return $this->datatables->generate();
        }

        public function getOldItemReport($date_from = false, $date_to = false) {
            $outlet_id = $this->session->userdata('outlet_id');
            $date_from = $date_from ? date('Y-m-d H:i:s', strtotime($date_from)) : date('Y-01-01 00:00:00');
            $date_to = $date_to ? date('Y-m-d H:i:s', strtotime($date_to)) : date('Y-m-d H:i:s');
            $this->load->library('datatables');
            $where_sup = '';
            $query = 'SELECT *,balance as final_balance
              FROM 
              (SELECT ii.item_id,users.outlet_id, stock_num,barcode,brand,stock.supplier_id,category,description, date_format(invoice_time_stamp,"%D %b %Y") as date_f, invoice_time_stamp as date, ii.invoice_id as reference, customers.name as customer_vendor,"invoice" as transaction_type, SUM(quantity) as issued, 0 as received, null as balance
                FROM invoices i JOIN i_items ii USING(invoice_id) JOIN stock USING(item_id) JOIN customers USING(customer_id) JOIN users USING(user_id)
                WHERE i.active="Y" AND returned="N" AND users.outlet_id="' . $this->session->userdata('outlet_id') . '" AND invoice_time_stamp BETWEEN "' . $date_from . '" AND "' . $date_to . '"
                GROUP BY item_id,i.invoice_id
                UNION ALL
                SELECT ii.item_id,users.outlet_id, stock_num,barcode,brand,stock.supplier_id,category,description, date_format(cs_time_stamp,"%D %b %Y") as date_f, cs_time_stamp as date, ii.cs_id as reference, customers.name as customer_vendor,"retail sale" as transaction_type, SUM(quantity) as issued, 0 as received, null as balance
                FROM cash_sales i JOIN cs_items ii USING(cs_id) JOIN stock USING(item_id) JOIN customers USING(customer_id) JOIN users USING(user_id)
                WHERE i.active="Y" AND returned="N" AND users.outlet_id="' . $this->session->userdata('outlet_id') . '" AND cs_time_stamp BETWEEN "' . $date_from . '" AND "' . $date_to . '"
                GROUP BY item_id,i.cs_id
                UNION ALL
                SELECT ii.item_id,users.outlet_id, stock_num,barcode,brand,stock.supplier_id,category,description, date_format(so_time_stamp,"%D %b %Y") as date_f, so_time_stamp as date, ii.so_id as reference, customers.name as customer_vendor,"sales order" as transaction_type, SUM(quantity) as issued, 0 as received, null as balance
                FROM sales_orders i JOIN so_items ii USING(so_id) JOIN stock USING(item_id) JOIN customers USING(customer_id) JOIN users USING(user_id)
                WHERE i.active="Y" AND i.cs_id is null AND i.invoice_id is null AND returned="N" AND so_time_stamp BETWEEN "' . $date_from . '" AND "' . $date_to . '"  AND users.outlet_id="' . $this->session->userdata('outlet_id') . '"
                GROUP BY item_id,i.so_id
                UNION ALL
                SELECT ii.item_id,users.outlet_id, stock_num,barcode,brand,stock.supplier_id,category,description, date_format(date,"%D %b %Y") as date_f, date as date, ii.do_id as reference, IF(outlets.name is null, customers.name, outlets.name) as customer_vendor,"delivery order" as transaction_type, SUM(quantity) as issued, 0 as received, null as balance
                FROM delivery_orders i JOIN do_items ii USING(do_id) JOIN stock USING(item_id) LEFT JOIN outlets USING(outlet_id) LEFT JOIN customers USING(customer_id) JOIN users USING(user_id)
                WHERE i.active="Y" AND users.outlet_id="' . $this->session->userdata('outlet_id') . '" AND i.invoice_id is null AND date BETWEEN "' . $date_from . '" AND "' . $date_to . '"
                GROUP BY item_id,i.do_id
                UNION ALL
                SELECT ii.item_id,i.outlet_id, stock_num,barcode,brand,stock.supplier_id,category,description, date_format(date,"%D %b %Y") as date_f, date as date, ii.do_id as reference, outlets.name as customer_vendor,"delivery order" as transaction_type, 0 as issued, SUM(quantity) as received, null as balance
                FROM delivery_orders i JOIN do_items ii USING(do_id) JOIN stock USING(item_id) JOIN users USING(user_id) JOIN outlets ON outlets.outlet_id=users.outlet_id
                WHERE i.active="Y" AND i.outlet_id="' . $this->session->userdata('outlet_id') . '" AND date BETWEEN "' . $date_from . '" AND "' . $date_to . '"
                GROUP BY item_id,i.do_id
                UNION ALL
                SELECT ii.item_id,users.outlet_id, stock_num,barcode,brand,stock.supplier_id,category,description, date_format(recieving_date,"%D %b %Y") as date_f, recieving_date as date, ii.pi_id as reference, suppliers.name as customer_vendor,"purchase invoice" as transaction_type, 0 as issued, SUM(quantity) as received, null as balance
                FROM purchase_invoices i JOIN pi_items ii USING(pi_id) JOIN stock USING(item_id) JOIN users USING(user_id) JOIN suppliers ON suppliers.supplier_id=i.supplier_id
                WHERE i.active="Y" AND i.published=1 AND users.outlet_id="' . $this->session->userdata('outlet_id') . '" AND recieving_date BETWEEN "' . $date_from . '" AND "' . $date_to . '"
                GROUP BY item_id,i.pi_id 
                UNION ALL
                SELECT i.item_id,users.outlet_id, stock_num,barcode,brand,stock.supplier_id,category,description, date_format(date,"%D %b %Y") as date_f, date as date, "/" as reference, "/" as customer_vendor,"stock take" as transaction_type, 0 as issued, 0 received, stock_take as balance
                FROM stock_take i JOIN stock USING(item_id) JOIN users USING(user_id) 
                WHERE users.outlet_id="' . $this->session->userdata('outlet_id') . '" AND date BETWEEN "' . $date_from . '" AND "' . $date_to . '"
                GROUP BY item_id,i.stock_take_id) as i
              WHERE 1 ' . $where_sup;
            $fields = array('stock_num', 'description', 'date', 'reference', 'customer_vendor', 'transaction_type', 'issued', 'received', 'final_balance', 'item_id');
            $search_fields = array('stock_num', 'barcode', 'brand', 'category', 'description', 'date');
            return $this->datatables->customQuery($fields, $query, $search_fields, 'ORDER BY date asc,');
        }
        
        public function getItemReport($date_from = false, $date_to = false)
        {
            $outlet_id = $this->session->userdata('outlet_id');
            $date_from = $date_from ? date('Y-m-d 00:00:00', strtotime($date_from)) : date('Y-01-01 00:00:00');
            $date_to = $date_to ? date('Y-m-d 23:59:59', strtotime($date_to)) : date('Y-m-d H:i:s');
            $this->load->library('datatables');
            $query = 'SELECT stock_activity.item_id, stock_activity.outlet_id, stock_num, barcode, brand, supplier_id, category, description, date_format(activity_time_stamp,"%D %b %Y") as date_f, activity_time_stamp as date, reference_id as reference, reference as transaction_type, IF(reference = "Invoice" OR reference = "Invoice Update Increase Quantity" OR reference = "Cash Sale" OR reference = "Cash Sale Update Increase Quantity" OR reference = "Service Order"  OR reference = "Delivery Order" OR reference = "Own Products" OR reference = "Invoice Return Delete" OR reference = "Cash Sale Return Delete" OR reference = "Service Order Return Delete" OR reference = "Invoice Retrieve" OR reference = "Cash Sale Retrieve" OR reference = "Service Order Retrieve" OR reference = "Invoice Return Undo" OR reference = "Purchase Returns" OR reference = "Cash Sale Return Undo" OR reference = "Service Order Return Undo",quantity,0) as issued, IF(reference = "Invoice Return Item" OR reference = "Credit Note" OR reference = "Invoice Update Reduce Quantity" OR reference = "Cash Sale Return Item" OR reference = "Cash Sale Update Reduce Quantity" OR reference = "Service Order Return Item" OR reference = "Purchase Invoice" OR reference = "Invoice Delete" OR reference = "Invoice Item Delete" OR reference = "Cash Sale Delete" OR reference = "Service Order Delete" OR reference = "Own Products Delete" OR reference = "Invoice Return Retrieve" OR reference = "Cash Sale Return Retrieve" OR reference = "Service Order Return Retrieve" OR reference = "Accept Delivery In",quantity,0) as received, balance as final_balance from stock_activity JOIN stock USING(item_id) WHERE outlet_id = "' . $this->session->userdata('outlet_id') . '" AND activity_time_stamp BETWEEN "' . $date_from . '" AND "' . $date_to . '" ORDER BY activity_time_stamp desc';

            $fields=array('stock_num','description','date', 'reference',  'transaction_type', 'issued', 'received', 'final_balance','item_id');
            $search_fields=array('stock_num','barcode','brand','category','description', 'date', 'reference', 'transaction_type', 'issued', 'received', 'final_balance','item_id');      
            return $this->datatables->customQuery($fields,$query,$search_fields,'ORDER BY date desc, '); 
        }

        public function getStockBalance($date_from = false, $date_to = false, $brand = false, $category = false)
        {
            $outlet_id = $this->session->userdata('outlet_id');
            $date_from = $date_from ? date('Y-m-d 00:00:00', strtotime($date_from)) : date('Y-01-01 00:00:00');
            $date_to = $date_to ? date('Y-m-d 23:59:59', strtotime($date_to)) : date('Y-m-d H:i:s');
            $this->load->library('datatables');
            ///mfhassan22@gmail.com i have changed the *balance* to (SELECT qty FROM stock_outlets WHERE stock_outlets.item_id=stock_activity.item_id AND stock_outlets.outlet_id=stock_activity.outlet_id)


            $query = 'SELECT stock_num, brand,category, description, "'.$date_to.'" as date, SUM(qty) as final_balance, cost_price, CAST(qty as DECIMAL)*CAST(cost_price as DECIMAL(10,2)) as selling_price
                FROM stock
                join stock_outlets using(item_id)

                WHERE outlet_id = "' . $this->session->userdata('outlet_id') . '"
                and stock.item_id NOT IN (SELECT distinct(item_id) from stock_activity where activity_time_stamp > "'.$date_to.'")';

            if ($brand && $brand != "") {
                $query .= ' AND brand="' . $brand . '"';
            }
            if ($category && $category != "" ) {
                $query .= ' AND category="' . $category . '"';
            }

            $query .= 'GROUP BY stock.item_id

                UNION 

                SELECT stock_num, brand,category, description, "'.$date_to.'" as date, SUM(qty) as final_balance, cost_price, CAST(qty as DECIMAL)*CAST(cost_price as DECIMAL(10,2)) as selling_price

                FROM stock s
                join stock_outlets so using(item_id)
                join stock_activity sa using(item_id)

                WHERE so.outlet_id = "' . $this->session->userdata('outlet_id') . '" and sa.outlet_id = "' . $this->session->userdata('outlet_id') . '" 
                and s.item_id IN (SELECT distinct(item_id) from stock_activity where activity_time_stamp > "'.$date_to.'")
                and sa.activity_time_stamp = (SELECT MAX(sa2.activity_time_stamp) FROM stock_activity sa2 WHERE sa2.activity_time_stamp <= "'.$date_to.'" and sa.item_id=sa2.item_id and sa2.outlet_id="' . $this->session->userdata('outlet_id') . '")';

            if ($brand && $brand != "") {
                $query .= ' AND brand="' . $brand . '"';
            }
            if ($category && $category != "" ) {
                $query .= ' AND category="' . $category . '"';
            }

            $query .= 'GROUP BY s.item_id';


            /*$query = 'SELECT stock_activity.item_id, stock_activity.outlet_id, stock_num, brand, category, description, date_format(activity_time_stamp,"%D %b %Y") as date_f, activity_time_stamp as date, (SELECT qty FROM stock_outlets WHERE stock_outlets.item_id=stock_activity.item_id AND stock_outlets.outlet_id=stock_activity.outlet_id) as final_balance, (cost_price * (SELECT qty FROM stock_outlets WHERE stock_outlets.item_id=stock_activity.item_id AND stock_outlets.outlet_id=stock_activity.outlet_id)) as cost_price, (sell_price * (SELECT qty FROM stock_outlets WHERE stock_outlets.item_id=stock_activity.item_id AND stock_outlets.outlet_id=stock_activity.outlet_id)) as selling_price from stock_activity JOIN stock USING(item_id) JOIN stock_outlets USING (item_id) WHERE stock_outlets.outlet_id = "' . $this->session->userdata('outlet_id') . '" AND activity_time_stamp BETWEEN "'  . $date_from . '" AND "' . $date_to . '"';*/
            

            

            $fields=array('stock_num', 'brand', 'category', 'description', 'date', 'final_balance','cost_price', 'selling_price');
            $search_fields=array('stock_num', 'brand', 'category', 'description', 'final_balance','cost_price', 'selling_price');      
            $dataqry =  $this->datatables->customQuery($fields,$query,$search_fields,'ORDER BY date asc, ');
            foreach ($dataqry as $key => $value) {
                 # code...
             } 
        }

        public function getStockBalanceExport($date_from = false, $date_to = false, $brand = false, $category = false)
        {
            $outlet_id = $this->session->userdata('outlet_id');
            $date_from = $date_from ? date('Y-m-d 00:00:00', strtotime($date_from)) : date('Y-01-01 00:00:00');
            $date_to = $date_to ? date('Y-m-d 23:59:59', strtotime($date_to)) : date('Y-m-d H:i:s');
            ///mfhassan22@gmail.com i have changed the *balance* to (SELECT qty FROM stock_outlets WHERE stock_outlets.item_id=stock_activity.item_id AND stock_outlets.outlet_id=stock_activity.outlet_id)

            $query = 'SELECT stock_num, brand,category, description, "'.$date_to.'" as date, SUM(qty) as final_balance, cost_price, CAST(qty as DECIMAL)*CAST(cost_price as DECIMAL(10,2)) as selling_price
                FROM stock
                join stock_outlets using(item_id)

                WHERE outlet_id = "' . $this->session->userdata('outlet_id') . '"
                and stock.item_id NOT IN (SELECT distinct(item_id) from stock_activity where activity_time_stamp > "'.$date_to.'")';

            if ($brand && $brand != "") {
                $query .= ' AND brand="' . $brand . '"';
            }
            if ($category && $category != "" ) {
                $query .= ' AND category="' . $category . '"';
            }

            $query .= 'GROUP BY stock.item_id

                UNION 

                SELECT stock_num, brand,category, description, "'.$date_to.'" as date, SUM(qty) as final_balance, cost_price, CAST(qty as DECIMAL)*CAST(cost_price as DECIMAL(10,2)) as selling_price

                FROM stock s
                join stock_outlets so using(item_id)
                join stock_activity sa using(item_id)

                WHERE so.outlet_id = "' . $this->session->userdata('outlet_id') . '" and sa.outlet_id = "' . $this->session->userdata('outlet_id') . '" 
                and s.item_id IN (SELECT distinct(item_id) from stock_activity where activity_time_stamp > "'.$date_to.'")
                and sa.activity_time_stamp = (SELECT MAX(sa2.activity_time_stamp) FROM stock_activity sa2 WHERE sa2.activity_time_stamp <= "'.$date_to.'" and sa.item_id=sa2.item_id and sa2.outlet_id="' . $this->session->userdata('outlet_id') . '")';

            if ($brand && $brand != "") {
                $query .= ' AND brand="' . $brand . '"';
            }
            if ($category && $category != "" ) {
                $query .= ' AND category="' . $category . '"';
            }

            $query .= 'GROUP BY s.item_id';
            
            $result = $this->shared_model->getQuery($query);

            return $result;
        }

        function calculateCurrentBalance($date, $item_id, $outlet_id) {
            $qry = 'SELECT SUM(qty) as s
            FROM 
             (SELECT (-SUM(quantity)) as qty
              FROM invoices i JOIN i_items ii USING(invoice_id) JOIN stock USING(item_id) JOIN customers USING(customer_id) JOIN users USING(user_id)
              WHERE users.outlet_id="' . $outlet_id . '" AND i.active="Y" AND returned="N" AND TIMESTAMPDIFF(SECOND,invoice_time_stamp,"' . $date . '")>=0 AND stock.item_id="' . $item_id . '"  AND NOT EXISTS(SELECT * FROM stock_take JOIN users u USING(user_id) WHERE date>i.invoice_time_stamp AND date<"' . $date . '" AND item_id=ii.item_id AND u.outlet_id=users.outlet_id)
              UNION ALL
              SELECT (-SUM(quantity)) as qty
              FROM cash_sales i JOIN cs_items ii USING(cs_id) JOIN stock USING(item_id) JOIN customers USING(customer_id) JOIN users USING(user_id)
              WHERE users.outlet_id="' . $outlet_id . '" AND i.active="Y" AND returned="N" AND TIMESTAMPDIFF(SECOND,cs_time_stamp,"' . $date . '")>=0 AND stock.item_id="' . $item_id . '" AND NOT EXISTS(SELECT * FROM stock_take JOIN users u USING(user_id) WHERE date>i.cs_time_stamp AND date<"' . $date . '" AND item_id=ii.item_id AND u.outlet_id=users.outlet_id)
              UNION ALL
              SELECT (-SUM(quantity)) as qty
              FROM sales_orders i JOIN so_items ii USING(so_id) JOIN stock USING(item_id) JOIN customers USING(customer_id) JOIN users USING(user_id)
              WHERE users.outlet_id="' . $outlet_id . '" AND i.active="Y" AND i.cs_id is null AND i.invoice_id is null AND returned="N" AND TIMESTAMPDIFF(SECOND,so_time_stamp,"' . $date . '")>=0 AND stock.item_id="' . $item_id . '" AND NOT EXISTS(SELECT * FROM stock_take JOIN users u USING(user_id) WHERE date>i.so_time_stamp AND date<"' . $date . '" AND item_id=ii.item_id AND u.outlet_id=users.outlet_id)
              UNION ALL
              SELECT (-SUM(quantity)) as qty
              FROM delivery_orders i JOIN do_items ii USING(do_id) JOIN stock USING(item_id) LEFT JOIN outlets USING(outlet_id) JOIN users USING(user_id)
              WHERE users.outlet_id="' . $outlet_id . '" AND i.active="Y" AND TIMESTAMPDIFF(SECOND,date,"' . $date . '")>=0 AND stock.item_id="' . $item_id . '" AND invoice_id is null AND NOT EXISTS(SELECT * FROM stock_take JOIN users u USING(user_id) WHERE date>i.date AND date<"' . $date . '" AND item_id=ii.item_id AND u.outlet_id=users.outlet_id)
              UNION ALL
              SELECT (SUM(quantity)) as qty
              FROM delivery_orders i JOIN do_items ii USING(do_id) JOIN stock USING(item_id) JOIN users USING(user_id) JOIN outlets ON outlets.outlet_id=users.outlet_id
              WHERE i.outlet_id="' . $outlet_id . '" AND i.active="Y"  AND TIMESTAMPDIFF(SECOND,date,"' . $date . '")>=0 AND stock.item_id="' . $item_id . '" AND NOT EXISTS(SELECT * FROM stock_take JOIN users u USING(user_id) WHERE date>i.date AND date<"' . $date . '" AND item_id=ii.item_id AND u.outlet_id=users.outlet_id)
              UNION ALL
              SELECT (SUM(quantity)) as qty
              FROM purchase_invoices i JOIN pi_items ii USING(pi_id) JOIN stock USING(item_id) JOIN users USING(user_id) JOIN suppliers ON suppliers.supplier_id=i.supplier_id
              WHERE users.outlet_id="' . $outlet_id . '" AND i.active="Y"  AND i.published=1  AND TIMESTAMPDIFF(SECOND,recieving_date,"' . $date . '")>=0 AND stock.item_id="' . $item_id . '" AND NOT EXISTS(SELECT * FROM stock_take JOIN users u USING(user_id) WHERE date>i.recieving_date AND date<"' . $date . '" AND item_id=ii.item_id AND u.outlet_id=users.outlet_id)
              UNION ALL 
              (SELECT stock_take as qty
              FROM stock_take i JOIN stock USING(item_id) JOIN users USING(user_id) 
              WHERE users.outlet_id="' . $outlet_id . '" AND TIMESTAMPDIFF(SECOND,date,"' . $date . '")>=0 AND stock.item_id="' . $item_id . '"
              ORDER BY i.date desc LIMIT 1)
            ) as der_sum';


            return $this->shared_model->getRow($qry)->s;
        }

        public function getInternalPurchaseOrders() {
            $sql = 'select outlets.name, purchase_orders.po_id, purchase_orders.date from purchase_orders JOIN users USING (user_id) JOIN outlets ON (outlets.outlet_id=purchase_orders.outlet_id) where users.outlet_id=' . $this->session->userdata('outlet_id');
            $result = $this->shared_model->getQuery($sql);
            return $result;
        }
    }