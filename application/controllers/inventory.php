<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

    class Inventory extends MY_Controller {

        public function __construct() {
            parent::__construct();
            $this->data['active'] = 'inventory';
            $this->load->model('stock_model');
        }

        public function index() {
            $data = $this->data;
            $data['headline'] = 'Inventory';
            $data['subactive'] = 'inventory';
            $data['active'] = 'inventory';
            $data['prettyPhoto'] = true;

            $data['breadcrumbs'] = array('Inventory' => '#');
            $this->load->view('inventory/list', $data);
        }

        public function getStockItems($outlet_id = false) {

            $this->load->library('datatables');
            $outlet_id = $this->session->userdata('outlet_id');
            if ($outlet_id) {
                $stock = $this->stock_model->getStockItems($outlet_id);
            } else {
                $stock = $this->stock_model->getStockItems();
            }
            $stock = json_decode($stock, true);

            foreach ($stock['aaData'] as $key => $res) {
                $stock['aaData'][$key]['7'] = format_price($res['7']);
                $stock['aaData'][$key]['8'] = format_price($res['8']);
                $stock['aaData'][$key]['9'] = format_price($res['9']);
                $stock['aaData'][$key]['14'] = format_price($res['14']);
                $stock['aaData'][$key]['15'] = format_price($res['15']);
                $stock['aaData'][$key]['16'] = format_price($res['16']);
            }

            $stock = $this->shared_model->JEncode($stock);
            echo $stock;
        }

        public function getStockLists($outlet_id = false) {

            $this->load->library('datatables');
            $outlet_id = $this->session->userdata('outlet_id');
            if ($outlet_id) {
                $stock = $this->stock_model->getStockLists($outlet_id);
            } else {
                $stock = $this->stock_model->getStockLists();
            }

            $stock = json_decode($stock, true);
            foreach ($stock['aaData'] as $key => $res) {
                $stock['aaData'][$key]['8'] = format_price($res['8']);
                $stock['aaData'][$key]['9'] = format_price($res['9']);
                /*$stock['aaData'][$key]['16'] = format_price($res['16']);
                $stock['aaData'][$key]['17'] = format_price($res['17']);
                $stock['aaData'][$key]['18'] = format_price($res['18']);*/
            }

            //echo '<pre>'.print_r( $stock, true ).'</pre>';

            $stock = $this->shared_model->JEncode($stock);
            echo $stock;
        }

        public function getStockItemDetails($type = 'JSON', $item_id = false) {
            if (!$item_id) {
                $item_id = $this->input->post('product_id');
            }
            $productDetails = $this->stock_model->getStockItemDetails($item_id);
            if ($type == 'JSON') {
                echo $this->shared_model->JEncode($productDetails);
            } else {
                return $productDetails;
            }
        }

        public function addItem($item_id = false) {
            $data = $this->data;
            $data['headline'] = 'Add Item';
            $data['subactive'] = 'inventory';
            $data['active'] = 'inventory';

            $data['breadcrumbs'] = array('Inventory' => '#');
            $data['categories'] = $this->shared_model->getRecords('categories');

            if ($item_id) {
                $data['item'] = $this->stock_model->getItemDetails($item_id);
            }

            $this->load->view('inventory/addItem', $data);
        }

        public function saveItem($item_id = false) {

            $this->load->helper(array('form', 'url'));
            // $this->load->library('form_validation');

            // $this->form_validation->set_rules('stock_num', 'Stock Code', 'required');
            // $this->form_validation->set_rules('description', 'Description', 'required');
            // $this->form_validation->set_rules('barcode', 'Barcode', 'required');

            // if ($this->form_validation->run() == false) {
                // $validate = false;
                // $this->session->set_flashdata('error', validation_errors());
                // if ($item_id) {
                    // redirect('inventory/addItem/' . $item_id);
                // } else {
                    // redirect('inventory/addItem/');
                // }

            // } else {
                // $validate = true;
            // }
			 $validate = true;
            if ($validate) {
                $stock = $this->shared_model->getPost('stock');
                $stock_outlet = $this->shared_model->getPost('stock_outlets');
                $stock_outlet['sell_price'] = isset($stock_outlet['price1']) ? $stock_outlet['price1'] : 0;

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['file_name'] = time() . rand(100, 900);

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('upload')) {
                    $uploadData = $this->upload->data();
                    $stock_outlet['image'] = $uploadData['file_name'];
                }/* else {
				$this->session->set_flashdata(array('error'=>'Upload file problem!'));
				redirect('inventory');
			}*/

                if (isset($item_id) && $item_id != '') {

                    if ($this->checkstocknumber($stock['stock_num'], $item_id)) {
                        $this->session->set_flashdata(array('error' => 'The stock number already exists in the database'));
                        redirect('inventory/addItem/' . $item_id);
                        exit;
                    }


                    $update_stock = $this->shared_model->update('stock', 'item_id', $item_id, $stock);
                    if ($update_stock)
                        log_db_query($this->db->last_query());  // Log DB Query

                    $update_so = $this->shared_model->updateNew('stock_outlets', $stock_outlet, array('item_id' => $item_id, 'outlet_id' => $this->session->userdata('outlet_id')));
                    if ($update_so)
                        log_db_query($this->db->last_query());  // Log DB Query

                    if ($update_stock || $update_so)
                        $this->session->set_flashdata(array('success' => "Thise entry is updated sucessfully"));
                    else
                        $this->session->set_flashdata(array('error' => 'Error occured'));


                } else {

                    if ($this->checkstocknumber($stock['stock_num'])) {
                        $this->session->set_flashdata(array('error' => 'The stock number already exists in the database'));
                        redirect('inventory/addItem');
                        exit;
                    }

                    $insert_stock = $this->shared_model->insert('stock', $stock);
                    if ($insert_stock)
                        log_db_query($this->db->last_query());  // Log DB Query

                    $stock_outlet['item_id'] = mysql_insert_id();
                    $stock_outlet['outlet_id'] = $this->session->userdata('outlet_id');

                    $insert_so = $this->shared_model->insert('stock_outlets', $stock_outlet);
                    if ($insert_so)
                        log_db_query($this->db->last_query());  // Log DB Query

                    if ($insert_stock || $insert_so)
                        $this->session->set_flashdata(array('success' => "This entry is updated successful"));
                    else
                        $this->session->set_flashdata(array('error' => 'Error occurred'));
                }
                redirect('inventory');


            }
        }

        private function checkstocknumber($number, $id = false) {
            if ($number) {
                $result = $this->shared_model->getRow('select * from stock where stock_num = "' . $number . '" AND item_id!="' . $id . '"');
            } else {
                $result = false;
            }
            return $result;
        }

        public function deleteItem($id) {
            $sql = 'delete from stock_outlets where item_id="' . $id . '" and outlet_id="' . $this->session->userdata('outlet_id') . '"';
            $deleted = $this->shared_model->execute($sql);
            if ($deleted) {
                log_db_query($this->db->last_query());  // Log DB Query
                $this->session->set_flashdata(array('success' => "This entry is successfully deleted"));
            } else {
                $this->session->set_flashdata(array('error' => "Error occurred"));
            }

            redirect('inventory');
        }


        public function stocklist() {
            $data = $this->data;
            $data['headline'] = 'Stock List';
            $data['subactive'] = 'stock_list';
            $data['active'] = 'inventory';

            $data['breadcrumbs'] = array('Inventory' => '#');
            $this->load->view('inventory/stocklist', $data);
        }
		
		public function stock_balance() {
            $data = $this->data;
            $data['headline'] = 'Stock Balance';
            $data['subactive'] = 'stock_balance';
            $data['active'] = 'inventory';
			$data['brands'] = $this->shared_model->getQuery('select distinct brand from stock where brand!="" order by brand ASC');
            $data['categories'] = $this->shared_model->getQuery('select distinct category from stock where category!="" order by category ASC');

            $data['breadcrumbs'] = array('Inventory' => '#');
            $this->load->view('inventory/stock_balance', $data);
        }

        public function getPriceHistory() {
            $item_id = $this->input->post('product_id');
            $sql = 'SELECT distinct(cost), pi.date FROM pi_items JOIN purchase_invoices as pi USING (pi_id) where item_id="' . $item_id . '" order by pi.date desc';
            $history = $this->shared_model->getQuery($sql);
            foreach ($history as $key => $value) {
                $history[$key]->date = format_date($value->date);
                $history[$key]->cost = format_price($value->cost);

            }
            $history = $this->shared_model->JEncode($history);
            echo $history;
        }

        public function stock_take() {
            $data = $this->data;
            $data['headline'] = 'Stock Take';
            $data['subactive'] = 'stock_take';
            $data['active'] = 'inventory';
            $data['brands'] = $this->shared_model->getQuery('select distinct brand from stock where brand!=""');
            $data['categories'] = $this->shared_model->getQuery('select distinct category from stock where category!=""');

            $data['breadcrumbs'] = array('Stock Take' => '#');
            $this->load->view('inventory/stock_take', $data);
        }

        public function getStockTake() {
            $stock_take = $this->stock_model->getStockTake();
            $stock_take = json_decode($stock_take, true);
            foreach ($stock_take['aaData'] as $key => $res) {
                $stock_take['aaData'][$key]['6'] = format_price($res['6']);
                $stock_take['aaData'][$key]['7'] = format_price($res['7']);
                $stock_take['aaData'][$key]['8'] = format_price($res['8']);
            }
            echo $this->shared_model->JEncode($stock_take);
        }

        /*public function generate_st_template($brand = false, $category = false) {
            $brand = $this->input->get('brand');
            $category = $this->input->get('category');
            $sites_details = $this->stock_model->stockItems($brand, $category);

            $this->load->library('excel');
            $phpExcel = $this->excel;
            $phpExcel->setActiveSheetIndex(0);

            $detailsSheet = $this->excel->getActiveSheet();
            $detailsSheet->setTitle('StockTake');
            $detailsSheet->setCellValue('A1', 'Stock Code')
                ->setCellValue('B1', 'Part No.')
                ->setCellValue('C1', 'Description')
                ->setCellValue('D1', 'Car Model')
                ->setCellValue('E1', 'Brand')
                ->setCellValue('F1', 'Category')
                ->setCellValue('G1', 'Cost Price')
                ->setCellValue('H1', 'Sell Price')
                ->setCellValue('I1', 'Last Sell Price')
                ->setCellValue('J1', 'Current Quantity')
                ->setCellValue('K1', 'Stock Take')
                ->setCellValue('L1', 'Reason');
            foreach ($sites_details as $key => $value) {
                $detailsSheet->setCellValue('A' . ($key + 2), $value->stock_num)
                    ->setCellValue('B' . ($key + 2), $value->part_no)
                    ->setCellValue('C' . ($key + 2), $value->description)
                    ->setCellValue('D' . ($key + 2), $value->model_no)
                    ->setCellValue('E' . ($key + 2), $value->brand)
                    ->setCellValue('F' . ($key + 2), $value->category)
                    ->setCellValue('G' . ($key + 2), $value->cost_price)
                    ->setCellValue('H' . ($key + 2), $value->sell_price)
                    ->setCellValue('I' . ($key + 2), $value->last_sell_price)
                    ->setCellValue('J' . ($key + 2), $value->qty);
            }
            $detailsSheet->getStyle('A1:L1')->getFont()->setBold(true);
            foreach (range('A', 'L') as $columnID) {
                $detailsSheet->getColumnDimension($columnID)->setAutoSize(true);
            }
            $detailsSheet->getStyle('A1:F1')->getAlignment()->setWrapText(true);
            $filename = 'StockTake.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache

            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
            $objWriter->save('php://output');
        }*/
		
		public function generate_st_template($brand = false, $category = false)
		{
			$brand = $this->input->get('brand');
            $category = $this->input->get('category');
           		
			$download_name = 'Stock Take.csv';
			
			header('Content-type: text/csv'); //mime type
			header('Content-Disposition: attachment;filename="' . $download_name . '"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache
			
			$sites_details = $this->stock_model->stockItems($brand, $category);	
			$fields = array('Item Id', 'Stock Code', 'Part No', 'Description', 'Car Model', 'Brand', 'Category', 'Cost Price', 'Sell Price', 'Last Sell Price', 'Current Quantity',  'Stock Take', 'Reason');
			echo implode(",", $fields) . "\n";
			//print_r($sites_details);		
			
			foreach ($sites_details as $key => $value) 
			{			
				echo $value->item_id . "," .  $value->stock_num . "," . $this->removeComma($value->part_no) . "," . $this->removeComma($value->description) . "," . $this->removeComma($value->model_no) . "," . $this->removeComma($value->brand) . "," . $this->removeComma($value->category) . "," . $value->cost_price . "," . $value->sell_price . "," . $value->last_sell_price . "," . $value->qty . "\n";
				
				/*echo $sites_details[$key]->stock_num . "," . $sites_details[$key]->part_no . "," . $sites_details[$key]->description . "," . $sites_details[$key]->model_no . "," . $sites_details[$key]->brand . "," . $sites_details[$key]->category . "," . $sites_details[$key]->cost_price . "," . $sites_details[$key]->sell_price . "," . $sites_details[$key]->last_sell_price . "," . $sites_details[$key]->qty . "," . $sites_details[$key]->stock_num . "," . $sites_details[$key]->stock_num . "\n";*/
				
			}
			//echo ',,,,,,,,Total:,' . $total_inv_cost . ',' . $total_cost . ',' . $total_sell . ',' . $gross_total;
			exit;
		}

		
		
		public function export_st_balance($date_from = false, $date_to = false, $brand_filter = false, $category_filter = false)
		{
			$date_from = $this->input->get('date_from');
			$date_to = $this->input->get('date_to');
			$brand = $this->input->get('brand_filter');
            $category = $this->input->get('category_filter');
           		
			$download_name = 'Stock Balance.csv';
			
			header('Content-type: text/csv'); //mime type
			header('Content-Disposition: attachment;filename="' . $download_name . '"'); //tell browser what's the file name
			header('Cache-Control: max-age=0'); //no cache

			$result = $this->stock_model->getStockBalanceExport($date_from, $date_to, $brand, $category);	
			$fields = array('stock num', 'brand', 'category', 'description','date', 'final balance','cost price', 'selling price');
			echo implode(",", $fields) . "\n";
			
			foreach ($result as $key=>$item) {
				echo str_replace(',', '', $item->stock_num) . ',' . 
					 str_replace(',', '', $item->brand) . ',' . 
					 str_replace(',', '', $item->category) . ',' . 
					 str_replace(',', '', $item->description) . ',' . 
					 str_replace(',', '', $item->date) . ',' . 
					 str_replace(',', '', $item->final_balance) . ',' . 
					 str_replace(',', '', format_price($item->cost_price)) . ',' . 
					 str_replace(',', '', format_price($item->selling_price)) . "\n";
			}			
			exit;
		}
        /*public function importStockTake() {
            $config['upload_path'] = 'uploads';
            $config['allowed_types'] = 'xls|xlsx|csv';
            $config['file_name'] = time() . rand();
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('file')) {
                $data_info = $this->upload->data();
            } else {
                $this->session->set_flashdata('error', 'An error occured: ' . $this->upload->display_errors());
                redirect('inventory/stock_take');
            }
            $this->load->library('excel');
            $objRead = PHPExcel_IOFactory::load('uploads/' . $data_info['file_name']);
            $error = array();
            $success = array();
            foreach ($objRead->getWorksheetIterator() as $worksheet) {
                $title = $worksheet->getTitle();

                // read document sheets one by one and do different actions depending on the title
                if ($title == 'StockTake') {
                    $db_columns = array('item_id', 'part_no', 'description', 'model_no', 'brand', 'category', 'cost_price', 'sell_price', 'last_sell_price', 'qty', 'stock_take', 'reason');
                    $highestRow = $worksheet->getHighestRow();
                    //$highestColumn=$worksheet->getHighestColumn();
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString('L');
                    // iterate through all cells using row and column index values
                    for ($i = 2; $i <= $highestRow; $i++) {
                        $stock_take = array();
                        for ($j = 0; $j < $highestColumnIndex; $j++) {
                            $cell = $worksheet->getCellByColumnAndRow($j, $i);
                            if ($cell->getValue() instanceof PHPExcel_RichText) {
                                $cell_val = $cell->getValue()->getPlainText();
                            } else {
                                $cell_val = $cell->getValue();
                            }
                            array_push($stock_take, $cell_val);
                        }

                        $stock_take = array_combine($db_columns, $stock_take);
                        if (isset($stock_take['stock_take']) && is_numeric($stock_take['stock_take'])) {
                            $stock_code = $stock_take['item_id'];
                            unset($stock_take['description']);
                            unset($stock_take['part_no']);
                            unset($stock_take['model_no']);
                            unset($stock_take['brand']);
                            unset($stock_take['category']);
                            unset($stock_take['cost_price']);
                            unset($stock_take['sell_price']);
                            unset($stock_take['last_sell_price']);
                            unset($stock_take['qty']);
                            $stock_take['item_id'] = $this->shared_model->Lookup('stock', 'item_id', array('stock_num' => $stock_take['item_id']));
                            if ($stock_take['item_id']) {
                                $stock_take['user_id'] = $this->session->userdata('user_id');
                                $stock_take['date'] = date('Y-m-d H:i:s');
                                $insert = $this->shared_model->insert('stock_take', $stock_take);
                                $stock_take_id = mysql_insert_id();
                                if ($insert) {
                                    $this->shared_model->execute('update stock_outlets set qty="' . $stock_take['stock_take'] . '" where item_id="' . $stock_take['item_id'] . '" and outlet_id="' . $this->session->userdata('outlet_id') . '"');
                                    $this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $stock_take['item_id'] . '",0,"' . $stock_take_id . '","Stock Take","' . $stock_take['stock_take'] . '","' . $this->session->userdata('outlet_id') . '")');
                                    array_push($success, 'Stock take for item with stock code ' . $stock_code . ' is successfully saved.');
                                } else {
                                    array_push($error, 'An error occurred when trying to save stock take for item with stock code ' . $stock_code . '. Please try again.');
                                }
                            } else {
                                array_push($error, 'The stock code ' . $stock_code . ' is invalid. Please try again.');
                            }
                        }
                    }
                }
            }
            if (!empty($error)) {
                $error = '<p>' . implode('</p><p>', $error) . '</p>';
                $error = rtrim($error, '<p></p>');
                $this->session->set_flashdata('error', $error);
            }
            if (!empty($success)) {
                $success = '<p>' . implode('</p><p>', $success) . '</p>';
                $success = rtrim($success, '<p></p>');
                $this->session->set_flashdata('success', $success);
            }
            redirect('inventory/stock_take');

        }*/
		
		public function importStockTake() {
            $this->load->helper('inflector');
			$config['upload_path'] = 'uploads';
			$config['allowed_types'] = 'csv';
			$config['file_name'] = time() . rand();
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('file')) {
				$data_info = $this->upload->data();
			} else {
				$this->session->set_flashdata('error', 'An error occured: ' . $this->upload->display_errors());
				redirect('inventory/stock_take');
			}
			$objRead = file_get_contents('uploads/' . $data_info['file_name']);
			$fields = explode("\n", $objRead);
			if (count($fields) <= 1) {
				$fields = explode("\r", $objRead);
			}
			$success = false;
			$stock_take = array();
			
           foreach ($fields as $key => $value) 
		   {
				if ($key == 0) continue;
				$value = explode(",", $value);						
				$item_exists = $this->shared_model->Lookup('stock', 'item_id', array('item_id' => $value[0]));			
							
				//echo "item_id" . $item_exists;
				//exit;
				//$stock_take['item_id'] = $this->shared_model->Lookup('stock', 'item_id', array('stock_num' => $stock_take['item_id']));

				if (!$item_exists) continue;
				if (!isset($value[11]) || !is_numeric($value[11])) continue;
				if ($value[11] === "") continue;
				 $stock_take = array('item_id' => $value['0'], 'date' => date('Y-m-d H:i:s'), 'user_id' => $this->session->userdata('user_id'), 'stock_take' => $value[11], 'reason' => $value[12]);
						
				$insert = $this->shared_model->insert('stock_take', $stock_take);
				$stock_take_id = mysql_insert_id();
				if ($insert) {
					$this->shared_model->execute('update stock_outlets set qty="' . $stock_take['stock_take'] . '" where item_id="' . $stock_take['item_id'] . '" and outlet_id="' . $this->session->userdata('outlet_id') . '"');
					$this->shared_model->execute('insert into stock_activity(item_id, quantity, reference_id, reference, balance, outlet_id) VALUES("' . $stock_take['item_id'] . '",0,"' . $stock_take_id . '","Stock Take","' . $stock_take['stock_take'] . '","' . $this->session->userdata('outlet_id') . '")');
					array_push($success, 'Stock take for item with stock code ' . $stock_code . ' is successfully saved.');
				} else {
					array_push($error, 'An error occurred when trying to save stock take for item with stock code ' . $stock_code . '. Please try again.');
				}					
            }
            if (!empty($error)) {
                $error = '<p>' . implode('</p><p>', $error) . '</p>';
                $error = rtrim($error, '<p></p>');
                $this->session->set_flashdata('error', $error);
            }
            if (!empty($success)) {
                $success = '<p>' . implode('</p><p>', $success) . '</p>';
                $success = rtrim($success, '<p></p>');
                $this->session->set_flashdata('success', $success);
            }
            redirect('inventory/stock_take');

        }

        public function itemReport() {
            $data = $this->data;
            $data['headline'] = 'Item Report';
            $data['subactive'] = 'item_report';
            $data['active'] = 'inventory';

            $data['breadcrumbs'] = array('Item Report' => '#');
            $this->load->view('inventory/item_report', $data);
        }
		
        public function oldItemReport() {
            $data = $this->data;
            $data['headline'] = 'Old Item Report';
            $data['subactive'] = 'old_item_report';
            $data['active'] = 'inventory';

            $data['breadcrumbs'] = array('Old Item Report' => '#');
            $this->load->view('inventory/old_item_report', $data);
        }
		
		public function getItemReport() {
            $item_report = json_decode($this->stock_model->getItemReport($this->input->get('date_from'), $this->input->get('date_to')), true);  
            echo json_encode($item_report);
        }
		
		public function getStockBalance() {			
            $stock_balance = json_decode($this->stock_model->getStockBalance($this->input->get('date_from'), $this->input->get('date_to'), $this->input->get('brand_filter'), $this->input->get('category_filter') ), true);  
			
			foreach ($stock_balance['aaData'] as $key => $res) {
                $stock_balance['aaData'][$key]['6'] = format_price($res['6']);
                $stock_balance['aaData'][$key]['7'] = format_price($res['7']);
                //$stock_take['aaData'][$key]['8'] = format_price($res['8']);
            }
			
            echo json_encode($stock_balance);
        }
		
		
        public function getOldItemReport() {
            $item_report = json_decode($this->stock_model->getOldItemReport($this->input->get('date_from'), $this->input->get('date_to')), true);

            foreach ($item_report['aaData'] as $key => $value) {
                $item_report['aaData'][$key][2] = format_time($value[2]);
                if (is_null($value[8]) || $value[8] <= 0) {
                    $item_report['aaData'][$key][8] = $this->stock_model->calculateCurrentBalance($value[2], $value[9], $this->session->userdata('outlet_id'));
                }
            }

            echo json_encode($item_report);
        }

        public function genBarCode($item_id) {
            $this->load->library('Barcode39');
            $data['itemDetails'] = $this->stock_model->getItemDetails($item_id);
            $barcode = new Barcode39($data['itemDetails']->barcode);
            // display new barcode
            $barcode->draw();
        }

        public function showBarcode($item_id) {
            $data['itemDetails'] = $this->stock_model->getItemDetails($item_id);
            $html_data = $this->load->view('inventory/barcode', $data);
        }

        public function addToCart() {
            if ($this->session->userdata('cart')) {
                $item_obj = $this->session->userdata('cart');
            } else {
                $item_obj = array();
            }

            $item['item_id'] = $this->input->post('product_id');
            $item['outlet_id'] = $this->input->post('outlet_id');
            $item['description'] = $this->shared_model->Lookup('stock', 'description', array('item_id' => $item['item_id']));
            $item['stock_num'] = $this->shared_model->Lookup('stock', 'stock_num', array('item_id' => $item['item_id']));
            $item['cost_price'] = $this->shared_model->Lookup('stock_outlets', 'cost_price', array('item_id' => $item['item_id'], 'outlet_id' => $item['outlet_id']));
            array_push($item_obj, $item);
            $this->session->set_userdata('cart', $item_obj);
            echo '<li data-rem="' . end(array_keys($this->session->userdata('cart'))) . '"><h6><a>' . $item['description'] . '</a><span class="pull-right removefromcart"><i class="fa fa-times"></i></span><span class="label label-warning pull-right">' . format_price($item['cost_price']) . '</span></h6><div class="clearfix"></div><hr></li>';
        }

        public function delFromCart() {
            $removekey = $this->input->post('removekey');
            $item_obj = $this->session->userdata('cart');
            unset($item_obj[$removekey]);
            $this->session->set_userdata('cart', $item_obj);
            echo 'Removed';
        }

        public function cartorder() {

            if ($this->session->userdata('cart')) {

                $data['orders'] = $this->session->userdata('cart');
                $outlets = array();
                $items = array();


                foreach ($data['orders'] as $key => $do) {
                    if (!in_array($do['outlet_id'], $outlets)) {
                        array_push($outlets, $do['outlet_id']);
                    }
                }

                foreach ($outlets as $keyo => $o) {

                    $po['date'] = date('Y-m-d');
                    $po['user_id'] = $this->session->userdata('user_id');
                    $po['outlet_id'] = $o;
                    $po['status'] = 'pending';
                    $po['active'] = 'Y';
                    $this->shared_model->insert('purchase_orders', $po);
                    log_db_query($this->db->last_query());  // Log DB Query
                    $po_id = $this->db->insert_id();

                    foreach ($data['orders'] as $keydo => $do) {
                        if ($do['outlet_id'] == $o) {
                            $do['cost'] = $do['cost_price'];
                            $do['quantity'] = 1;
                            $do['po_id'] = $po_id;
                            unset($do['description'], $do['stock_num'], $do['cost_price'], $do['outlet_id']);

                            if (!$this->shared_model->Lookup('po_items', 'po_id', array('po_id' => $po_id, 'item_id' => $do['item_id']))) {
                                $this->shared_model->insert('po_items', $do);
                                log_db_query($this->db->last_query());  // Log DB Query
                            } else {
                                $sql = 'Update po_items set quantity=quantity+1 where item_id=' . $do['item_id'] . ' AND po_id=' . $po_id;
                                $this->shared_model->execute($sql);
                                log_db_query($this->db->last_query());  // Log DB Query
                            }
                        }
                    }
                }

                $this->session->unset_userdata('cart');
                redirect('inventory/cartorderview');
            } else {
                $this->session->set_flashdata('error', 'Your cart is currently empty');
                redirect('inventory');
            }
        }

        public function cartorderview() {
            $data = $this->data;
            $data['headline'] = 'Purchase Orders';
            $data['subactive'] = 'iopo';
            $data['active'] = 'purchasing';
            $data['breadcrumbs'] = array('Purchase Orders' => '#');
            $data['porders'] = $this->stock_model->getInternalPurchaseOrders();

            $this->load->view('inventory/cartorder', $data);
        }

        public function deleteInterPurchase($id) {
            if ($id) {
                $sql = 'delete from purchase_orders where po_id="' . $id . '"';
                $this->shared_model->execute($sql);
                log_db_query($this->db->last_query());  // Log DB Query
            } else {
                $this->session->set_flashdata('error', 'Error occurred');
            }
            redirect('inventory/cartorderview');

        }

    }