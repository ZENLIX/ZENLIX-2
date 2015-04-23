<?php

ini_set('max_execution_time', 300);

$base = dirname(dirname(__FILE__)); 
include($base ."/conf.php");
//include_once($base ."/functions.inc.php");
date_default_timezone_set('Europe/Kiev');
include($base .'/sys/class.phpmailer.php');

include($base .'/integration/PushBullet.class.php');

include_once $base.'/lang/lang.ua.php';
include_once $base.'/lang/lang.ru.php';
include_once $base.'/lang/lang.en.php';

//include_once($base .'/inc/notification.inc.php');

$dbConnection = new PDO(
    'mysql:host='.$CONF_DB['host'].';dbname='.$CONF_DB['db_name'],
    $CONF_DB['username'],
    $CONF_DB['password'],
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
);
$dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

include($base .'/library/smsc_smpp.php');

/*
кому?

получатель будет получать весь лог только тому кому надо

*/



function make_device_push($type_op, $usr_lang, $usr_id, $ticket_id)
{
  global $dbConnection,$base,$CONF;



$MAIL_new=lang($lang,'MAIL_new');
$MAIL_refer=lang($lang,'mail_msg_ticket_refer');
$MAIL_refer_ext=lang($lang,'mail_msg_ticket_refer_ext');
$MAIL_to_w=lang($lang,'mail_msg_ticket_to_ext');

$MAIL_msg_comment=lang($lang,'mail_msg_ticket_comment');
$MAIL_msg_comment_ext=lang($lang,'mail_msg_ticket_comment_ext');


$MAIL_msg_lock=lang($lang,'mail_msg_ticket_lock');
$MAIL_msg_lock_ext=lang($lang,'mail_msg_ticket_lock_ext');
$MAIL_msg_unlock=lang($lang,'mail_msg_ticket_unlock');
$MAIL_msg_unlock_ext=lang($lang,'mail_msg_ticket_unlock_ext');
$MAIL_msg_ok=lang($lang,'mail_msg_ticket_ok');
$MAIL_msg_ok_ext=lang($lang,'mail_msg_ticket_ok_ext');
$MAIL_msg_no_ok=lang($lang,'mail_msg_ticket_no_ok');
$MAIL_msg_no_ok_ext=lang($lang,'mail_msg_ticket_no_ok_ext');

        $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $ticket_res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $h=$ticket_res['hash_name'];
        $user_init_id=$ticket_res['user_init_id'];
        $uin=name_of_user_ret($user_init_id);//????? IF CLIENT /////

        $nou=name_of_client_ret($ticket_res['client_id']);
        $to_id=$ticket_res['user_to_id'];
        $s=$ticket_res['subj'];
        $m=$ticket_res['msg'];
        $unit_id=$ticket_res['unit_id'];
        //кому?
        if ($ticket_res['user_to_id'] <> 0 ) {
            $to_text="".name_of_user_ret($to_id)."";
        }
        else if ($ticket_res['user_to_id'] == 0 ) {
            $to_text=view_array(get_unit_name_return($unit_id));
        }
        

$stmt = $dbConnection->prepare('SELECT device_token from user_devices where user_id=:uid');
    $stmt->execute(array(':uid' => $usr_id));
    $res1 = $stmt->fetchAll();

foreach ($res1 as $value) {
  # code...

if ($type_op == "ticket_create") {

$msg=lang($lang,'MAIL_new').' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";

$content[] = array(
'device_token'=>$value,
'msg'=>$msg
  );


}
else if ($type_op == "ticket_refer") {
$msg=$MAIL_refer.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
  $content[] = array(
'device_token'=>$value,
'msg'=>$msg
  );
}
else if ($type_op == "ticket_comment") {
$msg=$MAIL_msg_comment.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
  $content[] = array(
'device_token'=>$value,
'msg'=>$msg
  );
}
else if ($type_op == "ticket_lock") {
$msg=$MAIL_msg_lock.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
  $content[] = array(
'device_token'=>$value,
'msg'=>$msg
  );
}
else if ($type_op == "ticket_unlock") {
$msg=$MAIL_msg_unlock.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
  $content[] = array(
'device_token'=>$value,
'msg'=>$msg
  );
}
else if ($type_op == "ticket_ok") {
$msg=$MAIL_msg_ok.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
  $content[] = array(
'device_token'=>$value,
'msg'=>$msg
  );
}
else if ($type_op == "ticket_no_ok") {
$msg=$MAIL_msg_no_ok.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
  $content[] = array(
'device_token'=>$value,
'msg'=>$msg
  );
  
}  


/*
$content[] = array(

  );
  */

$url = "http://api.zenlix.com/api.php";    
//$content = $results;

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER,
        array("Content-type: application/json"));
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$json_response = curl_exec($curl);

$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ( $status != 201 ) {
    die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
}


curl_close($curl);

//$response = json_decode($json_response, true);


}


}



function send_smsc($type_op, $lang, $user_mail, $ticket_id) {
  global $dbConnection,$base,$CONF;




$MAIL_new=lang($lang,'MAIL_new');
$MAIL_refer=lang($lang,'mail_msg_ticket_refer');
$MAIL_refer_ext=lang($lang,'mail_msg_ticket_refer_ext');
$MAIL_to_w=lang($lang,'mail_msg_ticket_to_ext');

$MAIL_msg_comment=lang($lang,'mail_msg_ticket_comment');
$MAIL_msg_comment_ext=lang($lang,'mail_msg_ticket_comment_ext');


$MAIL_msg_lock=lang($lang,'mail_msg_ticket_lock');
$MAIL_msg_lock_ext=lang($lang,'mail_msg_ticket_lock_ext');
$MAIL_msg_unlock=lang($lang,'mail_msg_ticket_unlock');
$MAIL_msg_unlock_ext=lang($lang,'mail_msg_ticket_unlock_ext');
$MAIL_msg_ok=lang($lang,'mail_msg_ticket_ok');
$MAIL_msg_ok_ext=lang($lang,'mail_msg_ticket_ok_ext');
$MAIL_msg_no_ok=lang($lang,'mail_msg_ticket_no_ok');
$MAIL_msg_no_ok_ext=lang($lang,'mail_msg_ticket_no_ok_ext');

        $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $ticket_res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $h=$ticket_res['hash_name'];
        $user_init_id=$ticket_res['user_init_id'];
        $uin=name_of_user_ret($user_init_id);//????? IF CLIENT /////

        $nou=name_of_client_ret($ticket_res['client_id']);
        $to_id=$ticket_res['user_to_id'];
        $s=$ticket_res['subj'];
        $m=$ticket_res['msg'];
        $unit_id=$ticket_res['unit_id'];
        //кому?
        if ($ticket_res['user_to_id'] <> 0 ) {
            $to_text="".name_of_user_ret($to_id)."";
        }
        else if ($ticket_res['user_to_id'] == 0 ) {
            $to_text=view_array(get_unit_name_return($unit_id));
        }
        
        
        
        

if ($type_op == "ticket_create") {
$msg=lang($lang,'MAIL_new').' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";

$ar_list=explode(",", get_conf_param('smsc_list_action'));
if (in_array($type_op, $ar_list)) {
  if (check_notify_sms_user($type_op, $user_mail))
{

send_sms($user_mail, $msg, 1);

}
}



 }
else if ($type_op == "ticket_refer") {




$msg=$MAIL_refer.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";

$ar_list=explode(",", get_conf_param('smsc_list_action'));
if (in_array($type_op, $ar_list)) {
    if (check_notify_sms_user($type_op, $user_mail))
{
send_sms($user_mail, $msg, 1);
}
}



 }
else if ($type_op == "ticket_comment") {

$msg=$MAIL_msg_comment.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";

$ar_list=explode(",", get_conf_param('smsc_list_action'));
if (in_array($type_op, $ar_list)) {
    if (check_notify_sms_user($type_op, $user_mail))
{
send_sms($user_mail, $msg, 1);
}
}
 }
else if ($type_op == "ticket_lock") {


$msg=$MAIL_msg_lock.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";

$ar_list=explode(",", get_conf_param('smsc_list_action'));
if (in_array($type_op, $ar_list)) {
    if (check_notify_sms_user($type_op, $user_mail))
{
send_sms($user_mail, $msg, 1);
}
}


 }
else if ($type_op == "ticket_unlock") {

$msg=$MAIL_msg_unlock.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";

$ar_list=explode(",", get_conf_param('smsc_list_action'));
if (in_array($type_op, $ar_list)) {
    if (check_notify_sms_user($type_op, $user_mail))
{
send_sms($user_mail, $msg, 1);
}
}

 }
else if ($type_op == "ticket_ok") {

$msg=$MAIL_msg_ok.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";

$ar_list=explode(",", get_conf_param('smsc_list_action'));
if (in_array($type_op, $ar_list)) {
    if (check_notify_sms_user($type_op, $user_mail))
{
send_sms($user_mail, $msg, 1);
}
}


 }
else if ($type_op == "ticket_no_ok") {

$msg=$MAIL_msg_no_ok.' #'.$ticket_id."\r\n";
$msg.=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";

$ar_list=explode(",", get_conf_param('smsc_list_action'));
if (in_array($type_op, $ar_list)) {
    if (check_notify_sms_user($type_op, $user_mail))
{
send_sms($user_mail, $msg, 1);
}
}



 }




}






function send_pushbullet($type_op, $lang, $user_mail, $ticket_id) {
  global $dbConnection,$base,$CONF;
  

$MAIL_new=lang($lang,'MAIL_new');
$MAIL_refer=lang($lang,'mail_msg_ticket_refer');
$MAIL_refer_ext=lang($lang,'mail_msg_ticket_refer_ext');
$MAIL_to_w=lang($lang,'mail_msg_ticket_to_ext');

$MAIL_msg_comment=lang($lang,'mail_msg_ticket_comment');
$MAIL_msg_comment_ext=lang($lang,'mail_msg_ticket_comment_ext');


$MAIL_msg_lock=lang($lang,'mail_msg_ticket_lock');
$MAIL_msg_lock_ext=lang($lang,'mail_msg_ticket_lock_ext');
$MAIL_msg_unlock=lang($lang,'mail_msg_ticket_unlock');
$MAIL_msg_unlock_ext=lang($lang,'mail_msg_ticket_unlock_ext');
$MAIL_msg_ok=lang($lang,'mail_msg_ticket_ok');
$MAIL_msg_ok_ext=lang($lang,'mail_msg_ticket_ok_ext');
$MAIL_msg_no_ok=lang($lang,'mail_msg_ticket_no_ok');
$MAIL_msg_no_ok_ext=lang($lang,'mail_msg_ticket_no_ok_ext');

        $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $ticket_res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $h=$ticket_res['hash_name'];
        $user_init_id=$ticket_res['user_init_id'];
        $uin=name_of_user_ret($user_init_id);//????? IF CLIENT /////

        $nou=name_of_client_ret($ticket_res['client_id']);
        $to_id=$ticket_res['user_to_id'];
        $s=$ticket_res['subj'];
        $m=$ticket_res['msg'];
        $unit_id=$ticket_res['unit_id'];
        //кому?
        if ($ticket_res['user_to_id'] <> 0 ) {
            $to_text="".name_of_user_ret($to_id)."";
        }
        else if ($ticket_res['user_to_id'] == 0 ) {
            $to_text=view_array(get_unit_name_return($unit_id));
        }
        
        
        
        

if ($type_op == "ticket_create") {
$tn=lang($lang,'TICKET_name').' #'.$ticket_id." (".$MAIL_new.")";
$msg=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
$msg.=lang($lang,'MAIL_to').": ".$to_text."\r\n";
$msg.=lang($lang,'MAIL_worker').": ".$nou."\r\n";
$msg.=lang($lang,'MAIL_msg').": ".$m."\r\n";

  try {
  $p = new PushBullet(get_conf_param('pb_api'));
  //email, title, msg
  $p->pushNote($user_mail, $tn, $msg);
  } catch (PushBulletException $e) {
  die($e->getMessage());
}


 }
else if ($type_op == "ticket_refer") {
$tn=lang($lang,'TICKET_name').' #'.$ticket_id." (".$MAIL_refer.")";
$msg=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
$msg.=lang($lang,'MAIL_to').": ".$to_text."\r\n";
$msg.=lang($lang,'MAIL_worker').": ".$nou."\r\n";
$msg.=lang($lang,'MAIL_msg').": ".$m."\r\n";

  try {
  $p = new PushBullet(get_conf_param('pb_api'));
  //email, title, msg
  $p->pushNote($user_mail, $tn, $msg);
  } catch (PushBulletException $e) {
  die($e->getMessage());
}

 }
else if ($type_op == "ticket_comment") {
$tn=lang($lang,'TICKET_name').' #'.$ticket_id." (".$MAIL_msg_comment.")";
$msg=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
$msg.=lang($lang,'MAIL_to').": ".$to_text."\r\n";
$msg.=lang($lang,'MAIL_worker').": ".$nou."\r\n";
$msg.=lang($lang,'MAIL_msg').": ".$m."\r\n";

  try {
  $p = new PushBullet(get_conf_param('pb_api'));
  //email, title, msg
  $p->pushNote($user_mail, $tn, $msg);
  } catch (PushBulletException $e) {
  die($e->getMessage());
}

 }
else if ($type_op == "ticket_lock") {

$tn=lang($lang,'TICKET_name').' #'.$ticket_id." (".$MAIL_msg_lock.")";
$msg=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
$msg.=lang($lang,'MAIL_to').": ".$to_text."\r\n";
$msg.=lang($lang,'MAIL_worker').": ".$nou."\r\n";
$msg.=lang($lang,'MAIL_msg').": ".$m."\r\n";

  try {
  $p = new PushBullet(get_conf_param('pb_api'));
  //email, title, msg
  $p->pushNote($user_mail, $tn, $msg);
  } catch (PushBulletException $e) {
  die($e->getMessage());
}


 }
else if ($type_op == "ticket_unlock") {
$tn=lang($lang,'TICKET_name').' #'.$ticket_id." (".$MAIL_msg_unlock.")";
$msg=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
$msg.=lang($lang,'MAIL_to').": ".$to_text."\r\n";
$msg.=lang($lang,'MAIL_worker').": ".$nou."\r\n";
$msg.=lang($lang,'MAIL_msg').": ".$m."\r\n";

  try {
  $p = new PushBullet(get_conf_param('pb_api'));
  //email, title, msg
  $p->pushNote($user_mail, $tn, $msg);
  } catch (PushBulletException $e) {
  die($e->getMessage());
}



 }
else if ($type_op == "ticket_ok") {
$tn=lang($lang,'TICKET_name').' #'.$ticket_id." (".$MAIL_msg_ok.")";
$msg=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
$msg.=lang($lang,'MAIL_to').": ".$to_text."\r\n";
$msg.=lang($lang,'MAIL_worker').": ".$nou."\r\n";
$msg.=lang($lang,'MAIL_msg').": ".$m."\r\n";

  try {
  $p = new PushBullet(get_conf_param('pb_api'));
  //email, title, msg
  $p->pushNote($user_mail, $tn, $msg);
  } catch (PushBulletException $e) {
  die($e->getMessage());
}


 }
else if ($type_op == "ticket_no_ok") {
$tn=lang($lang,'TICKET_name').' #'.$ticket_id." (".$MAIL_msg_no_ok.")";
$msg=lang($lang,'MAIL_subj').": ".$s."\r\n";
$msg.=lang($lang,'MAIL_created').": ".$uin."\r\n";
$msg.=lang($lang,'MAIL_to').": ".$to_text."\r\n";
$msg.=lang($lang,'MAIL_worker').": ".$nou."\r\n";
$msg.=lang($lang,'MAIL_msg').": ".$m."\r\n";

  try {
  $p = new PushBullet(get_conf_param('pb_api'));
  //email, title, msg
  $p->pushNote($user_mail, $tn, $msg);
  } catch (PushBulletException $e) {
  die($e->getMessage());
}



 }













}





function check_user_devices($id) {
global $dbConnection, $CONF;

$stmt = $dbConnection->prepare('SELECT device_token from user_devices where user_id=:uid');
    $stmt->execute(array(':uid' => $id));
    $res1 = $stmt->fetchAll();

if (!empty($res1)) {
return true;
}
if (empty($res1)) {
    return false;
}

    //foreach ($res1 as $row) {}






}




function get_conf_param($in) {
    global $dbConnection;
    
    
    $stmt = $dbConnection->prepare('SELECT value FROM perf where param=:in');
    $stmt->execute(array(':in' => $in));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);

return $fio['value'];

}
function get_unit_name_return($input) {
    global $dbConnection;

    $u=explode(",", $input);
    $res=array();
    foreach ($u as $val) {

        $stmt = $dbConnection->prepare('SELECT name FROM deps where id=:val');
        $stmt->execute(array(':val' => $val));
        $dep = $stmt->fetch(PDO::FETCH_ASSOC);

    
    array_push($res, $dep['name']);
        //$res.=$dep['name'];
        //$res.="<br>";
    }

    return $res;
}


$def_timezone = get_conf_param('time_zone');

date_default_timezone_set($def_timezone);
$date_tz = new DateTime();
$date_tz->setTimezone(new DateTimeZone($def_timezone));
$now_date_time = $date_tz->format('Y-m-d H:i:s');


$CONF = array (
  'title_header'  => get_conf_param('title_header'),
  'hostname'    => 'http://'.get_conf_param('hostname'),
  'mail'      => get_conf_param('mail'),
  'days2arch'   => get_conf_param('days2arch'),
  'name_of_firm'  => get_conf_param('name_of_firm'),
  'fix_subj'    => get_conf_param('fix_subj'),
  'first_login' => get_conf_param('first_login'),
  'file_uploads'  => get_conf_param('file_uploads'),
  'file_types'  => '('.get_conf_param('file_types').')',
  'file_size'   => get_conf_param('file_size'),
  'now_dt' => $now_date_time
  );
$CONF_MAIL = array (
  'active'  => get_conf_param('mail_active'),
  'host'    => get_conf_param('mail_host'),
  'port'    => get_conf_param('mail_port'),
  'auth'    => get_conf_param('mail_auth'),
  'auth_type' => get_conf_param('mail_auth_type'),
  'username'  => get_conf_param('mail_username'),
  'password'  => get_conf_param('mail_password'),
  'from'    => get_conf_param('mail_from'),
  'debug'   => 'false'
);





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










function send_mail($to,$subj,$msg, $msg_id) {
  global $CONF, $CONF_MAIL, $dbConnection;
  
  
$v=parse_url("http://".get_conf_param('hostname'));

if(!isset($msg_id)) {
  $msg_id=md5(time());
}


  //echo "helo";
  if (get_conf_param('mail_type') == "sendmail") {
  
  $mail = new PHPMailer();
  //$mail->SMTPDebug = 1;
  $mail->CharSet    = 'UTF-8';
  $mail->IsSendmail();

  $mail->AddReplyTo($CONF_MAIL['from'], $CONF['name_of_firm']);
  $mail->AddAddress($to, $to);
$mail->MessageID = $msg_id."@".$v['host'];
  $mail->SetFrom($CONF_MAIL['from'], $CONF['name_of_firm']);
  $mail->Subject = $subj;
  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; 
  $mail->MsgHTML($msg);
  $mail->Send();

}
else if (get_conf_param('mail_type') == "SMTP") {
  
  
  $mail = new PHPMailer();
  $mail->CharSet    = 'UTF-8';
  $mail->IsSMTP();
  $mail->SMTPAuth   = $CONF_MAIL['auth'];                  // enable SMTP authentication
if (get_conf_param('mail_auth_type') != "none") 
  { 
    $mail->SMTPSecure = $CONF_MAIL['auth_type'];
  }
$mail->Host       = $CONF_MAIL['host']; 
$mail->Port       = $CONF_MAIL['port'];                  
$mail->Username   = $CONF_MAIL['username'];
$mail->Password   = $CONF_MAIL['password'];   
  //$mail->set('Message-ID', '008');
  //$mail->addCustomHeader("Message-ID: 008");
$mail->MessageID = $msg_id."@".$v['host'];


  $mail->AddReplyTo($CONF_MAIL['from'], $CONF['name_of_firm']);
  $mail->AddAddress($to, $to);
  $mail->SetFrom($CONF_MAIL['from'], $CONF['name_of_firm']);
  $mail->Subject = $subj;
  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
  $mail->MsgHTML($msg);
  $mail->Send();


  
  
}
}



function name_of_user_ret($input) {
    global $dbConnection;

$u=explode(",", $input);
$u_count=count($u);

if ($u_count > 1) {
  $res="";
foreach ($u as $val) {
    $stmt = $dbConnection->prepare('SELECT fio FROM users where id=:input');
    $stmt->execute(array(':input' => $val));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
$res.=$fio['fio'].", ";


}
$res=substr($res, 0, -2);
}
else if ($u_count <= 1) {
  $stmt = $dbConnection->prepare('SELECT fio FROM users where id=:input');
    $stmt->execute(array(':input' => $input));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);
    $res=$fio['fio'];
}
    return($res);
}


function lang($lang, $in) {
  
  
  switch ($lang) {
    case 'ua':
        $res=lang_ua($in);
        break;

    case 'ru':
        $res=lang_ru($in);
        break;

    case 'en':
        $res=lang_en($in);
        break;

    default:
        $res=lang_en($in);
}
  
  return $res;
}


function view_array($in) {
$end_element = array_pop($in);
$res="";
foreach ($in as $value) {
   // делаем что-либо с каждым элементом
        $res.=$value;
        $res.="<br>";
}
$res.=$end_element;
   // делаем что-либо с последним элементом $end_element
  
  return $res;
}
function name_of_client_ret($input) {
    global $dbConnection;

    $stmt = $dbConnection->prepare('SELECT fio FROM users where id=:input');
    $stmt->execute(array(':input' => $input));
    $fio = $stmt->fetch(PDO::FETCH_ASSOC);

    return $fio['fio'];

}








function make_mail($type_op, $lang, $user_mail, $ticket_id) {
  global $dbConnection,$base,$CONF;
  




/*
ticket_create
ticket_refer
ticket_comment
ticket_lock
ticket_unlock
ticket_ok
ticket_no_ok
*/
$MAIL_new=lang($lang,'MAIL_new');
$MAIL_code=lang($lang,'MAIL_code');
$MAIL_2link=lang($lang,'MAIL_2link');
$MAIL_info=lang($lang,'MAIL_info');
$MAIL_created=lang($lang,'MAIL_created');
$MAIL_to=lang($lang,'MAIL_to');
$MAIL_prio=lang($lang,'MAIL_prio');
$MAIL_worker=lang($lang,'MAIL_worker');
$MAIL_msg=lang($lang,'MAIL_msg');
$MAIL_subj=lang($lang,'MAIL_subj');
$MAIL_text=lang($lang,'MAIL_text');

$MAIL_refer=lang($lang,'mail_msg_ticket_refer');
$MAIL_refer_ext=lang($lang,'mail_msg_ticket_refer_ext');
$MAIL_to_w=lang($lang,'mail_msg_ticket_to_ext');

$MAIL_msg_comment=lang($lang,'mail_msg_ticket_comment');
$MAIL_msg_comment_ext=lang($lang,'mail_msg_ticket_comment_ext');


$MAIL_msg_lock=lang($lang,'mail_msg_ticket_lock');
$MAIL_msg_lock_ext=lang($lang,'mail_msg_ticket_lock_ext');
$MAIL_msg_unlock=lang($lang,'mail_msg_ticket_unlock');
$MAIL_msg_unlock_ext=lang($lang,'mail_msg_ticket_unlock_ext');
$MAIL_msg_ok=lang($lang,'mail_msg_ticket_ok');
$MAIL_msg_ok_ext=lang($lang,'mail_msg_ticket_ok_ext');
$MAIL_msg_no_ok=lang($lang,'mail_msg_ticket_no_ok');
$MAIL_msg_no_ok_ext=lang($lang,'mail_msg_ticket_no_ok_ext');





   if ($type_op == "mailers") {


            $stmt22 = $dbConnection->prepare('SELECT value FROM perf where param=:tid');
            $stmt22->execute(array(
                ':tid' => 'mailers_text'
            ));
            $mm = $stmt22->fetch(PDO::FETCH_ASSOC);
            $mmm=$mm['value'];


$subject=get_conf_param('mailers_subj');
$message=$mmm;

    send_mail($user_mail,$subject,$message);

   }


   if ($type_op == "portal_post_new") {

    

        $stmt = $dbConnection->prepare('SELECT subj, author_id, uniq_id, msg FROM portal_posts where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $post_res = $stmt->fetch(PDO::FETCH_ASSOC);

$SUBJ_POST=$post_res['subj'];
$AUTHOR_POST=name_of_user_ret($post_res['author_id']);
$THREAD_HASH=$post_res['uniq_id'];

$POST_COMMENT=$post_res['msg'];


   
$subject = $SUBJ_POST.' - '.lang($lang, 'POST_MAIL_POST_NEW');
//$message = eval(file_get_contents($base . "/inc/mail_tmpl/new_ticket.tpl"));
ob_start();
include($base . "/inc/mail_tmpl/portal_post_new.tpl");
$message = ob_get_clean();


$message = str_replace("{PORTAL_post_comment}", lang($lang, 'POST_MAIL_POST_NEW').' '.get_conf_param('name_of_firm'), $message);
$message = str_replace("{MAIL_info}", lang($lang,'MAIL_info'), $message);
$message = str_replace("{POST_created_author}", lang($lang,'POST_created_author'), $message);
$message = str_replace("{POST_MAIL_subj}", lang($lang,'POST_MAIL_subj'), $message);
$message = str_replace("{PORTAL_post_comment_ext}", lang($lang,'PORTAL_post_NEWM_ext'), $message);
$message = str_replace("{MAIL_2link}", lang($lang,'PORTAL_post_MAIL_2link'), $message);





$message = str_replace("{uin}", $AUTHOR_POST, $message);
$message = str_replace("{to_text}", $SUBJ_POST, $message);
$message = str_replace("{who_init}", $AUTHOR_POST, $message);
$message = str_replace("{comment}", $POST_COMMENT, $message);
$message = str_replace("{h}", $THREAD_HASH, $message);


     send_mail($user_mail,$subject,$message, $post_res['uniq_id']);


   }


  else if ($type_op == "portal_post_comment") {


        $stmt = $dbConnection->prepare('SELECT subj, author_id, uniq_id FROM portal_posts where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $post_res = $stmt->fetch(PDO::FETCH_ASSOC);

$SUBJ_POST=$post_res['subj'];
$AUTHOR_POST=name_of_user_ret($post_res['author_id']);
$THREAD_HASH=$post_res['uniq_id'];

    $stmt2 = $dbConnection->prepare('SELECT * FROM post_comments where p_id=:tid ORDER BY id DESC LIMIT 1');
    $stmt2->execute(array(':tid' => $ticket_id));
    $res_post_comment = $stmt2->fetch(PDO::FETCH_ASSOC);
    //$user_init_comment=$res_post_comment['user_id'];

$USER_AUTHOR_COMMENT=name_of_user_ret($res_post_comment['user_id']);
$POST_COMMENT=$res_post_comment['comment_text'];


   
$subject = $SUBJ_POST.' - '.lang($lang, 'POST_MAIL_COMMENT');
//$message = eval(file_get_contents($base . "/inc/mail_tmpl/new_ticket.tpl"));
ob_start();
include($base . "/inc/mail_tmpl/portal_post_comment.tpl");
$message = ob_get_clean();


$message = str_replace("{PORTAL_post_comment}", lang($lang, 'POST_MAIL_COMMENT').' '.get_conf_param('name_of_firm'), $message);
$message = str_replace("{MAIL_info}", lang($lang,'MAIL_info'), $message);
$message = str_replace("{POST_created_author}", lang($lang,'POST_created_author'), $message);
$message = str_replace("{POST_MAIL_subj}", lang($lang,'POST_MAIL_subj'), $message);
$message = str_replace("{PORTAL_post_comment_ext}", lang($lang,'PORTAL_post_comment_ext'), $message);
$message = str_replace("{MAIL_2link}", lang($lang,'PORTAL_post_MAIL_2link'), $message);





$message = str_replace("{uin}", $AUTHOR_POST, $message);
$message = str_replace("{to_text}", $SUBJ_POST, $message);
$message = str_replace("{who_init}", $USER_AUTHOR_COMMENT, $message);
$message = str_replace("{comment}", $POST_COMMENT, $message);
$message = str_replace("{h}", $THREAD_HASH, $message);


     send_mail($user_mail,$subject,$message, $post_res['uniq_id']);

   }



   else if ($type_op == "ticket_create") {

        $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $ticket_res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $h=$ticket_res['hash_name'];
        $user_init_id=$ticket_res['user_init_id'];
        $uin=name_of_user_ret($user_init_id);//????? IF CLIENT /////
                
        $prio=lang($lang,'t_list_a_p_norm');
        if ($ticket_res['prio'] == "0") {$prio= lang($lang,'t_list_a_p_low'); }
        else if ($ticket_res['prio'] == "2") {$prio= lang($lang,'t_list_a_p_high'); }
        $nou=name_of_client_ret($ticket_res['client_id']);
        $to_id=$ticket_res['user_to_id'];
        $s=$ticket_res['subj'];
        $m=$ticket_res['msg'];
        $unit_id=$ticket_res['unit_id'];
        //кому?
        if ($ticket_res['user_to_id'] <> 0 ) {
            $to_text="".name_of_user_ret($to_id)."";
        }
        else if ($ticket_res['user_to_id'] == 0 ) {
            $to_text=view_array(get_unit_name_return($unit_id));
        }



   
$subject = lang($lang,'TICKET_name').' #'.$ticket_id.' - '.$MAIL_new;
//$message = eval(file_get_contents($base . "/inc/mail_tmpl/new_ticket.tpl"));
ob_start();
include($base . "/inc/mail_tmpl/new_ticket.tpl");
$message = ob_get_clean();
$message = str_replace("{MAIL_new_ext}", lang('mail_msg_ticket_new'), $message);
$message = str_replace("{MAIL_new}", $MAIL_new, $message);
$message = str_replace("{MAIL_code}", $MAIL_code, $message);
$message = str_replace("{ticket_id}", $ticket_id, $message);
$message = str_replace("{MAIL_2link}", $MAIL_2link, $message);
$message = str_replace("{MAIL_info}", $MAIL_info, $message);
$message = str_replace("{MAIL_created}", $MAIL_created, $message);
$message = str_replace("{uin}", $uin, $message);
$message = str_replace("{MAIL_to}", $MAIL_to, $message);
$message = str_replace("{to_text}", $to_text, $message);
$message = str_replace("{MAIL_prio}", $MAIL_prio, $message);
$message = str_replace("{prio}", $prio, $message);
$message = str_replace("{MAIL_worker}", $MAIL_worker, $message);
$message = str_replace("{nou}", $nou, $message);
$message = str_replace("{MAIL_msg}", $MAIL_msg, $message);

$message = str_replace("{MAIL_subj}", $MAIL_subj, $message);
$message = str_replace("{s}", $s, $message);
$message = str_replace("{MAIL_text}", $MAIL_text, $message);
$message = str_replace("{m}", $m, $message);

$message = str_replace("{h}", $h, $message);

     if (check_notify_mail_user($type_op, $user_mail))
{
  send_mail($user_mail,$subject,$message,$h);
}
     
/*

ticket_create:true,
ticket_refer:true,
ticket_comment:true,
ticket_lock:true,
ticket_unlock:true,
ticket_ok:true,
ticket_no_ok:true




if (check_notify_mail_user($type_op, $user_mail))
{
  send_mail($user_mail,$subject,$message,$h);
}
*/




   }
   
else if ($type_op == "ticket_refer") {
  /*
  Тема: Заявка # переадресована
  Текст: ФИО, Вы получили это сообщение, потому что заявка была переадресована.
  send_mail($to,$subj,$msg);
  */
    $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $ticket_res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //Узнать кем?
        $stmt_log = $dbConnection->prepare('SELECT init_user_id FROM ticket_log where ticket_id=:tid and msg=:msg order by ID desc limit 1');
        $stmt_log->execute(array(':tid' => $ticket_id, ':msg'=>'refer'));
        $ticket_log_res = $stmt_log->fetch(PDO::FETCH_ASSOC);
        $who_init=name_of_user_ret($ticket_log_res['init_user_id']);
        
        $h=$ticket_res['hash_name'];
        $user_init_id=$ticket_res['user_init_id'];
        $uin=name_of_user_ret($user_init_id);//????? IF CLIENT /////
                
        $prio=lang($lang,'t_list_a_p_norm');
        if ($ticket_res['prio'] == "0") {$prio= lang($lang,'t_list_a_p_low'); }
        else if ($ticket_res['prio'] == "2") {$prio= lang($lang,'t_list_a_p_high'); }
        $nou=name_of_client_ret($ticket_res['client_id']);
        $to_id=$ticket_res['user_to_id'];
        $s=$ticket_res['subj'];
        $m=$ticket_res['msg'];
        $unit_id=$ticket_res['unit_id'];
        //кому?
        if ($ticket_res['user_to_id'] <> 0 ) {
            $to_text="".name_of_user_ret($to_id)."";
        }
        else if ($ticket_res['user_to_id'] == 0 ) {
            $to_text=view_array(get_unit_name_return($unit_id));
        }



   
$subject = lang($lang,'TICKET_name').' #'.$ticket_id.' - '.$MAIL_refer;


ob_start();
include($base . "/inc/mail_tmpl/refer_ticket.tpl");
$message = ob_get_clean();

$message = str_replace("{MAIL_refer}", $MAIL_refer, $message);
$message = str_replace("{MAIL_refer_ext}", $MAIL_refer_ext, $message);
$message = str_replace("{who_init}", $who_init, $message);
$message = str_replace("{MAIL_to_w}", $MAIL_to_w, $message);




$message = str_replace("{MAIL_code}", $MAIL_code, $message);
$message = str_replace("{ticket_id}", $ticket_id, $message);

$message = str_replace("{MAIL_2link}", $MAIL_2link, $message);
$message = str_replace("{MAIL_info}", $MAIL_info, $message);

$message = str_replace("{MAIL_created}", $MAIL_created, $message);
$message = str_replace("{uin}", $uin, $message);
$message = str_replace("{MAIL_to}", $MAIL_to, $message);
$message = str_replace("{to_text}", $to_text, $message);

$message = str_replace("{MAIL_prio}", $MAIL_prio, $message);
$message = str_replace("{prio}", $prio, $message);
$message = str_replace("{MAIL_worker}", $MAIL_worker, $message);
$message = str_replace("{nou}", $nou, $message);

$message = str_replace("{MAIL_msg}", $MAIL_msg, $message);
$message = str_replace("{MAIL_subj}", $MAIL_subj, $message);
$message = str_replace("{s}", $s, $message);
$message = str_replace("{MAIL_text}", $MAIL_text, $message);
$message = str_replace("{m}", $m, $message);

$message = str_replace("{h}", $h, $message, $h);

if (check_notify_mail_user($type_op, $user_mail))
{
  send_mail($user_mail,$subject,$message,$h);
}
  
}
else if ($type_op == "ticket_comment") {
      $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $ticket_res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //Узнать кем?
        $stmt_log = $dbConnection->prepare('SELECT init_user_id FROM ticket_log where ticket_id=:tid and msg=:msg order by ID desc limit 1');
        $stmt_log->execute(array(':tid' => $ticket_id, ':msg'=>'comment'));
        $ticket_log_res = $stmt_log->fetch(PDO::FETCH_ASSOC);
        $who_init=name_of_user_ret($ticket_log_res['init_user_id']);
        $wuid=$ticket_log_res['init_user_id'];
        
        $stmt_com = $dbConnection->prepare('SELECT comment_text FROM comments where t_id=:tid and user_id=:uid order by ID desc limit 1');
        $stmt_com->execute(array(':tid' => $ticket_id,':uid' => $wuid));
        $ticket_com_res = $stmt_com->fetch(PDO::FETCH_ASSOC);
        $comment=$ticket_com_res['comment_text'];
        
        
        
        $h=$ticket_res['hash_name'];
        $user_init_id=$ticket_res['user_init_id'];
        $uin=name_of_user_ret($user_init_id);//????? IF CLIENT /////
                
        $prio=lang($lang,'t_list_a_p_norm');
        if ($ticket_res['prio'] == "0") {$prio= lang($lang,'t_list_a_p_low'); }
        else if ($ticket_res['prio'] == "2") {$prio= lang($lang,'t_list_a_p_high'); }
        $nou=name_of_client_ret($ticket_res['client_id']);
        $to_id=$ticket_res['user_to_id'];
        $s=$ticket_res['subj'];
        $m=$ticket_res['msg'];
        $unit_id=$ticket_res['unit_id'];
        //кому?
        if ($ticket_res['user_to_id'] <> 0 ) {
            $to_text="".name_of_user_ret($to_id)."";
        }
        else if ($ticket_res['user_to_id'] == 0 ) {
            $to_text=view_array(get_unit_name_return($unit_id));
        }



   
$subject = lang($lang,'TICKET_name').' #'.$ticket_id.' - '.$MAIL_msg_comment;
ob_start();
include($base . "/inc/mail_tmpl/comment_ticket.tpl");
$message = ob_get_clean();

$message = str_replace("{MAIL_msg_comment}", $MAIL_msg_comment, $message);
$message = str_replace("{MAIL_msg_comment_ext}", $MAIL_msg_comment_ext, $message);
$message = str_replace("{who_init}", $who_init, $message);
$message = str_replace("{comment}", $comment, $message);




$message = str_replace("{MAIL_code}", $MAIL_code, $message);
$message = str_replace("{ticket_id}", $ticket_id, $message);

$message = str_replace("{MAIL_2link}", $MAIL_2link, $message);
$message = str_replace("{MAIL_info}", $MAIL_info, $message);

$message = str_replace("{MAIL_created}", $MAIL_created, $message);
$message = str_replace("{uin}", $uin, $message);

$message = str_replace("{MAIL_to}", $MAIL_to, $message);
$message = str_replace("{to_text}", $to_text, $message);

$message = str_replace("{MAIL_prio}", $MAIL_prio, $message);
$message = str_replace("{prio}", $prio, $message);
$message = str_replace("{MAIL_worker}", $MAIL_worker, $message);
$message = str_replace("{nou}", $nou, $message);

$message = str_replace("{MAIL_msg}", $MAIL_msg, $message);
$message = str_replace("{MAIL_subj}", $MAIL_subj, $message);
$message = str_replace("{s}", $s, $message);
$message = str_replace("{MAIL_text}", $MAIL_text, $message);
$message = str_replace("{m}", $m, $message);

$message = str_replace("{h}", $h, $message);
    if (check_notify_mail_user($type_op, $user_mail))
{
  send_mail($user_mail,$subject,$message,$h);
}
}
else if ($type_op == "ticket_lock") {
    
    $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $ticket_res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //Узнать кем?
        $stmt_log = $dbConnection->prepare('SELECT init_user_id FROM ticket_log where ticket_id=:tid and msg=:msg order by ID desc limit 1');
        $stmt_log->execute(array(':tid' => $ticket_id, ':msg'=>'lock'));
        $ticket_log_res = $stmt_log->fetch(PDO::FETCH_ASSOC);
        $who_init=name_of_user_ret($ticket_log_res['init_user_id']);
        
        
        
        
        $h=$ticket_res['hash_name'];
        $user_init_id=$ticket_res['user_init_id'];
        $uin=name_of_user_ret($user_init_id);//????? IF CLIENT /////
                
        $prio=lang($lang,'t_list_a_p_norm');
        if ($ticket_res['prio'] == "0") {$prio= lang($lang,'t_list_a_p_low'); }
        else if ($ticket_res['prio'] == "2") {$prio= lang($lang,'t_list_a_p_high'); }
        $nou=name_of_client_ret($ticket_res['client_id']);
        $to_id=$ticket_res['user_to_id'];
        $s=$ticket_res['subj'];
        $m=$ticket_res['msg'];
        $unit_id=$ticket_res['unit_id'];
        //кому?
        if ($ticket_res['user_to_id'] <> 0 ) {
            $to_text="".name_of_user_ret($to_id)."";
        }
        else if ($ticket_res['user_to_id'] == 0 ) {
            $to_text=view_array(get_unit_name_return($unit_id));
        }



   
$subject = lang($lang,'TICKET_name').' #'.$ticket_id.' - '.$MAIL_msg_lock;

ob_start();
include($base . "/inc/mail_tmpl/lock_ticket.tpl");
$message = ob_get_clean();

$message = str_replace("{MAIL_msg_lock}", $MAIL_msg_lock, $message);
$message = str_replace("{MAIL_msg_lock_ext}", $MAIL_msg_lock_ext, $message);
$message = str_replace("{who_init}", $who_init, $message);





$message = str_replace("{MAIL_code}", $MAIL_code, $message);
$message = str_replace("{ticket_id}", $ticket_id, $message);

$message = str_replace("{MAIL_2link}", $MAIL_2link, $message);
$message = str_replace("{MAIL_info}", $MAIL_info, $message);

$message = str_replace("{MAIL_created}", $MAIL_created, $message);
$message = str_replace("{uin}", $uin, $message);

$message = str_replace("{MAIL_to}", $MAIL_to, $message);
$message = str_replace("{to_text}", $to_text, $message);

$message = str_replace("{MAIL_prio}", $MAIL_prio, $message);
$message = str_replace("{prio}", $prio, $message);
$message = str_replace("{MAIL_worker}", $MAIL_worker, $message);
$message = str_replace("{nou}", $nou, $message);

$message = str_replace("{MAIL_msg}", $MAIL_msg, $message);
$message = str_replace("{MAIL_subj}", $MAIL_subj, $message);
$message = str_replace("{s}", $s, $message);
$message = str_replace("{MAIL_text}", $MAIL_text, $message);
$message = str_replace("{m}", $m, $message);

$message = str_replace("{h}", $h, $message);
if (check_notify_mail_user($type_op, $user_mail))
{
  send_mail($user_mail,$subject,$message,$h);
}
}
else if ($type_op == "ticket_unlock") {
  $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $ticket_res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //Узнать кем?
        $stmt_log = $dbConnection->prepare('SELECT init_user_id FROM ticket_log where ticket_id=:tid and msg=:msg order by ID desc limit 1');
        $stmt_log->execute(array(':tid' => $ticket_id, ':msg'=>'unlock'));
        $ticket_log_res = $stmt_log->fetch(PDO::FETCH_ASSOC);
        $who_init=name_of_user_ret($ticket_log_res['init_user_id']);
        
        
        
        
        $h=$ticket_res['hash_name'];
        $user_init_id=$ticket_res['user_init_id'];
        $uin=name_of_user_ret($user_init_id);//????? IF CLIENT /////
                
        $prio=lang($lang,'t_list_a_p_norm');
        if ($ticket_res['prio'] == "0") {$prio= lang($lang,'t_list_a_p_low'); }
        else if ($ticket_res['prio'] == "2") {$prio= lang($lang,'t_list_a_p_high'); }
        $nou=name_of_client_ret($ticket_res['client_id']);
        $to_id=$ticket_res['user_to_id'];
        $s=$ticket_res['subj'];
        $m=$ticket_res['msg'];
        $unit_id=$ticket_res['unit_id'];
        //кому?
        if ($ticket_res['user_to_id'] <> 0 ) {
            $to_text="".name_of_user_ret($to_id)."";
        }
        else if ($ticket_res['user_to_id'] == 0 ) {
            $to_text=view_array(get_unit_name_return($unit_id));
        }



   
$subject = lang($lang,'TICKET_name').' #'.$ticket_id.' - '.$MAIL_msg_unlock;

ob_start();
include($base . "/inc/mail_tmpl/unlock_ticket.tpl");
$message = ob_get_clean();

$message = str_replace("{MAIL_msg_unlock}", $MAIL_msg_unlock, $message);
$message = str_replace("{MAIL_msg_unlock_ext}", $MAIL_msg_unlock_ext, $message);
$message = str_replace("{who_init}", $who_init, $message);





$message = str_replace("{MAIL_code}", $MAIL_code, $message);
$message = str_replace("{ticket_id}", $ticket_id, $message);

$message = str_replace("{MAIL_2link}", $MAIL_2link, $message);
$message = str_replace("{MAIL_info}", $MAIL_info, $message);

$message = str_replace("{MAIL_created}", $MAIL_created, $message);
$message = str_replace("{uin}", $uin, $message);

$message = str_replace("{MAIL_to}", $MAIL_to, $message);
$message = str_replace("{to_text}", $to_text, $message);

$message = str_replace("{MAIL_prio}", $MAIL_prio, $message);
$message = str_replace("{prio}", $prio, $message);
$message = str_replace("{MAIL_worker}", $MAIL_worker, $message);
$message = str_replace("{nou}", $nou, $message);

$message = str_replace("{MAIL_msg}", $MAIL_msg, $message);
$message = str_replace("{MAIL_subj}", $MAIL_subj, $message);
$message = str_replace("{s}", $s, $message);
$message = str_replace("{MAIL_text}", $MAIL_text, $message);
$message = str_replace("{m}", $m, $message);

$message = str_replace("{h}", $h, $message);

if (check_notify_mail_user($type_op, $user_mail))
{
  send_mail($user_mail,$subject,$message,$h);
}
}
else if ($type_op == "ticket_ok") {
  $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $ticket_res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //Узнать кем?
        $stmt_log = $dbConnection->prepare('SELECT init_user_id FROM ticket_log where ticket_id=:tid and msg=:msg order by ID desc limit 1');
        $stmt_log->execute(array(':tid' => $ticket_id, ':msg'=>'ok'));
        $ticket_log_res = $stmt_log->fetch(PDO::FETCH_ASSOC);
        $who_init=name_of_user_ret($ticket_log_res['init_user_id']);
        
        
        
        
        $h=$ticket_res['hash_name'];
        $user_init_id=$ticket_res['user_init_id'];
        $uin=name_of_user_ret($user_init_id);//????? IF CLIENT /////
                
        $prio=lang($lang,'t_list_a_p_norm');
        if ($ticket_res['prio'] == "0") {$prio= lang($lang,'t_list_a_p_low'); }
        else if ($ticket_res['prio'] == "2") {$prio= lang($lang,'t_list_a_p_high'); }
        $nou=name_of_client_ret($ticket_res['client_id']);
        $to_id=$ticket_res['user_to_id'];
        $s=$ticket_res['subj'];
        $m=$ticket_res['msg'];
        $unit_id=$ticket_res['unit_id'];
        //кому?
        if ($ticket_res['user_to_id'] <> 0 ) {
            $to_text="".name_of_user_ret($to_id)."";
        }
        else if ($ticket_res['user_to_id'] == 0 ) {
            $to_text=view_array(get_unit_name_return($unit_id));
        }



   
$subject = lang($lang,'TICKET_name').' #'.$ticket_id.' - '.$MAIL_msg_ok;

ob_start();
include($base . "/inc/mail_tmpl/ok_ticket.tpl");
$message = ob_get_clean();

$message = str_replace("{MAIL_msg_ok}", $MAIL_msg_ok, $message);
$message = str_replace("{MAIL_msg_ok_ext}", $MAIL_msg_ok_ext, $message);
$message = str_replace("{who_init}", $who_init, $message);





$message = str_replace("{MAIL_code}", $MAIL_code, $message);
$message = str_replace("{ticket_id}", $ticket_id, $message);

$message = str_replace("{MAIL_2link}", $MAIL_2link, $message);
$message = str_replace("{MAIL_info}", $MAIL_info, $message);

$message = str_replace("{MAIL_created}", $MAIL_created, $message);
$message = str_replace("{uin}", $uin, $message);

$message = str_replace("{MAIL_to}", $MAIL_to, $message);
$message = str_replace("{to_text}", $to_text, $message);

$message = str_replace("{MAIL_prio}", $MAIL_prio, $message);
$message = str_replace("{prio}", $prio, $message);
$message = str_replace("{MAIL_worker}", $MAIL_worker, $message);
$message = str_replace("{nou}", $nou, $message);

$message = str_replace("{MAIL_msg}", $MAIL_msg, $message);
$message = str_replace("{MAIL_subj}", $MAIL_subj, $message);
$message = str_replace("{s}", $s, $message);
$message = str_replace("{MAIL_text}", $MAIL_text, $message);
$message = str_replace("{m}", $m, $message);

$message = str_replace("{h}", $h, $message, $h);


if (check_notify_mail_user($type_op, $user_mail))
{
  send_mail($user_mail,$subject,$message,$h);
}
}
else if ($type_op == "ticket_no_ok") {
  $stmt = $dbConnection->prepare('SELECT user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio,last_update FROM tickets where id=:tid');
        $stmt->execute(array(':tid' => $ticket_id));
        $ticket_res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //Узнать кем?
        $stmt_log = $dbConnection->prepare('SELECT init_user_id FROM ticket_log where ticket_id=:tid and msg=:msg order by ID desc limit 1');
        $stmt_log->execute(array(':tid' => $ticket_id, ':msg'=>'no_ok'));
        $ticket_log_res = $stmt_log->fetch(PDO::FETCH_ASSOC);
        $who_init=name_of_user_ret($ticket_log_res['init_user_id']);
        
        
        
        
        $h=$ticket_res['hash_name'];
        $user_init_id=$ticket_res['user_init_id'];
        $uin=name_of_user_ret($user_init_id);//????? IF CLIENT /////
                
        $prio=lang($lang,'t_list_a_p_norm');
        if ($ticket_res['prio'] == "0") {$prio= lang($lang,'t_list_a_p_low'); }
        else if ($ticket_res['prio'] == "2") {$prio= lang($lang,'t_list_a_p_high'); }
        $nou=name_of_client_ret($ticket_res['client_id']);
        $to_id=$ticket_res['user_to_id'];
        $s=$ticket_res['subj'];
        $m=$ticket_res['msg'];
        $unit_id=$ticket_res['unit_id'];
        //кому?
        if ($ticket_res['user_to_id'] <> 0 ) {
            $to_text="".name_of_user_ret($to_id)."";
        }
        else if ($ticket_res['user_to_id'] == 0 ) {
            $to_text=view_array(get_unit_name_return($unit_id));
        }



   
$subject = lang($lang,'TICKET_name').' #'.$ticket_id.' - '.$MAIL_msg_no_ok;


ob_start();
include($base . "/inc/mail_tmpl/unok_ticket.tpl");
$message = ob_get_clean();

$message = str_replace("{MAIL_msg_no_ok}", $MAIL_msg_no_ok, $message);
$message = str_replace("{MAIL_msg_no_ok_ext}", $MAIL_msg_no_ok_ext, $message);
$message = str_replace("{who_init}", $who_init, $message);





$message = str_replace("{MAIL_code}", $MAIL_code, $message);
$message = str_replace("{ticket_id}", $ticket_id, $message);

$message = str_replace("{MAIL_2link}", $MAIL_2link, $message);
$message = str_replace("{MAIL_info}", $MAIL_info, $message);

$message = str_replace("{MAIL_created}", $MAIL_created, $message);
$message = str_replace("{uin}", $uin, $message);

$message = str_replace("{MAIL_to}", $MAIL_to, $message);
$message = str_replace("{to_text}", $to_text, $message);

$message = str_replace("{MAIL_prio}", $MAIL_prio, $message);
$message = str_replace("{prio}", $prio, $message);
$message = str_replace("{MAIL_worker}", $MAIL_worker, $message);
$message = str_replace("{nou}", $nou, $message);

$message = str_replace("{MAIL_msg}", $MAIL_msg, $message);
$message = str_replace("{MAIL_subj}", $MAIL_subj, $message);
$message = str_replace("{s}", $s, $message);
$message = str_replace("{MAIL_text}", $MAIL_text, $message);
$message = str_replace("{m}", $m, $message);

$message = str_replace("{h}", $h, $message);


if (check_notify_mail_user($type_op, $user_mail))
{
  send_mail($user_mail,$subject,$message,$h);
}
}

}





$stmt = $dbConnection->prepare('SELECT id, delivers_id,type_op,ticket_id 
from notification_pool where status=:n');
$stmt->execute(array(':n'=>'0'));
$res1 = $stmt->fetchAll();                 


foreach($res1 as $qrow) { 
//
$r_id=$qrow['id'];
$stmt_del = $dbConnection->prepare('delete from notification_pool where id=:n');
$stmt_del->execute(array(':n'=>$r_id));


$users=explode(",",$qrow['delivers_id']);
$type_op=$qrow['type_op'];
$ticket_id=$qrow['ticket_id'];



  foreach ($users as $val) {
            //from users fio,lang,email where status=1
            //$val
            
            
      $stmt = $dbConnection->prepare('SELECT email, pb, lang, mob, id FROM users where id=:tid');
            $stmt->execute(array(':tid' => $val));
            $usr_info = $stmt->fetch(PDO::FETCH_ASSOC);
            $pb=$usr_info['pb'];
      $usr_mail=$usr_info['email'];
      $usr_lang=$usr_info['lang'];
      $mob=$usr_info['mob'];

      $usr_id=$usr_info['id'];
           // $lb=$fio['lock_by'];
            
            
            if ($pb) {
              send_pushbullet($type_op, $usr_lang, $pb, $ticket_id);
            }
            
            
            if ($usr_mail) {
            make_mail($type_op, $usr_lang, $usr_mail, $ticket_id);
            }

if (check_user_devices($usr_id)) {
  make_device_push($type_op, $usr_lang, $usr_id, $ticket_id);
}


if (get_conf_param('smsc_active') == "true") {
                        if ($mob) {
            send_smsc($type_op, $usr_lang, $mob, $ticket_id);
            }
          }
            //make_mail($type_op, $usr_lang, $usr_mail, $ticket_id);





            
  }





}
//make_mail('ticket_no_ok','ru', 'info@rustem.com.ua', '288');
//send_mail('info@rustem.com.ua','hello','eeee');
/*
  
*/
include($base .'/sys/scheduler.php');


?>