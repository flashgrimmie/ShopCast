<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

    class Retail extends MY_Controller {


        function __construct() {
            parent::__construct();
            $this->data['active'] = 'retail';
            $this->load->model('retail_model');
            $this->load->model('setup_model');
        }

        public function index() {
            redirect('retail/invoices');
        }

        public function invoices($limit = false) {
            $data = $this->data;
            $data['headline'] = 'Invoices';
            $data['subactive'] = 'invoices';
            $data['date_filter'] = '';
            if ($this->input->post('date_from') != '') {
                $data['date_filter'] = date('Y-m-d', strtotime($this->input->post('date_from')));
            }

            $data['breadcrumbs'] = array('Retail' => '#', 'Invoices' => '#');
            $this->load->view('retail/invoices', $data);
        }

        public function exportInvoiceReport($active = 'N') {
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="downloaded.csv"');
            $invoices = $this->retail_model->getVoidInvoices($active);
            $invoices = json_decode($invoices, true);

            foreach ($invoices['aaData'] as $key => $value) {
                $invoices['aaData'][$key][1] = format_date($value[1]);
                $invoices['aaData'][$key][4] = format_price($value[4]);
                $invoices['aaData'][$key][5] = format_price($value[5]);
                $invoices['aaData'][$key][6] = format_price($value[4] - $value[5] - $value[6]);
            }

            $fields = array(0 => 'ID', 1 => 'date', 2 => 'customer', 3 => 'car_plate', 4 => 'total amount', 5 => 'deposit', 6 => 'amount due', 7 => 'status');
            echo implode(',', $fields) . "\n";
            foreach ($invoices['aaData'] as $key => $value) {
                foreach ($fields as $key1 => $field) {
                    echo str_replace(',', '', $value[$key1]) . ',';
                }
                echo "\n";
            }
            exit;
        }

        public function getInvoices($active = 'Y') {
            $issue_date = $this->input->post('date_from');
            $invoices = $this->retail_model->getInvoices($active, $issue_date);

            $invoices = json_decode($invoices, true);

            foreach ($invoices['aaData'] as $key => $value) {
                $invoices['aaData'][$key][1] = format_date($value[1]);
                $invoices['aaData'][$key][4] = format_price($value[4]);
                $invoices['aaData'][$key][5] = format_price($value[5]);
                $invoices['aaData'][$key][6] = format_price($value[6]);
                $invoices['aaData'][$key][7] = format_price($value[4] - $value[5] - $value[7]);
            }

            $invoices = $this->shared_model->JEncode($invoices);
            echo $invoices;
        }

        public function create_invoice($invoice_id = false) {
            $data = $this->data;
            $data['headline'] = 'Create Invoices';
            $data['subactive'] = 'invoices';
            $data['breadcrumbs'] = array('Retail' => '#', 'Create Invoice' => '#');
            $data['retail_items'] = $this->retail_model->getRetailItems();
			
            if ($invoice_id) {
                $data['invoice_details'] = $this->retail_model->invoiceDetails($invoice_id);
                $data['mechanics'] = isset($data['invoice_details']->mechanics) ? unserialize($data['invoice_details']->mechanics) : array();
                $data['i_items'] = $this->retail_model->getInvoiceItems($invoice_id);
            }
            $this->load->view('retail/add_invoice', $data);
        }

        public function discard_invoice($invoice_id) {
            $reason = $this->input->post('reason');
            $this->shared_model->update('invoices', 'invoice_id', $invoice_id, array('discard_reason' => $reason, 'active' => 'N', 'draft' => 'Y'));
            log_db_query($this->db->last_query());  // Log DB Query
            $this->session->set_flashdata('success', 'Invoice is successfully marked as void.');
            redirect('retail/draft_transactions');
        }

        public function save_invoice($invoice_id = false) {
            $invoice_details = $this->shared_model->getPost('invoices');
            $draft = 1;
            if ($this->input->post('draft') == 'draft') {
                $draft = 1;
                $invoice_details['draft'] = 'Y';
            } else {
                $draft = 0;
                $invoice_details['draft'] = 'N';
            }

            if($this->input->post('save')){
                $draft = 0;
                $invoice_details['draft'] = 'N';
            }

            $num = $this->input->post('item_id') > 0 ? sizeof($this->input->post('item_id')) : 0;

            if (count($invoice_details) <= 3 && !$num) {
                exit;
            }
            if ($invoice_id) {
                $saved = $this->shared_model->Lookup('invoices', 'invoice_id', array('invoice_id' => $invoice_id, 'draft' => 'N'));				
            } else {
                $saved = true;
            }
            if (!$invoice_id) {
                if ($invoice_details['status'] == 'paid') {
                    $invoice_details['date_payment'] = date('Y-m-d h:i:s');
                }
                $invoice_details['invoice_time_stamp'] = date('Y-m-d h:i:s');
                $invoice_details['user_id'] = $this->session->userdata('user_id');
                $this->shared_model->insert('invoices', $invoice_details);
                log_db_query($this->db->last_query());  // Log DB Query
                $invoice_id = mysql_insert_id();
                $customer_payments = array('invoice_id' => $invoice_id);
                $this->shared_model->insert('customer_payments', $customer_payments);
                log_db_query($this->db->last_query());  // Log DB Query
            }

            $item_ids = $this->input->post('item_id');
            $qtys = $this->input->post('quantity');
            $discounts = $this->input->post('item_discount');
            $discount_vals = $this->input->post('discount_value');
            $markup = $this->input->post('markup');
            $prices = $this->input->post('price');
            $total = 0;
            $subtotal = 0;

			//Check for negative quantity for new invoice - START
            if($this->input->post('save') && !$saved){
				for ($i = 0; $i < $num; $i++) {
					$old_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_ids[$i] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
					$correct_bal = $old_balance - $qtys[$i];
					/*if($correct_bal < 0) {
						$this->session->set_flashdata('error', 'Quantity can not be in negative. Please change the quantity.');
						redirect('retail/create_invoice/' . $invoice_id);
					}*/
				}
			}
			//Check for negative quantity for new invoice - END
			
            if ($saved) {

                $sql = 'select sum(quantity) as old_qty,item_id from i_items where invoice_id="' . $invoice_id . '" group by item_id';
                $old_items = $this->shared_model->getQuery($sql);
                foreach ($old_items as $key => $value) {
                    $check_items[$value->item_id] = $value->old_qty;
                }
					
				//Check for negative quantity for edited invoice - START
				for ($i = 0; $i < $num; $i++) {
					if (isset($check_items[$item_ids[$i]])) {
						$item_qty['quantity'] = $qtys[$i] - $check_items[$item_ids[$i]];
					} else {
						$item_qty['quantity'] = $qtys[$i];
					}
					$old_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_ids[$i] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
					$correct_bal = $old_balance - $item_qty['quantity'];
					/*if($correct_bal < 0) {
						 $this->session->set_flashdata('error', 'Quantity can not be in negative. Please change the quantity.');
						 redirect('retail/create_invoice/' . $invoice_id);
					}*/
				}
				//Check for negative quantity for edited invoice - END
            }
            $this->shared_model->delete('i_items', 'invoice_id', $invoice_id);

            for ($i = 0; $i < $num; $i++) {

                $item['item_id'] = $item_ids[$i];
                $item['invoice_id'] = $invoice_id;
                $item['quantity'] = $qtys[$i];
                $item['price'] = $prices[$i];
                $item['discount_value'] = $discount_vals[$i];
                $item['markup'] = $markup[$i];
                $item['discount'] = $discounts[$i];


                $inserted = $this->shared_model->insert('i_items', $item);
                if ($inserted) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    $desc_arr = explode("-", $item['discount']);
                    $desc = $desc_arr[0];
                    $total = ($item['quantity'] * ($item['price'] * (1 - $desc / 100) - $item['discount_value'] + $item['markup']));
                    if (sizeof($desc_arr) > 1 && $desc_arr[1] != '') {
                        $desc = $desc_arr[1];
                        $total = ($total * (1 - $desc / 100));
                    }
                    $subtotal += $total;

                    if (isset($check_items[$item['item_id']])) {
                        $item_qty['quantity'] = $item['quantity'] - $check_items[$item['item_id']];
                        unset($check_items[$item['item_id']]);
                    } else {
                        $item_qty['quantity'] = $item['quantity'];
                    }
                    if ($this->input->post('save_draft') != '1') {
						updateOutletQty($item_qty, $item['item_id'], $this->session->userdata('outlet_id'), 'out');
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						if ($this->shared_model->getRow('select activity_id from stock_activity where item_id=' . $item['item_id'] . ' AND reference_id = "' . $invoice_id . '" AND reference = "Invoice" AND outlet_id = ' . $this->session->userdata('outlet_id'))) {
							
							if ($item_qty['quantity'] < 0) {
								$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . (0-$item_qty['quantity']) . '","' . $invoice_id . '","Invoice Update Reduce Quantity","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")'); 
							} 
							if ($item_qty['quantity'] > 0){
								$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item_qty['quantity'] . '","' . $invoice_id . '","Invoice Update Increase Quantity","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")'); 
							}
							
						} else {				
							$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item['quantity'] . '","' . $invoice_id . '","Invoice","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")'); 
						}
						log_db_query($this->db->last_query());  // Log DB Query
					}
                }
            }
            if (isset($check_items)) {
                foreach ($check_items as $key => $value) {
                    updateOutletQty(array('quantity' => $value), $key, $this->session->userdata('outlet_id'), 'in');
					$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $key . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
					$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $key . '","' . $value . '","' . $invoice_id . '","Invoice Item Delete","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');
                    log_db_query($this->db->last_query());  // Log DB Query
                }
            }

            if (!isset($invoice_details['discount'])) {
                $invoice_details['discount'] = 0;
            }

            $total = $subtotal * (1 - $invoice_details['discount'] / 100);

            $invoice_details['subtotal'] = format_number($subtotal);
            $invoice_details['total'] = format_number($total);
            $invoice_details['date_issue'] = isset($invoice_details['date_issue']) ? date('Y-m-d', strtotime($invoice_details['date_issue'])) : date('Y-m-d');
            $invoice_details['delivery_order_no'] = $this->input->post('delivery_order_no');
            $mechanics = $this->input->post('mechanic');
            $mechanic_charge = $this->input->post('mechanic_charge');
            $num_mech = $this->input->post('mechanic') ? count($this->input->post('mechanic')) : 0;
            $invoice_details['mechanics'] = array();
            for ($i = 0; $i < $num_mech; $i++) {
                $invoice_details['mechanics'][$mechanics[$i]] = $mechanic_charge[$i];
            }

            $invoice_details['mechanics'] = serialize($invoice_details['mechanics']);
            if ($invoice_details['status'] == 'paid') {
                $date_payment = $this->shared_model->Lookup('invoices', 'date_payment', array('invoice_id' => $invoice_id));
                if (!intval($date_payment)) {
                    $invoice_details['date_payment'] = date('Y-m-d h:i:s');
                }
            }

            $success = $this->shared_model->update('invoices', 'invoice_id', $invoice_id, $invoice_details);

            if ($draft) {
                if ($success) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    echo $invoice_id;
                }
            } else {
                if ($success) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    $invoice = $this->shared_model->getRow('select * from invoices where invoice_id="' . $invoice_id . '"');
                    $customer_payments = array('customer_id' => $invoice->customer_id, 'amount' => $invoice_details['total'], 'date' => date('Y-m-d h:i:s'), 'status' => $invoice->status);
                    $this->shared_model->update('customer_payments', 'invoice_id', $invoice_id, $customer_payments);
                    log_db_query($this->db->last_query());  // Log DB Query
                    $this->session->set_flashdata('success', 'Entry is successfully saved.');
                    redirect('retail/invoices/#' . $invoice_id);
                } else {
                    $this->session->set_flashdata('error', 'An error occured. Please try again.');
                    redirect('retail/create_invoice/' . $invoice_id);
                }
            }
        }
		
		public function exportDOReport($active = 'N') {
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="downloadDO.csv"');
            $DOs = $this->retail_model->getVoidDOs($active);
            $DOs = json_decode($DOs, true);

            foreach ($DOs['aaData'] as $key => $value) {
                $DOs['aaData'][$key][1] = format_date($value[1]);
                $DOs['aaData'][$key][3] = format_price($value[3]);
                $DOs['aaData'][$key][4] = format_price($value[4]);
                //$DOs['aaData'][$key][6] = format_price($value[4] - $value[5] - $value[6]);
            }

            $fields = array(0 => 'ID', 1 => 'date', 2 => 'customer', 3 => 'total amount', 4 => 'deposit', 5 => 'status');
            echo implode(',', $fields) . "\n";
            foreach ($DOs['aaData'] as $key => $value) {
                foreach ($fields as $key1 => $field) {
                    echo str_replace(',', '', $value[$key1]) . ',';
                }
                echo "\n";
            }
            exit;
        }
		
		public function exportCSReport($active = 'N') {
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="downloadCS.csv"');
            $CS = $this->retail_model->getVoidCS($active);
            $CS = json_decode($CS, true);

            foreach ($CS['aaData'] as $key => $value) {
                $CS['aaData'][$key][1] = format_date($value[1]);
                $CS['aaData'][$key][3] = format_price($value[3]);
                $CS['aaData'][$key][4] = format_price($value[4]);
                //$DOs['aaData'][$key][6] = format_price($value[4] - $value[5] - $value[6]);
            }

            $fields = array(0 => 'ID', 1 => 'date', 2 => 'customer', 3 => 'total amount', 4 => 'discount', 5 => 'remark');
            echo implode(',', $fields) . "\n";
            foreach ($CS['aaData'] as $key => $value) {
                foreach ($fields as $key1 => $field) {
                    echo str_replace(',', '', $value[$key1]) . ',';
                }
                echo "\n";
            }
            exit;
        }
		
		
        public function getRetailItemsTable() {
            $this->load->library('datatables');
            $stock = $this->retail_model->getRetailItemsTable();
            $stock = json_decode($stock, true);

            foreach ($stock['aaData'] as $key => $res) {
                $stock['aaData'][$key]['10'] = format_price($res['10']);
                $stock['aaData'][$key]['9'] = format_price($res['9']);
            }

            $stock = $this->shared_model->JEncode($stock);
            echo $stock;
        }

        public function selectInvoiceItem() {
            $item_id = $this->input->post('item_id');
            $item_details = $this->retail_model->getItemDetails($item_id);
            // if ($item_details->qty <= 0) {
                // echo 'sold_out';
                // exit;
            // }
            $prices = '<select id="customer_prices">
						<option value="' . format_number($item_details->price1) . '">Price1</option>
						<option value="' . format_number($item_details->price2) . '">Price2</option>
						<option value="' . format_number($item_details->price3) . '">Price3</option>
						<option value="' . format_number($item_details->price4) . '">Price4</option>
					</select>';
            $response = '<tr class="text-center" id="item_' . $item_details->id . '">
					<td>' . $item_details->stock_num . '
						<input type="hidden" value="' . $item_id . '" id="itemid_' . $item_id . '" name="item_id[]"/>
					</td>
					<td>' . $item_details->description . '
						<input type="hidden" value="' . $item_details->description . '" id="desc_' . $item_details->id . '" name="desc[]"/>
					<td>' . $prices . '
						<input type="hidden" value="' . format_number($item_details->price1) . '" id="price_' . $item_details->id . '" name="price[]"/>
					</td>
					<td><input class="form-control inputsmall" type="text" id="quantity_' . $item_details->id . '" value="1" name="quantity[]"/></td>
					<td><input class="form-control inputsmall" type="text" id="discount_' . $item_details->id . '" name="item_discount[]" value="0"/></td>
					<!---<td>
						<input class="form-control inputsmall" type="hidden" id="discountvalue_' . $item_details->id . '" name="discount_value[]" value="0"/>
						 <input class="form-control inputsmall" type="text" id="markup_' . $item_details->id . '" name="markup[]" value="0"/>
					// </td>--->
					<td><span id="linetotal_' . $item_details->id . '">' . format_number($item_details->price1) . '</span>
						<input type="hidden" value="' . format_number($item_details->price1) . '" id="linetotalval_' . $item_details->id . '"/>
					</td>
					<td><a class="btn btn-xs btn-danger deleteitem" id="deleteitem_' . $item_details->id . '" role="button">
			                <i class="fa fa-times delete"></i>
			            </a>
		            </td>
				   </tr>';
            echo $response;
        }
		
		public function selectInvoiceItemCN() {
            $item_id = $this->input->post('item_id');
            $item_details = $this->retail_model->getItemDetails($item_id);           
            $prices = '<select id="customer_prices">
						<option value="' . format_number($item_details->price1) . '">Price1</option>
						<option value="' . format_number($item_details->price2) . '">Price2</option>
						<option value="' . format_number($item_details->price3) . '">Price3</option>
						<option value="' . format_number($item_details->price4) . '">Price4</option>
					</select>';
            $response = '<tr class="text-center" id="item_' . $item_details->id . '">
					<td>' . $item_details->stock_num . '
						<input type="hidden" value="' . $item_id . '" id="itemid_' . $item_id . '" name="item_id[]"/>
					</td>
					<td>' . $prices . '
						<input type="hidden" value="' . format_number($item_details->price1) . '" id="price_' . $item_details->id . '" name="price[]"/>
					</td>
					<td><input class="form-control inputsmall" type="text" id="quantity_' . $item_details->id . '" value="1" name="quantity[]"/></td>
					<td><input class="form-control inputsmall" type="text" id="discount_' . $item_details->id . '" name="item_discount[]" value="0"/></td>
					<td>
						<input class="form-control inputsmall" type="hidden" id="discountvalue_' . $item_details->id . '" name="discount_value[]" value="0"/>
						<input class="form-control inputsmall" type="text" id="markup_' . $item_details->id . '" name="markup[]" value="0"/>
					</td>
					<td><span id="linetotal_' . $item_details->id . '">' . format_number($item_details->price1) . '</span>
						<input type="hidden" value="' . format_number($item_details->price1) . '" id="linetotalval_' . $item_details->id . '"/>
					</td>
					<td><a class="btn btn-xs btn-danger deleteitem" id="deleteitem_' . $item_details->id . '" role="button">
			                <i class="fa fa-times delete"></i>
			            </a>
		            </td>
				   </tr>';
            echo $response;
        }

        public function delete_invoice($id) {
            if ($id) {
                $this->db->trans_start();
                $success = $this->shared_model->update('invoices', 'invoice_id', $id, array('active' => 'N', 'draft' => 'Y'));
                $csInfo = $this->shared_model->getRow('select * from invoices where invoice_id="' . $id . '"', true);
                $iitems = $this->shared_model->getQuery('select * from i_items where invoice_id="' . $id . '"', true);
                foreach ($iitems as $item) {
                    if ($item['returned'] == 'Y') {
                        updateOutletQty($item, $item['item_id'], $this->session->userdata('outlet_id'), 'out');
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item['quantity'] . '","' . $id . '","Invoice Return Delete","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');
                        log_db_query($this->db->last_query());  // Log DB Query
                    } else if ($item['returned'] == 'N') {
                        updateOutletQty($item, $item['item_id'], $this->session->userdata('outlet_id'), 'in');
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item['quantity'] . '","' . $id . '","Invoice Delete","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');
                        log_db_query($this->db->last_query());  // Log DB Query
                    }
                }
                $this->db->trans_complete();
                if ($success) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    $this->session->set_flashdata('success', 'Entry is successfully deleted.');
                } else {
                    $this->session->set_flashdata('error', 'An error occurred. Please try again.');
                }
            }
            redirect('retail/invoices');
        }

        public function deleted_invoices() {
            $data = $this->data;
            $data['headline'] = 'Deleted Invoices';
            $data['subactive'] = 'invoices';
            $data['isactive'] = 'N';
            $data['breadcrumbs'] = array('Retail' => '#', 'Deleted Invoices' => '#');
            $this->load->view('retail/invoices', $data);
        }

        public function retrieve_invoice($id) {
            if ($id) {
                $success = $this->shared_model->update('invoices', 'invoice_id', $id, array('active' => 'Y'));
                $this->db->trans_start();
                $iitems = $this->shared_model->getQuery('select * from i_items where invoice_id="' . $id . '"', true);
                foreach ($iitems as $item) {
                    if ($item['returned'] == 'Y') {
                        updateOutletQty($item, $item['item_id'], $this->session->userdata('outlet_id'), 'in');
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item['quantity'] . '","' . $id . '","Invoice Return Retrieve","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');
                    } else if ($item['returned'] == 'N') {
                        updateOutletQty($item, $item['item_id'], $this->session->userdata('outlet_id'), 'out');
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item['quantity'] . '","' . $id . '","Invoice Retrieve","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');
                    }
                }
                $this->db->trans_complete();
                if ($success) {
                    $this->session->set_flashdata('success', 'Entry is successfully retrieved.');
                    redirect('retail/invoices');
                } else {
                    $this->session->set_flashdata('error', 'An error occurred. Please try again.');
                    redirect('retail/deleted_invoices');
                }
            }
            redirect('retail/invoices');
        }

        public function return_invoice($id) {
            $data = $this->data;
            $data['headline'] = 'Return Invoice Items';
            $data['subactive'] = 'invoices';
            $data['breadcrumbs'] = array('Retail' => '#', 'Return Invoice Items' => '#');
            if ($id) {
                $data['invoice_details'] = $this->retail_model->invoiceDetails($id);
                $data['i_items'] = $this->retail_model->getInvoiceItems($id);
                $data['returned_items'] = $this->retail_model->getInvoiceItems($id, 'Y');
            }
            $this->load->view('retail/return_invoice', $data);
        }

        public function return_iitem() {
            $id = $this->input->post('id');
            $quantity = $this->input->post('quantity');
            $item_details = $this->shared_model->getRow('select * from i_items where id="' . $id . '"', true);
            $returned_items = $this->shared_model->getRow('select sum(quantity) as s from i_items where item_id="' . $item_details['item_id'] . '" and returned="Y"');
            //if (($item_details['quantity'] - $returned_items->s) >= $quantity) {
                $item_details['id'] = null;
                $item_details['quantity'] = $quantity;
                $item_details['returned'] = 'Y';
                $item_details['date_returned'] = date('Y-m-d H:i:s');
                $this->shared_model->insert('i_items', $item_details);
                log_db_query($this->db->last_query());  // Log DB Query
                $returned_id = mysql_insert_id();				
                updateOutletQty($item_details, $item_details['item_id'], $this->session->userdata('outlet_id'), 'in');
				$new_balance =$this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_details['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
				$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item_details['item_id'] . '","' . $quantity . '","' . $returned_id . '","Invoice Return Item","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');
                log_db_query($this->db->last_query());  // Log DB Query
                $total = $this->retail_model->update_total('invoices', 'i_items', 'invoice_id', $item_details['invoice_id']);
                log_db_query($this->db->last_query());  // Log DB Query
                $arr['type'] = 'success';
                $arr['msg'] = format_price($total);
                $all_item_details = $this->retail_model->iItemDetails($returned_id);
                $arr['returned'] = '<tr class="text-center" id="item_' . $all_item_details->iitem_id . '">
				                  <td>' . $all_item_details->stock_num . '</td>
				                  <td>' . $all_item_details->description . '</td>
				                  <td>' . format_number($all_item_details->price) . '</td>
				                  <td>' . $all_item_details->quantity . '</td>
				                  <td>' . $all_item_details->discount . '</td>
				                  <td>' . $all_item_details->discount_value . '</td>
				                  <td id="linetotal_' . $all_item_details->iitem_id . '">' . format_number((($all_item_details->price * (1 - $all_item_details->discount / 100)) - $all_item_details->discount_value) * $all_item_details->quantity) . '</td>
				                  <td>' . format_date($all_item_details->date_returned) . '</td>
				                  <td><a class="btn btn-xs btn-success" id="undoreturn_' . $all_item_details->iitem_id . '" role="button">
				                        <i class="fa fa-undo"></i>
				                      </a>
				                  </td>
				                </tr>';
            /*} else {
                $arr['type'] = 'error';
                $arr['msg'] = 'Wrong value for quantity! The number can\'t be higher than ' . ($item_details['quantity'] - $returned_items->s) . '.';
            }*/
            echo $this->shared_model->JEncode($arr);
        }

        public function undoreturn_iitem() {
            $id = $this->input->post('id');
            $item_details = $this->shared_model->getRow('select * from i_items where id="' . $id . '"', true);
            $this->shared_model->delete('i_items', 'id', $id);
            log_db_query($this->db->last_query());  // Log DB Query
			
            updateOutletQty($item_details, $item_details['item_id'], $this->session->userdata('outlet_id'), 'out');
			$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_details['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
			$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item_details['item_id'] . '","' . $item_details['quantity'] . '","' . $id . '","Invoice Return Undo","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');
            $total = $this->retail_model->update_total('invoices', 'i_items', 'invoice_id', $item_details['invoice_id']);
            log_db_query($this->db->last_query());  // Log DB Query
            echo format_price($total);
        }

        public function cash_sales() {
            $data = $this->data;
            $data['headline'] = 'Cash Sales';
            $data['subactive'] = 'cs';
            $data['breadcrumbs'] = array('Retail' => '#', 'Cash Sales' => '#');
            $this->load->view('retail/cash_sales', $data);
        }

        public function getCSs($active = 'Y') {
            $cash_sales = $this->retail_model->getCSs($active);
            $cash_sales = json_decode($cash_sales, true);

            foreach ($cash_sales['aaData'] as $key => $value) {
                $cash_sales['aaData'][$key][1] = format_date($value[1]);
                $cash_sales['aaData'][$key][2] = format_price($value[2]);
            }

            $cash_sales = $this->shared_model->JEncode($cash_sales);
            echo $cash_sales;
        }

        public function create_cs($cs_id = false) {
            $data = $this->data;
            $data['headline'] = 'Create C.S';
            $data['subactive'] = 'cs';
            $data['breadcrumbs'] = array('Retail' => '#', 'Create C.S' => '#');
            $data['retail_items'] = $this->retail_model->getRetailItems();
            if ($cs_id) {
                $data['invoice_details'] = $this->retail_model->csDetails($cs_id);
                $data['i_items'] = $this->retail_model->getCSItems($cs_id);
            }
            $this->load->view('retail/add_cs', $data);
        }

        public function save_cs($cs_id = false) {
            $cs_details = $this->shared_model->getPost('cash_sales');

            if ($this->input->post('draft') == 'draft' && !$this->input->post('save')) {
                $draft = 1;
                $cs_details['draft'] = 'Y';
            } else {
                $draft = 0;
                $cs_details['draft'] = 'N';
            }
            $num = $this->input->post('item_id') > 0 ? count($this->input->post('item_id')) : 0;

            if (count($cs_details) <= 3 && !$num) {
                exit;
            }

            if ($cs_id) {
                $saved = $this->shared_model->Lookup('cash_sales', 'cs_id', array('cs_id' => $cs_id, 'draft' => 'N'));
            } else {
                $saved = false;
            }
            if (!$cs_id) {
                $cs_details['cs_time_stamp'] = date('Y-m-d h:i:s');
                $cs_details['user_id'] = $this->session->userdata('user_id');
                $this->shared_model->insert('cash_sales', $cs_details);
                log_db_query($this->db->last_query());  // Log DB Query
                $cs_id = mysql_insert_id();
            }

            $item_ids = $this->input->post('item_id');
            $qtys = $this->input->post('quantity');
            $discounts = $this->input->post('item_discount');
            $discount_vals = $this->input->post('discount_value');
            $markup = $this->input->post('markup');
            $prices = $this->input->post('price');
            $total = 0;
            $subtotal = 0;
            if ($saved) {
            	/* edithing after save */
            	$existing_items=$this->shared_model->getQuery('SELECT * FROM cs_items WHERE cs_id='.$cs_id);
            	foreach ($existing_items as $key=>$value) {
            		$e_item=$this->shared_model->getRow('SELECT * FROM stock_outlets WHERE item_id='.$value->item_id.' AND outlet_id='.$this->session->userdata('outlet_id'));
            		$this->shared_model->execute('UPDATE stock_outlets SET qty='.($e_item->qty+$value->quantity).' WHERE item_id='.$value->item_id.' AND outlet_id='.$this->session->userdata('outlet_id'));
            	}
            	$this->shared_model->execute('DELETE FROM stock_activity WHERE reference_id = "' . $cs_id . '" AND reference LIKE "Cash Sale" AND outlet_id = ' . $this->session->userdata('outlet_id'));
            	$this->shared_model->execute('DELETE FROM cs_items WHERE cs_id='.$cs_id);


            	/**********************/
                $sql = 'select sum(quantity) as old_qty,item_id from cs_items where cs_id="' . $cs_id . '" group by item_id';
                $old_items = $this->shared_model->getQuery($sql);
                foreach ($old_items as $key => $value) {
                    $check_items[$value->item_id] = $value->old_qty;
                }
            }

            $this->shared_model->delete('cs_items', 'cs_id', $cs_id);
            log_db_query($this->db->last_query());  // Log DB Query
            for ($i = 0; $i < $num; $i++) {

                $item['item_id'] = $item_ids[$i];
                $item['cs_id'] = $cs_id;
                $item['quantity'] = $qtys[$i];
                $item['price'] = $prices[$i];
                $item['discount_value'] = $discount_vals[$i];
                $item['markup'] = $markup[$i];
                $item['discount'] = $discounts[$i];
                $inserted = $this->shared_model->insert('cs_items', $item);
                if ($inserted) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    $subtotal += $item['quantity'] * ($item['price'] * (1 - $item['discount'] / 100) - $item['discount_value'] + $item['markup']);
                    if (isset($check_items[$item['item_id']])) {
                        $item_qty['quantity'] = $item['quantity'] - $check_items[$item['item_id']];
                        unset($check_items[$item['item_id']]);
                    } else {
                        $item_qty['quantity'] = $item['quantity'];
                    }
                    if (!$draft) {
                        updateOutletQty($item_qty, $item['item_id'], $this->session->userdata('outlet_id'), 'out');
						log_db_query($this->db->last_query());  // Log DB Query
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						if ($this->shared_model->getRow('select activity_id from stock_activity where item_id=' . $item['item_id'] . ' AND reference_id = "' . $cs_id . '" AND reference = "Cash Sale" AND outlet_id = ' . $this->session->userdata('outlet_id'))) {
							if ($item_qty['quantity'] < 0) {
								$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . (0-$item_qty['quantity']) . '","' . $cs_id . '","Cash Sale Update Reduce Quantity","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")'); 
							} if ($item_qty['quantity'] > 0) {
								$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item_qty['quantity'] . '","' . $cs_id . '","Cash Sale Update Increase Quantity","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")'); 
							}
							//$this->shared_model->execute('update stock_activity SET quantity = "' . $item['quantity'] . '", balance = "' . $new_balance .'" WHERE item_id = "' . $item['item_id'] . '" AND reference_id = "' . $cs_id . '" AND reference = "Cash Sale" AND outlet_id = "' . $this->session->userdata('outlet_id') . '"');
						} else {				
							$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item['quantity'] . '","' . $cs_id . '","Cash Sale","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")'); 
						}                        
                    }
                }
            }
			
            if (!$draft && isset($check_items)) {
                foreach ($check_items as $key => $value) {
                    updateOutletQty(array('quantity' => $value), $key, $this->session->userdata('outlet_id'), 'in');
					$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $key . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
					$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $key . '","' . $value . '","' . $cs_id . '","Cash Sale Delete","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');  
                    log_db_query($this->db->last_query());  // Log DB Query
                }
            }

            if (!isset($cs_details['discount'])) {
                $cs_details['discount'] = 0;
            }
            $total = $subtotal * (1 - $cs_details['discount'] / 100);

            $cs_details['subtotal'] = format_number($subtotal);
            $cs_details['total'] = format_number($total);
            $cs_details['date'] = isset($cs_details['date']) ? date('Y-m-d', strtotime($cs_details['date'])) : date('Y-m-d');
            $cs_details['remark']=$this->input->post('remark');

            if ($this->input->post('publish')) {
                $cs_details['published'] = 1;
            }

            $success = $this->shared_model->update('cash_sales', 'cs_id', $cs_id, $cs_details);

            if ($draft) {
                if ($success) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    echo $cs_id;
                }
            } else {
                if ($success) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    $this->session->set_flashdata('success', 'Entry is successfully saved.');
                    redirect('retail/cash_sales/#' . $cs_id);
                } else {
                    $this->session->set_flashdata('error', 'An error occured. Please try again.');
                    redirect('retail/create_cs/' . $cs_id);
                }
            }

        }

        public function delete_cs($id) {
            if ($id) {
                $this->db->trans_start();
                $success = $this->shared_model->update('cash_sales', 'cs_id', $id, array('active' => 'N', 'draft' => 'Y'));
                log_db_query($this->db->last_query());  // Log DB Query
                $csInfo = $this->shared_model->getRow('select * from cash_sales where cs_id="' . $id . '"', true);
                $iitems = $this->shared_model->getQuery('select * from cs_items where cs_id="' . $id . '"', true);
                foreach ($iitems as $item) {
                    if ($item['returned'] == 'Y') {
                        updateOutletQty($item, $item['item_id'], $this->session->userdata('outlet_id'), 'out');
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item['quantity'] . '","' . $id . '","Cash Sale Return Delete","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');
                        log_db_query($this->db->last_query());  // Log DB Query
                    } else if ($item['returned'] == 'N') {
                        updateOutletQty($item, $item['item_id'], $this->session->userdata('outlet_id'), 'in');
                        log_db_query($this->db->last_query());  // Log DB Query
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item['quantity'] . '","' . $id . '","Cash Sale Delete","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');  
                    }
                }
                $this->db->trans_complete();
                if ($success) {

                    $this->session->set_flashdata('success', 'Entry is successfully deleted.');
                } else {
                    $this->session->set_flashdata('error', 'An error occurred. Please try again.');
                }
            }
            redirect('retail/cash_sales');
        }

        public function deleted_cs() {
            $data = $this->data;
            $data['headline'] = 'Deleted Cash Sales';
            $data['subactive'] = 'cs';
            $data['isactive'] = 'N';
            $data['breadcrumbs'] = array('Retail' => '#', 'Deleted Cash Sales' => '#');
            $this->load->view('retail/cash_sales', $data);
        }

        public function retrieve_cs($id) {
            if ($id) {
                $success = $this->shared_model->update('cash_sales', 'cs_id', $id, array('active' => 'Y'));
                log_db_query($this->db->last_query());  // Log DB Query
                $this->db->trans_start();
                $iitems = $this->shared_model->getQuery('select * from cs_items where cs_id="' . $id . '"', true);
                foreach ($iitems as $item) {
                    if ($item['returned'] == 'Y') {
						//$itm[qty] is there 
                        updateOutletQty($item, $item['item_id'], $this->session->userdata('outlet_id'), 'in');
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item['quantity'] . '","' . $id . '","Cash Sale Return Retrieve","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');                                      
                        log_db_query($this->db->last_query());  // Log DB Query
                    } else if ($item['returned'] == 'N') {
                        updateOutletQty($item, $item['item_id'], $this->session->userdata('outlet_id'), 'out');
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item['item_id'] . '","' . $item['quantity'] . '","' . $id . '","Cash Sale Retrieve","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');					
						
                        log_db_query($this->db->last_query());  // Log DB Query
                    }
                }
                $this->db->trans_complete();
                if ($success) {
                    $this->session->set_flashdata('success', 'Entry is successfully retrieved.');
                    redirect('retail/cash_sales');
                } else {
                    $this->session->set_flashdata('error', 'An error occurred. Please try again.');
                    redirect('retail/deleted_cs');
                }
            }
            redirect('retail/cash_sales');
        }

        public function return_cs($id) {
            $data = $this->data;
            $data['headline'] = 'Return Cash Sale Items';
            $data['subactive'] = 'cs';
            $data['breadcrumbs'] = array('Retail' => '#', 'Return Cash Sale Items' => '#');
            if ($id) {
                $data['invoice_details'] = $this->retail_model->csDetails($id);
                $data['i_items'] = $this->retail_model->getCSItems($id);
                $data['returned_items'] = $this->retail_model->getCSItems($id, 'Y');
            }
            $this->load->view('retail/return_cs', $data);
        }

        public function return_csitem() {
            $id = $this->input->post('id');
            $quantity = $this->input->post('quantity');
            $item_details = $this->shared_model->getRow('select * from cs_items where id="' . $id . '"', true);
            $returned_items = $this->shared_model->getRow('select sum(quantity) as s from cs_items where item_id="' . $item_details['item_id'] . '" and returned="Y"');
            //if (($item_details['quantity'] - $returned_items->s) >= $quantity) {
                $item_details['id'] = null;
                $item_details['quantity'] = $quantity;
                $item_details['returned'] = 'Y';
                $item_details['date_returned'] = date('Y-m-d H:i:s');
                $this->shared_model->insert('cs_items', $item_details);
                $returned_id = mysql_insert_id();
				
                updateOutletQty($item_details, $item_details['item_id'], $this->session->userdata('outlet_id'), 'in');
				$new_balance =$this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_details['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
				$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item_details['item_id'] . '","' . $quantity . '","' . $returned_id . '","Cash Sale Return Item","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');
				
                $total = $this->retail_model->update_total('cash_sales', 'cs_items', 'cs_id', $item_details['cs_id']);
                $arr['type'] = 'success';
                $arr['msg'] = format_price($total);
                $all_item_details = $this->retail_model->csItemDetails($returned_id);
                $arr['returned'] = '<tr class="text-center" id="item_' . $all_item_details->iitem_id . '">
				                  <td>' . $all_item_details->stock_num . '</td>
				                  <td>' . $all_item_details->description . '</td>
				                  <td>' . format_number($all_item_details->price) . '</td>
				                  <td>' . $all_item_details->quantity . '</td>
				                  <td>' . $all_item_details->discount . '</td>
				                  <td>' . $all_item_details->discount_value . '</td>
				                  <td id="linetotal_' . $all_item_details->iitem_id . '">' . format_number((($all_item_details->price * (1 - $all_item_details->discount / 100)) - $all_item_details->discount_value) * $all_item_details->quantity) . '</td>
				                  <td>' . format_date($all_item_details->date_returned) . '</td>
				                  <td><a class="btn btn-xs btn-success" id="undoreturn_' . $all_item_details->iitem_id . '" role="button">
				                        <i class="fa fa-undo"></i>
				                      </a>
				                  </td>
				                </tr>';
            /*} else {
                $arr['type'] = 'error';
                $arr['msg'] = 'Wrong value for quantity! The number can\'t be higher than ' . ($item_details['quantity'] - $returned_items->s) . '.';
            }*/
            echo $this->shared_model->JEncode($arr);
        }

        public function undoreturn_csitem() {
            $id = $this->input->post('id');
            $item_details = $this->shared_model->getRow('select * from cs_items where id="' . $id . '"', true);
            $this->shared_model->delete('cs_items', 'id', $id);
			
            updateOutletQty($item_details, $item_details['item_id'], $this->session->userdata('outlet_id'), 'out');
			$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_details['item_id'] . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
			$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item_details['item_id'] . '","' . $item_details['quantity'] . '","' . $id . '","Cash Sale Return Undo","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');
			
            log_db_query($this->db->last_query());  // Log DB Query
            $total = $this->retail_model->update_total('cash_sales', 'cs_items', 'cs_id', $item_details['cs_id']);
            log_db_query($this->db->last_query());  // Log DB Query
            echo format_price($total);
        }

        public function sales_orders() {
            $data = $this->data;
            $data['headline'] = 'Sales Orders';
            $data['subactive'] = 'so';
            $data['breadcrumbs'] = array('Retail' => '#', 'Sales Orders' => '#');
            $this->load->view('retail/sales_orders', $data);
        }

        public function getSOs($active = 'Y') {
            $sales_orders = $this->retail_model->getSOs($active);
            $sales_orders = json_decode($sales_orders, true);

            foreach ($sales_orders['aaData'] as $key => $value) {
                $sales_orders['aaData'][$key][1] = format_date($value[1]);
                $sales_orders['aaData'][$key][3] = format_price($value[3]);
                $sales_orders['aaData'][$key][4] = format_price($value[4]);
                $sales_orders['aaData'][$key][5] = format_price($value[3] - $value[4]);
            }

            $sales_orders = $this->shared_model->JEncode($sales_orders);
            echo $sales_orders;
        }

        public function discard_cs($cs_id) {
            $reason = $this->input->post('reason');
            $this->shared_model->update('cash_sales', 'cs_id', $cs_id, array('discard_reason' => $reason, 'active' => 'N', 'draft' => 'Y'));
            log_db_query($this->db->last_query());  // Log DB Query
            $this->session->set_flashdata('success', 'Retail Sale is successfully marked as void.');
            redirect('retail/draft_transactions');
        }

        public function discard_so($so_id) {
            $reason = $this->input->post('reason');
            $this->shared_model->update('sales_orders', 'so_id', $so_id, array('discard_reason' => $reason, 'active' => 'N', 'draft' => 'Y'));
            log_db_query($this->db->last_query());  // Log DB Query
            $this->session->set_flashdata('success', 'Sales order is successfully marked as void.');
            redirect('retail/draft_transactions');
        }

        public function discard_po($po_id) {
            $reason = $this->input->post('reason');
            $this->shared_model->update('purchase_orders', 'po_id', $po_id, array('discard_reason' => $reason, 'active' => 'N', 'draft' => 'Y'));
            log_db_query($this->db->last_query());  // Log DB Query
            $this->session->set_flashdata('success', 'Purchase order is successfully marked as void.');
            redirect('purchasing/draft_transactions');
        }

        public function discard_pi($pi_id) {
            $reason = $this->input->post('reason');
            $this->shared_model->update('purchase_invoices', 'pi_id', $pi_id, array('discard_reason' => $reason, 'active' => 'N', 'draft' => 'Y'));
            log_db_query($this->db->last_query());  // Log DB Query
            $this->session->set_flashdata('success', 'Purchase order is successfully marked as void.');
            redirect('purchasing/draft_transactions');
        }


        public function create_so($so_id = false) {
            $data = $this->data;
            $data['headline'] = 'Create S.O';
            $data['subactive'] = 'so';
            $data['breadcrumbs'] = array('Retail' => '#', 'Create S.O' => '#');
            $data['retail_items'] = $this->retail_model->getRetailItems();
            if ($so_id) {
                $data['invoice_details'] = $this->retail_model->soDetails($so_id);
                $data['mechanics'] = isset($data['invoice_details']->mechanics) ? unserialize($data['invoice_details']->mechanics) : array();
                $data['i_items'] = $this->retail_model->getSOItems($so_id);
            }
            $this->load->view('retail/add_so', $data);
        }

        public function save_so($so_id = false) {
            $so_details = $this->shared_model->getPost('sales_orders');

            if ($this->input->post('draft') == 'draft' && !$this->input->post('save') && !$this->input->post('publish')) {
                $draft = 1;
                $so_details['draft'] = 'Y';
            } else {
                $draft = 0;
                $so_details['draft'] = 'N';
            }

            $num = $this->input->post('item_id') > 0 ? count($this->input->post('item_id')) : 0;

            if (count($so_details) <= 3 && !$num) {
                exit;
            }

            if (!$so_id) {
                $so_details['so_time_stamp'] = date('Y-m-d h:i:s');
                $so_details['user_id'] = $this->session->userdata('user_id');
                $this->shared_model->insert('sales_orders', $so_details);
                log_db_query($this->db->last_query());  // Log DB Query
                $so_id = mysql_insert_id();
            }
            $sale_order = $this->shared_model->getRow('select * from sales_orders where so_id="' . $so_id . '"');
            $cs_id = $sale_order->cs_id;
            $invoice_id = $sale_order->invoice_id;
            if ($this->input->post('publish') && !isset($cs_id) && !isset($invoice_id)) {
                $so_details['published'] = 1;
                $so_details['discount'] = isset($so_details['discount']) ? $so_details['discount'] : 0;
                $so_details['deposit'] = isset($so_details['deposit']) ? $so_details['deposit'] : 0;
                if ($this->input->post('payment_method') == 'cs') {
                    $cs_details = array('cs_time_stamp' => date('Y-m-d H:i:s'), 'date' => date('Y-m-d'), 'customer_id' => $so_details['customer_id'], 'discount' => $so_details['discount'], 'draft' => 'Y');
                    $cs_details['user_id'] = $this->session->userdata('user_id');
                    $this->shared_model->insert('cash_sales', $cs_details);
                    log_db_query($this->db->last_query());  // Log DB Query
                    $cs_id = mysql_insert_id();
                    $so_details['cs_id'] = $cs_id;
                } else if ($this->input->post('payment_method') == 'invoice') {
                    $invoice_details = array('invoice_time_stamp' => date('Y-m-d H:i:s'), 'date_issue' => date('Y-m-d'), 'customer_id' => $so_details['customer_id'], 'deposit' => $so_details['deposit'], 'discount' => $so_details['discount'], 'draft' => 'Y');
                    $invoice_details['user_id'] = $this->session->userdata('user_id');
                    $this->shared_model->insert('invoices', $invoice_details);
                    log_db_query($this->db->last_query());  // Log DB Query
                    $invoice_id = mysql_insert_id();
                    $so_details['invoice_id'] = $invoice_id;
                }
            }

            $item_ids = $this->input->post('item_id');
            $qtys = $this->input->post('quantity');
            $discounts = $this->input->post('item_discount');
            $discount_vals = $this->input->post('discount_value');
            $markup = $this->input->post('markup');
            $prices = $this->input->post('price');
            $total = 0;
            $subtotal = 0;
            $this->shared_model->delete('so_items', 'so_id', $so_id);
            log_db_query($this->db->last_query());  // Log DB Query
            for ($i = 0; $i < $num; $i++) {

                $item['item_id'] = $item_ids[$i];
                $item['so_id'] = $so_id;
                $item['quantity'] = $qtys[$i];
                $item['price'] = $prices[$i];
                $item['discount_value'] = $discount_vals[$i];
                $item['markup'] = $markup[$i];
                $item['discount'] = $discounts[$i];
                $inserted = $this->shared_model->insert('so_items', $item);
                log_db_query($this->db->last_query());  // Log DB Query
                if ($this->input->post('publish')) {
                    $invoice_item = $item;
                    unset($invoice_item['so_id']);
                    if ($this->input->post('payment_method') == 'cs') {
                        $invoice_item['cs_id'] = $cs_id;
                        $this->shared_model->insert('cs_items', $invoice_item);
                        log_db_query($this->db->last_query());  // Log DB Query
                    } else if ($this->input->post('payment_method') == 'invoice') {
                        $invoice_item['invoice_id'] = $invoice_id;
                        $this->shared_model->insert('i_items', $invoice_item);
                        log_db_query($this->db->last_query());  // Log DB Query
                    }
                }
                if ($inserted) {
                    $subtotal += $item['quantity'] * ($item['price'] * (1 - $item['discount'] / 100) - $item['discount_value'] + $item['markup']);
                    //updateOutletQty($item,$item['item_id'],$this->session->userdata('outlet_id'),'out');
                }
            }

            if (!isset($so_details['discount'])) {
                $so_details['discount'] = 0;
            }

            $total = $subtotal * (1 - $so_details['discount'] / 100);

            $so_details['subtotal'] = format_number($subtotal);
            $so_details['total'] = format_number($total);
            $so_details['date'] = isset($so_details['date']) ? date('Y-m-d', strtotime($so_details['date'])) : date('Y-m-d');
            $mechanics = $this->input->post('mechanic');
            $mechanic_charge = $this->input->post('mechanic_charge');
            $num_mech = $mechanics ? count($this->input->post('mechanic')) : 0;
            $so_details['mechanics'] = array();
            for ($i = 0; $i < $num_mech; $i++) {
                $so_details['mechanics'][$mechanics[$i]] = $mechanic_charge[$i];
            }

            $so_details['mechanics'] = serialize($so_details['mechanics']);
            if (isset($invoice_details)) {
                $invoice_details['mechanics'] = $so_details['mechanics'];
            }
            if (isset($invoice_details['status']) && $invoice_details['status'] == 'paid') {
                $date_payment = $this->shared_model->Lookup('invoices', 'date_payment', array('invoice_id' => $invoice_id));
                if (!intval($date_payment)) {
                    $invoice_details['date_payment'] = date('Y-m-d h:i:s');
                }
            }

            if ($this->input->post('publish')) {
                $invoice_details['subtotal'] = format_number($subtotal);
                $invoice_details['total'] = format_number($total);
                if ($this->input->post('payment_method') == 'cs' && isset($cs_id)) {
                    $this->shared_model->update('cash_sales', 'cs_id', $cs_id, $invoice_details);
                    log_db_query($this->db->last_query());  // Log DB Query
                } else if ($this->input->post('payment_method') == 'invoice' && isset($invoice_id)) {
                    $this->shared_model->update('invoices', 'invoice_id', $invoice_id, $invoice_details);
                    log_db_query($this->db->last_query());  // Log DB Query
                }
            }

            $success = $this->shared_model->update('sales_orders', 'so_id', $so_id, $so_details);

            if ($draft) {
                if ($success) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    echo $so_id;
                }
            } else {
                if ($success) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    $this->session->set_flashdata('success', 'Entry is successfully saved.');
                    if ($this->input->post('payment_method') == 'cs' && isset($cs_id)) {
                        redirect('retail/create_cs/' . $cs_id);
                    } else if ($this->input->post('payment_method') == 'invoice' && isset($invoice_id)) {
                        redirect('retail/create_invoice/' . $invoice_id);
                    } else {
                        redirect('retail/sales_orders/#' . $so_id);
                    }
                } else {
                    $this->session->set_flashdata('error', 'An error occured. Please try again.');
                    redirect('retail/create_so/' . $so_id);
                }
            }


        }

        public function deleted_so() {
            $data = $this->data;
            $data['headline'] = 'Deleted Sales Orders';
            $data['subactive'] = 'so';
            $data['isactive'] = 'N';
            $data['breadcrumbs'] = array('Retail' => '#', 'Deleted Sales Orders' => '#');
            $this->load->view('retail/sales_orders', $data);
        }

        public function retrieve_so($id) {
            if ($id) {
                $success = $this->shared_model->update('sales_orders', 'so_id', $id, array('active' => 'Y'));
                if ($success) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    $this->session->set_flashdata('success', 'Entry is successfully retrieved.');
                    redirect('retail/sales_orders');
                } else {
                    $this->session->set_flashdata('error', 'An error occurred. Please try again.');
                    redirect('retail/deleted_so');
                }
            }
            redirect('retail/sales_orders');
        }

        public function return_so($id) {
            $data = $this->data;
            $data['headline'] = 'Return Sales Order Items';
            $data['subactive'] = 'so';
            $data['breadcrumbs'] = array('Retail' => '#', 'Return Sales Order Items' => '#');
            if ($id) {
                $data['invoice_details'] = $this->retail_model->soDetails($id);
                $data['i_items'] = $this->retail_model->getSOItems($id);
                $data['returned_items'] = $this->retail_model->getSOItems($id, 'Y');
            }
            $this->load->view('retail/return_so', $data);
        }

        public function return_soitem() {
            $id = $this->input->post('id');
            $quantity = $this->input->post('quantity');
            $item_details = $this->shared_model->getRow('select * from so_items where id="' . $id . '"', true);
            $returned_items = $this->shared_model->getRow('select sum(quantity) as s from so_items where item_id="' . $item_details['item_id'] . '" and returned="Y"');
           // if (($item_details['quantity'] - $returned_items->s) >= $quantity) {
                $item_details['id'] = null;
                $item_details['quantity'] = $quantity;
                $item_details['returned'] = 'Y';
                $item_details['date_returned'] = date('Y-m-d H:i:s');
                $this->shared_model->insert('so_items', $item_details);
                log_db_query($this->db->last_query());  // Log DB Query
                $outlet_stock->quantity = $item['quantity'];
				
				updateOutletQty($outlet_stock, $item['item_id'], $this->session->userdata('outlet_id'), 'out');
                log_db_query($this->db->last_query());  // Log DB Query
                $returned_id = mysql_insert_id();
                updateOutletQty($item_details, $item['item_id'], $this->session->userdata('outlet_id'), 'in');
                log_db_query($this->db->last_query());  // Log DB Query
                $total = $this->retail_model->update_total('sales_orders', 'so_items', 'so_id', $item_details['so_id']);
                log_db_query($this->db->last_query());  // Log DB Query
                $arr['type'] = 'success';
                $arr['msg'] = format_price($total);
                $all_item_details = $this->retail_model->soItemDetails($returned_id);
                $arr['returned'] = '<tr class="text-center" id="item_' . $all_item_details->iitem_id . '">
				                  <td>' . $all_item_details->stock_num . '</td>
				                  <td>' . $all_item_details->description . '</td>
				                  <td>' . format_number($all_item_details->price) . '</td>
				                  <td>' . $all_item_details->quantity . '</td>
				                  <td>' . $all_item_details->discount . '</td>
				                  <td>' . $all_item_details->discount_value . '</td>
				                  <td id="linetotal_' . $all_item_details->iitem_id . '">' . format_number((($all_item_details->price * (1 - $all_item_details->discount / 100)) - $all_item_details->discount_value) * $all_item_details->quantity) . '</td>
				                  <td>' . format_date($all_item_details->date_returned) . '</td>
				                  <td><a class="btn btn-xs btn-success" id="undoreturn_' . $all_item_details->iitem_id . '" role="button">
				                        <i class="fa fa-undo"></i>
				                      </a>
				                  </td>
				                </tr>';
            /*} else {
                $arr['type'] = 'error';
                $arr['msg'] = 'Wrong value for quantity! The number can\'t be higher than ' . ($item_details['quantity'] - $returned_items->s) . '.';
            }*/
            echo $this->shared_model->JEncode($arr);
        }

        public function undoreturn_soitem() {
            $id = $this->input->post('id');
            $item_details = $this->shared_model->getRow('select * from so_items where id="' . $id . '"', true);
            $this->shared_model->delete('so_items', 'id', $id);
			
            updateOutletQty($item_details, $item['item_id'], $this->session->userdata('outlet_id'), 'out');
            log_db_query($this->db->last_query());  // Log DB Query
            $total = $this->retail_model->update_total('sales_orders', 'so_items', 'so_id', $item_details['so_id']);
            echo format_price($total);
        }

        public function delete_so($id) {
            if ($id) {
                $success = $this->shared_model->update('sales_orders', 'so_id', $id, array('active' => 'N'));
                if ($success) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    $this->session->set_flashdata('success', 'Entry is successfully deleted.');
                } else {
                    $this->session->set_flashdata('error', 'An error occurred. Please try again.');
                }
            }
            redirect('retail/sales_orders');
        }

        public function delivery_orders() {
            $data = $this->data;
            $data['headline'] = 'Delivery Orders';
            $data['subactive'] = 'do';
            $data['breadcrumbs'] = array('Retail' => '#', 'Delivery Orders' => '#');
            $this->load->view('retail/delivery_orders', $data);
        }

        public function getDOs($active = 'Y') {
            $delivery_orders = $this->retail_model->getDOs($active);
            $delivery_orders = json_decode($delivery_orders, true);

            foreach ($delivery_orders['aaData'] as $key => $value) {
                $delivery_orders['aaData'][$key][3] = format_price($value[3]);
            }

            $delivery_orders = $this->shared_model->JEncode($delivery_orders);
            echo $delivery_orders;
        }

        public function create_do($do_id = false) {
            $data = $this->data;
            $data['headline'] = 'Create D.O';
            $data['subactive'] = 'do';
            $data['breadcrumbs'] = array('Retail' => '#', 'Create D.O' => '#');
            $data['retail_items'] = $this->retail_model->getRetailItems();
            if ($do_id) {
                $data['invoice_details'] = $this->retail_model->doDetails($do_id);
                $data['i_items'] = $this->retail_model->getDOItems($do_id);
            }
            $this->load->view('retail/add_do', $data);
        }

        public function selectDOItem() {
            $item_id = $this->input->post('item_id');
            $item_details = $this->retail_model->getItemDetails($item_id);
            // if ($item_details->qty <= 0) {
            //     echo 'sold_out';
            //     exit;
            // }
            $response = '<tr class="text-center" id="item_' . $item_details->id . '">
					<td>' . $item_details->stock_num . '
						<input type="hidden" value="' . $item_id . '" id="itemid_' . $item_id . '" name="item_id[]"/>
					</td>
					<td>' . $item_details->description . '</td>
					<td><input class="form-control inputsmall" type="text" id="quantity_' . $item_details->id . '" value="1" name=quantity[]/></td>
					<td><a class="btn btn-xs btn-danger deleteitem" id="deleteitem_' . $item_details->id . '" role="button">
			                <i class="fa fa-times delete"></i>
			            </a>
		            </td>
				   </tr>';
            echo $response;
        }

        public function save_do($do_id = false) {
            $do_details = $this->shared_model->getPost('delivery_orders');

            if (!$do_id) {
                $do_details['do_time_stamp'] = date('Y-m-d h:i:s');
                $do_details['user_id'] = $this->session->userdata('user_id');
                $this->shared_model->insert('delivery_orders', $do_details);
                log_db_query($this->db->last_query());  // Log DB Query
                $do_id = mysql_insert_id();
            }

            $invoice_id = $this->shared_model->Lookup('delivery_orders', 'invoice_id', array('do_id' => $do_id));

            if ($this->input->post('publish') && !isset($invoice_id)) {
                $do_details['published'] = 1;
                /********************* added by punit to uncheck zero validation****************/
                if (!isset($do_details['deposit'])) {
                    $do_details['deposit'] = 0;
                }
                $invoice_details = array('invoice_time_stamp' => date('Y-m-d H:i:s'), 'date_issue' => date('Y-m-d'), 'customer_id' => $do_details['customer_id'], 'deposit' => $do_details['deposit']);
                $invoice_details['user_id'] = $this->session->userdata('user_id');
                $this->shared_model->insert('invoices', $invoice_details);
                log_db_query($this->db->last_query());  // Log DB Query
                $invoice_id = mysql_insert_id();
                $do_details['invoice_id'] = $invoice_id;
            }

            $num = count($this->input->post('item_id'));
            $item_ids = $this->input->post('item_id');
            $qtys = $this->input->post('quantity');
            $this->shared_model->delete('do_items', 'do_id', $do_id);
            log_db_query($this->db->last_query());  // Log DB Query
            $total = 0;
            for ($i = 0; $i < $num; $i++) {
                $item['item_id'] = $item_ids[$i];
                $item['do_id'] = $do_id;
                $item['quantity'] = $qtys[$i];
                $item_details = $this->shared_model->getRow('select * from stock_outlets where item_id="' . $item['item_id'] . '" and outlet_id="' . $this->session->userdata('outlet_id') . '"');
                $item['price'] = $item_details->sell_price;
                $this->shared_model->insert('do_items', $item);
                if ($this->input->post('publish')) {
                    $invoice_item = array('item_id' => $item['item_id'], 'price' => $item_details->sell_price, 'quantity' => $item['quantity'], 'invoice_id' => $invoice_id);
                    $inserted = $this->shared_model->insert('i_items', $invoice_item);
                    if ($inserted) {
                        log_db_query($this->db->last_query());  // Log DB Query
                        $total += $item_details->sell_price;
                    }
                }
            }

            if ($this->input->post('publish')) {
                $invoice_details['subtotal'] = $invoice_details['total'] = format_number($total);
                $this->shared_model->update('invoices', 'invoice_id', $invoice_id, $invoice_details);
                log_db_query($this->db->last_query());  // Log DB Query
            }

            $do_details['total'] = $total;
            $do_details['date'] = date('Y-m-d', strtotime($do_details['date']));

            $success = $this->shared_model->update('delivery_orders', 'do_id', $do_id, $do_details);

            if ($success) {
                log_db_query($this->db->last_query());  // Log DB Query
                $this->session->set_flashdata('success', 'Entry is successfully saved.');
                if ($this->input->post('publish')) {
                    redirect('retail/create_invoice/' . $invoice_id);
                } else {
                    redirect('retail/delivery_orders');
                }
            } else {
                $this->session->set_flashdata('error', 'An error occured. Please try again.');
                redirect('retail/create_do/' . $do_id);
            }

        }

        public function delete_do($id) {
            if ($id) {
                $success = $this->shared_model->update('delivery_orders', 'do_id', $id, array('active' => 'N'));
                if ($success) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    $this->session->set_flashdata('success', 'Entry is successfully deleted.');
                } else {
                    $this->session->set_flashdata('error', 'An error occurred. Please try again.');
                }
            }
            redirect('retail/delivery_orders');
        }

        public function deleted_do() {
            $data = $this->data;
            $data['headline'] = 'Deleted Delivery Orders';
            $data['subactive'] = 'do';
            $data['isactive'] = 'N';
            $data['breadcrumbs'] = array('Retail' => '#', 'Deleted Delivery Orders' => '#');
            $this->load->view('retail/delivery_orders', $data);
        }

        public function retrieve_do($id) {
            if ($id) {
                $success = $this->shared_model->update('delivery_orders', 'do_id', $id, array('active' => 'Y'));
                if ($success) {
                    $this->session->set_flashdata('success', 'Entry is successfully retrieved.');
                    redirect('retail/delivery_orders');
                } else {
                    $this->session->set_flashdata('error', 'An error occurred. Please try again.');
                    redirect('retail/deleted_do');
                }
            }
            redirect('retail/delivery_orders');
        }

        public function returned_goods() {
            $data = $this->data;
            $data['headline'] = 'Returned Goods';
            $data['subactive'] = 'returned';
            $data['breadcrumbs'] = array('Retail' => '#', 'Returned Goods' => '#');
            $this->load->view('retail/returned_items', $data);
        }

        public function getReturnedItems() {
            $returned_items = $this->retail_model->getReturnedItems();
            echo $returned_items;
        }

        public function draft_transactions() {
            $data = $this->data;
            $data['headline'] = 'Draft Transactions';
            $data['subactive'] = 'draft';
            $data['breadcrumbs'] = array('Retail' => '#', 'Draft Transactions' => '#');
            $this->load->view('retail/draft_transactions', $data);
        }

        public function getDraftTransactions() {
            $draft_transactions = $this->retail_model->getDraftTransactions();
            echo $draft_transactions;
        }

        public function void_transactions() {
            $data = $this->data;
            $data['headline'] = 'Void Transactions';
            $data['subactive'] = 'draft';
            $data['breadcrumbs'] = array('Retail' => '#', 'Void Transactions' => '#');
            $this->load->view('retail/void_transactions', $data);
        }

        public function getVoidTransactions() {
            $void_transactions = $this->retail_model->getVoidTransactions();
            echo $void_transactions;
        }

        public function exportVoidTrans() {
            //echo "asdfasd";exit;
            $void_transactions = $this->retail_model->getexpVoidTransactions();

            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="downloadVoid.csv"');

            $fields = array('reference_id' => 'reference_id', 'date' => 'date', 'customer' => 'customer', 'total' => 'total', 'type' => 'type', 'staff' => 'staff', 'discard_reason' => 'discard_reason');
            echo implode(',', $fields) . "\n";
            foreach ($void_transactions as $key => $value) {

                foreach ($fields as $key1 => $field) {
                    echo str_replace(',', '', $value->$key1) . ',';
                }
                echo "\n";
            }
            exit;
        }

        public function searchByStockNum($stock_num) {
            $data['item_details'] = $this->retail_model->searchByStockNum($stock_num);
            echo $this->shared_model->JEncode($data['item_details']);
        }

        public function searchByDescription() {
            $description = $this->input->post('description');
            if ($description != '') {

                $items = $this->retail_model->searchByDescription($description);
                if ($items) {
                    $data = '';
                    foreach ($items as $key => $item) {
                        $data .= '<tr data-selected="' . $item->item_id . '" class="searchdescopt" style="cursor:pointer">
							<td>' . $item->stock_num . '</td>
							<td>' . $item->barcode . '</td>
							<td>' . $item->brand . '</td>
							<td>' . $item->category . '</td>
							<td>' . $item->description . '</td>
							<td>' . $item->model_no . '</td>
							<td>' . $item->remark . '</td>
							</tr>';
                    }
                    echo $data;
                }
            }
        }

        public function searchByItemId() {
            $item_id = $this->input->post('selected');
            $data['item_details'] = $this->retail_model->searchByItemId($item_id);
            echo $this->shared_model->JEncode($data['item_details']);
        }

        public function searchStock() {
            $search = $this->input->post('search');
            $stock = $this->retail_model->getRetailItems($search);
            $result = '';
            foreach ($stock as $key => $value) {
                $result .= '<div class="col-md-4">
                		<a id="retailitem_' . $value->item_id . '" class="prettyPhoto[pp_gal]">';
                if ($value->image) {
                    $result .= '<img src="' . base_url() . 'uploads/' . $value->image . '" alt="' . $value->description . '">';
                } else {
                    $result .= '<div class="no-image">' . $value->description . '</div>';
                }
                $result .= '<span class="label label-default">' . $value->stock_num . '</span>
	                </a>
	              </div>';
            }
            echo $result;
        }

        /*********************************** added by punit for new return credit note functionality************************************/


        public function credit_notes($limit = false) {
            $data = $this->data;
            $data['headline'] = 'Credit Notes';
            $data['subactive'] = 'creditnotes';
            $data['breadcrumbs'] = array('Retail' => '#', 'Credit Notes' => '#');
            $this->load->view('retail/credit_notes', $data);
        }

        public function getCreditNotes() {
            $creditNotes = $this->retail_model->getCreditNotes();
            $creditNotes = json_decode($creditNotes, true);

            foreach ($creditNotes['aaData'] as $key => $value) {
                $creditNotes['aaData'][$key][1] = format_date($value[1]);
            }

            $creditNotes = $this->shared_model->JEncode($creditNotes);
            echo $creditNotes;
        }

        public function create_cn($invoice_id = false) {
			$data = $this->data;
            $data['headline'] = 'Create Credit Note';
            $data['subactive'] = 'Credit Notes';
            $data['breadcrumbs'] = array('Retail' => '#', 'Create Invoice' => '#');

            $invoice_id = $this->uri->segment(3);
            if( $cn_id != ''){
                $sql = 'SELECT * FROM returned_cn WHERE cn_id=' . $cn_id;
                $result = $this->shared_model->getRow($sql);
                $data['returned_items'] = $result;
            }
            $data['retail_items'] = $this->retail_model->getRetailItems();
            if ($msg == 'error') {
                $data['msg'] = 'Wrong value for quantity! The number can\'t be higher than ' . ($item_details['quantity'] - $returned_items->s) . '.';
            }
            if ($invoice_id) {
                $data['invoice_details'] = $this->retail_model->invoiceDetails($invoice_id);
                $data['mechanics'] = isset($data['invoice_details']->mechanics) ? unserialize($data['invoice_details']->mechanics) : array();
                $data['i_items'] = $this->retail_model->getInvoiceItems($invoice_id, 'Y');
            }
            $this->load->view('retail/add_cn', $data);
        }


        public function save_cn($invoice_id = false) {			
			
            $cn_id = $this->input->post('cn_id');
			/*if($cn_id == "")
			{
				$cn_id = $invoice_id;
			}*/
            $all_items = $this->input->post('item_id');
            $all_quantity = $this->input->post('quantity');
            $all_price = $this->input->post('price');
            $all_discount = $this->input->post('item_discount');
            $all_markup = $this->input->post('markup');
            $customer_id = $this->input->post('customer_id');
            $outlet_id = $this->session->userdata('outlet_id');
            $remark = $this->input->post('remark');
            $date = date('Y-m-d H:i:s');
			
			//echo "here";
			//exit;
			

            if (!$this->input->post('item_id') || $customer_id == '' ) {
                //redirect('retail/credit_notes');
                exit;
            }

            $returned_items = array();
            //$i = 0;
            for( $i = 0; $i < count($all_items); $i++ ) {
                $returned_items[$i]['item_id'] = $all_items[$i];
                $returned_items[$i]['price'] = $all_price[$i];
                $returned_items[$i]['discount'] = $all_discount[$i];
                $returned_items[$i]['vat'] = 18;
                $returned_items[$i]['qty'] = $all_quantity[$i];
                $returned_items[$i]['markup'] = $all_markup[$i];
            }

            //echo '<pre>'.print_r( $returned_items, true ).'</pre>';

            $data['customer_id'] = $customer_id;
            $data['cn_items'] = serialize($returned_items);
            $data['remarks'] = $remark;
            $data['cn_date'] = $date;
            $data['outlet_id'] = $outlet_id;
            $data['draft'] = 'N';

            if ($cn_id == '') {
                if ($this->input->post('save')) {
					//echo "inside save";
					//exit;
                    $this->shared_model->insert('returned_cn', $data);
                    log_db_query($this->db->last_query());  // Log DB Query
					if($invoice_id)
					{
						redirect('retail/credit_notes/#' . $invoice_id . '/cn');
					}
					else
					{
						$cn_id = mysql_insert_id();
						foreach ($all_items as $key => $item_id) 
						{
							$sql = 'SELECT * FROM stock_outlets WHERE item_id=' . $item_id . ' AND outlet_id=' . $outlet_id;
							$result = $this->shared_model->getRow($sql);
							if ($result) {						
								$num = $result->qty + $all_quantity[$key];
								updateOutletQty($all_quantity[$key], $item_id, $outlet_id, 'in');
								$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_id . ' AND outlet_id = ' . $outlet_id)->qty;
								$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item_id . '","' . $all_quantity[$key] . '","' . $cn_id . '","Credit Note","' . $new_balance . '","' . $outlet_id . '")');  
								log_db_query($this->db->last_query());  // Log DB Query
							}
						}
						redirect('retail/credit_notes/?added=' . $cn_id);
					}                    
                }
                $data['draft'] = 'Y';
                $this->shared_model->insert('returned_cn', $data);
                log_db_query($this->db->last_query());  // Log DB Query
                echo mysql_insert_id();
				//exit;
            }else{
				//echo "else";
                $this->shared_model->update('returned_cn', 'cn_id', $cn_id, $data);
                log_db_query($this->db->last_query());  // Log DB Query
                echo $cn_id;
            }

            if ($this->input->post('save')) {
				//echo "Im here";
				//exit;
                $this->shared_model->update('returned_cn', 'cn_id', $cn_id, $data);
                log_db_query($this->db->last_query());  // Log DB Query
                foreach ($all_items as $key => $item_id) {
                    $sql = 'SELECT * FROM stock_outlets WHERE item_id=' . $item_id . ' AND outlet_id=' . $outlet_id;
                    $result = $this->shared_model->getRow($sql);
                    if ($result) {						
                        $num = $result->qty + $all_quantity[$key];
                        $this->db->where('item_id', $item_id);
                        $this->db->where('outlet_id', $outlet_id);
                        $this->db->update('stock_outlets', array('qty' => $num));
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_id . ' AND outlet_id = ' . $outlet_id)->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item_id . '","' . $all_quantity[$key] . '","' . $cn_id . '","Credit Note","' . $new_balance . '","' . $outlet_id . '")');  
						log_db_query($this->db->last_query());  // Log DB Query
                    }
                }
                $this->session->set_flashdata('success', 'Entry is successfully saved.');
                redirect('retail/credit_notes/?added=' . $cn_id);
            }

        }

        public function create_cncs($cs_id = false, $msg = '') {
            $data['headline'] = 'Create C.S';
            $data['subactive'] = 'cs';
            $data['breadcrumbs'] = array('Retail' => '#', 'Create C.S' => '#');
			
			if( $cn_id != ''){
                $sql = 'SELECT * FROM returned_cn WHERE cn_id=' . $cn_id;
                $result = $this->shared_model->getRow($sql);
                $data['returned_items'] = $result;
            }
			
            $data['retail_items'] = $this->retail_model->getRetailItems();
            if ($msg == 'error') {
                $data['msg'] = 'Wrong value for quantity! The number can\'t be higher than ' . ($item_details['quantity'] - $returned_items->s) . '.';
            }			
            if ($cs_id) {
                $data['invoice_details'] = $this->retail_model->csDetails($cs_id);
                $data['submit_to'] = 'cs';
                $data['i_items'] = $this->retail_model->getCSItems($cs_id, 'Y');
            }
            $this->load->view('retail/add_cn', $data);
        }

        public function save_cncs($cs_id = false) {

            $cn_id = $this->input->post('cn_id');
			/*if($cn_id == "")
			{
				$cn_id = $invoice_id;
			}*/
            $all_items = $this->input->post('item_id');
            $all_quantity = $this->input->post('quantity');
            $all_price = $this->input->post('price');
            $all_discount = $this->input->post('item_discount');
            $all_markup = $this->input->post('markup');
            $customer_id = $this->input->post('customer_id');
            $outlet_id = $this->session->userdata('outlet_id');
            $remark = $this->input->post('remark');
            $date = date('Y-m-d H:i:s');
			
			//echo "here";
			//exit;
			

            if (!$this->input->post('item_id') || $customer_id == '' ) {
                //redirect('retail/credit_notes');
                exit;
            }

            $returned_items = array();
            //$i = 0;
            for( $i = 0; $i < count($all_items); $i++ ) {
                $returned_items[$i]['item_id'] = $all_items[$i];
                $returned_items[$i]['price'] = $all_price[$i];
                $returned_items[$i]['discount'] = $all_discount[$i];
                $returned_items[$i]['vat'] = 18;
                $returned_items[$i]['qty'] = $all_quantity[$i];
                $returned_items[$i]['markup'] = $all_markup[$i];
            }

            //echo '<pre>'.print_r( $returned_items, true ).'</pre>';

            $data['customer_id'] = $customer_id;
            $data['cn_items'] = serialize($returned_items);
            $data['remarks'] = $remark;
            $data['cn_date'] = $date;
            $data['outlet_id'] = $outlet_id;
            $data['draft'] = 'N';

            if ($cn_id == '') {
                if ($this->input->post('save')) {
					//echo "inside save";
					//exit;
                    $this->shared_model->insert('returned_cn', $data);
                    log_db_query($this->db->last_query());  // Log DB Query
					if($cs_id)
					{
						redirect('retail/credit_notes/#' . $cs_id . '/cn');
					}
					else
					{
						$cn_id = mysql_insert_id();
						foreach ($all_items as $key => $item_id) 
						{
							$sql = 'SELECT * FROM stock_outlets WHERE item_id=' . $item_id . ' AND outlet_id=' . $outlet_id;
							$result = $this->shared_model->getRow($sql);
							if ($result) {						
								$num = $result->qty + $all_quantity[$key];
								updateOutletQty($all_quantity[$key], $item_id, $outlet_id, 'in');
								$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_id . ' AND outlet_id = ' . $outlet_id)->qty;
								$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item_id . '","' . $all_quantity[$key] . '","' . $cn_id . '","Credit Note","' . $new_balance . '","' . $outlet_id . '")');  
								log_db_query($this->db->last_query());  // Log DB Query
							}
						}
						redirect('retail/credit_notes/?added=' . $cn_id);
					}                    
                }
                $data['draft'] = 'Y';
                $this->shared_model->insert('returned_cn', $data);
                log_db_query($this->db->last_query());  // Log DB Query
                echo mysql_insert_id();
				//exit;
            }else{
				//echo "else";
                $this->shared_model->update('returned_cn', 'cn_id', $cn_id, $data);
                log_db_query($this->db->last_query());  // Log DB Query
                echo $cn_id;
            }

            if ($this->input->post('save')) {
				//echo "Im here";
				//exit;
                $this->shared_model->update('returned_cn', 'cn_id', $cn_id, $data);
                log_db_query($this->db->last_query());  // Log DB Query
                foreach ($all_items as $key => $item_id) {
                    $sql = 'SELECT * FROM stock_outlets WHERE item_id=' . $item_id . ' AND outlet_id=' . $outlet_id;
                    $result = $this->shared_model->getRow($sql);
                    if ($result) {						
                        $num = $result->qty + $all_quantity[$key];
                        $this->db->where('item_id', $item_id);
                        $this->db->where('outlet_id', $outlet_id);
                        $this->db->update('stock_outlets', array('qty' => $num));
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_id . ' AND outlet_id = ' . $outlet_id)->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item_id . '","' . $all_quantity[$key] . '","' . $cn_id . '","Credit Note","' . $new_balance . '","' . $outlet_id . '")');  
						log_db_query($this->db->last_query());  // Log DB Query
                    }
                }
                $this->session->set_flashdata('success', 'Entry is successfully saved.');
                redirect('retail/credit_notes/?added=' . $cn_id);
            }
        }
		
		public function create_cncs_old($cs_id = false, $msg = '') {
            $data['headline'] = 'Create C.S';
            $data['subactive'] = 'cs';
            $data['breadcrumbs'] = array('Retail' => '#', 'Create C.S' => '#');
			
            $data['retail_items'] = $this->retail_model->getRetailItems();
            if ($msg == 'error') {
                $data['msg'] = 'Wrong value for quantity! The number can\'t be higher than ' . ($item_details['quantity'] - $returned_items->s) . '.';
            }			
            if ($cs_id) {
                $data['invoice_details'] = $this->retail_model->csDetails($cs_id);
                $data['submit_to'] = 'cs';
                $data['i_items'] = $this->retail_model->getCSItems($cs_id, 'Y');
            }
            $this->load->view('retail/add_cn', $data);
        }

        public function save_cncs_old($cs_id = false) {

            $cn_id = $cs_id;
            $all_quantity = $this->input->post('quantity');
            $total_qty = array_sum($quantity);

            if (!$this->input->post('item_id')) {
                redirect('retail/credit_notes');
                exit;
            }
            foreach ($this->input->post('item_id') as $key => $item_id) {
                $id = $item_id;
                $quantity = $all_quantity[$key];

                $item_details = $this->shared_model->getRow('select * from cs_items where item_id="' . $id . '" and returned="N" and cs_id="' . $cs_id . '"', true);

                $returned_items = $this->shared_model->getRow('select sum(quantity) as s from cs_items where item_id="' . $item_details['item_id'] . '" and returned="Y"');

                if ($returned_items->s != $quantity) {
                    if (($item_details['quantity'] - $returned_items->s) >= $quantity) {
                        $item_details['id'] = null;
                        $item_details['quantity'] = $quantity;
                        $item_details['returned'] = 'Y';
                        $item_details['date_returned'] = date('Y-m-d H:i:s');
                        $this->shared_model->insert('cs_items', $item_details);
                        log_db_query($this->db->last_query());  // Log DB Query
                        $returned_id = mysql_insert_id();
						
                        updateOutletQty($item_details, $item_details['item_id'], $this->session->userdata('outlet_id'), 'in');
                        $total = $this->retail_model->update_total('cash_sales', 'cs_items', 'cs_id', $item_details['cs_id']);
                        log_db_query($this->db->last_query());  // Log DB Query
                        $arr['type'] = 'success';
                        $arr['msg'] = format_price($total);
                        $all_item_details = $this->retail_model->csItemDetails($returned_id);

                    } else {
                        $arr['type'] = 'error';
                        $arr['msg'] = 'Wrong value for quantity! The number can\'t be higher than ' . ($item_details['quantity'] - $returned_items->s) . '.';
                        redirect('retail/create_cncs/' . $cn_id . '/error/');
                        exit;
                    }
                } else {
                    redirect('retail/credit_notes/#' . $cn_id . '/cs');
                    exit;
                }
            }
        }

        /*******************************************************************************************************************************/
	/********************************** added by farooq for item returns ************************************************************/
		public function returns_notes($limit = false) {
			$data = $this->data;
			$data['headline'] = 'Cash Sales Return';
			$data['subactive'] = 'returnsnotes';
			$data['breadcrumbs'] = array('Retail' => '#', 'Returns Notes' => '#');
			$this->load->view('retail/returns_notes', $data);
		}
		public function getReturnsNotes() {
			$creditNotes = $this->retail_model->getReturnsNotes();
			$creditNotes = json_decode($creditNotes, true);

			foreach ($creditNotes['aaData'] as $key => $value) {
				$creditNotes['aaData'][$key][1] = format_date($value[1]);
			}

			$creditNotes = $this->shared_model->JEncode($creditNotes);
			echo $creditNotes;
		}
		public function create_ri($invoice_id = false) {
			$data = $this->data;
			$data['headline'] = 'Returns Notes';
			$data['subactive'] = 'returnsnotes';
			$data['breadcrumbs'] = array('Retail' => '#', 'Create Invoice' => '#');

			$invoice_id = $this->uri->segment(3);
			if( $cn_id != ''){
				$sql = 'SELECT * FROM returned_items WHERE cn_id=' . $cn_id;
				$result = $this->shared_model->getRow($sql);
				$data['returned_items'] = $result;
			}
			$data['retail_items'] = $this->retail_model->getRetailItems();
			if ($msg == 'error') {
				$data['msg'] = 'Wrong value for quantity! The number can\'t be higher than ' . ($item_details['quantity'] - $returned_items->s) . '.';
			}
			if ($invoice_id) {
				$data['invoice_details'] = $this->retail_model->invoiceDetails($invoice_id);
				$data['mechanics'] = isset($data['invoice_details']->mechanics) ? unserialize($data['invoice_details']->mechanics) : array();
				$data['i_items'] = $this->retail_model->getInvoiceItems($invoice_id, 'Y');
			}
			$this->load->view('retail/add_returns', $data);
		}
		public function save_ri($invoice_id = false) {			
			
			$cn_id = $this->input->post('cn_id');
			/*if($cn_id == "")
			{
				$cn_id = $invoice_id;
			}*/
			$all_items = $this->input->post('item_id');
			$all_quantity = $this->input->post('quantity');
			$all_price = $this->input->post('price');
			$all_discount = $this->input->post('item_discount');
			$all_markup = $this->input->post('markup');
			$customer_id = $this->input->post('customer_id');
			$outlet_id = $this->session->userdata('outlet_id');
			$remark = $this->input->post('remark');
			$date = date('Y-m-d H:i:s');
			
			//echo "here";
			//exit;
			

			if (!$this->input->post('item_id') || $customer_id == '' ) {
				//redirect('retail/credit_notes');
				exit;
			}

			$returned_items = array();
			//$i = 0;
			for( $i = 0; $i < count($all_items); $i++ ) {
				$returned_items[$i]['item_id'] = $all_items[$i];
				$returned_items[$i]['price'] = $all_price[$i];
				$returned_items[$i]['discount'] = $all_discount[$i];
				$returned_items[$i]['vat'] = 18;
				$returned_items[$i]['qty'] = $all_quantity[$i];
				$returned_items[$i]['markup'] = $all_markup[$i];
			}

			//echo '<pre>'.print_r( $returned_items, true ).'</pre>';

			$data['customer_id'] = $customer_id;
			$data['cn_items'] = serialize($returned_items);
			$data['remarks'] = $remark;
			$data['cn_date'] = $date;
			$data['outlet_id'] = $outlet_id;
			$data['draft'] = 'N';

			if ($cn_id == '') {
				if ($this->input->post('save')) {
					//echo "inside save";
					//exit;
					$this->shared_model->insert('returned_items', $data);
					log_db_query($this->db->last_query());  // Log DB Query
					if($invoice_id)
					{
						redirect('retail/returns_notes/#' . $invoice_id . '/cn');
					}
					else
					{
						$cn_id = mysql_insert_id();
						foreach ($all_items as $key => $item_id) 
						{
							$sql = 'SELECT * FROM stock_outlets WHERE item_id=' . $item_id . ' AND outlet_id=' . $outlet_id;
							$result = $this->shared_model->getRow($sql);
							if ($result) {						
								$num = $result->qty + $all_quantity[$key];
								updateOutletQty($all_quantity[$key], $item_id, $outlet_id, 'in');
								$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_id . ' AND outlet_id = ' . $outlet_id)->qty;
								$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item_id . '","' . $all_quantity[$key] . '","' . $cn_id . '","Credit Note","' . $new_balance . '","' . $outlet_id . '")');  
								log_db_query($this->db->last_query());  // Log DB Query
							}
						}
						redirect('retail/returns_notes/?added=' . $cn_id);
					}                    
				}
				$data['draft'] = 'Y';
				$this->shared_model->insert('returned_items', $data);
				log_db_query($this->db->last_query());  // Log DB Query
				echo mysql_insert_id();
				//exit;
			}else{
				//echo "else";
				$this->shared_model->update('returned_items', 'cn_id', $cn_id, $data);
				log_db_query($this->db->last_query());  // Log DB Query
				echo $cn_id;
			}

			if ($this->input->post('save')) {
				//echo "Im here";
				//exit;
				$this->shared_model->update('returned_items', 'cn_id', $cn_id, $data);
				log_db_query($this->db->last_query());  // Log DB Query
				foreach ($all_items as $key => $item_id) {
					$sql = 'SELECT * FROM stock_outlets WHERE item_id=' . $item_id . ' AND outlet_id=' . $outlet_id;
					$result = $this->shared_model->getRow($sql);
					if ($result) {						
						$num = $result->qty + $all_quantity[$key];
						$this->db->where('item_id', $item_id);
						$this->db->where('outlet_id', $outlet_id);
						$this->db->update('stock_outlets', array('qty' => $num));
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item_id . ' AND outlet_id = ' . $outlet_id)->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item_id . '","' . $all_quantity[$key] . '","' . $cn_id . '","Credit Note","' . $new_balance . '","' . $outlet_id . '")');  
						log_db_query($this->db->last_query());  // Log DB Query
					}
				}
				$this->session->set_flashdata('success', 'Entry is successfully saved.');
				redirect('retail/returns_notes/?added=' . $cn_id);
			}

		}
		
		
		/*************************************************************************************/
    }