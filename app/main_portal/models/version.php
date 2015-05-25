<?php

$rkeys = array_keys($_GET);

$hn = $rkeys[0];
$CONF['title_header'] = get_conf_param('name_of_firm') . " - " . lang('PORTAL_versions');
if (isset($hn)) {
    $news_item = get_version_info($hn);
    $CONF['title_header'] = get_conf_param('name_of_firm') . " - " . $news_item['subj'];
}
include "head.inc.php";

include "navbar.inc.php";

$validate_user = false;
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    $validate_user = true;
}

if ($hn) {
    $hnset = true;
    
    if ($hn == "new_feed") {
        $hnparam = "new_feed";
    } 
    else if ($hn != "new_feed") {
        $hnparam = "no_new_feed";
        $news_item = get_version_info($hn);
        
        if (isset($_GET['edit_feed'])) {
            $hnget = "edit_feed";
        } 
        else if (!isset($_GET['edit_feed'])) {
            $hnget = "no_edit_feed";
        }
    }
}

if (!$hn) {
    $hnset = false;
}

$logo_img = $CONF['hostname'] . "upload_files/avatars/" . get_conf_param('logo_img');
if (strlen(get_conf_param('logo_img')) < 5) {
    $logo_img = $CONF['hostname'] . 'img/ZENLIX_small.png';
}

$getna = array();
$news_arr = get_version_array();
foreach ($news_arr as $n) {
    array_push($getna, array(
        
        'uniq_id' => $n['uniq_id'],
        'subj' => $n['subj'],
        'title' => $n['title'],
        'dt' => $n['dt']
    ));
}

ob_start();
showMenu_todo();
$showMenu_todo = ob_get_contents();
ob_end_clean();

ob_start();
get_main_todo();
$get_main_todo = ob_get_contents();
ob_end_clean();

ob_start();
view_release_bar();
$view_release_bar = ob_get_contents();
ob_end_clean();

ob_start();
view_stat_cat();
$view_stat_cat = ob_get_contents();
ob_end_clean();

$basedir = dirname(dirname(dirname(__FILE__)));

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
    $template = $twig->loadTemplate('version.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'validate_user' => $validate_user,
        'hnset' => $hnset,
        'hnparam' => $hnparam,
        'hnget' => $hnget,
        'PORTAL_new_msg' => lang('PORTAL_new_msg') ,
        'PORTAL_subj' => lang('PORTAL_subj') ,
        'PORTAL_t' => lang('PORTAL_t') ,
        'PORTAL_news_create' => lang('PORTAL_news_create') ,
        'time' => md5(time()) ,
        'subj' => $news_item['subj'],
        'title' => $news_item['title'],
        'msg' => $news_item['msg'],
        'PORTAL_news_save' => lang('PORTAL_news_save') ,
        'uniq_id' => $news_item['uniq_id'],
        'logo_img' => $logo_img,
        'REQUEST_URI' => urlencode($CONF['real_hostname'] . $_SERVER['REQUEST_URI']) ,
        'dt' => $news_item['dt'],
        'nameshort' => nameshort(name_of_user_ret_nolink($news_item['author_id'])) ,
        'PORTAL_act_del' => lang('PORTAL_act_del') ,
        'PORTAL_act_edit' => lang('PORTAL_act_edit') ,
        'PORTAL_version_box_title' => lang('PORTAL_version_box_title') ,
        'portal_box_version_n' => get_conf_param('portal_box_version_n') ,
        'FIELD_type_text' => lang('FIELD_type_text') ,
        'portal_box_version_text' => get_conf_param('portal_box_version_text') ,
        'PORTAL_icon' => lang('PORTAL_icon') ,
        'portal_box_version_icon' => get_conf_param('portal_box_version_icon') ,
        'JS_save' => lang('JS_save') ,
        'PORTAL_todo_1' => lang('PORTAL_todo_1') ,
        'showMenu_todo' => $showMenu_todo,
        'NOTES_create' => lang('NOTES_create') ,
        'PORTAL_todo_2' => lang('PORTAL_todo_2') ,
        'get_main_todo' => $get_main_todo,
        'PORTAL_versions' => lang('PORTAL_versions') ,
        'getna' => $getna,
        'PORTAL_admin_menu' => lang('PORTAL_admin_menu') ,
        'PORTAL_news_create' => lang('PORTAL_news_create') ,
        'view_stat_cat' => $view_stat_cat,
        'view_release_bar' => $view_release_bar
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}

include "footer.inc.php";
?>