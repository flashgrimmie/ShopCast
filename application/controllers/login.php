<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {


	public function index() {
		$data['loginpage']=true;
		$data['headline']='User Login';
		$this->load->view('login',$data);
	}

	public function loginaction()
	{

		if ($this->input->post('username')!='' && $this->input->post('password')!='') {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
			$logged = $this->user_model->login($username, $password);
		} else {
			$this->session->set_flashdata(array('error'=>'Please specify your login details!'));
			redirect('login');
		}
		if ($logged) {
			
			if($this->user_model->isAdmin()) {
				redirect();
			}
				
        } else {
			redirect('login');
			$this->session->set_flashdata('error','Invalid password! Please try again.');
		}
	}

	function logout() {
       	$this->user_model->logout();
    }

    function change_password() 
    { 
      	
      	$old_pwd=$this->input->post('old_password');
      	$new_pwd=$this->input->post('new_password');
      	$conf_pwd=$this->input->post('confirm_password');
      	$redirect=$this->input->post('redirect');

      	$user=$this->user_model->getUser($this->session->userdata('user_id'));

      	if($user->password==md5($old_pwd)) {
      		if($new_pwd==$conf_pwd) {
      			$this->shared_model->update('users','user_id',$this->session->userdata('user_id'), array('password'=>md5($conf_pwd)));
	      		$this->session->set_flashdata('success','Your password was updated');
	      	} else {
	      		$this->session->set_flashdata('error','Passwords did not match! Please try again.');
	      	}
      	} else {
      		$this->session->set_flashdata('error','Invalid password! Please try again.');
      	}
		redirect($redirect);          
    } 
}