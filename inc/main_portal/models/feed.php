<?php
$rkeys = array_keys($_GET);

$hn = $rkeys[0];

$CONF['title_header'] = get_conf_param('name_of_firm') . " - " . lang('PORTAL_news');
if (isset($hn)) {
    $news_item = get_news_info($hn);
    $CONF['title_header'] = get_conf_param('name_of_firm') . " - " . $news_item['subj'];
}

include "head.inc.php";

include "navbar.inc.php";

$val_user = false;
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    $val_user = true;
}

if ($hn) {
    $hnset = true;
    
    if ($hn == "new_feed") {
        
        //val
        $hn = 'new_feed';
    } 
    else if ($hn != "new_feed") {
        
        $news_item = get_news_info($hn);
        
        if (isset($_GET['edit_feed'])) {
            $hn = 'edit_feed';
            
            //val
            
            
        } 
        else if (!isset($_GET['edit_feed'])) {
            $hn = 'else';
        }
    }
}
if (!$hn) {
    $hnset = false;
    
    //val
    
    
}

$logo_img = $CONF['hostname'] . "upload_files/avatars/" . get_conf_param('logo_img');
if (strlen(get_conf_param('logo_img')) < 5) {
    $logo_img = $CONF['hostname'] . 'img/ZENLIX_small.png';
}

$news_arr = get_news_array();

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
    $template = $twig->loadTemplate('feed.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'hnset' => $hnset,
        'hn' => $hn,
        'val_user' => $val_user,
        'PORTAL_new_msg' => lang('PORTAL_new_msg') ,
        'PORTAL_subj' => lang('PORTAL_subj') ,
        'PORTAL_ann' => lang('PORTAL_ann') ,
        'PORTAL_news_create' => lang('PORTAL_news_create') ,
        'time' => md5(time()) ,
        'PORTAL_new_msg' => lang('PORTAL_new_msg') ,
        'subj' => $news_item['subj'],
        'PORTAL_ann' => lang('PORTAL_ann') ,
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
        'PORTAL_news' => lang('PORTAL_news') ,
        'news_arr' => $news_arr,
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