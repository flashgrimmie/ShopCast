<?php
/**
 * Class Shared_Model
 * Functions shared by multiple models
 * @package     CodeIgniter
 * @access      public
 * @subpackage  Models
 * @copyright   Copyright (c) 2008-11
 * @license        http://www.gnu.org/licenses/lgpl.html
*/

class Shared_Model extends CI_Model
{

    var $alphabet = array ("A","B","C","D","E","F","G","H","I","J","K","L","M",
        "N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
    var $numbers = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
    var $parens = array("(", ")");
    var $yes_no = array("'Y'", "'N'");
        
    // ------------------------------------------------------------------------
    /**
     * initialises the class inheriting the methods of the class Model 
     *
     * @return Shared_Model
     */
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    
    /************************************************************************
     * return a blank array for each column in a table
     * 
     * table = table to extract column names from
     * incl_primary = boolean; in most cases, we don't need to retrieve the primary key value
     * since it's available when editing or being auto-inserted when newly submitted.  However,
     * if the primary key is being manually entered, we need to obtain the posted value before
     * submitting a new record.
     * 
     * @access   public
     * @param    string     table to retrieve records from
     * @param    boolean    include primary key in data returned?
     * @param    boolean    convert blank array to object?
     * 
     * @return   array     array containing all values posted from previous interface
     */
    function blankRecord($table, $incl_primary = false, $object = false) {
        $data = array();

        // autopopulate data array values from resultset
        $result = mysql_query("SHOW COLUMNS FROM $table");
        if (!$result) {
            return $data;
        }
        
        if (mysql_num_rows($result) > 0) {
            while ($columns = mysql_fetch_assoc($result)) {
                $fld = $columns['Field'];
                $temptype = $columns['Type'];
                
                // don't include primary key columns unless flag is set
                if($columns['Key'] == 'PRI'  && ! $incl_primary) continue;
                
                // get datatype
                $omit = array_merge(array("UNSIGNED"), $this->numbers, $this->parens, 
                    $this->yes_no, array(","));
                $datatype = strtolower(str_replace($omit, "", strtoupper($temptype)));

                switch ($datatype) {
                    case 'blob':
                    case 'char':
                    case 'varchar':
                    case 'date':
                    case 'datetime':
                    case 'enum':
                        $data[$fld] = '';
                        break;
                        $data[$fld] = array('name'=> $fld, 'id' => $fld, 'size'=> 8, 
                            'maxlength' => 10, 'value' => $value, 'class' => 'date-pick');
                        break;
                    case 'double':
                    case 'float':
                    case 'int':
                        $data[$fld] = 0;
                        break;
                    default:
                        $data[$fld] = '';
                        break;
                }

            }
        }
        
        if($object) {
            $data = (object) $data;
        }

        return $data;
    }
    
    /**
     * returns an array which is decoded from JSON
     *
     * @param   array   JSON array to be decoded
     * 
     * @return  array
     */
    function decode_json($arr) {
        if (version_compare(PHP_VERSION,"5.2","<"))  {
            require_once("./JSON.php"); 
            $json = new Services_JSON();
            $result = $json->decode(stripslashes($arr));
        } else {
            $result = json_decode(stripslashes($arr));
        }
        
        return $result;
    }

    /**
     * Deletes a record from the specified table by primary key val.ue
     *
     * @param   string  name of table to be deleted from
     * @param   string  primary key column
     * @param   int     primary key value
     */
    function delete($table, $primary_key, $id)
    {
        $this->db->delete($table, array($primary_key => $id));
        
        if($this->db->affected_rows() > 0) {
            $result = true;
        }
        else {
            $result = false;
        }
        
        return $result;
    }

    function custom_delete($table, $primary_key, $id)
    {
        $sql='DELETE FROM '.$table.' WHERE '.$primary_key.'="'.$id.'"';
        if(mysql_query($sql)) {
            $result=true;
        } else {
            $result=false;
        } 
        return $result;
    }
    
    /**
     * Executes a SQL command
     *
     * @param   string  SQL command to be executed
     * 
     * @return  boolean true for success; false for failure
     */
    function execute($sql) {        
            $query = $this->db->query($sql);        
            if ($query)
            {
                return true;
            }
            else {
                return false;
            }
    }    
    
    /**
     * getRecord
     *
     * get the specified record (or posted variables for an edited record when validations failed)
     * and format the columns for display
     *
     * @access   public
     * @param    string    table to retrieve records from
     * @param    string    primary key column
     * @param    string     primary key value
     * @param    boolean    if true, populate post array based upon table's structure; otherwise, retrieve data from DB
     * @param    boolean    if true, return HTML based column datatypes; if false, just return data
     * @param    boolean    if true, return HTML with empty column values
     * 
     * @return   array     array containing dynamically-generated textboxes based upon column datatype and length
     */
    function getDisplayRecord($table, $col = '', $id = '', $getpost = FALSE, $getHTML = TRUE, $blank_record = false) {
        $data_found = false;
        $row = array();
        
        // determine source of returned record
        if(! $getpost) {
            // retrieve record from database
            if($col == '') {
                $query = $this->db->get($table);
            }
            else {
                $query = $this->db->get_where($table, array($col => $id));
            }
            if($query->num_rows() > 0) {
                $row = $query->row_array();
                $data_found = true;
            }
        }
        
        // return data without column attributes
        if($getHTML === FALSE) {
            return $row;
        }
        
        // autopopulate data array values from resultset
        $result = mysql_query("SHOW COLUMNS FROM $table");
        if (!$result) {
            $data = array();
            return $data;
        }

        $this->load->helper('inflector');
        if (mysql_num_rows($result) > 0) {
            while ($columns = mysql_fetch_assoc($result)) {
                $fld = $columns['Field'];
                $temptype = $columns['Type'];

                // generate label for entry field
                $data['lbl_' . $fld] = humanize($fld);

                // get datatype
                $omit = array_merge(array("UNSIGNED"), $this->numbers, $this->parens, 
                    $this->yes_no, array(","));
                $datatype = strtolower(str_replace($omit, "", strtoupper($temptype)));

                if($data_found) {
                    $value = $row[$fld];
                }
                elseif ($getpost) {
                    $value = $this->input->post($fld);
                }
                else {
                    $value = '';
                }
                
                if($blank_record) $value = '';

                // see if length is available
                $omit = array_merge(array("UNSIGNED"), $this->alphabet, $this->parens, 
                    $this->yes_no);
                $len = intval(strtolower(str_replace($omit, "", strtoupper($temptype))));

                switch ($datatype) {
                    case 'blob':
                    case 'char':
                    case 'varchar':
                        // generate a textarea if length is greater than 80
                        if($datatype == 'blob') {
                            $data[$fld] = array('name'=> $fld, 'id' => $fld, 'rows'=> 10, 
                                'cols'=> 60, 'class' => 'textbox', 'value' => $value);
                        }
                        else {
                            // restrict textbox width to 45
                            if($len > 80) {
                                $data[$fld] = array('name'=> $fld, 'id' => $fld, 
                                    'size'=> 80, 'maxlength' => $len, 'class' => 'textbox', 
                                    'value' => $value);
                            }
                            else {
                                $data[$fld] = array('name'=> $fld, 'id' => $fld, 
                                    'size'=> $len + 2, 'maxlength' => $len, 'class' => 'textbox', 
                                    'value' => $value);
                            }
                        }
                        break;
                    case 'date':
                    case 'datetime':
                        $data[$fld] = array('name'=> $fld, 'id' => $fld, 'size'=> 8, 
                            'maxlength' => 10, 'value' => $value, 'class' => 'date-pick');
                        break;
                    case 'enum':
                        $pos = strpos($temptype, "'Y', 'N'");
                        
                        if($pos >= 0) {
                            $data[$fld] = array('name'=> $fld, 'id' => $fld, 'size' => '1',
                                'class' => 'textbox', 'value' => $value);
                            $data[$fld]['options'] = array('Y' => 'Yes', 'N' => 'No');
                        }
                        else {
                            $data[$fld] = array('name'=> $fld, 'id' => $fld, 'size' => '1',
                                'class' => 'textbox', 'value' => $value);
                        }
                        break;
                    case 'double':
                    case 'float':
                    case 'int':
                        $data[$fld] = array('name'=> $fld, 'id' => $fld, 'size'=> 9, 
                            'maxlength' => 8, 'class' => 'textbox', 'value' => $value);
                        break;
                    default:
                        if($len > 0) {
                            $data[$fld] = array('name'=> $fld, 'id' => $fld, 'size'=> $len + 2, 
                                'maxlength' => $len, 'class' => 'textbox', 'value' => $value);
                        }
                        else {
                            $data[$fld] = array('name'=> $fld, 'id' => $fld, 'size'=>50,
                                'class' => 'textbox', 'value' => $value);
                        }
                        break;
                }

            }
        }

        return $data;
    }
    
    /**
     * returns a <select> dropdown
     *
     * @param   string  name of lookup table
     * @param   string  name of dropdown
     * @param   string  display column
     * @param   string  value associated with display column
     * @param   string  current value of dropdown
     * @param   string  JavaScript function called when dropdown changes
     * @param   string  WHERE clause for retrieving from table (format: array('active' => 'Y'))
     * @param   boolean specify whether dropdown should be disabled
     * @param   string  ORDER BY clause
     * 
     * @return  array
     */
    function getDropdown($table, $dropdown, $disp_col, $value_col, $current_val = "", 
        $class = "", $where = "", $disabled = false, $order_by = "")
    {
        // see if any Ajax functionality is required for the dropdown
        if($class !== "") {
            $output = "<select id='$dropdown' name='$dropdown' class='$class'";
        }
        else {
            $output = "<select id='$dropdown' name='$dropdown'";
        }
        
        if($disabled) {
            $output .= " disabled";
        }
        $output .= ">" . $this->new_option('Any', '', $current_val);

        $this->load->database();
        $this->db->select($value_col . ' as value, ' . $disp_col . ' as display');
        
        if($order_by) {
            $this->db->order_by($order_by, 'asc');
        }
        else {
            $this->db->order_by($disp_col, 'asc');
        }
        
        if($where !== "") {
            $this->db->where($where);
        }
        $query = $this->db->get($table);

        foreach ($query->result() as $row)
        {
            $output .= $this->new_option($row->display, $row->value, $current_val);
        }

        $output .= "</select>";

        return ($output);
    
    }
    
    /************************************************************************
     * get posted variables before submitting Insert and Update statements
     * 
     * table = table to extract column names from
     * incl_primary = boolean; in most cases, we don't need to retrieve the primary key value
     * since it's available when editing or being auto-inserted when newly submitted.  However,
     * if the primary key is being manually entered, we need to obtain the posted value before
     * submitting a new record.
     * 
     * @access   public
     * @param    string     table to retrieve records from
     * @param    boolean    include primary key in data returned?
     * @param    array      columns to be cleared (nulled) if they were not POSTed
     * 
     * @return   array     array containing all values posted from previous interface
     */
    function getPost($table, $incl_primary = false, $clear_columns = array()) {
        $data = array();
		
        // autopopulate data array values from resultset
        $result = mysql_query("SHOW COLUMNS FROM $table");
        if (!$result) {
            return $data;
        }
        
        if (mysql_num_rows($result) > 0) {
            while ($columns = mysql_fetch_assoc($result)) {
                $fld = $columns['Field'];
                
                // don't include primary key columns unless flag is set
                if($columns['Key'] == 'PRI'  && ! $incl_primary) continue;
                
                // only return fields that are posted, not all fields in DB
				
                if($this->input->post($fld) <> "") {
                    $data = $data + array($fld => $this->input->post($fld));
                }
                else  {
                    // see if column should be cleared
                    if($clear_columns) {
                        if (in_array(strtolower($fld), $clear_columns))
                            $data = $data + array($fld => null);
                    }
                }
            }
        }
		
        return $data;
    }

    /**
     * Retrieves all records from specified table which meet the criteria in the WHERE clause
     * passed in as an argument.
     *
     * @param   string  name of table to retrieve records from
     * @param   string  column to sort resultset by
     * @param   string  sort order (ascending or descending) of resultset
     * @param   string  where clause for desired resultset to compare against table
     * 
     * @return  object  array of objects
     */
    function getRecords($table, $sort_col = '', $sort_order = "asc", $where = '')
    {
        // sort if requested
        if($sort_col != '') $this->db->order_by($sort_col, $sort_order);
        if($where != '') $this->db->where($where);

        $query = $this->db->get($table); 
        return $query->result();
    }

    /**
     * Retrieves a record from the specified table which meets the criteria in the WHERE clause
     * passed in as an argument.
     *
     * @param   string  SQL SELECT statement used to retrieve record
     * @param   boolean true (default), return array; false, return object
     * 
     * @return  array   array or object
     */
    function getRow($sql, $array = false)
    {
        $query = $this->db->query($sql);        
        if ($query->num_rows() > 0)
        {
            if($array) {
                $result = $query->row_array();
            }                           
            else {
                $result = $query->row();
            }       
        }
        else {
            $result = false;
        }
        
        return $result;
    }
    
    /**
     * Retrieves all records from specified table which meet the criteria in the WHERE clause
     * passed in as an argument.
     *
     * @param   string  SQL SELECT statement used to retrieve records
     * @param   boolean true, return array; false (default), return object
     * 
     * @return  array   array or object
     */
    function getQuery($sql, $array = false)
    {
        $query = $this->db->query($sql); 
        // return an array containing the data
        if($array) {
            $result = $query->result_array();
        }
        else {
            // return an object or array of objects containing the data
            $result = $query->result();
        }
        return $result;
    }
    
    /**
     * returns a <select> dropdown based upon a SELECT statement
     *
     * @param   string  SELECT statement used to generate dropdown (in format, select description as display, id as value from customers)
     * @param   string  name of dropdown
     * @param   string  current value of dropdown
     * @param   string  JavaScript function called when dropdown changes
     * @param   boolean true to disable dropdown; false (default) to leave enabled
     * @param   boolean true to enable multiple selection; false (default) to only permit one selection
     * 
     * @return  array
     */
    function getSQLDropdown($sql, $dropdown, $current_val = '', $on_change = "", $disabled = false, $multiple = false)
    {
        // see if any Ajax functionality is required for the dropdown
        if($on_change !== "") {
            $output = "<select id='$dropdown' name='$dropdown' onChange='$on_change'";
        }
        else {
            $output = "<select id='$dropdown' name='$dropdown'";
        }

        if($disabled) {
            $output .= " disabled";
        }
        
        if($multiple) {
            $output .= ' multiple="multiple"';
        }
        
        $output .= ">" . $this->new_option('Any', '', $current_val);

        $this->load->database();
        $query = $this->db->query($sql);

        foreach ($query->result() as $row)
        {
            $output .= $this->new_option($row->display, $row->value, $current_val);
        }

        $output .= "</select>";

        return ($output);
    
    }
    
    function getTableArray($table, $display_col, $value_col, $order = '', $direction = 'ASC')
    {    
            $this->ci->load->database();
            $this->ci->db->select($value_col . ', ' . $display_col);
            
            if($order) {
                $this->ci->db->order_by($order, $direction);
            }
            $query = $this->ci->db->get($table);
            
            $options['']="Please Select";
               
            foreach ($query->result() as $row)
               {
                    $options[$row->{$display_col}] = $row->{$value_col};
               }
            
               $query->free_result();
                  
               return $options;
               
    }
    
    function getToday()
    {
        return date("Y-m-d H:i:s");
    }

    /**
     * Inserts a record into the specified table using the columns and data passed in an array
     *
     * @param string table
     * @param array data
     * @param   (optional): elements: ['general'], ['phone_numbers'], ['emails'], ['websites']; contains columns allowed to pass entry restrictions
     * @return boolean result
     */
    function insert($table, $data, $exceptions = array())
    {
        // prevent restricted data from being entered
        $failed = $this->restricted_data_found($data, $exceptions);
        if($failed) {
            $result = false;
        }
        else {            
            // execute query
            $this->db->insert($table, $data);
            
            if($this->db->affected_rows() > 0) {
                $result = mysql_insert_id();
            }
            else {
                $result = false;
            }
        }

        
        return $result;
    }
    
    /**
     * returns an array in JSON format (often from an AJAX call)
     *
     * @param   array   array to be converted to JSON
     * 
     * @return  array   array converted into JSON format
     */
    function JEncode($arr){
        if (version_compare(PHP_VERSION,"5.2","<"))
        {    
            require_once("./JSON.php"); //if php<5.2 need JSON class
            $json = new Services_JSON();//instantiate new json object
            $data=$json->encode($arr);  //encode the data in json format
        } else
        {
            $data = json_encode($arr);  //encode the data in json format
        }
        return $data;
    }
    
    /**
     * returns an array containing lookup values from an entire row cross-referenced from a parent table
     *
     * @param   string  name of lookup table
     * @param   string  lookup key value
     * @param   string  lookup description column
     * @param   string  criteria for lookup search
     * 
     * @return  array
     */
    function LookupArray($table, $key_column, $display_column, $where = '')
    {
        $this->db->select($key_column . ', ' . $display_column);
        if($where) $this->db->where($where);
        $this->db->order_by($display_column, 'asc');

        $query = $this->db->get($table);
        if($query->num_rows() > 0) {
            foreach ($query->result_array() as $row)
            {
                $rows[$row[$key_column]] = $row[$display_column];
            }
        }
        else {
            $rows = array();
        }
        
        return $rows;
    }

    /**
     * returns the description from a lookup table based upon the cross-reference ID passed in
     *
     * @param   string  name of lookup table
     * @param   string  lookup description column
     * @param   string  criteria for lookup search
     * 
     * @return  string  result of lookup query
     */
    function Lookup($table, $display_column, $where = '')
    {
        $this->db->select($display_column);
        if($where) $this->db->where($where);
//        $this->db->order_by($display_column, 'asc');

        $query = $this->db->get($table);
        if($query->num_rows() > 0) {
            $row = $query->result_array();
            $result = $row[0][$display_column];
        }
        else {
            $result = '';
        }
        
        return $result;
    }
    
    private function new_option($text, $value, $value_cmp)
    {
        $output = "<option value=\"" . $value . "\"";

        if ($value === $value_cmp) 
        {
            $output .= " selected";
        }

        $output .= ">" . $text . "</option>";

        return ($output);
    }    
    
    private function restricted_data_found(&$data, $exceptions)
    {
        // we're not using this function for MD.com at this time, so return false so that it doesn't prevent updates from working
        return false;
        
        // if this is a system-generated update, ignore restrictions and permit all updates
        if(isset($data['system_update'])) {
            unset($data['system_update']);
            return false;
        }
        
        $failed = false;
        $error_message = '';        
        
        // prevent restricted data from being entered
        $this->load->library('editing');        
        
        // do not include sys_update_date column in restrictions
        $system_update_columns = array('sys_update_date', 'lastpagevisit', 'lastvisitdate');

        foreach($data as $key => $value) {
            // only permit one error to be flagged
            if($failed) continue;
            
            // if column name is a system update column, do not verify
            if(in_array(strtolower($key), $system_update_columns)) continue;
            
            // if column is listed under general exceptions, do not verify it
            if(isset($exceptions['general'])) {
                if(in_array(strtolower($key), $exceptions['general'])) continue;
            }    
            
            // confirm whether current column includes an email
            $cnt = $this->editing->number_of_emails($value);
            if($cnt > 0) {                
                // see if key is in email exceptions
                if(isset($exceptions['emails'])) {
                    if(! in_array($key, $exceptions['emails'])) {                
                        $failed = true;
                        $error_message .= $this->set_restricted_error_message($key);
                    }
                }
                else {
                    $failed = true;
                    $error_message .= $this->set_restricted_error_message($key);
                }
                
            }
            
            // confirm whether current column includes a phone number
            if(! $failed) {                
                $cnt = $this->editing->number_of_phone_numbers($value);
                if($cnt > 0) {
                    // see if key is in email exceptions
                    if(isset($exceptions['phone_numbers'])) {
                         if(! in_array($key, $exceptions['phone_numbers'])) {                
                              $failed = true;
                              $error_message .= $this->set_restricted_error_message($key);
                         }
                    }
                    else {
                        $failed = true;
                        $error_message .= $this->set_restricted_error_message($key);
                    }
                    
                }
            }

            if(! $failed) {                
                $cnt = $this->editing->number_of_URLs($value);
                if($cnt > 0) {                
                    // see if key is in email exceptions
                    if(isset($exceptions['websites'])) {
                         if(! in_array($key, $exceptions['websites'])) {                
                              $failed = true;
                              $error_message .= $this->set_restricted_error_message($key);
                         }
                    }
                    else {
                        $failed = true;
                        $error_message .= $this->set_restricted_error_message($key);
                    }
                    
                }
            }
        }
        
        if($error_message) {
            $this->zend->getSession()->status_message = $error_message;
        }
        else {
            $this->zend->getSession()->status_message = '';
        }
        
        return $failed;
        
    }
    
    // sets default field titles for validations rules array
    function setFieldTitles($rules)
    {
        $this->load->helper('inflector');
        foreach ($rules as $key => $val) {
            $fields['lbl_' . $key] = humanize($key);
        }
        
        return $fields;
    }

    private function set_restricted_error_message($key)
    {
        $this->load->helper('inflector');
        $vague_columns = array('value1', 'value2', 'value3');
        
        // if column name is vague, do not specify column            
        if(in_array($key, $vague_columns)) {
            $error_col = '<p>You are not permitted to enter an email address, phone number, or ' .
                'website address. Please go back and correct this.</p>';
        }
        else {
            $error_col = '<p>You are not permitted to enter an email address, phone number, or ' .
                'website address in the ' . humanize($key) . ' field. Please go back and correct ' .
                'this.</p>';
        }
        
        return $error_col;
        
    }
    
        // ------------------------------------------------------------------------
    
    /**
     * Updates a record in the specified table using the columns and data passed in an array
     *
     * @param   string  table
     * @param   string  primary key column of table
     * @param   int  primary key value of table
     * @param   array   data
     * @param   (optional): elements: ['general'], ['phone_numbers'], ['emails'], ['websites']; contains columns allowed to pass entry restrictions
     * 
     * @return  boolean result
     */
    function update($table, $primary_key, $key_val, $data, $exceptions = array()) {
        // prevent restricted data from being entered
        $failed = $this->restricted_data_found($data, $exceptions);

        // if no restricted data was entered, execute query
        if($failed) {
            $result = false;            
        }
        else {            
            $this->db->where($primary_key, $key_val);
            $success=$this->db->update($table, $data);
            
            if($success) {
                $result = true;
            }
            else {
                $result = false;
            }
        }
        
        return $result;
    }

    function updateNew($table,$data,$where)
    {
        if($test=$this->db->update($table,$data,$where))
        if($this->db->affected_rows() > 0) {
                $result = true;
            }
            else {
                $result = false;
            }
        return $result;
    }
    
    function getUUID()
    {
        $sql = "select uuid() as uuid";
        $row = $this->getQuery($sql);
        return $row[0]->uuid;
    }
         
}
?>