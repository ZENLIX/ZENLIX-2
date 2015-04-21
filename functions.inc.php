<?php
include_once ('conf.php');
include_once ('sys/class.phpmailer.php');
include_once ('sys/Parsedown.php');
require 'library/HTMLPurifier.auto.php';
include_once ('library/gump.class.php');

include_once('inc/main_portal/portal.functions.inc.php');
//include_once('integration/PushBullet.class.php');

$dbConnection = new PDO('mysql:host=' . $CONF_DB['host'] . ';dbname=' . $CONF_DB['db_name'], $CONF_DB['username'], $CONF_DB['password'], array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
));
$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function site_proto() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'] . '/';
    return $protocol;
}

$def_timezone = get_conf_param('time_zone');

date_default_timezone_set($def_timezone);
$date_tz = new DateTime();
$date_tz->setTimezone(new DateTimeZone($def_timezone));
$now_date_time = $date_tz->format('Y-m-d H:i:s');




$CONF = array(
    'title_header' => get_conf_param('title_header') ,
    'hostname' => site_proto() . get_conf_param('hostname') ,
    'mail' => get_conf_param('mail') ,
    'days2arch' => get_conf_param('days2arch') ,
    'name_of_firm' => get_conf_param('name_of_firm') ,
    'fix_subj' => get_conf_param('fix_subj') ,
    'first_login' => get_conf_param('first_login') ,
    'file_uploads' => get_conf_param('file_uploads') ,
    'file_types' => '(' . get_conf_param('file_types') . ')',
    'file_size' => get_conf_param('file_size') ,
    'update_server' => 'http://update.zenlix.com/',
    'timezone' => get_conf_param('time_zone') ,
    'now_dt' => $now_date_time,
    'main_portal'=>get_conf_param('portal_status'),
    'bf_pass'=> 300
);
$CONF_MAIL = array(
    'active' => get_conf_param('mail_active') ,
    'host' => get_conf_param('mail_host') ,
    'port' => get_conf_param('mail_port') ,
    'auth' => get_conf_param('mail_auth') ,
    'auth_type' => get_conf_param('mail_auth_type') ,
    'username' => get_conf_param('mail_username') ,
    'password' => get_conf_param('mail_password') ,
    'from' => get_conf_param('mail_from') ,
    'debug' => 'false'
);

if ($CONF_HD['debug_mode'] == false) {
    error_reporting(E_ALL ^ E_NOTICE);
    error_reporting(0);
}

function check_user_noty_layot($in) {
$layot=get_user_val_by_id($_SESSION['helpdesk_user_id'], 'noty_layot');
if ($in == $layot) { echo "selected";}
}

function get_noty_layot() {

//$layot=get_user_val_by_id($_SESSION['helpdesk_user_id'], 'noti_layot');



}




function generate_timezone_list()
{
    static $regions = array(
        DateTimeZone::AFRICA,
        DateTimeZone::AMERICA,
        DateTimeZone::ANTARCTICA,
        DateTimeZone::ASIA,
        DateTimeZone::ATLANTIC,
        DateTimeZone::AUSTRALIA,
        DateTimeZone::EUROPE,
        DateTimeZone::INDIAN,
        DateTimeZone::PACIFIC,
    );

    $timezones = array();
    foreach( $regions as $region )
    {
        $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
    }

    $timezone_offsets = array();
    foreach( $timezones as $timezone )
    {
        $tz = new DateTimeZone($timezone);
        $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
    }

    // sort timezone by offset
    asort($timezone_offsets);

    $timezone_list = array();
    foreach( $timezone_offsets as $timezone => $offset )
    {
        $offset_prefix = $offset < 0 ? '-' : '+';
        $offset_formatted = gmdate( 'H:i', abs($offset) );

        $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

        $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
    }

    return $timezone_list;
}

function send_mail_reg($to, $subj, $msg) {
    global $CONF, $CONF_MAIL, $dbConnection;
    
    //echo "helo";
    if (get_conf_param('mail_type') == "sendmail") {
        
        $mail = new PHPMailer();
        
        //$mail->SMTPDebug = 1;
        $mail->CharSet = 'UTF-8';
        $mail->IsSendmail();
        
        $mail->AddReplyTo($CONF_MAIL['from'], $CONF['name_of_firm']);
        $mail->AddAddress($to, $to);
        $mail->SetFrom($CONF_MAIL['from'], $CONF['name_of_firm']);
        $mail->Subject = $subj;
        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        $mail->MsgHTML($msg);
        $mail->Send();
    } else if (get_conf_param('mail_type') == "SMTP") {
        
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->IsSMTP();
        $mail->SMTPAuth = $CONF_MAIL['auth'];
        
        // enable SMTP authentication
        if (get_conf_param('mail_auth_type') != "none") {
            $mail->SMTPSecure = $CONF_MAIL['auth_type'];
        }
        $mail->Host = $CONF_MAIL['host'];
        $mail->Port = $CONF_MAIL['port'];
        $mail->Username = $CONF_MAIL['username'];
        $mail->Password = $CONF_MAIL['password'];
        
        $mail->AddReplyTo($CONF_MAIL['from'], $CONF['name_of_firm']);
        $mail->AddAddress($to, $to);
        $mail->SetFrom($CONF_MAIL['from'], $CONF['name_of_firm']);
        $mail->Subject = $subj;
        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        
        // optional - MsgHTML will create an alternate automatically
        $mail->MsgHTML($msg);
        $mail->Send();
    }
}

include_once ('inc/notification.inc.php');

$forhostname = substr($CONF['hostname'], -1);
if ($forhostname == "/") {
    $CONF['hostname'] = $CONF['hostname'];
} else if ($forhostname <> "/") {
    $CONF['hostname'] = $CONF['hostname'] . "/";
}

function get_user_lang() {
    global $dbConnection;
    
    $mid = $_SESSION['helpdesk_user_id'];
    $stmt = $dbConnection->prepare('SELECT lang from users where id=:mid');
    $stmt->execute(array(
        ':mid' => $mid
    ));
    $max = $stmt->fetch(PDO::FETCH_NUM);
    
    $max_id = $max[0];
    $length = strlen(utf8_decode($max_id));
    if (($length < 1) || $max_id == "0") {
        $ress = get_conf_param('lang_def');
    } else {
        $ress = $max_id;
    }
    return $ress;
}

$lang = get_user_lang();

/*
switch ($lang) {
    case 'ua':
        $lang_file = 'lang.ua.php';
        break;

    case 'ru':
        $lang_file = 'lang.ru.php';
        break;

    case 'en':
        $lang_file = 'lang.en.php';
        break;

    default:
        $lang_file = 'lang.en.php';
}

include_once 'lang/' . $lang_file;
*/
include_once 'lang/' . 'lang.ua.php';
include_once 'lang/' . 'lang.ru.php';
include_once 'lang/' . 'lang.en.php';

function lang($in) {
    $lang = get_user_lang();
    
    switch ($lang) {
        case 'ua':
            $res = lang_ua($in);
            break;

        case 'ru':
            $res = lang_ru($in);
            break;

        case 'en':
            $res = lang_en($in);
            break;

        default:

            $res = lang_en($in);
    }
    
    return $res;
}

function get_ticket_sla_status_nook($ticket_id) {


 global $dbConnection;

    $stmt = $dbConnection->prepare('select * from tickets where id=:ticket_id');
    $stmt->execute(array(
        ':ticket_id' => $ticket_id
    ));
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ticket['sla_plan_id'] == 0) {
    $r = array(
    'status'=>'false'
    );
}
    else if ($ticket['sla_plan_id'] != 0) {

           $stmt_sla = $dbConnection->prepare('SELECT * from sla_plans where id=:uid');
    $stmt_sla->execute(array(':uid' => $ticket['sla_plan_id']));
    $row_sla = $stmt_sla->fetch(PDO::FETCH_ASSOC);
$pr=$ticket['prio'];
if ($pr == "0") {
$sla_react=$row_sla['reaction_time_low_prio'];
$sla_work=$row_sla['work_time_low_prio'];
$sla_deadline=$row_sla['deadline_time_low_prio'];
}
else if ($pr == "1") {
$sla_react=$row_sla['reaction_time_def'];
$sla_work=$row_sla['work_time_def'];
$sla_deadline=$row_sla['deadline_time_def'];
}

else if ($pr == "2") {
$sla_react=$row_sla['reaction_time_high_prio'];
$sla_work=$row_sla['work_time_high_prio'];
$sla_deadline=$row_sla['deadline_time_high_prio'];
}


$dcr=$ticket['date_create'];


if (get_ticket_time_reaction_sec($ticket_id) == 0) {
$per=floor((get_ticket_time_reaction_sec_no_lock($ticket_id)*100)/$sla_react);
}
else if (get_ticket_time_reaction_sec($ticket_id) != 0) {
$per=floor((get_ticket_time_reaction_sec($ticket_id)*100)/$sla_react);
}
if ($per > 100) { $per=100;}
$per=100-$per;





if (get_ticket_time_lock_sec($ticket_id) == 0) {
$perw=0;
}
else if (get_ticket_time_lock_sec($ticket_id) != 0) {
    $perw=floor((get_ticket_time_lock_sec($ticket_id)*100)/$sla_work);
}
if ($perw > 100) { $perw=100;}
$perw=100-$perw;




    $stmt_dl = $dbConnection->prepare('SELECT date_op from ticket_log where ticket_id=:tid and msg=:m order by date_op DESC limit 1');
    $stmt_dl->execute(array(
        ':tid' => $ticket_id,
        ':m'=>'ok'
    ));
    $tts_dl = $stmt_dl->fetch(PDO::FETCH_ASSOC);


$perd=floor(((time()-strtotime($dcr))*100)/$sla_deadline);
if ($perd > 100) { $perd=100;}
$perd=100-$perd;











$r = array(
    'react'=> $per,
    'work'=> $perw,
    'dl'=> $perd,
    'status'=>'true'
    );


    }

    return $r;


}

function get_ticket_sla_status($ticket_id) {
        global $dbConnection;

    $stmt = $dbConnection->prepare('select * from tickets where id=:ticket_id');
    $stmt->execute(array(
        ':ticket_id' => $ticket_id
    ));
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ticket['sla_plan_id'] == 0) {
    $r = array(
    'status'=>'false'
    );
}
    else if ($ticket['sla_plan_id'] != 0) {

           $stmt_sla = $dbConnection->prepare('SELECT * from sla_plans where id=:uid');
    $stmt_sla->execute(array(':uid' => $ticket['sla_plan_id']));
    $row_sla = $stmt_sla->fetch(PDO::FETCH_ASSOC);
$pr=$ticket['prio'];
if ($pr == "0") {
$sla_react=$row_sla['reaction_time_low_prio'];
$sla_work=$row_sla['work_time_low_prio'];
$sla_deadline=$row_sla['deadline_time_low_prio'];
}
else if ($pr == "1") {
$sla_react=$row_sla['reaction_time_def'];
$sla_work=$row_sla['work_time_def'];
$sla_deadline=$row_sla['deadline_time_def'];
}

else if ($pr == "2") {
$sla_react=$row_sla['reaction_time_high_prio'];
$sla_work=$row_sla['work_time_high_prio'];
$sla_deadline=$row_sla['deadline_time_high_prio'];
}


$dcr=$ticket['date_create'];



$per=floor((get_ticket_time_reaction_sec($ticket_id)*100)/$sla_react);
if ($per > 100) { $per=100;}
$per=100-$per;







$perw=floor((get_ticket_time_lock_sec($ticket_id)*100)/$sla_work);
if ($perw > 100) { $perw=100;}
$perw=100-$perw;




    $stmt_dl = $dbConnection->prepare('SELECT date_op from ticket_log where ticket_id=:tid and msg=:m order by date_op DESC limit 1');
    $stmt_dl->execute(array(
        ':tid' => $ticket_id,
        ':m'=>'ok'
    ));
    $tts_dl = $stmt_dl->fetch(PDO::FETCH_ASSOC);




$perd=floor(((strtotime($tts_dl['date_op'])-strtotime($dcr))*100)/$sla_deadline);
if ($perd > 100) { $perd=100;}
$perd=100-$perd;











$r = array(
    'react'=> $per,
    'work'=> $perw,
    'dl'=> $perd,
    'status'=>'true'
    );


    }

    return $r;

}


function get_last_action_ticket($ticket_id) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('select date_op, msg, init_user_id, to_user_id, to_unit_id from ticket_log where ticket_id=:ticket_id order by date_op DESC limit 1');
    $stmt->execute(array(
        ':ticket_id' => $ticket_id
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    $r = $fio['msg'];
    $uss = nameshort(name_of_user_ret($fio['init_user_id']));
    $uss_to = nameshort(name_of_user_ret($fio['to_user_id']));
    $unit_to = get_unit_name_return4news($fio['to_unit_id']);
    if ($r == 'refer') {
        $red = '<i class=\'fa fa-long-arrow-right\'></i> ' . lang('TICKET_ACTION_refer') . ' <em>' . $uss . '</em> ' . lang('TICKET_ACTION_refer_to') . ' ' . $unit_to . ' ' . $uss_to;
    }
    if ($r == 'ok') {
        $red = '<i class=\'fa fa-check-circle-o\'></i> ' . lang('TICKET_ACTION_ok') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'no_ok') {
        $red = '<i class=\'fa fa-circle-o\'></i> ' . lang('TICKET_ACTION_nook') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'lock') {
        $red = '<i class=\'fa fa-lock\'></i> ' . lang('TICKET_ACTION_lock') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'unlock') {
        $red = '<i class=\'fa fa-unlock\'></i> ' . lang('TICKET_ACTION_unlock') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'create') {
        $red = '<i class=\'fa fa-star-o\'></i> ' . lang('TICKET_ACTION_create') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'edit_msg') {
        $red = '<i class=\'fa fa-pencil-square\'></i> ' . lang('TICKET_ACTION_edit') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'edit_prio') {
        $red = '<i class=\'fa fa-pencil-square\'></i> ' . lang('TICKET_ACTION_edit') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'edit_subj') {
        $red = '<i class=\'fa fa-pencil-square\'></i> ' . lang('TICKET_ACTION_edit') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'comment') {
        $red = '<i class=\'fa fa-comment\'></i> ' . lang('TICKET_ACTION_comment') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'arch') {
        $red = '<i class=\'fa fa-archive\'></i> ' . lang('TICKET_ACTION_arch') . '';
    }
    return $red;
}

function get_conf_param($in) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT value FROM perf where param=:in');
    $stmt->execute(array(
        ':in' => $in
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return htmlspecialchars($fio['value']);
}

//$fio_user=$fio['fio'];

function generateRandomString($length = 5) {
    $characters = '0123456789';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString.= $characters[rand(0, strlen($characters) - 1) ];
    }
    
    return $randomString;
}

function generatepassword($length = 8) {
    $characters = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString.= $characters[rand(0, strlen($characters) - 1) ];
    }
    
    return $randomString;
}

function get_deadline_label($in) {
global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT status as n, deadline_time from tickets where id = :id');
    $stmt->execute(array(':id' => $in));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $status=$row['n'];
    $date_finish=$row['deadline_time'];
     
if ($date_finish) {


$birthday =strtotime($date_finish);
$now = strtotime(date("Y-m-d H:i:s"));
$datediff =  $birthday - $now;
$hoursRemaining = floor($datediff);

$h=60*60*24;
//дата наступила
if ($hoursRemaining <= 0) { 
    $r="<br><span class=\"label label-danger\">".lang('TICKET_deadline_old')."</span>"; 
    }

//осталось меньше суток
else if (($hoursRemaining >0) && ($hoursRemaining <= $h ))  { 
    $r="<br><span class=\"label label-warning\">".lang('TICKET_deadline_time')." <time id=\"b\" datetime=\"".$date_finish."\"></time></span>";
    }

//показать дату
else if ($hoursRemaining > $h) { 
// You can change to id=\"a\" or id=\"c\" for different date format
$r="<br><span class=\"label label-primary\">".lang('TICKET_deadline_time')." <time id=\"b\" datetime=\"".$date_finish."\"></time></span>"; 
    }


    if ($status == "1") { 
        $r="<br><span class=\"label label-success\">".lang('TICKET_deadline_success')."</span>";
    }
    else if ($status == "0") { 
        //do nothing
    }
}



if (get_conf_param('ticket_last_time') == "true") {
    return $r;
}

}


function get_current_URL_name($requestUri) {
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
    $file = $_SERVER['REQUEST_URI'];
    $file = explode("?", basename($file));
    $current_file_name = $file[0];
    
    //$file = $_SERVER['REQUEST_URI'];
    //$file = explode("?", basename($file));
    
    if ($current_file_name == $requestUri) {
        return true;
    } else {
        return false;
    }
}

function validate_exist_login($str) {
    global $dbConnection;
    $uid = $_SESSION['helpdesk_user_id'];
    
    $stmt = $dbConnection->prepare('SELECT count(login) as n from users where login=:str and status!=2 ');
    $stmt->execute(array(
        ':str' => $str
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['n'] > 0) {
        $r = false;
    } else if ($row['n'] == 0) {
        $r = true;
    }
    
    return $r;
}

function validate_exist_login_ex($str, $ex) {
    global $dbConnection;
    $uid = $_SESSION['helpdesk_user_id'];
    
    $stmt = $dbConnection->prepare('SELECT count(login) as n from users where login=:str and login!=:ex and status!=2 ');
    $stmt->execute(array(
        ':str' => $str,
        ':ex' => $ex
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['n'] > 0) {
        $r = false;
    } else if ($row['n'] == 0) {
        $r = true;
    }
    
    return $r;
}

function validate_exist_mail_not_auth($str) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT count(email) as n from users where email=:str and status!=2');
    $stmt->execute(array(
        ':str' => $str
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['n'] > 0) {
        $r = false;
    } else if ($row['n'] == 0) {
        $r = true;
    }
    
    return $r;
}

function validate_exist_mail($str) {
    global $dbConnection;
    $uid = $_SESSION['helpdesk_user_id'];
    
    $stmt = $dbConnection->prepare('SELECT count(email) as n from users where email=:str and id != :uid and status!=2 ');
    $stmt->execute(array(
        ':str' => $str,
        ':uid' => $uid
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['n'] > 0) {
        $r = false;
    } else if ($row['n'] == 0) {
        $r = true;
    }
    
    return $r;
}

function validate_email($str) {
    return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $str);
}

function validate_alphanumeric_underscore($str) {
    return preg_match('/^[a-zA-Z0-9_\.-]+$/', $str);
}

function update_val_by_key($key, $val) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('update perf set value=:value where param=:param');
    $stmt->execute(array(
        ':value' => $val,
        ':param' => $key
    ));
    
    return true;
}





function calendar_get_events_today() {
    global $dbConnection;


if (calendar_get_count() == 0) {
?>

no

<?php
}
if (calendar_get_count() != 0) {
    


$res_main=array();

    $stmt = $dbConnection->prepare('SELECT * from calendar where visibility=0 and user_id=:uid and DATE(dtStart) = DATE(NOW())');
    $stmt->execute(array(':uid'=>$_SESSION['helpdesk_user_id']));
    $res_1 = $stmt->fetchAll();
    $res_main=array_merge($res_main, $res_1);

    
     $stmt2 = $dbConnection->prepare('SELECT * from calendar where visibility=1 and DATE(dtStart) = DATE(NOW())');
     $stmt2->execute();
     $res_2 = $stmt2->fetchAll();
foreach ($res_2 as $key => $value) {
    # code...
    if (!check_admin_user_priv($value['user_id'])) {
        unset($res_2[$key]);
    }
}
$res_main=array_merge($res_main, $res_2);







    $stmt3 = $dbConnection->prepare('SELECT * from calendar where visibility=2 and DATE(dtStart) = DATE(NOW())');
    $stmt3->execute();
    $res_3 = $stmt3->fetchAll();
    $res_main=array_merge($res_main, $res_3);

usort($res_main, function($a1, $a2) {
   $v1 = strtotime($a1['dtStart']);
   $v2 = strtotime($a2['dtStart']);
   return $v1 - $v2; // $v2 - $v1 to reverse direction
});


$allday=lang('CALENDAR_allday');


foreach ($res_main as $value) {
    $allday=lang('CALENDAR_allday');

if ($value['allday'] == 'false') {
    $allday="<time id=\"e\" datetime=\"".$value['dtStart']."\"></time>";
}

$de="";
if ($value['description'] != "0") {
    $de=$value['description'];
}

    # code...?>
    <li><!-- start message -->
                        <a href="calendar">
                          <h4 style="margin:0px; white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;">
                           <i style="font-size:10px; color:<?=$value['backgroundColor'];?>" class="fa fa-circle"></i> <?=cutstr_api($value['title'], 50);?>
                            <small><i class="fa fa-clock-o"></i> <?=$allday;?></small>
                          </h4>
                          <p style="margin:0px; white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;"><?=$de;?></p>
                        </a>
                      </li>
    <?php
}
}

}



function calendar_get_count() {
        global $dbConnection;

$res_main=array();

    $stmt = $dbConnection->prepare('SELECT * from calendar where visibility=0 and user_id=:uid and DATE(dtStart) = DATE(NOW())');
    $stmt->execute(array(':uid'=>$_SESSION['helpdesk_user_id']));
    $res_1 = $stmt->fetchAll();
    $res_main=array_merge($res_main, $res_1);

    
     $stmt2 = $dbConnection->prepare('SELECT * from calendar where visibility=1 and DATE(dtStart) = DATE(NOW())');
     $stmt2->execute();
     $res_2 = $stmt2->fetchAll();
foreach ($res_2 as $key => $value) {
    # code...
    if (!check_admin_user_priv($value['user_id'])) {
        unset($res_2[$key]);
    }
}
$res_main=array_merge($res_main, $res_2);







    $stmt3 = $dbConnection->prepare('SELECT * from calendar where visibility=2 and DATE(dtStart) = DATE(NOW())');
    $stmt3->execute();
    $res_3 = $stmt3->fetchAll();
    $res_main=array_merge($res_main, $res_3);





return count($res_main);
    //$res_main=array_merge($res_main, $res_1);


}


function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyz0123456789";
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 5; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}

function check_admin_user_priv($in) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT id, unit from users where id=:in and status!=2 ');
    $stmt->execute(array(
        ':in' => $in
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $users_units = $row['unit'];
    $admin_units = get_user_val_by_id($_SESSION['helpdesk_user_id'], 'unit');
    
    $users_units = explode(',', $users_units);
    $admin_units = explode(',', $admin_units);
    $result = array_intersect($users_units, $admin_units);
    if ($result) {
        return true;
    } else if (!$result) {
        return false;
    }
}

function randomhash() {
    $alphabet = "abcdefghijklmnopqrstuwxyz0123456789";
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 24; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}

function nameshort($name) {
    $nameshort = preg_replace('/(\w+) (\w)\w+ (\w)\w+/iu', '$1 $2.$3.', $name);
    return $nameshort;
}

function xss_clean($data) {
    
    $data = str_replace(array(
        '&amp;',
        '&lt;',
        '&gt;'
    ) , array(
        '&amp;amp;',
        '&amp;lt;',
        '&amp;gt;'
    ) , $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
    
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
    
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
    
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
    
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
    
    do {
        
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    } while ($old_data !== $data);
    
    return $data;
}

function get_file_icon($in) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT file_type FROM files where file_hash=:file_hash');
    $stmt->execute(array(
        ':file_hash' => $in
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $ftype = $row['file_type'];
    
    switch ($ftype) {
        case 'application/pdf':
            $icon = "<i class=\"fa fa-file-pdf-o\"></i>";
            break;

        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            $icon = "<i class=\"fa fa-file-word-o\"></i>";
            break;

        case 'application/msword':
            $icon = "<i class=\"fa fa-file-word-o\"></i> ";
            break;

        case 'application/excel':
            $icon = "<i class=\"fa fa-file-excel-o\"></i>";
            break;

        case 'application/vnd.ms-excel':
            $icon = "<i class=\"fa fa-file-excel-o\"></i>";
            break;

        case 'application/x-excel':
            $icon = "<i class=\"fa fa-file-excel-o\"></i>";
            break;

        case 'application/x-msexcel':
            $icon = "<i class=\"fa fa-file-excel-o\"></i>";
            break;

        case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            $icon = "<i class=\"fa fa-file-word-o\"></i>";
            break;

        case 'image/jpeg':
            $icon = "<i class=\"fa fa-file-image-o\"></i>";
            break;

        case 'image/jpg':
            $icon = "<i class=\"fa fa-file-image-o\"></i>";
            break;

        case 'image/gif':
            $icon = "<i class=\"fa fa-file-image-o\"></i>";
            break;

        case 'image/png':
            $icon = "<i class=\"fa fa-file-image-o\"></i>";
            break;

        default:
            $icon = "<i class=\"fa fa-file\"></i>";
    }
    
    return $icon;
}

function validate_admin($user_id) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT is_admin from users where id=:user_id and status!=2 LIMIT 1');
    $stmt->execute(array(
        ':user_id' => $user_id
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $admin = $row['is_admin'];
    
    if ($admin == "8") {
        return true;
    } else {
        return false;
    }
}

function get_user_authtype($login) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT ldap_key from users where login=:user_login and status!=2 LIMIT 1');
    $stmt->execute(array(
        ':user_login' => $login
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $lkey = $row['ldap_key'];
    if ($lkey == "1") {
        $res = true;
    } else $res = false;
    return $res;
}

function ldap_auth($login, $pass) {
    $ldaprdn = $login . '@' . get_conf_param('ldap_domain');
    
    // ldap rdn or dn
    $ldappass = $pass;
    
    // associated password
    
    // connect to ldap server
    $ldapconn = ldap_connect(get_conf_param('ldap_ip')) or die("Could not connect to LDAP server.");
    
    if ($ldapconn) {
        
        // binding to ldap server
        $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
        
        // verify binding
        if ($ldapbind) {
            $res = true;
        } else {
            $res = false;
        }
    }
    
    return $res;
}

function get_posada_by_id($id) {
    global $dbConnection;
    if ($id) {
        $stmt = $dbConnection->prepare('SELECT name from posada where id=:user_id LIMIT 1');
        $stmt->execute(array(
            ':user_id' => $id
        ));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $admin = $row['name'];
    } else {
        $admin = '';
    }
    return $admin;
}

function sel_ldap_attr($param) {

?>




<select data-placeholder="" class="input-sm form-control" id="<?=$param;?>">
                                                <option value="empty"> <?=lang('LDAP_IMPORT_empty');?>                 </option>
                                                <option value="name"> name                 </option>
                                                <option value="mail"> mail                 </option>
                                                <option value="mobile"> mobile               </option>
                                                <option value="skype"> skype                </option>
                                                <option value="telephone"> telephone            </option>
                                                <option value="department"> department           </option>
                                                <option value="title"> title                </option>
                                                <option value="userprincipalname"> userprincipalname    </option>
                                                <option value="samaccountname"> samaccountname       </option>
                                                <option value="othertelephone"> othertelephone       </option>
</select>


<?php
}

function view_log($tid) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT msg,
                            date_op, init_user_id, to_user_id, to_unit_id from ticket_log where
                            ticket_id=:tid order by date_op DESC');
    $stmt->execute(array(
        ':tid' => $tid
    ));
    $re = $stmt->fetchAll();
    
    if (!empty($re)) {
?>


<div class="box box-solid">
<div class="box-body">















                                    <div class="panel-body" style="max-height: 400px; scroll-behavior: initial; overflow-y: scroll;">

                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th><center><small><?php
        echo lang('TICKET_t_date'); ?></small></center>    </th>
                                                <th><center><small><?php
        echo lang('TICKET_t_init'); ?> </small></center></th>
                                                <th><center><small><?php
        echo lang('TICKET_t_action'); ?>   </small></center></th>
                                                <th><center><small><?php
        echo lang('TICKET_t_desc'); ?> </small></center></th>


                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php
        foreach ($re as $row) {
            
            $t_action = $row['msg'];
            
            if ($t_action == 'refer') {
                $icon_action = "fa fa-long-arrow-right";
                $text_action = "" . lang('TICKET_t_a_refer') . " <br>" . view_array(get_unit_name_return($row['to_unit_id'])) . "<br>" . name_of_user_ret($row['to_user_id']);
            }
            if ($t_action == 'arch') {
                $icon_action = "fa fa-archive";
                $text_action = lang('TICKET_t_a_arch');
            }
            
            if ($t_action == 'ok') {
                $icon_action = "fa fa-check-circle-o";
                $text_action = lang('TICKET_t_a_ok');
            }
            if ($t_action == 'no_ok') {
                $icon_action = "fa fa-circle-o";
                $text_action = lang('TICKET_t_a_nook');
            }
            if ($t_action == 'lock') {
                $icon_action = "fa fa-lock";
                $text_action = lang('TICKET_t_a_lock');
            }
            if ($t_action == 'unlock') {
                $icon_action = "fa fa-unlock";
                $text_action = lang('TICKET_t_a_unlock');
            }
            if ($t_action == 'create') {
                $icon_action = "fa fa-star-o";
                $text_action = lang('TICKET_t_a_create');
            }
            if ($t_action == 'edit_prio') {
                $icon_action = "fa fa-pencil-square";
                $text_action = lang('TICKET_t_a_e_prio');
            }
            if ($t_action == 'edit_msg') {
                $icon_action = "fa fa-pencil-square";
                $text_action = lang('TICKET_t_a_e_text');
            }
            if ($t_action == 'edit_subj') {
                $icon_action = "fa fa-pencil-square";
                $text_action = lang('TICKET_t_a_e_subj');
            }
            if ($t_action == 'comment') {
                $icon_action = "fa fa-comment";
                $text_action = lang('TICKET_t_a_com');
            }
            
            $ru = name_of_user_ret($row['init_user_id']);
?>
                                                <tr>
                                                    <td style="width: 100px; vertical-align: inherit;"><small><center>
                                                    
                                                    <time id="c" datetime="<?php
            echo $row['date_op'] ?>"></time>
                                                    
                                                    </center></small></td>
                                                    <td style=" width: 200px; vertical-align: inherit;"><small><center>
                                                    <a href="view_user?<?php
            echo get_user_hash_by_id($row['init_user_id']); ?>"><?php
            echo $ru
?>
                                                    </a>
                                                    </center></small></td>
                                                    <td style=" width: 50px; vertical-align: inherit;"><small><center><i class="<?php
            echo $icon_action; ?>"></i>  </center></small></td>
                                                    <td style=" width: 200px; vertical-align: inherit;"><small><?php
            echo $text_action
?></small></td>


                                                </tr>
                                            <?php
        } ?>
                                            </tbody>
                                        </table>
                                    </div>
</div>
</div>



                        <?php
    }
}

function make_html($in, $type) {
    
    $Parsedown = new Parsedown();
    $text = $Parsedown->text($in);
    $text = str_replace("\n", "<br />", $text);
    $config = HTMLPurifier_Config::createDefault();
    
    $config->set('Core.Encoding', 'UTF-8');
    $config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
    $config->set('Cache.DefinitionImpl', null);
    $config->set('AutoFormat.RemoveEmpty', false);
    $config->set('AutoFormat.AutoParagraph', true);
    
    //$config->set('URI.DisableExternal', true);
    if ($type == "no") {
        $config->set('HTML.ForbiddenElements', array(
            'p'
        ));
    }
    
    $purifier = new HTMLPurifier($config);
    $def = $config->getHTMLDefinition(true);
    $def->addElement('ul', 'List', 'Optional: List | li', 'Common', array());
    $def->addElement('ol', 'List', 'Optional: List | li', 'Common', array());
    
    // here, the javascript command is stripped off
    $content = $purifier->purify($text);
    
    return $content;
}






function get_current_sort_p($val) {

$pre="<span type=\"button\" class=\"label label-success\"><a href=\"#\" id=\"reset_sort\"><span aria-hidden=\"true\">&times;</span></a> ".lang('SORT_BY');
$r="";
    if ($val == "in") {
if (!isset($_SESSION['hd.rustem_sort_in'])) {
        if (isset($_SESSION['zenlix_list_in_sort'])) {
            switch ($_SESSION['zenlix_list_in_sort']) {
                    case 'id':
                    $r=$pre."<mark>ID</mark>";
                    break;
                    case 'prio':
                    $r=$pre."<mark>".lang('t_LIST_prio')."</mark>";
                    break;
                    case 'subj':
                    $r=$pre."<mark>".lang('t_LIST_subj')."</mark>";
                    break;
                    case 'client_id':
                    $r=$pre."<mark>".lang('t_LIST_worker')."</mark>";
                    break;
                    case 'date_create':
                    $r=$pre."<mark>".lang('t_LIST_create')."</mark>";
                    break;
                    case 'user_init_id':
                    $r=$pre."<mark>".lang('t_LIST_init')."</mark>";
                    break;
            }
        }
         if (isset($_SESSION['zenlix_list_in_sort_var'])) {

            if ($_SESSION['zenlix_list_in_sort_var'] == "asc") { $r=$r." <small>".lang('SORT_BY_asc')."</small>";}
            if ($_SESSION['zenlix_list_in_sort_var'] == "desc") { $r=$r." <small>".lang('SORT_BY_desc')."</small>";}
         }
    }
}
     if ($val == "out") {
if (!isset($_SESSION['hd.rustem_sort_out'])) {
        if (isset($_SESSION['zenlix_list_out_sort'])) {
            switch ($_SESSION['zenlix_list_out_sort']) {
                    case 'id':
                    $r=$pre."<mark>ID</mark>";
                    break;
                    case 'prio':
                    $r=$pre."<mark>".lang('t_LIST_prio')."</mark>";
                    break;
                    case 'subj':
                    $r=$pre."<mark>".lang('t_LIST_subj')."</mark>";
                    break;
                    case 'client_id':
                    $r=$pre."<mark>".lang('t_LIST_worker')."</mark>";
                    break;
                    case 'date_create':
                    $r=$pre."<mark>".lang('t_LIST_create')."</mark>";
                    break;
                    case 'user_init_id':
                    $r=$pre."<mark>".lang('t_LIST_init')."</mark>";
                    break;
            }
        }
         if (isset($_SESSION['zenlix_list_out_sort_var'])) {

            if ($_SESSION['zenlix_list_out_sort_var'] == "asc") { $r=$r." <small>".lang('SORT_BY_asc')."</small>";}
            if ($_SESSION['zenlix_list_out_sort_var'] == "desc") { $r=$r." <small>".lang('SORT_BY_desc')."</small>";}
         }
    }
}

return $r;
}


function get_current_sort($val) {


if ($val == "in") {

            if (isset($_SESSION['hd.rustem_sort_in'])) {
            
            switch ($_SESSION['hd.rustem_sort_in']) {
                case 'ok':
                    $r=lang('CONF_EMAIL_GATE_filter').": "."<span class=\"label label-success\">".lang('ticket_sort_ok')."</span>";
                    break;

                case 'free':
                    $r=lang('CONF_EMAIL_GATE_filter').": "."<span class=\"label label-info\">".lang('STATS_t_free')."</span>";
                    break;

                case 'ilock':
                    $r=lang('CONF_EMAIL_GATE_filter').": "."<span class=\"label label-warning\">".lang('ticket_sort_ilock')."</span>";
                    break;

                case 'lock':
                    $r=lang('CONF_EMAIL_GATE_filter').": "."<span class=\"label label-default\">".lang('ticket_sort_lock')."</span>";
                    break;

                default:
                    $r="";
            }
        }
        else {
            $r="";
        }

}


else if ($val == "out") {
                if (isset($_SESSION['hd.rustem_sort_out'])) {
            
            switch ($_SESSION['hd.rustem_sort_out']) {
                case 'ok':
                    $r=lang('CONF_EMAIL_GATE_filter').": "."<span class=\"label label-success\">".lang('ticket_sort_ok')."</span>";
                    break;

                case 'free':
                    $r=lang('CONF_EMAIL_GATE_filter').": "."<span class=\"label label-info\">".lang('STATS_t_free')."</span>";
                    break;

                case 'ilock':
                    $r=lang('CONF_EMAIL_GATE_filter').": "."<span class=\"label label-warning\">".lang('ticket_sort_ilock')."</span>";
                    break;

                case 'lock':
                    $r=lang('CONF_EMAIL_GATE_filter').": "."<span class=\"label label-default\">".lang('ticket_sort_lock')."</span>";
                    break;

                default:
                    $r="";
            }
        }
        else {
            $r="";
        }
}

return $r;
}




function view_messages($in) {
    global $dbConnection;
    
    if ($in == "main") {
        
        $stmt = $dbConnection->prepare('SELECT id, user_from,user_to,date_op,msg,type_msg,is_read
FROM
(
     SELECT *
     FROM messages
     ORDER BY id DESC
     LIMIT 20 
) messages
         where
        (type_msg=:tm)
         ORDER BY id');
        $stmt->execute(array(
            ':tm' => 'main'
        ));
        $stmt2 = $dbConnection->prepare('UPDATE messages set is_read=1 where type_msg=:tm');
        $stmt2->execute(array(
            ':tm' => 'main'
        ));
    } else if ($in != "main") {
        
        $stmt = $dbConnection->prepare('SELECT id, user_from,user_to,date_op,msg,type_msg,is_read
FROM
(
     SELECT *
     FROM messages
     ORDER BY id DESC
     LIMIT 20 
) messages
         where
        (user_from=:ufrom and user_to=:uto) or (user_from=:uto2 and user_to=:ufrom2)
         ORDER BY id');
        $stmt->execute(array(
            ':ufrom' => $in,
            ':uto' => $_SESSION['helpdesk_user_id'],
            ':ufrom2' => $in,
            ':uto2' => $_SESSION['helpdesk_user_id']
        ));
        
        $stmt2 = $dbConnection->prepare('UPDATE messages set is_read=1 where user_from=:user_from and user_to=:user_to');
        $stmt2->execute(array(
            ':user_from' => $in,
            ':user_to' => $_SESSION['helpdesk_user_id']
        ));
    }
    
    while ($rews = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        $ru = nameshort(name_of_user_ret($rews['user_from']));
        
        if ($rews['is_read'] == 0) {
            $s = "background-color: #FAFAFA;";
        } else {
            $s = "";
        }
        
        $ct = make_html($rews['msg'], true);
?>

                                    
                                    
                                    
                                    
                            <div class="direct-chat-msg">
                      <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name pull-left"><a href="view_user?<?php
        echo get_user_hash_by_id($rews['user_from']); ?>" class="name">
                                                
                                                <?php
        echo $ru; ?>
                                            </a></span>
                        <span class="direct-chat-timestamp pull-right"><small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 
                                                <time id="b" datetime="<?php
        echo $rews['date_op']; ?>"></time> <time id="c" datetime="<?php
        echo $rews['date_op']; ?>"></time>
                                                </small></span>
                      </div><!-- /.direct-chat-info -->
                      <img class="direct-chat-img <?php
        echo get_user_status_text($rews['user_from']); ?>"  src="<?php
        echo get_user_img_by_id($rews['user_from']); ?>" alt=""><!-- /.direct-chat-img -->
                      <div class="direct-chat-text">
                     <small>    <?=$ct; ?></small>
                      </div><!-- /.direct-chat-text -->
                    </div><!-- /.direct-chat-msg -->
        
        
        

        
        
        
        

                            

        <?php
    }
    
    //echo "ok";
    
    
}


function get_ticket_time_lock_sec($t_id) {
    global $dbConnection;

$stmt = $dbConnection->prepare("SELECT c.ticket_id, c.init_user_id, Min(c.StartTime), Max(c.EndTime), Sum(c.Seconds) as sum_sec
 FROM (
  SELECT   a.ticket_id,
           a.Id aStateId, a.msg aName,
           b.Id bStateId, b.msg bname,
           a.init_user_id,
           a.date_op as StartTime, min(b.date_op) as EndTime,
           timestampdiff(Second, a.date_op, b.date_op) as Seconds
  FROM ticket_log as a inner join ticket_log as b
    ON ( ((a.msg <=> :lock AND b.msg <=> :ok) OR 
          (a.msg <=> :lock2 AND b.msg <=> :unlock) OR
          (a.msg <=> :no_ok AND b.msg <=> :unlock2) OR
          (a.msg <=> :no_ok2 AND b.msg <=> :ok2))
          AND a.date_op < b.date_op
          -- AND a.init_user_id = b.init_user_id
          AND a.ticket_id = b.ticket_id
          AND a.ticket_id=:tid
       ) 
  GROUP BY a.ticket_id, a.msg, a.date_op
  ) as c
  GROUP BY c.ticket_id");



    $stmt->execute(array(
        ':tid'       => $t_id,
        ':lock'      => 'lock',
        ':lock2'      => 'lock',
        ':ok'        => 'ok',        
        ':ok2'        => 'ok',
        ':no_ok'     => 'no_ok',
        ':unlock'    => 'unlock',        
        ':no_ok2'     => 'no_ok',
        ':unlock2'    => 'unlock'
    ));


$tt = $stmt->fetch(PDO::FETCH_ASSOC);

if ($tt['sum_sec']) {
    //$t=round((strtotime($tt['date_op'])-strtotime($date_create)));
   


    $stmt2 = $dbConnection->prepare('SELECT date_op from ticket_log where ticket_id=:tid and msg=:m order by date_op DESC limit 1');
    $stmt2->execute(array(
        ':tid' => $t_id,
        ':m'=>'lock'
    ));
    $tts = $stmt2->fetch(PDO::FETCH_ASSOC);

$now = strtotime(date("Y-m-d H:i:s"));

$t_actual=round(($now - strtotime($tts['date_op'])));



//ТЕКУЩЕЕ ВРЕМЯ - ДАТА ПОСЛЕДНЕЙ БЛОКИРОВКИ = СЕКУНДЫ + СУМ_СЕК
$thash=get_ticket_hash_by_id($t_id);
$t_status=get_ticket_val_by_hash('status', $thash);
$t_lock=get_ticket_val_by_hash('lock_by', $thash);

if ($t_status == 1) {
    $res_time_sec=$tt['sum_sec'];
    //$res_time_sec=$t_actual+$tt['sum_sec'];
}
if ($t_status == 0) {
if ($t_lock == 0) {
    $res_time_sec=$tt['sum_sec'];
}
else if ($t_lock != 0) {
    $res_time_sec=($t_actual)+$tt['sum_sec'];
}
}

//$tt['sum_sec'] - СУМ_СЕК
//


$t=$res_time_sec;

//$t=$tts['date_op'];




    $r=$t;
}
else if (!$tt['sum_sec']) {
    
    $stmt2 = $dbConnection->prepare('SELECT date_op from ticket_log where ticket_id=:tid and msg=:m order by date_op DESC limit 1');
    $stmt2->execute(array(
        ':tid' => $t_id,
        ':m'=>'lock'
    ));
    $tts = $stmt2->fetch(PDO::FETCH_ASSOC);

$now = strtotime(date("Y-m-d H:i:s"));

$t_actual=round(($now - strtotime($tts['date_op'])));



//ТЕКУЩЕЕ ВРЕМЯ - ДАТА ПОСЛЕДНЕЙ БЛОКИРОВКИ = СЕКУНДЫ + СУМ_СЕК
$thash=get_ticket_hash_by_id($t_id);
$t_status=get_ticket_val_by_hash('status', $thash);
$t_lock=get_ticket_val_by_hash('lock_by', $thash);

if ($t_status == 1) {
    $res_time_sec=$tt['sum_sec'];
    //$res_time_sec=$t_actual+$tt['sum_sec'];
}
if ($t_status == 0) {
if ($t_lock == 0) {
    $res_time_sec=$tt['sum_sec'];
}
else if ($t_lock != 0) {
    $res_time_sec=($t_actual)+$tt['sum_sec'];
}
}

//$tt['sum_sec'] - СУМ_СЕК
//


$t=$res_time_sec;

//$t=$tts['date_op'];




    $r=$t;
}

return $r;

}



function get_ticket_time_lock($t_id) {
    global $dbConnection;

$stmt = $dbConnection->prepare("SELECT c.ticket_id, c.init_user_id, Min(c.StartTime), Max(c.EndTime), Sum(c.Seconds) as sum_sec
 FROM (
  SELECT   a.ticket_id,
           a.Id aStateId, a.msg aName,
           b.Id bStateId, b.msg bname,
           a.init_user_id,
           a.date_op as StartTime, min(b.date_op) as EndTime,
           timestampdiff(Second, a.date_op, b.date_op) as Seconds
  FROM ticket_log as a inner join ticket_log as b
    ON ( ((a.msg <=> :lock AND b.msg <=> :ok) OR 
          (a.msg <=> :lock2 AND b.msg <=> :unlock) OR
          (a.msg <=> :no_ok AND b.msg <=> :unlock2) OR
          (a.msg <=> :no_ok2 AND b.msg <=> :ok2))
          AND a.date_op < b.date_op
          -- AND a.init_user_id = b.init_user_id
          AND a.ticket_id = b.ticket_id
          AND a.ticket_id=:tid
       ) 
  GROUP BY a.ticket_id, a.msg, a.date_op
  ) as c
  GROUP BY c.ticket_id");



    $stmt->execute(array(
        ':tid'       => $t_id,
        ':lock'      => 'lock',
        ':lock2'      => 'lock',
        ':ok'        => 'ok',        
        ':ok2'        => 'ok',
        ':no_ok'     => 'no_ok',
        ':unlock'    => 'unlock',        
        ':no_ok2'     => 'no_ok',
        ':unlock2'    => 'unlock'
    ));


$tt = $stmt->fetch(PDO::FETCH_ASSOC);

if ($tt['sum_sec']) {
    //$t=round((strtotime($tt['date_op'])-strtotime($date_create)));
   


    $stmt2 = $dbConnection->prepare('SELECT date_op from ticket_log where ticket_id=:tid and msg=:m order by date_op DESC limit 1');
    $stmt2->execute(array(
        ':tid' => $t_id,
        ':m'=>'lock'
    ));
    $tts = $stmt2->fetch(PDO::FETCH_ASSOC);

$now = strtotime(date("Y-m-d H:i:s"));

$t_actual=round(($now - strtotime($tts['date_op'])));



//ТЕКУЩЕЕ ВРЕМЯ - ДАТА ПОСЛЕДНЕЙ БЛОКИРОВКИ = СЕКУНДЫ + СУМ_СЕК
$thash=get_ticket_hash_by_id($t_id);
$t_status=get_ticket_val_by_hash('status', $thash);
$t_lock=get_ticket_val_by_hash('lock_by', $thash);

if ($t_status == 1) {
    $res_time_sec=$tt['sum_sec'];
    //$res_time_sec=$t_actual+$tt['sum_sec'];
}
if ($t_status == 0) {
if ($t_lock == 0) {
    $res_time_sec=$tt['sum_sec'];
}
else if ($t_lock != 0) {
    $res_time_sec=($t_actual)+$tt['sum_sec'];
}
}

//$tt['sum_sec'] - СУМ_СЕК
//


$t="<time id=\"f\" datetime=\"".$res_time_sec."\"></time>";

//$t=$tts['date_op'];




    $r=$t;
}
else if (!$tt['sum_sec']) {
       $stmt2 = $dbConnection->prepare('SELECT date_op from ticket_log where ticket_id=:tid and msg=:m order by date_op DESC limit 1');
    $stmt2->execute(array(
        ':tid' => $t_id,
        ':m'=>'lock'
    ));
    $tts = $stmt2->fetch(PDO::FETCH_ASSOC);

$now = strtotime(date("Y-m-d H:i:s"));

$t_actual=round(($now - strtotime($tts['date_op'])));



//ТЕКУЩЕЕ ВРЕМЯ - ДАТА ПОСЛЕДНЕЙ БЛОКИРОВКИ = СЕКУНДЫ + СУМ_СЕК
$thash=get_ticket_hash_by_id($t_id);
$t_status=get_ticket_val_by_hash('status', $thash);
$t_lock=get_ticket_val_by_hash('lock_by', $thash);

if ($t_status == 1) {
    $res_time_sec=$tt['sum_sec'];
    //$res_time_sec=$t_actual+$tt['sum_sec'];
}
if ($t_status == 0) {
if ($t_lock == 0) {
    $res_time_sec=$tt['sum_sec'];
}
else if ($t_lock != 0) {
    $res_time_sec=($t_actual)+$tt['sum_sec'];
}
}

//$tt['sum_sec'] - СУМ_СЕК
//


$t="<time id=\"f\" datetime=\"".$res_time_sec."\"></time>";

//$t=$tts['date_op'];




    $r=$t;
}

return $r;

}



function get_sla_plans_times($uniq_id) {
global $dbConnection;

    $stmt = $dbConnection->prepare('SELECT * from sla_plans where uniq_id=:uid');
    $stmt->execute(array(':uid' => $uniq_id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);


$param=array();
$s = $row['reaction_time_low_prio']%60;



$param= array(
    'react_low_1'=>floor(($row['reaction_time_low_prio']%2592000)/86400),
    'react_low_2'=>floor(($row['reaction_time_low_prio']%86400)/3600),
    'react_low_3'=>floor(($row['reaction_time_low_prio']%3600)/60),
    'react_low_4'=>$row['reaction_time_low_prio']%60,

    'react_def_1'=>floor(($row['reaction_time_def']%2592000)/86400),
    'react_def_2'=>floor(($row['reaction_time_def']%86400)/3600),
    'react_def_3'=>floor(($row['reaction_time_def']%3600)/60),
    'react_def_4'=>$row['reaction_time_def']%60,

    'react_high_1'=>floor(($row['reaction_time_high_prio']%2592000)/86400),
    'react_high_2'=>floor(($row['reaction_time_high_prio']%86400)/3600),
    'react_high_3'=>floor(($row['reaction_time_high_prio']%3600)/60),
    'react_high_4'=>$row['reaction_time_high_prio']%60,


    'work_low_1'=>floor(($row['work_time_low_prio']%2592000)/86400),
    'work_low_2'=>floor(($row['work_time_low_prio']%86400)/3600),
    'work_low_3'=>floor(($row['work_time_low_prio']%3600)/60),
    'work_low_4'=>$row['work_time_low_prio']%60,

    'work_def_1'=>floor(($row['work_time_def']%2592000)/86400),
    'work_def_2'=>floor(($row['work_time_def']%86400)/3600),
    'work_def_3'=>floor(($row['work_time_def']%3600)/60),
    'work_def_4'=>$row['work_time_def']%60,

    'work_high_1'=>floor(($row['work_time_high_prio']%2592000)/86400),
    'work_high_2'=>floor(($row['work_time_high_prio']%86400)/3600),
    'work_high_3'=>floor(($row['work_time_high_prio']%3600)/60),
    'work_high_4'=>$row['work_time_high_prio']%60,


    'deadline_low_1'=>floor(($row['deadline_time_low_prio']%2592000)/86400),
    'deadline_low_2'=>floor(($row['deadline_time_low_prio']%86400)/3600),
    'deadline_low_3'=>floor(($row['deadline_time_low_prio']%3600)/60),
    'deadline_low_4'=>$row['deadline_time_low_prio']%60,

    'deadline_def_1'=>floor(($row['deadline_time_def']%2592000)/86400),
    'deadline_def_2'=>floor(($row['deadline_time_def']%86400)/3600),
    'deadline_def_3'=>floor(($row['deadline_time_def']%3600)/60),
    'deadline_def_4'=>$row['deadline_time_def']%60,

    'deadline_high_1'=>floor(($row['deadline_time_high_prio']%2592000)/86400),
    'deadline_high_2'=>floor(($row['deadline_time_high_prio']%86400)/3600),
    'deadline_high_3'=>floor(($row['deadline_time_high_prio']%3600)/60),
    'deadline_high_4'=>$row['deadline_time_high_prio']%60
    );

return $param;
}



function get_sla_view_select_box($level = 0) {
global $dbConnection;

//$result = mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$level); 

    $stmt = $dbConnection->prepare('SELECT id, name, type, uniq_id from sla_plans where parent_id=:p_id order by sort_id ASC');
    $stmt->execute(array(':p_id' => $level));
    $re = $stmt->fetchAll();



//if ($level != 0) { echo "<ul>"; }
//    else if ($level == 0) { echo "<ul class=\"todo-list sortable\" style=\"margin: 0px;\">"; }

   // while ($node = mysql_fetch_array($result)) { 
    foreach ($re as $row) {


//if ($level == 0) {echo "<optgroup label=\"".$row['name']."\">";}
//else if ($level != 0) {
    echo "<option value=\"".$row['id']."\">".$row['name']."</option>";
//}
        //echo "<li id=\"list-".$row['id']."\"><div>".$row['name'];
        ?>

                                                
                                           
                                        
        <?php 
        //$hasChild = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$node['ID'])) != null;

        
        $stmt2 = $dbConnection->prepare('SELECT id, name from sla_plans where parent_id=:p_id');
        $stmt2->execute(array(':p_id' => $row['id']));
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        //$hasChild=$row2['parent_id'];
        

        if ($row2) {
            get_sla_view_select_box($row['id']);
        }
       // echo "</li>";
    }
//echo "</ul>";
}




//get_sla_view()

function get_sla_view($level = 0) {
global $dbConnection;

//$result = mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$level); 

    $stmt = $dbConnection->prepare('SELECT id, name, type, uniq_id from sla_plans where parent_id=:p_id order by sort_id ASC');
    $stmt->execute(array(':p_id' => $level));
    $re = $stmt->fetchAll();



if ($level != 0) { echo "<ul>"; }
    else if ($level == 0) { echo "<ul class=\"todo-list sortable\" style=\"margin: 0px;\">"; }

   // while ($node = mysql_fetch_array($result)) { 
    foreach ($re as $row) {


                if ($row['type'] == "1") {$c1="checked";}
        else if ($row['type'] == "0") {$c1="";}


        //echo "<li id=\"list-".$row['id']."\"><div>".$row['name'];
        ?>
                                        <li id="list_<?=$row['id'];?>">
                                            <div>
                                            <!-- drag handle -->
                                            <span class="handle ui-sortable-handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <!-- checkbox -->
                                            
                                            <!-- todo text -->
                                            <span class="text" id="val_<?=$row['id'];?>" style="  font-weight: 400;">
                                        <a href="#" data-pk="<?=$row['id'];?>" data-url="actions.php" id="edit_item" data-type="text" class="">
                                                <?=$row['name'];?>
                                            </a> 
                                            </span>

                                            <!-- General tools such as edit or delete-->
                                            <span class="tools">
                                                              <!--label style="padding-right:20px;">
                    <input id="make_cat_manual" name="" value="<?=$row['id'];?>" type="checkbox" <?=$c1;?>> <small><?=lang('PORTAL_cat');?></small>
                </label-->

                <i id="edit_sla_plan" value="<?=$row['uniq_id'];?>" class="fa fa-pencil-square-o"></i>  
            <i id="del_item_sla" value="<?=$row['id']?>" class="fa fa-trash-o"></i>
                                              


                                                
                                            </span>
                                        </div>
                                        
        <?php
        //$hasChild = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$node['ID'])) != null;

        
        $stmt2 = $dbConnection->prepare('SELECT id, name from sla_plans where parent_id=:p_id');
        $stmt2->execute(array(':p_id' => $row['id']));
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        //$hasChild=$row2['parent_id'];
        

        if ($row2) {
            get_sla_view($row['id']);
        }
        echo "</li>";
    }
echo "</ul>";
}


function showMenu_sla($level = 0) {
global $dbConnection;

//$result = mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$level); 

    $stmt = $dbConnection->prepare('SELECT id, name, type, uniq_id from subj where parent_id=:p_id order by sort_id ASC');
    $stmt->execute(array(':p_id' => $level));
    $re = $stmt->fetchAll();



if ($level != 0) { echo "<ul>"; }
    else if ($level == 0) { echo "<ul class=\"todo-list sortable\" style=\"margin: 0px;\">"; }

   // while ($node = mysql_fetch_array($result)) { 
    foreach ($re as $row) {


                if ($row['type'] == "1") {$c1="checked";}
        else if ($row['type'] == "0") {$c1="";}


        //echo "<li id=\"list-".$row['id']."\"><div>".$row['name'];
        ?>
                                        <li id="list_<?=$row['id'];?>">
                                            <div>
                                            <!-- drag handle -->
                                            <span class="handle ui-sortable-handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <!-- checkbox -->
                                            
                                            <!-- todo text -->
                                            <span class="text" id="val_<?=$row['id'];?>">
                                        <a href="#" data-pk="<?=$row['id'];?>" data-url="actions.php" id="edit_item" data-type="text" class="">
                                                <?=$row['name'];?>
                                            </a> 
                                            </span>

                                            <!-- General tools such as edit or delete-->
                                            <span class="tools">
                                             
            <i id="del_item_subj" value="<?=$row['id']?>" class="fa fa-trash-o"></i>
                                              


                                                
                                            </span>
                                        </div>
                                        
        <?php
        //$hasChild = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$node['ID'])) != null;

        
        $stmt2 = $dbConnection->prepare('SELECT id, name from subj where parent_id=:p_id');
        $stmt2->execute(array(':p_id' => $row['id']));
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        //$hasChild=$row2['parent_id'];
        

        if ($row2) {
            showMenu_sla($row['id']);
        }
        echo "</li>";
    }
echo "</ul>";
}






function get_ticket_time_reaction_sec_no_lock($t_id) {
    global $dbConnection;

$thash=get_ticket_hash_by_id($t_id);
$date_create=get_ticket_val_by_hash('date_create', $thash);

    //отнять две даты
    //
    $t=round((time()-strtotime($date_create)));
    //$t="<time id=\"f\" datetime=\"".$t."\"></time>";
    //$t="<time id=\"f\" datetime=\"".$tt['date_op']."\"></time>";
    $res=$t;



return $res;
}


function get_ticket_time_reaction_sec($t_id) {
    global $dbConnection;

$thash=get_ticket_hash_by_id($t_id);
$date_create=get_ticket_val_by_hash('date_create', $thash);

    $stmt = $dbConnection->prepare('SELECT date_op from ticket_log where ticket_id=:tid and msg=:m order by date_op ASC limit 1');
    $stmt->execute(array(
        ':tid' => $t_id,
        ':m'=>'lock'
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tt['date_op']) { $r=true;}

    else if (!$tt['date_op']) { $r=false;}


if ($r == true) {

    //отнять две даты
    //
    $t=round((strtotime($tt['date_op'])-strtotime($date_create)));
    //$t="<time id=\"f\" datetime=\"".$t."\"></time>";
    //$t="<time id=\"f\" datetime=\"".$tt['date_op']."\"></time>";
    $res=$t;

} 
else if ($r == false) {$res=0;}

return $res;
}


function get_ticket_time_reaction($t_id) {
    global $dbConnection;

$thash=get_ticket_hash_by_id($t_id);
$date_create=get_ticket_val_by_hash('date_create', $thash);

    $stmt = $dbConnection->prepare('SELECT date_op from ticket_log where ticket_id=:tid and msg=:m order by date_op ASC limit 1');
    $stmt->execute(array(
        ':tid' => $t_id,
        ':m'=>'lock'
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tt['date_op']) { $r=true;}

    else if (!$tt['date_op']) { $r=false;}


if ($r == true) {

    //отнять две даты
    //
    $t=round((strtotime($tt['date_op'])-strtotime($date_create)));
    $t="<time id=\"f\" datetime=\"".$t."\"></time>";
    //$t="<time id=\"f\" datetime=\"".$tt['date_op']."\"></time>";
    $res=$t;

} 
else if ($r == false) {$res=lang('SLA_NOT_LOCK');}

return $res;
}


function view_comment($tid) {
    global $dbConnection;
?>
    
    


    
    
        <div class="direct-chat-messages" id="comment_body" style="min-height: 10px; max-height: 400px; scroll-behavior: initial; overflow-y: scroll;">
    
        
        <?php
    $stmt = $dbConnection->prepare('SELECT user_id, comment_text, dt from comments where t_id=:tid order by dt ASC');
    $stmt->execute(array(
        ':tid' => $tid
    ));
    while ($rews = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        $ru = nameshort(name_of_user_ret($rews['user_id']));
        
        // if (startsWith($rews['comment_text'], '[file:') ) { $ct="file"; }
        // else if (!startsWith($rews['comment_text'], '[file:')) {$ct=make_html($rews['comment_text'], true); }
        if (substr($rews['comment_text'], 0, 6) === "[file:") {
            
            $arr_hash = explode(":", $rews['comment_text']);
            $f_hash = substr($arr_hash[1], 0, -1);
            
            //$hn=get_ticket_id_by_hash($f_hash);
            $stmt2 = $dbConnection->prepare('SELECT original_name, file_size,file_type,file_ext FROM files where file_hash=:tid');
            $stmt2->execute(array(
                ':tid' => $f_hash
            ));
            $file_arr = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            $ct = '<div class=\'text-muted\' style=\'margin-bottom: 5px;\'><em><small>' . lang('EXT_attach_file') . '</small> <br></em>';
            
            $fts = array(
                'image/jpeg',
                'image/gif',
                'image/png'
            );
            
            if (in_array($file_arr['file_type'], $fts)) {
                
                $ct.= ' <small><a class=\'fancybox\' href=\'' . $CONF['hostname'] . 'upload_files/' . $f_hash . '.' . $file_arr['file_ext'] . '\'><img style=\'max-height:100px;\' src=\'' . $CONF['hostname'] . 'upload_files/' . $f_hash . '.' . $file_arr['file_ext'] . '\'></a>  </small>';
            } else {
                $ct.= get_file_icon($f_hash) . ' <small><a href=\'' . $CONF['hostname'] . 'sys/download.php?' . $f_hash . '\'>' . $file_arr['original_name'] . '</a> ' . round(($file_arr['file_size'] / (1024 * 1024)) , 2) . ' Mb </small>';
            }
            
            $ct.= '</div>';
        } else {
            $ct = make_html($rews['comment_text'], true);
        }
?>


 <!-- Message. Default to the left -->
                    <div class="direct-chat-msg">
                      <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name pull-left"><a href="view_user?<?php
        echo get_user_hash_by_id($rews['user_id']); ?>" class="name">
                                                
                                                <?php
        echo $ru; ?>
                                            </a></span>
                        <span class="direct-chat-timestamp pull-right"><small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 
                                                <time id="b" datetime="<?php
        echo $rews['dt']; ?>"></time> <time id="c" datetime="<?php
        echo $rews['dt']; ?>"></time>
                                                </small></span>
                      </div><!-- /.direct-chat-info -->
                      <img class="direct-chat-img <?php
        echo get_user_status_text($rews['user_id']); ?>"  src="<?php
        echo get_user_img_by_id($rews['user_id']); ?>" alt=""><!-- /.direct-chat-img -->
                      <div class="direct-chat-text">
                     <small>    <?=$ct; ?></small>
                      </div><!-- /.direct-chat-text -->
                    </div><!-- /.direct-chat-msg -->


                                            <!-- chat item -->
                                    
                                    
                                    
                                    
                                    
        
        
        
        

        
        
        
        

                            

        <?php
    } ?>
                   
        
    </div>





<?php
}

function get_total_msgs_main() {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT count(id) as cou from messages where type_msg=:tm');
    $stmt->execute(array(
        ':tm' => 'main'
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $tt['cou'];
}

function get_total_unread_messages() {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT count(id) as cou from messages where user_to=:uto and is_read=0');
    $stmt->execute(array(
        ':uto' => $_SESSION['helpdesk_user_id']
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $tt['cou'];
}

function get_notify_opt_list() {

$r=array('ticket_create'  =>lang('PROFILE_notify_new_ticket')  ,
         'ticket_refer'   =>lang('PROFILE_notify_refer_ticket')  ,
         'ticket_comment' =>lang('PROFILE_notify_comment_ticket')  ,
         'ticket_lock'    =>lang('PROFILE_notify_lock_ticket')  ,
         'ticket_unlock'  =>lang('PROFILE_notify_unlock_ticket')  ,
         'ticket_ok'      =>lang('PROFILE_notify_ok_ticket')  ,
         'ticket_no_ok'   =>lang('PROFILE_notify_un_ok_ticket')
);

return $r;
}

function check_notify_mail_user($action, $mail) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('SELECT id from users where email=:uto');
    $stmt->execute(array(':uto' => $mail));
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    $uid=$tt['id'];


    $stmt2 = $dbConnection->prepare('SELECT mail from users_notify where user_id=:uto');
    $stmt2->execute(array(':uto' => $uid));
    $tt2 = $stmt2->fetch(PDO::FETCH_ASSOC);

if ($tt2['mail']) {


$p_str=explode(",",$tt2['mail']);

if (in_array($action, $p_str)) {
    $res=true;
}
if (!in_array($action, $p_str)) {
    $res=false;
}


}
else if (!$tt2['mail']) {
    $res=true;
}

return $res;
}


function check_notify_sms_user($action, $mail) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('SELECT id from users where mob=:uto');
    $stmt->execute(array(':uto' => $mail));
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
    $uid=$tt['id'];


    $stmt2 = $dbConnection->prepare('SELECT sms from users_notify where user_id=:uto');
    $stmt2->execute(array(':uto' => $uid));
    $tt2 = $stmt2->fetch(PDO::FETCH_ASSOC);

if ($tt2['sms']) {


$p_str=explode(",",$tt2['sms']);

if (in_array($action, $p_str)) {
    $res=true;
}
if (!in_array($action, $p_str)) {
    $res=false;
}


}
else if (!$tt2['sms']) {
    $res=false;
}

return $res;
}


function check_unlinked_file() {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT original_name, ticket_hash, file_hash, file_ext FROM files
                                    LEFT JOIN tickets ON tickets.hash_name = files.ticket_hash
                                    WHERE tickets.hash_name IS NULL and obj_type=0');
    $stmt->execute();
    $result = $stmt->fetchAll();
    if (!empty($result)) {
        
        foreach ($result as $row) {
            
            $stmt = $dbConnection->prepare("delete FROM files where ticket_hash=:id");
            $stmt->execute(array(
                ':id' => $row['ticket_hash']
            ));
            unlink(realpath(dirname(__FILE__)) . "/upload_files/" . $row['file_hash'] . "." . $row['file_ext']);
        }
    }
}

function validate_client($user_id, $input) {
    
    global $dbConnection;
    $result = false;
    
    if (!isset($_SESSION['code'])) {
        
        if (isset($_COOKIE['authhash_code'])) {
            
            $user_id = $_COOKIE['authhash_uid'];
            $input = $_COOKIE['authhash_code'];
            $_SESSION['code'] = $input;
            $_SESSION['helpdesk_user_id'] = $user_id;
        }
    }
    
    $ul = get_userlogin_byid($user_id);
    
    if (get_user_authtype($ul)) {
        if (ldap_auth($ul, $input)) {
            
            $stmt = $dbConnection->prepare('SELECT login, fio from users where id=:user_id and status=:ls LIMIT 1');
            $stmt->execute(array(
                ':user_id' => $user_id,
                ':ls' => '1'
            ));
            
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $_SESSION['helpdesk_user_login'] = $row['login'];
                $_SESSION['helpdesk_user_fio'] = $row['fio'];
                
                return true;
            }
        } else if (ldap_auth($ul, $input) == false) {
            return false;
        }
    } else if (get_user_authtype($ul) == false) {
        
        $stmt = $dbConnection->prepare('SELECT pass,login,fio from users where id=:user_id and status=:ls and is_client=:ic LIMIT 1');
        $stmt->execute(array(
            ':user_id' => $user_id,
            ':ls' => '1',
            ':ic' => '1'
        ));
        
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $dbpass = md5($row['pass']);
            $_SESSION['helpdesk_user_login'] = $row['login'];
            $_SESSION['helpdesk_user_fio'] = $row['fio'];
            
            //$_SESSION['helpdesk_sort_prio'] == "none";
            if ($dbpass == $input) {
                return true;
            } else {
                return false;
            }
        }
    }
}

function get_userlogin_byid($in) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT login from users where id=:user_id and status!=2 LIMIT 1');
    $stmt->execute(array(
        ':user_id' => $in
    ));
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $r = $row['login'];
    }
    return $r;
}



function validate_user_by_api($input) {
    
    global $dbConnection, $CONF;
    $result = false;

    $stmt = $dbConnection->prepare('SELECT login, fio from users where api_key=:ls and status!=2 LIMIT 1');
            $stmt->execute(array(
                ':ls' => $input
            ));
            
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $result = true;
    }
    else {
        $result = false;
    }
    
    
    return $result;
}



function validate_user($user_id, $input) {
    
    global $dbConnection, $CONF;
    $result = false;
    
    if (!isset($_SESSION['code'])) {
        
        if (isset($_COOKIE['authhash_code'])) {
            
            $user_id = $_COOKIE['authhash_uid'];
            $input = $_COOKIE['authhash_code'];
            $_SESSION['code'] = $input;
            $_SESSION['helpdesk_user_id'] = $user_id;
        }
    }
    
    $ul = get_userlogin_byid($user_id);
    
    if (get_user_authtype($ul)) {
        if (ldap_auth($ul, $input)) {
            
            $stmt = $dbConnection->prepare('SELECT login, fio from users where id=:user_id and status=:ls and is_client=:ic LIMIT 1');
            $stmt->execute(array(
                ':user_id' => $user_id,
                ':ls' => '1',
            ':ic' => '0'
            ));
            
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $_SESSION['helpdesk_user_login'] = $row['login'];
                $_SESSION['helpdesk_user_fio'] = $row['fio'];
                $stmt = $dbConnection->prepare('update users set last_time=:n where id=:cid');
                $stmt->execute(array(
                    ':cid' => $user_id,
                    ':n' => $CONF['now_dt']
                ));
                return true;
            }
        } else if (ldap_auth($ul, $input) == false) {
            return false;
        }
    } else if (get_user_authtype($ul) == false) {
        
        $stmt = $dbConnection->prepare('SELECT pass,login,fio from users where id=:user_id and status=:ls and is_client=:ic LIMIT 1');
        $stmt->execute(array(
            ':user_id' => $user_id,
            ':ls' => '1',
            ':ic' => '0'
        ));
        
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $dbpass = md5($row['pass']);
            $_SESSION['helpdesk_user_login'] = $row['login'];
            $_SESSION['helpdesk_user_fio'] = $row['fio'];
            
            //$_SESSION['helpdesk_sort_prio'] == "none";
            if ($dbpass == $input) {
                $stmt = $dbConnection->prepare('update users set last_time=:n where id=:cid');
                $stmt->execute(array(
                    ':cid' => $user_id,
                    ':n' => $CONF['now_dt']
                ));
                return true;
            } else {
                return false;
            }
        }
    }
}

function get_user_status($in) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('select last_time from users where id=:in');
    $stmt->execute(array(
        ':in' => $in
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    $lt = $total_ticket['last_time'];
    $d = time() - strtotime($lt);
    if ($d > 70) {
        $lt_tooltip = "";
        if ($lt) {
            $lt_tooltip = lang('stats_last_time') . "<br>" . $lt;
        }
        
        $res = "<span data-toggle=\"tooltip\" data-placement=\"bottom\" class=\"label label-default\" data-original-title=\"" . $lt_tooltip . "\" data-html=\"true\">offline</span>";
    } else {
        $res = "<span class=\"label label-success\">online</span>";
    }
    
    return $res;
}

function get_total_users_online() {
    global $dbConnection, $CONF;
    $stmt = $dbConnection->prepare('select count(id) as cou from users where last_time >= DATE_SUB(:n,INTERVAL 2 MINUTE)');
    $stmt->execute(array(
        ':n' => $CONF['now_dt']
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    $lt = $total_ticket['cou'];
    
    echo $lt;
}

function get_user_status_text($in) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('select last_time from users where id=:in');
    $stmt->execute(array(
        ':in' => $in
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    $lt = $total_ticket['last_time'];
    $d = time() - strtotime($lt);
    if ($d > 70) {
        
        $res = "offline";
    } else {
        $res = "online";
    }
    
    return $res;
}

function get_ticket_id_by_hash($in) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('select id from tickets where hash_name=:in');
    $stmt->execute(array(
        ':in' => $in
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $tt = $total_ticket['id'];
    return $tt;
}

function get_ticket_hash_by_id($in) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('select hash_name from tickets where id=:in');
    $stmt->execute(array(
        ':in' => $in
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $tt = $total_ticket['hash_name'];
    return $tt;
}

function get_ticket_val_by_hash($what, $in) {
    global $CONF;
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT ' . $what . ' FROM tickets where hash_name=:in');
    $stmt->execute(array(
        ':in' => $in
    ));
    
    $fior = $stmt->fetch(PDO::FETCH_NUM);
    
    return $fior[0];
}

function get_user_id_by_hash($in) {
    global $dbConnection;
    

if (strpos($in,',') !== false) {
    $ar=explode(",", $in);

    $p=array();
    foreach ($ar as $value) {
        # code...
    $stmt = $dbConnection->prepare('select id from users where uniq_id=:in');
    $stmt->execute(array(
        ':in' => $value
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    array_push($p, $total_ticket['id']);

    }

$tt=implode(",", $p);

}

else if (strpos($in,',') !== true) {
    $stmt = $dbConnection->prepare('select id from users where uniq_id=:in');
    $stmt->execute(array(
        ':in' => $in
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $tt = $total_ticket['id'];
}

    return $tt;
}


function get_user_hash_by_id($in) {
    global $dbConnection;
    

if (strpos($in,',') !== false) {
    $ar=explode(",", $in);

    $p=array();
    foreach ($ar as $value) {
        # code...
    $stmt = $dbConnection->prepare('select uniq_id from users where id=:in');
    $stmt->execute(array(
        ':in' => $value
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    array_push($p, $total_ticket['uniq_id']);

    }

$tt=implode(",", $p);

}

else if (strpos($in,',') !== true) {
    $stmt = $dbConnection->prepare('select uniq_id from users where id=:in');
    $stmt->execute(array(
        ':in' => $in
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $tt = $total_ticket['uniq_id'];
}

    return $tt;
}


function get_user_form_view() {
        global $dbConnection;

    $stmt = $dbConnection->prepare("SELECT * from user_fields");
    $stmt->execute();
    $result = $stmt->fetchAll();

?>
    <table class="table table-hover" id="">
        <?php
    if (empty($result)) {
?>
            <div id="" class="well well-large well-transparent lead">
                <center>
                    <?php
        echo lang('MSG_no_records'); ?>
                </center>
            </div>
        <?php
    } else if (!empty($result)) {
        ?>
<tr><th><center><small> <?=lang('FIELD_status');?>     </small></center></th>
<th><center><small>     <?=lang('FIELD_name');?>     </small></center></th>
<th><center><small>     placeholder                   </small></center></th>
<th><center><small>     <?=lang('FIELD_value');?>     </small></center></th>
<th><center><small>     <?=lang('FIELD_type');?>     </small></center></th> 
<th><center><small>     <?=lang('FIELD_client');?>     </small></center></th> 
<th><center><small>     <?=lang('FIELD_del');?>     </center></small></center></th></tr>
        <?php
        foreach ($result as $row) {
$sel['text']="";
$sel['select']="";
$sel['multiselect']="";
$sel['textarea']="";

if ($row['status'] == "0") {$st="";}
if ($row['status'] == "1") {$st="checked";}
if ($row['for_client'] == "0") {$st_c="";}
if ($row['for_client'] == "1") {$st_c="checked";}

if ($row['t_type'] == "text") { $sel['text']="selected";}
if ($row['t_type'] == "select") { $sel['select']="selected";}
if ($row['t_type'] == "multiselect") { $sel['multiselect']="selected";}
if ($row['t_type'] == "textarea") { $sel['textarea']="selected";}

$input['value']=$row['value'];
$input['name']=$row['name'];
$input['placeholder']=$row['placeholder'];

if ($row['value'] == "0") {$input['value']="";}
if ($row['name'] == "0") {$input['name']="";}
if ($row['placeholder'] == "0") {$input['placeholder']="";}

?>
                    <tr id ="<?=$row['hash'];?>">
                    <td>    <div class="checkbox">
    <label>
      <input type="checkbox" id="field_perf_check" class="icheck field_perf_check" value="1" <?=$st;?>>
    </label>
  </div></td>
 <td> 
 <input autocomplete="off" name="" type="text" class="form-control input-sm" id="field_perf_name" placeholder="name" 
 value="<?=$input['name']; ?>">
 </td>
 <td> 
 <input autocomplete="off" name="" type="text" class="form-control input-sm" id="field_perf_placeholder" placeholder="placeholder" value="<?php echo $input['placeholder']; ?>">
 </td>
  <td> 
 <input autocomplete="off" name="" type="text" class="form-control input-sm" id="field_perf_value" placeholder="value" value="<?php echo $input['value']; ?>">
 </td>
  <td> 
<select id="field_perf_select" name="" class="form-control input-sm">

<option value="text" <?=$sel['text'];?>><?=lang('FIELD_type_text');?></option>
<option value="textarea" <?=$sel['textarea'];?>><?=lang('FIELD_type_textarea');?></option>
<option value="select" <?=$sel['select'];?>><?=lang('FIELD_type_select');?></option>
<option value="multiselect" <?=$sel['multiselect'];?>><?=lang('FIELD_type_multiselect');?></option>
</select>
 </td>
 <td>    <div class="checkbox">
    <label>
      <input type="checkbox" id="field_perf_client" class="icheck field_perf_client" value="1" <?=$st_c;?>>
    </label>
  </div></td>
  <td> 
<button id="del_userfield_item" class="btn btn-danger btn-sm" type="submit"><i class="fa fa-trash"></i></button>
 </td>

</tr>
                   

                <?php
        }
    }
?>
    </table>

    <br>
<?php
        
}



function get_ticket_form_view() {
        global $dbConnection;

    $stmt = $dbConnection->prepare("SELECT * from ticket_fields");
    $stmt->execute();
    $result = $stmt->fetchAll();

?>
    <table class="table table-hover" id="">
        <?php
    if (empty($result)) {
?>
            <div id="" class="well well-large well-transparent lead">
                <center>
                    <?php
        echo lang('MSG_no_records'); ?>
                </center>
            </div>
        <?php
    } else if (!empty($result)) {
        ?>
<tr><th><center><small> <?=lang('FIELD_status');?>     </small></center></th>
<th><center><small>     <?=lang('FIELD_name');?>     </small></center></th>
<th><center><small>     placeholder                   </small></center></th>
<th><center><small>     <?=lang('FIELD_value');?>     </small></center></th>
<th><center><small>     <?=lang('FIELD_type');?>     </small></center></th> 
<th><center><small>     <?=lang('FIELD_client');?>     </small></center></th> 
<th><center><small>     <?=lang('FIELD_del');?>     </center></small></center></th></tr>
        <?php
        foreach ($result as $row) {
$sel['text']="";
$sel['select']="";
$sel['multiselect']="";
$sel['textarea']="";

if ($row['status'] == "0") {$st="";}
if ($row['status'] == "1") {$st="checked";}
if ($row['for_client'] == "0") {$st_c="";}
if ($row['for_client'] == "1") {$st_c="checked";}

if ($row['t_type'] == "text") { $sel['text']="selected";}
if ($row['t_type'] == "select") { $sel['select']="selected";}
if ($row['t_type'] == "multiselect") { $sel['multiselect']="selected";}
if ($row['t_type'] == "textarea") { $sel['textarea']="selected";}

$input['value']=$row['value'];
$input['name']=$row['name'];
$input['placeholder']=$row['placeholder'];

if ($row['value'] == "0") {$input['value']="";}
if ($row['name'] == "0") {$input['name']="";}
if ($row['placeholder'] == "0") {$input['placeholder']="";}

?>
                    <tr id ="<?=$row['hash'];?>">
                    <td>    <div class="checkbox">
    <label>
      <input type="checkbox" id="field_perf_check" class="icheck field_perf_check" value="1" <?=$st;?>>
    </label>
  </div></td>
 <td> 
 <input autocomplete="off" name="" type="text" class="form-control input-sm" id="field_perf_name" placeholder="name" 
 value="<?=$input['name']; ?>">
 </td>
 <td> 
 <input autocomplete="off" name="" type="text" class="form-control input-sm" id="field_perf_placeholder" placeholder="placeholder" value="<?php echo $input['placeholder']; ?>">
 </td>
  <td> 
 <input autocomplete="off" name="" type="text" class="form-control input-sm" id="field_perf_value" placeholder="value" value="<?php echo $input['value']; ?>">
 </td>
  <td> 
<select id="field_perf_select" name="" class="form-control input-sm">

<option value="text" <?=$sel['text'];?>><?=lang('FIELD_type_text');?></option>
<option value="textarea" <?=$sel['textarea'];?>><?=lang('FIELD_type_textarea');?></option>
<option value="select" <?=$sel['select'];?>><?=lang('FIELD_type_select');?></option>
<option value="multiselect" <?=$sel['multiselect'];?>><?=lang('FIELD_type_multiselect');?></option>
</select>
 </td>
 <td>    <div class="checkbox">
    <label>
      <input type="checkbox" id="field_perf_client" class="icheck field_perf_client" value="1" <?=$st_c;?>>
    </label>
  </div></td>
  <td> 
<button id="del_field_item" class="btn btn-danger btn-sm" type="submit"><i class="fa fa-trash"></i></button>
 </td>

</tr>
                   

                <?php
        }
    }
?>
    </table>

    <br>
<?php
        
}


function get_client_helper() {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare("SELECT
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where client_flag='1'
                            order by dt desc
                            limit 5");
    $stmt->execute();
    $result = $stmt->fetchAll();
?>
    <table class="table table-hover" style="margin-bottom: 0px;" id="">
        <?php
    if (empty($result)) {
?>
            <div id="" class="well well-large well-transparent lead">
                <center>
                    <?php
        echo lang('MSG_no_records'); ?>
                </center>
            </div>
        <?php
    } else if (!empty($result)) {
        foreach ($result as $row) {
?>
                    <tr><td><small><i class="fa fa-file-text-o"></i> </small><a href="helper?h=<?php
            echo $row['hashname']; ?>"><small><?php
            echo cutstr_help2_ret($row['title']); ?></small></a></td><td><small style="float:right;" class="text-muted">(<?php
            echo lang('DASHBOARD_author'); ?>: <?php
            echo nameshort(name_of_user_ret($row['user_init_id'])); ?>)</small></td></tr>

                <?php
        }
    }
?>
    </table>
<?php
}

function get_helper() {
    global $dbConnection;
    
    $user_id = id_of_user($_SESSION['helpdesk_user_login']);
    $unit_user = unit_of_user($user_id);
    $priv_val = priv_status($user_id);
    
    $units = explode(",", $unit_user);
    array_push($units, "0");
    
    $stmt = $dbConnection->prepare('SELECT
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper
                            order by dt desc
                            limit 5');
    $stmt->execute();
    $result = $stmt->fetchAll();
?>
    <table class="table table-hover" style="margin-bottom: 0px;" id="">
        <?php
    if (empty($result)) {
?>
            <div id="" class="well well-large well-transparent lead">
                <center>
                    <?php
        echo lang('MSG_no_records'); ?>
                </center>
            </div>
        <?php
    } else if (!empty($result)) {
        foreach ($result as $row) {
            
            $unit2id = explode(",", $row['unit_to_id']);
            $diff = array_intersect($units, $unit2id);
            if ($priv_val == 1) {
                if ($diff) {
                    $ac = "ok";
                }
                
                if ($user_id == $row['user_init_id']) {
                    $priv_h = "yes";
                }
            } else if ($priv_val == 0) {
                $ac = "ok";
                if ($user_id == $row['user_init_id']) {
                    $priv_h = "yes";
                }
            } else if ($priv_val == 2) {
                $ac = "ok";
                $priv_h = "yes";
            }
            
            if ($ac == "ok") {
?>
                    <tr><td><small><i class="fa fa-file-text-o"></i> </small><a href="helper?h=<?php
                echo $row['hashname']; ?>"><small><?php
                echo cutstr_help2_ret($row['title']); ?></small></a></td><td><small style="float:right;" class="text-muted">(<?php
                echo lang('DASHBOARD_author'); ?>: 
                    <a href="view_user?<?php
                echo get_user_hash_by_id($row['user_init_id']); ?>">
                    <?php
                echo nameshort(name_of_user_ret($row['user_init_id'])); ?>)
                    </a>
                    
                    </small></td></tr>

                <?php
            }
        }
    }
?>
    </table>
<?php
}




function get_clients_total_ticket($in) {
global $dbConnection;

$id=$in;
        $stmt = $dbConnection->prepare('select count(id) as t1 from tickets where client_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $total_ticket['t1'];
}



function get_client_info_client_ticket($id) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT fio,tel,unit_desc,adr,tel_ext,email,login,posada FROM users where id=:id and status!=2');
    $stmt->execute(array(
        ':id' => $id
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $fio_user = $fio['fio'];
    $loginf = $fio['login'];
    $tel_user = $fio['tel'];
    $pod = $fio['unit_desc'];
    $adr = $fio['adr'];
    $tel_ext = $fio['tel_ext'];
    
    $posada = $fio['posada'];
    $email = $fio['email'];
    
    $stmt = $dbConnection->prepare('select count(id) as t1 from tickets where client_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $tt = $total_ticket['t1'];
    
    $stmt = $dbConnection->prepare('select max(date_create) as dc from tickets where client_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $last_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $lt = $last_ticket['dc'];
    
    $uid = $_SESSION['helpdesk_user_id'];
    $priv_val = priv_status($uid);
?>



<div class="box box-info">
                                <div class="box-header">
                                    <i class="fa fa-user"></i>
                                    <h3 class="box-title"><?php
    echo lang('WORKER_TITLE'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
        <h4><center><strong><?php
    echo $fio_user; ?></strong></center></h4>

        <table class="table  ">
            <tbody>
            <?php
    if ($loginf) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?php
        echo lang('WORKER_login'); ?>:</small></td>
                    <td><small><?php
        echo $loginf
?></small></td>
                </tr>
            <?php
    }
    if ($posada) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?php
        echo lang('WORKER_posada'); ?>:</small></td>
                    <td><small><?php
        echo $posada; ?></small></td>
                </tr>
            <?php
    }
    if ($pod) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?php
        echo lang('WORKER_unit'); ?>:</small></td>
                    <td><small><?php
        echo $pod; ?></small></td>
                </tr>
            <?php
    }
    if ($tel_user) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?php
        echo lang('WORKER_tel'); ?>:</small></td>
                    <td><small><?php
        echo $tel_user . " " . $tel_ext; ?></small></td>
                </tr>
            <?php
    }
    if ($adr) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?php
        echo lang('WORKER_room'); ?>:</small></td>
                    <td><small><?php
        echo $adr; ?></small></td>
                </tr>
            <?php
    }
    if ($email) { ?>
                <tr>
                    <td style=" width: 30px; "><small><?php
        echo lang('WORKER_mail'); ?>:</small></td>
                    <td><small><?php
        echo $email; ?></small></td>
                </tr>
            <?php
    } ?>
            <tr>
                <td style=" width: 30px; "><small class="text-muted"><?php
    echo lang('WORKER_total'); ?>:</small></td>
                <td><small>

                            <?php
    echo $tt; ?>
                            
                    </small></td>
            </tr>
<?php
    if ($tt <> 0) { ?>
            <tr>
                <td style=" width: 30px; "><small class="text-muted"><?php
        echo lang('WORKER_last'); ?>:</small></td>
                <td><small>
                
                <time id="b" datetime="<?php
        echo $lt; ?>"></time>
                <time id="c" datetime="<?php
        echo $lt; ?>"></time>
                
                </small></td>
            </tr>
            <?php
    } ?>
            </tbody>
        </table>

    </div>
</div>


<?php
}

function get_client_info_ticket($id) {
    global $dbConnection, $CONF;
    $stmt = $dbConnection->prepare('SELECT fio,tel,unit_desc,adr,email,login,posada,usr_img FROM users where id=:id and status!=2');
    $stmt->execute(array(
        ':id' => $id
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $fio_user = $fio['fio'];
    $loginf = $fio['login'];
    $tel_user = $fio['tel'];
    $pod = $fio['unit_desc'];
    $adr = $fio['adr'];
    $tel_ext = '';
    
    $posada = $fio['posada'];
    $email = $fio['email'];
    if ($fio['usr_img']) {
        $user_img = $CONF['hostname'] . '/upload_files/avatars/' . $fio['usr_img'];
    } else if (!$fio['usr_img']) {
        $user_img = $CONF['hostname'] . '/img/avatar5.png';
    }
    
    $stmt = $dbConnection->prepare('select count(id) as t1 from tickets where client_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $tt = $total_ticket['t1'];
    
    $stmt = $dbConnection->prepare('select max(date_create) as dc from tickets where client_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $last_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $lt = $last_ticket['dc'];
    
    $uid = $_SESSION['helpdesk_user_id'];
    $priv_val = priv_status($uid);
?>
<div class="box box-info">
                                <div class="box-header">
                                    <i class="fa fa-user"></i>
                                    <h3 class="box-title"><?php
    echo lang('WORKER_TITLE'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    
                                    <div class="row">
                                <div class="col-md-5"><img class="img-rounded" src="<?php
    echo $user_img; ?>" height="120"></div>
                                <div class="col-md-7">
                                <h4><center><strong><?php
    echo $fio_user; ?></strong></center></h4>
                                
                                
                                
                                
                                
                                 <small class="text-muted"><?php
    echo lang('WORKER_total'); ?>:</small>
                                 <small>
                        <?php
    if ($priv_val <> "1") { ?>
                        <a target="_blank" href="userinfo?user=<?php
        echo get_user_hash_by_id($id);
?>">
                            <?php
    } ?>
                            <?php
    echo $tt; ?>
                            <?php
    if ($priv_val <> "1") { ?>
                        </a>
                    <?php
    } ?>
                    </small><br>
<?php
    if ($tt <> 0) { ?>
            
            <small class="text-muted"><?php
        echo lang('WORKER_last'); ?>:</small>
               <small><?php
        if ($priv_val <> "1") { ?><a target="_blank" href="userinfo?user=<?php
            echo get_user_hash_by_id($id);
?>"><?php
        } ?>
                
                <time id="b" datetime="<?php
        echo $lt; ?>"></time>
                <time id="c" datetime="<?php
        echo $lt; ?>"></time>
                
                <?php
        if ($priv_val <> "1") { ?></a><?php
        } ?></small>
            
            <?php
    } ?>

                                
                                
                                
                                
                                
                                
                                
                                
                                </div>
                                </div>
                                <br>

        <table class="table  ">
            <tbody>
            <?php
    if ($loginf) { ?>
                <tr>
                    <td style=" width: 50px; "><small><?php
        echo lang('WORKER_login'); ?>:</small></td>
                    <td><small><?php
        echo $loginf
?></small></td>
                </tr>
            <?php
    }
    if ($posada) { ?>
                <tr>
                    <td style=" width: 50px; "><small><?php
        echo lang('WORKER_posada'); ?>:</small></td>
                    <td><small><?php
        echo $posada; ?></small></td>
                </tr>
            <?php
    }
    if ($pod) { ?>
                <tr>
                    <td style=" width: 50px; "><small><?php
        echo lang('WORKER_unit'); ?>:</small></td>
                    <td><small><?php
        echo $pod; ?></small></td>
                </tr>
            <?php
    }
    if ($tel_user) { ?>
                <tr>
                    <td style=" width: 50px; "><small><?php
        echo lang('WORKER_tel'); ?>:</small></td>
                    <td><small><?php
        echo $tel_user . " " . $tel_ext; ?></small></td>
                </tr>
            <?php
    }
    if ($adr) { ?>
                <tr>
                    <td style=" width: 50px; "><small><?php
        echo lang('WORKER_room'); ?>:</small></td>
                    <td><small><?php
        echo $adr; ?></small></td>
                </tr>
            <?php
    }
    if ($email) { ?>
                <tr>
                    <td style=" width: 50px; "><small><?php
        echo lang('WORKER_mail'); ?>:</small></td>
                    <td><small><?php
        echo $email; ?></small></td>
                </tr>
            <?php
    } ?>
                       </tbody>
        </table>

                                    
 
<?php
        $stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=1 and user_fields.status=1');
        $stmtf->execute(array(':uid' => $id));
        $resf = $stmtf->fetchAll();


if (!empty($resf)) {
?>
<br>
<center>
<?=lang('FIELD_add_title');?></center>
<table class="table  ">
   <tbody>
<?php



foreach ($resf as $fv) {

    ?>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo $fv['udfn']; ?>:</small></td>
                <td><small><?php
        echo $fv['udf']
?></small></td>
            </tr>
    <?php


}
?>

                 </tbody>
                 </table> 

<?php
}

?>

<?php

        if ($priv_val <> "1") { 
        $stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=0 and user_fields.status=1');
        $stmtf->execute(array(':uid' => $id));
        $resf = $stmtf->fetchAll();


if (!empty($resf)) {
?>
<br>
<table class="table  ">
   <tbody>
<?php



foreach ($resf as $fv) {

    ?>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo $fv['udfn']; ?>:</small></td>
                <td><small><?php
        echo $fv['udf']
?></small></td>
            </tr>
    <?php


}
?>

                 </tbody>
                 </table> 

<?php
}
}
?>                                   
                                    
                                    
                                                                 </div><!-- /.box-body -->
                            </div>




<?php
}


/*
/////////////////////////////////////
function api_back($type_op, $lang, $user_id, $ticket_id) {
    global $dbConnection,$base,$CONF;
  //API Url
$url = 'http://example.com/api/JSON/create';
 
//Initiate cURL.
$ch = curl_init($url);
 

        $stmt = $dbConnection->prepare('SELECT * FROM tickets where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $ticket_res = $stmt->fetch(PDO::FETCH_ASSOC);



//The JSON data.
$jsonData = array(
    'username' => 'MyUsername',
    'password' => 'MyPassword'
);
 
//Encode the array into JSON.
$jsonDataEncoded = json_encode($jsonData);
 
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
 
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
 
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
 
//Execute the request
$result = curl_exec($ch);
return $result;
}
/////////////////////////////////////
*/

function get_unit_name_return4news($input) {
    global $dbConnection;
    
    $u = explode(",", $input);
    foreach ($u as $val) {
        
        $stmt = $dbConnection->prepare('SELECT name FROM deps where id=:val');
        $stmt->execute(array(
            ':val' => $val
        ));
        $dep = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $res = $dep['name'];
    }
    return $res;
}

function get_unit_name_return($input) {
    global $dbConnection;
    
    $u = explode(",", $input);
    $res = array();
    foreach ($u as $val) {
        
        $stmt = $dbConnection->prepare('SELECT name FROM deps where id=:val');
        $stmt->execute(array(
            ':val' => $val
        ));
        $dep = $stmt->fetch(PDO::FETCH_ASSOC);
        
        array_push($res, $dep['name']);
        
        //$res.=$dep['name'];
        //$res.="<br>";
        
        
    }
    
    return $res;
}

function view_array($in) {
    $end_element = array_pop($in);
    foreach ($in as $value) {
        
        // делаем что-либо с каждым элементом
        $res.= $value;
        $res.= "<br>";
    }
    $res.= $end_element;
    
    // делаем что-либо с последним элементом $end_element
    
    return $res;
}

function get_user_val_by_api($id, $in) {
    
    //val.id
    global $CONF;
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT ' . $in . ' FROM users where api_key=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    
    $fior = $stmt->fetch(PDO::FETCH_NUM);
    
    return $fior[0];
}

function get_user_add_field_val($user_id, $field_id) {
        global $CONF;
    global $dbConnection;

            $stmt = $dbConnection->prepare('SELECT field_val FROM user_data where user_id=:val and field_id=:fid');
        $stmt->execute(array(
            ':val' => $user_id,
            ':fid' => $field_id
        ));
        $dep = $stmt->fetch(PDO::FETCH_ASSOC);

return $dep['field_val'];
}
function get_user_val_by_hash_api($id, $in) {
    
    //val.id
    global $CONF;
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT ' . $in . ' FROM users where uniq_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    
    $fior = $stmt->fetch(PDO::FETCH_NUM);
    
$r=$fior[0];

if ($fior[0] == Null) {
    $r=null;
} 

    return $r;
}

function get_user_val_by_hash($id, $in) {
    
    //val.id
    global $CONF;
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT ' . $in . ' FROM users where uniq_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    
    $fior = $stmt->fetch(PDO::FETCH_NUM);
    
    return $fior[0];
}



function get_ticket_action_priv_api_arr($ticked_id, $user_id) {
    global $CONF,$dbConnection;


$priv_res_arr=array();

$ticket_hash=get_ticket_hash_by_id($ticked_id);

$ticket['arch']=get_ticket_val_by_hash('arch', $ticket_hash);
$ticket['status']=get_ticket_val_by_hash('status',$ticket_hash);
$ticket['ok_by']=get_ticket_val_by_hash('ok_by',$ticket_hash);
$ticket['lock_by']=get_ticket_val_by_hash('lock_by',$ticket_hash);
$ticket['user_init_id']=get_ticket_val_by_hash('user_init_id',$ticket_hash);
$ticket['user_to_id']=get_ticket_val_by_hash('user_to_id',$ticket_hash);
$ticket['user_units']=get_ticket_val_by_hash('unit_id',$ticket_hash);

$user['priv']=get_user_val_by_id($user_id, 'priv');
$user['units']=get_user_val_by_id($user_id, 'unit');

$haystack=explode(",", $ticket['user_to_id']);
$haystack_units=explode(",", $user['units']);


if ($ticket['arch'] == 1) {
    $st = 'arch';
}
else if ($ticket['arch'] == 0) {
                if ($ticket['status'] == 1) {
                    //$st = 'ok';
                        if ($ticket['ok_by'] == $user_id) {
                            $st = "ok_by_me";
                        }
                        
                        if ($ticket['ok_by'] <> $user_id) {
                            $st = "ok_by_other";
                        }


                }
                if ($ticket['status'] == 0) {
                    if ($ticket['lock_by'] <> 0) {
                        
                        if ($ticket['lock_by'] == $user_id) {
                            $st = "lock_by_me";
                        }
                        
                        if ($ticket['lock_by'] <> $user_id) {
                            $st = "lock_by_other";
                        }
                    }
                    else if ($ticket['lock_by'] == 0) {
                        $st = "free";
                    }
                }
            }

//echo $st;

$tpriv=false;


$priv_res="no_priv";

//Если я инициатор
//Если заявка мне
//Если заявка моему отделу


if ($user['priv'] == 1) {

if ($ticket['user_init_id'] == $user_id) {$tpriv=true;}
if (in_array($ticket['user_units'], $haystack_units)) {
    if ($ticket['user_to_id'] == 0) {$tpriv=true;}
    else if ($ticket['user_to_id'] != 0) {
        if (in_array($user_id, $haystack)) {$tpriv=true;}
    }
}

    if ($tpriv==true) {
    switch ($st) {
        case 'arch':
            $priv_res="no_priv";
            array_push($priv_res_arr, "no_priv");
            # code...
            break;
        case 'free':
            $priv_res="ref,lock";
            //array_push($priv_res_arr, "ref");
            array_push($priv_res_arr, "lock");
            # code...
            break;
        case 'ok_by_me':
            $priv_res="un_ok";
            array_push($priv_res_arr, "un_ok");
            # code...
            break;
        case 'ok_by_other':
            $priv_res="no_priv";
            array_push($priv_res_arr, "no_priv");
            # code...
            break;
        case 'lock_by_me':
            $priv_res="ref,unlock,ok";
            //array_push($priv_res_arr, "ref");
            array_push($priv_res_arr, "unlock");
            array_push($priv_res_arr, "ok");
            # code...
            break;
        case 'lock_by_other':
            $priv_res="no_priv";
            array_push($priv_res_arr, "no_priv");
            # code...
            break;
        
        default:
            $priv_res="no_priv";
            array_push($priv_res_arr, "no_priv");
            break;
    }
}
else if ($tpriv==true) {
    $priv_res="no_priv";
    array_push($priv_res_arr, "no_priv");
}
}


if ($user['priv'] == 0) {

if ($ticket['user_init_id'] == $user_id) {$tpriv=true;}
if (in_array($ticket['user_units'], $haystack_units)) {
    $tpriv=true;
}

    if ($tpriv==true) {
    switch ($st) {
        case 'arch':
            $priv_res="no_priv";
            array_push($priv_res_arr, "no_priv");
            # code...
            break;
        case 'free':
            $priv_res="ref,lock";
            //array_push($priv_res_arr, "ref");
            array_push($priv_res_arr, "lock");
            # code...
            break;
        case 'ok_by_me':
            $priv_res="un_ok";
            array_push($priv_res_arr, "un_ok");
            # code...
            break;
        case 'ok_by_other':
            $priv_res="un_ok";
            array_push($priv_res_arr, "un_ok");
            # code...
            break;
        case 'lock_by_me':
            $priv_res="ref,unlock,ok";
            //array_push($priv_res_arr, "ref");
            array_push($priv_res_arr, "unlock");
            array_push($priv_res_arr, "ok");
            # code...
            break;
        case 'lock_by_other':
            $priv_res="unlock";
            array_push($priv_res_arr, "unlock");
            # code...
            break;
        
        default:
            $priv_res="no_priv";
            array_push($priv_res_arr, "no_priv");
            break;
    }
}
else if ($tpriv==true) {
    $priv_res="no_priv";
    array_push($priv_res_arr, "no_priv");
}
}



if ($user['priv'] == 2) {
    
    switch ($st) {
        case 'arch':
            $priv_res="no_priv";
            array_push($priv_res_arr, "no_priv");
            # code...
            break;
        case 'free':
            $priv_res="ref,lock";
            //array_push($priv_res_arr, "ref");
            array_push($priv_res_arr, "lock");
            # code...
            break;
        case 'ok_by_me':
            $priv_res="un_ok";
            array_push($priv_res_arr, "un_ok");
            # code...
            break;
        case 'ok_by_other':
            $priv_res="un_ok";
            array_push($priv_res_arr, "un_ok");
            # code...
            break;
        case 'lock_by_me':
            $priv_res="ref,unlock,ok";
            //array_push($priv_res_arr, "ref");
            array_push($priv_res_arr, "unlock");
            array_push($priv_res_arr, "ok");
            # code...
            break;
        case 'lock_by_other':
            $priv_res="unlock";
            array_push($priv_res_arr, "unlock");
            # code...
            break;
        
        default:
            $priv_res="no_priv";
            array_push($priv_res_arr, "no_priv");
            break;
    }

}

/*
foreach ($priv_res_arr as $key => $value) {
    # code...
    $ares[$value]=$value;
}
*/

$ares=implode(",", $priv_res_arr);

return $ares;
//echo $lo;
}




function get_ticket_action_priv_api($ticked_id, $user_id) {
    global $CONF,$dbConnection;

$ticket_hash=get_ticket_hash_by_id($ticked_id);

$ticket['arch']=get_ticket_val_by_hash('arch', $ticket_hash);
$ticket['status']=get_ticket_val_by_hash('status',$ticket_hash);
$ticket['ok_by']=get_ticket_val_by_hash('ok_by',$ticket_hash);
$ticket['lock_by']=get_ticket_val_by_hash('lock_by',$ticket_hash);
$ticket['user_init_id']=get_ticket_val_by_hash('user_init_id',$ticket_hash);
$ticket['user_to_id']=get_ticket_val_by_hash('user_to_id',$ticket_hash);
$ticket['user_units']=get_ticket_val_by_hash('unit_id',$ticket_hash);

$user['priv']=get_user_val_by_id($user_id, 'priv');
$user['units']=get_user_val_by_id($user_id, 'unit');

$haystack=explode(",", $ticket['user_to_id']);
$haystack_units=explode(",", $user['units']);


if ($ticket['arch'] == 1) {
    $st = 'arch';
}
else if ($ticket['arch'] == 0) {
                if ($ticket['status'] == 1) {
                    //$st = 'ok';
                        if ($ticket['ok_by'] == $user_id) {
                            $st = "ok_by_me";
                        }
                        
                        if ($ticket['ok_by'] <> $user_id) {
                            $st = "ok_by_other";
                        }


                }
                if ($ticket['status'] == 0) {
                    if ($ticket['lock_by'] <> 0) {
                        
                        if ($ticket['lock_by'] == $user_id) {
                            $st = "lock_by_me";
                        }
                        
                        if ($ticket['lock_by'] <> $user_id) {
                            $st = "lock_by_other";
                        }
                    }
                    else if ($ticket['lock_by'] == 0) {
                        $st = "free";
                    }
                }
            }

//echo $st;

$tpriv=false;


$priv_res="no_priv";

//Если я инициатор
//Если заявка мне
//Если заявка моему отделу


if ($user['priv'] == 1) {

if ($ticket['user_init_id'] == $user_id) {$tpriv=true;}
if (in_array($ticket['user_units'], $haystack_units)) {
    if ($ticket['user_to_id'] == 0) {$tpriv=true;}
    else if ($ticket['user_to_id'] != 0) {
        if (in_array($user_id, $haystack)) {$tpriv=true;}
    }
}

    if ($tpriv==true) {
    switch ($st) {
        case 'arch':
            $priv_res="no_priv";
            # code...
            break;
        case 'free':
            $priv_res="ref,lock";
            # code...
            break;
        case 'ok_by_me':
            $priv_res="un_ok";
            # code...
            break;
        case 'ok_by_other':
            $priv_res="no_priv";
            # code...
            break;
        case 'lock_by_me':
            $priv_res="ref,unlock,ok";
            # code...
            break;
        case 'lock_by_other':
            $priv_res="no_priv";
            # code...
            break;
        
        default:
            $priv_res="no_priv";
            break;
    }
}
else if ($tpriv==true) {
    $priv_res="no_priv";
}
}


if ($user['priv'] == 0) {

if ($ticket['user_init_id'] == $user_id) {$tpriv=true;}
if (in_array($ticket['user_units'], $haystack_units)) {
    $tpriv=true;
}

    if ($tpriv==true) {
    switch ($st) {
        case 'arch':
            $priv_res="no_priv";
            # code...
            break;
        case 'free':
            $priv_res="ref,lock";
            # code...
            break;
        case 'ok_by_me':
            $priv_res="un_ok";
            # code...
            break;
        case 'ok_by_other':
            $priv_res="un_ok";
            # code...
            break;
        case 'lock_by_me':
            $priv_res="ref,unlock,ok";
            # code...
            break;
        case 'lock_by_other':
            $priv_res="unlock";
            # code...
            break;
        
        default:
            $priv_res="no_priv";
            break;
    }
}
else if ($tpriv==true) {
    $priv_res="no_priv";
}
}



if ($user['priv'] == 2) {
    
    switch ($st) {
        case 'arch':
            $priv_res="no_priv";
            # code...
            break;
        case 'free':
            $priv_res="ref,lock";
            # code...
            break;
        case 'ok_by_me':
            $priv_res="un_ok";
            # code...
            break;
        case 'ok_by_other':
            $priv_res="un_ok";
            # code...
            break;
        case 'lock_by_me':
            $priv_res="ref,unlock,ok";
            # code...
            break;
        case 'lock_by_other':
            $priv_res="unlock";
            # code...
            break;
        
        default:
            $priv_res="no_priv";
            break;
    }

}



return $priv_res;
//echo $lo;
}



function get_ticket_action_priv($ticked_id) {
    global $CONF,$dbConnection;
$user_id=$_SESSION['helpdesk_user_id'];
$ticket_hash=get_ticket_hash_by_id($ticked_id);

$ticket['arch']=get_ticket_val_by_hash('arch', $ticket_hash);
$ticket['status']=get_ticket_val_by_hash('status',$ticket_hash);
$ticket['ok_by']=get_ticket_val_by_hash('ok_by',$ticket_hash);
$ticket['lock_by']=get_ticket_val_by_hash('lock_by',$ticket_hash);
$ticket['user_init_id']=get_ticket_val_by_hash('user_init_id',$ticket_hash);
$ticket['user_to_id']=get_ticket_val_by_hash('user_to_id',$ticket_hash);
$ticket['user_units']=get_ticket_val_by_hash('unit_id',$ticket_hash);

$user['priv']=get_user_val_by_id($user_id, 'priv');
$user['units']=get_user_val_by_id($user_id, 'unit');

$haystack=explode(",", $ticket['user_to_id']);
$haystack_units=explode(",", $user['units']);


if ($ticket['arch'] == 1) {
    $st = 'arch';
}
else if ($ticket['arch'] == 0) {
                if ($ticket['status'] == 1) {
                    //$st = 'ok';
                        if ($ticket['ok_by'] == $user_id) {
                            $st = "ok_by_me";
                        }
                        
                        if ($ticket['ok_by'] <> $user_id) {
                            $st = "ok_by_other";
                        }


                }
                if ($ticket['status'] == 0) {
                    if ($ticket['lock_by'] <> 0) {
                        
                        if ($ticket['lock_by'] == $user_id) {
                            $st = "lock_by_me";
                        }
                        
                        if ($ticket['lock_by'] <> $user_id) {
                            $st = "lock_by_other";
                        }
                    }
                    else if ($ticket['lock_by'] == 0) {
                        $st = "free";
                    }
                }
            }

//echo $st;

$tpriv=false;


$priv_res="no_priv";

//Если я инициатор
//Если заявка мне
//Если заявка моему отделу


if ($user['priv'] == 1) {

if ($ticket['user_init_id'] == $user_id) {$tpriv=true;}
if (in_array($ticket['user_units'], $haystack_units)) {
    if ($ticket['user_to_id'] == 0) {$tpriv=true;}
    else if ($ticket['user_to_id'] != 0) {
        if (in_array($user_id, $haystack)) {$tpriv=true;}
    }
}

    if ($tpriv==true) {
    switch ($st) {
        case 'arch':
            $priv_res="no_priv";
            # code...
            break;
        case 'free':
            $priv_res="ref,lock";
            # code...
            break;
        case 'ok_by_me':
            $priv_res="un_ok";
            # code...
            break;
        case 'ok_by_other':
            $priv_res="no_priv";
            # code...
            break;
        case 'lock_by_me':
            $priv_res="ref,unlock,ok";
            # code...
            break;
        case 'lock_by_other':
            $priv_res="no_priv";
            # code...
            break;
        
        default:
            $priv_res="no_priv";
            break;
    }
}
else if ($tpriv==true) {
    $priv_res="no_priv";
}
}


if ($user['priv'] == 0) {

if ($ticket['user_init_id'] == $user_id) {$tpriv=true;}
if (in_array($ticket['user_units'], $haystack_units)) {
    $tpriv=true;
}

    if ($tpriv==true) {
    switch ($st) {
        case 'arch':
            $priv_res="no_priv";
            # code...
            break;
        case 'free':
            $priv_res="ref,lock";
            # code...
            break;
        case 'ok_by_me':
            $priv_res="un_ok";
            # code...
            break;
        case 'ok_by_other':
            $priv_res="un_ok";
            # code...
            break;
        case 'lock_by_me':
            $priv_res="ref,unlock,ok";
            # code...
            break;
        case 'lock_by_other':
            $priv_res="unlock";
            # code...
            break;
        
        default:
            $priv_res="no_priv";
            break;
    }
}
else if ($tpriv==true) {
    $priv_res="no_priv";
}
}



if ($user['priv'] == 2) {
    
    switch ($st) {
        case 'arch':
            $priv_res="no_priv";
            # code...
            break;
        case 'free':
            $priv_res="ref,lock";
            # code...
            break;
        case 'ok_by_me':
            $priv_res="un_ok";
            # code...
            break;
        case 'ok_by_other':
            $priv_res="un_ok";
            # code...
            break;
        case 'lock_by_me':
            $priv_res="ref,unlock,ok";
            # code...
            break;
        case 'lock_by_other':
            $priv_res="unlock";
            # code...
            break;
        
        default:
            $priv_res="no_priv";
            break;
    }

}



return $priv_res;
//echo $lo;
}

function get_button_act_status($arr, $button) {
        global $dbConnection;
//$arr: no_priv, lock,unlock, ref,ok,un_ok
        $arr_str=explode(",", $arr);
$res="disabled";
        if (in_array("no_priv", $arr_str)) {
$res="disabled";
        }
        else if (!in_array("no_priv", $arr_str)) {

            switch ($button) {
                case 'refer':
                    if (in_array("ref", $arr_str)) {$res="";}
                    break;
                case 'lock':
                    if (in_array("lock", $arr_str)) {$res="";}
                    break;
                case 'unlock':
                    if (in_array("unlock", $arr_str)) {$res="";}
                    break;
                case 'ok':
                    if (in_array("un_ok", $arr_str)) {$res="";}
                    break;
                case 'no_ok':
                    if (in_array("ok", $arr_str)) {$res="";}
                    break;
                
                default:
                    $res="disabled";
                    break;
            }

        }
return $res;
}




function get_user_val_by_id($id, $in) {
    
    //val.id
    global $CONF;
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT ' . $in . ' FROM users where id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    
    $fior = $stmt->fetch(PDO::FETCH_NUM);
    
    return $fior[0];
}

function get_user_name($in) {
    $parts = explode(" ", $in);
    return $parts[1];
}

function get_user_val($in) {
    global $CONF;
    global $dbConnection;
    $i = $_SESSION['helpdesk_user_id'];
    $stmt = $dbConnection->prepare('SELECT ' . $in . ' FROM users where id=:id');
    $stmt->execute(array(
        ':id' => $i
    ));
    
    $fior = $stmt->fetch(PDO::FETCH_NUM);
    
    return $fior[0];
}

function get_logo_img($type) {
    global $CONF;
    
    if (isset($type)) {
    if (get_conf_param('logo_img')) {
        $fn=explode(".", get_conf_param('logo_img'));
        $gn=$fn[0]."_logo.".$fn[1];
        $r = $CONF['hostname'] . 'upload_files/avatars/' . $gn;
    } else {
        $r = $CONF['hostname'] . 'img/ZENLIX_small.png';
    }

    }
        else if (!isset($type)) {

    if (get_conf_param('logo_img')) {
        $r = $CONF['hostname'] . 'upload_files/avatars/' . get_conf_param('logo_img');
    } else {
        $r = $CONF['hostname'] . 'img/ZENLIX.png';
    }
}
    return $r;
}

function get_user_img() {
    global $CONF;
    
    if (get_user_val('usr_img')) {
        $r = $CONF['hostname'] . 'upload_files/avatars/' . get_user_val('usr_img');
    } else {
        $r = $CONF['hostname'] . 'img/avatar5.png';
    }
    return $r;
}

function get_user_img_by_id($id) {
    global $CONF;
    
    if (get_user_val_by_id($id, 'usr_img')) {
        $r = $CONF['hostname'] . 'upload_files/avatars/' . get_user_val_by_id($id, 'usr_img');
    } else {
        $r = $CONF['hostname'] . 'img/avatar5.png';
    }
    return $r;
}

function get_my_info() {
    global $CONF;
    global $dbConnection;
    $id = $_SESSION['helpdesk_user_id'];
    $stmt = $dbConnection->prepare('SELECT fio,tel,unit_desc,adr,email,login, posada, email, usr_img FROM users where id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $priv_edit_client = get_user_val('priv_edit_client');
    $fio_user = $fio['fio'];
    $loginf = $fio['login'];
    $tel_user = $fio['tel'];
    $pod = $fio['unit_desc'];
    $adr = $fio['adr'];
    $tel_ext = '';
    $mails = $fio['email'];
    $posada = $fio['posada'];
    
    if ($fio['usr_img']) {
        $user_img = $CONF['hostname'] . '/upload_files/avatars/' . $fio['usr_img'];
    } else if (!$fio['usr_img']) {
        $user_img = $CONF['hostname'] . '/img/avatar5.png';
    }
    
    $stmt = $dbConnection->prepare('select count(id) as t1 from tickets where client_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    $tt = $total_ticket['t1'];
    
    $stmt = $dbConnection->prepare('select max(date_create) as dc from tickets where client_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $last_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $lt = $last_ticket['dc'];
    $uid = $_SESSION['helpdesk_user_id'];
    $priv_val = priv_status($uid);
?>



   <div class="box box-info">
                                <div class="box-header">
                                    <i class="fa fa-user"></i>
                                    <h3 class="box-title"> <?php
    echo lang('WORKER_TITLE'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body" >
                                
                                <div class="row">
                                <div class="col-md-5"><img class="img-rounded" src="<?php
    echo $user_img; ?>" height="120"></div>
                                <div class="col-md-7">
                                <h4><center><strong><?php
    echo $fio_user; ?></strong></center></h4></div>
                                </div>
                                <br>
                                    
                                    
                                    
                                    
                                    
                                    
                                    <table class="table  ">
            <tbody>

            <tr>
                <td style=" width: 30px; "><small><?php
    echo lang('WORKER_login'); ?>:</small></td>
                <td><small><?php
    echo $loginf
?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
    echo lang('WORKER_posada'); ?>:</small></td>
                <td><small><?php
    echo $posada
?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
    echo lang('WORKER_unit'); ?>:</small></td>
                <td><small><?php
    echo $pod; ?></small></td>
            </tr>

            <tr>
                <td style=" width: 30px; "><small><?php
    echo lang('WORKER_tel'); ?>:</small></td>
                <td><small><?php
    echo $tel_user . " " . $tel_ext; ?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
    echo lang('WORKER_room'); ?>:</small></td>
                <td><small><?php
    echo $adr; ?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
    echo lang('WORKER_mail'); ?>:</small></td>
                <td><small><?php
    echo $mails
?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small class="text-muted"><?php
    echo lang('WORKER_total'); ?>:</small></td>
                <td><small class="text-muted">
                        <?php
    if ($priv_val <> "1") { ?>
                        <a target="_blank" href="userinfo?user=<?php
        echo get_user_hash_by_id($id);
?>"><?php
    } ?><?php
    echo $tt; ?><?php
    if ($priv_val <> "1") { ?></a><?php
    } ?></small></td>
            </tr>
<?php
    if ($tt <> 0) { ?>
            <tr>
                <td style=" width: 30px; "><small class="text-muted"><?php
        echo lang('WORKER_last'); ?>:</small></td>
                <td><small class="text-muted">
                        <?php
        if ($priv_val <> "1") { ?>
                        <a target="_blank" href="userinfo?user=<?php
            echo get_user_hash_by_id($id);
?>">
                            <?php
        } ?>
                <time id="b" datetime="<?php
        echo $lt; ?>"></time>
                <time id="c" datetime="<?php
        echo $lt; ?>"></time>
                            
                            <?php
        if ($priv_val <> "1") { ?></a><?php
        } ?></small></td>
            </tr>
            <?php
    } ?>
            </tbody>
        </table>
                                    
                                </div><!-- /.box-body -->
                            </div>
<?php
}

function get_client_info($id) {
    global $CONF;
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT fio,tel,unit_desc,adr,email,login, posada, email, usr_img FROM users where id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $priv_edit_client = get_user_val('priv_edit_client');
    $fio_user = $fio['fio'];
    $loginf = $fio['login'];
    $tel_user = $fio['tel'];
    $pod = $fio['unit_desc'];
    $adr = $fio['adr'];
    $tel_ext = '';
    $mails = $fio['email'];
    $posada = $fio['posada'];
    
    if ($fio['usr_img']) {
        $user_img = $CONF['hostname'] . '/upload_files/avatars/' . $fio['usr_img'];
    } else if (!$fio['usr_img']) {
        $user_img = $CONF['hostname'] . '/img/avatar5.png';
    }
    
    $stmt = $dbConnection->prepare('select count(id) as t1 from tickets where client_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    $tt = $total_ticket['t1'];
    
    $stmt = $dbConnection->prepare('select max(date_create) as dc from tickets where client_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $last_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $lt = $last_ticket['dc'];
    $uid = $_SESSION['helpdesk_user_id'];
    $priv_val = priv_status($uid);
    
    //echo $priv_edit_client;
    if ($priv_edit_client == 1) {
        $can_edit = true;
    } else if ($priv_edit_client == 0) {
        $can_edit = false;
    }
    
    //$can_edit=false;
    if ($can_edit == true) {
?>

<div class="box box-info">
                                <div class="box-header">
                                    <i class="fa fa-user"></i>
                                    <h3 class="box-title"> <?php
        echo lang('WORKER_TITLE'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body" >
                                
                                <div class="row">
                                <div class="col-md-5"><img class="img-rounded" src="<?php
        echo $user_img; ?>" height="120"></div>
                                <div class="col-md-7">
                                <h4><center><strong><?php
        echo $fio_user; ?></strong></center></h4>
                                
                                
                                
                                
                                
                          <small class="text-muted"><?php
        echo lang('WORKER_total'); ?>:</small>
               <small class="text-muted">
                        <?php
        if ($priv_val <> "1") { ?>
                        <a target="_blank" href="userinfo?user=<?php
            echo get_user_hash_by_id($id);
?>"><?php
        } ?><?php
        echo $tt; ?><?php
        if ($priv_val <> "1") { ?></a><?php
        } ?></small><br>
<?php
        if ($tt <> 0) { ?>
           <small class="text-muted"><?php
            echo lang('WORKER_last'); ?>:</small>
                <td><small class="text-muted">
                        <?php
            if ($priv_val <> "1") { ?>
                        <a target="_blank" href="userinfo?user=<?php
                echo get_user_hash_by_id($id);
?>">
                            <?php
            } ?>
                <time id="b" datetime="<?php
            echo $lt; ?>"></time>
                <time id="c" datetime="<?php
            echo $lt; ?>"></time>
                            
                            <?php
            if ($priv_val <> "1") { ?></a><?php
            } ?></small>
                            
                            
                                        <?php
        } ?>

                                
                                
                                
                                
                                
                                
                                
                                
                                </div>
                                </div>
                                <br>
                                    
                                    
                                    
                                    
                                    
                                    
                                    <table class="table  ">
            <tbody>

            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_login'); ?>:</small></td>
                <td><small><a href="#" id="edit_login" data-type="text"><?php
        echo $loginf
?></a></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_posada'); ?>:</small></td>
                <td><small><a href="#" id="edit_posada" data-type="select" data-source="<?php
        echo $CONF['hostname']; ?>/inc/json.php?posada" data-pk="1" data-title="<?php
        echo lang('WORKER_posada'); ?>"><?php
        echo $posada
?></a></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_unit'); ?>:</small></td>
                <td><small><a href="#" id="edit_unit" data-type="select" data-source="<?php
        echo $CONF['hostname']; ?>/inc/json.php?units" data-pk="1" data-title="<?php
        echo lang('NEW_to_unit'); ?>"><?php
        echo $pod; ?></a></small></td>
            </tr>

            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_tel'); ?>:</small></td>
                <td><small><a href="#" id="edit_tel" data-type="text"><?php
        echo $tel_user . " " . $tel_ext; ?></a></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_room'); ?>:</small></td>
                <td><small><a href="#" id="edit_adr" data-type="text"><?php
        echo $adr; ?></a></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_mail'); ?>:</small></td>
                <td><small><a href="#" id="edit_mail" data-type="text"><?php
        echo $mails
?></a></small></td>
            </tr>
                        </tbody>
        </table>



<?php
        $stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=1 and user_fields.status=1');
        $stmtf->execute(array(':uid' => $id));
        $resf = $stmtf->fetchAll();


if (!empty($resf)) {
?>
<br>
<center>
<?=lang('FIELD_add_title');?></center>
<table class="table  ">
   <tbody>
<?php



foreach ($resf as $fv) {

    ?>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo $fv['udfn']; ?>:</small></td>
                <td><small><?php
        echo $fv['udf']
?></small></td>
            </tr>
    <?php


}
?>

                 </tbody>
                 </table> 

<?php
}

?>

<?php

        if ($priv_val <> "1") { 
        $stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=0 and user_fields.status=1');
        $stmtf->execute(array(':uid' => $id));
        $resf = $stmtf->fetchAll();


if (!empty($resf)) {
?>
<br>
<table class="table  ">
   <tbody>
<?php



foreach ($resf as $fv) {

    ?>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo $fv['udfn']; ?>:</small></td>
                <td><small><?php
        echo $fv['udf']
?></small></td>
            </tr>
    <?php


}
?>

                 </tbody>
                 </table> 

<?php
}
}
?>


              
                                </div><!-- /.box-body -->
                            </div>
                            
                            
                            
                            



<?php
    }
    
    if ($can_edit == false) {
?>



   <div class="box box-info">
                                <div class="box-header">
                                    <i class="fa fa-user"></i>
                                    <h3 class="box-title"> <?php
        echo lang('WORKER_TITLE'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body" >
                                
                                <div class="row">
                                <div class="col-md-5"><img class="img-rounded" src="<?php
        echo $user_img; ?>" height="120"></div>
                                <div class="col-md-7">
                                <h4><center><strong><?php
        echo $fio_user; ?></strong></center></h4>
                                
                                
                                
                                
                                
                                
                                
                                
                               <small class="text-muted"><?php
        echo lang('WORKER_total'); ?>:</small>
                               <small class="text-muted">
                        <?php
        if ($priv_val <> "1") { ?>
                        <a target="_blank" href="userinfo?user=<?php
            echo get_user_hash_by_id($id);
?>"><?php
        } ?><?php
        echo $tt; ?><?php
        if ($priv_val <> "1") { ?></a><?php
        } ?></small><br>
                        
<?php
        if ($tt <> 0) { ?>
           
           <small class="text-muted"><?php
            echo lang('WORKER_last'); ?>:</small>
           
           <small class="text-muted">
                        <?php
            if ($priv_val <> "1") { ?>
                        <a target="_blank" href="userinfo?user=<?php
                echo get_user_hash_by_id($id);
?>">
                            <?php
            } ?>
                <time id="b" datetime="<?php
            echo $lt; ?>"></time>
                <time id="c" datetime="<?php
            echo $lt; ?>"></time>
                            
                            <?php
            if ($priv_val <> "1") { ?></a><?php
            } ?></small>
                            
            <?php
        } ?>

                                
                                
                                
                                
                                
                                
                                
                                </div>
                                </div>
                                <br>
                                    
                                    
                                    
                                    
                                    
                                    
                                    <table class="table  ">
            <tbody>

            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_login'); ?>:</small></td>
                <td><small><?php
        echo $loginf
?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_posada'); ?>:</small></td>
                <td><small><?php
        echo $posada
?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_unit'); ?>:</small></td>
                <td><small><?php
        echo $pod; ?></small></td>
            </tr>

            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_tel'); ?>:</small></td>
                <td><small><?php
        echo $tel_user . " " . $tel_ext; ?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_room'); ?>:</small></td>
                <td><small><?php
        echo $adr; ?></small></td>
            </tr>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo lang('WORKER_mail'); ?>:</small></td>
                <td><small><?php
        echo $mails
?></small></td>
            </tr>
                        </tbody>
        </table>
                                    
<?php
        $stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=1 and user_fields.status=1');
        $stmtf->execute(array(':uid' => $id));
        $resf = $stmtf->fetchAll();


if (!empty($resf)) {
?>
<br>
<center>
<?=lang('FIELD_add_title');?></center>
<table class="table  ">
   <tbody>
<?php



foreach ($resf as $fv) {

    ?>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo $fv['udfn']; ?>:</small></td>
                <td><small><?php
        echo $fv['udf']
?></small></td>
            </tr>
    <?php


}
?>

                 </tbody>
                 </table> 

<?php
}

?>

<?php

        if ($priv_val <> "1") { 
        $stmtf = $dbConnection->prepare('SELECT user_data.field_val as udf, user_data.field_name as udfn from user_data,user_fields where user_data.field_id=user_fields.id and user_data.user_id=:uid and user_fields.for_client=0 and user_fields.status=1');
        $stmtf->execute(array(':uid' => $id));
        $resf = $stmtf->fetchAll();


if (!empty($resf)) {
?>
<br>
<table class="table  ">
   <tbody>
<?php



foreach ($resf as $fv) {

    ?>
            <tr>
                <td style=" width: 30px; "><small><?php
        echo $fv['udfn']; ?>:</small></td>
                <td><small><?php
        echo $fv['udf']
?></small></td>
            </tr>
    <?php


}
?>

                 </tbody>
                 </table> 

<?php
}
}
?>
                                </div><!-- /.box-body -->
                            </div>
<?php
    }
}

function id_of_user($input) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT id FROM users where login=:input');
    $stmt->execute(array(
        ':input' => $input
    ));
    $id = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return ($id['id']);
}

function priv_status_name($input) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT priv,is_client FROM users where id=:input');
    $stmt->execute(array(
        ':input' => $input
    ));
    $id = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($id['is_client'] == "1") {
        $r = "<strong class=\"text-default\">" . lang('USERS_p_4') . "</strong>";
    } else if ($id['is_client'] == "0") {
        
        switch ($id['priv']) {
            case '2':
                $r = "<strong class=\"text-warning\">" . lang('USERS_nach1') . "</strong>";
                break;

            case '0':
                $r = "<strong class=\"text-success\">" . lang('USERS_nach') . "</strong>";
                break;

            case '1':
                $r = "<strong class=\"text-info\">" . lang('USERS_wo') . "</strong>";
                break;

            default:
                $r = "";
        }
    }
    
    return ($r);
}

function priv_status($input) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT priv FROM users where id=:input');
    $stmt->execute(array(
        ':input' => $input
    ));
    $id = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return ($id['priv']);
}

function get_last_ticket_new($id) {
    global $dbConnection;
    $unit_user = unit_of_user($id);
    $priv_val = priv_status($id);
    $units = explode(",", $unit_user);
    
    $units = implode("', '", $units);
    
    $ee = explode(",", $unit_user);
    foreach ($ee as $key => $value) {
        $in_query = $in_query . ' :val_' . $key . ', ';
    }
    $in_query = substr($in_query, 0, -2);
    foreach ($ee as $key => $value) {
        $vv[":val_" . $key] = $value;
    }
    
    if ($priv_val == "0") {
        
        $stmt = $dbConnection->prepare('SELECT max(last_update) from tickets where unit_id IN (' . $in_query . ') or user_init_id=:id');
        
        $paramss = array(
            ':id' => $id
        );
        $stmt->execute(array_merge($vv, $paramss));
        
        $max = $stmt->fetch(PDO::FETCH_NUM);
        
        $max_id = $max[0];
        
        //echo $max_id;
        
        
    } else if ($priv_val == "1") {
        
        $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((find_in_set(:id,user_to_id)) or (find_in_set(:tid,user_to_id) and unit_id IN (" . $in_query . "))) or user_init_id=:id2");
        
        $paramss = array(
            ':id' => $id,
            ':tid' => '0',
            ':id2' => $id
        );
        $stmt->execute(array_merge($vv, $paramss));
        
        $max = $stmt->fetch(PDO::FETCH_NUM);
        
        $max_id = $max[0];
    } else if ($priv_val == "2") {
        
        $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets;");
        $stmt->execute();
        $max = $stmt->fetch(PDO::FETCH_NUM);
        
        $max_id = $max[0];
    }
    return $max_id;
}

function get_who_last_action_ticket($ticket_id) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('select init_user_id from ticket_log where ticket_id=:ticket_id order by date_op DESC limit 1');
    $stmt->execute(array(
        ':ticket_id' => $ticket_id
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $r = $fio['init_user_id'];
    return $r;
}

function get_last_action_type($ticket_id) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('select date_op, msg, init_user_id, to_user_id, to_unit_id from ticket_log where ticket_id=:ticket_id order by date_op DESC limit 1');
    $stmt->execute(array(
        ':ticket_id' => $ticket_id
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $r = $fio['msg'];
    return $r;
}
function get_last_msg_ticket($ticket_id, $type_op) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('select 
    date_op, 
    msg, 
    init_user_id, 
    to_user_id, 
    to_unit_id 
    from ticket_log where ticket_id=:ticket_id and msg=:top order by date_op DESC limit 1');
    $stmt->execute(array(
        ':ticket_id' => $ticket_id,
        ':top' => $type_op
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $r = $fio['msg'];
    $uss = nameshort(name_of_user_ret_nolink($fio['init_user_id']));
    
    $uss_to = nameshort(name_of_user_ret_nolink($fio['to_user_id']));
    $unit_to = get_unit_name_return4news($fio['to_unit_id']);
    if ($r == 'refer') {
        $red = '<i class=\'fa fa-long-arrow-right\'></i> ' . lang('TICKET_ACTION_refer') . ' <em>' . $uss . '</em> ' . lang('TICKET_ACTION_refer_to') . ' ' . $unit_to . ' ' . $uss_to;
    }
    if ($r == 'ok') {
        $red = '<i class=\'fa fa-check-circle-o\'></i> ' . lang('TICKET_ACTION_ok') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'no_ok') {
        $red = '<i class=\'fa fa-circle-o\'></i> ' . lang('TICKET_ACTION_nook') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'lock') {
        $red = '<i class=\'fa fa-lock\'></i> ' . lang('TICKET_ACTION_lock') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'unlock') {
        $red = '<i class=\'fa fa-unlock\'></i> ' . lang('TICKET_ACTION_unlock') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'create') {
        $red = '<i class=\'fa fa-star-o\'></i> ' . lang('TICKET_ACTION_create') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'edit_msg') {
        $red = '<i class=\'fa fa-pencil-square\'></i> ' . lang('TICKET_ACTION_edit') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'edit_prio') {
        $red = '<i class=\'fa fa-pencil-square\'></i> ' . lang('TICKET_ACTION_edit') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'edit_subj') {
        $red = '<i class=\'fa fa-pencil-square\'></i> ' . lang('TICKET_ACTION_edit') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'comment') {
        $red = '<i class=\'fa fa-comment\'></i> ' . lang('TICKET_ACTION_comment') . ' <em>' . $uss . '</em>';
    }
    if ($r == 'arch') {
        $red = '<i class=\'fa fa-archive\'></i> ' . lang('TICKET_ACTION_arch') . '';
    }
    return $red;
}

function get_last_ticket($menu, $id) {
    global $dbConnection;
    
    if ($menu == "all") {
        $unit_user = unit_of_user($id);
        $priv_val = priv_status($id);
        
        $ee = explode(",", $unit_user);
        foreach ($ee as $key => $value) {
            $in_query = $in_query . ' :val_' . $key . ', ';
        }
        $in_query = substr($in_query, 0, -2);
        foreach ($ee as $key => $value) {
            $vv[":val_" . $key] = $value;
        }
        
        if ($priv_val == "0") {
            
            $stmt = $dbConnection->prepare('SELECT max(last_update) from tickets where unit_id IN (' . $in_query . ') or user_init_id=:id');
            $paramss = array(
                ':id' => $id
            );
            $stmt->execute(array_merge($vv, $paramss));
            $max = $stmt->fetch(PDO::FETCH_NUM);
            $max_id = $max[0];
        } else if ($priv_val == "1") {
            $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where (
            (find_in_set(:id,user_to_id)) or (find_in_set(:tid,user_to_id) and unit_id IN (" . $in_query . "))
            ) or user_init_id=:id2");
            $paramss = array(
                ':id' => $id,
                ':tid' => '0',
                ':id2' => $id
            );
            $stmt->execute(array_merge($vv, $paramss));
            
            $max = $stmt->fetch(PDO::FETCH_NUM);
            $max_id = $max[0];
        } else if ($priv_val == "2") {
            
            $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets");
            $stmt->execute();
            $max = $stmt->fetch(PDO::FETCH_NUM);
            
            $max_id = $max[0];
        }
    } else if ($menu == "in") {
        
        $unit_user = unit_of_user($id);
        $priv_val = priv_status($id);
        $units = explode(",", $unit_user);
        $units = implode("', '", $units);
        $ee = explode(",", $unit_user);
        foreach ($ee as $key => $value) {
            $in_query = $in_query . ' :val_' . $key . ', ';
        }
        $in_query = substr($in_query, 0, -2);
        foreach ($ee as $key => $value) {
            $vv[":val_" . $key] = $value;
        }
        
        if ($priv_val == "0") {
            
            if (isset($_SESSION['hd.rustem_sort_in'])) {
                if ($_SESSION['hd.rustem_sort_in'] == "ok") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (" . $in_query . ") and arch='0' and status=:s");
                    $paramss = array(
                        ':s' => '1'
                    );
                    $stmt->execute(array_merge($vv, $paramss));
                } else if ($_SESSION['hd.rustem_sort_in'] == "free") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (" . $in_query . ") and arch='0' and status=:s and lock_by=:lb");
                    $paramss = array(
                        ':s' => '0',
                        ':lb' => '0'
                    );
                    $stmt->execute(array_merge($vv, $paramss));
                } else if ($_SESSION['hd.rustem_sort_in'] == "ilock") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (" . $in_query . ") and arch='0' and lock_by=:lb");
                    $paramss = array(
                        ':lb' => $id
                    );
                    $stmt->execute(array_merge($vv, $paramss));
                } else if ($_SESSION['hd.rustem_sort_in'] == "lock") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (" . $in_query . ") and arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
                    $paramss = array(
                        ':lb' => $id
                    );
                    $stmt->execute(array_merge($vv, $paramss));
                }
            }
            
            if (!isset($_SESSION['hd.rustem_sort_in'])) {
                $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (" . $in_query . ") and arch='0'");
                
                //$stmt->execute(array(':units' => $units));
                $stmt->execute($vv);
            }
            
            $max = $stmt->fetch(PDO::FETCH_NUM);
            
            $max_id = $max[0];
        } else if ($priv_val == "1") {
            
            if (isset($_SESSION['hd.rustem_sort_in'])) {
                if ($_SESSION['hd.rustem_sort_in'] == "ok") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((find_in_set(:id,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0')) and status=:s");
                    $paramss = array(
                        ':id' => $id,
                        ':s' => '1'
                    );
                    $stmt->execute(array_merge($vv, $paramss));
                    $max = $stmt->fetch(PDO::FETCH_NUM);
                    $max_id = $max[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "free") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((find_in_set(:id,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0')) and lock_by=:lb and status=:s");
                    $paramss = array(
                        ':id' => $id,
                        ':lb' => '0',
                        ':s' => '0'
                    );
                    $stmt->execute(array_merge($vv, $paramss));
                    $max = $stmt->fetch(PDO::FETCH_NUM);
                    $max_id = $max[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "ilock") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((find_in_set(:id,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0')) and lock_by=:lb");
                    $paramss = array(
                        ':id' => $id,
                        ':lb' => $lb
                    );
                    $stmt->execute(array_merge($vv, $paramss));
                    $max = $stmt->fetch(PDO::FETCH_NUM);
                    $max_id = $max[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "lock") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((find_in_set(:id,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0')) and (lock_by<>:lb and lock_by<>0) and (status=0)");
                    $paramss = array(
                        ':id' => $id,
                        ':lb' => $lb
                    );
                    $stmt->execute(array_merge($vv, $paramss));
                    $max = $stmt->fetch(PDO::FETCH_NUM);
                    $max_id = $max[0];
                }
            }
            
            if (!isset($_SESSION['hd.rustem_sort_in'])) {
                $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where ((find_in_set(:id,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0'))");
                $paramss = array(
                    ':id' => $id
                );
                $stmt->execute(array_merge($vv, $paramss));
                $max = $stmt->fetch(PDO::FETCH_NUM);
                $max_id = $max[0];
            }
        } else if ($priv_val == "2") {
            
            if (isset($_SESSION['hd.rustem_sort_in'])) {
                if ($_SESSION['hd.rustem_sort_in'] == "ok") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='0' and status=:s");
                    $stmt->execute(array(
                        ':s' => '1'
                    ));
                    $max = $stmt->fetch(PDO::FETCH_NUM);
                    $max_id = $max[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "free") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='0' and lock_by=:lb and status=:s");
                    $stmt->execute(array(
                        ':lb' => '0',
                        ':s' => '0'
                    ));
                    $max = $stmt->fetch(PDO::FETCH_NUM);
                    $max_id = $max[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "ilock") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='0' and lock_by=:lb");
                    $stmt->execute(array(
                        ':lb' => $id
                    ));
                    $max = $stmt->fetch(PDO::FETCH_NUM);
                    $max_id = $max[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "lock") {
                    $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
                    $stmt->execute(array(
                        ':lb' => $id
                    ));
                    $max = $stmt->fetch(PDO::FETCH_NUM);
                    $max_id = $max[0];
                }
            }
            
            if (!isset($_SESSION['hd.rustem_sort_in'])) {
                
                $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='0'");
                $stmt->execute();
                $max = $stmt->fetch(PDO::FETCH_NUM);
                $max_id = $max[0];
            }
        }
    } else if ($menu == "client") {
        
        $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:cid and arch='0' and client_id=:id");
        $stmt->execute(array(
            ':id' => $id,
            ':cid' => $id
        ));
        $max = $stmt->fetch(PDO::FETCH_NUM);
        $max_id = $max[0];
    } else if ($menu == "out") {
        
        if (isset($_SESSION['hd.rustem_sort_out'])) {
            if ($_SESSION['hd.rustem_sort_out'] == "ok") {
                $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0' and status=:s");
                $stmt->execute(array(
                    ':id' => $id,
                    ':s' => '1'
                ));
                $max = $stmt->fetch(PDO::FETCH_NUM);
                $max_id = $max[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "free") {
                $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0' and lock_by=:lb and status=:s");
                $stmt->execute(array(
                    ':id' => $id,
                    ':lb' => '0',
                    ':s' => '0'
                ));
                $max = $stmt->fetch(PDO::FETCH_NUM);
                $max_id = $max[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "ilock") {
                $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0' and lock_by=:lb");
                $stmt->execute(array(
                    ':id' => $id,
                    ':lb' => $id
                ));
                $max = $stmt->fetch(PDO::FETCH_NUM);
                $max_id = $max[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "lock") {
                $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0'  and (lock_by<>:lb and lock_by<>0) and (status=0)");
                $stmt->execute(array(
                    ':id' => $id,
                    ':lb' => $id
                ));
                $max = $stmt->fetch(PDO::FETCH_NUM);
                $max_id = $max[0];
            }
        }
        
        if (!isset($_SESSION['hd.rustem_sort_out'])) {
            
            $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where user_init_id=:id and arch='0'");
            $stmt->execute(array(
                ':id' => $id
            ));
            $max = $stmt->fetch(PDO::FETCH_NUM);
            $max_id = $max[0];
        }
    } else if ($menu == "arch") {
        
        $unit_user = unit_of_user($id);
        $priv_val = priv_status($id);
        
        $ee = explode(",", $unit_user);
        $s = 1;
        foreach ($ee as $key => $value) {
            $in_query = $in_query . ' :val_' . $key . ', ';
            $s++;
        }
        $c = ($s - 1);
        foreach ($ee as $key => $value) {
            $in_query2 = $in_query2 . ' :val_' . ($c + $key) . ', ';
        }
        $in_query = substr($in_query, 0, -2);
        $in_query2 = substr($in_query2, 0, -2);
        foreach ($ee as $key => $value) {
            $vv[":val_" . $key] = $value;
        }
        foreach ($ee as $key => $value) {
            $vv2[":val_" . ($c + $key) ] = $value;
        }
        
        if ($priv_val == "0") {
            
            $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where unit_id IN (" . $in_query . ") and arch='1'");
            
            //$stmt->execute(array(':units' => $units));
            $stmt->execute($vv);
            $max = $stmt->fetch(PDO::FETCH_NUM);
            
            $max_id = $max[0];
        } else if ($priv_val == "1") {
            
            $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where 
            (find_in_set(:id,user_to_id) and unit_id IN (" . $in_query . ") and arch='1')
             or
            (user_to_id='0' and unit_id IN (" . $in_query2 . ") and arch='1')");
            
            $paramss = array(
                ':id' => $id
            );
            $stmt->execute(array_merge($vv, $vv2, $paramss));
            
            $max = $stmt->fetch(PDO::FETCH_NUM);
            
            $max_id = $max[0];
        } else if ($priv_val == "2") {
            
            $stmt = $dbConnection->prepare("SELECT max(last_update) from tickets where arch='1'");
            $stmt->execute();
            $max = $stmt->fetch(PDO::FETCH_NUM);
            
            $max_id = $max[0];
        }
    }
    
    return $max_id;
}



function get_helper_cat_name($id) {
global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT id, name from helper_cat where id=:p_id');
    $stmt->execute(array(':p_id' => $id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

return $row['name'];
}


function showMenu_helper($level = 0) {
global $dbConnection;

//$result = mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$level); 

    $stmt = $dbConnection->prepare('SELECT id, name from helper_cat where parent_id=:p_id order by sort_id ASC');
    $stmt->execute(array(':p_id' => $level));
    $re = $stmt->fetchAll();



if ($level != 0) { echo "<ul>"; }
    else if ($level == 0) { echo "<ul class=\"todo-list sortable\">"; }

   // while ($node = mysql_fetch_array($result)) { 
    foreach ($re as $row) {
        //echo "<li id=\"list-".$row['id']."\"><div>".$row['name'];
        ?>
                                        <li id="list_<?=$row['id'];?>">
                                            <div>
                                            <!-- drag handle -->
                                            <span class="handle ui-sortable-handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <!-- checkbox -->
                                            
                                            <!-- todo text -->
                                            <span class="text" id="val_<?=$row['id'];?>">
                                        <a href="#" data-pk="<?=$row['id'];?>" data-url="actions.php" id="edit_item" data-type="text" class="">
                                                <?=$row['name'];?>
                                            </a> 
                                            </span>
<small class="text-muted">(<?=count_items_helper($row['id']);?>)</small>
                                            <!-- General tools such as edit or delete-->
                                            <span class="tools">
                                                <!--i class="fa fa-edit" id="edit_item" value="<?=$row['id'];?>"></i-->
                                                <?php if (count_items_helper($row['id']) != 0) {
                                                     echo "<i id=\"del_item_no\"  class=\"fa fa-trash-o\"></i>";
                                                }
                                                else if (count_items_helper($row['id']) == 0) {
                                                    echo "<i id=\"del_item\" value=\"".$row['id']."\" class=\"fa fa-trash-o\"></i>";
                                                }
                                                ?>


                                                
                                            </span>
                                        </div>
                                        
        <?php
        //$hasChild = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$node['ID'])) != null;

        
        $stmt2 = $dbConnection->prepare('SELECT id, name from helper_cat where parent_id=:p_id');
        $stmt2->execute(array(':p_id' => $row['id']));
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        //$hasChild=$row2['parent_id'];
        

        if ($row2) {
            showMenu_helper($row['id']);
        }
        echo "</li>";
    }
echo "</ul>";
}


function count_items_helper($id) {
global $dbConnection;

            $user_id = id_of_user($_SESSION['helpdesk_user_login']);
            $unit_user = unit_of_user($user_id);
            $priv_val = priv_status($user_id);
            
            $units = explode(",", $unit_user);
            array_push($units, "0");




            $is_client = get_user_val('is_client');

if ($is_client == "1") { 


    $res = $dbConnection->prepare('SELECT count(*) from helper where cat_id=:id and client_flag=:cf');
    $res->execute(array(
        ':id' => $id,
        ':cf' =>'1'
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    $res_m=$count[0];
}

else if ($is_client == "0") {

        $stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where cat_id=:id');
        $stmt->execute(array(
        ':id' => $id));
        $result = $stmt->fetchAll();
        $c=0;
                foreach ($result as $row) {
                    
                    $unit2id = explode(",", $row['unit_to_id']);
                    
                    $diff = array_intersect($units, $unit2id);
                    

                    if ($priv_val == 1) {
                        if (($diff) || ($user_id == $row['user_init_id'])) {
                            $ac = "ok";
                        }
                        
                    } else if ($priv_val == 0) {
                        $ac = "ok";
                    } else if ($priv_val == 2) {
                        $ac = "ok";
                    }
                    
                    if ($ac == "ok") {

                        $c++;
                    }
}
$res_m=$c;
}




    return $res_m;


}

function get_max_helper_parent() {
global $dbConnection;
/*
 $res = $dbConnection->prepare('SELECT id from helper_cat where cat_id=:id and client_flag=:cf');
    $res->execute(array(
        ':id' => $id,
        ':cf' =>'1'
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    $res_m=$count[0];
*/
return 0;
}

function show_item_helper_cat($id) {
    global $dbConnection, $CONF;
            //  $t = ($_POST['t']);
            $user_id = id_of_user($_SESSION['helpdesk_user_login']);
            $unit_user = unit_of_user($user_id);
            $priv_val = priv_status($user_id);
            
            $units = explode(",", $unit_user);
            array_push($units, "0");
            
            $is_client = get_user_val('is_client');
            
            if ($is_client == "1") {
                
                $stmt = $dbConnection->prepare("SELECT 
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where cat_id=:id and client_flag=:cf");
                $stmt->execute(array(':id' => $id, ':cf'=>'1'));
                $result = $stmt->fetchAll();
?>
            <div class="box box-solid">
            <div class="box-body">
            <?php
                 if (empty($result)) {
?>
            <div id="" class="well well-large well-transparent lead">
                <center>
                    <?php
        echo lang('MSG_no_records'); ?>
                </center>
            </div>
        <?php
    } else if (!empty($result)) {
                foreach ($result as $row) {
                    
                    
                    
                   
?>

                    <div class="box box-solid">
                                <div class="box-header">
                                    <h5 class="box-title"><small><i class="fa fa-file-text-o"></i></small> <a style="font-size: 18px;" class="text-light-blue" href="helper?h=<?php echo $row['hashname']; ?>"><?php echo $row['title']; ?></a></h5>
                                    <div class="box-tools pull-right">

                                    </div>
                                </div>
                                <div class="box-body">
                                    <small><?php echo cutstr_help_ret(strip_tags($row['message'])); ?>
                            </small>                                </div><!-- /.box-body -->
                            </div>
                <?php
                    
                }
            }
?></div></div> <?php
            } else if ($is_client == "0") {
                
                $stmt = $dbConnection->prepare("SELECT 
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where cat_id=:id");
                $stmt->execute(array(':id' => $id));
                $result = $stmt->fetchAll();
?>
            <div class="box box-solid">
            <div class="box-body">
            <?php
                 if (empty($result)) {
?>
            <div id="" class="well well-large well-transparent lead">
                <center>
                    <?php
        echo lang('MSG_no_records'); ?>
                </center>
            </div>
        <?php
    } else if (!empty($result)) {
                foreach ($result as $row) {
                    
                    $unit2id = explode(",", $row['unit_to_id']);
                    
                    $diff = array_intersect($units, $unit2id);
                    
                    $priv_h = "no";
                    if ($priv_val == 1) {
                        if (($diff) || ($user_id == $row['user_init_id'])) {
                            $ac = "ok";
                        }
                        
                        if ($user_id == $row['user_init_id']) {
                            $priv_h = "yes";
                        }
                    } else if ($priv_val == 0) {
                        $ac = "ok";
                        if ($user_id == $row['user_init_id']) {
                            $priv_h = "yes";
                        }
                    } else if ($priv_val == 2) {
                        $ac = "ok";
                        $priv_h = "yes";
                    }
                    
                    if ($ac == "ok") {
?>

                    <div class="box box-solid">
                                <div class="box-header">
                                    <h5 class="box-title"><small><i class="fa fa-file-text-o"></i></small> <a style="font-size: 18px;" class="text-light-blue" href="helper?h=<?php echo $row['hashname']; ?>"><?php echo $row['title']; ?></a></h5>
                                    <div class="box-tools pull-right">
<small>(<?php echo lang('DASHBOARD_author'); ?>: <?php echo nameshort(name_of_user_ret($row['user_init_id'])); ?>)<?php
                        if ($priv_h == "yes") {
                            echo " 
            <div class=\"btn-group\">
            <a href=\"" . $CONF['hostname']."/helper?h=".$row['hashname'] . "&edit\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-pencil\"></i></a>
            <button id=\"del_helper\" value=\"" . $row['hashname'] . "\"type=\"button\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-trash-o\"></i></button>
            </div>
            ";
                        } ?></small>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <small><?php echo cutstr_help_ret(strip_tags($row['message'])); ?>
                            </small>                                </div><!-- /.box-body -->
                            </div>                <?php
                    }
                } }
?></div></div><?php
            }
}

function show_items_helper($level = 0) {
global $dbConnection;

//$result = mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$level); 

    $stmt = $dbConnection->prepare('SELECT id, name from helper_cat where parent_id=:p_id order by sort_id ASC');
    $stmt->execute(array(':p_id' => $level));
    $re = $stmt->fetchAll();



if ($level != 0) { echo "<ul>"; }
    else if ($level == 0) { echo "<ul>"; }

   // while ($node = mysql_fetch_array($result)) { 
    foreach ($re as $row) {
        //echo "<li id=\"list-".$row['id']."\"><div>".$row['name'];

        ?>
                                        <li id="list-<?=$row['id'];?>">
                                            <div>
                                            
                                            <!-- todo text -->
                                            <span class="text">
                                        <a href="helper?cat=<?=$row['id'];?>" class="">
                                                <?=$row['name'];?> 
                                                <small class="text-muted">(<?=count_items_helper($row['id']);?>)</small>
                                            </a>
                                            </span>

                                            <!-- General tools such as edit or delete-->
                                           
                                        
        <?php
        //$hasChild = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_structure` WHERE `PARENTID` = ".$node['ID'])) != null;

        
        $stmt2 = $dbConnection->prepare('SELECT id, name from helper_cat where parent_id=:p_id');
        $stmt2->execute(array(':p_id' => $row['id']));
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        //$hasChild=$row2['parent_id'];
        

        if ($row2) {
            show_items_helper($row['id']);
        }
        echo "</div></li>";
    }
echo "</ul>";
}




function push_msg_action2user($deliver, $type_op) {
    global $dbConnection, $CONF;
    
    $u_hash = get_user_hash_by_id($deliver);
    $stmt_n = $dbConnection->prepare('insert into notification_msg_pool (delivers_id, type_op, dt) VALUES (:delivers_id, :type_op, :n)');
    $stmt_n->execute(array(
        ':delivers_id' => $deliver,
        ':type_op' => $type_op,
        ':n' => $CONF['now_dt']
    ));
}

function get_unit_stat_create($in, $start, $stop) {
    global $dbConnection;

//узнать всех пользователей отдела
    //для каждого пользователя подсчитать к-во созданных заявок за период
    //прибавить find_in_set(:id,unit)



if (isset($start, $stop)) {
    $stmt = $dbConnection->prepare('SELECT id from users where find_in_set(:uid,unit)');
    $stmt->execute(array(':uid' => $in));
    $res1 = $stmt->fetchAll();
    $cres=0;
    foreach ($res1 as $row) {
        $res = $dbConnection->prepare('SELECT count(*) from ticket_log where init_user_id=:uid and msg=:msg and date_op between :start AND :end');
        $res->execute(array(
        ':uid' => $row['id'], ':start' => $start, ':end' => $stop, ':msg'=>'create'
    ));
            $count = $res->fetch(PDO::FETCH_NUM);
            $cres=$cres+$count[0];
     }
}
    else {

   $stmt = $dbConnection->prepare('SELECT id from users where find_in_set(:uid,unit)');
    $stmt->execute(array(':uid' => $in));
    $res1 = $stmt->fetchAll();
    $cres=0;
    foreach ($res1 as $row) {
        $res = $dbConnection->prepare('SELECT count(*) from ticket_log where init_user_id=:uid and msg=:msg');
        $res->execute(array(
        ':uid' => $row['id'], ':msg'=>'create'
    ));
            $count = $res->fetch(PDO::FETCH_NUM);
            $cres=$cres+$count[0];
     }
}

    return $cres;
}


function get_unit_stat_free($in, $start, $stop) {
    global $dbConnection;

if (isset($start, $stop)) {
    $res = $dbConnection->prepare('SELECT count(*) from tickets where unit_id=:uid AND status=0 and lock_by=0 and last_update between :start AND :end');
    $res->execute(array(
        ':uid' => $in, ':start' => $start, ':end' => $stop
    ));
}
    else {

    $res = $dbConnection->prepare('SELECT count(*) from tickets where unit_id=:uid AND status=0 and lock_by=0');
    $res->execute(array(
        ':uid' => $in
    ));
}
    $count = $res->fetch(PDO::FETCH_NUM);
    return $count[0];
}
function get_unit_stat_lock($in, $start, $stop) {
    global $dbConnection;

if (isset($start, $stop)) {
    $res = $dbConnection->prepare('SELECT count(*) from tickets where unit_id=:uid AND status=0 and lock_by!=0 and last_update between :start AND :end');
    $res->execute(array(
        ':uid' => $in, ':start' => $start, ':end' => $stop
    ));

 }
else {

    $res = $dbConnection->prepare('SELECT count(*) from tickets where unit_id=:uid AND status=0 and lock_by!=0');
    $res->execute(array(
        ':uid' => $in
    ));
}
    $count = $res->fetch(PDO::FETCH_NUM);
    return $count[0];
}
function get_unit_stat_ok($in, $start, $stop) {
    global $dbConnection;

if (isset($start, $stop)) {
        $res = $dbConnection->prepare('SELECT count(*) from tickets where unit_id=:uid AND status=1 and ok_date between :start AND :end');
    $res->execute(array(
        ':uid' => $in, ':start' => $start, ':end' => $stop
    ));
}
else {
    $res = $dbConnection->prepare('SELECT count(*) from tickets where unit_id=:uid AND status=1');
    $res->execute(array(
        ':uid' => $in
    ));
}
    $count = $res->fetch(PDO::FETCH_NUM);
    return $count[0];
}

function get_total_client_tickets_out($in) {
    
    global $dbConnection;
    if (empty($in)) {
        $uid = $_SESSION['helpdesk_user_id'];
    } else if (!empty($in)) {
        $uid = $in;
    }
    
    $res = $dbConnection->prepare('SELECT count(*) from tickets where user_init_id=:uid and client_id=:cid');
    $res->execute(array(
        ':uid' => $uid,
        ':cid' => $uid
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    
    return $count[0];
}

function get_total_tickets_out($in) {
    
    global $dbConnection;
    if (empty($in)) {
        $uid = $_SESSION['helpdesk_user_id'];
    } else if (!empty($in)) {
        $uid = $in;
    }
    
    $res = $dbConnection->prepare('SELECT count(*) from tickets where user_init_id=:uid');
    $res->execute(array(
        ':uid' => $uid
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    
    return $count[0];
}

function get_total_client_tickets_lock($in) {
    global $dbConnection;
    if (empty($in)) {
        $uid = $_SESSION['helpdesk_user_id'];
    } else if (!empty($in)) {
        $uid = $in;
    }
    
    $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:uid and client_id=:cid and lock_by!='0' and status='0'");
    $res->execute(array(
        ':uid' => $uid,
        ':cid' => $uid
    ));
    
    $count = $res->fetch(PDO::FETCH_NUM);
    return $count[0];
}

function get_total_client_tickets_ok($in) {
    global $dbConnection;
    
    if (empty($in)) {
        $uid = $_SESSION['helpdesk_user_id'];
    } else if (!empty($in)) {
        $uid = $in;
    }
    
    $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:uid and client_id=:cid and ok_by!='0' and status='1'");
    $res->execute(array(
        ':uid' => $uid,
        ':cid' => $uid
    ));
    
    $count = $res->fetch(PDO::FETCH_NUM);
    return $count[0];
}

function get_total_tickets_lock($in) {
    global $dbConnection;
    if (empty($in)) {
        $uid = $_SESSION['helpdesk_user_id'];
    } else if (!empty($in)) {
        $uid = $in;
    }
    
    $res = $dbConnection->prepare("SELECT count(*) from tickets where lock_by=:uid and status='0'");
    $res->execute(array(
        ':uid' => $uid
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    return $count[0];
}
function get_total_tickets_ok($in) {
    global $dbConnection;
    if (empty($in)) {
        $uid = $_SESSION['helpdesk_user_id'];
    } else if (!empty($in)) {
        $uid = $in;
    }
    $res = $dbConnection->prepare("SELECT count(*) from tickets where ok_by=:uid");
    $res->execute(array(
        ':uid' => $uid
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    
    return $count[0];
}
function get_total_tickets_out_and_success($in) {
    global $dbConnection;
    




    if (empty($in)) {
        $uid = $_SESSION['helpdesk_user_id'];
    } else if (!empty($in)) {
        $uid = $in;
    }

    $priv_val = priv_status($uid);

if ($priv_val == "2") {

            $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0'");
            $res->execute();

}
else if ($priv_val == "0") {
        $p=get_users_from_units_by_user();
//print_r($p);

        
        //$ee = explode(",", $unit_user);
        
        foreach ($p as $key => $value) {
            $in_query = $in_query . ' :val_' . $key . ', ';
        }
        
        $in_query = substr($in_query, 0, -2);
        foreach ($p as $key => $value) {
            $vv[":val_" . $key] = $value;
        }
            $res = $dbConnection->prepare('SELECT count(*) from tickets where user_init_id IN (' . $in_query . ') and arch=0');
            $res->execute($vv);
}
else if ($priv_val == "1") {
    $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:uid and (ok_by='0') and arch='0'");
    $res->execute(array(
        ':uid' => $uid
    )); 
}







    $count = $res->fetch(PDO::FETCH_NUM);
    
    return $count[0];
}
function get_total_tickets_out_and_lock() {
    global $dbConnection;
    $uid = $_SESSION['helpdesk_user_id'];
    
    $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:uid and (lock_by!='0' and ok_by='0') and arch='0'");
    $res->execute(array(
        ':uid' => $uid
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    
    return $count[0];
}

function get_total_tickets_count() {
    global $dbConnection;
    $uid = $_SESSION['helpdesk_user_id'];
    
    $res = $dbConnection->prepare("SELECT count(*) from tickets");
    $res->execute();
    $count = $res->fetch(PDO::FETCH_NUM);
    
    return $count[0];
}

function get_total_tickets_out_and_ok() {
    global $dbConnection;
    $uid = $_SESSION['helpdesk_user_id'];
    
    $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:uid and (ok_by!='0') and arch='0'");
    $res->execute(array(
        ':uid' => $uid
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    
    return $count[0];
}
function get_total_tickets_free_client() {
    global $dbConnection;
    $res = $dbConnection->prepare("SELECT count(*) from tickets where status='0' and user_init_id='0' and client_id=:uid");
    
    $res->execute(array(
        ':uid' => $_SESSION['helpdesk_user_id']
    ));
    $count = $res->fetch(PDO::FETCH_NUM);
    $count = $count[0];
    
    return $count;
}
function get_total_tickets_free($in) {
    global $dbConnection;
    
    if (empty($in)) {
        $uid = $_SESSION['helpdesk_user_id'];
    } else if (!empty($in)) {
        $uid = $in;
    }
    
    //$uid=$_SESSION['helpdesk_user_id'];
    
    $unit_user = unit_of_user($uid);
    $priv_val = priv_status($uid);
    
    $units = $unit_user;
    
    $in_query = "";
    $unit_user = unit_of_user($uid);
    $ee = explode(",", $unit_user);
    foreach ($ee as $key => $value) {
        $in_query = $in_query . ' :val_' . $key . ', ';
    }
    $in_query = substr($in_query, 0, -2);
    foreach ($ee as $key => $value) {
        $vv[":val_" . $key] = $value;
    }
    
    if ($priv_val == "0") {
        
        $res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (" . $in_query . ") and status='0' and lock_by='0'");
        
        //$res->execute(array(':units' => $units));
        $res->execute($vv);
        $count = $res->fetch(PDO::FETCH_NUM);
        $count = $count[0];
    } else if ($priv_val == "1") {
        
        $res = $dbConnection->prepare("SELECT count(*) from tickets where ((find_in_set(:uid,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0')) and status='0' and lock_by='0'");
        
        //$res->execute(array(':uid' => $uid));
        
        $paramss = array(
            ':uid' => $uid
        );
        $res->execute(array_merge($vv, $paramss));
        $count = $res->fetch(PDO::FETCH_NUM);
        $count = $count[0];
    } else if ($priv_val == "2") {
        
        $res = $dbConnection->prepare("SELECT count(*) from tickets where status='0' and lock_by='0'");
        $res->execute();
        $count = $res->fetch(PDO::FETCH_NUM);
        $count = $count[0];
    }
    
    return $count;
}

function get_dashboard_msg() {
    global $dbConnection;
    $mid = $_SESSION['helpdesk_user_id'];
    
    $stmt = $dbConnection->prepare('SELECT messages from users where id=:mid');
    $stmt->execute(array(
        ':mid' => $mid
    ));
    
    //$max = $stmt->fetch(PDO::FETCH_ASSOC);
    $res1 = $stmt->fetchAll();
    
    if (isset($res1['messages'])) {
        $max_id = $res1['messages'];
    } else {
        $max_id = "";
    }
    
    $length = strlen(utf8_decode($max_id));
    if ($length < 1) {
        $ress = lang('DASHBOARD_def_msg');
    } else {
        $ress = $max_id;
    }
    return $ress;
}
function get_myname() {
    $uid = $_SESSION['helpdesk_user_id'];
    $nu = name_of_user_ret($uid);
    $length = strlen(utf8_decode($nu));
    
    if ($length > 2) {
        $n = explode(" ", name_of_user_ret($uid));
        $t = $n[1] . " " . $n[2];
    } else if ($length <= 2) {
        $t = "";
    }
    
    //$n=explode(" ", name_of_user_ret($uid));
    return $t;
}

function get_total_pages_clients() {
    global $dbConnection;
    $perpage = '15';
    
    $res = $dbConnection->prepare("SELECT count(*) from users where is_client=1 and status!=2");
    $res->execute();
    $count = $res->fetch(PDO::FETCH_NUM);
    $count = $count[0];
    
    if ($count <> 0) {
        $pages_count = ceil($count / $perpage);
        return $pages_count;
    } else {
        $pages_count = 0;
        return $pages_count;
    }
    
    return $count;
}

function get_total_pages_workers() {
    global $dbConnection;
    $perpage = '15';
    
    $res = $dbConnection->prepare("SELECT count(*) from users where status!=2");
    $res->execute();
    $count = $res->fetch(PDO::FETCH_NUM);
    $count = $count[0];
    
    if ($count <> 0) {
        $pages_count = ceil($count / $perpage);
        return $pages_count;
    } else {
        $pages_count = 0;
        return $pages_count;
    }
    
    return $count;
}

function get_approve() {
    global $dbConnection;
    $stmt = $dbConnection->prepare('select count(id) as t1 from approved_info ');
    $stmt->execute();
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $total_ticket['t1'];
}

function get_total_pages($menu, $id) {
    
    global $dbConnection;
    $perpage = '10';
    
    if ($menu == "dashboard") {
        $perpage = '10';
        
        if (isset($_SESSION['hd.rustem_list_in'])) {
            $perpage = $_SESSION['hd.rustem_list_in'];
        }
        
        $unit_user = unit_of_user($id);
        $priv_val = priv_status($id);
        $units = explode(",", $unit_user);
        $units = implode("', '", $units);
        
        $ee = explode(",", $unit_user);
        foreach ($ee as $key => $value) {
            $in_query = $in_query . ' :val_' . $key . ', ';
        }
        $in_query = substr($in_query, 0, -2);
        foreach ($ee as $key => $value) {
            $vv[":val_" . $key] = $value;
        }
        
        if ($priv_val == "0") {
            
            $res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (" . $in_query . ") and arch='0'");
            $res->execute($vv);
            $count = $res->fetch(PDO::FETCH_NUM);
            $count = $count[0];
        } else if ($priv_val == "1") {
            
            $res = $dbConnection->prepare("SELECT count(*) from tickets where ((find_in_set(:id,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0'))");
            
            $paramss = array(
                ':id' => $id
            );
            $res->execute(array_merge($vv, $paramss));
            $count = $res->fetch(PDO::FETCH_NUM);
            $count = $count[0];
        } else if ($priv_val == "2") {
            
            $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0'");
            $res->execute();
            $count = $res->fetch(PDO::FETCH_NUM);
            $count = $count[0];
        }
    }
    
    if ($menu == "in") {
        $perpage = '10';
        
        if (isset($_SESSION['hd.rustem_list_in'])) {
            $perpage = $_SESSION['hd.rustem_list_in'];
        }
        
        $unit_user = unit_of_user($id);
        $priv_val = priv_status($id);
        $units = explode(",", $unit_user);
        $units = implode("', '", $units);
        
        $ee = explode(",", $unit_user);
        foreach ($ee as $key => $value) {
            $in_query = $in_query . ' :val_' . $key . ', ';
        }
        $in_query = substr($in_query, 0, -2);
        foreach ($ee as $key => $value) {
            $vv[":val_" . $key] = $value;
        }
        
        if ($priv_val == "0") {
            
            if (isset($_SESSION['hd.rustem_sort_in'])) {
                if ($_SESSION['hd.rustem_sort_in'] == "ok") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (" . $in_query . ") and arch='0' and status=:s");
                    $paramss = array(
                        ':s' => '1'
                    );
                    $res->execute(array_merge($vv, $paramss));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "free") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (" . $in_query . ") and arch='0' and lock_by=:lb and status=:s");
                    $paramss = array(
                        ':lb' => '0',
                        ':s' => '0'
                    );
                    $res->execute(array_merge($vv, $paramss));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "ilock") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (" . $in_query . ") and arch='0' and lock_by=:lb");
                    $paramss = array(
                        ':lb' => $id
                    );
                    $res->execute(array_merge($vv, $paramss));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "lock") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (" . $in_query . ") and arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
                    $paramss = array(
                        ':lb' => $id
                    );
                    $res->execute(array_merge($vv, $paramss));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                }
            }
            
            if (!isset($_SESSION['hd.rustem_sort_in'])) {
                
                $res = $dbConnection->prepare("SELECT count(*) from tickets where unit_id IN (" . $in_query . ") and arch='0'");
                $res->execute($vv);
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            }
        } else if ($priv_val == "1") {
            if (isset($_SESSION['hd.rustem_sort_in'])) {
                if ($_SESSION['hd.rustem_sort_in'] == "ok") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where ((find_in_set(:id,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0')) and status=:s");
                    
                    $paramss = array(
                        ':id' => $id,
                        ':s' => '1'
                    );
                    $res->execute(array_merge($vv, $paramss));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "free") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where ((find_in_set(:id,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0')) and lock_by=:lb and status=:s");
                    
                    $paramss = array(
                        ':id' => $id,
                        ':lb' => '0',
                        ':s' => '0'
                    );
                    $res->execute(array_merge($vv, $paramss));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "ilock") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where ((find_in_set(:id,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0')) and lock_by=:lb");
                    
                    $paramss = array(
                        ':id' => $id,
                        ':lb' => $id
                    );
                    $res->execute(array_merge($vv, $paramss));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "lock") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where ((find_in_set(:id,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0')) and (lock_by<>:lb and lock_by<>0) and (status=0)");
                    
                    $paramss = array(
                        ':id' => $id,
                        ':lb' => $id
                    );
                    $res->execute(array_merge($vv, $paramss));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                }
            }
            if (!isset($_SESSION['hd.rustem_sort_in'])) {
                $res = $dbConnection->prepare("SELECT count(*) from tickets where ((find_in_set(:id,user_to_id) and arch='0') or (user_to_id='0' and unit_id IN (" . $in_query . ") and arch='0'))");
                
                $paramss = array(
                    ':id' => $id
                );
                $res->execute(array_merge($vv, $paramss));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            }
        } else if ($priv_val == "2") {
            
            if (isset($_SESSION['hd.rustem_sort_in'])) {
                
                if ($_SESSION['hd.rustem_sort_in'] == "ok") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and status=:s");
                    $res->execute(array(
                        ':s' => '1'
                    ));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "free") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and lock_by=:lb and status=:s");
                    $res->execute(array(
                        ':lb' => '0',
                        ':s' => '0'
                    ));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "ilock") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and lock_by=:lb");
                    $res->execute(array(
                        ':lb' => $id
                    ));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                } else if ($_SESSION['hd.rustem_sort_in'] == "lock") {
                    $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
                    $res->execute(array(
                        ':lb' => $id
                    ));
                    $count = $res->fetch(PDO::FETCH_NUM);
                    $count = $count[0];
                }
            }
            if (!isset($_SESSION['hd.rustem_sort_in'])) {
                $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0'");
                $res->execute();
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            }
        }
    }
    
    if ($menu == "clients") {
        $perpage = '10';
        if (isset($_SESSION['hd.rustem_list_out'])) {
            $perpage = $_SESSION['hd.rustem_list_out'];
        }
        
        $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and client_id=:cid");
        $res->execute(array(
            ':id' => $id,
            ':cid' => $id
        ));
        $count = $res->fetch(PDO::FETCH_NUM);
        $count = $count[0];
    }
     //get_total_tickets_out_and_success()
    if ($menu == "out") {
        $perpage = '10';
        if (isset($_SESSION['hd.rustem_list_out'])) {
            $perpage = $_SESSION['hd.rustem_list_out'];
        }
        $unit_user = unit_of_user($id);
        $priv_val = priv_status($id);


        if ($priv_val == "2") { 
        if (isset($_SESSION['hd.rustem_sort_out'])) {
            
            if ($_SESSION['hd.rustem_sort_out'] == "ok") {
                $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and status=:s");
                $res->execute(array(
                    ':s' => '1'
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "free") {
                $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and lock_by=:lb and status=:s");
                $res->execute(array(
                    ':lb' => '0',
                    ':s' => '0'
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "ilock") {
                $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and lock_by=:lb");
                $res->execute(array(
                    ':lb' => $id
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "lock") {
                $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
                $res->execute(array(
                    ':lb' => $id
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            }
        }
        
        if (!isset($_SESSION['hd.rustem_sort_out'])) {
            
            $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='0'");
            $res->execute();
            $count = $res->fetch(PDO::FETCH_NUM);
            $count = $count[0];
        }

        }



                if ($priv_val == "0") { 


$p=get_users_from_units_by_user();
//print_r($p);

        
        //$ee = explode(",", $unit_user);
        
        foreach ($p as $key => $value) {
            $in_query = $in_query . ' :val_' . $key . ', ';
        }
        
        $in_query = substr($in_query, 0, -2);
        foreach ($p as $key => $value) {
            $vv[":val_" . $key] = $value;
        }




        if (isset($_SESSION['hd.rustem_sort_out'])) {
            
            if ($_SESSION['hd.rustem_sort_out'] == "ok") {
                $res = $dbConnection->prepare('SELECT count(*) from tickets where user_init_id IN (' . $in_query . ') and arch=0 and status=:s');
                $paramss=array(':s' => '1');
                $res->execute(array_merge($vv, $paramss));
                    
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "free") {
                $res = $dbConnection->prepare('SELECT count(*) from tickets where user_init_id IN (' . $in_query . ') and  arch=0 and lock_by=:lb and status=:s');
                $paramss=array(':lb' => '0', ':s' => '0');
                $res->execute(array_merge($vv, $paramss));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "ilock") {
                $res = $dbConnection->prepare('SELECT count(*) from tickets where user_init_id IN (' . $in_query . ') and arch=0 and lock_by=:lb');
                $paramss=array(':lb' => $id);
                $res->execute(array_merge($vv, $paramss));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "lock") {
                $res = $dbConnection->prepare('SELECT count(*) from tickets where  user_init_id IN (' . $in_query . ') and  arch=0 and (lock_by<>:lb and lock_by<>0) and (status=0)');
                $paramss=array(':lb' => $id);
                $res->execute(array_merge($vv, $paramss));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            }
        }
        
        if (!isset($_SESSION['hd.rustem_sort_out'])) {
            
            $res = $dbConnection->prepare('SELECT count(*) from tickets where user_init_id IN (' . $in_query . ') and arch=0');
            $res->execute($vv);
            $count = $res->fetch(PDO::FETCH_NUM);
            $count = $count[0];
        }

        }




        else if ($priv_val == "1") { 
        if (isset($_SESSION['hd.rustem_sort_out'])) {
            
            if ($_SESSION['hd.rustem_sort_out'] == "ok") {
                $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and status=:s");
                $res->execute(array(
                    ':id' => $id,
                    ':s' => '1'
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "free") {
                $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and lock_by=:lb and status=:s");
                $res->execute(array(
                    ':id' => $id,
                    ':lb' => '0',
                    ':s' => '0'
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "ilock") {
                $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and lock_by=:lb");
                $res->execute(array(
                    ':id' => $id,
                    ':lb' => $id
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            } else if ($_SESSION['hd.rustem_sort_out'] == "lock") {
                $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0' and (lock_by<>:lb and lock_by<>0) and (status=0)");
                $res->execute(array(
                    ':id' => $id,
                    ':lb' => $id
                ));
                $count = $res->fetch(PDO::FETCH_NUM);
                $count = $count[0];
            }
        }
        
        if (!isset($_SESSION['hd.rustem_sort_out'])) {
            
            $res = $dbConnection->prepare("SELECT count(*) from tickets where user_init_id=:id and arch='0'");
            $res->execute(array(
                ':id' => $id
            ));
            $count = $res->fetch(PDO::FETCH_NUM);
            $count = $count[0];
        }

        }



    }
    if ($menu == "arch") {
        $perpage = '10';
        if (isset($_SESSION['hd.rustem_list_arch'])) {
            $perpage = $_SESSION['hd.rustem_list_arch'];
        }
        
        $unit_user = unit_of_user($id);
        $priv_val = priv_status($id);
        $units = explode(",", $unit_user);
        $units = implode("', '", $units);
        
        $ee = explode(",", $unit_user);
        $s = 1;
        foreach ($ee as $key => $value) {
            $in_query = $in_query . ' :val_' . $key . ', ';
            $s++;
        }
        $c = ($s - 1);
        foreach ($ee as $key => $value) {
            $in_query2 = $in_query2 . ' :val_' . ($c + $key) . ', ';
        }
        $in_query = substr($in_query, 0, -2);
        $in_query2 = substr($in_query2, 0, -2);
        foreach ($ee as $key => $value) {
            $vv[":val_" . $key] = $value;
        }
        foreach ($ee as $key => $value) {
            $vv2[":val_" . ($c + $key) ] = $value;
        }
        
        if ($priv_val == "0") {
            
            $res = $dbConnection->prepare("SELECT count(*) from tickets where (unit_id IN (" . $in_query . ") or user_init_id=:id) and arch='1'");
            
            $paramss = array(
                ':id' => $id
            );
            $res->execute(array_merge($vv, $paramss));
            $count = $res->fetch(PDO::FETCH_NUM);
            $count = $count[0];
        } else if ($priv_val == "1") {
            
            $res = $dbConnection->prepare("SELECT count(*) from tickets
                            where (find_in_set(:id,user_to_id) and unit_id IN (" . $in_query . ") and arch='1') or
                            (user_to_id='0' and unit_id IN (" . $in_query2 . ") and arch='1') or
                            (user_init_id=:id2 and arch='1')");
            
            $paramss = array(
                ':id' => $id,
                ':id2' => $id
            );
            $res->execute(array_merge($vv, $vv2, $paramss));
            $count = $res->fetch(PDO::FETCH_NUM);
            $count = $count[0];
        } else if ($priv_val == "2") {
            
            $res = $dbConnection->prepare("SELECT count(*) from tickets where arch='1'");
            
            $res->execute();
            $count = $res->fetch(PDO::FETCH_NUM);
            $count = $count[0];
        }
    }
    
    if ($count <> 0) {
        $pages_count = ceil($count / $perpage);
        return $pages_count;
    } else {
        $pages_count = 0;
        return $pages_count;
    }
    
    return $count;
}

function get_users_from_units_by_user() {
    global $dbConnection;

    $user_id = id_of_user($_SESSION['helpdesk_user_login']);
    $unit_user = unit_of_user($user_id);
    $unit_user_arr=explode(",", $unit_user);
    


$stmt = $dbConnection->prepare('SELECT id, unit FROM users where status!=2');

$stmt->execute();
$res1 = $stmt->fetchAll();

$users_id=array();
foreach ($res1 as $row) {

    $unit_user_item=$row['unit'];
    $u_r=explode(",", $unit_user_item);

//$unit_user_arr[]
//$u_r[]
    $resultss = array_intersect($unit_user_arr, $u_r);
                        if ($resultss) {

array_push($users_id, $row['id']);

                         }


}
return $users_id;
}

function name_of_client($input) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT fio FROM users where id=:input');
    $stmt->execute(array(
        ':input' => $input
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo ($fio['fio']);
}
function name_of_client_ret($input) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT fio FROM users where id=:input');
    $stmt->execute(array(
        ':input' => $input
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $fio['fio'];
}

function time_ago($in) {
    global $CONF;
    $time = $in;
    $datetime1 = date_create($time);
    $datetime2 = date_create('now', new DateTimeZone($CONF['timezone']));
    $interval = date_diff($datetime1, $datetime2);
    echo $interval->format('%d д %h:%I');
}

function humanTiming_period($time1, $time_ago) {
    
    $time = (strtotime($time_ago) - strtotime($time1));
    
    // to get the time since that moment
    
    return $time;
}

function humanTiming_old($time) {
    
    $time = time() - $time;
    
    return floor($time / 86400);
}

function get_unit_name($input) {
    global $dbConnection;
    
    $u = explode(",", $input);
    
    foreach ($u as $val) {
        $stmt = $dbConnection->prepare('SELECT name FROM deps where id=:val');
        $stmt->execute(array(
            ':val' => $val
        ));
        $dep = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $res.= $dep['name'];
        $res.= "<br>";
    }
    
    echo $res;
}

function name_of_user($input) {
    global $dbConnection;
    
    $u = explode(",", $input);
    foreach ($u as $val) {
        $stmt = $dbConnection->prepare('SELECT fio FROM users where id=:input');
        $stmt->execute(array(
            ':input' => $val
        ));
        $fio = $stmt->fetch(PDO::FETCH_ASSOC);
        $res.= $fio['fio'];
        $res.= "<br>";
    }
    
    echo ($res);
}

function gen_new_api($user_id) {
global $dbConnection;
$message=md5(randomPassword());

            $stmt = $dbConnection->prepare('update users set api_key=:message where id=:noteid');
            $stmt->execute(array(
                ':message' => $message,
                ':noteid' => $user_id
            ));

    return $message;

}

function name_of_user_ret_nolink($input) {
    global $dbConnection;
    
    $u = explode(",", $input);
    $u_count = count($u);
    
    if ($u_count > 1) {
        foreach ($u as $val) {
            $stmt = $dbConnection->prepare('SELECT fio, uniq_id FROM users where id=:input');
            $stmt->execute(array(
                ':input' => $val
            ));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            $res.= $fio['fio'] . ", ";
        }
        $res = substr($res, 0, -2);
    } else if ($u_count <= 1) {
        $stmt = $dbConnection->prepare('SELECT fio, uniq_id FROM users where id=:input');
        $stmt->execute(array(
            ':input' => $input
        ));
        $fio = $stmt->fetch(PDO::FETCH_ASSOC);
        $res.= $fio['fio'];
    }
    return ($res);
}

function name_of_user_ret($input) {
    global $dbConnection;
    
    $u = explode(",", $input);
    $u_count = count($u);
    
    if ($u_count > 1) {
        foreach ($u as $val) {
            $stmt = $dbConnection->prepare('SELECT fio, uniq_id FROM users where id=:input');
            $stmt->execute(array(
                ':input' => $val
            ));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            $res.= "<a href='view_user?" . $fio['uniq_id'] . "'>" . $fio['fio'] . "</a>, ";
        }
        $res = substr($res, 0, -2);
    } else if ($u_count <= 1) {
        $stmt = $dbConnection->prepare('SELECT fio, uniq_id FROM users where id=:input');
        $stmt->execute(array(
            ':input' => $input
        ));
        $fio = $stmt->fetch(PDO::FETCH_ASSOC);
        $res.= "<a href='view_user?" . $fio['uniq_id'] . "'>" . $fio['fio'] . "</a>";
    }
    return ($res);
}

function unit_of_user($input) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT unit FROM users where id=:input');
    $stmt->execute(array(
        ':input' => $input
    ));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return ($fio['unit']);
}

function cutstr_help_ret($input) {
    
    $result = implode(array_slice(explode('<br>', wordwrap($input, 500, '<br>', false)) , 0, 1));
    $r = $result;
    if ($result != $input) $r.= '...';
    return $r;
}

function cutstr_help2_ret($input) {
    
    $result = implode(array_slice(explode('<br>', wordwrap($input, 100, '<br>', false)) , 0, 1));
    $r = $result;
    if ($result != $input) $r.= '...';
    return $r;
}

function cutstr_ret($input) {
    
    $result = implode(array_slice(explode('<br>', wordwrap($input, 30, '<br>', true)) , 0, 1));
    return $result;
    if ($result != $input) return '...';
}

function cutstr_api($input, $c) {
    
    $result = implode(array_slice(explode('<br>', wordwrap($input, $c, '<br>', true)) , 0, 1));
    return $result;
    if ($result != $input) return '...';
}



function cutstr($input) {
    
    $result = implode(array_slice(explode('<br>', wordwrap($input, 51, '<br>', false)) , 0, 1));
    echo $result;
    if ($result != $input) echo '...';
}
function get_date_ok($d_create, $id) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('select date_op from ticket_log where ticket_id=:id and msg=:ok order by date_op DESC');
    $stmt->execute(array(
        ':id' => $id,
        ':ok' => 'ok'
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $tt = $total_ticket['date_op'];
    
    return $tt;
}

function get_ticket_info_source($id) {
     global $dbConnection;


         $stmt = $dbConnection->prepare('select ticket_source from ticket_info where ticket_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $tt = $total_ticket['ticket_source'];
$r="from web";
    if ($tt == "web") { $r="from web"; }
    else if ($tt == "mail") { $r="from e-mail"; }
    else if ($tt == "api") {$r="from api";}

    return $r;

}

function get_ticket_info($id) {
     global $dbConnection;


         $stmt = $dbConnection->prepare('select * from ticket_info where ticket_id=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $os = $total_ticket['os'];
    $ip = $total_ticket['ip'];
    $browser = $total_ticket['browser'];



$r="";


if ($total_ticket['os']) {
    $r="OS: ".$os." / Browser: ".$browser." / IP:".$ip;
}

    return $r;

}


function get_client_ip() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

$user_agent     =   $_SERVER['HTTP_USER_AGENT'];

function getOS() { 

    global $user_agent;

    $os_platform    =   "Unknown OS Platform";

    $os_array       =   array(
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );

    foreach ($os_array as $regex => $value) { 

        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }

    }   

    return $os_platform;

}

function user_browser() {
global $user_agent;
$agent=$user_agent;

    preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $agent, $browser_info);
    list(,$browser,$version) = $browser_info;
    if (preg_match("/Opera ([0-9.]+)/i", $agent, $opera)) return 'Opera '.$opera[1];
    if ($browser == 'MSIE') {
        preg_match("/(Maxthon|Avant Browser|MyIE2)/i", $agent, $ie);
        if ($ie) return $ie[1].' based on IE '.$version;
        return 'IE '.$version;
    }
    if ($browser == 'Firefox') {
        preg_match("/(Flock|Navigator|Epiphany)\/([0-9.]+)/", $agent, $ff);
        if ($ff) return $ff[1].' '.$ff[2];
    }
    if ($browser == 'Opera' && $version == '9.80') return 'Opera '.substr($agent,-5);
    if ($browser == 'Version') return 'Safari '.$version;
    if (!$browser && strpos($agent, 'Gecko')) return 'Browser based on Gecko';
    return $browser.' '.$version;
}
/*
function getBrowser() {

    global $user_agent;

    $browser        =   "Unknown Browser";

    $browser_array  =   array(
                            '/msie/i'       =>  'Internet Explorer',
                            '/firefox/i'    =>  'Firefox',
                            '/safari/i'     =>  'Safari',
                            '/chrome/i'     =>  'Chrome',
                            '/opera/i'      =>  'Opera',
                            '/netscape/i'   =>  'Netscape',
                            '/maxthon/i'    =>  'Maxthon',
                            '/konqueror/i'  =>  'Konqueror',
                            '/mobile/i'     =>  'Handheld Browser'
                        );

    foreach ($browser_array as $regex => $value) { 

        if (preg_match($regex, $user_agent)) {
            $browser    =   $value;
        }

    }





    return $browser;

}

*/







function insert_ticket_info ($t_id, $source) {
global $dbConnection;


$stmt = $dbConnection->prepare('insert into ticket_info (ticket_id, ticket_source, ip, os, browser) values (:ticket_id, :ticket_source, :ip, :os, :browser)');
            $stmt->execute(array(
                ':ticket_id' => $t_id,
                ':ticket_source'=>$source,
                ':ip'=>get_client_ip(),
                ':os'=>getOS(),
                ':browser'=>user_browser()
            ));


}


?>
