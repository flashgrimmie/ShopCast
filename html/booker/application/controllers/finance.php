<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finance extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->data['active']='finance';
		$this->load->model('finance_model');
		$this->load->model('admin_model','dba');
	}

		public function general_ledger()
	{
		$date=$this->input->get('date') ? $this->input->get('date') : false;
		$data=$this->data;
		$petty_cash = 0;
		$data['headline']='General Ledger';
		$data['subactive']='general_ledger';
		$data['active']='finance';
		$data['breadcrumbs']=array('General Ledger'=>'#');
		$cash_sales=$this->finance_model->getCashTotal($date);
		$invoices=$this->finance_model->getInvoiceTotal($date);
		$returned_invoices=$this->finance_model->getInvoicesReturn($date);
		$returned_cs=$this->finance_model->getCashSalesReturn($date);
		$data['delivery_orders']=$this->finance_model->getDOTotal($date);
		$data['petty_cash'] = $this->finance_model->getAllPettyCash($date);
		$data['cash_sales']=$cash_sales+$returned_cs;
		$data['invoices']=$invoices+$returned_invoices;
		$data['returned_sales']=$returned_invoices+$returned_cs;
		$data['partial_payments']='';//$this->finance_model->getPartialPayments($date);
		$data['net_sales']=$cash_sales+$invoices+$data['partial_payments']+$data['delivery_orders'];
		$invoice_cost=$this->finance_model->getInvoicesCost($date);
		$cs_cost=$this->finance_model->getCSCost($date);
		$data['cost_sales']=$invoice_cost+$cs_cost+$data['delivery_orders'];
		$data['gross_profit']=$data['net_sales']-$data['cost_sales'];
		$data['recurring_fields']=$this->finance_model->getReccurringExpences($date);
		$data['recurring_expences']=$this->finance_model->getReccurringExpencesValues($date);
		$data['one_time_expences']=$this->finance_model->getOneTimeExpences($date);
		$data['outlet_expenses']=$this->finance_model->getOutletExpences($date);
		$data['purchase_total']=$this->finance_model->getPurchaseTotal($date);
		$data['one_time_total']=$this->finance_model->getOneTimeTotal($date);
		$data['recurring_total']=0;
		foreach($data['recurring_fields'] as $value) {
			$data['recurring_total']+=$data['recurring_expences']->$value;
		}
		$data['net_profit']=$data['gross_profit']-($data['purchase_total']+$data['one_time_total']+$data['recurring_total']);
		$this->load->helper('inflector');
		$this->load->view('finance/general_ledger',$data);
	}

	// public function daily_balance()
	// {
	// 	$date=$this->input->get('date') ? $this->input->get('date') : date('Y-m-d');
	// 	$data=$this->data;
	// 	$data['headline']='Daily Balance';
	// 	$data['subactive']='daily';
	// 	$data['active']='finance';
	// 	$data['breadcrumbs']=array('Daily Balance'=>'#');
	// 	$invoices=$this->finance_model->getDailyInvoices($date);
	// 	$cash_sales=$this->finance_model->getDailyCS($date);
	// 	$data['partial_payments']=$this->finance_model->getPartialPayments($date);
	// 	$data['delivery_orders']=$this->finance_model->getDOTotal($date);
	// 	$data['returned_cs']=$this->finance_model->getCashSalesReturn($date);
	// 	$data['returned_invoices']=$this->finance_model->getInvoicesReturn($date);
	// 	$data['cash_sales']=$cash_sales+$data['returned_cs'];
	// 	$data['invoices']=$invoices+$data['returned_invoices'];
	// 	$data['returned_sales']=$data['returned_invoices']+$data['returned_cs'];
	// 	$data['net_profit']=$invoices+$cash_sales+$data['partial_payments'];
	// 	$this->load->view('finance/daily_balance',$data);
	// }

public function daily_balance()
	{
		$date=$this->input->get('date') ? $this->input->get('date') : date('Y-m-d');
		$data=$this->data;
		$data['headline']='Daily Balance';
		$data['subactive']='daily';
		$data['active']='finance';
		$data['breadcrumbs']=array('Daily Balance'=>'#');
		$invoices=$this->finance_model->getDailyInvoices($date);
		$cash_sales=$this->finance_model->getDailyCS($date);
		$data['partial_payments']=$this->finance_model->getPartialPayments($date);
		$data['delivery_orders']=$this->finance_model->getDOTotal($date);

		//The section is written for returned items
		$data['returned_items']=$this->dba->get_records('returned_items',array("DATE_FORMAT(cn_date,'%Y-%m-%d')"=>$date,'outlet_id'=>$this->session->userdata('outlet_id')));
		$returned_cash_sale_sum=0;
		foreach($data['returned_items'] as $row)
		{
			$check=unserialize($row->cn_items);
			if(is_array($check))
			{
				foreach($check as $row1)
				{
					$returned_cash_sale_sum=$returned_cash_sale_sum+($row1['qty']*$row1['price']);
				}
			}
		}
		$data['returned_cs']=$returned_cash_sale_sum;
		//The section is written for credit_notes @table returned_cn
		$data['returned_credit_note']=$this->dba->get_records('returned_cn',array("DATE_FORMAT(cn_date,'%Y-%m-%d')"=>$date,'outlet_id'=>$this->session->userdata('outlet_id')));
		$returned_credit_note_sum=0;
		foreach($data['returned_credit_note'] as $row)
		{
			$check=unserialize($row->cn_items);
			if(is_array($check))
			{
				foreach($check as $row1)
				{
					$returned_credit_note_sum=$returned_credit_note_sum+($row1['qty']*$row1['price']);
				}
			}
		}
		$data['returned_cn']=$returned_credit_note_sum;

		$data['cash_sales']=$cash_sales;
		$data['invoices']=$invoices;
		
		$data['returned_sales']=$data['returned_cn']+$data['returned_cs'];
		$data['net_profit']=($data['invoices']+$data['partial_payments']+$data['cash_sales'])-$data['returned_sales'];

		$this->load->view('finance/daily_balance',$data);

	}

	public function debtors()
	{
		$data=$this->data;
		$data['headline']='Debtors';
		$data['subactive']='debtors';
		$data['active']='finance';
		$data['breadcrumbs']=array('Debtors'=>'#');
		$this->load->view('finance/debtors',$data);
	}

	public function getDebtors() {
		$debtors=$this->finance_model->getDebtors();

		$debtors = json_decode($debtors, true);

		foreach ($debtors['aaData'] as $key=>$res) {
            $debtors['aaData'][$key]['1']=format_price($res['1']);
            if($res['2']!='Old') {
	            $debtors['aaData'][$key]['2']=format_date($res['2']);
            }
        }

        $debtors=$this->shared_model->JEncode($debtors);
		echo $debtors;
	}

	public function add_debtor($id=false)
	{
		$data=$this->data;
		$data['headline']='Add Debtor';
		$data['subactive']='debtors';
		$data['active']='finance';
		$data['breadcrumbs']=array('Debtors'=>'#');
		if($id) {
			$data['debtor']=$this->shared_model->getRow('select * from customers join debtors using(customer_id)');
		}
		$this->load->view('finance/add_debtor',$data);
	}

	public function save_debtor($id=false)
	{
		$debtor=$this->shared_model->getPost('debtors');
		$debtor['user_id']=$this->session->userdata('user_id');
		if(!$id) {
			$success=$this->shared_model->insert('debtors',$debtor);
		} else {
			$success=$this->shared_model->update('debtors','debtor_id',$id,$debtor);
		}

		if($success) {
            log_db_query($this->db->last_query());  // Log DB Query
			$this->session->set_userdata('success','Entry is successfully saved.');
		} else {
			$this->session->set_userdata('error','An error occurred or no changes have been made. Please try again.');
		}
		redirect('finance/debtors');
	}

	public function creditors()
	{
		$data=$this->data;
		$data['headline']='Creditors';
		$data['subactive']='creditors';
		$data['active']='finance';

		$data['breadcrumbs']=array('Creditors'=>'#');
		$this->load->view('finance/creditors',$data);
	}

	public function getCreditors() {
		$creditors=$this->finance_model->getCreditors();

		$creditors = json_decode($creditors, true);

		foreach ($creditors['aaData'] as $key=>$res) {
            $creditors['aaData'][$key]['1']=format_price($res['1']);
            $creditors['aaData'][$key]['2']=format_date($res['2']);
        }

        $creditors=$this->shared_model->JEncode($creditors);
		echo $creditors;
	}

	public function recurring_expenses($month='',$year='')
	{

		$data=$this->data;
		$data['headline']='Recuring Expenses';
		$data['subactive']='recurring';
		$data['active']='finance';

		$data['breadcrumbs']=array('Recuring Expenses'=>'#');

		$display_date=date('M, Y',time());
		
		if($month!=''&&$year!='')
		{
			$tmp_date=$month.'/01/'.$year;
			$display_date=date('M, Y',strtotime($tmp_date));
		}

		$data['expenses']=$this->finance_model->recurringExpenses($month,$year);
		$data['fields']=$this->finance_model->recurringFields();
		$data['total_cost']=$this->finance_model->recurringTotal($month,$year);

		for($i=1; $i<=12; $i++) {
			$data['recurring_exp'][$i]=$this->finance_model->recurringExpenses($i,$year);
		}

		$this->load->view('finance/recurring_expenses',$data);
	}

	public function add_recurring()
	{
		$this->finance_model->addRecurringExpense($this->input->post('column'));
        log_db_query($this->db->last_query());  // Log DB Query
		redirect('finance/recurring_expenses');
	}

	public function insert_recurring()
	{
		$fields=$this->finance_model->recurringFields();
		$insert_string='';
		$insert=array();

		foreach($fields as $key=>$field) {
			$insert[]=$this->input->post('recuring_'.$key)!=''?$this->input->post('recuring_'.$key):'NULL';
		}

		$insert[]=$this->session->userdata('outlet_id');
		$insert_string=implode(',',$insert);
		$this->finance_model->insertRecurringExpenses($insert_string);
        log_db_query($this->db->last_query());  // Log DB Query
		redirect('finance/recurring_expenses');
		
	}

	public function delete_recurring($column)
	{	
		if($column){
			$this->finance_model->deleteRecurringExpense($column);
            log_db_query($this->db->last_query());  // Log DB Query
		} else {
			$this->set_flashdata('error','Invalid Data. Please try again later');
		}
		redirect('finance/delete_recurring');
	}

	public function otherPurchases()
	{
		$data=$this->data;
		$data['headline']='Other Purchases';
		$data['subactive']='other_purchases';
		$data['active']='finance';

		$data['breadcrumbs']=array('Other Purchases'=>'#');
		$this->load->view('finance/other_purchases',$data);
	}

	public function getOtherPurchases()
	{
		$otherPurchases=$this->finance_model->getOtherPurchases();
		$otherPurchases = json_decode($otherPurchases, true);

		foreach ($otherPurchases['aaData'] as $key=>$res) {
            $otherPurchases['aaData'][$key]['1']=format_price($res['1']);
            $otherPurchases['aaData'][$key]['2']=format_date($res['2']);
        }

        $otherPurchases=$this->shared_model->JEncode($otherPurchases);
		echo $otherPurchases;
	}

	public function insert_purchase()
	{
		$purchase=$this->shared_model->getPost('outlet_purchases');
		$purchase['outlet_id']=$this->session->userdata('outlet_id');

		$config['upload_path'] = 'uploads/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['file_name']	= time().rand(100,900);

		$this->load->library('upload', $config);

		if($this->upload->do_upload('image'))
		{
			$uploadData=$this->upload->data();
			$purchase['image']=$uploadData['file_name'];
		} else {
			$this->session->set_flashdata(array('error'=>'Upload file problem!'));
			redirect('finance/otherPurchases');
		}


		$inserted=$this->shared_model->insert('outlet_purchases',$purchase);

		if($inserted){
            log_db_query($this->db->last_query());  // Log DB Query
			$this->session->set_flashdata(array('success'=>'This entry was successfully insterted'));
		} else {
			$this->session->set_flashdata(array('error'=>'Error Occured'));
		}

		redirect('finance/otherPurchases');
	}

	public function delete_purchase($id)
	{
		$deleted=$this->shared_model->delete('outlet_purchases','id',$id);

		if($deleted){
            log_db_query($this->db->last_query());  // Log DB Query
			$this->session->set_flashdata(array('success'=>'Successfully deleted entry'));
		} else {
			$this->session->set_flashdata(array('error'=>'Error'));
		}

		redirect('reports/other_purchases');
	}

	public function stockPurchases()
	{
		$data=$this->data;
		$data['headline']='Stock Purchases';
		$data['subactive']='stock_purchases';
		$data['active']='finance';

		$data['breadcrumbs']=array('Stock Purchases'=>'#');
		$this->load->view('finance/stock_purchases',$data);
	}

	public function getStockPurchases() 
	{
		$stockPurchases=$this->finance_model->getStockPurchases();
		$stockPurchases = json_decode($stockPurchases, true);

		foreach ($stockPurchases['aaData'] as $key=>$res) {
            $stockPurchases['aaData'][$key]['3']=format_price($res['1']*$res['2']);
            $stockPurchases['aaData'][$key]['2']=format_price($res['2']);
            $stockPurchases['aaData'][$key]['4']=format_date($res['3']);
            $stockPurchases['aaData'][$key]['5']=$res['4'];

        }

        $stockPurchases=$this->shared_model->JEncode($stockPurchases);
		echo $stockPurchases;
	}

public function petty_cash()
	{
		$data=$this->data;
		$data['headline']='Petty Cash';
		$data['subactive']='petty_cash';
		$data['active']='finance';
		$date=date('Y-m');
		$data['petty_cash_amount']=$this->shared_model->getRow('select * from petty_cash_amounts where date like "'.$date.'-%"');
		$data['date_filter'] = '';
		if ($this->input->post('month') != '') {
			$data['date_filter'] = $this->input->post('month');
			$date = $this->input->post('month');
			$data['cash_total']=format_price($this->finance_model->getPettySum($date));
			$data['petty_cash_amount']=$this->shared_model->getRow('select * from petty_cash_amounts where date like "'.$date.'-%"');
		}
		else{
			$data['cash_total']=format_price($this->finance_model->getPettySum());
		}
		$data['breadcrumbs']=array('Finance'=>'#','Petty Cash'=>'#');
		$this->load->view('finance/petty_cash',$data);
	}

	public function getPettyCash($date=false)
	{
		if ($this->input->post('date')){
			$petty_cash=$this->finance_model->getAllPettyCashDate($this->input->post('date'));
		}else{
			$petty_cash=$this->finance_model->getPettyCash();
		}
		
		$petty_cash = json_decode($petty_cash, true);

		foreach ($petty_cash['aaData'] as $key=>$res) {
            $petty_cash['aaData'][$key]['1']=format_price($res['1']);
            $petty_cash['aaData'][$key]['3']=format_date($res['3']);
            $petty_cash['aaData'][$key]['4']=format_price($res['4']);
			$petty_cash['aaData'][$key]['9']=$res['1'];
        }

        $petty_cash=$this->shared_model->JEncode($petty_cash);
		echo $petty_cash;
	}
	
	public function getAllPettyCash($date=false)
	{
		$petty_cash=$this->finance_model->getAllPettyCashDate();
		$petty_cash = json_decode($petty_cash, true);

		foreach ($petty_cash['aaData'] as $key=>$res) {
            $petty_cash['aaData'][$key]['1']=format_price($res['1']);
            $petty_cash['aaData'][$key]['3']=format_date($res['3']);
            $petty_cash['aaData'][$key]['4']=format_price($res['4']);
        }

        $petty_cash=$this->shared_model->JEncode($petty_cash);
		echo $petty_cash;
	}

	public function petty_cash_history() 
	{
		$data=$this->data;
		$data['headline']='Petty Cash History';
		$data['subactive']='petty_cash';
		$data['active']='finance';
		$data['breadcrumbs']=array('Finance'=>'#','Petty Cash History'=>'#');
		$this->load->view('finance/petty_cash_history',$data);
	}

	public function getPettyCashHistory()
	{
		$petty_cash=$this->finance_model->getPettyCashHistory();
		$petty_cash = json_decode($petty_cash, true);

		foreach ($petty_cash['aaData'] as $key=>$res) {
            $petty_cash['aaData'][$key]['0']=format_date($res['0']);
            $petty_cash['aaData'][$key]['2']=format_price($res['2']);
            $petty_cash['aaData'][$key]['3']=format_price($res['3']);
        }

        $petty_cash=$this->shared_model->JEncode($petty_cash);
		echo $petty_cash;
	}

		public function setPettyCashAmount()
	{
		echo $date=date('Y-m', strtotime('last month'));//date('Y').'-'.str_pad((date('m')-1),2,0,STR_PAD_LEFT);
		$outlet_id=$this->session->userdata('outlet_id');
		$petty_details=$this->shared_model->getRow('select * from petty_cash_amounts join users using(user_id) where outlet_id="'.$outlet_id.'" and date like "'.date('Y-m').'-%"');
		$petty_cash=$this->shared_model->getPost('petty_cash_amounts');
		$petty_cash['date']=date('Y-m-d h:i:s');
		$petty_cash['user_id']=$this->session->userdata('user_id');
		if($petty_details) {
			echo $petty_cash['balance']=$petty_details->balance-$petty_details->value+$petty_cash['value'];
			$this->shared_model->update('petty_cash_amounts','id',$petty_details->id,$petty_cash);
            log_db_query($this->db->last_query());  // Log DB Query
		} else {
			$balance=$this->shared_model->getRow('select petty_cash_amounts.* from petty_cash_amounts join users using(user_id) where outlet_id="'.$outlet_id.'" and date like "'.$date.'-%"');
			$current_expense_check=$this->dba->get_sum('petty_cash','value',array('date like'=>date('Y-m').'-%'));

			$balance = $balance ? $balance->balance : 0;
			$petty_cash['balance']=$balance+$petty_cash['value']-$current_expense_check;
			$petty_cash['value']=$petty_cash['value']+$balance;
			$petty_cash['current_month_amount']=$this->input->post('value');
			$petty_cash['previous_balance']=$balance;
			$this->shared_model->insert('petty_cash_amounts',$petty_cash);
            log_db_query($this->db->last_query());  // Log DB Query
		}
		$this->session->set_flashdata('success','Petty Cash Amount is successfully saved');
		redirect('finance/petty_cash');
	}
	public function	topUpPettyCash()
	{
		if ($this->input->post('value') != "") {
			$this->db->trans_begin();
			$user_id=$this->session->userdata('user_id');
			$outlet_id=$this->session->userdata('outlet_id');
			$petty_details=$this->shared_model->getRow('select * from petty_cash_amounts join users using(user_id) where outlet_id="'.$outlet_id.'" and date like "'.date('Y-m').'-%"');

			$data = array(
				'user_id' => $user_id,
				'amount' => $this->input->post('value'),
				'type' => $this->input->post('payment_type'),
				'petty_cash_amount_id'=>$petty_details->id,
			);
			$query = $this->dba->add_record('petty_cash_topup', $data);
			$previous_amount=$petty_details->value+$this->input->post('value');
			$previous_balance=$petty_details->balance+$this->input->post('value');

			$this->dba->update_record('petty_cash_amounts',array('value'=>$previous_amount,'balance'=>$previous_balance),array('id'=>$petty_details->id));

			if($this->db->trans_status()===FALSE)
			{
				$this->db->trans_rollback();
					$this->session->set_flashdata('success','Sorry Please Try again');
					redirect('finance/petty_cash');
			}
			else{
				$this->db->trans_commit();
				$this->session->set_flashdata('success','Petty cash amount has been toped up');
				redirect('finance/petty_cash');
			}

			}
	}

	public function insert_petty_cash()
	{
		$expense=$this->shared_model->getPost('petty_cash');
		$expense['user_id']=$this->session->userdata('user_id');
		$expense['date']=date('Y-m-d h:i:s');
		$inserted=$this->shared_model->insert('petty_cash',$expense);
		$outlet_id=$this->session->userdata('outlet_id');

		if($inserted){
            log_db_query($this->db->last_query());  // Log DB Query
			$date=date('Y-m');

			$sql='update petty_cash_amounts set balance=balance-'.$expense['value'].' where user_id IN(SELECT user_id FROM users WHERE outlet_id="'.$outlet_id.'") AND petty_cash_amounts.date LIKE "'.$date.'-%"';
			$petty_cash=$this->shared_model->execute($sql);
            log_db_query($this->db->last_query());  // Log DB Query
			$this->session->set_flashdata(array('success'=>'This entry was successfully saved'));
		} else {
			$this->session->set_flashdata(array('error'=>'Error. Please try again.'));
		}
		redirect('finance/petty_cash');
	}

	public function delete_petty_cash($id)
	{
		$outlet_id=$this->session->userdata('outlet_id');
		$petty_details=$this->shared_model->getRow('select * from petty_cash where id="'.$id.'"');
		$date=date('Y-m',strtotime($petty_details->date));
		$sql='update petty_cash_amounts set balance=balance+'.$petty_details->value.' where user_id IN(SELECT user_id FROM users WHERE outlet_id="'.$outlet_id.'") AND petty_cash_amounts.date LIKE "'.$date.'-%"';
		$delete=$this->shared_model->delete('petty_cash','id',$id);
		if($delete) {
            log_db_query($this->db->last_query());  // Log DB Query
			$this->shared_model->execute($sql);
            log_db_query($this->db->last_query());  // Log DB Query
			$this->session->set_flashdata(array('success'=>'This entry was successfully deleted.'));
		} else {
			$this->session->set_flashdata(array('error'=>'Error. Please try again.'));
		}
		redirect('finance/petty_cash');
	}

	public function oneTimeExpenses()
	{
		$data=$this->data;
		$data['headline']='One Time Expenses';
		$data['subactive']='onetime';
		$data['active']='finance';

		$data['breadcrumbs']=array('One Time Expenses'=>'#');
		$this->load->view('finance/one_time_expenses',$data);
	}

	public function getOneTimeExpenses()
	{
		$oneTimeExpenses=$this->finance_model->getOneTimeExpenses();
		$oneTimeExpenses = json_decode($oneTimeExpenses, true);

		foreach ($oneTimeExpenses['aaData'] as $key=>$res) {
            $oneTimeExpenses['aaData'][$key]['1']=format_price($res['1']);
            $oneTimeExpenses['aaData'][$key]['2']=format_date($res['2']);
        }

        $oneTimeExpenses=$this->shared_model->JEncode($oneTimeExpenses);
		echo $oneTimeExpenses;
	}

	public function insert_one_time()
	{
		$expense=$this->shared_model->getPost('one_time_expenses');
		$expense['outlet_id']=$this->session->userdata('outlet_id');
		$inserted=$this->shared_model->insert('one_time_expenses',$expense);

		if($inserted){
            log_db_query($this->db->last_query());  // Log DB Query
			$this->session->set_flashdata(array('success'=>'This entry was successfully insterted'));
		} else {
			$this->session->set_flashdata(array('error'=>'Error occuerd'));
		}
		redirect('finance/oneTimeExpenses');
	}

	public function delete_one_time($id)
	{
		$deleted=$this->shared_model->delete('one_time_expenses','id',$id);

		if($deleted){
            log_db_query($this->db->last_query());  // Log DB Query
			$this->session->set_flashdata(array('success'=>'This entry was successfully deleted'));
		} else {
			$this->session->set_flashdata(array('error'=>'Error occuerd'));
		}

		redirect('finance/oneTimeExpenses');
	}



	
	

}