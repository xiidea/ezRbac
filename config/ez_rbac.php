<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

//List of public controller which access will not be checked by our library
$config['public_controller'] = array();

// Message to return while performing an ajax request
$config['ajax_no_permission_msg'] = "You do not have permission to perform this action!";

//The default access for a unknown method TRUE for give access denied otherwise
$config['default_access'] = FALSE;

//Access Map array Used by the system
$config['default_access_map'] = array("view", "create", "edit", "delete", "publish");

//The remember time for user login with remember me checked
$config['autologin_cookie_life'] = 24 * 60 * 60 * 30;

//The redirect url if access denied for a resource url, should be a public controller
// Leave it empty if you are not sure about this
$config['redirect_url'] = "";

//If you like to handle login then set your login url here, Leave it empty to let me handle it!!
$config['login_url'] = "";
//Login check session key name,  used to check if a user is loged in or not,
// Session will store the access_role_id of loged in user
$config['login_session_key'] = "access_role";

//The password validation rule check for minimum password length
$config['password_min_length'] = 6;
//The autologin cookie name used to store user data
$config['autologin_cookie_name'] = "ezrbac_remember_me";

//The From email for password recovery email
$config['password_recovery_email'] = "noreply@yourdomain.com";
//The From name for password recovery email
$config['password_recovery_email_name'] = "EzRbac";

//The subject for password recovery email
$config['password_recovery_subject'] = "Password Reset Request";


//The database table name used to store autologin data
$config['auto_login_table'] = "user_autologin";
//The database table  name used to store user data
$config['user_table'] = "system_users";
//The database table  name used to store user meta data
$config['user_meta_table'] = "user_meta";
//The database table  name used to store user role info
$config['user_role_table'] = "user_role";
//The database table  name used to store Access Control List as per user role
$config['access_map_table'] = "user_access_map";

//Define schema map
//Helpful to adapt your db without modifying the code!!
$config['schema_user_table'] = array(
    'id'                  => 'id',
    'email'               => 'email',
    'password'            => 'password',
    'salt'                => 'salt',
    'user_role_id'        => 'user_role_id',
    'last_login'          => 'last_login',
    'last_login_ip'       => 'last_login_ip',
    'reset_request_code'  => 'reset_request_code',
    'reset_request_time'  => 'reset_request_time',
    'reset_request_ip'    => 'reset_request_ip',
    'verification_status' => 'verification_status',
    'status'              => 'status'
);
$config['schema_user_role']  = array(
    'id'        => 'id',
    'role_name' => 'role_name'
);

$config['user_meta_user_id'] = 'user_id';

//Enable showing the email on browser rather then sending it. for debug and dev environment
$config['show_password_reset_mail'] = FALSE;

//use your own function to send email
//$config['override_email_function']='name_of_your_function';
//$option = array('subject'=>'','from'=>'', 'from_name'=>'', 'to'=>'','body'=>'');
//  name_of_your_function($option) will be called to send email
$config['override_email_function'] = FALSE;


//Enable or disable the management interface
$config['enable_ezrbac_gui'] = TRUE;
// Url identifier for ezrbac gui interface access
$config['ezrbac_gui_url'] = "gui";
// Password to access management interface of ACL
$config['ezrbac_password'] = "hardtoremember";

//ezrbac specific url identifier
$config['ezrbac_url'] = 'rbac';
//This configuration value tell the library from where it should use the resource
//if set to true the js/css/images used in the library views will be served from the
//assets directory in package (helpful for quick setup)
//for advance user it will be better to copy the contents of assets directory in a web accessible location
//and set the $config['assets_base_directory']='the/relative/path/of/assets/directory/from/root'
$config['use_assets_within_package'] = TRUE;

//Optional only used if you set the the $config['use_assets_within_package']=false
//then set the relative path of assets directory from root
$config['assets_base_directory'] = 'assets';

//enable clean url for management interface by adding routing rule
// if $config['ezrbac_url']='rbac' then
// add $route['^(rbac)/(.+)$'] = $route['default_controller']."/index/$1/$2";
// and set the value to true
$config['use_routing'] = FALSE;


/* End of file ez_rbac.php */
/* Location: ./ezRbac/config/ez_rbac.php */
