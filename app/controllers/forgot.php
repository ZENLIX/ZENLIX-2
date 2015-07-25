<?php
include_once ("head.inc.php");

//include("dbconnect.inc.php");

$allow_forgot = false;
if (get_conf_param('allow_forgot') == "true") {
    
    $allow_forgot = true;
}

if ($_GET['m']) {
    $get = true;
    $ct = false;
    
    if (!empty($_GET['uc']) && !empty($_GET['ph'])) {
        
        $uniq_code = $_GET['uc'];
        $pass_md = $_GET['ph'];
        
        $stmt = $dbConnection->prepare('select pass from users where uniq_id=:uniq_id limit 1');
        $stmt->execute(array(
            ':uniq_id' => $uniq_code
        ));
        $r = $stmt->fetchAll();
        
        if (!empty($r)) {
            foreach ($r as $v) {
                
                //echo md5($v['pass'])." == ".$pass_md;
                if (md5($v['pass']) == $pass_md) {
                    $ct = true;
                }
            }
        }
        
        //echo "change";
        
        
    }
} 
else if (!$_GET['m']) {
    $get = false;
}

try {
    
    // указывае где хранятся шаблоны
    $loader = new Twig_Loader_Filesystem($basedir.'/views');
    
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
    $template = $twig->loadTemplate('forgot.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'name_of_firm' => $CONF['name_of_firm'],
        'allow_forgot' => $allow_forgot,
        'get' => $get,
        'ct' => $ct,
        'get_logo_img' => get_logo_img() ,
        'MAIN_TITLE' => lang('MAIN_TITLE') ,
        'P_pass_new' => lang('P_pass_new') ,
        'P_pass_new_re' => lang('P_pass_new_re') ,
        'P_do_edit_pass' => lang('P_do_edit_pass') ,
        'va' => $va,
        'error_auth' => lang('error_auth') ,
        'req' => $_SERVER['REQUEST_URI'],
        'uc' => $_GET['uc'],
        'ph' => $_GET['ph'],
        'USERS_login' => lang('USERS_login') ,
        'FORGOT_button' => lang('FORGOT_button')
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}
?>
