<?php
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
	'title_header'	=> get_conf_param('title_header'),
	'hostname'		=> 'http://'.get_conf_param('hostname'),
	'mail'			=> get_conf_param('mail'),
	'days2arch'		=> get_conf_param('days2arch'),
	'name_of_firm'	=> get_conf_param('name_of_firm'),
	'fix_subj'		=> get_conf_param('fix_subj'),
	'first_login'	=> get_conf_param('first_login'),
	'file_uploads'	=> get_conf_param('file_uploads'),
	'file_types'	=> '('.get_conf_param('file_types').')',
	'file_size'		=> get_conf_param('file_size'),
	'now_dt' => $now_date_time
	);
$CONF_MAIL = array (
	'active'	=> get_conf_param('mail_active'),
	'host'		=> get_conf_param('mail_host'),
	'port'		=> get_conf_param('mail_port'),
	'auth'		=> get_conf_param('mail_auth'),
	'auth_type' => get_conf_param('mail_auth_type'),
	'username'	=> get_conf_param('mail_username'),
	'password'	=> get_conf_param('mail_password'),
	'from'		=> get_conf_param('mail_from'),
	'debug'		=> 'false'
);

function send_mail($to,$subj,$msg) {
	global $CONF, $CONF_MAIL, $dbConnection;
	
	//echo "helo";
	if (get_conf_param('mail_type') == "sendmail") {
	
	$mail = new PHPMailer();
	//$mail->SMTPDebug = 1;
	$mail->CharSet 	  = 'UTF-8';
	$mail->IsSendmail();

  $mail->AddReplyTo($CONF_MAIL['from'], $CONF['name_of_firm']);
  $mail->AddAddress($to, $to);
  $mail->SetFrom($CONF_MAIL['from'], $CONF['name_of_firm']);
  $mail->Subject = $subj;
  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; 
  $mail->MsgHTML($msg);
  $mail->Send();

}
else if (get_conf_param('mail_type') == "SMTP") {
	
	
	$mail = new PHPMailer();
	$mail->CharSet 	  = 'UTF-8';
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

/*

1. offline/online check ???
2. страница пользователи как-то переименовать кнопки
3. какие страницы обновлять в случае изменения заявки
4.

arch?????



Удалить пользователя
пользователь не удаляется а помечается как удалён. нигде больше не участвует но вся инфа о нём сохраняется.

actions.php:deps_del


//online/offline status user - every

///////////////////////////////


-Перевод языки
-Графики и отчёты
-дизайн и стили
////////////////////////////////

-удаление заявок/пользователей

///////////////////////////////

-Справка
-нотификация
-просмотр заявок пользователя


-учёт времени?

Notifications ON


actions: logout, refresh, gotourl


nodejs_pool

ticket_create:
	send_notifications


ticket_refer
	send notification


Для извещений нужно:
в таблицу для каждого получателя узнать uniq_id, и внести его.

`delivers_id` - уникальный хэш пользователя
  `type_op`   - ticket_create, ticket_refer
  `ticket_id` - t_id
  `dt`        - now()





ticket_comment

ticket_lock

ticket_unlock

ticket_ok

ticket_no_ok





*/
	 if ($type_op == "ticket_create") {

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
$message =<<<EOBODY
<div style="background: #ffffff; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
<p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">{$MAIL_new}!</p>
<table width="100%" cellpadding="3" cellspacing="0">
  <tbody>
    <tr id="tr_">
      <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_code}:</td>
      <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 19px;"><b>#{$ticket_id}</b></td>
      <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href='{$CONF['hostname']}ticket?{$h}'>{$MAIL_2link}</a>.</p></td>
    </tr>
  </tbody>
</table>
<br />
<table width="100%" cellspacing="0" cellpadding="3" style="">
  <tr style="border: 1px solid #ddd;">
    <td colspan="2" style="border: 1px solid #ddd; background-color: #f5f5f5; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><center>
      <strong>{$MAIL_info} </strong>
    </center></td>


  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_created}:</td>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$uin}</td>
  </tr>
  <tr>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_to}:</td>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$to_text}</td>
  </tr>
    <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_prio}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$prio}</td>
  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_worker}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$nou}</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px; background-color: #f5f5f5;"><center>
      <strong>{$MAIL_msg}</strong>
    </center></td>
  </tr>
  <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_subj}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$s}</td>
  </tr>
    <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_text}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$m}</td>
  </tr>
    <tr>
    <td colspan="5">&nbsp;</td>
  </tr>  
 
</table>
</center>

</div>
EOBODY;

		 send_mail($user_mail,$subject,$message);
		 
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
$message =<<<EOBODY
<div style="background: #ffffff; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
<p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">{$MAIL_refer}!</p>
<p style="font-family: Arial, Helvetica, sans-serif; font-size:12px; text-align:center;">{$MAIL_refer_ext} <strong>{$who_init}</strong> {$MAIL_to_w}  <strong>{$to_text}</strong></p>
<table width="100%" cellpadding="3" cellspacing="0">
  <tbody>
    <tr id="tr_">
      <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_code}:</td>
      <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 19px;"><b>#{$ticket_id}</b></td>
      <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href='{$CONF['hostname']}ticket?{$h}'>{$MAIL_2link}</a>.</p></td>
    </tr>
  </tbody>
</table>
<br />
<table width="100%" cellspacing="0" cellpadding="3" style="">
  <tr style="border: 1px solid #ddd;">
    <td colspan="2" style="border: 1px solid #ddd; background-color: #f5f5f5; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><center>
      <strong>{$MAIL_info} </strong>
    </center></td>


  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_created}:</td>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$uin}</td>
  </tr>
  <tr>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_to}:</td>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$to_text}</td>
  </tr>
    <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_prio}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$prio}</td>
  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_worker}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$nou}</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px; background-color: #f5f5f5;"><center>
      <strong>{$MAIL_msg}</strong>
    </center></td>
  </tr>
  <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_subj}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$s}</td>
  </tr>
    <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_text}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$m}</td>
  </tr>
    <tr>
    <td colspan="5">&nbsp;</td>
  </tr>  
 
</table>
</center>

</div>
EOBODY;

		 send_mail($user_mail,$subject,$message);
	
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
$message =<<<EOBODY
<div style="background: #ffffff; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
<p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">{$MAIL_msg_comment}!</p>
<p style="font-family: Arial, Helvetica, sans-serif; font-size:12px; text-align:center;">{$MAIL_msg_comment_ext} <strong>{$who_init}</strong>: <strong>{$comment}</strong></p>
<table width="100%" cellpadding="3" cellspacing="0">
  <tbody>
    <tr id="tr_">
      <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_code}:</td>
      <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 19px;"><b>#{$ticket_id}</b></td>
      <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href='{$CONF['hostname']}ticket?{$h}'>{$MAIL_2link}</a>.</p></td>
    </tr>
  </tbody>
</table>
<br />
<table width="100%" cellspacing="0" cellpadding="3" style="">
  <tr style="border: 1px solid #ddd;">
    <td colspan="2" style="border: 1px solid #ddd; background-color: #f5f5f5; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><center>
      <strong>{$MAIL_info} </strong>
    </center></td>


  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_created}:</td>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$uin}</td>
  </tr>
  <tr>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_to}:</td>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$to_text}</td>
  </tr>
    <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_prio}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$prio}</td>
  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_worker}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$nou}</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px; background-color: #f5f5f5;"><center>
      <strong>{$MAIL_msg}</strong>
    </center></td>
  </tr>
  <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_subj}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$s}</td>
  </tr>
    <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_text}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$m}</td>
  </tr>
    <tr>
    <td colspan="5">&nbsp;</td>
  </tr>  
 
</table>
</center>

</div>
EOBODY;

		 send_mail($user_mail,$subject,$message);
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
$message =<<<EOBODY
<div style="background: #ffffff; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
<p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">{$MAIL_msg_lock}!</p>
<p style="font-family: Arial, Helvetica, sans-serif; font-size:12px; text-align:center;">{$MAIL_msg_lock_ext} <strong>{$who_init}</strong></p>
<table width="100%" cellpadding="3" cellspacing="0">
  <tbody>
    <tr id="tr_">
      <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_code}:</td>
      <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 19px;"><b>#{$ticket_id}</b></td>
      <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href='{$CONF['hostname']}ticket?{$h}'>{$MAIL_2link}</a>.</p></td>
    </tr>
  </tbody>
</table>
<br />
<table width="100%" cellspacing="0" cellpadding="3" style="">
  <tr style="border: 1px solid #ddd;">
    <td colspan="2" style="border: 1px solid #ddd; background-color: #f5f5f5; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><center>
      <strong>{$MAIL_info} </strong>
    </center></td>


  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_created}:</td>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$uin}</td>
  </tr>
  <tr>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_to}:</td>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$to_text}</td>
  </tr>
    <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_prio}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$prio}</td>
  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_worker}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$nou}</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px; background-color: #f5f5f5;"><center>
      <strong>{$MAIL_msg}</strong>
    </center></td>
  </tr>
  <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_subj}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$s}</td>
  </tr>
    <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_text}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$m}</td>
  </tr>
    <tr>
    <td colspan="5">&nbsp;</td>
  </tr>  
 
</table>
</center>

</div>
EOBODY;

		 send_mail($user_mail,$subject,$message);
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
$message =<<<EOBODY
<div style="background: #ffffff; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
<p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">{$MAIL_msg_unlock}!</p>
<p style="font-family: Arial, Helvetica, sans-serif; font-size:12px; text-align:center;">{$MAIL_msg_unlock_ext} <strong>{$who_init}</strong></p>
<table width="100%" cellpadding="3" cellspacing="0">
  <tbody>
    <tr id="tr_">
      <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_code}:</td>
      <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 19px;"><b>#{$ticket_id}</b></td>
      <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href='{$CONF['hostname']}ticket?{$h}'>{$MAIL_2link}</a>.</p></td>
    </tr>
  </tbody>
</table>
<br />
<table width="100%" cellspacing="0" cellpadding="3" style="">
  <tr style="border: 1px solid #ddd;">
    <td colspan="2" style="border: 1px solid #ddd; background-color: #f5f5f5; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><center>
      <strong>{$MAIL_info} </strong>
    </center></td>


  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_created}:</td>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$uin}</td>
  </tr>
  <tr>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_to}:</td>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$to_text}</td>
  </tr>
    <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_prio}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$prio}</td>
  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_worker}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$nou}</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px; background-color: #f5f5f5;"><center>
      <strong>{$MAIL_msg}</strong>
    </center></td>
  </tr>
  <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_subj}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$s}</td>
  </tr>
    <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_text}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$m}</td>
  </tr>
    <tr>
    <td colspan="5">&nbsp;</td>
  </tr>  
 
</table>
</center>

</div>
EOBODY;

		 send_mail($user_mail,$subject,$message);
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
$message =<<<EOBODY
<div style="background: #ffffff; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
<p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">{$MAIL_msg_ok}!</p>
<p style="font-family: Arial, Helvetica, sans-serif; font-size:12px; text-align:center;">{$MAIL_msg_ok_ext} <strong>{$who_init}</strong></p>
<table width="100%" cellpadding="3" cellspacing="0">
  <tbody>
    <tr id="tr_">
      <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_code}:</td>
      <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 19px;"><b>#{$ticket_id}</b></td>
      <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href='{$CONF['hostname']}ticket?{$h}'>{$MAIL_2link}</a>.</p></td>
    </tr>
  </tbody>
</table>
<br />
<table width="100%" cellspacing="0" cellpadding="3" style="">
  <tr style="border: 1px solid #ddd;">
    <td colspan="2" style="border: 1px solid #ddd; background-color: #f5f5f5; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><center>
      <strong>{$MAIL_info} </strong>
    </center></td>


  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_created}:</td>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$uin}</td>
  </tr>
  <tr>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_to}:</td>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$to_text}</td>
  </tr>
    <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_prio}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$prio}</td>
  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_worker}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$nou}</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px; background-color: #f5f5f5;"><center>
      <strong>{$MAIL_msg}</strong>
    </center></td>
  </tr>
  <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_subj}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$s}</td>
  </tr>
    <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_text}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$m}</td>
  </tr>
    <tr>
    <td colspan="5">&nbsp;</td>
  </tr>  
 
</table>
</center>

</div>
EOBODY;

		 send_mail($user_mail,$subject,$message);
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
$message =<<<EOBODY
<div style="background: #ffffff; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
<p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">{$MAIL_msg_no_ok}!</p>
<p style="font-family: Arial, Helvetica, sans-serif; font-size:12px; text-align:center;">{$MAIL_msg_no_ok_ext} <strong>{$who_init}</strong></p>
<table width="100%" cellpadding="3" cellspacing="0">
  <tbody>
    <tr id="tr_">
      <td width="15%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_code}:</td>
      <td width="36%" align="center" valign="middle" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 19px;"><b>#{$ticket_id}</b></td>
      <td width="49%" style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><p style="font-family: Arial, Helvetica, sans-serif; font-size:11px; text-align:center;"> <a href='{$CONF['hostname']}ticket?{$h}'>{$MAIL_2link}</a>.</p></td>
    </tr>
  </tbody>
</table>
<br />
<table width="100%" cellspacing="0" cellpadding="3" style="">
  <tr style="border: 1px solid #ddd;">
    <td colspan="2" style="border: 1px solid #ddd; background-color: #f5f5f5; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;"><center>
      <strong>{$MAIL_info} </strong>
    </center></td>


  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_created}:</td>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$uin}</td>
  </tr>
  <tr>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_to}:</td>
    <td  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$to_text}</td>
  </tr>
    <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_prio}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$prio}</td>
  </tr>
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_worker}:</td>
    <td style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$nou}</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"  style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px; background-color: #f5f5f5;"><center>
      <strong>{$MAIL_msg}</strong>
    </center></td>
  </tr>
  <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_subj}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$s}</td>
  </tr>
    <tr>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$MAIL_text}:</td>
    <td   style="border: 1px solid #ddd;font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;">{$m}</td>
  </tr>
    <tr>
    <td colspan="5">&nbsp;</td>
  </tr>  
 
</table>
</center>

</div>
EOBODY;

		 send_mail($user_mail,$subject,$message);
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
						
						
			$stmt = $dbConnection->prepare('SELECT email, pb, lang FROM users where id=:tid and is_client=0');
            $stmt->execute(array(':tid' => $val));
            $usr_info = $stmt->fetch(PDO::FETCH_ASSOC);
            $pb=$usr_info['pb'];
			$usr_mail=$usr_info['email'];
			$usr_lang=$usr_info['lang'];
           // $lb=$fio['lock_by'];
            
            
            if ($pb) {
	            send_pushbullet($type_op, $usr_lang, $pb, $ticket_id);
            }
            
            
						if ($usr_mail) {
						make_mail($type_op, $usr_lang, $usr_mail, $ticket_id);
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