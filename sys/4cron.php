<?php
ini_set('max_execution_time', 300);
use EmailReplyParser\Parser\EmailParser;
$base = dirname(dirname(__FILE__));
include ($base . "/conf.php");

//date_default_timezone_set('Europe/Kiev');
include ($base . "/library/ImapMailbox/ImapMailbox.php");
include ($base . '/library/PHPMailer/class.phpmailer.php');
include_once $base . '/lang/lang.ua.php';
include_once $base . '/lang/lang.ru.php';
include_once $base . '/lang/lang.en.php';


include ($base . '/library/autoload.php');

//


function lang($lang, $in) {
    
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


function humanTiming_old($time) {
    
    $time = time() - $time;
    
    return floor($time / 86400);
}
$dbConnection = new PDO('mysql:host=' . $CONF_DB['host'] . ';dbname=' . $CONF_DB['db_name'], $CONF_DB['username'], $CONF_DB['password'], array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
));
$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function get_conf_param($in) {
    global $dbConnection;
    $stmt = $dbConnection->prepare('SELECT value FROM perf where param=:in');
    $stmt->execute(array(
        ':in' => $in
    ));
    $con = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $con['value'];
}

function generateRandomString($length = 5) {
    $characters = '0123456789';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString.= $characters[rand(0, strlen($characters) - 1) ];
    }
    
    return $randomString;
}

$CONF = array(
    'days2arch' => get_conf_param('days2arch') ,
    'time_zone' => get_conf_param('time_zone')
);
$def_timezone = get_conf_param('time_zone');

date_default_timezone_set($def_timezone);
$date_tz = new DateTime();
$date_tz->setTimezone(new DateTimeZone($def_timezone));
$now_date_time = $date_tz->format('Y-m-d H:i:s');

//$time_zone=$CONF['now_dt'];

/*
5 0 * * * /usr/bin/php5 -f /var/www/hd_prod/sys/4cron.php > /var/www/hd_prod/4cron.log 2>&1
*/

$stmt = $dbConnection->prepare('SELECT id, ok_by, ok_date,date_create,user_to_id,unit_id,user_init_id
                            from tickets
                            where arch=:n1 and ok_by !=:n2');
$stmt->execute(array(
    ':n1' => '0',
    ':n2' => '0'
));
$res1 = $stmt->fetchAll();
foreach ($res1 as $row) {
    
    $user_to_id = $row['user_to_id'];
    $unit_to_id = $row['unit_id'];
    $user_init_id = $row['user_init_id'];
    
    $m = $row['id'];
    $td = humanTiming_old(strtotime($row['ok_date']));
    
    if ($td >= $CONF['days2arch']) {
        
        $stmt = $dbConnection->prepare('update tickets set arch=:n1, last_update=:n where id=:m');
        $stmt->execute(array(
            ':n1' => '1',
            ':m' => $m,
            ':n' => $now_date_time
        ));
        
        $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, ticket_id)
values (:arch, :n, :m)');
        $stmt->execute(array(
            ':arch' => 'arch',
            ':m' => $m,
            ':n' => $now_date_time
        ));
        
        $delivers_ids = array();
        array_push($delivers_ids, $user_init_id);
        
        ///////////Исполнителям?///////////////////
        if ($user_to_id == 0) {
            
            //выбрать всех с отдела
            $stmt = $dbConnection->prepare('SELECT id FROM users where find_in_set(:id,unit) and status=:n and is_client=0');
            $stmt->execute(array(
                ':n' => '1',
                ':id' => $unit_to_id
            ));
            $res1 = $stmt->fetchAll();
            
            foreach ($res1 as $qrow) {
                array_push($delivers_ids, $qrow['id']);
            }
        } 
        else if ($user_to_id <> 0) {
            $users = explode(",", $user_to_id);
            foreach ($users as $val) {
                
                //всем исполнителям
                array_push($delivers_ids, $val);
            }
        }
        
        ///////////Исполнителям?///////////////////
        
        //кто прокомментировал - тому не слать
        //SELECT id,init_user_id FROM ticket_log where ticket_id=1 and msg='comment' order by id DESC limit 1
        $stmt = $dbConnection->prepare("SELECT init_user_id FROM ticket_log where ticket_id=:id and msg=:n order by id DESC limit 1");
        $stmt->execute(array(
            ':n' => 'comment',
            ':id' => $m
        ));
        $who_last = $stmt->fetch(PDO::FETCH_NUM);
        $res = $who_last[0];
        
        $delivers_ids = array_unique($delivers_ids);
        if (($key = array_search($res, $delivers_ids)) !== false) {
            unset($delivers_ids[$key]);
        }
        
        $delivers_ids = implode(",", array_unique($delivers_ids));
        
        $stmt = $dbConnection->prepare('insert into news (date_op, msg, init_user_id, target_user, ticket_id) 
                                                           VALUES (:n, :msg, :init_user_id, :target_user,:ticket_id)');
        $stmt->execute(array(
            ':msg' => 'ticket_arch',
            ':init_user_id' => $user_init_id,
            ':target_user' => $delivers_ids,
            ':ticket_id' => $m,
            ':n' => $now_date_time
        ));
    }
}
//################################### MAIL IN #######################################

function check_user_mail($in) {
    
    global $dbConnection;
    
    //$uid = $_SESSION['helpdesk_user_id'];
    
    $stmt = $dbConnection->prepare('SELECT count(email) as n from users where email=:str');
    $stmt->execute(array(
        ':str' => $in
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['n'] > 0) {
        $r = false;
    } 
    else if ($row['n'] == 0) {
        $r = true;
    }
    
    return $r;
}

function get_user_info_by_email($id, $in) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('SELECT ' . $in . ' FROM users where email=:id');
    $stmt->execute(array(
        ':id' => $id
    ));
    
    $fior = $stmt->fetch(PDO::FETCH_NUM);
    
    return $fior[0];
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

function validate_exist_login($str) {
    global $dbConnection;
    
    //$uid = $_SESSION['helpdesk_user_id'];
    
    $stmt = $dbConnection->prepare('SELECT count(login) as n from users where login=:str');
    $stmt->execute(array(
        ':str' => $str
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['n'] > 0) {
        $r = false;
    } 
    else if ($row['n'] == 0) {
        $r = true;
    }
    
    return $r;
}

function generatepassword($length = 8) {
    $characters = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString.= $characters[rand(0, strlen($characters) - 1) ];
    }
    
    return $randomString;
}

function send_mail_reg($to, $subj, $msg) {
    global $dbConnection;
    
    //echo "helo";
    if (get_conf_param('mail_type') == "sendmail") {
        
        $mail = new PHPMailer();
        
        //$mail->SMTPDebug = 1;
        $mail->CharSet = 'UTF-8';
        $mail->IsSendmail();
        
        $mail->AddReplyTo(get_conf_param('mail_from') , get_conf_param('name_of_firm'));
        $mail->AddAddress($to, $to);
        $mail->SetFrom(get_conf_param('mail_from') , get_conf_param('name_of_firm'));
        $mail->Subject = $subj;
        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        $mail->MsgHTML($msg);
        $mail->Send();
    } 
    else if (get_conf_param('mail_type') == "SMTP") {
        
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->IsSMTP();
        $mail->SMTPAuth = get_conf_param('mail_auth');
        
        // enable SMTP authentication
        if (get_conf_param('mail_auth_type') != "none") {
            $mail->SMTPSecure = get_conf_param('mail_auth_type');
        }
        $mail->Host = get_conf_param('mail_host');
        $mail->Port = get_conf_param('mail_port');
        $mail->Username = get_conf_param('mail_username');
        $mail->Password = get_conf_param('mail_password');
        
        $mail->AddReplyTo(get_conf_param('mail_from') , get_conf_param('name_of_firm'));
        $mail->AddAddress($to, $to);
        $mail->SetFrom(get_conf_param('mail_from') , get_conf_param('name_of_firm'));
        $mail->Subject = $subj;
        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        
        // optional - MsgHTML will create an alternate automatically
        $mail->MsgHTML($msg);
        $mail->Send();
    }
}

function get_user_hash_by_id($in) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('select uniq_id from users where id=:in');
    $stmt->execute(array(
        ':in' => $in
    ));
    $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $tt = $total_ticket['uniq_id'];
    return $tt;
}

function send_notification($ticket_id) {
    global $dbConnection, $now_date_time;
    
    $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
    $stmt->execute(array(
        ':tid' => $ticket_id
    ));
    $res_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $user_to_id = $res_ticket['user_to_id'];
    $unit_to_id = $res_ticket['unit_id'];
    $user_init_id = $res_ticket['user_init_id'];
    
    $type = "ticket_create";
    if ($user_to_id == 0) {
        
        //отправка всему отделу
        /* выбрать всех пользователей у кого статус активен и отдел равен N и в БД записать: id пользователей,$type,$ticket_id */
        
        $stmt = $dbConnection->prepare('SELECT id FROM users where find_in_set(:id,unit) and status=:n and is_client=0');
        $stmt->execute(array(
            ':n' => '1',
            ':id' => $unit_to_id
        ));
        $res1 = $stmt->fetchAll();
        $delivers_ids = array();
        foreach ($res1 as $qrow) {
            array_push($delivers_ids, $qrow['id']);
        }
        
        if (($key = array_search($user_init_id, $delivers_ids)) !== false) {
            unset($delivers_ids[$key]);
        }
        
        /*  ADD TO notification_msg_pool: uniq_id  */
        
        foreach ($delivers_ids as $uniq_id_row) {
            
            $u_hash = get_user_hash_by_id($uniq_id_row);
            $stmt_n = $dbConnection->prepare('insert into notification_msg_pool (delivers_id, type_op, ticket_id, dt) VALUES (:delivers_id, :type_op, :tid, :n)');
            $stmt_n->execute(array(
                ':delivers_id' => $u_hash,
                ':type_op' => 'ticket_create',
                ':tid' => $ticket_id,
                ':n' => $now_date_time
            ));
        }
        
        $res_str = implode(",", $delivers_ids);
        
        $stmt = $dbConnection->prepare('insert into news (date_op, msg, init_user_id, target_user, ticket_id) 
                                                           VALUES (:n, :msg, :init_user_id, :target_user,:ticket_id)');
        $stmt->execute(array(
            ':msg' => $type,
            ':init_user_id' => $user_init_id,
            ':target_user' => $res_str,
            ':ticket_id' => $ticket_id,
            ':n' => $now_date_time
        ));
        
        $stmt = $dbConnection->prepare('insert into notification_pool (delivers_id, type_op, ticket_id, dt) VALUES (:delivers_id, :type_op, :tid, :n)');
        $stmt->execute(array(
            ':delivers_id' => $res_str,
            ':type_op' => $type,
            ':tid' => $ticket_id,
            ':n' => $now_date_time
        ));
    } 
    else if ($user_to_id <> 0) {
        
        $su = array();
        $users = explode(",", $user_to_id);
        foreach ($users as $val) {
            $stmt = $dbConnection->prepare('SELECT unit FROM users where id=:n');
            $stmt->execute(array(
                ':n' => $val
            ));
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $qrow) {
                $user_units = $qrow['unit'];
                $res_str = explode(",", $user_units);
                foreach ($res_str as $vals) {
                    $stmt2 = $dbConnection->prepare('SELECT id FROM users where find_in_set(:id,unit) and (priv=2 OR priv=0) and is_client=0');
                    $stmt2->execute(array(
                        ':id' => $vals
                    ));
                    $res2 = $stmt2->fetchAll();
                    foreach ($res2 as $qrow2) {
                        array_push($su, $qrow2['id']);
                    }
                }
            }
        }
        
        // array_merge($users,$su);
        $nr = array();
        $nr = array_unique(array_merge($users, $su));
        if (($key = array_search($user_init_id, $nr)) !== false) {
            unset($nr[$key]);
        }
        
        /*  ADD TO notification_msg_pool: uniq_id  */
        
        foreach ($nr as $uniq_id_row) {
            
            $u_hash = get_user_hash_by_id($uniq_id_row);
            $stmt_n = $dbConnection->prepare('insert into notification_msg_pool (delivers_id, type_op, ticket_id, dt) VALUES (:delivers_id, :type_op, :tid, :n)');
            $stmt_n->execute(array(
                ':delivers_id' => $u_hash,
                ':type_op' => 'ticket_create',
                ':tid' => $ticket_id,
                ':n' => $now_date_time
            ));
        }
        
        $su = implode(",", $nr);
        
        if ($su) {
            
            $stmt = $dbConnection->prepare('insert into news (date_op, msg, init_user_id, target_user, ticket_id) 
                                                           VALUES (:n, :msg, :init_user_id, :target_user,:ticket_id)');
            $stmt->execute(array(
                ':msg' => $type,
                ':init_user_id' => $user_init_id,
                ':target_user' => $su,
                ':ticket_id' => $ticket_id,
                ':n' => $now_date_time
            ));
            
            $stmt = $dbConnection->prepare('insert into notification_pool (delivers_id, type_op, ticket_id, dt) VALUES (:delivers_id, :type_op, :tid, :n)');
            $stmt->execute(array(
                ':delivers_id' => $su,
                ':type_op' => $type,
                ':tid' => $ticket_id,
                ':n' => $now_date_time
            ));
        }
    }
}

function create_new_client($mail, $fio) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM users");
    $stmt->execute();
    $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);
    
    $max_user_id = $max_id_ticket[0] + 1;
    
    $pass = generatepassword();
    $hn = md5(time());
    
    $login_pre = explode('@', $mail);
    $login_pre2 = $login_pre[0];
    
    $errors = false;
    
    if (validate_exist_login($login_pre2) == false) {
        
        //$errors = true;
        //$el = lang('ticket_login_error') . "<br>";
        $login_pre2 = $login_pre2 . "_" . rand(0, 100);
    }
    
    //if (validate_email())
    
    $stmt = $dbConnection->prepare('insert into users 
             (id,
             fio, 
             login, 
             email, 
             priv,
             is_client,
             uniq_id,
             status,
             pass) 
             VALUES         
             (
             :id,
             :client_fio, 
             :client_login,   
             :client_mail, 
             :priv,
             :is_client,
             :uniq_id,
             :status,
             :pass)');
    
    $stmt->execute(array(
        ':id' => $max_user_id,
        ':client_fio' => $fio,
        ':client_login' => $login_pre2,
        ':client_mail' => $mail,
        ':priv' => '1',
        ':is_client' => '1',
        ':uniq_id' => $hn,
        ':status' => '1',
        ':pass' => md5($pass)
    ));
    
    $subject = get_conf_param('name_of_firm') . " - registration successfull";
    $message = <<<EOBODY
<div style="background: #ffffff; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
<p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">REGISTRATION INFORMATION!</p>

<br />
<table width="100%" cellspacing="0" cellpadding="3" style="">
  
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;">Login:</td>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;">{$login_pre2}</td>
  </tr>
  <tr>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;">Password:</td>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;">{$pass}</td>
  </tr>
   
 
</table>
</center>

</div>
EOBODY;
    
    send_mail_reg($mail, $subject, $message);
    
    return $max_user_id;
}
function get_client_ip() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP']) $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if ($_SERVER['HTTP_X_FORWARDED_FOR']) $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if ($_SERVER['HTTP_X_FORWARDED']) $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if ($_SERVER['HTTP_FORWARDED_FOR']) $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if ($_SERVER['HTTP_FORWARDED']) $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if ($_SERVER['REMOTE_ADDR']) $ipaddress = $_SERVER['REMOTE_ADDR'];
    else $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

$user_agent = $_SERVER['HTTP_USER_AGENT'];

function getOS() {
    
    global $user_agent;
    
    $os_platform = "Unknown OS Platform";
    
    $os_array = array(
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );
    
    foreach ($os_array as $regex => $value) {
        
        if (preg_match($regex, $user_agent)) {
            $os_platform = $value;
        }
    }
    
    return $os_platform;
}

function getBrowser() {
    
    global $user_agent;
    
    $browser = "Unknown Browser";
    
    $browser_array = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    );
    
    foreach ($browser_array as $regex => $value) {
        
        if (preg_match($regex, $user_agent)) {
            $browser = $value;
        }
    }
    
    return $browser;
}

function insert_ticket_info($t_id, $source) {
    global $dbConnection;
    
    $stmt = $dbConnection->prepare('insert into ticket_info (ticket_id, ticket_source, ip, os, browser) values (:ticket_id, :ticket_source, :ip, :os, :browser)');
    $stmt->execute(array(
        ':ticket_id' => $t_id,
        ':ticket_source' => $source,
        ':ip' => get_client_ip() ,
        ':os' => getOS() ,
        ':browser' => getBrowser()
    ));
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


function send_notification_ticket_comment($tid_comment) {
    global $dbConnection, $now_date_time;

    $zenlix_session_id = "api";

$stmt_notificate = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
    $stmt_notificate->execute(array(
        ':tid' => $tid_comment
    ));
    $res_ticket = $stmt_notificate->fetch(PDO::FETCH_ASSOC);
    
    $user_to_id = $res_ticket['user_to_id'];
    $unit_to_id = $res_ticket['unit_id'];
    $user_init_id = $res_ticket['user_init_id'];
        // отправить письмо автору заявки, исполнителям, всех кто есть в комментариях
        // $user_init_id, $user_to_id,
        // узнать
        $delivers_ids = array();
        
        $stmt = $dbConnection->prepare('SELECT init_user_id FROM ticket_log where ticket_id=:id and msg=:n');
        $stmt->execute(array(
            ':n' => 'comment',
            ':id' => $tid_comment
        ));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $qrow) {
            
            //всем кто в комментах есть
            array_push($delivers_ids, $qrow['init_user_id']);
        }
        
        //автору заявки
        array_push($delivers_ids, $user_init_id);
        
        ///////////Исполнителям?///////////////////
        if ($user_to_id == 0) {
            
            //выбрать всех с отдела
            $stmt = $dbConnection->prepare('SELECT id FROM users where find_in_set(:id,unit) and status=:n and is_client=0');
            $stmt->execute(array(
                ':n' => '1',
                ':id' => $unit_to_id
            ));
            $res1 = $stmt->fetchAll();
            
            foreach ($res1 as $qrow) {
                array_push($delivers_ids, $qrow['id']);
            }
        } 
        else if ($user_to_id <> 0) {
            $users = explode(",", $user_to_id);
            foreach ($users as $val) {
                
                //всем исполнителям
                array_push($delivers_ids, $val);
            }
        }
        
        ///////////Исполнителям?///////////////////
        
        //кто прокомментировал - тому не слать
        //SELECT id,init_user_id FROM ticket_log where ticket_id=1 and msg='comment' order by id DESC limit 1
        $stmt = $dbConnection->prepare("SELECT init_user_id FROM ticket_log where ticket_id=:id and msg=:n order by id DESC limit 1");
        $stmt->execute(array(
            ':n' => 'comment',
            ':id' => $tid_comment
        ));
        $who_last = $stmt->fetch(PDO::FETCH_NUM);
        $res = $who_last[0];
        
        $delivers_ids = array_unique($delivers_ids);
        $del_nodes = $delivers_ids;
        if (($key = array_search($res, $delivers_ids)) !== false) {
            unset($delivers_ids[$key]);
        }
        
        $init_user_h = get_user_hash_by_id($res);
        
        foreach ($del_nodes as $uniq_id_row) {
            
            $u_hash = get_user_hash_by_id($uniq_id_row);
            $stmt_n = $dbConnection->prepare('insert into notification_msg_pool (delivers_id, type_op, ticket_id, dt, session_id, user_init) VALUES (:delivers_id, :type_op, :tid, :n, :s, :ui)');
            $stmt_n->execute(array(
                ':delivers_id' => $u_hash,
                ':type_op' => 'ticket_comment',
                ':tid' => $tid_comment,
                ':n' => $now_date_time,
                ':s' => $zenlix_session_id,
                ':ui' => $init_user_h
            ));
        }
        
        $delivers_ids = implode(",", array_unique($delivers_ids));
        
        $stmt_log = $dbConnection->prepare('SELECT init_user_id FROM ticket_log where ticket_id=:tid and msg=:msg order by ID desc limit 1');
        $stmt_log->execute(array(
            ':tid' => $tid_comment,
            ':msg' => 'comment'
        ));
        $ticket_log_res = $stmt_log->fetch(PDO::FETCH_ASSOC);
        $who_init = $ticket_log_res['init_user_id'];
        
        $stmt = $dbConnection->prepare('insert into news (date_op, msg, init_user_id, target_user, ticket_id) 
                                                           VALUES (:n, :msg, :init_user_id, :target_user,:ticket_id)');
        $stmt->execute(array(
            ':msg' => 'ticket_comment',
            ':init_user_id' => $who_init,
            ':target_user' => $delivers_ids,
            ':ticket_id' => $tid_comment,
            ':n' => $now_date_time
        ));
        
        $stmt = $dbConnection->prepare('insert into notification_pool (delivers_id, type_op, ticket_id, dt) VALUES (:delivers_id, :type_op, :tid, :n)');
        $stmt->execute(array(
            ':delivers_id' => $delivers_ids,
            ':type_op' => 'ticket_comment',
            ':tid' => $tid_comment,
            ':n' => $now_date_time
        ));



}

function check_file($fp) {
    
    
            $maxsize=get_conf_param('file_size');
            $good_files = explode("|", get_conf_param('file_types'));
            $acceptable = $good_files;
            $ext = pathinfo($fp, PATHINFO_EXTENSION);
    $flag = true;
    
    
    
    if (filesize($fp) > $maxsize) {
                        $flag = false;
    }
    
    if (!in_array($ext, $acceptable))
    {
       // if (!empty(mime_content_type($fp))) {
            $flag = false;
       // }
    }
    
    
    if ($flag == false) {
        unlink($fp);
    } 
    
    return $flag;
}

function add_file($hn, $name, $path) {
    global $dbConnection;
    
    $path_parts = pathinfo($path);
    $fhash = $path_parts['filename'];
    
    //$path
    $filetype = mime_content_type($path);
    $filesize = filesize($path);
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    
    //$fileName_norm = $fhash.".".$ext;
    
    $stmt = $dbConnection->prepare('insert into files 
        (ticket_hash, original_name, file_hash, file_type, file_size, file_ext) values 
        (:ticket_hash, :original_name, :file_hash, :file_type, :file_size, :file_ext)');
    $stmt->execute(array(
        ':ticket_hash' => $hn,
        ':original_name' => $name,
        ':file_hash' => $fhash,
        ':file_type' => $filetype,
        ':file_size' => $filesize,
        ':file_ext' => $ext
    ));
}

if (get_conf_param('email_gate_status') == "true") {
    
    define('GMAIL_EMAIL', get_conf_param('email_gate_login'));
    define('GMAIL_PASSWORD', get_conf_param('email_gate_pass'));
    define('ATTACHMENTS_DIR', $base . '/upload_files');
    
    /*
    
    /pop3
    
    /imap/ssl
    
    /pop3/ssl/novalidate-cert
    
    /nntp
    
    
    Optional flags for names
    Flag    Description
    /service=service    mailbox access service, default is "imap"
    /user=user  remote user name for login on the server
    /authuser=user  remote authentication user; if specified this is the user name whose password is used (e.g. administrator)
    /anonymous  remote access as anonymous user
    /debug  record protocol telemetry in application's debug log
    /secure do not transmit a plaintext password over the network
    /imap, /imap2, /imap2bis, /imap4, /imap4rev1    equivalent to /service=imap
    /pop3   equivalent to /service=pop3
    /nntp   equivalent to /service=nntp
    /norsh  do not use rsh or ssh to establish a preauthenticated IMAP session
    /ssl    use the Secure Socket Layer to encrypt the session
    /validate-cert  validate certificates from TLS/SSL server (this is the default behavior)
    /novalidate-cert    do not validate certificates from TLS/SSL server, needed if server uses self-signed certificates
    /tls    force use of start-TLS to encrypt the session, and reject connection to servers that do not support it
    /notls  do not do start-TLS to encrypt the session, even with servers that support it
    /readonly   request read-only mailbox open (IMAP only; ignored on NNTP, and an error with SMTP and POP3)
    
    */
    
    $mailbox = new ImapMailbox('{' . get_conf_param('email_gate_host') . ':' . get_conf_param('email_gate_port') . get_conf_param('email_gate_connect_param') . '}' . get_conf_param('email_gate_cat') . '', GMAIL_EMAIL, GMAIL_PASSWORD, ATTACHMENTS_DIR);
    
    $mails = array();
    
    //
    
    // Get some mail
    $mailsIds = $mailbox->searchMailBox(get_conf_param('email_gate_filter'));
    
    $max_msg = 50;
    $i = 0;
    foreach ($mailsIds as $id) {
        if ($i > $max_msg) break;

        
        $i++;
        
        $message = $mailbox->getMail($id);
        
        $attachments=$message->getAttachments();
        //$vv=$message->m_id;
        
        /*
         echo "<code><pre>";
            print_r($message);
            echo "</pre></code><hr><hr>";
                        echo "<code><pre>";
            print_r($message->textHtml);
            echo "</pre></code><hr><hr>";
        */
        
        
        
        /*
        echo "MID:".$message->m_id."<br>";
        echo "ref:".$message->references."<br>";
        echo "rto:".$message->in_reply_to."<br>";
        */
        
        $mref = $message->references;
        $mid_code = $message->m_id;
        
        //echo "<pre>".print_r($message->getAttachments())."</pre>";
        
        //to code place
        
        // $message is now an object containing the parsed message, allowing us to easily access the subject, header components, body and any attachments
        //echo "This message has subject {$message->subject} and was sent from {$message->fromAddress} on {$message->date}<br><br>";
        
        /*
        
        Проверить - есть ли такой пользователь в базе
        
        */
        
        /*
        $message->subject
        $message->id
        $message->date
        $message->fromName
        $message->fromAddress
        $message->toString
        $message->textPlain
        
            $attachments->filePath
            $attachments->name
        */
        
        if (check_user_mail($message->fromAddress) == false) {
            
            //есть такой пользователь
            
            $user_init_id = get_user_info_by_email($message->fromAddress, 'id');
            $client_id_param = get_user_info_by_email($message->fromAddress, 'id');
            
            if (get_conf_param('email_gate_unit_id')) {
                $unit_id = get_conf_param('email_gate_unit_id');
            } 
            else if (!get_conf_param('email_gate_unit_id')) {
                $unit_id = '0';
            }
            
            $user_to_id = '0';
            if (get_conf_param('email_gate_user_id')) {
                if (get_conf_param('email_gate_user_id') != "Null") {
                    $user_to_id = get_conf_param('email_gate_user_id');
                }
            }
            
            //echo $message->all;
 

$m_tp=$message->textPlain;
$m_th=$message->textHtml;
$m_ta=$message->all;


if (base64_decode($m_tp, true)) {
    // is valid
    $m_tp=$message->textPlain;
} else {
    // not valid
    $m_tp=$message->textPlain;
}


if (base64_decode($m_th, true)) {
    // is valid
    $m_th=$message->textHtml;
} else {
    // not valid
    $m_th=$message->textHtml;
}


if (base64_decode($m_ta, true)) {
    // is valid
    $m_ta=$message->all;
} else {
    // not valid
    $m_ta=$message->all;
}



            $subj = strip_tags($message->subject);
            $msg = strip_tags($m_tp);
            

if (empty($m_tp)) {
    
    if (empty($m_th)) { $msg=strip_tags($m_ta); }
    else { $msg=strip_tags($m_th); }
    
    
    
}

/*
            echo "<code><pre>";
            print_r($message->textPlain);
            echo "</pre></code><hr><hr>";
                        echo "<code><pre>";
            print_r($email->getFragments());
            echo "</pre></code><hr><hr>";
            */
//print_r($subj);
if (preg_match('/(#[0-9]+)/',$subj)) {

//$msg = strip_tags($message->all);

$email = (new EmailParser())->parse($msg);
$fragment = current($email->getFragments());



$replyTextMsg=$fragment->getContent();

//echo lang('en', 'REPLY_INFORMATION_YES');


$replyTextMsg = array_shift(explode(lang('en', 'REPLY_INFORMATION_YES'), $replyTextMsg));
$replyTextMsg = array_shift(explode(lang('ru', 'REPLY_INFORMATION_YES'), $replyTextMsg));
$replyTextMsg = array_shift(explode(lang('ua', 'REPLY_INFORMATION_YES'), $replyTextMsg));

$v=preg_match('/(#[0-9]+)/',$subj, $mt);

$ticketfrommail_id=substr($mt[0], 1);





$user_comment = $user_init_id;
$tid_comment = $ticketfrommail_id;
$text_comment = $replyTextMsg;
/*
if (strpos($text_comment,'-- \n') !== false) {
    $text_comment=substr($text_comment, 0, strpos($text_comment,'-- \n'));
}
if (strpos($text_comment,'--\n') !== false) {
    $text_comment=substr($text_comment, 0, strpos($text_comment,'--\n'));
}
*/
/*
Lines that equal '-- \n' (standard email sig delimiter)
Lines that equal '--\n' (people often forget the space in sig delimiter; and this is not that common outside sigs)
Lines that begin with '-----Original Message-----' (MS Outlook default)
Lines that begin with '________________________________' (32 underscores, Outlook again)
Lines that begin with 'On ' and end with ' wrote:\n' (OS X Mail.app default)
Lines that begin with 'From: ' (failsafe four Outlook and some other reply formats)
Lines that begin with 'Sent from my iPhone'
Lines that begin with 'Sent from my BlackBerry'
*/






$at_arr=array();
            foreach($attachments as $attachment) {
                
                //print check_file($attachment->filePath)."->".$attachment->filePath."<br>";
                
            if (check_file($attachment->filePath)) {
                
                $ext = pathinfo($attachment->filePath, PATHINFO_EXTENSION);
                $fhash = randomhash();
                $fileName_norm = $fhash . "." . $ext;
                
                $filetype=mime_content_type($attachment->filePath);
                $filesize=filesize($attachment->filePath);
                
                //echo $attachment->filePath." ==> ".$base . '/upload_files/'.$fileName_norm;
                rename($attachment->filePath, $base . '/upload_files/'.$fileName_norm);
                $fname=$attachment->name;
                $stmt = $dbConnection->prepare('insert into files 
        (original_name, file_hash, file_type, file_size, file_ext, obj_type) values 
        (:original_name, :file_hash, :file_type, :file_size, :file_ext, :obj_type)');
                        $stmt->execute(array(
                            ':original_name' => $fname,
                            ':file_hash' => $fhash,
                            ':file_type' => $filetype,
                            ':file_size' => $filesize,
                            ':file_ext' => $ext,
                            ':obj_type' => '1'
                        ));
                array_push($at_arr, $fhash);
            }
            
            
            
            
            
            
            
            }

//print_r($at_arr);
//$comma_separated = implode(",", $at_arr);
if (!empty($at_arr)) {
    $si=implode(",", $at_arr);
$text_comment=$text_comment."<br> [file:".$si."]";
}
//echo $text_comment;


$stmt = $dbConnection->prepare('INSERT INTO comments (t_id, user_id, comment_text, dt)
values (:tid_comment, :user_comment, :text_comment, :n)');
            $stmt->execute(array(
                ':tid_comment' => $tid_comment,
                ':user_comment' => $user_comment,
                ':text_comment' => $text_comment,
                ':n' => $now_date_time
            ));
            
            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:comment, :n, :user_comment, :tid_comment)');
            $stmt->execute(array(
                ':tid_comment' => $tid_comment,
                ':user_comment' => $user_comment,
                ':comment' => 'comment',
                ':n' => $now_date_time
            ));
            
            send_notification_ticket_comment($tid_comment);











            
            //}
            
            $stmt = $dbConnection->prepare('update tickets set last_update=:n where id=:tid_comment');
            $stmt->execute(array(
                ':tid_comment' => $tid_comment,
                ':n' => $now_date_time
            ));

}

else {
    

            

           
            
            
            
            
            $status = '0';
            $hashname = md5(time()) . generateRandomString();
            $prio = '1';
            
            
            
            
            foreach($attachments as $attachment) {
                
                //print check_file($attachment->filePath)."->".$attachment->filePath."<br>";
                
            if (check_file($attachment->filePath)) {
                
                $ext = pathinfo($attachment->filePath, PATHINFO_EXTENSION);
                $fhash = randomhash();
                $fileName_norm = $fhash . "." . $ext;
                
                $filetype=mime_content_type($attachment->filePath);
                $filesize=filesize($attachment->filePath);
                
                //echo $attachment->filePath." ==> ".$base . '/upload_files/'.$fileName_norm;
                rename($attachment->filePath, $base . '/upload_files/'.$fileName_norm);
                $fname=$attachment->name;
                $stmt = $dbConnection->prepare('insert into files 
        (ticket_hash, original_name, file_hash, file_type, file_size, file_ext, obj_type) values 
        (:ticket_hash, :original_name, :file_hash, :file_type, :file_size, :file_ext, :obj_type)');
                        $stmt->execute(array(
                            ':ticket_hash' => $hashname,
                            ':original_name' => $fname,
                            ':file_hash' => $fhash,
                            ':file_type' => $filetype,
                            ':file_size' => $filesize,
                            ':file_ext' => $ext,
                            ':obj_type' => '1'
                        ));
                
            }
            }
            
            
            
            
            
            
            $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
            $stmt->execute();
            $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);
            
            $max_id_res_ticket = $max_id_ticket[0] + 1;
            
            $stmt = $dbConnection->prepare('INSERT INTO tickets
                                (id, user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio, last_update, deadline_time) VALUES (:max_id_res_ticket, :user_init_id, :user_to_id, :n,:subj, :msg,:max_id,:unit_id, :status, :hashname, :prio, :nz, :deadline_time)');
            $stmt->execute(array(
                ':max_id_res_ticket' => $max_id_res_ticket,
                ':user_init_id' => $user_init_id,
                ':user_to_id' => $user_to_id,
                ':subj' => $subj,
                ':msg' => $msg,
                ':max_id' => $client_id_param,
                ':unit_id' => $unit_id,
                ':status' => $status,
                ':hashname' => $hashname,
                ':prio' => $prio,
                ':n' => $now_date_time,
                ':nz' => $now_date_time,
                ':deadline_time' => NULL
            ));
            
            $ft = get_conf_param('file_types');
            
            $ag = explode("|", $ft);

            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, :n, :unow, :max_id_res_ticket, :user_to_id, :unit_id)');
            
            $stmt->execute(array(
                ':create' => 'create',
                ':unow' => $user_init_id,
                ':max_id_res_ticket' => $max_id_res_ticket,
                ':user_to_id' => $user_to_id,
                ':unit_id' => $unit_id,
                ':n' => $now_date_time
            ));
            
            //echo("dd");
            //if ($CONF_MAIL['active'] == "true") {
            send_notification($max_id_res_ticket);
            insert_ticket_info($max_id_res_ticket, 'mail');
        }
        } 
        else if (check_user_mail($message->fromAddress) == true) {
            
            //пользователя нет такого
            
            if (get_conf_param('email_gate_all') == "true") {
                
                //создать пользователя
                //направить письмо пользователю
                //создать заявку
                
                $user_id = create_new_client($message->fromAddress, $message->fromName);
                
                $user_init_id = $user_id;
                $client_id_param = $user_id;
                
                if (get_conf_param('email_gate_unit_id')) {
                    $unit_id = get_conf_param('email_gate_unit_id');
                } 
                else if (!get_conf_param('email_gate_unit_id')) {
                    $unit_id = '0';
                }
                
                $user_to_id = '0';
                if (get_conf_param('email_gate_user_id')) {
                    if (get_conf_param('email_gate_user_id') != "Null") {
                        $user_to_id = get_conf_param('email_gate_user_id');
                    }
                }
                
                $subj = strip_tags(xss_clean($message->subject));
                $msg = strip_tags(xss_clean($message->all));
                
                $status = '0';
                $hashname = md5(time().randomhash());
                $prio = '1';
                
                
                
                
                            foreach($attachments as $attachment) {
                
                //print check_file($attachment->filePath)."->".$attachment->filePath."<br>";
                
            if (check_file($attachment->filePath)) {
                
                $ext = pathinfo($attachment->filePath, PATHINFO_EXTENSION);
                $fhash = randomhash();
                $fileName_norm = $fhash . "." . $ext;
                
                $filetype=mime_content_type($attachment->filePath);
                $filesize=filesize($attachment->filePath);
                
                //echo $attachment->filePath." ==> ".$base . '/upload_files/'.$fileName_norm;
                rename($attachment->filePath, $base . '/upload_files/'.$fileName_norm);
                $fname=$attachment->name;
                $stmt = $dbConnection->prepare('insert into files 
        (ticket_hash, original_name, file_hash, file_type, file_size, file_ext, obj_type) values 
        (:ticket_hash, :original_name, :file_hash, :file_type, :file_size, :file_ext, :obj_type)');
                        $stmt->execute(array(
                            ':ticket_hash' => $hashname,
                            ':original_name' => $fname,
                            ':file_hash' => $fhash,
                            ':file_type' => $filetype,
                            ':file_size' => $filesize,
                            ':file_ext' => $ext,
                            ':obj_type' => '1'
                        ));
                
            }
            }
                
                
                
                
                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
                $stmt->execute();
                $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);
                
                $max_id_res_ticket = $max_id_ticket[0] + 1;
                
                $stmt = $dbConnection->prepare('INSERT INTO tickets
                                (id, user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio, last_update, deadline_time) VALUES (:max_id_res_ticket, :user_init_id, :user_to_id, :n,:subj, :msg,:max_id,:unit_id, :status, :hashname, :prio, :nz, :deadline_time)');
                $stmt->execute(array(
                    ':max_id_res_ticket' => $max_id_res_ticket,
                    ':user_init_id' => $user_init_id,
                    ':user_to_id' => $user_to_id,
                    ':subj' => $subj,
                    ':msg' => $msg,
                    ':max_id' => $client_id_param,
                    ':unit_id' => $unit_id,
                    ':status' => $status,
                    ':hashname' => $hashname,
                    ':prio' => $prio,
                    ':n' => $now_date_time,
                    ':nz' => $now_date_time,
                    ':deadline_time' => NULL
                ));
                
                /*
                
                foreach ($attachments as $attachment) {
                // Array of IncomingMailAttachment objects
                //       echo $attachment->filePath;
                //$attachment->name;
                add_file($hashname, $attachment->name, $attachment->filePath);
                
                }
                */
                
                //$unow=$user_init_id;
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, :n, :unow, :max_id_res_ticket, :user_to_id, :unit_id)');
                
                $stmt->execute(array(
                    ':create' => 'create',
                    ':unow' => $user_init_id,
                    ':max_id_res_ticket' => $max_id_res_ticket,
                    ':user_to_id' => $user_to_id,
                    ':unit_id' => $unit_id,
                    ':n' => $now_date_time
                ));
                
                //echo("dd");
                //if ($CONF_MAIL['active'] == "true") {
                send_notification($max_id_res_ticket);
                insert_ticket_info($max_id_res_ticket, 'mail');
            } 
            else if (get_conf_param('email_gate_all') == "false") {
                
                //не создавать пользователя
                
                
            }
        }
    }
}
?>
