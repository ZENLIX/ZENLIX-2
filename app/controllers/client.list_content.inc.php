<?php
session_start();

//error_reporting(0);
include_once ("../../functions.inc.php");
if ($CONF_HD['debug_mode'] == false) {
    error_reporting(E_ALL ^ E_NOTICE);
    error_reporting(0);
}

if (isset($_POST['menu'])) {
    
    if ($_POST['menu'] == 'out') {
        
        $page = ($_POST['page']);
        $perpage = '10';
        
        if (isset($_SESSION['hd.rustem_list_out'])) {
            $perpage = $_SESSION['hd.rustem_list_out'];
        }
        $start_pos = ($page - 1) * $perpage;
        $user_id = $_SESSION['helpdesk_user_id'];
        


        /*

проверить все units
если в каком-то из них есть main_user
то вывести все заявки где клиенты равны
    where (user_init_id IN (' . $in_query . ') and client_id) OR (client_id=:cid2)

        */


$stmt = $dbConnection->prepare('SELECT id from units where main_user=:u');
$stmt->execute(array(
':u'=>$user_id
    ));
$res_check = $stmt->fetch(PDO::FETCH_ASSOC);;



if (!empty($res_check)) {
$mu_status=true;

$p = get_clients_from_units($res_check['id']);
//print_r($p);
            foreach ($p as $key => $value) {
                $in_query = $in_query . ' :val_' . $key . ', ';
            }
            
            $in_query = substr($in_query, 0, -2);
            foreach ($p as $key => $value) {
                $vv[":val_" . $key] = $value;
            }
////
            foreach ($p as $key => $value) {
                $in_query2 = $in_query2 . ' :val2_' . $key . ', ';
            }
            
            $in_query2 = substr($in_query2, 0, -2);
            foreach ($p as $key => $value) {
                $vv2[":val2_" . $key] = $value;
            }


        $stmt = $dbConnection->prepare('SELECT 
        id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read,lock_by, ok_by, prio 
        from tickets where user_init_id IN (' . $in_query . ') OR client_id IN (' . $in_query2 . ')
           order by ok_by asc, prio desc, id desc limit :start_pos, :perpage');
        $paramss=array(
            ':start_pos' => $start_pos,
            ':perpage' => $perpage
        );
        $stmt->execute(array_merge($vv,$vv2, $paramss));
        //$stmt->execute();
        
        $res1 = $stmt->fetchAll();

        $aha = get_total_pages('clients_main', $_SESSION['helpdesk_user_id']);
}

else if (empty($res_check)) {
$mu_status=false;

        $stmt = $dbConnection->prepare('SELECT 
        id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read,lock_by, ok_by, prio 
        from tickets where (user_init_id=:user_id and client_id=:cid) or (client_id=:cid2)
        order by ok_by asc, prio desc, id desc limit :start_pos, :perpage');
        $stmt->execute(array(
            ':user_id' => $_SESSION['helpdesk_user_id'],
            ':cid' => $_SESSION['helpdesk_user_id'],
            ':cid2' => $_SESSION['helpdesk_user_id'],
            ':start_pos' => $start_pos,
            ':perpage' => $perpage
        ));
        
        $res1 = $stmt->fetchAll();
        $aha = get_total_pages('clients', $_SESSION['helpdesk_user_id']);
    }
        
        
        
        $ticket_arr = array();
        foreach ($res1 as $row) {
            $lb = $row['lock_by'];
            $ob = $row['ok_by'];
            
            ////////////////////////////Раскрашивает и подписывает кнопки/////////////////////////////////////////////////////////////////
            if ($row['is_read'] == "0") {
                $style = "bold_for_new";
            }
            if ($row['is_read'] <> "0") {
                $style = "";
            }
            if ($row['status'] == "1") {
                $ob_text = "<i class=\"fa fa-check-circle-o\"></i>";
                $ob_status = "unok";
                $ob_tooltip = lang('t_list_a_nook');
                $style = "success";
                
                if ($lb <> "0") {
                    $lb_text = "<i class=\"fa fa-lock\"></i>";
                    $lb_status = "unlock";
                    $lb_tooltip = lang('t_list_a_unlock');
                }
                if ($lb == "0") {
                    $lb_text = "<i class=\"fa fa-unlock\"></i>";
                    $lb_status = "lock";
                    $lb_tooltip = lang('t_list_a_lock');
                }
            }
            
            if ($row['status'] == "0") {
                $ob_text = "<i class=\"fa fa-circle-o\"></i>";
                $ob_status = "ok";
                $ob_tooltip = lang('t_list_a_ok');
                if ($lb <> "0") {
                    $lb_text = "<i class=\"fa fa-lock\"></i>";
                    $lb_status = "unlock";
                    $lb_tooltip = lang('t_list_a_unlock');
                    if ($lb == $user_id) {
                        $style = "warning";
                    }
                    if ($lb <> $user_id) {
                        $style = "active";
                    }
                }
                
                if ($lb == "0") {
                    $lb_text = "<i class=\"fa fa-unlock\"></i>";
                    $lb_status = "lock";
                    $lb_tooltip = lang('t_list_a_lock');
                }
            }
            
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            ////////////////////////////Показывает приоритет//////////////////////////////////////////////////////////////
            $prio = "<span class=\"label label-info\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . lang('t_list_a_p_norm') . "\"><i class=\"fa fa-minus\"></i></span>";
            
            if ($row['prio'] == "0") {
                $prio = "<span class=\"label label-primary\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . lang('t_list_a_p_low') . "\"><i class=\"fa fa-arrow-down\"></i></span>";
            }
            
            if ($row['prio'] == "2") {
                $prio = "<span class=\"label label-danger\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . lang('t_list_a_p_high') . "\"><i class=\"fa fa-arrow-up\"></i></span>";
            }
            
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            ////////////////////////////Показывает labels//////////////////////////////////////////////////////////////
            if ($row['status'] == 1) {
                $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('t_list_a_oko') . " " . nameshort(name_of_user_ret_nolink($ob)) . "</span>";
                $t_ago = get_date_ok($row['date_create'], $row['id']);
            }
            if ($row['status'] == 0) {
                $t_ago = $row['date_create'];
                if ($lb <> 0) {
                    
                    if ($lb == $user_id) {
                        $st = "<span class=\"label label-warning\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_i') . "</span>";
                    }
                    
                    if ($lb <> $user_id) {
                        $st = "<span class=\"label label-default\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_u') . " " . nameshort(name_of_user_ret_nolink($lb)) . "</span>";
                    }
                }
                if ($lb == 0) {
                    $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('t_list_a_hold') . "</span>";
                }
            }
            
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            ////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
            if ($row['user_to_id'] <> 0) {
                $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['user_to_id'])) . "</div>";
            }
            if ($row['user_to_id'] == 0) {
                $to_text = "<strong data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . view_array(get_unit_name_return($row['unit_id'])) . "\">" . lang('t_list_a_all') . "</strong>";
            }
            
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            ob_start();
            cutstr(make_html($row['subj'], 'no'));
            $cut_subj = ob_get_contents();
            ob_end_clean();
            
            array_push($ticket_arr, array(
                
                'id' => $row['id'],
                'style' => $style,
                'init_fio' => nameshort(name_of_user_ret($row['user_init_id'])) ,
                'prio' => $prio,
                'muclass' => $muclass,
                'subj' => make_html($row['subj'], 'no') ,
                'msg' => str_replace('"', "", make_html(strip_tags($row['msg']) , 'no')) ,
                'hash_name' => $row['hash_name'],
                'subj_cut' => $cut_subj,
                'date_create' => $row['date_create'],
                't_ago' => $t_ago,
                'client' => get_user_val_by_id($row['client_id'], 'fio') ,
                'to_text' => $to_text,
                'st' => $st
            ));
        }
        
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
            $template = $twig->loadTemplate('client.list_content.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'hostname' => $CONF['hostname'],
                'name_of_firm' => $CONF['name_of_firm'],
                'aha' => $aha,
                'MSG_no_records' => lang('MSG_no_records') ,
                'get_total_pages' => get_total_pages('clients', $_SESSION['helpdesk_user_id']) ,
                'mu_status'=>$mu_status,
                't_LIST_init' => lang('t_LIST_init') ,
                't_LIST_prio' => lang('t_LIST_prio') ,
                't_LIST_subj' => lang('t_LIST_subj') ,
                't_LIST_create' => lang('t_LIST_create') ,
                't_LIST_ago' => lang('t_LIST_ago') ,
                't_LIST_to' => lang('t_LIST_to') ,
                't_LIST_worker' => lang('t_LIST_worker') ,
                't_LIST_status' => lang('t_LIST_status') ,
                'ticket_arr' => $ticket_arr
            ));
        } 
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
    }
}
?>
