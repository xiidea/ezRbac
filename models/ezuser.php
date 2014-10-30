<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ezuser
 *
 * This model represents user  data. It can be used
 * for retriving user data and validate agains the login.
 *
 * @version	2
 * @package ezRbac
 * @since ezRbac v 2.0
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class Ezuser extends  CI_Model {
    /**
     * @var CI_Controller CI instance reference holder
     */
    private $CI;

    /**
     * @var $_table_name store the table name of user table
     */
    private $_table_name;

    /**
     * @var $_schema store the schema map of user table
     */
    private $_schema;

    /**
     * @var $_field store the schema of user table
     */
    private $_fields;

    /**
     * @var $_meta_table_name store the name of user_meta table
     */
    private $_meta_table;

    /**
     * @var $_user_meta_field store the schema of user table
     */
    private $_user_meta_fields;

    /**
     * constructor function
     */
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->CI=& get_instance();

        $this->_table_name=$this->CI->config->item('user_table','ez_rbac');
        $this->_meta_table=$this->CI->config->item('user_meta_table','ez_rbac');
        $this->_schema=$this->CI->config->item('schema_user_table','ez_rbac');

        $this->validate_schema();
        $this->_user_meta_fields     = $this->db->list_fields($this->_meta_table);
        $this->_user_meta_fields = array_combine($this->_user_meta_fields, $this->_user_meta_fields);
    }

    private function validate_schema(){
        $this->_fields     = $this->db->list_fields($this->_table_name);
        $missingFields = array_diff($this->_schema, $this->_fields);

        if(!empty($missingFields)){
            $missingStr = '<b>'.implode(', ', $missingFields).'<b>';
            show_error("The database schema for <b>{$this->_table_name}</b> Table is missing some required field(s): {$missingStr}");
        }
    }

    private function _f($f){
        return $this->_schema[$f];
    }

    /**
     * Get user record by email
     *
     * @param	string
     * @return	object
     */
    public function get_user_by_email($email)
    {
        $this->db->where('LOWER('.$this->_schema['email'].')=', strtolower($email));

        $query = $this->db->get($this->_table_name, 1);

        if ($query->num_rows() > 0) return $query->row();

        return NULL;
    }

    /**
     * Get user record by user_id
     *
     * @param	string
     * @return	object
     */
    public function get_user_by_id($user_id)
    {
        $this->db->select($this->_schema['id']);
        $this->db->select($this->_schema['email']);
        $this->db->select($this->_schema['user_role_id']);
        $this->db->select($this->_schema['last_login']);
        $this->db->select($this->_schema['status']);
        $this->db->select($this->_schema['verification_status']);
        $this->db->where($this->_schema['id'], $user_id);

        $query = $this->db->get($this->_table_name, 1);

        if ($query->num_rows() < 1 ) {return NULL;}

        $row = $query->row();
        $row->meta = $this->get_user_meta($row->{$this->_schema['id']});

        $key_field = $this->CI->config->item('user_meta_user_id','ez_rbac');

        if(isset($row->meta->{$key_field})) {
            unset($row->meta->{$key_field});
        }

        return $row;
    }

    public function get_user_meta($user_id)
    {
        $key_field = $this->CI->config->item('user_meta_user_id','ez_rbac');
        $this->db->where($key_field, $user_id);

        $query = $this->db->get($this->_meta_table, 1);

        if ($query->num_rows() > 0) return $query->row();
    }

    public function update_user_meta($user_id, $data)
    {
        $key_field = $this->CI->config->item('user_meta_user_id','ez_rbac');
        if(!isset($data[$key_field])){
            $data[$key_field] = $user_id;
        }

        return $this->on_duplicate_update($this->_meta_table, $data);
    }

    function getUserID($user)
    {
        $user->{$this->_schema['id']};
    }

    /**
     * Update user login info, such as IP-address or login time, and
     * clear previously generated (but not activated) passwords.
     *
     * @param	int
     * @return	void
     */
    public function update_login_info($user_id)
    {
        $this->db->set($this->_schema['reset_request_code'], NULL);
        $this->db->set($this->_schema['reset_request_time'], NULL);
        $this->db->set($this->_schema['reset_request_ip'], NULL);
        $this->db->set($this->_schema['verification_status'], 1);
        $this->db->set($this->_schema['last_login_ip'], $this->CI->input->ip_address());
        $this->db->set($this->_schema['last_login'], date('Y-m-d H:i:s'));

        $this->db->where($this->_schema['id'], $user_id);
        $this->db->update($this->_table_name);
    }

    /**
     * Reset user password, create reset request key and return it
     * @param $user_id
     * @return string
     */
    public function requestPassword($user_id){
        $data[$this->_schema['reset_request_code']]=$this->generateSalt();
        $data[$this->_schema['reset_request_time']]=date('Y-m-d H:i:s');
        $data[$this->_schema['reset_request_ip']]=ip2long($this->CI->input->ip_address());
        $this->db->where($this->_schema['id'],$user_id);
        $this->db->update($this->_table_name,$data);
        return md5($data[$this->_schema['reset_request_code']].
                   $data[$this->_schema['reset_request_time']].
                   $data[$this->_schema['reset_request_ip']]);
    }

    /**
     * Save new password after hashing that
     * @param $npass
     * @param $email
     */
    public function set_new_password($npass,$email){
        $salt=$this->generateSalt();
        $password=$this->CI->encrypt->sha1($npass.$salt);

        $this->db->set($this->_schema['reset_request_code'], NULL);
        $this->db->set($this->_schema['reset_request_time'], NULL);
        $this->db->set($this->_schema['reset_request_ip'], NULL);
        $this->db->set($this->_schema['salt'], $salt);
        $this->db->set($this->_schema['password'], $password);
        $this->db->where($this->_schema['email'],$email);
        $this->db->update($this->_table_name);
    }

    public function create($data = array())
    {
        if(isset($data['id'])){
            unset($data['id']);
        };

        if(!isset($data['email'])){
            throw new Exception('You must provide `email`');
        }

        if($this->get_user_by_email($data['email'])){
            throw new Exception('email already exist');
        }

        if(!isset($data['user_role_id'])){
            throw new Exception('You must user_role_id');
        }

        $data['salt'] = $this->generateSalt();

        if(!isset($data['password'])){
            $data['password'] = $this->generate_password($this->generateSalt());
        }

        $data['password'] = $this->encrypt->sha1($data['password'].$data['salt']);

        $data = $this->parseData($data);

        $this->db->insert($this->_table_name, $data);

        return  $this->db->insert_id();

    }

    public function update($data = array()){
        $user = false;

        if(isset($data['email'])){
            $user = $this->get_user_by_email($data['email']);
        }

        if($user && $data['id'] !== $user->{$this->_schema['id']}){
            throw new Exception('Email already register');
        }

        $id = $data['id'];

        if(isset($data['password']) && !empty($data['password'])){
            $data['salt'] = $this->generateSalt();
            $data['password'] = $this->encrypt->sha1($data['password'].$data['salt']);
        }

        $data = $this->parseData($data);

        $this->db->update($this->_table_name,$data,
            array($this->_schema['id'] => $id));

    }

    /**
     * @param $table
     * @param null $data
     * @param null $update
     *
     * @return bool
     */
    public function on_duplicate_update($table, $data = NULL, $update = NULL)
    {
        if (is_null($data)) {
            return FALSE;
        }

        $sql = $this->_duplicate_insert_sql($table, $data, $update);
        return $this->db->query($sql);
    }

    /**
     * @param      $table
     * @param      $values
     * @param null $update
     *
     * @return string
     */
    private function _duplicate_insert_sql($table, $values, $update = NULL)
    {
        $updateStr = array();
        $keyStr    = array();
        $valStr    = array();

        foreach ($values as $key => $val) {
            $keyStr[] = $key;
            $valStr[] = $this->db->escape($val);
        }

        if (is_null($update)) {
            $update = $values;
        }

        foreach ($update as $key => $val) {
            $updateStr[] = $key . " = '{$val}'";
        }

        $sql = "INSERT INTO " . $this->db->_protect_identifiers($table) . " (" . implode(', ', $keyStr) . ") ";
        $sql .= "VALUES (" . implode(', ', $valStr) . ") ";
        $sql .= "ON DUPLICATE KEY UPDATE " . implode(", ", $updateStr);

        return $sql;
    }

    private function parseData($data)
    {
        $return  = array();
        foreach($data as $key=>$value){
            if(isset($this->_schema[$key]))
            $return[$this->_schema[$key]] = $value;
       }
        return $return;
    }

    /**
     * Generates a salt that can be used to generate a password hash.
     * @return string the salt
     */
    protected function generateSalt()
    {
        return uniqid('',true);
    }

    function generate_password($salt) {
        return substr($salt,rand(1,5),6);
    }

}

/* End of file ezuser.php */
/* Location: ./ezRbac/models/ezuser.php */