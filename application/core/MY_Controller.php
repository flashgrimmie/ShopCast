<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

protected $data;

	function __construct()
    {
        parent::__construct();

        if(!$this->user_model->isLoggedIn()){
			redirect(base_url().'login');
		}
		
		$this->checkIP();
		date_default_timezone_set('Asia/Brunei');
		$sql="select * from purchase_orders JOIN users USING(user_id) JOIN outlets ON(users.outlet_id=outlets.outlet_id) where purchase_orders.outlet_id='".$this->session->userdata('outlet_id')."' AND notify='Y'";
		$this->data['incomingPurchases']=$this->shared_model->getQuery($sql);

		$sql="select * from delivery_orders JOIN users USING(user_id) JOIN outlets ON(users.outlet_id=outlets.outlet_id) where delivery_orders.outlet_id='".$this->session->userdata('outlet_id')."' AND notify='Y' and delivery_orders.active='Y' AND published=1 AND accepted='N'";
		//echo $sql;
		//exit;
		$this->data['outgoingDeliveries']=$this->shared_model->getQuery($sql);
		$disallowed=$this->session->userdata('disallowed');
		if(!$disallowed) {
			$disallowed=$this->check_permissions();
			$this->session->set_userdata('disallowed',$disallowed);
		}
		if(isset($disallowed[$this->uri->segment(1)]) && in_array($this->uri->segment(2), $disallowed[$this->uri->segment(1)])) {
			show_error('This action is not allowed for your user level.', 403);
		}

    }

	function check_permissions() 
	{
		$permissions=$this->shared_model->getRow('select * from user_types where type_id="'.$this->session->userdata('type_id').'"',true);
		$disallowed=array();
		foreach($permissions as $key=>$value) {
			if($value!='type_id' && $value!='type') {
				$section=explode('_',$key,2);
				if($section[0]=='setup') {
					$section[0]='system_setup';
				}
				if($value=='N') {
					if(!isset($disallowed[$section[0]])) {
						$disallowed[$section[0]]=array();
					}
					array_push($disallowed[$section[0]], $section[1]);
				}
			}
		}
		return $disallowed;
	}

	protected function checkIP()
	{
		$allowed_ips=$this->shared_model->LookupArray('allowed_ips','id','ip_address',array('user_id'=>$this->session->userdata('user_id')));
		if(!empty($allowed_ips)) {
			$user_ip=$this->input->ip_address();
			if(!in_array($user_ip, $allowed_ips)) {
				show_error("You can't access the system using this IP address.",'403');
				exit;
			}
		}
	}
	protected function removeComma($string)
	{
		return str_replace("\r","",str_replace("\n","",str_replace("'","",str_replace('"','',str_replace(',', '', $string)))));
	}

}
?>