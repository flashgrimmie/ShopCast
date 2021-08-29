<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {


	function __construct()

    {
        parent::__construct();
    }

    public function petty_cash()
    {
        $petty=$this->shared_model->getQuery('select * from petty_cash_amounts order by id desc limit 1');
        foreach($petty as $petty_details) {
             if(date('Y-m',strtotime($petty_details->date))==date('Y-').str_pad((date('m')-1),2,0,STR_PAD_LEFT)) {
                $petty_cash['balance']=$petty_details->balance;
                $petty_cash['date']=date('Y-m-d h:i:s');
                $petty_cash['user_id']=$petty_details->user_id;
                $this->shared_model->insert('petty_cash_amounts',$petty_cash);
            }
        }
       
    }
}