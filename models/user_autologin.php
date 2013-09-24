<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User_Autologin model class
 *
 * This model represents user autologin data. It can be used
 * for user verification when user claims his autologin passport.
 *
 * @version	1.1
 * @package ezRbac
 * @since ezRbac v 0.3
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class User_Autologin extends CI_Model
{
    /**
     * @var $_table_name store auto_login_table name
     */
    private $_table_name;

    /**
     * @var $_user_table_name store the table name of user table
     */
    private $_user_table_name;

    private $_user_schema;

    /**
     * Constructor function
     */
    function __construct()
	{
		parent::__construct();
        $CI=& get_instance();
        $this->_table_name=$CI->config->item('auto_login_table','ez_rbac');
        $this->_user_table_name=$CI->config->item('user_table','ez_rbac');
        $this->_user_schema=$CI->config->item('schema_user_table','ez_rbac');
	}

	/**
	 * Get user data for auto-logged in user.
	 * Return NULL if given key or user ID is invalid.
	 *
	 * @param	int
	 * @param	string
	 * @return	object
	 */
	function get($user_id, $key)
	{
		$this->db->select($this->_user_table_name.'.'.$this->_user_schema['id']);
		$this->db->select($this->_user_table_name.'.'.$this->_user_schema['email']);
        $this->db->select($this->_user_table_name.'.'.$this->_user_schema['user_role_id']);
        $this->db->select($this->_user_table_name.'.'.$this->_user_schema['status']);
        $this->db->select($this->_user_table_name.'.'.$this->_user_schema['verification_status']);
		$this->db->from($this->_user_table_name);
		$this->db->join($this->_table_name, $this->_table_name.'.user_id = '.$this->_user_table_name.'.'.$this->_user_schema['id']);
		$this->db->where($this->_table_name.'.user_id', $user_id);
		$this->db->where($this->_table_name.'.key_id', $key);
		$query = $this->db->get();
		if ($query->num_rows() == 1) return $query->row();
		return NULL;
	}

	/**
	 * Save data for user's autologin
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function set($user_id, $key)
	{
		return $this->db->insert($this->_table_name, array(
			'user_id' 		=> $user_id,
			'key_id'	 	=> $key,
			'user_agent' 	=> substr($this->input->user_agent(), 0, 149),
			'last_ip' 		=> $this->input->ip_address(),
		));
	}

	/**
	 * Delete user's autologin data
	 *
	 * @param	int
	 * @param	string
	 * @return	void
	 */
	function delete($user_id, $key)
	{
		$this->db->where('user_id', $user_id);
		$this->db->where('key_id', $key);
		$this->db->delete($this->_table_name);
	}

	/**
	 * Delete all autologin data for given user
	 *
	 * @param	int
	 * @return	void
	 */
	function clear($user_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->delete($this->_table_name);
	}

	/**
	 * Purge autologin data for given user and login conditions
	 *
	 * @param	int
	 * @return	void
	 */
	function purge($user_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->where('user_agent', substr($this->input->user_agent(), 0, 149));
		$this->db->where('last_ip', $this->input->ip_address());
		$this->db->delete($this->_table_name);
	}
}

/* End of file user_autologin.php */
/* Location: ./ezrbac/models/user_autologin.php */