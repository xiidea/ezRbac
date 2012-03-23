<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ezuser
 *
 * This model represents user  data. It can be used
 * for retriving user data and validate agains the login.
 *
 * @version	1.1
 * @package ezRbac
 * @since ezRbac v 0.2
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class Ezuser extends  CI_Model {

    private $CI;
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->CI=& get_instance();
    }

    /**
     * Get user record by email
     *
     * @param	string
     * @return	object
     */
    function get_user_by_email($email)
    {
        $this->db->where('LOWER(email)=', strtolower($email));

        $query = $this->db->get("system_users");
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
        $this->db->set('reset_request_code', NULL);
        $this->db->set('reset_request_time', NULL);
        $this->db->set('reset_request_expiry', NULL);
        $this->db->set('reset_request_ip', NULL);
        $this->db->set('new_email', NULL);
        $this->db->set('new_password', NULL);
        $this->db->set('verification_status', 1);

        $this->db->set('last_login_ip', $this->CI->input->ip_address());
        $this->db->set('last_login', date('Y-m-d H:i:s'));

        $this->db->where('id', $user_id);
        $this->db->update('system_users');
    }

    public function requestPassword($user_id){
        $data['reset_request_code']=$this->generateSalt();
        $data['reset_request_time']=date('Y-m-d H:i:s');
        $data['reset_request_ip']=ip2long($this->CI->input->ip_address());
        $this->db->where('id',$user_id);
        $this->db->update('system_users',$data);
        return md5($data['reset_request_code'].$data['reset_request_time'].$data['reset_request_ip']);
    }

    public function set_new_password($npass){
        $salt=$this->generateSalt();
        $password=$this->CI->encrypt->sha1($npass.$salt);

        $this->db->set('reset_request_code', NULL);
        $this->db->set('reset_request_time', NULL);
        $this->db->set('reset_request_ip', NULL);
        $this->db->set('salt', $salt);
        $this->db->set('password', $password);
        $this->db->update('system_users');
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