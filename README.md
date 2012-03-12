ezRbac
======
A simple yet easy to implement Role Based Access Control Library for popular PHP framework Codeigniter


Key Features
============
* Easy to integrate with Codeigniter Application
* User Login system integrated
* Easy To Customize!!!
* Coming many more....!!!
 

Current Active Version
======================
v 0.1 Under development

 
How To Install
==============
Installation of this library is simple 3 steps

1. Put **ezRbac** in the **third_party** Directory of your application

2. Run the sqls in scheme directory or create three tables in your database manually.

3. Add a hook in **./application/config/hooks.php**

```php
    $hook['post_controller_constructor'] = array(
            'class' => 'ezRbacHook',
            'function' => 'AccessCheck',
            'filename' => 'ezRbacHook.php',
            'filepath' => 'third_party/ezRbac'
        );
```


Customization
=============
Modify the configuration to match with your choice at **./ezRbac/config/ez_rbac.php**


Dependencies
============
To use this library you you need **Codeigniter 2.1**
