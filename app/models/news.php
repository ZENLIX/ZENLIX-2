<?php
session_start();
include_once ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    //if (validate_admin($_SESSION['helpdesk_user_id'])) {
    $CONF['title_header'] = lang('NAVBAR_news') . " - " . $CONF['name_of_firm'];
    
    include ("head.inc.php");
    include ("navbar.inc.php");
    
    /*
    
    from ticket_log: date_op, msg, init_user_id, to_user_id, ticket_id, to_unit_id
    
    
    
    */
    
    class news_list
    {
        
        //public $var;
        
        function __construct() {
            $this->arr = $this->get_arr();
            
            //$this->var2="ff";
            
            
        }
        
        function get_arr() {
            global $dbConnection;
            $uid = $_SESSION['helpdesk_user_id'];
            $unit_user = unit_of_user($uid);
            $priv_val = priv_status($uid);
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
                
                //Начальник
                //print_r($vv);
                
                //SELECT date_op, msg, init_user_id, target_user, ticket_id from news where find_in_set(:n,target_user) order by id DESC limit :c
                //$paramss=array(':n'=>$uid, ':uid2'=>$uid, ':c'=>'30');
                $stmt = $dbConnection->prepare('SELECT date_op, msg, init_user_id, target_user, ticket_id from news where find_in_set(:n,target_user) order by id DESC limit :c');
                $stmt->execute(array(
                    ':n' => $uid,
                    ':c' => '30'
                ));
                $res = $stmt->fetchAll();
            } 
            else if ($priv_val == "1") {
                
                $stmt = $dbConnection->prepare('SELECT date_op, msg, init_user_id, target_user, ticket_id from news where find_in_set(:n,target_user) order by id DESC limit :c');
                $stmt->execute(array(
                    ':n' => $uid,
                    ':c' => '30'
                ));
                $res = $stmt->fetchAll();
            } 
            else if ($priv_val == "2") {
                
                $stmt = $dbConnection->prepare('SELECT date_op, msg, init_user_id, target_user, ticket_id from news order by id DESC limit :c');
                $stmt->execute(array(
                    ':c' => '30'
                ));
                $res = $stmt->fetchAll();
            }
            return $res;
        }
    }
    
    $news_list = new news_list();
    
    //print_r ($news_list->var);
    
    $res = $news_list->arr;
    
    $news_arr = array();
    
    foreach ($res as $rows) {
        
        $today = date('d-m-Y');
        $re_date = date('d-m-Y', strtotime($rows['date_op']));
        
        $init_user = $rows['init_user_id'];
        $t_id = $rows['ticket_id'];
        $t_dc = $rows['date_op'];
        
        $stmt_t = $dbConnection->prepare('select id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, prio, last_update from tickets where id=:hn');
        $stmt_t->execute(array(
            ':hn' => $t_id
        ));
        $ticket = $stmt_t->fetch(PDO::FETCH_ASSOC);
        
        $t_msg = $ticket['msg'];
        
        $stmt_comment = $dbConnection->prepare('select comment_text from comments where t_id=:hn and dt=:do');
        $stmt_comment->execute(array(
            ':hn' => $t_id,
            ':do' => $t_dc
        ));
        $ticket_comment = $stmt_comment->fetch(PDO::FETCH_ASSOC);
        
        $tc = $ticket_comment['comment_text'];
        
        $user2id = get_ticket_val_by_hash('user_to_id', get_ticket_hash_by_id($t_id));
        $unit2id = get_ticket_val_by_hash('unit_id', get_ticket_hash_by_id($t_id));
        
        if ($user2id <> 0) {
            $to_text = name_of_user_ret($user2id);
        }
        if ($user2id == 0) {
            $to_text = view_array(get_unit_name_return($unit2id));
        }
        

        /*
        if (substr($tc, 0, 6) === "[file:") {
            
            $arr_hash = explode(":", $tc);
            $f_hash = substr($arr_hash[1], 0, -1);
            
            //$hn=get_ticket_id_by_hash($f_hash);
            $stmt2 = $dbConnection->prepare('SELECT original_name, file_size,file_type,file_ext FROM files where file_hash=:tid');
            $stmt2->execute(array(
                ':tid' => $f_hash
            ));
            $file_arr = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            $ct = '<div class=\' \' style=\'margin-bottom: 5px;\'><em><small>' . lang('EXT_attach_file') . '</small> <br></em>';
            
            $fts = array(
                'image/jpeg',
                'image/gif',
                'image/png'
            );
            
            if (in_array($file_arr['file_type'], $fts)) {
                
                $ct.= ' <small><a class="text-light-blue" href=\'' . $CONF['hostname'] . 'sys/download.php?' . $f_hash . '\'><img style=\'max-height:100px;\' src=\'' . $CONF['hostname'] . 'upload_files/' . $f_hash . '.' . $file_arr['file_ext'] . '\'></a>  </small>';
            } 
            else {
                $ct.= get_file_icon($f_hash) . ' <small><a class="text-light-blue" href=\'' . $CONF['hostname'] . 'sys/download.php?' . $f_hash . '\'>' . $file_arr['original_name'] . '</a> ' . round(($file_arr['file_size'] / (1024 * 1024)) , 2) . ' Mb </small>';
            }
            $ct.= '</div>';
        }

        */ 

$fl=strpos(make_html($tc, true),'[file:');

if ($fl !== false) {
    


$cline=substr(make_html($tc, true), strpos(make_html(tc, true),'[file:'));

$cline=rtrim($cline, "]");



$cline_res=explode(":", $cline);

$some_arr=explode(",", $cline_res[1]);

$ct = substr(make_html($tc, true), 0, strpos(make_html($tc, true),'[file:'));
$ct .= '<div class=\'text-muted\' style=\'margin-bottom: 5px;\'><em><small>' . lang('EXT_attach_file') . '</small> <br></em>';

foreach ($some_arr as $f_hash) {

$stmt2 = $dbConnection->prepare('SELECT original_name, file_size,file_type,file_ext FROM files where file_hash=:tid');
            $stmt2->execute(array(
                ':tid' => $f_hash
            ));
$file_arr = $stmt2->fetch(PDO::FETCH_ASSOC);

$fts = array(
                'image/jpeg',
                'image/gif',
                'image/png'
            );
            
            if (in_array($file_arr['file_type'], $fts)) {
                
                $ct.= ' <small><a class=\'fancybox\' href=\'' . $CONF['hostname'] . 'upload_files/' . $f_hash . '.' . $file_arr['file_ext'] . '\'><img style=\'max-height:100px;\' src=\'' . $CONF['hostname'] . 'upload_files/' . $f_hash . '.' . $file_arr['file_ext'] . '\'></a>  </small> ';
            } 
            else {
                $ct.= get_file_icon($f_hash) . ' <small><a class="text-light-blue" href=\'' . $CONF['hostname'] . 'action?mode=download_file&file=' . $f_hash . '\'>' . $file_arr['original_name'] . '</a> ' . round(($file_arr['file_size'] / (1024 * 1024)) , 2) . ' Mb </small><br>';
            }

    # code...
}
$ct.= '</div>';



}

        else {
            $ct = make_html($tc, 'no');
        }
        
        array_push($news_arr, array(
            
            'msg' => $rows['msg'],
            't_dc' => $t_dc,
            'initUserHash' => get_user_hash_by_id($init_user) ,
            'initUserName' => name_of_user_ret($init_user) ,
            'NEWS_action_lock' => lang('NEWS_action_lock') ,
            'ticketHash' => get_ticket_hash_by_id($t_id) ,
            'ticketID' => $t_id,
            'htmlMSG' => make_html(get_ticket_val_by_hash('msg', get_ticket_hash_by_id($t_id)) , 'no') ,
            
            'today' => date('d-m-Y') ,
            're_date' => date('d-m-Y', strtotime($rows['date_op'])) ,
            'NEWS_today' => lang('NEWS_today') ,
            'date_op' => $rows['date_op'],
            'NEWS_action_unlock' => lang('NEWS_action_unlock') ,
            'EXT_news_view_t' => lang('EXT_news_view_t') ,
            'NEWS_action_ok' => lang('NEWS_action_ok') ,
            'NEWS_action_no_ok' => lang('NEWS_action_no_ok') ,
            'NEWS_action_no_ok2' => lang('NEWS_action_no_ok2') ,
            'NEWS_action_refer' => lang('NEWS_action_refer') ,
            'mail_msg_ticket_to_ext' => lang('mail_msg_ticket_to_ext') ,
            'to_text' => $to_text,
            'NEWS_action_comment' => lang('NEWS_action_comment') ,
            'NEWS_text_comment' => lang('NEWS_text_comment') ,
            'ct' => $ct,
            'NEWS_action_create' => lang('NEWS_action_create') ,
            'FILES_ticket' => lang('FILES_ticket') ,
            'NEWS_action_ticket_arch' => lang('NEWS_action_ticket_arch')
        ));
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
        $template = $twig->loadTemplate('news.view.tmpl');
        
        // передаём в шаблон переменные и значения
        // выводим сформированное содержание
        echo $template->render(array(
            'NAVBAR_news' => lang('NAVBAR_news') ,
            'DASHBOARD_last_news' => lang('DASHBOARD_last_news') ,
            'hostname' => $CONF['hostname'],
            'name_of_firm' => $CONF['name_of_firm'],
            'news_arr' => $news_arr,
        ));
    }
    catch(Exception $e) {
        die('ERROR: ' . $e->getMessage());
    }
    
    include ("footer.inc.php");
?>

<?php
} 
else {
    include '../auth.php';
}
?>