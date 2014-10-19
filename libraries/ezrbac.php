<?php
/**
 * ezrbac class
 * The Api class for this package
 *
 * @version    1.0
 * @package    ezRbac
 * @since      ezRbac v 1.0
 * @author     Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright  Copyright &copy; 2012 Roni Saha
 * @license    GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

class ezrbac
{

    /**
     * @var CI_Controller
     */
    private $CI;

    /**
     * @var
     */
    private $user;

    /**
     *
     */
    public function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model('ezuser');
        $this->CI->load->library('ezlogin');
        $this->CI->load->model('manage/user_role');
    }

    /**
     * return the user object for logged in user
     *
     * @return null|obj
     */
    public function getCurrentUser()
    {
        if (!$this->user) {
            $user_id = $this->getCurrentUserID();
            if (!$user_id) {
                return NULL;
            }

            $this->user = $this->CI->ezuser->get_user_by_id($user_id);
        }
        return $this->user;
    }

    /**
     * return the user id for logged in user
     *
     * @return null|int
     */
    public function getCurrentUserID()
    {
        $user_id = $this->CI->session->userdata('user_id');
        if ($user_id && !empty($user_id)) {
            return $user_id;
        }

        return NULL;
    }

    /**
     *
     * set current user object while login
     *
     * @param $user
     *
     * @return mixed
     */
    public function setCurrentUser($user)
    {
        return $this->user = $user;
    }

    /**
     * check if accessing as guest user or not
     *
     * @return bool
     */
    public function isGuest()
    {
        $guest = TRUE;

        if ($this->CI->session->userdata($this->CI->config->item('login_session_key', 'ez_rbac'))) {
            $guest = !$this->getCurrentUserID();
        }

        return $guest;

    }

    /**
     * api create new user
     *
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     */
    public function createUser($data = array())
    {

        if (!(isset($data['user_role_id']) || isset($data['user_role']))) {
            throw new Exception('You must provide user_role_id or user_role');
        }

        if (!isset($data['user_role_id'])) {
            $data['user_role_id'] = $this->CI->user_role->create($data['user_role']);
        }

        $user_id = $this->CI->ezuser->create($data);

        if ($user_id && isset($data['meta'])) {
            $this->CI->ezuser->update_user_meta($user_id, $data['meta']);
        }

        return $user_id;
    }

    /**
     *
     * api update user data
     *
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     */
    public function updateUser($data = array())
    {
        if (!isset($data['id'])) {
            throw new Exception('You must provide id');
        }

        if (isset($data['user_role'])) {
            $data['user_role_id'] = $this->CI->user_role->create($data['user_role']);
        }

        $success = $this->CI->ezuser->update($data);

        if ($data['id'] && isset($data['meta'])) {
            $this->CI->ezuser->update_user_meta($data['id'], $data['meta']);
        }

        return $success;
    }

    /**
     * Helpful if you have your login verification script, you can register session just by passing the user object
     *
     * @param      $user
     * @param bool $remember
     *
     * @return bool
     * @throws Exception
     */
    public function registerUserSession($user, $remember = FALSE)
    {
        if (!is_object($user)) {
            $user = $this->CI->ezuser->get_user_by_id($user);
            if (!$user) {
                throw new Exception('The user not found!');
            }
        }
        $success = $this->ezlogin->register_session($user, $remember);

        if (!$success) {
            throw new Exception($this->ezlogin->getError());
        }

        return TRUE;
    }

    /**
     *
     * return the user meta data
     *
     * @param $user_id
     *
     * @return mixed
     */
    public function getUserMeta($user_id)
    {
        return $this->CI->ezuser->get_user_meta($user_id);
    }

    /**
     * find and return user object by user ID
     *
     * @param $user_id
     *
     * @return mixed
     */
    public function getUserByID($user_id)
    {
        return $this->CI->ezuser->get_user_by_id($user_id);
    }

    /**
     * find and return Logout url
     *
     * @return string
     */
    public function logoutUrl()
    {
        return $this->CI->ezuri->logout();
    }
}