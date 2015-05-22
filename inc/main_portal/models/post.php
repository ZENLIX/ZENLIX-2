<?php
session_start();

$rkeys = array_keys($_GET);

$hn = $rkeys[0];

$stmt = $dbConnection->prepare('SELECT * from portal_posts where uniq_id=:hn');
$stmt->execute(array(
    ':hn' => $hn
));

$post = $stmt->fetch(PDO::FETCH_ASSOC);


if (empty($post)){
    header("Location: " . site_proto() . $_SERVER['HTTP_HOST'] . $CONF['hostname']);
}

$validate = false;
if ((validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) || (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code']))) {
    $validate = true;
}

switch ($post['type']) {
    case '1':
        $icon = '<i class="fa fa-lightbulb-o"></i>';
        
        break;

    case '2':
        $icon = '<i class="fa fa-exclamation-triangle"></i>';
        
        break;

    case '3':
        $icon = '<i class="fa fa-question-circle"></i>';
        break;

    case '4':
        $icon = '<i class="fa fa-heart"></i>';
        break;

    default:
        // code...
        break;
}

if (!$_GET['p']) {
    $p = 1;
} 
else if ($_GET['p']) {
    $p = $_GET['p'];
}

$page = ($p);
$perpage = '10';
$start_pos = ($page - 1) * $perpage;

$CONF['title_header'] = get_conf_param('name_of_firm') . " - " . $post['subj'];

include "head.inc.php";

include "navbar.inc.php";

ob_start();
get_post_rate_post($post['uniq_id']);
$get_post_rate_post = ob_get_contents();
ob_end_clean();

ob_start();
view_likes_button($post['id']);
$view_likes_button = ob_get_contents();
ob_end_clean();

$validate_user = false;
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    $validate_user = true;
}

ob_start();
view_attach_files($post['uniq_id'], 'post');
$view_attach_files_main = ob_get_contents();
ob_end_clean();

$official_status = false;
$official_arr = array();
$stmt = $dbConnection->prepare('SELECT * from post_comments where p_id=:pid and official=1 order by dt asc');
$stmt->execute(array(
    ':pid' => $post['id']
));
$res1 = $stmt->fetchAll();
if (!empty($res1)) {
    $official_status = true;
    
    foreach ($res1 as $r) {
        
        ob_start();
        view_attach_files($r['uniq_hash'], 'comment');
        $view_attach_files = ob_get_contents();
        ob_end_clean();
        
        $ro_dt = $r['dt'];
        $ro_uh = $r['uniq_hash'];
        
        array_push($official_arr, array(
            'nameshort' => nameshort(name_of_user_ret_nolink($r['user_id'])) ,
            'get_user_img_by_id' => get_user_img_by_id($r['user_id']) ,
            'uniq_hash' => $r['uniq_hash'],
            'comment_text' => $r['comment_text'],
            'dt' => $r['dt'],
            'view_attach_files' => $view_attach_files,
            'PORTAL_cancel' => lang('PORTAL_cancel') ,
            'PORTAL_save' => lang('PORTAL_save') ,
            'PORTAL_act_del' => lang('PORTAL_act_del') ,
            'PORTAL_act_edit' => lang('PORTAL_act_edit')
        ));
    }
}

$no_official_status = false;
$no_official_arr = array();

$stmt = $dbConnection->prepare('SELECT * from post_comments where p_id=:pid and official=0  order by dt asc 
              limit :start_pos, :perpage');
$stmt->execute(array(
    ':pid' => $post['id'],
    ':start_pos' => $start_pos,
    ':perpage' => $perpage
));
$res1 = $stmt->fetchAll();

if (!empty($res1)) {
    $no_official_status = true;
    
    $h = 0;
    foreach ($res1 as $r) {
        $line = "<hr>";
        if ($h == 0) {
            $line = "";
        }
        
        ob_start();
        view_attach_files($r['uniq_hash'], 'comment');
        $view_attach_files = ob_get_contents();
        ob_end_clean();
        
        array_push($no_official_arr, array(
            'line' => $line,
            'nameshort' => nameshort(name_of_user_ret_nolink($r['user_id'])) ,
            'get_user_img_by_id' => get_user_img_by_id($r['user_id']) ,
            'uniq_hash' => $r['uniq_hash'],
            'comment_text' => $r['comment_text'],
            'dt' => $r['dt'],
            'view_attach_files' => $view_attach_files,
            'PORTAL_cancel' => lang('PORTAL_cancel') ,
            'PORTAL_save' => lang('PORTAL_save') ,
            'PORTAL_act_del' => lang('PORTAL_act_del') ,
            'PORTAL_act_edit' => lang('PORTAL_act_edit')
        ));
        
        $h++;
    }
}

ob_start();
view_admin_menu($post['uniq_id']);
$view_admin_menu = ob_get_contents();
ob_end_clean();

ob_start();
view_maybe_block($post['uniq_id']);
$view_maybe_block = ob_get_contents();
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
    $template = $twig->loadTemplate('post.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'icon' => $icon,
        'subj' => $post['subj'],
        'get_post_status' => get_post_status($post['uniq_id'], 'e') ,
        'uniq_id' => $post['uniq_id'],
        'msg' => $post['msg'],
        'view_attach_files_main' => $view_attach_files_main,
        'validate_user' => $validate_user,
        'PORTAL_cancel' => lang('PORTAL_cancel') ,
        'PORTAL_save' => lang('PORTAL_save') ,
        'dt' => $post['dt'],
        'PORTAL_adr' => lang('PORTAL_adr') ,
        'get_post_rate_post' => get_post_rate_post($post['uniq_id']) ,
        'validate' => $validate,
        'view_likes_button' => $view_likes_button,
        'official_status' => $official_status,
        'PORTAL_oa' => lang('PORTAL_oa') ,
        'official_arr' => $official_arr,
        'no_official_status' => $no_official_status,
        'no_official_arr' => $no_official_arr,
        'PORTAL_com' => lang('PORTAL_com') ,
        'p' => $p,
        'time' => md5(time()) ,
        'get_total_pages_comments' => get_total_pages_comments($post['uniq_id']) ,
        'PORTAL_must_reg' => lang('PORTAL_must_reg') ,
        'PORTAL_add_comm' => lang('PORTAL_add_comm') ,
        'fio' => nameshort(get_user_val('fio')) ,
        'get_user_img' => get_user_img() ,
        'PORTAL_fileplace' => lang('PORTAL_fileplace') ,
        'PORTAL_stay_comm' => lang('PORTAL_stay_comm') ,
        'PORTAL_author' => lang('PORTAL_author') ,
        'get_user_img_by_id' => get_user_img_by_id($post['author_id']) ,
        'author_id_fio' => get_user_val_by_id($post['author_id'], 'fio') ,
        'author_id_posada' => get_user_val_by_id($post['author_id'], 'posada') ,
        'view_admin_menu' => $view_admin_menu,
        'view_maybe_block' => $view_maybe_block,
        'view_stat_cat' => $view_stat_cat,
        'ro_dt' => $ro_dt,
        'ro_uh' => $ro_uh
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}

include "footer.inc.php";
?>