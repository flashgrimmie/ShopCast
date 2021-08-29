<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$data=$this->data;
		$data['headline']='Dashboard';
		$data['breadcrumbs']=array('Dashboard'=>'#');
		$data['flot']=true;
		$data['morris']=true;
		$invoices=$this->shared_model->getRow('select count(*) as num from invoices join users using(user_id) where invoices.active="Y" and date_issue like "%'.(date('Y-m')).'%" and outlet_id="'.$this->session->userdata('outlet_id').'"');
		$cs=$this->shared_model->getRow('select count(*) as num from cash_sales join users using(user_id) where cash_sales.active="Y" and date like "%'.(date('Y-m')).'%" and outlet_id="'.$this->session->userdata('outlet_id').'"');
		$so=$this->shared_model->getRow('select count(*) as num from sales_orders join users using(user_id) where sales_orders.active="Y" and date like "%'.(date('Y-m')).'%" and outlet_id="'.$this->session->userdata('outlet_id').'"');
		$do=$this->shared_model->getRow('select count(*) as num from delivery_orders join users using(user_id) where delivery_orders.active="Y" and date like "%'.(date('Y-m')).'%" and users.outlet_id="'.$this->session->userdata('outlet_id').'" and delivery_orders.outlet_id is not null');
		$data['pie_info']=array('invoice'=>array('label'=>'Invoices','num'=>$invoices->num,'color'=>"#C5CED6"),
								'cs'=>array('label'=>'Cash Sales','num'=>$cs->num,'color'=>"#59646E"),
								'so'=>array('label'=>'Sales Orders','num'=>$so->num,'color'=>"#384B5E"),
								'do'=>array('label'=>'I.O. Delivery Orders','num'=>$do->num,'color'=>"#999"));
		for($i=1;$i<=date('m');$i++) {
			$inv_sql='select sum(total) as total, count(*) as num from invoices i join users u using(user_id) where i.active="Y" and u.outlet_id="'.$this->session->userdata('outlet_id').'" and month(date_issue)="'.$i.'" and year(date_issue)="'.date('Y').'"';
			$cs_sql='select sum(total) as total, count(*) as num from cash_sales i join users u using(user_id) where i.active="Y" and u.outlet_id="'.$this->session->userdata('outlet_id').'" and month(date)="'.$i.'" and year(date)="'.date('Y').'"';
			$so_sql='select sum(total) as total, count(*) as num from sales_orders i join users u using(user_id) where i.active="Y" and u.outlet_id="'.$this->session->userdata('outlet_id').'" and month(date)="'.$i.'" and year(date)="'.date('Y').'"';
			$do_sql='select count(*) as num from delivery_orders i join users u using(user_id) where i.active="Y" and u.outlet_id="'.$this->session->userdata('outlet_id').'" and month(date)="'.$i.'" and year(date)="'.date('Y').'" and i.outlet_id is not null';
			$inv_val=$this->shared_model->getRow($inv_sql,true);
			$cs_val=$this->shared_model->getRow($cs_sql,true);
			$so_val=$this->shared_model->getRow($so_sql,true);
			$do_val=$this->shared_model->getRow($do_sql,true);
			$data['line_info'][$i]=array('invoices'=>intval($inv_val['num']),'cash_sales'=>intval($cs_val['num']),'sales_orders'=>intval($so_val['num']),'delivery_orders'=>intval($do_val['num']));
			$data['bar_info'][$i]=array('invoices'=>intval($inv_val['total']),'cash_sales'=>intval($cs_val['total']),'sales_orders'=>intval($so_val['total']));
		}
		$this->load->view('dashboard',$data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */