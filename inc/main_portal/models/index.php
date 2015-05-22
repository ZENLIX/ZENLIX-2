

<?php

$CONF['title_header'] = get_conf_param('name_of_firm') . " - SUPPORT CENTER";

include "head.inc.php";

include "navbar.inc.php";

$portal_msg_status = false;
if (get_conf_param('portal_msg_status') == "true") {
    
    switch (get_conf_param('portal_msg_type')) {
        case 'info':
            $ic = "callout-info";
            $ic1 = "fa-info";
            break;

        case 'warning':
            $ic = "callout-warning";
            $ic1 = "fa-warning";
            break;

        case 'danger':
            $ic = "callout-danger";
            $ic1 = "fa-danger";
            break;

        default:
            $ic = "callout-info";
            $ic1 = "fa-info";
            break;
    }
    $portal_msg_status = true;
}

ob_start();
get_main_manual();
$get_main_manual = ob_get_contents();
ob_end_clean();

$portal_posts_status_arr = array();
$portal_posts_status = false;
$stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=1
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit 3');
$stmt->execute();
$res1 = $stmt->fetchAll();
if (!empty($res1)) {
    $portal_posts_status = true;
    foreach ($res1 as $r) {
        array_push($portal_posts_status_arr, array(
            
            'uniq_id' => $r['uniq_id'],
            'subj' => $r['subj'],
            'get_post_rate' => get_post_rate($r['uniq_id']) ,
            'get_post_status' => get_post_status($r['uniq_id']) ,
            'PORTAL_comments' => lang('PORTAL_comments') ,
            'get_count_comments' => get_count_comments($r['uniq_id']) ,
            'get_official_comments' => get_official_comments($r['uniq_id'])
        ));
    }
}

$portal_posts_status_arr2 = array();
$portal_posts_status2 = false;
$stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=2
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit 3');
$stmt->execute();
$res1 = $stmt->fetchAll();
if (!empty($res1)) {
    $portal_posts_status2 = true;
    foreach ($res1 as $r) {
        
        array_push($portal_posts_status_arr2, array(
            
            'uniq_id' => $r['uniq_id'],
            'subj' => $r['subj'],
            'get_post_rate' => get_post_rate($r['uniq_id']) ,
            'get_post_status' => get_post_status($r['uniq_id']) ,
            'PORTAL_comments' => lang('PORTAL_comments') ,
            'get_count_comments' => get_count_comments($r['uniq_id']) ,
            'get_official_comments' => get_official_comments($r['uniq_id'])
        ));
    }
}

$portal_posts_status_arr3 = array();
$portal_posts_status3 = false;
$stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=3
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit 3');
$stmt->execute();
$res1 = $stmt->fetchAll();
if (!empty($res1)) {
    $portal_posts_status3 = true;
    
    foreach ($res1 as $r) {
        array_push($portal_posts_status_arr3, array(
            
            'uniq_id' => $r['uniq_id'],
            'subj' => $r['subj'],
            'get_post_rate' => get_post_rate($r['uniq_id']) ,
            'PORTAL_comments' => lang('PORTAL_comments') ,
            'get_count_comments' => get_count_comments($r['uniq_id']) ,
            'get_official_comments' => get_official_comments($r['uniq_id'])
        ));
    }
}

$portal_posts_status_arr4 = array();
$portal_posts_status4 = false;
$stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=4
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit 3');
$stmt->execute();
$res1 = $stmt->fetchAll();
if (!empty($res1)) {
    $portal_posts_status4 = true;
    
    foreach ($res1 as $r) {
        array_push($portal_posts_status_arr4, array(
            
            'uniq_id' => $r['uniq_id'],
            'subj' => $r['subj'],
            'get_post_rate' => get_post_rate($r['uniq_id']) ,
            'PORTAL_comments' => lang('PORTAL_comments') ,
            'get_count_comments' => get_count_comments($r['uniq_id']) ,
            'get_official_comments' => get_official_comments($r['uniq_id'])
        ));
    }
}

ob_start();
view_release_bar();
$view_release_bar = ob_get_contents();
ob_end_clean();

ob_start();
view_top_news_bar();
$view_top_news_bar = ob_get_contents();
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
    $template = $twig->loadTemplate('index.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'portal_msg_status' => $portal_msg_status,
        'ic' => $ic,
        'ic1' => $ic1,
        'portal_msg_title' => get_conf_param('portal_msg_title') ,
        'portal_msg_text' => get_conf_param('portal_msg_text') ,
        'PORTAL_idea_one' => lang('PORTAL_idea_one') ,
        'PORTAL_trouble_one' => lang('PORTAL_trouble_one') ,
        'PORTAL_question_one' => lang('PORTAL_question_one') ,
        'PORTAL_thank_one' => lang('PORTAL_thank_one') ,
        'PORTAL_idea_you' => lang('PORTAL_idea_you') ,
        'PORTAL_next' => lang('PORTAL_next') ,
        'PORTAL_trouble_you' => lang('PORTAL_trouble_you') ,
        'PORTAL_question_you' => lang('PORTAL_question_you') ,
        'PORTAL_thank_you' => lang('PORTAL_thank_you') ,
        'PORTAL_maybe' => lang('PORTAL_maybe') ,
        'PORTAL_now_new_post' => lang('PORTAL_now_new_post') ,
        'PORTAL_help_center' => lang('PORTAL_help_center') ,
        'get_main_manual' => $get_main_manual,
        'PORTAL_idea' => lang('PORTAL_idea') ,
        'portal_posts_status' => $portal_posts_status,
        'portal_posts_status_arr' => $portal_posts_status_arr,
        'PORTAL_idea_all' => lang('PORTAL_idea_all') ,
        'get_total_posts_by_type1' => get_total_posts_by_type('1') ,
        'MSG_no_records' => lang('MSG_no_records') ,
        'PORTAL_trouble' => lang('PORTAL_trouble') ,
        'portal_posts_status_arr2' => $portal_posts_status_arr2,
        'portal_posts_status2' => $portal_posts_status2,
        'PORTAL_trouble_all' => lang('PORTAL_trouble_all') ,
        'get_total_posts_by_type2' => get_total_posts_by_type('2') ,
        'PORTAL_question' => lang('PORTAL_question') ,
        'portal_posts_status3' => $portal_posts_status3,
        'portal_posts_status_arr3' => $portal_posts_status_arr3,
        'PORTAL_question_all' => lang('PORTAL_question_all') ,
        'get_total_posts_by_type3' => get_total_posts_by_type('3') ,
        'PORTAL_thank' => lang('PORTAL_thank') ,
        'portal_posts_status_arr4' => $portal_posts_status_arr4,
        'portal_posts_status4' => $portal_posts_status4,
        'PORTAL_thank_all' => lang('PORTAL_thank_all') ,
        'get_total_posts_by_type4' => get_total_posts_by_type('4') ,
        'view_release_bar' => $view_release_bar,
        'view_top_news_bar' => $view_top_news_bar,
        'view_stat_cat' => $view_stat_cat
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}


include "footer.inc.php";
?>