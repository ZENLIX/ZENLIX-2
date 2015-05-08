<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
     $CONF['title_header'] = lang('USERS_list') . " - " . $CONF['name_of_firm'];

    include ("head.inc.php");
    include ("navbar.inc.php");
    
    /*
    
    если есть права на добавление или редактирование то показывать страницу
    
    если есть на добавление - кнопка добавить пользователя - форма (аппрув)
    если есть на редактирование -  кнопка напротив редактировать - форма редактирования (аппрув)
    
    get_user_val('priv_add_client')
    get_user_val('priv_edit_client')
    
    
    */














$priv_add_client=false;
if (get_user_val('priv_add_client') == "1") {
$priv_add_client=true;
}

$ae=false;
if ((!isset($_GET['add']) && (!isset($_GET['edit'])))) {
$ae=true;
}




if (isset($_GET['add'])) {
$get_menu="add";
ob_start();
$_POST['menu'] = "new";
include_once ("clients.inc.php");
$clients_inc = ob_get_contents();
ob_end_clean();
}



else if (isset($_GET['list'])) {
    $get_menu="list";
    ob_start();
$_POST['menu'] = "list";
$_POST['page'] = "1";
include_once ("clients.inc.php");
$clients_inc = ob_get_contents();
ob_end_clean();
}

else if (isset($_GET['edit'])) {
    $get_menu="edit";
ob_start();
$_POST['menu'] = "edit";
$_POST['id'] = $_GET['edit'];
include_once ("clients.inc.php");
$clients_inc = ob_get_contents();
ob_end_clean();
}

else {
    $get_menu="else";
    ob_start();
$_GET['list'] = "s";
        $_POST['menu'] = "list";
        $_POST['page'] = "1";
        include_once ("clients.inc.php");
        $clients_inc = ob_get_contents();
ob_end_clean();
}



    $basedir = dirname(dirname(__FILE__)); 
            ////////////
    try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/views');
            
            // инициализируем Twig
            $twig = new Twig_Environment($loader);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('clients.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'hostname'=>$CONF['hostname'],
                'name_of_firm'=>$CONF['name_of_firm'],
                'UNITS_title_ext'=>lang('UNITS_title_ext'),
                'USERS_list'=>lang('USERS_list'),
                'priv_add_client'=>$priv_add_client,
                'USERS_create'=>lang('USERS_create'),
                'WORKERS_info'=>lang('WORKERS_info'),
                'ae'=>$ae,
                'NEW_fio'=>lang('NEW_fio'),
                'clients_inc'=>$clients_inc,
                'get_total_pages_clients'=>get_total_pages_clients(),
                'get_menu'=>$get_menu
                



            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }

    include ("footer.inc.php");

} else {
    include '../auth.php';
}
?>