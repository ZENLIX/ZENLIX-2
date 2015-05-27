<?php
session_start();
include_once ("../functions.inc.php");


    if ($_SESSION['helpdesk_user_id']) {
        
        $CONF['title_header'] = lang('MESSAGES_us') . " - " . $CONF['name_of_firm'];
        
        include ("head.inc.php");
        include ("client.navbar.inc.php");
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if (($_SESSION['helpdesk_user_id'])) {
            
            if ($_GET['to']) {
                $t = $_GET['to'];
                $ufio = get_user_val_by_hash($t, 'fio');
            } 
            else {
                $ufio = "";
            }
            
            //список всех пользователей с которыми когда-либо общался
            $stmt = $dbConnection->prepare('SELECT id, user_from,user_to from messages where
                        (user_to=:u_to)
                        order by is_read , date_op ASC');
            $stmt->execute(array(
                ':u_to' => $_SESSION['helpdesk_user_id']
            ));
            
            $re = $stmt->fetchAll();
            if (!empty($re)) {
                $user_arr = array();
                foreach ($re as $rews) {
                    
                    array_push($user_arr, $rews['user_from']);
                    array_push($user_arr, $rews['user_to']);
                }
            }
            
            $user_arr = array_unique($user_arr);
            if (($key = array_search($_SESSION['helpdesk_user_id'], $user_arr)) !== false) {
                unset($user_arr[$key]);
            }
            if (($key = array_search('0', $user_arr)) !== false) {
                unset($user_arr[$key]);
            }
            
            $uarr = array();
            foreach ($user_arr as $uniq_id) {
                
                $stmt1 = $dbConnection->prepare('SELECT count(id) as cou from messages where
        ((user_from=:ufrom and user_to=:uto)) and is_read=0
         ');
                $stmt1->execute(array(
                    ':ufrom' => $uniq_id,
                    ':uto' => $_SESSION['helpdesk_user_id']
                ));
                
                $tt = $stmt1->fetch(PDO::FETCH_ASSOC);
                if ($tt['cou'] != 0) {
                    $tt = "<small id=\"ul_label_" . $uniq_id . "\"><small class=\"badge pull-right\">" . $tt['cou'] . "</small></small>";
                } 
                else {
                    $tt = "<small id=\"ul_label_" . $uniq_id . "\"></small>";
                }
                
                if ($_GET['to']) {
                    $get_act = true;
                    $t = $_GET['to'];
                    $tuid = get_user_val_by_hash($t, 'id');
                    
                    if ($tuid == $uniq_id) {
                    }
                } 
                else {
                    $get_act = false;
                }
                
                array_push($uarr, array(
                    
                    'get_act' => $get_act,
                    'tuid' => $tuid,
                    'uniq_id' => $uniq_id,
                    'usr_img' => get_user_img_by_id($uniq_id) ,
                    'name_user' => nameshort(name_of_user_ret_nolink($uniq_id)) ,
                    'tt' => $tt
                ));
            }
            
            if ($_GET['to']) {
                $t = $_GET['to'];
                $mwith = lang('MESSAGES_with') . " " . get_user_val_by_hash($t, 'fio');
                $tget = true;
                
                ob_start();
                view_messages(get_user_val_by_hash($t, 'id'));
                $view_messages = ob_get_contents();
                ob_end_clean();
            } 
            else {
                $tget = false;
                $mwith = lang('MESSAGES_main');
                ob_start();
                view_messages('main');
                $view_messages = ob_get_contents();
                ob_end_clean();
            }
            





/*


Есть ли открытые запросы?
Если есть то вывести чат с кнопкой завершить
Если нет показать кнопку:
*/

/*
?>
<div class="col-md-6 col-md-offset-3">
    <br><br><br>
            <a id="ClientChatRequest_action" class="btn btn-block btn-primary" >Запрос на чат</a>
            <br>
            <center><small class="text-muted">Что бы начать чат, подайте запрос.</small></center>
        </div>

<?
*/



//$newt = get_total_client_tickets_out();
//$newt2 = get_total_client_tickets_ok();
//$newt = $newt - $newt2;


    $stmt_spec = $dbConnection->prepare('SELECT client_request_status  from messages where type_msg=:str and user_from=:uf');
    $stmt_spec->execute(array(
        ':str' => 'request',
        ':uf' => $_SESSION['helpdesk_user_id']
    ));
    $row = $stmt_spec->fetch(PDO::FETCH_ASSOC);


if (!empty($row)){
$client_request_status=$row['client_request_status'];
$active_chat='false';

if ($client_request_status == "0") {
$chat_msgs=lang('chat_wait');
$active_chat='false';$get_total_msgs_user="false";
}
else if ($client_request_status == "1") {

    $stmt = $dbConnection->prepare('SELECT user_to from messages where type_msg=:type_msg and user_from=:ufrom');
    $stmt->execute(array(
        ':type_msg'=>'request',
        ':ufrom' => $_SESSION['helpdesk_user_id']
    ));
    
    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
   



$get_total_msgs_user=get_total_msgs_user($tt['user_to']);
$active_chat=$tt['user_to'];
ob_start();
view_messages($tt['user_to']);

//echo get_total_msgs_user('1');
$view_messages2 = ob_get_contents();
ob_end_clean();


    $chat_msgs=$view_messages2;
}
else if ($client_request_status == "2") {
    $chat_msgs="close";
    $active_chat='false';
    $get_total_msgs_user="false";
}

}
else if (empty($row)){
    $get_total_msgs_user="false";
    $active_chat='false';
$chat_msgs="
<div class=\"col-md-6 col-md-offset-3\">
    <br><br><br>
            <a id=\"ClientChatRequest_action\" class=\"btn btn-block btn-primary\" >Запрос на чат</a>
            <br>
            <center><small class=\"text-muted\">Что бы начать чат, подайте запрос.</small></center>
        </div>
    ";
}








            $basedir = dirname(dirname(__FILE__));
            
            ////////////
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
                $template = $twig->loadTemplate('client.messages.view.tmpl');
                
                // передаём в шаблон переменные и значения
                // выводим сформированное содержание
                echo $template->render(array(
                    'chat_msgs'=>$chat_msgs,
                    'hostname' => $CONF['hostname'],
                    'name_of_firm' => $CONF['name_of_firm'],
                    'MESSAGES_us' => lang('MESSAGES_us') ,
                    'MESSAGES_us_ext' => lang('MESSAGES_us_ext') ,
                    'MESSAGES_main' => lang('MESSAGES_main') ,
                    'MESSAGES_fio' => lang('MESSAGES_fio') ,
                    'ufio' => $ufio,
                    'uarr' => $uarr,
                    'mwith' => $mwith,
                    'view_messages' => $view_messages,
                    'MESSAGES_sel_text' => lang('MESSAGES_sel_text') ,
                    'get_user_val_by_hash' => get_user_val_by_hash($t, 'id') ,
                    'get_total_msgs_main' => $get_total_msgs_user ,
                    'tget' => $tget,
                    'active_chat'=>$active_chat
                ));
            }
            catch(Exception $e) {
                die('ERROR: ' . $e->getMessage());
            }
            
            include ("footer.inc.php");

        }
    }

?>
