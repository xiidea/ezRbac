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


Current Active Version
======================
[v1.2.1 Released](https://github.com/xiidea/ezRbac/archive/v1.2.1.zip)!

 
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


Dependencies
============
To use this library you need **Codeigniter 2.1**
