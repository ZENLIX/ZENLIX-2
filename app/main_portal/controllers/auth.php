

<?php
include "head.inc.php";

include "navbar.inc.php";

if ($_SESSION['z.times'] >= 5) {
    
    //$vart = "bf";
    $rt = time() - $_SESSION['z.times_lt'];
    if ($rt > $CONF['bf_pass']) {
        
        //показать форму логина
        $login_form = true;
        unset($_SESSION['z.times']);
        unset($_SESSION['z.times_lt']);
    } 
    else if ($rt <= $CONF['bf_pass']) {
        $login_form = false;
        
        //не показать форму логина
        
        
    }
} 
else if ($_SESSION['z.times'] < 5) {
    
    //показать форму логина
    $login_form = true;
}

if ($CONF['main_portal'] == true) {
    $link = "auth";
} 
else if ($CONF['main_portal'] == false) {
    $link = "index.php";
}

$basedir = dirname(dirname(dirname(__FILE__)));

////////////
try {
    
    // указывае где хранятся шаблоны
    $loader = new Twig_Loader_Filesystem($basedir . '/main_portal/views');
    
    // инициализируем Twig
    if (get_conf_param('twig_cache') == "true") {
        $twig = new Twig_Environment($loader, array(
            'cache' => $basedir . '/cache',
        ));
    } 
    else {
        $twig = new Twig_Environment($loader);
    }
    
    // подгружаем шаблон
    $template = $twig->loadTemplate('auth.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'PORTAL_auth' => lang('PORTAL_auth') ,
        'get_logo_img' => get_logo_img() ,
        'login_form' => $login_form,
        'LOGIN_ERROR_title' => lang('LOGIN_ERROR_title') ,
        'LOGIN_ERROR_desc' => lang('LOGIN_ERROR_desc') ,
        'link' => $link,
        'login' => lang('login') ,
        'pass' => lang('pass') ,
        'remember_me' => lang('remember_me') ,
        'log_in' => lang('log_in') ,
        'allow_register' => get_conf_param('allow_register') ,
        'allow_forgot' => get_conf_param('allow_forgot') ,
        'REG_new' => lang('REG_new') ,
        'Forgot_pass_me' => lang('Forgot_pass_me') ,
        'va' => $va,
        'error_auth' => lang('error_auth') ,
        'REQUEST_URI' => $_SERVER['REQUEST_URI']
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}

include "footer.inc.php";
?>