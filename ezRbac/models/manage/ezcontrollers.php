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
    /**
     * @var CI_Controller CI instance reference holder
     */
    private $CI;

    /**
     * @var string Cache base path value of controller directory
     */
    private $_controllers_basepath;


    /**
     * Constructor function
     */
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->CI=& get_instance();
        $this->_controllers_basepath=realpath(APPPATH.DIRECTORY_SEPARATOR."controllers");
    }

    /**
     * Get user record by email
     *
     * @param $access_role
     * @param $controller
     * @return    object
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

    /**
     * @param string $d directory to search
     * @param string $pre prepend the directory name to form unique controller name
     * @return array
     */
    private function scan_for_controller($d="",$pre=""){
        $files = array();
        $dir=array();
        $more_files=array();
        foreach (new DirectoryIterator($d) as $file) {
                if($file->isDir()){
                    if(!$file->isDot()){
                        $dir[] =(string) $file;
                    }
                }else{
                   (preg_match('/^.*\.(php)$/i', $file)) AND $files[] = "$pre".$file->getBasename(".php");
                }
        }
        if(!empty($dir)){
            foreach($dir as $dname){
                $more_files= array_merge($this->scan_for_controller($d.DIRECTORY_SEPARATOR.$dname,"$dname/"),$more_files);
            }

        }
        return array_merge($files,$more_files);
    }


    /**
     * get the controller list
     * @return array
     */
    function get_controllers(){
        //Get first set of controllers!
        $all_list=$this->scan_for_controller($this->_controllers_basepath);
        //Ommit all public controllers! we do not need to think about those!!
        return array_diff($all_list,$this->CI->config->item('public_controller', 'ez_rbac'));
    }

    /**
     * Remove all access_role info which controller is not available
     *
     * @param array $existing
     */
    public function clear_garbage($existing=array()){
        $this->db->where_not_in('controller',$existing);
        $this->db->delete('user_access_map');
    }

}


/* End of file ezcontrollers.php */
/* Location: ./ezRbac/models/manage/ezcontrollers.php */