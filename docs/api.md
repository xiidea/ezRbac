ezRbac Api reference
=====================
You can use this api from your controller to interact with this library. Api methods are accessible through ezrbac property of controller.
For an example you can use $this->ezrbac->getCurrentUser() from your controller to get the user object for logged in user.


Available API
==============
* getCurrentUser() - return the user object for logged in user
* getCurrentUserID() - return the user id for logged in user
* isGuest() - check if accessing as guest user or not
* createUser($userdata) - create new user with provided [$userdata][1] parameter
* updateUser($userdata) - update existing user with provided [$userdata][1] parameter
* registerUserSession($user) - Helpful if you have your login verification script, you can register session just by passing the user object
* getUserMeta($user) - return the user meta data
* getUserByID($user_id) - find and return user object by user ID
* logoutUrl() - return the logout url

$userdata details
=================
The **$userdata** array can contain the following fields

* **id**              - An integer The user id(while update only)
* **user_role**       - A string that contain User Role of the user(you can provide user role or the **user_role_id** whatever you like if provided role is not exist it will be created).
* **user_role_id**    - An integer that contain User Role ID of the user(You have to provide only if you did not give the **user_role** ).
* **meta**       - An array containing user meta data. array must be as per schema definition of user_meta table. You have full freedom to create your meta_table with your requirement. for example table you can provide the value as <code>array('first_name'=>'First', 'last_name'=>'Last')</code>
* **email**           - A string that contain user email address
* **password**        - A string that contain plain password for the user
* **status**          - An integer 0(Active user) or 1(Disabled User)

[1]: ./api.md#userdata-details
