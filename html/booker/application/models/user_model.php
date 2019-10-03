<?php
/**
 * User_model Model Class
 *
 * Model handling user registration, authentication and session management
 *
 * The class requires the use of the Zend session class
 *
 * @package     CodeIgniter
 * @subpackage  Models
 * 
*/

class User_Model extends CI_Model
{    
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function encrypt_password($password)
    {    
        // encrypt password
        $this->load->library('encrypt');
        return $this->encrypt->encode($password);
    }    
        
    function getUser($user_id = '')
    {
        if(! $user_id) {
            $user_id = $this->getUserID();
        }
    
        // if an ID is available, retrieve the information and set up the editor otherwise, return false
        if($user_id) {
            $result = $this->shared_model->getRow('select * from users where user_id = ' . $user_id);
        }
        else {
            $result = false;
        }        

        return $result;        
    }
    
    function getUsersCount()
    {
        $sql = "select count(*) as total_rows from users";
        $rows = $this->shared_model->getQuery($sql);
        
        return $rows;
    }
    
    function getUserByEmail($email)
    {
        if($email) {
            $result = $this->shared_model->getRow("select * from users where email =  '". $email."' ");
        }
        else {
            $result = false;
        }        

        return $result;        
    }
    
    /**
     * returns ID of current authenticated user from session data
     *
     * @return  int     user ID
     */
    function getUserID()
    {
        if(! $this->isLoggedIn()) return false;
        $ret = isset($this->zend->getSession()->user_id) ? $this->zend->getSession()->user_id : false;        
        return $ret;
    }
    
    /**
     * returns username of current authenticated user
     *
     * @return  string  username
     */
    function getUsername()
    {
        if(! $this->isLoggedIn()) return false;
        $ret = isset($this->zend->getSession()->username) ? $this->zend->getSession()->username : false;
        return $ret;
    }
    
    /**
     * returns usertype of current authenticated user
     *
     * @return  string  username
     */
    function getUserType()
    {
        if(! $this->isLoggedIn()) return false;
        $ret = is_numeric($this->session->userdata('user_id')) ? $this->session->userdata('user_type') : false;
        return $ret;
    }
    
    /**
     * checks whether user is an administrator
     *
     * @return  boolean
     */
    function isAdmin()
    {
        if(! $this->isLoggedIn()) return false;
        $ret = ($this->session->userdata('type_id') == '1'||$this->session->userdata('type_id') == '2'||$this->session->userdata('type_id') == '3')  ? true : false;
        return $ret;
    }
    
    /**
     * checks whether user is a instructor
     *
     * @return  boolean
     */
    function isDealer()
    {
        if(!$this->isLoggedIn()) return false;
        $ret = ($this->session->userdata('type_id')=='3') ? true : false;
        return $ret;
    }

    function isI()
    {
       if(!$this->isLoggedIn()) return false;
        $ret = ($this->session->userdata('type_id')=='4') ? true : false;
        return $ret;
    }
    
    
    /**
     * checks whether user is logged in; if not, redirects to login page
     *
     * @return  boolean
     */
    function isLoggedIn()
    {
        $ret = ($this->session->userdata('user_id')) ? true : false;        
        return $ret;
    }
    
    function login($username, $password)
    {

       
        // return failure if username and/or password are blanked or populated with help text
        if(($username == 'Username' && $username == 'Password') || ($username == '' && $password == ''))
        {
            $this->session->set_flashdata('error', 'You must enter Email and Password!  Please try again.');
            return false;
        }

        $authenticated = false;
        $continue = true;
        
        // locate user's record for password verification
        $sql = "select * from users JOIN user_types USING(type_id) where username = '" . $username . "' AND active='Y'";

        $row = $this->shared_model->getRow($sql);
        if($row) {                        
          if($continue==TRUE) {
               
                $password_confirm =$row->password;
                
                if(md5($password) == $password_confirm) {
                      // successful login; set session variables
                      $this->session->set_userdata('user_id', $row->user_id);
                      $this->session->set_userdata('name', $row->name);
                      $this->session->set_userdata('email', $row->email);
                      $this->session->set_userdata('type_id',$row->type_id);
                      $this->session->set_userdata('user_type',$row->type);
                      $this->session->set_userdata('outlet_id',$row->outlet_id);
                      $sql = "select * from outlets where outlet_id = '" . $row->outlet_id . "'";
                      $outletrow = $this->shared_model->getRow($sql);
                      if($outletrow) {
                          $this->session->set_userdata('location', $outletrow->location);
                          $this->session->set_userdata('contact', $outletrow->contact);
                          $this->session->set_userdata('outlet_name',$outletrow->name);
                      }
                      $authenticated = true;
                }
                else {
                    $this->session->set_flashdata('error','Invalid password! Please try again.'); 
                }
            }
        }
        else {
            // username not found; notify user
            $this->session->set_flashdata('error','Sorry, there&#8217;s no account for that email.'); 
        }
        return $authenticated;
    }

    
    function logout()
    {
        // set all session variables associated with authentication to null
            $array_items = array('user_id' => '', 'name' => '','email'=>'', 'user_type'=>'', 'disallowed'=>'');
            $this->session->unset_userdata($array_items);
            redirect();
    }

   function unique_email($email)
    {        
        // if the username exists return a 1 indicating true
        $row = $this->shared_model->getRow("select * from users where email = '" . $email . "' AND active='Y'");
        if ($row) {
          $ret = true;
        }
        else {
            $ret = false;
        }
        
        return $ret;
    }
    
  function username_taken($username)
    {        
        // if the username exists return a 1 indicating true
        $row = $this->shared_model->getRow("select * from users where username = '" . $username . "'");
        if ($row) {
          $ret = true;
        }
        else {
            $ret = false;
        }
        
        return $ret;
    }
}  
?>