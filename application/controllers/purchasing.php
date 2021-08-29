<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

    class Purchasing extends MY_Controller {

        public function __construct() {
            parent::__construct();
            $this->data['active'] = 'purchasing';
            $this->load->model('purchasing_model');
        }
        /**Items Record added By Farooq**/
        public function piitems_record() {
            $data = $this->data;
            $data['headline'] = 'Purchase Items Record';
            $data['subactive'] = 'piitems_record';
            $data['breadcrumbs'] = array('Purchasing' => '#', 'Purchase Items Record' => '#');
            $this->load->view('purchasing/piitems_record', $data);
        }
        public function get_piitems(){
            $this->load->library('datatables');
            $this->datatables->select('pi_items.pi_id,pi_items.item_id,stock.barcode,stock.part_no,stock.description,pi_items.cost,pi_items.quantity,purchase_invoices.date,suppliers.name')
                    ->from('pi_items')
                    ->join('stock','pi_items.item_id=stock.item_id')
                    ->join('purchase_invoices','pi_items.pi_id=purchase_invoices.pi_id')
                    ->join('suppliers','purchase_invoices.supplier_id=suppliers.supplier_id');
            echo $this->datatables->generate();
        }

        /** Purchase Invoices **/

        public function purchase_invoices() {
            $data = $this->data;
            $data['headline'] = 'Purchase Invoices';
            $data['subactive'] = 'pi';
            $data['breadcrumbs'] = array('Purchasing' => '#', 'Purchase Invoices' => '#');
            $this->load->view('purchasing/pi_list', $data);
        }
		
		public function purchase_returns()
		{
			$data=$this->data;
			$data['headline']='Debit Notes';
			$data['subactive']='rpi';
			$data['breadcrumbs']=array('Purchasing'=>'#','Debit Notes'=>'#');
			$this->load->view('purchasing/rpi_list',$data);
		}
		
		public function getRPIs()
		{
			$purchase_returns=$this->purchasing_model->getRPIs();
			$purchase_returns = json_decode($purchase_returns, true);

			foreach ($purchase_returns['aaData'] as $key=>$value) {
				$purchase_returns['aaData'][$key][1]=format_date($value[1]);
				$purchase_returns['aaData'][$key][2]=format_date($value[2]);
				$invoice_details=$this->purchasing_model->rpiDetails($value[0]);
				//$currency=$this->shared_model->Lookup('currencies','currency',array('currency_id'=>$invoice_details->currency_id));
				$purchase_returns['aaData'][$key][4]=format_price($value[4]);
			}

			$purchase_returns=$this->shared_model->JEncode($purchase_returns);
			echo $purchase_returns;
		}

        public function getPIs() {
            $purchase_invoices = $this->purchasing_model->getPIs();
            $purchase_invoices = json_decode($purchase_invoices, true);

            foreach ($purchase_invoices['aaData'] as $key => $value) {
                $purchase_invoices['aaData'][$key][1] = format_date($value[1]);
                $purchase_invoices['aaData'][$key][2] = $value[2] ? format_date($value[2]) : '';
                $purchase_invoices['aaData'][$key][3] = $value[3] ? format_date($value[3]) : '';
                $purchase_invoices['aaData'][$key][5] = $value[5] ? format_price($value[5]) : '';
            }

            $purchase_invoices = $this->shared_model->JEncode($purchase_invoices);
            echo $purchase_invoices;
        }

        public function draft_transactions() {
            $data = $this->data;
            $data['headline'] = 'Draft Transactions';
            $data['subactive'] = 'draft';
            $data['breadcrumbs'] = array('Purchasing' => '#', 'Draft Transactions' => '#');
            $this->load->view('purchasing/draft_transactions', $data);
        }

        public function getDraftTransactions() {
            $draft_transactions = $this->purchasing_model->getDraftTransactions();
            echo $draft_transactions;
        }

        public function void_transactions() {
            $data = $this->data;
            $data['headline'] = 'Void Transactions';
            $data['subactive'] = 'draft';
            $data['breadcrumbs'] = array('Purchasing' => '#', 'Void Transactions' => '#');
            $this->load->view('purchasing/void_transactions', $data);
        }

        public function getVoidTransactions() {
            $void_transactions = $this->purchasing_model->getVoidTransactions();
            echo $void_transactions;
        }

        public function create_pi($pi_id = false) {
            $data = $this->data;
            $data['headline'] = 'Create P.I';
            $data['subactive'] = 'pi';
            $data['breadcrumbs'] = array('Purchasing' => '#', 'Create P.I' => '#');
            if ($pi_id) {
                $data['invoice_items'] = $this->purchasing_model->getPiItems($pi_id);
                $data['invoice_details'] = $this->purchasing_model->piDetails($pi_id);
                $data['invoice_details']->landed_cost = isset($data['invoice_details']->subtotal) ? ($data['invoice_details']->subtotal + $data['invoice_details']->additional_expenses) : '';
            }
            $this->load->view('purchasing/add_pi', $data);
        }

        public function searchSupplier() {
            $search_term = $this->input->post('customer');
            if ($search_term != '') {
                $result = $this->purchasing_model->searchSupplier($search_term);
                echo $this->shared_model->JEncode($result);
            }
        }

        public function searchCustomer() {
            $search_term = $this->input->post('customer');
            if ($search_term != '') {
                $result = $this->purchasing_model->searchCustomer($search_term);
                echo $this->shared_model->JEncode($result);
            }
        }

        public function addPIItem() {
            $this->load->model('retail_model');
            $item_id = $this->input->post('item_id');
            $qty = $this->input->post('qty');
            $cost = $this->input->post('item_cost');
            $pi_id = $this->input->post('invoice_id');

            if ($item_id && $cost !== '' && $qty !== '') {
                $item_details = $this->retail_model->getItemDetails($item_id);
                $insert_item = array('item_id' => $item_id, 'quantity' => $qty, 'cost' => $cost);
                if ($pi_id > 0) {
                    $insert_item['pi_id'] = $pi_id;
                    $this->shared_model->insert('pi_items', $insert_item);
                    log_db_query($this->db->last_query());  // Log DB Query
                    $iitem_id = mysql_insert_id();
                    $this->shared_model->execute('UPDATE purchase_invoices SET subtotal=subtotal+' . ($qty * $insert_item['cost']) . ',total=total+' . ($qty * $insert_item['cost']) . ' WHERE pi_id="' . $pi_id . '"');
                    log_db_query($this->db->last_query());  // Log DB Query
                } else {
                    $this->shared_model->insert('purchase_invoices', array('date' => date('Y-m-d H:i:s', time()), 'subtotal' => ($qty * $insert_item['cost']), 'total' => ($qty * $insert_item['cost']), 'user_id' => $this->session->userdata('user_id')));
                    log_db_query($this->db->last_query());  // Log DB Query
                    $pi_id = mysql_insert_id();
                    $insert_item['pi_id'] = $pi_id;
                    $this->shared_model->insert('pi_items', $insert_item);
                    log_db_query($this->db->last_query());  // Log DB Query
                    $iitem_id = mysql_insert_id();
                }

                $return = array(
                    'res' => 1,
                    'pi_id'   => $pi_id,
                    'content' => '<tr class="iitems" id="iitem_' . $iitem_id . '">
									<td>' . $item_details->description . '</td>
									<td>' . format_price($insert_item['cost']) . '</td>
									<td>' . $insert_item['quantity'] . '</td>
									<td>' . format_price($insert_item['quantity'] * $insert_item['cost']) . '
									<input type="hidden" class="item_price" value="' . $insert_item['quantity'] * $insert_item['cost'] . '"/>
									</td>
									<td class="actions">
										<a class="btn btn-xs btn-danger deleteitem" id="deleteitem_' . $iitem_id . '" role="button">
							                <i class="fa fa-times delete"></i>
							            </a>
							        </td>
								  </tr>'
                );
                echo $this->shared_model->JEncode($return);
            } else {
                echo $this->shared_model->JEncode( array( 'res' => 0, 'msg' =>'All fields are mandatory.'));
            }
        }
		
		public function return_pi($rpi_id=false)
		{
			$data=$this->data;
			$data['headline']='Debit Notes';
			$data['subactive']='rpi';
			$data['breadcrumbs']=array('Purchasing'=>'#','Debit Notes'=>'#');
			//$data['currencies']=$this->shared_model->getRecords('currencies');
			if($rpi_id)
			{
				$data['invoice_items']=$this->purchasing_model->getRPIItems($rpi_id);
				$data['invoice_details']=$this->purchasing_model->rpiDetails($rpi_id);
				if(!isset($data['invoice_details']->conversion) || !$data['invoice_details']->conversion) {
					//$data['invoice_details']->conversion=1;
					//$data['invoice_details']->currency='$';
				}
			}
			$this->load->view('purchasing/return_pi',$data);
		}
		
		public function addRPIItem()
		{
		$this->load->model('retail_model');
		$item_id=$this->input->post('item_id');
		$qty=$this->input->post('qty');
		$cost=$this->input->post('item_cost');
		$rpi_id=$this->input->post('invoice_id');
		if($item_id&&$cost!==''&&$qty!==''){
			$item_details=$this->retail_model->getItemDetails($item_id);
			$insert_item=array('item_id'=>$item_id,'quantity'=>$qty,'cost'=>$cost);
			if($rpi_id>0)
			{
				$insert_item['rpi_id']=$rpi_id;
				$this->shared_model->insert('rpi_items',$insert_item);
				$iitem_id=$this->db->insert_id();
				$this->shared_model->execute('UPDATE purchase_returns SET subtotal=subtotal+'.($qty*$insert_item['cost']).',total=total+'.($qty*$insert_item['cost']).' WHERE rpi_id="'.$rpi_id.'"');
			}
			else
			{ 
				$this->shared_model->insert('purchase_returns',array('date'=>date('Y-m-d H:i:s',time()),'subtotal'=>($qty*$insert_item['cost']),'total'=>($qty*$insert_item['cost']),'user_id'=>$this->session->userdata('user_id'),'draft'=>'Y'));
				$rpi_id=$this->db->insert_id();
				$insert_item['rpi_id']=$rpi_id;
				$this->shared_model->insert('rpi_items',$insert_item);
				$iitem_id=$this->db->insert_id();
			}

			$return=array('rpi_id'=>$rpi_id,
					'content'=>'<tr class="iitems" id="iitem_'.$iitem_id.'">
									<td>'.$item_details->stock_num.'</td>
									<td>'.$item_details->barcode.'</td>
									<td>'.$item_details->brand.'</td>
									<td>'.$item_details->category.'</td>
									<td>'.$item_details->description.'</td>
									<td>'.$item_details->model_no.'</td>
									<td>'.$item_details->color.'</td>
									<td>'.$item_details->size.'</td>
									<td>'.format_number($insert_item['cost']).'</td>
									<td>'.$insert_item['quantity'].'</td>
									<td>'.format_number($insert_item['quantity']*$insert_item['cost']).'
									<input type="hidden" class="item_price" value="'.$insert_item['quantity']*$insert_item['cost'].'"/>
									<input type="hidden" name="item_id" id="itemidd_' . $item_id . '" value="'.$item_id.'"/>
									</td>
									<td class="actions">
										<a class="btn btn-xs btn-danger deleteitem" id="deleteitem_'.$iitem_id.'" role="button">
							                <i class="fa fa-times delete"></i>
							            </a>
							        </td>
								  </tr>');
			echo $this->shared_model->JEncode($return);
		} else {
			echo 'All fields are mandatory.';
		}
	}

        public function save_pi() {
            $pi_id = $this->input->post('invoice_id');
            if ($pi_id) {
                $invoice_details = $this->shared_model->getPost('purchase_invoices');
                $invoice_details['date'] = date('Y-m-d H:i:s', time());
                //$invoice_details['payment_date']=date('Y-m-d',strtotime($invoice_details['payment_date']));
                if ($this->input->post('draft') == 'draft' && !$this->input->post('save')) {
                    $draft = 1;
                    $invoice_details['draft'] = 'Y';
                } else {
                    $draft = 0;
                    $invoice_details['draft'] = 'N';
                }

                $invoice_details['issue_date'] = isset($invoice_details['issue_date']) ? date('Y-m-d', strtotime($invoice_details['issue_date'])) : date('Y-m-d');
                $iitems = $this->purchasing_model->getPiItems($pi_id);
                $total_qty = $this->shared_model->getRow('select sum(quantity) as c from pi_items where pi_id=' . $pi_id)->c;
                $total = 0;
                $subtotal = 0;
                foreach ($iitems as $item) {
                    $total += $item->quantity * $item->cost;
                    $subtotal += $item->quantity * $item->cost;
                    if ($this->input->post('publish') && $this->input->post('save')) {
                        /*if($item->cost!=$item->cost_price) {
                            if($item->sell_price && $item->cost_price) {
                                $item->sell=($item->sell_price/$item->cost_price)*$item->cost;
                            } else {
                                $item->sell=0;
                            }
                            $item->cost=($item->cost_price+$item->cost)/2;
                        }*/
                        $item->total_qty = $total_qty;
                        $item->additional_expenses = $invoice_details['additional_expenses'];
                        updateOutletQty($item, $item->item_id, $this->session->userdata('outlet_id'), 'pi');
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item->item_id . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item->item_id . '","' . $item->quantity . '","' . $pi_id . '","Purchase Invoice","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');
                    }
                }
                $invoice_details['user_id'] = $this->session->userdata('user_id');
                $invoice_details['total'] = $total;
                $invoice_details['subtotal'] = $subtotal;
                if ($this->input->post('publish')) {
                    $invoice_details['published'] = 1;
                    $invoice_details['recieving_date'] = date('Y-m-d H:i:s');
                }

                // Create Purchase
                $success = $this->shared_model->update('purchase_invoices', 'pi_id', $pi_id, $invoice_details);
                if ($draft) {
                    if ($success) {
                        log_db_query($this->db->last_query());  // Log DB Query
                        echo $pi_id;
                    }
                } else {
                    if ($success) {
                        log_db_query($this->db->last_query());  // Log DB Query
                        $this->session->set_flashdata('success', 'Entry is successfully saved.');
                        redirect('purchasing/purchase_invoices/#' . $pi_id);
                    } else {
                        $this->session->set_flashdata('error', 'An error occured. Please try again');
                        redirect('purchasing/create_pi/' . $pi_id);
                    }
                }
            }
        }
		
	public function save_rpi()
	{
		$rpi_id=$this->input->post('invoice_id');
		if($rpi_id)
		{
			$invoice_details=$this->shared_model->getPost('purchase_returns',false,array('currency_id'));
			$invoice_details['date']=date('Y-m-d H:i:s',time());
			//$currency_id=$this->input->post('currency_id');
			//$currency_val=$currency_id ? $this->shared_model->Lookup('currencies','conversion',array('currency_id'=>$currency_id)) : 1;
			//$invoice_details['payment_date']=date('Y-m-d',strtotime($invoice_details['payment_date']));
			if($this->input->post('draft')=='draft' && !$this->input->post('save')) {
				$draft=1;
				$invoice_details['draft']='Y';
			} else {
				$draft=0;
				$invoice_details['draft']='N';
			} 
			$invoice_details['issue_date']=isset($invoice_details['issue_date']) ? date('Y-m-d',strtotime($invoice_details['issue_date'])) : date('Y-m-d');
			$iitems=$this->purchasing_model->getRPIItems($rpi_id);
			$total=0;
			$subtotal=0;
			foreach($iitems as $item)
			{
				$total+=$item->quantity*$item->cost;
				$subtotal+=$item->quantity*$item->cost;
				if($this->input->post('save')) {
					//$item->currency_val=$currency_val;
					//$item->currency_id=$currency_id;
					updateOutletQty(array('quantity'=>$item->quantity),$item->item_id,$this->session->userdata('outlet_id'),'out');
					$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item->item_id . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
					$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item->item_id . '","' . $item->quantity . '","' . $rpi_id . '","Purchase Returns","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")');                                      
				}
			}
			$invoice_details['user_id']=$this->session->userdata('user_id');
			$invoice_details['total']=$total;
			$invoice_details['subtotal']=$subtotal;
			if($this->input->post('save')) {
				$invoice_details['published']=1;
			}
			// Create Purchase	
			$success=$this->shared_model->update('purchase_returns','rpi_id',$rpi_id,$invoice_details);
			if($draft) {
				if($success) {
					echo '1';
				} else {
					echo '0';
				}
			} else {
				if($success) {
					$this->session->set_flashdata('success','Entry is successfully saved.');
					redirect('purchasing/purchase_returns/#'.$rpi_id); 
				} else {
					$this->session->set_flashdata('error','An error occurred. Please try again');
					redirect('purchasing/return_pi/'.$rpi_id); 
				}
			}
		} 
	}

	public function deletePiItem($id) {
		$this->shared_model->delete('pi_items', 'id', $id);
		log_db_query($this->db->last_query());  // Log DB Query
		echo $id;
	}
	public function deleteRPIItem($id)
	{
		$this->shared_model->delete('rpi_items','id',$id);
		echo $id;
	}

        public function payment_status() {
            $id = $this->input->post('id');
            $info = $this->shared_model->getRecords('purchase_invoices', '', '', array('pi_id' => $id));
            if ($info[0]->status == 'paid') {
                $new['status'] = $response['status'] = 'pending';
                $response['old'] = 'btn-success';
                $response['new'] = 'btn-info';
            } else {
                $new['status'] = $response['status'] = 'paid';
                $new['payment_date'] = date('Y-m-d H:i:s', time());
                $response['old'] = 'btn-info';
                $response['new'] = 'btn-success';
            }
            $this->shared_model->update('purchase_invoices', 'pi_id', $id, $new);
            log_db_query($this->db->last_query());  // Log DB Query
            echo $this->shared_model->JEncode($response);
        }

        /** Delivery orders **/

        public function delivery_orders() {
            $data = $this->data;
            $data['headline'] = 'Delivery Orders';
            $data['subactive'] = 'do';
            $data['breadcrumbs'] = array('Purchasing' => '#', 'Delivery Orders' => '#');
            $this->load->view('purchasing/do_list', $data);
        }

        public function getDOs() {
            $delivery_orders = $this->purchasing_model->getDOs();
            $delivery_orders = json_decode($delivery_orders, true);

            foreach ($delivery_orders['aaData'] as $key => $value) {
                $delivery_orders['aaData'][$key][1] = format_date($value[1]);
            }

            $delivery_orders = $this->shared_model->JEncode($delivery_orders);
            echo $delivery_orders;
        }

        public function create_do($do_id = false) {
            $data = $this->data;
            $data['headline'] = 'Create D.O';
            $data['subactive'] = 'do';
            $data['breadcrumbs'] = array('Purchasing' => '#', 'Create D.O' => '#');
            $data['outlets'] = $this->shared_model->getRecords('outlets', '', '', array('active' => 'Y'));
            if ($do_id) {
                $data['invoice_items'] = $this->purchasing_model->getDoItems($do_id);
                $data['invoice_details'] = $this->purchasing_model->doDetails($do_id);
            }
            $this->load->view('purchasing/add_do', $data);
        }

        public function addDOItem() {
            $this->load->model('retail_model');
            $item_id = $this->input->post('item_id');
            $qty = $this->input->post('qty');
            $do_id = $this->input->post('invoice_id');
            if ($item_id && $qty !== '') {
                $item_details = $this->retail_model->getItemDetails($item_id);
                $insert_item = array('item_id' => $item_id, 'quantity' => $qty, 'cost' => $item_details->cost_price);
                if ($do_id > 0) {
                    $insert_item['do_id'] = $do_id;
                    $this->shared_model->insert('do_items', $insert_item);
                    log_db_query($this->db->last_query());  // Log DB Query
                    $iitem_id = mysql_insert_id();
                } else {
                    $this->shared_model->insert('delivery_orders', array('do_time_stamp' => date('Y-m-d H:i:s'), 'date' => date('Y-m-d', time()), 'user_id' => $this->session->userdata('user_id')));
                    log_db_query($this->db->last_query());  // Log DB Query
                    $do_id = mysql_insert_id();
                    $insert_item['do_id'] = $do_id;
                    $this->shared_model->insert('do_items', $insert_item);
                    log_db_query($this->db->last_query());  // Log DB Query
                    $iitem_id = mysql_insert_id();
                }

                $return = array(
                    'do_id'   => $do_id,
                    'content' => '<tr class="iitems" id="iitem_' . $iitem_id . '">
									<td>' . $item_details->stock_num . '</td>
									<td>' . $item_details->description . '</td>
									<td>' . $insert_item['quantity'] . '</td>
									<td class="actions">
										<a class="btn btn-xs btn-danger deleteitem" id="deleteitem_' . $iitem_id . '" role="button">
							                <i class="fa fa-times delete"></i>
							            </a>
							        </td>
								  </tr>'
                );
                echo $this->shared_model->JEncode($return);
            } else {
                echo 'All fields are mandatory.';
            }
        }

        public function save_do() {
            $do_id = $this->input->post('invoice_id');
            if ($do_id) {
                $invoice_details = $this->shared_model->getPost('delivery_orders');
                $invoice_details['date'] = date('Y-m-d H:i:s', time());
                $iitems = $this->purchasing_model->getDoItems($do_id);
                $total = 0;
                foreach ($iitems as $item) {
                    $total += $item->cost * $item->quantity;
                    if ($this->input->post('publish')) {
                        updateOutletQty((array)$item, $item->item_id, $this->session->userdata('outlet_id'), 'out');
						$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $item->item_id . ' AND outlet_id = ' . $this->session->userdata('outlet_id'))->qty;
						$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $item->item_id . '","' . $item->quantity . '","' . $do_id . '","Delivery Order","' . $new_balance . '","' . $this->session->userdata('outlet_id') . '")'); 						
                        log_db_query($this->db->last_query());  // Log DB Query
                    }
                }
                $invoice_details['total'] = format_number($total);
                $invoice_details['user_id'] = $this->session->userdata('user_id');
                if ($this->input->post('publish')) {
                    $invoice_details['published'] = 1;
                }
                // Create Purchase
                $invoice_details['invoice_id'] = null;
                $success = $this->shared_model->update('delivery_orders', 'do_id', $do_id, $invoice_details);
                if ($success) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    $this->session->set_flashdata('success', 'Entry is successfully saved.');
                    redirect('purchasing/delivery_orders/#' . $do_id);
                } else {
                    $this->session->set_flashdata('error', 'An error occured. Please try again');
                    redirect('purchasing/create_do/' . $do_id);
                }
            }
        }

        public function deleteDoItem($id) {
            $this->shared_model->delete('do_items', 'id', $id);
            log_db_query($this->db->last_query());  // Log DB Query
            echo $id;
        }

        /** Purchase orders **/

        public function purchase_orders() {
            $data = $this->data;
            $data['headline'] = 'Purchase Orders';
            $data['subactive'] = 'po';
            $data['breadcrumbs'] = array('Purchasing' => '#', 'Purchase Orders' => '#');
            $this->load->view('purchasing/po_list', $data);
        }

        public function getPOs() {
            $purchase_orders = $this->purchasing_model->getPOs();
            $purchase_orders = json_decode($purchase_orders, true);

            foreach ($purchase_orders['aaData'] as $key => $value) {
                $purchase_orders['aaData'][$key][1] = format_date($value[1]);
            }

            $purchase_orders = $this->shared_model->JEncode($purchase_orders);
            echo $purchase_orders;
        }

        public function create_po($po_id = false) {
            $data = $this->data;
            $data['headline'] = 'Create P.O';
            $data['subactive'] = 'po';
            $data['breadcrumbs'] = array('Purchasing' => '#', 'Create P.O' => '#');
            if ($po_id) {
                $data['invoice_items'] = $this->purchasing_model->getPoItems($po_id);
                $data['invoice_details'] = $this->purchasing_model->poDetails($po_id);
            }
            $this->load->view('purchasing/add_po', $data);
        }


        public function create_iopo($iopo_id = false) {
            $data = $this->data;
            $data['headline'] = 'Create Inter Outlet Purchase Order';
            $data['subactive'] = 'iopo';
            $data['breadcrumbs'] = array('Purchasing' => '#', 'Create Inter Outlet Purchase Order' => '#');
            if ($iopo_id) {
                $data['invoice_items'] = $this->purchasing_model->getPoItems($iopo_id);
                $data['invoice_details'] = $this->purchasing_model->iopoDetails($iopo_id);
            }
            $this->load->view('purchasing/add_iopo', $data);
        }

        public function addPOItem() {
            $this->load->model('retail_model');
            $item_id = $this->input->post('item_id');
            $qty = $this->input->post('qty');
            $po_id = $this->input->post('invoice_id');
            if ($item_id && $qty !== '') {
                $item_details = $this->retail_model->getItemDetails($item_id);
                $insert_item = array('item_id' => $item_id, 'quantity' => $qty, 'cost' => $item_details->cost_price);
                if ($po_id > 0) {
                    $insert_item['po_id'] = $po_id;
                    $this->shared_model->insert('po_items', $insert_item);
                    log_db_query($this->db->last_query());  // Log DB Query
                    $iitem_id = mysql_insert_id();
                } else {
                    $this->shared_model->insert('purchase_orders', array('date' => date('Y-m-d', time()), 'user_id' => $this->session->userdata('user_id')));
                    log_db_query($this->db->last_query());  // Log DB Query
                    $po_id = mysql_insert_id();
                    $insert_item['po_id'] = $po_id;
                    $this->shared_model->insert('po_items', $insert_item);
                    log_db_query($this->db->last_query());  // Log DB Query
                    $iitem_id = mysql_insert_id();
                }

                $return = array(
                    'po_id'   => $po_id,
                    'content' => '<tr class="iitems" id="iitem_' . $iitem_id . '">
									<td>' . $item_details->description . '</td>
									<td>' . $insert_item['quantity'] . '</td>
									<td class="actions">
										<a class="btn btn-xs btn-danger deleteitem" id="deleteitem_' . $iitem_id . '" role="button">
							                <i class="fa fa-times delete"></i>
							            </a>
							        </td>
								  </tr>'
                );
                echo $this->shared_model->JEncode($return);
            } else {
                echo 'All fields are mandatory.';
            }
        }

        public function save_iopo() {
            $data['id'] = $this->input->post('id');
            $data['quantity'] = $this->input->post('quantity');
            $countitems = sizeof($data['id']);
            for ($i = 0; $i < $countitems; $i++) {
                $update['id'] = $data['id'][$i];
                $update['quantity'] = $data['quantity'][$i];
                $sql = 'UPDATE po_items SET quantity="' . $update['quantity'] . '" WHERE id="' . $update['id'] . '"';
                if ($this->shared_model->execute($sql)) {
                    log_db_query($this->db->last_query());  // Log DB Query
                    $this->session->userdata('success', 'Your order is successfully updated');
                } else {
                    $this->session->userdata('success', 'Error occured');
                }
            }
            redirect('inventory/cartorderview');
        }

        public function save_po() {
            $po_id = $this->input->post('invoice_id');
            if ($po_id) {
                $invoice_details = $this->shared_model->getPost('purchase_orders');
                $invoice_details['date'] = date('Y-m-d H:i:s', time());
                $invoice_details['user_id'] = $this->session->userdata('user_id');
                if ($this->input->post('draft') == 'draft' && !$this->input->post('publish') && !$this->input->post('save')) {
                    $draft = 1;
                    $invoice_details['draft'] = 'Y';
                } else {
                    $draft = 0;
                    $invoice_details['draft'] = 'N';
                }
                $po_info = $this->shared_model->getRow('select sum(quantity*cost) as total from po_items where po_id="' . $po_id . '"');
                $total = $po_info ? $po_info->total : 0;
                $invoice_details['subtotal'] = $total;
                $invoice_details['user_id'] = $this->session->userdata('user_id');
                if ($this->input->post('publish')) {
                    $pi_details = $invoice_details;
                    $pi_details['issue_date'] = date('Y-m-d');
                    $pi_id = $this->shared_model->Lookup('purchase_orders', 'pi_id', array('po_id' => $po_id));
                    if ($pi_id) {
                        $this->shared_model->update('purchase_invoices', 'pi_id', $pi_id, $pi_details);
                        log_db_query($this->db->last_query());  // Log DB Query
                    } else {
                        $this->shared_model->insert('purchase_invoices', $pi_details);
                        log_db_query($this->db->last_query());  // Log DB Query
                        $pi_id = mysql_insert_id();
                        $invoice_details['pi_id'] = $pi_id;
                    }
                }
                $iitems = $this->purchasing_model->getPoItems($po_id);
                if ($this->input->post('publish')) {
                    $this->shared_model->delete('pi_items', 'pi_id', $pi_id);
                    log_db_query($this->db->last_query());  // Log DB Query
                }
                foreach ($iitems as $item) {
                    if ($this->input->post('publish')) {
                        //updateOutletQty($item,$item->item_id,$this->session->userdata('outlet_id'),'po');
                        $pi_item = array('pi_id' => $pi_id, 'item_id' => $item->item_id, 'quantity' => $item->quantity, 'cost' => $item->cost);
                        $this->shared_model->insert('pi_items', $pi_item);
                        log_db_query($this->db->last_query());  // Log DB Query
                    }
                }
                // Create Purchase
                $success = $this->shared_model->update('purchase_orders', 'po_id', $po_id, $invoice_details);
                if ($draft) {
                    if ($success) {
                        log_db_query($this->db->last_query());  // Log DB Query
                        echo $po_id;
                    }
                } else {
                    if ($success) {
                        log_db_query($this->db->last_query());  // Log DB Query
                        $this->session->set_flashdata('success', 'Entry is successfully saved.');
                        if ($this->input->post('publish')) {
                            redirect('purchasing/create_pi/' . $pi_id);
                        } else {
                            redirect('purchasing/purchase_orders');
                        }
                    } else {
                        $this->session->set_flashdata('error', 'An error occured. Please try again');
                        redirect('purchasing/create_po/' . $po_id);
                    }
                }
            }
        }

        public function deletePoItem($id) {
            $this->shared_model->delete('po_items', 'id', $id);
            log_db_query($this->db->last_query());  // Log DB Query
            echo $id;
        }

        public function incommingpo() {
            $data = $this->data;
            $data['headline'] = 'Incoming purchase orders';
            $data['breadcrumbs'] = array('Incoming purchase orders' => '#');

            $data['notifications'] = $this->purchasing_model->getIncomingPurchaseOrders();
            $data['deliveries'] = $this->shared_model->LookupArray('delivery_orders', 'do_id', 'po_id');
            $this->load->view('purchasing/notifications', $data);
        }

        public function create_delivery($id) {
            $data = $this->data;
            $data['headline'] = 'Create Delivery Order';
            $data['breadcrumbs'] = array('Create Delivery Order' => '#');

            if ($id) {
                $data['invoice_items'] = $this->purchasing_model->getPoItems($id);
            }

            $this->load->view('purchasing/create_delivery', $data);
        }

        public function saveDelivery($po_id) {
            $do_data['deposit'] = $this->input->post('deposit');
            $do_data['status'] = $this->input->post('status');
            $do_data['po_id'] = $po_id;
            $do_data['user_id'] = $this->session->userdata('user_id');
            $do_data['date'] = date('Y-m-d H:i:s');
            $do_data['outlet_id'] = $this->shared_model->Lookup('purchase_orders', 'outlet_id', array('po_id' => $po_id));
            $this->shared_model->insert('delivery_orders', $do_data);
            log_db_query($this->db->last_query());  // Log DB Query

            $data['do_id'] = $this->db->insert_id();
            $data['quantity'] = $this->input->post('quantity');
            $data['item_id'] = $this->input->post('item_id');
            $countItems = sizeof($data['quantity']);
            for ($i = 0; $i < $countItems; $i++) {
                $iData['quantity'] = $data['quantity'][$i];
                $iData['item_id'] = $data['item_id'][$i];
                $iData['do_id'] = $data['do_id'];
                $this->shared_model->insert('do_items', $iData);
                log_db_query($this->db->last_query());  // Log DB Query
            }
            redirect('purchasing/delivery_orders');
        }

        public function incomingDelivery() {
            $data = $this->data;
            $data['headline'] = 'Incoming Deliveries';
            $data['breadcrumbs'] = array('Incoming Deliveries' => '#');

            $data['delorders'] = $this->purchasing_model->getIncomingDeliveryOrders();
            $this->load->view('purchasing/incomingdelivery', $data);

        }

        public function acceptDelivery($id) {
            $data['delivery_orders'] = $this->purchasing_model->getDoItems($id);
            $this->db->trans_start();
            $s = $this->shared_model->update('delivery_orders', 'do_id', $id, array('accepted' => 'Y'));
            log_db_query($this->db->last_query());  // Log DB Query
            foreach ($data['delivery_orders'] as $key => $value) {
				$old_details=$this->shared_model->getRow('select * from stock_outlets where item_id="'.$value->item_id.'" and outlet_id="'.$outOutlet_id.'"');
				$old_details->quantity=$value->quantity;
				updateOutletQty($old_details,$value->item_id,$outlet_info->outlet_id,'do');
				$new_balance = $this->shared_model->getRow('select qty from stock_outlets where item_id=' . $value->item_id . ' AND outlet_id = ' . $outlet_info->outlet_id)->qty;
				$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $value->item_id . '","' . $old_details->quantity . '","' . $id . '","Accept Delivery In","' . $new_balance . '","' . $outlet_info->outlet_id . '")'); 
                /*updateOutletQty($value, $value->item_id, $this->session->userdata('outlet_id'), 'do');
                log_db_query($this->db->last_query());  // Log DB Query*/
            }
            $this->db->trans_complete();
            $this->session->set_flashdata('success', 'Goods are accepted.');
            redirect('purchasing/incomingDelivery');
        }
    }