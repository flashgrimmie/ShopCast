<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin_model extends CI_Model{

	// 
	// 
	// ****************** Functions from haider bhai model 
	// 
	// 

   function get_records_with_limit($tbl = '', $data, $where='', $single = FALSE,$group_by = '',$order_by = '',$limit='')
    {
        $this->db->select($data);
        if($group_by !== ''){
            $this->db->group_by($group_by);
        }
		if(!empty($where))
		{
			$this->db->where($where);
		}
        if($order_by !== ''){
            if(is_array($order_by)){
                $this->db->order_by($order_by[0],$order_by[1]);
            }else{
                $this->db->order_by($order_by);
            }
        }
        if($limit != '')
        {
        	$this->db->limit($limit);
        }
        $query = $this->db->get($tbl);
        //return $this->db->last_query();
        if ($query->num_rows() > 0) {
            if ($single == TRUE) {
                return $query->row();
            } else {
                return $query->result();
            }
        } else {
            return FALSE;
        }
    }	



   //Insert Record If Don't Exist Else Update the Record
    function insert_slash_update($tbl, $data, $field, $id,$where){
        $this->db->where($where);
        $q = $this->db->get($tbl);
        if ( $q->num_rows() > 0 )
        {
            $this->db->where($field,$id);
            $this->db->update($tbl,$data);
            $affectedRows = $this->db->affected_rows();
            if($affectedRows){
                return TRUE;
            }else{
                return FALSE;
            }
        } else {
            $this->db->set($field, $id);
            $this->db->insert($tbl,$data);
            $insertedID = $this->db->insert_id();
            if($insertedID > 0){
                return TRUE;
            }else{
                return FALSE;
            }
        }
    }

    //Common AutoComplete Queries
    function get_autoComplete($tbl, $data, $field, $value, $where = '', $group_by = false, $limit = '')
    {
        $this->db->select($data);
        $this->db->from($tbl);
        if ($where != '') {
            $this->db->where($where);
        }
        $this->db->like('LOWER(' . $field . ')', strtolower($value));
        if ($group_by == true) {
            $this->db->group_by($field);
        }
        if ($limit != '') {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        return $query->result();
    }

// Please read the following fucntion carefully
// The param array should be multidimensional
// e.g array(array('table'=>'tablename','condition'=>'condition'),array())

    function select_fields_where_like__orLikes_join($tbl = '', $data, $joins = '', $where = '', $single = FALSE, $field = '', $value = '', $orLikes = '',$distinct='', $group_by='',$order_by = '',$limit = '')
    {

    	if(!empty($distinct))
    	{
    		$this->db->distinct($distinct);
    	}
        if (is_array($data) and isset($data[1])){
            $this->db->select($data[0],$data[1]);
        }else{
            $this->db->select($data);
        }

        $this->db->from($tbl);
        if ($joins != '') {
            foreach ($joins as $k => $v) {
                $this->db->join($v['table'], $v['condition'], $v['type']);
            }
        }
        if ($value !== '') {
            $this->db->like('LOWER(' . $field . ')', strtolower($value));
        }

        if($orLikes != '' and is_array($orLikes)){
            foreach($orLikes as $key=>$array){
                $this->db->or_like('LOWER('.$array['field'].')', strtolower($array['value']));
            }
        }

        if ($where != '') {
            $this->db->where($where);
        }
        if($group_by != ''){
            $this->db->group_by($group_by);
        }
        if($order_by != ''){
            if(is_array($order_by)){
                $this->db->order_by($order_by[0],$order_by[1]);
            }else{
                $this->db->order_by($order_by);
            }
        }
        if($limit != ''){
            if(is_array($limit)){
                $this->db->limit($limit[0],$limit[1]);
            }else{
                $this->db->limit($limit);
            }
        }
        $query = $this->db->get();
//        return $this->db->last_query();
//return $this->db->last_query();
        if ($query->num_rows() > 0) {
// query returned results
            if ($single == TRUE) {
                return $query->row();
            } else {
                return $query->result();
            }
        } else {
// query returned no results
            return FALSE;
        }
    }


function select_fields_where_like_join($tbl = '', $data, $joins = '', $where = '', $single = FALSE, $field = '', $value = '',$group_by='',$order_by = '',$limit = '')
    {
        if (is_array($data) and isset($data[1])){
            $this->db->select($data[0],$data[1]);
        }else{
            $this->db->select($data);
        }

        $this->db->from($tbl);
        if ($joins != '') {
            foreach ($joins as $k => $v) {
                $this->db->join($v['table'], $v['condition'], $v['type']);
            }
        }

        if ($value !== '') {
            $this->db->like('LOWER(' . $field . ')', strtolower($value));
        }

        if ($where != '') {
            $this->db->where($where);
        }
        if($group_by != ''){
            $this->db->group_by($group_by);
        }
        if($order_by != ''){
            if(is_array($order_by)){
                $this->db->order_by($order_by[0],$order_by[1]);
            }else{
                $this->db->order_by($order_by);
            }
        }
        if($limit != ''){
            if(is_array($limit)){
                $this->db->limit($limit[0],$limit[1]);
            }else{
                $this->db->limit($limit);
            }
        }
        $query = $this->db->get();
    //    return $this->db->last_query();
        if ($query->num_rows() > 0) {
// query returned results
            if ($single == TRUE) {
                return $query->row();
            } else {
                return $query->result();
            }
        } else {
// query returned no results
            return FALSE;
        }
    }

    //
    //
    //********************** MY HELPER FUNCTIONS
    //
    // 

    function readmore($where_string , $id , $path,$string_size=NULL){
        $string = strip_tags($where_string);
        if (strlen($string) > 100) {
        // truncate string
        $stringCut = substr($string, 0, 100);
        $string = substr($stringCut, 0, strrpos($stringCut, ' '))."<a href=\"$path/$id\">...</a>"; 
       }
    	echo "<a href=\"$path/$id\">".$string."</a>";
    }
   
	function timestamp_date($timestamp)
	{
		$date=date('Y-m-d',strtotime($timestamp));
		echo $date;
	}

	function download($name,$path){
         $this->load->helper('download');                      
        $data = file_get_contents($path); // Read the file's contents
        $name = $name;

        force_download($name, $data);
    }

   //
   //
   //*********************** END OF MY HELPER FUNCTIONS
   //
   // 
   //
   //*********************** Generic Function Start up From here
   // 

	public function get_records($table, $where=NULL) {
	
		if(!empty($where)){
			$this->db->where($where);
		}
		$query = $this->db->get($table);
		if($query->num_rows>0) {
			return $query->result();
		}
		else{
			return array();
		}
	}

	public function get_like_records($table, $where='', $like='' ,$single=false)
	{
		$this->db->select('*')
			->from($table);

		if(!empty($where))
		{
			$this->db->where($where);
		}
		if(!empty($like))
		{
			$this->db->like($like);
		}
		$query=$this->db->get();
		if($single == true)
		{
			return $query->row();
		}
		else
		{
			return $query->result();
		}

	}



	public function get_fields($table,$where=NULL,$fields=NULL)
	{
		if(isset($fields))
		{
			$this->db->select($fields);
			}
		if(isset($where))
		{
			$this->db->where($where);
			}
		
		$query=$this->db->get($table);
		if($query->num_rows>0){
			return $query->result();
			}
		else{return array();}
		}


	public function get_record($table, $where=NULL,$arr=false) {
		if(!empty($where)){
			$this->db->where($where);
		}
		$query = $this->db->get($table);
		if($arr==true)
		{
			return $query->row_array();
		}
		else
		{
			if($query->num_rows>0) 
			{
				return $query->row();
			} 
		else
			{
				return array();
			}	
		}
		
	}
	
	public function get_last_row($table, $where=NULL){
		if(!empty($where)){
			$this->db->where($where);
		}
		$query = $this->db->get($table);
		if($query->num_rows>0) {
			return $query->last_row();
		} 
		else{
			return array();
		}
	}
	
	function get_second_last_row($table)
	{
		$query= $this->db->query("SELECT * FROM $table where id=(select max(id)-1 from $table)");
		return $query->result();
	}
		
	public function get_field($table, $field, $where=NULL) {
		$this->db->select($field);
		$this->db->from($table);
		if(isset($where)){
		$this->db->where($where);
		}
		$query = $this->db->get();
		return $query->row();
	}
	
	public function add_record($table, $data){
		$this->db->insert($table,$data);
		return $this->db->insert_id();
	}
	
	public function update_record($table, $data, $where){
		$this->db->where($where);
		$this->db->update($table,$data);
		return $this->db->affected_rows();
	}
	
	public function delete_record($table, $where) {
		$this->db->where($where);
		$this->db->delete($table);
		return $this->db->affected_rows();
	}
	
	public function get_icon($file_name){
		if(!empty($file_name)){
			$ext = pathinfo($file_name, PATHINFO_EXTENSION);
			switch(strtolower($ext)){
				case 'pdf';
					$file = 'pdf.png';
				break;
				case 'xls';
				case 'xlsx';
					$file = 'excel.png';
				break;
				case 'doc';
				case 'docx':
					$file = 'word.png';
				break;
				case 'jpg';
				case 'png';
				case 'gif';
					$file = 'image.png';
				break;
				default:
					$file = 'default.png';
				break;
			}
			return $file;
		}
	}
	
	public function get_dropdown($table, $fields, $where=NULL){
		$this->db->select($fields);
		if(!empty($where)){
			$this->db->where($where);
		}
		$this->db->from($table);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			foreach($query->result_array() as $row){
				$data[$row[$fields[0]]] = $row[$fields[1]];
			}
			return $data;
		}
		else{
			return array();
		}
	}
	
	public function do_upload($path) { 
		$config['upload_path'] = $path;
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '2000';
		$config['max_width']  = '10000';
		$config['max_height']  = '10000';
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
        if (!$this->upload->do_upload('file')){ 
			return array();
		}
		else{
			$data = array('upload_data' => $this->upload->data());
			return $data;
		}
	}
	
	function file_upload($newname ,$path)
    {
        $config['file_name'] = $newname;
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|png|jpg';
        $config['max_size']	= '0';
        $this->load->library('upload', $config);
		$this->upload->initialize($config);
        if (!$this->upload->do_upload('file')){ 
			return array();
		}
		else{
			$data = array('upload_data' => $this->upload->data());
			return $data;
		}
    }
	
	function escape($input){
		if(!empty($input)){
			$input = mysql_real_escape_string($input);
		}
		return $input;
	}
	
	function show_text($textarea){
		$text = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$textarea);
		return $text;
	}
	
	function verfication($table, $truedir, $falsedir)
	{
		//echo "test 3";
	   $this->form_validation->set_rules('email','Email','trim|required|min_length[5]|max_length[25]|xss_clean');
	   $this->form_validation->set_rules('password','Password', 'trim|required|min_length[4]||xss_clean');
	   if($this->form_validation->run() == FALSE)
		 {
			// echo "false";
			redirect($falsedir);
		 }
		else
		{
		  $data=array(
		  'email'=>$this->input->post("email"),
		  'password'=>$this->input->post("password"),'status'=>'enable'
		  );		

		  $query=$this->get_record($table,$data);
		  if($query==true)
		  {
			 //  print_r($query);
			  // exit();
			$session_array=array('username'=>$query->username,'user_id'=>$query->id);
			$this->session->set_userdata($session_array);
			//echo "true";
			redirect($truedir);
		  }
		  else
		  {
			  $this->session->set_flashdata("error","invalid username or password");
			  redirect($falsedir);
			 // echo "false 2"; 
		  }
		}
	}
	
	
	//extending generic functions
		public function simple_search($term, $field, $limit)
	{
		$data = array();

		$this->db->like($this->table.'.'.$field, $term);

		$this->db->limit($limit);

		$query = $this->db->get($this->table);

		if($query->num_rows() > 0)
		{
			$data = $query->result_array();
		}

		return $data;
	}
	
	public function get_max($field, $table=NULL, $where=NULL)
	{
		$table = ( ! is_null($table)) ? $table : $this->table ;

		$this->db->select_max($field, 'maximum');

		if (! is_null($where))
		{
			$this->db->where($where);
		}

		$query = $this->db->get($table);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			return $row->maximum;
		}
		return FALSE;
	}
	
	
	// ------------------------------------------------------------------------


	/**
	 * Removes from the data array the index which are not in the table
	 *
	 * @param      $data		The data array to clean
	 * @param bool $table		Reference table. $this->table if not set.
	 *
	 * @return array
	 */
	public function clean_data($data, $table = FALSE)
	{
		$cleaned_data = array();

		if ( ! empty($data))
		{
			$table = ($table !== FALSE) ? $table : $this->table;

			$fields = $this->db->list_fields($table);

			$fields = array_fill_keys($fields,'');

			$cleaned_data = array_intersect_key($data, $fields);
		}
		foreach($cleaned_data as $key=>$row)
		{
			if (is_array($row))
				unset($cleaned_data[$key]);
		}
		return $cleaned_data;
	}
	
	/**
	 * List fields from one table of the current DB group
	 * and stores the result locally.
	 *
	 * @param	string
	 * @return	Array	List of table fields
	 *
	 */

	
	public function last_query()
	{
		return $this->db->last_query();
	}

	public function table_exists($table)
	{
		return $this->db->table_exists($table);
	}
	
	

	public function get_pk_name($table = NULL)
	{
		if (! is_null($table))
		{
			$fields = $this->db->field_data($table);

			foreach ($fields as $field)
			{
				if ($field->primary_key)
				{
					return $field->name;
					break;
				}
			}
		}
		else
		{
			return $this->pk_name;
		}
		return FALSE;
	}
	
	// ------------------------------------------------------------------------


	

	/**
	 * Count all rows in a table or count all results from the current query
	 *
	 * @access	public
	 * @param	bool	true / false
	 * @return	int 	The number of all results
	 *
	 */
	public function count_all($table ,$results = FALSE)
	{
		if($results !== FALSE)
		{
			$query = $this->db->count_all_results($table);
		}
		else
		{
			$query = $this->db->count_all($table);
		}
		return (int) $query;
	}


	/**
	 * Select sum of field from table 
	 *
	 * @access	public
	 * @param	$field,$table,$where	string,name of table,condtion
	 * @return	int 	The sum of required field
	 *
	 */

	public function get_sum($table,$field,$where)
	{
		$this->db->select_sum($field);
		$this->db->from($table);
		if(!empty($where))
		{
			$this->db->where($where);
		}
		$query=$this->db->get();
		return (int) $query->row()->$field;
	}

}
?>