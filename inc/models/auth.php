<?php
include_once ("head.inc.php");

//include("dbconnect.inc.php");

if ($CONF['main_portal'] == true) {
    $link = "auth";
} 
else if ($CONF['main_portal'] == false) {
    $link = "index.php";
}


if (!isset($va)) {
    $va=NULL;
}


if (!isset($_SESSION['z.times'])) {
    $_SESSION['z.times']=NULL;
}

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







$allow_register = false;
if (get_conf_param('allow_register') == "true") {
    $allow_register = true;
}

$allow_forgot = false;
if (get_conf_param('allow_forgot') == "true") {
    $allow_forgot = true;
}

$htf = true;
$filename = realpath(dirname(dirname(dirname((__FILE__))))) . "/.htaccess";
if (!file_exists($filename)) {
    $htf = false;
}

$upw = true;
$filename3 = realpath(dirname(dirname(dirname((__FILE__))))) . "/upload_files/";
if (!is_writable($filename3)) {
    $upw = false;
}

try {
    
    // указывае где хранятся шаблоны
    $loader = new Twig_Loader_Filesystem($basedir.'/views');
    
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
        'name_of_firm' => $CONF['name_of_firm'],
        'link' => $link,
        'login_form' => $login_form,
        'LOGIN_ERROR_title' => lang('LOGIN_ERROR_title') ,
        'LOGIN_ERROR_desc' => lang('LOGIN_ERROR_desc') ,
        'get_logo_img' => get_logo_img() ,
        'login' => lang('login') ,
        'pass' => lang('pass') ,
        'remember_me' => lang('remember_me') ,
        'log_in' => lang('log_in') ,
        'allow_register' => $allow_register,
        'allow_forgot' => $allow_forgot,
        'REG_new' => lang('REG_new') ,
        'Forgot_pass_me' => lang('Forgot_pass_me') ,
        'va' => $va,
        'error_auth' => lang('error_auth') ,
        'req' => $_SERVER['REQUEST_URI'],
        'd' => date("Y") ,
        'version' => get_conf_param('version') ,
        'short_open_tag' => ini_get('short_open_tag') ,
        'htf' => $htf,
        'upw' => $upw,
        'MAIN_TITLE' => lang('MAIN_TITLE') ,
        'filename' => $filename,
        'filename3' => $filename3
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}
?>
