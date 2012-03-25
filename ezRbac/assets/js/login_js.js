display = {
    login:function () {
        document.getElementById('log_in_div').style.display = '';
        document.getElementById('get_password_div').style.display = 'none';
    },
    password:function () {
        document.getElementById('log_in_div').style.display = 'none';
        document.getElementById('get_password_div').style.display = '';
    }
}