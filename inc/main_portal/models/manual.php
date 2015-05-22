<?php

$rkeys = array_keys($_GET);

$hn = $rkeys[0];

$CONF['title_header'] = get_conf_param('name_of_firm') . " - " . lang('PORTAL_help_center');
if (isset($hn)) {
    
    $news_item = get_manual_info($hn);
    $CONF['title_header'] = get_conf_param('name_of_firm') . " - " . $news_item['name'];
}
include "head.inc.php";

include "navbar.inc.php";
if ($hn == "qa") {
    $news_item = get_qa_obj($_GET['qa']);
    $CONF['title_header'] = get_conf_param('name_of_firm') . " - " . $news_item['question'];
}

$val_user = false;
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    $val_user = true;
}

if ($hn) {
    $hn_set = true;
    
    if ($hn == "edit_some_qa") {
        $hn_param = "edit_some_qa";
        $news_item = get_qa_obj($_GET['edit_some_qa']);
    } 
    else if ($hn == "edit_qa") {
        $hn_param = "edit_qa";
    } 
    else if ($hn == "edit_cat") {
        $hn_param = "edit_cat";
    } 
    else if ($hn == "qa") {
        $hn_param = "qa";
        $news_item = get_qa_obj($_GET['qa']);
    } 
    else if ($hn == "find") {
        $hn_param = "find";
        $t = $_GET['find'];
        
        $ex = explode(" ", $t);
        
        foreach ($ex as $value) {
            $stmt = $dbConnection->prepare("SELECT * from portal_posts where (portal_posts.subj like :t) limit 10");
            $stmt->execute(array(
                ':t' => '%' . $value . '%'
            ));
            $result = $stmt->fetchAll();
            $find_res = "<ul>";
            foreach ($result as $row) {
                
                $find_res.= "<li style='list-style:none;'>" . get_cat_icon($row['type']) . " <a href=\"" . $CONF['hostname'] . "thread?" . $row['uniq_id'] . "\">" . $row['subj'] . "</a></li>";
                // code...
                
            }
            $find_res.= "</ul>";
        }
        
        foreach ($ex as $value) {
            $stmt = $dbConnection->prepare("SELECT * from portal_manual_cat where (name like :t) limit 10");
            $stmt->execute(array(
                ':t' => '%' . $value . '%'
            ));
            $result = $stmt->fetchAll();
            $find_res.= "<ul>";
            foreach ($result as $row) {
                
                $find_res.= "<li style='list-style:none;'><i class=\"fa fa-graduation-cap\"></i> <a href=\"" . $CONF['hostname'] . "manual?" . $row['uniq_id'] . "\">" . $row['name'] . "</a></li>";
                // code...
                
            }
            $find_res.= "</ul>";
        }
    } 
    else if ($hn != "new_manual") {
        $hn_param = "no_new_manual";
        $news_item = get_manual_info($hn);
        
        if (isset($_GET['edit_manual'])) {
            $get_param = "edit_manual";
        } 
        else if (!isset($_GET['edit_manual'])) {
            $get_param = "no_edit_manual";
        }
    }
}
if (!$hn) {
    $hn_set = false;
}

ob_start();
view_stat_cat();
$view_stat_cat = ob_get_contents();
ob_end_clean();

ob_start();
show_all_manual();
$show_all_manual = ob_get_contents();
ob_end_clean();

ob_start();
showMenu_qa();
$showMenu_qa = ob_get_contents();
ob_end_clean();

ob_start();
showMenu_manual();
$showMenu_manual = ob_get_contents();
ob_end_clean();

ob_start();
view_attach_files($news_item['uniq_id'], 'comment');
$view_attach_files = ob_get_contents();
ob_end_clean();

ob_start();
get_main_manual();
$get_main_manual = ob_get_contents();
ob_end_clean();

ob_start();
show_qa_manual();
$show_qa_manual = ob_get_contents();
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
    $template = $twig->loadTemplate('manual.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'hn_set' => $hn_set,
        'hn_param' => $hn_param,
        'get_param' => $get_param,
        'val_user' => $val_user,
        'PORTAL_edit_qa' => lang('PORTAL_edit_qa') ,
        'PORTAL_q' => lang('PORTAL_q') ,
        'question' => $news_item['question'],
        'answer' => $news_item['answer'],
        'PORTAL_news_save' => lang('PORTAL_news_save') ,
        'uniq_id' => $news_item['uniq_id'],
        'PORTAL_q_manag' => lang('PORTAL_q_manag') ,
        'showMenu_qa' => $showMenu_qa,
        'NOTES_create' => lang('NOTES_create') ,
        'PORTAL_cat_manag' => lang('PORTAL_cat_manag') ,
        'showMenu_manual' => $showMenu_manual,
        'NOTES_create' => lang('NOTES_create') ,
        'dt' => $news_item['dt'],
        'author_id' => nameshort(name_of_user_ret_nolink($news_item['author_id'])) ,
        'PORTAL_s_res' => lang('PORTAL_s_res') ,
        'find_res' => $find_res,
        'PORTAL_edit_n' => lang('PORTAL_edit_n') ,
        'PORTAL_subj' => lang('PORTAL_subj') ,
        'name' => $news_item['name'],
        'msg' => $news_item['msg'],
        'PORTAL_fileplace' => lang('PORTAL_fileplace') ,
        'title' => $news_item['title'],
        'view_attach_files' => $view_attach_files,
        'id' => $news_item['id'],
        'PORTAL_act_del' => lang('PORTAL_act_del') ,
        'PORTAL_act_edit' => lang('PORTAL_act_edit') ,
        'PORTAL_findby_h' => lang('PORTAL_findby_h') ,
        'PORTAL_sel_text' => lang('PORTAL_sel_text') ,
        'PORTAL_find_act' => lang('PORTAL_find_act') ,
        'PORTAL_help_center' => lang('PORTAL_help_center') ,
        'get_main_manual' => $get_main_manual,
        'PORTAL_qa' => lang('PORTAL_qa') ,
        'show_qa_manual' => $show_qa_manual,
        'PORTAL_admin_menu' => lang('PORTAL_admin_menu') ,
        'PORTAL_cat_n_manag' => lang('PORTAL_cat_n_manag') ,
        'PORTAL_cat_list' => lang('PORTAL_cat_list') ,
        'show_all_manual' => $show_all_manual,
        'view_stat_cat' => $view_stat_cat
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}

include "footer.inc.php";
?>