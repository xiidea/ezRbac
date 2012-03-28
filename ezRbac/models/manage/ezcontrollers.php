<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ezcontrollers
 *
 * This model represents user access mapping data. It can be used
 * for manipulation and retriving access previlages information.
 *
 * @version	1.0
 * @package ezRbac
 * @since ezRbac v 0.2
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class ezcontrollers extends  CI_Model {

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
    function get_permission($access_role,$controller)
    {
        $this->db->select('permission');
        $this->db->where('user_role_id',$access_role);
        $this->db->where('controller',$controller);

        $query = $this->db->get($this->_table_name);
        if ($query->num_rows() == 1) {
            $row=$query->row();
            return $row->permission;
        }
        return NULL;
    }

    //NNNIIIinnnjaaa::
    //array of files without directories... optionally filtered by extension
    function get_list(){
        die(APPPATH);
        $d="";
        $x=".php";

        foreach(array_diff(scandir($d),array('.','..')) as $f)
            if(is_file($d.'/'.$f)&&(($x)?preg_match($x.'$',$f,$match):1)){
                $l[]=$f;
                print_r($match);
            }
       // return $l;
    }
}


/* End of file user_access_map.php */
/* Location: ./ezRbac/models/user_access_map.php */