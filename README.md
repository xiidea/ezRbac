ezRbac
======
A simple yet easy to implement Role Based Access Control Library for popular PHP framework Codeigniter


Key Features
============
* Easy to integrate with Codeigniter Application
* User Login system with password recovery integrated
* Easy To Customize!!!
* Integrated Access Control Management Interface
* Configurable DB Table name
* Support Clean Url routing!
* Easy to adapt existing schema just by editing config file!!
* Usable with your existing login system!
* [Api](./docs/api.md) to interact with library
* User meta table include profile data (like: name, address etc)for an user
* Mailer function override to send recovery email using your own function
* Configuration and view files can be overridden!
* Coming more....!!!


Current Stable Release
======================
[v1.2.2 Released](https://github.com/xiidea/ezRbac/archive/v1.2.2.zip)!

 
How To Install
==============
Installation of this library is simple 4 steps

1. Put **ezRbac** in the **third_party** Directory of your application

2. Run the SQL in schema directory or create three tables in your database manually.

3. Set <code>$config['enable_hooks'] = TRUE;</code> at **./application/config/config.php**

4. Add a hook in **./application/config/hooks.php** 

```php
$hook['post_controller_constructor'] = array(
    'class' => 'ezRbacHook',
    'function' => 'AccessCheck',
    'filename' => 'ezRbacHook.php',
    'filepath' => 'third_party/ezRbac'
);
```

Map custom actions to access map
================================
Define a public function named access_map in your controller something like bellow:   

```php
 public function access_map(){
        return array(
            'index'=>'view',
            'update'=>'edit'
        );
    }
```
The index of the array is the actual action name, and the value mapped to the access privileges configured as <code>$config['default_access_map'] = array("view", "create", "edit", "delete", "publish")<code>


##Default credential   
If you have imported the provided data.sql then the default credential is

    user: 		admin@admin.com
    password: 	123456


ezRbac Specific URL
===================
logout url : <code>/index.php/welcome/index/rbac/logout</code>

acl manage url : <code>/index.php/welcome/index/rbac/gui</code>

If you have enabled the routing(see **How to eneable Routing**) then you can access all url like <code> /rbac/(logout|gui)</code>

How to enable Routing
======================
Its easy to enable with 2 steps

1. Set <code>$config['use_routing'] = true;</code> at **./ezRbac/config/ez_rbac.php**

2. set <code>$route['^(rbac)/(.+)$'] = $route['default_controller']."/index/$1/$2"; </code> at **/application/config/routes.php** (where **rbac** can be replaced whatever you like by setting the <code>$config['ezrbac_url'] = 'rbac';</code>)

Customization
=============
Most of the customization can be done through setting configuration values. you can customize configuration in two ways:

1. (The Easy way) Modify the configuration to match with your choice at **./third_party/ezRbac/config/ez_rbac.php**
2. (The Advanced way) Make a copy of **./third_party/ezRbac/config/ez_rbac.php** as **./application/config/ez_rbac.php** or Create new file to override the default settings. These way whenever you do update the library, you do not need to worry about your configuration values.

You can override the view file also. just copy the view files into **/application/views/ezrbac/** and modify as per your need. Currently only views in **login** directory is extensible.

Configuration Options
=====================
* **public_controller**    
type: array    
default : array()    
List of public controller which access will not be checked by our library

* **ajax_no_permission_msg**    
type: string  
default : You do not have permission to perform this action!
Message to return while performing an ajax request instead of viewing the full restricted page, so you can handle the situation by your self.

* **default_access**  
type: boolean  
default : FALSE  
The default access for a unknown method TRUE for give access denied otherwise.

* **default_access_map**  
type: array  
default : array("view", "create", "edit", "delete", "publish")  
Access Map array Used by the system. You can name these as you like and you can add/remove access name.

* **autologin_cookie_life**  
type: int  
default : 2592000 (30 days)  
The time in second to remember user login session with remember me checked.

* **autologin_cookie_name**  
type: string  
default : ezrbac_remember_me  
The autologin cookie name used to store user data  with remember me checked.

* **redirect_url**  
type: string  
default : ""  
The redirect url if access denied for a resource url(should be a public controller). Leave it empty if you are not sure about this

* **login_url**  
type: string  
default : ""  
If you like to handle login then set your login url(should be a public controller) here, Leave it empty to let me handle it!! when you handling login,
use <code>$this->ezrbac->getCurrentUser($user)</code> to register the user session.

* **login_session_key**  
type: string  
default : access_role  
Login check session key name,  used to check if a user is logged in or not. Without modification of core library,
you may use any of these: user_id/user_email/access_role

* **password_min_length**  
type: int  
default : 6  
The password validation rule check for minimum length.

* **password_recovery_email**  
type: string  
default : noreply@yourdomain.com  
The From email for password recovery email

* **password_recovery_email_name**  
type: string  
default : EzRbac  
The From name for password recovery email

* **password_recovery_subject**  
type: string  
default : Password Reset Request  
The subject for password recovery email

* **auto_login_table**  
type: string  
default : user_autologin  
The database table name used to store auto login data

* **user_table**  
type: string  
default : system_users  
The database table  name used to store user data

* **user_meta_table**  
type: string  
default : user_meta  
The database table  name used to store user meta data

* **user_role_table**  
type: string  
default : user_role  
The database table  name used to store user role info

* **access_map_table**  
type: string  
default : user_access_map  
The database table  name used to store Access Control List as per user role

* **schema_user_table**  
type: array  
```php 
default : array(
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
```   
The database field map for your existing system. Helpful to adapt your db without modifying the code!!

* **schema_user_role**  
type: array  
default : <code> array(
              'id'        => 'id',
              'role_name' => 'role_name'
          );</code>  
The database field map for your existing system. Helpful to adapt your db without modifying the code!!

* **user_meta_user_id**  
type: string  
default : user_id  
The foreign key name in user meta table

* **show_password_reset_mail**  
type: boolean  
default : FALSE  
Enable showing the email on browser rather then sending it. for debug purpose. do not set true in the production

* **override_email_function**  
type: string|false  
default : FALSE  
You can use your own function to send email. if you set this value to 'name_of_your_function'. <code>name_of_your_function($option)</code> will be called to send email
where <code>$option = array('subject'=>'','from'=>'', 'from_name'=>'', 'to'=>'','body'=>'');</code>

* **enable_ezrbac_gui**  
type: boolean  
default : TRUE  
Enable or disable the management interface

* **ezrbac_gui_url**  
type: string  
default : gui  
Url identifier for ezrbac gui interface access

* **ezrbac_password**  
type: string  
default : hardtoremember  
Password to access management interface of ACL

* **ezrbac_url**  
type: string  
default : rbac  
Ezrbac specific url identifier.

* **use_assets_within_package**  
type: boolean  
default : TRUE  
This configuration value tell the library from where it should use the resource. if set to true the js/css/images used in the library views will be served from the assets directory in package (helpful for quick setup).
For advance user it will be better to copy the contents of assets directory in a web accessible location
and set the <code>$config['assets_base_directory']='the/relative/path/of/assets/directory/from/root' </code>

* **assets_base_directory**  
type: string  
default : rbac  
Optional only used if you set the the <code>$config['use_assets_within_package']=false; </code>
Then set the relative path of assets directory from root


* **use_routing**  
type: boolean  
default : FALSE  
enable clean url for management interface by adding routing rule. if <code>$config['ezrbac_url']='rbac'</code> then you can add <code>$route['^(rbac)/(.+)$'] = $route['default_controller']."/index/$1/$2"; </code> and set the value to true.

Dependencies
============
To use this library you need **Codeigniter 2.1**
