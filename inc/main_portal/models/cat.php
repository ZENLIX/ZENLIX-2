

<?php

$rkeys = array_keys($_GET);

$hn = $rkeys[0];

if (!$hn) {
    $hn = 1;
}

switch ($hn) {
    case '1':
        $t = lang('PORTAL_idea');
        $s = "box-success";
        break;

    case '2':
        $t = lang('PORTAL_trouble');
        $s = "box-danger";
        break;

    case '3':
        $t = lang('PORTAL_question');
        $s = "box-info";
        break;

    case '4':
        $t = lang('PORTAL_thank');
        $s = "box-warning";
        break;

    default:
        // code...
        $t = lang('PORTAL_comments');
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

$CONF['title_header'] = get_conf_param('name_of_firm') . " - " . $t;

include "head.inc.php";

include "navbar.inc.php";

$sel_status = false;
$sel_arr = array();
if (isset($_GET['status'])) {
    $stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=:t and p.status=:s
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit :start_pos, :perpage');
    $stmt->execute(array(
        ':start_pos' => $start_pos,
        ':perpage' => $perpage,
        ':t' => $hn,
        ':s' => $_GET['status']
    ));
} 
else if (!isset($_GET['status'])) {
    
    $stmt = $dbConnection->prepare('SELECT *
FROM portal_posts p 
    LEFT JOIN post_comments c on c.p_id = p.id
    where p.type=:t
GROUP BY p.id
ORDER BY COALESCE(GREATEST(p.dt, MAX(c.dt)), p.dt) DESC
limit :start_pos, :perpage');
    $stmt->execute(array(
        ':start_pos' => $start_pos,
        ':perpage' => $perpage,
        ':t' => $hn
    ));
}

$res1 = $stmt->fetchAll();
if (!empty($res1)) {
    $sel_status = true;
    foreach ($res1 as $r) {
        
        array_push($sel_arr, array(
            
            'uniq_id' => $r['uniq_id'],
            'type' => get_cat_icon($r['type']) ,
            'subj' => $r['subj'],
            'get_post_rate' => get_post_rate($r['uniq_id']) ,
            'get_post_status' => get_post_status($r['uniq_id']) ,
            'PORTAL_comments' => lang('PORTAL_comments') ,
            'get_count_comments' => get_count_comments($r['uniq_id']) ,
            'get_official_comments' => get_official_comments($r['uniq_id'])
        ));
    }
}

$status_sel = false;
if (isset($_GET['status'])) {
    $status_sel = true;
}

$status_label['def'] = "active";

if (isset($_GET['status'])) {
    
    switch ($_GET['status']) {
        case '0':
            $status_label['0'] = "active";
            $status_text['0'] = "active-text-cat";
            $status_label['def'] = "";
            break;

        case '1':
            $status_label['1'] = "active";
            $status_text['1'] = "active-text-cat";
            $status_label['def'] = "";
            break;

        case '2':
            $status_label['2'] = "active";
            $status_text['2'] = "active-text-cat";
            $status_label['def'] = "";
            break;

        case '3':
            $status_label['3'] = "active";
            $status_text['3'] = "active-text-cat";
            $status_label['def'] = "";
            break;

        case '4':
            $status_label['4'] = "active";
            $status_text['4'] = "active-text-cat";
            $status_label['def'] = "";
            break;

        default:
            $status_label['def'] = "active";
            break;
    }
}

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
    $template = $twig->loadTemplate('cat.view.tmpl');
    //echo get_total_pages_posts_status($hn, $_GET['status']);
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        't' => $t,
        'get_cat_icon' => get_cat_icon($hn),
        'sel_status' => $sel_status,
        'sel_arr' => $sel_arr,
        'get_total_pages_posts_status' => get_total_pages_posts_status($hn, $_GET['status']) ,
        'status' => $_GET['status'],
        'get_total_pages_posts' => get_total_pages_posts($hn) ,
        'MSG_no_records' => lang('MSG_no_records') ,
        'hn' => $hn,
        'p' => $p,
        's'=>$s,
        'status_sel'=>$status_sel,
        'status_label_def' => $status_label['def'],
        'status_label_0' => $status_label['0'],
        'get_count_post_1_0' => get_count_post('1', '0') ,
        'PORTAL_status_1' => lang('PORTAL_status_1') ,
        'status_label_1' => $status_label['1'],
        'get_count_post_1_1' => get_count_post('1', '1') ,
        'status_text_1' => $status_text['1'],
        'PORTAL_status_2' => lang('PORTAL_status_2') ,
        'status_label_2' => $status_label['2'],
        'get_count_post_1_2' => get_count_post('1', '2') ,
        'status_text_2' => $status_text['2'],
        'PORTAL_status_3' => lang('PORTAL_status_3') ,
        'status_label_3' => $status_label['3'],
        'get_count_post_1_3' => get_count_post('1', '3') ,
        'status_text_3' => $status_text['3'],
        'PORTAL_status_4' => lang('PORTAL_status_4') ,
        'status_label_4' => $status_label['4'],
        'get_count_post_1_4' => get_count_post('1', '4') ,
        'status_text_4' => $status_text['4'],
        'PORTAL_status_5' => lang('PORTAL_status_5') ,
        'get_count_post_2_0' => get_count_post('2', '0') ,
        'get_count_post_2_1' => get_count_post('2', '1') ,
        'get_count_post_2_2' => get_count_post('2', '2') ,
        'get_count_post_2_3' => get_count_post('2', '3') ,
        'get_count_post_2_4' => get_count_post('2', '4') ,
        'PORTAL_trouble_all' => lang('PORTAL_trouble_all') ,
        'PORTAL_idea_all' => lang('PORTAL_idea_all') ,
        'view_stat_cat' => $view_stat_cat,
        'view_top_news_bar' => $view_top_news_bar,
        'PORTAL_status_7' => lang('PORTAL_status_7') ,
        'PORTAL_status_6' => lang('PORTAL_status_6')
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}

include "footer.inc.php";
?>