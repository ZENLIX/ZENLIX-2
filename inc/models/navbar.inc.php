<?php
function cur_file_name() {
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
    $file = $_SERVER['REQUEST_URI'];
    $file = explode("?", basename($file));
    $current_file_name = $file[0];
    
    //$file = $_SERVER['REQUEST_URI'];
    //$file = explode("?", basename($file));
    
    //if ($current_file_name == $requestUri) echo 'class="active"';
    $current_file_name = explode('&', $current_file_name);
    $cfn = $current_file_name[0];
    
    return $cfn;
}

$p1 = array(
    'config',
    'users',
    'deps',
    'files',
    'scheduler',
    'approve',
    'posada',
    'units',
    'subj',
    'portal',
    'mailers'
);
$p2 = array(
    'main_stats',
    'user_stats',
    'sla_rep'
);
$current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
$file = $_SERVER['REQUEST_URI'];
$file = explode("?", basename($file));

$current_file_name = $file[0];

$current_file_name = explode('&', $current_file_name);
$cfn = $current_file_name[0];

$tree_admin_class = "";
$tree_stat_class = "";

//echo $current_file_name;

if (in_array($cfn, $p1)) {
    $tree_admin_class = "active";
}
if (in_array($cfn, $p2)) {
    $tree_stat_class = "active";
}

$cal_e = calendar_get_count();

if ($cal_e != 0) {
    $cal_et = "<small class=\"badge pull-right bg-info\">" . $cal_e . "</small>";
} 
else if ($cal_e == 0) {
    $cal_et = "";
}

$newt = get_total_tickets_free();

if ($newt != 0) {
    $newtickets = "<small id=\"tt_label\"> <small class=\"badge pull-right bg-red\">" . $newt . "</small></small>";
} 
else if ($newt == 0) {
    $newtickets = "<small id=\"tt_label\"></small>";
}

$ap = get_approve();
if ($ap != 0) {
    $apr = "
    <small class=\"badge pull-right bg-yellow\">" . $ap . "</small>";
} 
else if ($ap == 0) {
    $apr = "";
}

//get_total_unread_messages
//<small class="badge pull-right bg-yellow">12</small>

$tm = get_total_unread_messages();
if ($tm != 0) {
    $atm = "
    <small id=\"label_msg\"> <small class=\"badge pull-right bg-yellow\">" . $tm . "</small></small>";
    $atm_v = $tm;
} 
else if ($tm == 0) {
    $atm = "<small id=\"label_msg\"></small>";
    $atm_v = "";
}

$main_portal = $CONF['main_portal'];

if ($main_portal == "true") {
    $mp = true;
    $index_page = "dashboard";
} 
else if ($main_portal == "false") {
    $mp = false;
    $index_page = "index.php";
}

$stmt = $dbConnection->prepare('select fio,id,uniq_id from users where last_time >= DATE_SUB(:n,INTERVAL 2 MINUTE)');
$stmt->execute(array(
    ':n' => $CONF['now_dt']
));
$re = $stmt->fetchAll();
$ar_online = array();
foreach ($re as $rews) {
    array_push($ar_online, array(
        
        'uniq_id' => $rews['uniq_id'],
        'usr_img' => get_user_img_by_id($rews['id']) ,
        'name' => nameshort(name_of_user_ret_nolink($rews['id'])) ,
        'posada' => get_user_val_by_id($rews['id'], 'posada')
    ));
}

$stmt1 = $dbConnection->prepare('SELECT user_from, msg, date_op from messages where user_to=:uto and is_read=0');
$stmt1->execute(array(
    ':uto' => $_SESSION['helpdesk_user_id']
));

$re = $stmt1->fetchAll();

if (!empty($re)) {
    $titlem = lang('EXT_unread_msg1') . " <strong class=\"label_unread_msg\">" . $atm_v . "</strong> " . lang('EXT_unread_msg2');
} 
else if (empty($re)) {
    $titlem = lang('EXT_no_unread_msg');
}
$ar_msg = array();
foreach ($re as $rews) {
    array_push($ar_msg, array(
        
        'user_code' => get_user_val_by_id($rews['user_from'], 'uniq_id') ,
        'usr_img' => get_user_img_by_id($uniq_id) ,
        'usr_name' => nameshort(name_of_user_ret_nolink($rews['user_from'])) ,
        'date_op' => $rews['date_op'],
        'msg' => make_html($rews['msg'], 'no')
    ));
}

$val_admin = false;
if (validate_admin($_SESSION['helpdesk_user_id'])) {
    $val_admin = true;
}

$some_priv = false;
$priv_val = priv_status($_SESSION['helpdesk_user_id']);
if (($priv_val == "2") || ($priv_val == "0")) {
    $some_priv = true;
}

$style_hide = "display:none;";
if (get_current_URL_name('print_ticket')) {
    $style_hide = "";
}

ob_start();

//Start output buffer
calendar_get_events_today();

$calendar_get_events_today = ob_get_contents();

//Grab output
ob_end_clean();

$basedir = dirname(dirname(__FILE__));

try {
    
    // указывае где хранятся шаблоны
    $loader = new Twig_Loader_Filesystem($basedir . '/views');
    
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
    $template = $twig->loadTemplate('navbar.view.tmpl');
    
    // передаём в шаблон переменные и значения
    // выводим сформированное содержание
    echo $template->render(array(
        'hostname' => $CONF['hostname'],
        'index_page' => $index_page,
        'img_logo_small' => get_logo_img('small') ,
        'name_of_firm' => $CONF['name_of_firm'],
        'EXT_toggle_nav' => lang('EXT_toggle_nav') ,
        'main_portal_active' => $mp,
        'PORTAL_title' => lang('PORTAL_title') ,
        'get_total_users_online' => get_total_users_online() ,
        'EXT_users_online' => lang('EXT_users_online') ,
        'ar_online' => $ar_online,
        'EXT_users_all_view' => lang('EXT_users_all_view') ,
        'ar_msg' => $ar_msg,
        'atm_v' => $atm_v,
        'title_m' => $titlem,
        'EXT_all_msgs' => lang('EXT_all_msgs') ,
        'cal_count' => calendar_get_count() ,
        'CALENDAR_NAVBAR_W' => lang('CALENDAR_NAVBAR_W') ,
        'calendar_get_events_today' => $calendar_get_events_today,
        'namemy' => nameshort(get_user_val('fio')) ,
        'usr_img' => get_user_img() ,
        'usr_fio' => get_user_val('fio') ,
        'usr_posada' => get_user_val('posada') ,
        'STATS_TITLE_short' => lang('STATS_TITLE_short') ,
        'NAVBAR_help' => lang('NAVBAR_help') ,
        'NAVBAR_profile' => lang('NAVBAR_profile') ,
        'NAVBAR_logout' => lang('NAVBAR_logout') ,
        'EXT_hello' => lang('EXT_hello') ,
        'usr_name' => get_user_name(get_user_val('fio')) ,
        'LIST_find_button' => lang('LIST_find_button') ,
        'LIST_find_ph' => lang('LIST_find_ph') ,
        'style_hide' => $style_hide,
        'val_admin' => $val_admin,
        'some_priv' => $some_priv,
        'DASHBOARD_TITLE' => lang('DASHBOARD_TITLE') ,
        'NAVBAR_create_ticket' => lang('NAVBAR_create_ticket') ,
        'NAVBAR_list_ticket' => lang('NAVBAR_list_ticket') ,
        'NAVBAR_news' => lang('NAVBAR_news') ,
        'CALENDAR_title' => lang('CALENDAR_title') ,
        'MESSAGES_navbar' => lang('MESSAGES_navbar') ,
        'USERS_list' => lang('USERS_list') ,
        'NAVBAR_helper' => lang('NAVBAR_helper') ,
        'NAVBAR_notes' => lang('NAVBAR_notes') ,
        'EXT_graph' => lang('EXT_graph') ,
        'ALLSTATS_main' => lang('ALLSTATS_main') ,
        'EXT_graph_user' => lang('EXT_graph_user') ,
        'SLA_rep' => lang('SLA_rep') ,
        'NAVBAR_admin' => lang('NAVBAR_admin') ,
        'NAVBAR_conf' => lang('NAVBAR_conf') ,
        'PORTAL_title' => lang('PORTAL_title') ,
        'NAVBAR_users' => lang('NAVBAR_users') ,
        'NAVBAR_mailers' => lang('NAVBAR_mailers') ,
        'NAVBAR_deps' => lang('NAVBAR_deps') ,
        'NAVBAR_units' => lang('NAVBAR_units') ,
        'NAVBAR_files' => lang('NAVBAR_files') ,
        'cron_navbar' => lang('cron_navbar') ,
        'NAVBAR_approve' => lang('NAVBAR_approve') ,
        'NAVBAR_posads' => lang('NAVBAR_posads') ,
        'apr' => $apr,
        'newtickets' => $newtickets,
        'cal_et' => $cal_et,
        'atm' => $atm,
        'tree_stat_class' => $tree_stat_class,
        'tree_admin_class' => $tree_admin_class,
        'cur_file_name' => cur_file_name()
    ));
}
catch(Exception $e) {
    die('ERROR: ' . $e->getMessage());
}
?>
