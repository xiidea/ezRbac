<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ezuser
 *
 * This model represents user  data. It can be used
 * for retriving user data and validate agains the login.
 *
 * @version	1.2
 * @package ezRbac
 * @since ezRbac v 0.3
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
     * constructor function
     */
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->CI=& get_instance();

        $this->_table_name=$this->CI->config->item('user_table','ez_rbac');
        $this->_schema=$this->CI->config->item('schema_user_table','ez_rbac');
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
    function get_user_by_email($email)
    {
        $this->db->where('LOWER('.$this->_schema['email'].')=', strtolower($email));

        $query = $this->db->get($this->_table_name);
        if ($query->num_rows() == 1) return $query->row();
        return NULL;
    }

    /**
     * Update user login info, such as IP-address or login time, and
     * clear previously generated (but not activated) passwords.
     *
     * @param	int
     * @return	void
     */
    function update_login_info($user_id)
    {
        $this->db->set($this->_schema['reset_request_code'], NULL);
        $this->db->set($this->_schema['reset_request_time'], NULL);
        $this->db->set($this->_schema['reset_request_ip'], NULL);
        $this->db->set($this->_schema['new_email'], NULL);
        $this->db->set($this->_schema['new_password'], NULL);
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
     */
    public function set_new_password($npass){
        $salt=$this->generateSalt();
        $password=$this->CI->encrypt->sha1($npass.$salt);

        $this->db->set($this->_schema['reset_request_code'], NULL);
        $this->db->set($this->_schema['reset_request_time'], NULL);
        $this->db->set($this->_schema['reset_request_ip'], NULL);
        $this->db->set('', NULL);
        $this->db->set('', NULL);
        $this->db->set($this->_schema['salt'], $salt);
        $this->db->set($this->_schema['password'], $password);
        $this->db->update($this->_table_name);
    }

    /**
     * Generates a salt that can be used to generate a password hash.
     * @return string the salt
     */
    protected function generateSalt()
    {
        return uniqid('',true);
    }

}

/* End of file ezuser.php */
/* Location: ./ezRbac/models/ezuser.php */