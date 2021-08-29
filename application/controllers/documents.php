<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Documents extends MY_Controller {

	function __construct()
    {
        parent::__construct();

		$this->load->model('document_model');
		//$this->load->model('statistics_model');
		 $this->load->model('retail_model');
		$this->load->model('setup_model');
		$this->load->helper('pdf');
    }

	public function invoice($invoice_id)
	{
		$data['info']=$this->document_model->invoiceDetails($invoice_id);
		$data['mechanics']=unserialize($data['info']['mechanics']);
		$data['info']['mech_total']=0;
		foreach ($data['mechanics'] as $key => $value) {
			$data['info']['mech_total']+=$value;
		}
		$data['i_items']=$this->document_model->invoiceItems($invoice_id);
		$data['i_bal']=$this->document_model->invoiceOpening($invoice_id);
		$data['outlet'] = $this->shared_model->getRow('select * from outlets where outlet_id="'.$this->session->userdata('outlet_id').'"');
		 $html_data= $this->load->view('documents/invoice',$data, true);
		 create_pdf($html_data);
	}

    public function credit_note( $cn_id ){
        $credit_note = $this->document_model->getCredits( $cn_id );
        $items = unserialize($credit_note->cn_items );

        for( $i = 0; $i < count( $items ); $i++ ){
            $item_id = $items[$i]['item_id'];
            $item = $this->document_model->getItem($item_id);
            $items[$i]['stock_num'] = $item->stock_num;
            $items[$i]['description'] = $item->description;
        }

        $id = $credit_note->customer_id;
        $data['cn_no'] = $cn_id;
        $data['date_issue'] = $credit_note->cn_date;
        $data['customer'] = $this->document_model->getCustomerDetails($id);
        $data['items'] = $items;
        $data['remark'] = $credit_note->remarks;
		$data['outlet'] = $this->shared_model->getRow('select * from outlets where outlet_id="'.$this->session->userdata('outlet_id').'"');
        $html_data = $this->load->view('documents/credit_note', $data, true);
        create_pdf($html_data);
    }
		public function returns_notes( $cn_id ){
		$credit_note = $this->document_model->getReturnItem( $cn_id );
		$items = unserialize($credit_note->cn_items );

		for( $i = 0; $i < count( $items ); $i++ ){
			$item_id = $items[$i]['item_id'];
			$item = $this->document_model->getItem($item_id);
			$items[$i]['stock_num'] = $item->stock_num;
			$items[$i]['description'] = $item->description;
		}

		$id = $credit_note->customer_id;
		$data['cn_no'] = $cn_id;
		$data['date_issue'] = $credit_note->cn_date;
		$data['customer'] = $this->document_model->getCustomerDetails($id);
		$data['items'] = $items;
		$data['remark'] = $credit_note->remarks;
		$data['outlet'] = $this->shared_model->getRow('select * from outlets where outlet_id="'.$this->session->userdata('outlet_id').'"');
		$html_data = $this->load->view('documents/returns_notes', $data, true);
		create_pdf($html_data);
	}

	public function customer_statement($customer_id,$date=false)
	{
        $qdate = new DateTime($date);
        $qdate->modify('last day of this month');
        $qdate->setTime(23,59, 59);
		$data['customer_details'] = $this->shared_model->getRow('select * from customers where customer_id="'.$customer_id.'"');
		$data['items'] = $this->document_model->statementItems($customer_id,$date);
        $data['payments'] = $this->document_model->customerPayments($customer_id, $qdate->format('Y-m-d H:i:s'));
		$data['outlet'] = $this->shared_model->getRow('select * from outlets where outlet_id="'.$this->session->userdata('outlet_id').'"');
        $data['statement_date'] = $date;
		//print_r($data['customer_details']);
		//print_r($data['outlet']);
		//exit;
		$html_data = $this->load->view('documents/customer_statement',$data, true );
		create_pdf($html_data);
	}

	public function cash_sale($cs_id)
	{
		$data['info']=$this->document_model->csDetails($cs_id);
		$data['i_items']=$this->document_model->csItems($cs_id);
		$data['outlet'] = $this->shared_model->getRow('select * from outlets where outlet_id="'.$this->session->userdata('outlet_id').'"');
		$html_data= $this->load->view('documents/cash_sale',$data, true);
		create_pdf($html_data);
	}

	public function cash_sale_v2($cs_id)
	{
		$data['info']=$this->document_model->csDetails($cs_id);
		$data['i_items']=$this->document_model->csItems($cs_id);
		$data['outlet'] = $this->shared_model->getRow('select * from outlets where outlet_id="'.$this->session->userdata('outlet_id').'"');
		$html_data= $this->load->view('documents/cash_sale_v2',$data, true);
		create_pdf_letter($html_data);
	}

	public function sales_order($so_id)
	{
		$data['info']=$this->document_model->soDetails($so_id);
		$data['mechanics']=unserialize($data['info']['mechanics']);
		$data['info']['mech_total']=0;
		foreach ($data['mechanics'] as $key => $value) {
			$data['info']['mech_total']+=$value;
		}
		$data['i_items']=$this->document_model->soItems($so_id);
		$data['outlet'] = $this->shared_model->getRow('select * from outlets where outlet_id="'.$this->session->userdata('outlet_id').'"');
		$html_data=$this->load->view('documents/sales_order',$data, true);
		create_pdf($html_data);
	}

	public function delivery_order($do_id)
	{
		$sql="UPDATE delivery_orders SET notify='N' where do_id='".$do_id."' and outlet_id='".$this->session->userdata('outlet_id')."'";
		$this->shared_model->execute($sql);
		$data['info']=$this->document_model->doDetails($do_id);
		$data['i_items']=$this->document_model->doItems($do_id);
		$data['outlet'] = $this->shared_model->getRow('select * from outlets where outlet_id="'.$this->session->userdata('outlet_id').'"');
		$html_data=$this->load->view('documents/delivery_order',$data, true);
		create_pdf($html_data);
	}

	public function purchase_order($po_id)
	{
		$data['info']=$this->document_model->poDetails($po_id);
		$data['i_items']=$this->document_model->poItems($po_id);
		$data['outlet'] = $this->shared_model->getRow('select * from outlets where outlet_id="'.$this->session->userdata('outlet_id').'"');
		$html_data=$this->load->view('documents/purchase_order',$data, true);
		create_pdf($html_data);
	}

	public function inner_purchase_order($po_id)
	{
		$this->shared_model->update('purchase_orders', 'po_id',$po_id, array('notify'=>'N'));
		$data['info']=$this->document_model->iopoDetails($po_id);
		$data['i_items']=$this->document_model->poItems($po_id);
		$data['outlet'] = $this->shared_model->getRow('select * from outlets where outlet_id="'.$this->session->userdata('outlet_id').'"');
		$html_data=$this->load->view('documents/purchase_order',$data, true);
		create_pdf($html_data);
	}

	public function purchase_invoice($pi_id)
	{
		$data['info']=$this->document_model->piDetails($pi_id);
		$data['i_items']=$this->document_model->piItems($pi_id);
		$data['info']['landed_cost']=($data['info']['subtotal']+$data['info']['additional_expenses']);
		$data['outlet'] = $this->shared_model->getRow('select * from outlets where outlet_id="'.$this->session->userdata('outlet_id').'"');
		$html_data=$this->load->view('documents/purchase_invoice',$data, true);
		create_pdf($html_data);
	}
	
	public function purchase_return($rpi_id)
	{
		$data['info']=$this->document_model->rpiDetails($rpi_id);
		$data['i_items']=$this->document_model->rpiItems($rpi_id);
		if(!isset($data['info']['conversion']) || !$data['info']['conversion']) {
			$data['info']['conversion']=1;
			$data['info']['currency']='$';
		}
		$html_data=$this->load->view('documents/purchase_return',$data, true);
		create_pdf($html_data);
	}

	public function general_ledger()
	{
		$date=$this->input->get('date') ? $this->input->get('date') : false;
		$this->load->model('finance_model');
		$cash_sales=$this->finance_model->getCashTotal($date);
		$invoices=$this->finance_model->getInvoiceTotal($date);
		$returned_invoices=$this->finance_model->getInvoicesReturn($date);
		$returned_cs=$this->finance_model->getCashSalesReturn($date);
		$data['delivery_orders']=$this->finance_model->getDOTotal($date);
		$data['cash_sales']=$cash_sales+$returned_cs;
		$data['invoices']=$invoices+$returned_invoices;
		$data['returned_sales']=$returned_invoices+$returned_cs;
		$data['partial_payments']=$this->finance_model->getPartialPayments($date);
		$data['net_sales']=$cash_sales+$invoices+$data['delivery_orders']+$data['partial_payments'];
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
		$html_data=$this->load->view('documents/general_ledger',$data, true);
		create_pdf($html_data);
	}

	public function balance_sheet()
	{
		$date=$this->input->get('date') ? $this->input->get('date') : false;
		$this->load->model('finance_model');
		$data['cash_sales']=$this->finance_model->getCashTotal($date);
		$data['invoices']=$this->finance_model->getInvoiceTotal($date);
		$data['delivery_orders']=$this->finance_model->getDOTotal($date);
		$data['total_receipt']=$data['cash_sales']+$data['invoices']+$data['delivery_orders'];
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
		$data['purchase_invoices']=$this->finance_model->getPurchaseInvoices($date);
		$data['pi_total']=$this->finance_model->getPITotal($date);
		$data['balance']=$data['total_receipt']-($data['purchase_total']+$data['one_time_total']+$data['recurring_total']+$data['pi_total']);
		$this->load->helper('inflector');
		$html_data=$this->load->view('documents/balance_sheet',$data, true);
		create_pdf($html_data);
	}

	public function pinvoice($po_id)
	{

		$data['pi_info']=$this->document_model->piDetails($po_id);
		$data['pi_items']=$this->document_model->piItems($po_id);

		$html_data=$this->load->view('documents/pinvoice',$data,true);
		create_pdf($html_data);

	}


	public function approval($aid)
	{
		$data['approval']=$this->document_model->getApproval($aid);
		$html_data=$this->load->view('documents/approval',$data,true);
		create_pdf($html_data);

	}

	public function yreport($month='',$year='')
	{
		$display_date=date('M, Y',time());

		if($month!=''&&$year!='')
		{
			$tmp_date=$month.'/01/'.$year;
			$display_date=date('M, Y',strtotime($tmp_date));
		}

		$data['months']=array('','Jan', 'Feb', 'Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$data['cyear']=date('Y',time());
		$data['one_time_expenses']=$this->statistics_model->oneTimeTotal($month,$year);
		for($i=1; $i<=12; $i++) {
			$data['one_time'][$i]=$this->statistics_model->oneTimeTotal($i,$year);
		}

		//Get Recurring Expenses
		$data['recurring_expenses']=$this->statistics_model->recurringTotal($month,$year);
		for($i=1; $i<=12; $i++) {
			$data['recurring'][$i]=$this->statistics_model->recurringTotal($i,$year);
		}

		//Get Stock Expenses
		$data['stock_purchases']=$this->statistics_model->stockTotalDue($month,$year);
		for($i=1; $i<=12; $i++) {
			$data['stock'][$i]=$this->statistics_model->stockTotalDue($i,$year);
		}

		//Get Other Expenses
		$data['other_purchases']=$this->statistics_model->otherPurchasesTotal($month,$year);
		for($i=1; $i<=12; $i++) {
			$data['other'][$i]=$this->statistics_model->otherPurchasesTotal($i,$year);
		}

		//Get Invocies Total
		$data['invoices_toatl']=$this->statistics_model->monthlyInvoicesTotal($month,$year);
		for($i=1; $i<=12; $i++) {
			$data['invoices'][$i]=$this->statistics_model->monthlyInvoicesTotal($i,$year);
		}


		$data['total']=$data['invoices_toatl']-$data['one_time_expenses']-$data['recurring_expenses']-$data['stock_purchases']-$data['other_purchases'];

		for($i=1; $i<=12; $i++) {
			$data['total_m'][$i]=$data['invoices'][$i]-$data['one_time'][$i]-$data['recurring'][$i]-$data['stock'][$i]-$data['other'][$i];
		}

		$html_data=$this->load->view('documents/report',$data, true);
		create_pdf_l($html_data);
	}

	public function yreport_cash($month='',$year='')
	{
		if($this->session->userdata('user_type')=='I') {
			show_404();
		}
		$display_date=date('M, Y',time());

		if($month!=''&&$year!='')
		{
			$tmp_date=$month.'/01/'.$year;
			$display_date=date('M, Y',strtotime($tmp_date));
		}

		$data['months']=array('','Jan', 'Feb', 'Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$data['cyear']=date('Y',time());
		$data['one_time_expenses']=$this->statistics_model->oneTimeTotal($month,$year);
		for($i=1; $i<=12; $i++) {
			$data['one_time'][$i]=$this->statistics_model->oneTimeTotal($i,$year);
		}

		//Get Recurring Expenses
		$data['recurring_expenses']=$this->statistics_model->recurringTotal($month,$year);
		for($i=1; $i<=12; $i++) {
			$data['recurring'][$i]=$this->statistics_model->recurringTotal($i,$year);
		}

		//Get Stock Expenses
		$data['stock_purchases']=$this->statistics_model->stockTotalDue($month,$year);
		for($i=1; $i<=12; $i++) {
			$data['stock'][$i]=$this->statistics_model->stockTotalDue($i,$year);
		}

		//Get Other Expenses
		$data['other_purchases']=$this->statistics_model->otherPurchasesTotal($month,$year);
		for($i=1; $i<=12; $i++) {
			$data['other'][$i]=$this->statistics_model->otherPurchasesTotal($i,$year);
		}

		//Get Invocies Total
		$data['invoices_toatl']=$this->statistics_model->monthlyInvoicesTotal($month,$year);
		for($i=1; $i<=12; $i++) {
			$data['invoices'][$i]=$this->statistics_model->monthlyInvoicesTotal($i,$year);
		}

		//Get Cash Total
		$data['cash_total']=$this->statistics_model->monthlyCSTotal($month,$year);
		for($i=1; $i<=12; $i++) {
			$data['cash'][$i]=$this->statistics_model->monthlyCSTotal($i,$year);
		}


		$data['total']=$data['invoices_toatl']+$data['cash_total']-$data['one_time_expenses']-$data['recurring_expenses']-$data['stock_purchases']-$data['other_purchases'];

		for($i=1; $i<=12; $i++) {
			$data['total_m'][$i]=$data['invoices'][$i]+$data['cash'][$i]-$data['one_time'][$i]-$data['recurring'][$i]-$data['stock'][$i]-$data['other'][$i];
		}

		$html_data=$this->load->view('documents/report_cash',$data, true);
		create_pdf_l($html_data);
	}

	public function dealerreport($did) {
		$data['invoices']=$this->document_model->dealerDetails($did);
		$data['dealer']=$this->setup_model->outletDetails($did);

		$html_data=$this->load->view('documents/dealerreport',$data, true);
		create_pdf_l($html_data);
	}


	public function customerreport($cid) {
		$data['cus_invoices']=$this->document_model->custemerDetails($cid);

		//var_dump($data['cus_invoices'][0]); exit;
		$html_data=$this->load->view('documents/cusreport',$data, true);
		create_pdf_l($html_data);
	}

	public function stockreport($oid){
		$data['stockreport']=$this->document_model->stockStatus($oid);
		$html_data=$this->load->view('documents/stockreport',$data, true);
		create_pdf_l($html_data);

	}

	public function dealerstock($did) {
		$data['stockreport']=$this->document_model->dealerStatus($did);
		$html_data=$this->load->view('documents/dealerstock',$data, true);
		create_pdf_l($html_data);
	}

	public function assignments($aid) {

		$data['do_items']=$this->stock_model->getDoItems($aid);
		$data['do_details']=$this->stock_model->doDetails($aid);
		$html_data=$this->load->view('documents/admission',$data, true);
		create_pdf($html_data);
	}

/*********************************** added by punit for new return credit note functionality************************************/

	/*public function credit_notes($invoice_id,$type){
		
		if($type=='cs')
		{
			$data['info']=$this->document_model->csDetails($invoice_id);
			$data['i_items']=$this->retail_model->getCSItems($invoice_id,'Y');
			$data['info']['date_issue']=$data['info']['date'];
			$data['info']['cn_no']=$invoice_id;
			$html_data=$this->load->view('documents/credit_notes',$data, true);
			create_pdf($html_data);
		}
		if($type=='inv')
		{
			$data['info']=$this->document_model->invoiceDetails($invoice_id);
			$data['mechanics']=unserialize($data['info']['mechanics']);
			$data['info']['cn_no']=$invoice_id;
			$data['info']['mech_total']=0;
			foreach ($data['mechanics'] as $key => $value) {
				$data['info']['mech_total']+=$value;
			}
			$data['i_items']=$this->retail_model->getInvoiceItems($invoice_id,'Y');
			$html_data=$this->load->view('documents/credit_notes',$data, true);
			create_pdf($html_data);
		}
	}*/
	public function credit_notes($invoice_id,$type){
		
		if($type=='cs')
		{
			$data['info']=$this->document_model->csDetails($invoice_id);
			$data['i_items']=$this->retail_model->getCSItems($invoice_id,'Y');
			$data['info']['date_issue']=$data['info']['date'];
			$data['info']['cn_no']=$invoice_id;
			$html_data=$this->load->view('documents/credit_notes',$data, true);
			create_pdf($html_data);
		}
		if($type=='cn')
		{
			
			$data['info']=$this->document_model->invoiceDetails($invoice_id);
			$data['i_items']=$this->retail_model->getInvoiceItems($invoice_id,'Y');
			$data['info']['date_issue']=$data['info']['date'];
			$data['info']['cn_no']=$invoice_id;
			
			$html_data=$this->load->view('documents/credit_notes',$data, true);
			create_pdf($html_data);
		}
		if($type=='inv')
		{
			$data['info']=$this->document_model->invoiceDetails($invoice_id);
			$data['mechanics']=unserialize($data['info']['mechanics']);
			$data['info']['cn_no']=$invoice_id;
			$data['info']['mech_total']=0;
			foreach ($data['mechanics'] as $key => $value) {
				$data['info']['mech_total']+=$value;
			}
			$data['i_items']=$this->retail_model->getInvoiceItems($invoice_id,'Y');
			$html_data=$this->load->view('documents/credit_notes',$data, true);
			create_pdf($html_data);
		}
	}

/*********************************** *******************************************************************************************/


}