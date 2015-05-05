<?php
session_start();
error_reporting(0);
include_once ("../functions.inc.php");













if (isset($_POST['menu'])) {
    
    if ($_POST['menu'] == 'out') {
        
        $page = ($_POST['page']);
        $perpage = '10';
        if (isset($_SESSION['hd.rustem_list_out'])) {
            $perpage = $_SESSION['hd.rustem_list_out'];
        }
        $start_pos = ($page - 1) * $perpage;
        $user_id = id_of_user($_SESSION['helpdesk_user_login']);
        $ps = priv_status($user_id);
        


/*

2 boss
0 head
1 user


if boss - all created tickets
if head - user deps created tickets
if user - only his tickets

*/



        $order_l="id desc";
        $order_l_var="";
        if (isset($_SESSION['zenlix_list_out_sort'])) {

            switch ($_SESSION['zenlix_list_out_sort']) {
                case 'id':
                    $order_l="id";
                    break;
                case 'prio':
                    $order_l="prio";
                    break;
                case 'subj':
                    $order_l="subj";
                    break;
                case 'client_id':
                    $order_l="client_id";
                    break;
                case 'date_create':
                    $order_l="date_create";
                    break;
                case 'date_create':
                    $order_l="date_create";
                    break;
                case 'user_init_id':
                    $order_l="user_init_id";
                    break;
                     default:
                     $order_l="id desc";
        }






    }

    if (isset($_SESSION['zenlix_list_out_sort_var'])) {
             switch ($_SESSION['zenlix_list_out_sort_var']) {

                case 'asc':
                    $order_l_var="asc";
                    break;
                case 'desc':
                    $order_l_var="desc";
                    break;

             }

    }

    $order_l=$order_l." ".$order_l_var;



        






            if ($ps == "2") {




if (isset($_SESSION['hd.rustem_sort_out'])) {
            if ($_SESSION['hd.rustem_sort_out'] == "ok") {
                $stmt = $dbConnection->prepare('SELECT * from tickets where arch=:n and status=:s limit :start_pos, :perpage');
                $stmt->execute(array(':s' => '1', ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
            } else if ($_SESSION['hd.rustem_sort_out'] == "free") {
                $stmt = $dbConnection->prepare('SELECT * from tickets where arch=:n and lock_by=:lb and status=:s limit :start_pos, :perpage');
                $stmt->execute(array( ':lb' => '0', ':s' => '0', ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
            } else if ($_SESSION['hd.rustem_sort_out'] == "ilock") {
                $stmt = $dbConnection->prepare('SELECT * from tickets where arch=:n and lock_by=:lb limit :start_pos, :perpage');
                $stmt->execute(array( ':lb' => $user_id, ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
            } else if ($_SESSION['hd.rustem_sort_out'] == "lock") {
                $stmt = $dbConnection->prepare('SELECT * from tickets where arch=:n and (lock_by<>:lb and lock_by<>0) and (status=0) limit :start_pos, :perpage');
                $stmt->execute(array(':lb' => $user_id, ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
            }
        }
               else if (!isset($_SESSION['hd.rustem_sort_out'])) {
            
            $stmt = $dbConnection->prepare('SELECT * from tickets 
        where arch=:n 
        order by '.$order_l.' limit :start_pos, :perpage');
            $stmt->execute(array(':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
        }
             }


            else if ($ps == "0") {

/*

получить список всех пользователей всех отелов и отсеять дуликаты

получить список id всех пользователей где отдел = 



*/

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
                $stmt = $dbConnection->prepare('SELECT * from tickets where user_init_id IN (' . $in_query . ') and arch=:n and status=:s limit :start_pos, :perpage');

                $paramss=array(':s' => '1', ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                $stmt->execute(array_merge($vv, $paramss));

                    
            } else if ($_SESSION['hd.rustem_sort_out'] == "free") {
                $stmt = $dbConnection->prepare('SELECT * from tickets where user_init_id IN (' . $in_query . ') and arch=:n and lock_by=:lb and status=:s limit :start_pos, :perpage');

                $paramss=array( ':lb' => '0', ':s' => '0', ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                $stmt->execute(array_merge($vv, $paramss));
                    



            } else if ($_SESSION['hd.rustem_sort_out'] == "ilock") {
                $stmt = $dbConnection->prepare('SELECT * from tickets where user_init_id IN (' . $in_query . ') and arch=:n and lock_by=:lb limit :start_pos, :perpage');

                $paramss=array( ':lb' => $user_id, ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                $stmt->execute(array_merge($vv, $paramss));
                    


            } else if ($_SESSION['hd.rustem_sort_out'] == "lock") {
                $stmt = $dbConnection->prepare('SELECT * from tickets where user_init_id IN (' . $in_query . ') and arch=:n and (lock_by<>:lb and lock_by<>0) and (status=0) limit :start_pos, :perpage');

                $paramss=array(':lb' => $user_id, ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                $stmt->execute(array_merge($vv, $paramss));


            }
        }
               else if (!isset($_SESSION['hd.rustem_sort_out'])) {
            
            $stmt = $dbConnection->prepare('SELECT * from tickets 
        where user_init_id IN (' . $in_query . ') and arch=:n 
        order by '.$order_l.' limit :start_pos, :perpage');
            $paramss=array(':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
            $stmt->execute(array_merge($vv, $paramss));
        }


             }

            else if ($ps == "1") {



if (isset($_SESSION['hd.rustem_sort_out'])) {
            if ($_SESSION['hd.rustem_sort_out'] == "ok") {
                $stmt = $dbConnection->prepare('SELECT * from tickets where user_init_id=:user_id and arch=:n and status=:s limit :start_pos, :perpage');

                $stmt->execute(array(':user_id' => $user_id, ':s' => '1', ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
            } else if ($_SESSION['hd.rustem_sort_out'] == "free") {
                $stmt = $dbConnection->prepare('SELECT * from tickets where user_init_id=:user_id and arch=:n and lock_by=:lb and status=:s limit :start_pos, :perpage');
                $stmt->execute(array(':user_id' => $user_id, ':lb' => '0', ':s' => '0', ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
            } else if ($_SESSION['hd.rustem_sort_out'] == "ilock") {
                $stmt = $dbConnection->prepare('SELECT * from tickets where user_init_id=:user_id and arch=:n and lock_by=:lb limit :start_pos, :perpage');
                $stmt->execute(array(':user_id' => $user_id, ':lb' => $user_id, ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
            } else if ($_SESSION['hd.rustem_sort_out'] == "lock") {
                $stmt = $dbConnection->prepare('SELECT * from tickets where user_init_id=:user_id and arch=:n and (lock_by<>:lb and lock_by<>0) and (status=0) limit :start_pos, :perpage');
                $stmt->execute(array(':user_id' => $user_id, ':lb' => $user_id, ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
            }
        }
               else if (!isset($_SESSION['hd.rustem_sort_out'])) {
            
            $stmt = $dbConnection->prepare('SELECT * from tickets 
        where user_init_id=:user_id and arch=:n 
        order by '.$order_l.' limit :start_pos, :perpage');
            $stmt->execute(array(':user_id' => $user_id, ':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
        }

        }




        
        

        
        //or id in (LIST TICKET_ID)
        
        $res1 = $stmt->fetchAll();
        
        $aha = get_total_pages('out', $user_id);
         





            ?>


<?php

if (!isset($_SESSION['hd.rustem_sort_out'])) {
        if (isset($_SESSION['zenlix_list_out_sort'])) {


         if (isset($_SESSION['zenlix_list_out_sort_var'])) {

            if ($_SESSION['zenlix_list_out_sort_var'] == "asc") { $r=" <i class='fa fa-sort-asc'></i>";}
            if ($_SESSION['zenlix_list_out_sort_var'] == "desc") { $r=" <i class='fa fa-sort-desc'></i>";}
         }


            switch ($_SESSION['zenlix_list_out_sort']) {
                    case 'id':
                        $sort_type_start['id']="<mark>";
                        $sort_type_stop['id']=$r."</mark>";
                    break;
                    case 'prio':
                        $sort_type_start['prio']="<mark>";
                        $sort_type_stop['prio']=$r."</mark>";
                    break;
                    case 'subj':
                        $sort_type_start['subj']="<mark>";
                        $sort_type_stop['subj']=$r."</mark>";
                    break;
                    case 'client_id':
                        $sort_type_start['client_id']="<mark>";
                        $sort_type_stop['client_id']=$r."</mark>";
                    break;
                    case 'date_create':
                        $sort_type_start['date_create']="<mark>";
                        $sort_type_stop['date_create']=$r."</mark>";
                    break;
                    case 'user_init_id':
                        $sort_type_start['user_init_id']="<mark>";
                        $sort_type_stop['user_init_id']=$r."</mark>";
                    break;
            }
        }

    }




$ar_res=array();

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
        
        //Start output buffer
        cutstr(make_html($row['subj'], 'no'));
        $cut_subj = ob_get_contents();
        
        //Grab output
        ob_end_clean();




array_push($ar_res, array(

    'id'=>$row['id'],
    'style'=>$style,
    'prio'=>$prio,
    'muclass'=>$muclass,
    'subj'=>make_html($row['subj'], 'no'),
    'msg'=>str_replace('"', "", cutstr_help_ret(make_html(strip_tags($row['msg'])), 'no')),
    'hashname'=>$row['hash_name'],
    'cut_subj'=>$cut_subj,
    'get_user_hash_by_id_client'=>get_user_hash_by_id($row['client_id']),
    'client'=>get_user_val_by_id($row['client_id'], 'fio'),
    'date_create'=>$row['date_create'],
    't_ago'=>$t_ago,
    'get_deadline_label'=>get_deadline_label($row['id']),
    'name_of_user_ret'=>nameshort(name_of_user_ret($row['user_init_id'])),
    'to_text'=>$to_text,
    'st'=>$st));


}
$basedir = dirname(dirname(__FILE__)); 
            ////////////
    try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/views');
            
            // инициализируем Twig
            $twig = new Twig_Environment($loader);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('list_content_out.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'get_total_pages_out' => get_total_pages('out', $user_id),
                'sort_type_start_id'=>$sort_type_start['id'],
                'sort_type_stop_id'=>$sort_type_stop['id'],
                'sort_type_start_prio'=>$sort_type_start['prio'],
                't_LIST_prio'=>lang('t_LIST_prio'),
                'sort_type_stop_prio'=>$sort_type_stop['prio'],
                'sort_type_start_subj'=>$sort_type_start['subj'],
                't_LIST_subj'=>lang('t_LIST_subj'),
                'sort_type_stop_subj'=>$sort_type_stop['subj'],
                'sort_type_start_client_id'=>$sort_type_start['client_id'],
                't_LIST_worker'=>lang('t_LIST_worker'),
                'sort_type_stop_client_id'=>$sort_type_stop['client_id'],
                'sort_type_start_date_create'=>$sort_type_start['date_create'],
                't_LIST_create'=>lang('t_LIST_create'),
                'sort_type_stop_date_create'=>$sort_type_stop['date_create'],
                't_LIST_ago'=>lang('t_LIST_ago'),
                'sort_type_start_user_init_id'=>$sort_type_start['user_init_id'],
                't_LIST_init'=>lang('t_LIST_init'),
                'sort_type_stop_user_init_id'=>$sort_type_stop['user_init_id'],
                't_LIST_to'=>lang('t_LIST_to'),
                't_LIST_status'=>lang('t_LIST_status'),
                'ar_res'=>$ar_res,
                'aha'=>$aha,
                'MSG_no_records'=>lang('MSG_no_records')


            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }

    }
    
    if ($_POST['menu'] == 'find') {
        
        $z = ($_GET['t']);
        
        //echo($z);
        $user_id = id_of_user($_SESSION['helpdesk_user_login']);
        $unit_user = unit_of_user($user_id);
        $priv_val = priv_status($user_id);
        
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
        
        if ($priv_val == 0) {
            
            $stmt = $dbConnection->prepare('SELECT
    a.id, a.user_init_id, a.user_to_id, a.date_create, a.subj, a.msg, a.client_id, a.unit_id, a.status, a.hash_name, 
    a.is_read, a.lock_by, a.ok_by, a.prio, a.last_update, a.arch, b.comment_text, b.t_id
        from tickets as a LEFT JOIN  comments as b ON a.id = b.t_id
        where ((a.unit_id IN (' . $in_query . ') and a.arch=:n) or (a.user_init_id=:user_id)) and 
        (a.id=:z or a.subj like :z1 or a.msg like :z2 or b.comment_text like :z3)group by a.id limit 10');
            
            $paramss = array(':n' => '0', ':user_id' => $user_id, ':z' => $z, ':z1' => '%' . $z . '%', ':z2' => '%' . $z . '%', ':z3' => '%' . $z . '%');
            $stmt->execute(array_merge($vv, $paramss));
            $res1 = $stmt->fetchAll();
        } else if ($priv_val == 1) {
            
            $stmt = $dbConnection->prepare('SELECT
    a.id, a.user_init_id, a.user_to_id, a.date_create, a.subj, a.msg, a.client_id, a.unit_id, a.status, a.hash_name, 
    a.is_read, a.lock_by, a.ok_by, a.prio, a.last_update, a.arch, b.comment_text, b.t_id
        from tickets as a LEFT JOIN  comments as b ON a.id = b.t_id
    where (((find_in_set(:user_id,a.user_to_id) ) or
    (find_in_set(:n,a.user_to_id) and a.unit_id IN (' . $in_query . ') )) or a.user_init_id=:user_id2) and 
    (a.id=:z or a.subj like :z1 or a.msg like :z2 or b.comment_text like :z3) group by a.id limit 10');
            
            $paramss = array(':n' => '0', ':user_id' => $user_id, ':z' => $z, ':z1' => '%' . $z . '%', ':z2' => '%' . $z . '%', ':z3' => '%' . $z . '%', ':user_id2' => $user_id);
            $stmt->execute(array_merge($vv, $paramss));
            $res1 = $stmt->fetchAll();
        } else if ($priv_val == 2) {
            
            $stmt = $dbConnection->prepare('SELECT
    a.id, a.user_init_id, a.user_to_id, a.date_create, a.subj, a.msg, a.client_id, a.unit_id, a.status, a.hash_name, 
    a.is_read, a.lock_by, a.ok_by, a.prio, a.last_update, a.arch, b.comment_text, b.t_id
    from tickets as a LEFT JOIN  comments as b ON a.id = b.t_id
        where a.id=:z or a.subj like :z1 or a.msg like :z2 or b.comment_text like :z3 group by a.id limit 10');
            
            $stmt->execute(array(':z' => $z, ':z1' => '%' . $z . '%', ':z2' => '%' . $z . '%', ':z3' => '%' . $z . '%'));
            $res1 = $stmt->fetchAll();
        }
        
$ar_res=array();

if (empty($res1)) {$aha="0"; }
else if (!empty($res1)) {$aha="1"; }

            foreach ($res1 as $row) {
                $lb = $row['lock_by'];
                $ob = $row['ok_by'];
                $arch = $row['arch'];
                
                $user_id_z = id_of_user($_SESSION['helpdesk_user_login']);
                $unit_user_z = unit_of_user($user_id_z);
                $status_ok_z = $row['status'];
                $ok_by_z = $row['ok_by'];
                $lock_by_z = $row['lock_by'];
                
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
                
                ////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
                if ($row['user_to_id'] <> 0) {
                    $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['user_to_id'])) . "</div>";
                }
                if ($row['user_to_id'] == 0) {
                    $to_text = "<strong data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . view_array(get_unit_name_return($row['unit_id'])) . "\">" . lang('t_list_a_all') . "</strong>";
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
                
                /////////если пользователь///////////////////////////////////////////////////////////////////////////////////////////
                if ($priv_val == 1) {
                    
                    //ЗАявка не выполнена ИЛИ выполнена мной
                    //ЗАявка не заблокирована ИЛИ заблокирована мной
                    $lo == "no";
                    if (($status_ok_z == 0) || (($status_ok_z == 1) && ($ok_by_z == $user_id_z))) {
                        if (($lock_by_z == 0) || ($lock_by_z == $user_id_z)) {
                            $lo == "yes";
                        }
                    }
                    if ($lo == "yes") {
                        $lock_st = "";
                        $muclass = "";
                    } else if ($lo == "no") {
                        $lock_st = "disabled=\"disabled\"";
                        $muclass = "text-muted";
                    }
                }
                
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                
                /////////если нач отдела/////////////////////////////////////////////////////////////////////////////////////////////
                else if ($priv_val == 0) {
                    $lock_st = "";
                    $muclass = "";
                }
                
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                
                //////////главный админ//////////////////////////////////////////////////////////////////////////////////////////////
                else if ($priv_val == 2) {
                    $lock_st = "";
                    $muclass = "";
                }
                
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                
                if ($arch == "1") {
                    $st = "<span class=\"label label-default\">" . lang('t_list_a_arch') . " </span>";
                }
                if ($arch == "0") {
                    if ($row['status'] == 1) {
                        $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('t_list_a_oko') . " " . nameshort(name_of_user_ret_nolink($ob)) . "</span>";
                    }
                    if ($row['status'] == 0) {
                        if ($lb <> 0) {
                            $st = "<span class=\"label label-warning\"><i class=\"fa fa-gavel\"></i> " . lang('t_list_a_lock_u') . " " . nameshort(name_of_user_ret_nolink($lb)) . "</span>";
                        }
                        if ($lb == 0) {
                            $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('t_list_a_hold') . "</span>";
                        }
                    }
                }
                if ($row['status'] == 1) {
                    $t_ago = get_date_ok($row['date_create'], $row['id']);
                }
                if ($row['status'] == 0) {
                    $t_ago = $row['date_create'];
                }

            
            
        ob_start();
        
        //Start output buffer
        cutstr(make_html($row['subj'], 'no'));
        $cut_subj = ob_get_contents();
        
        //Grab output
        ob_end_clean();




array_push($ar_res, array(

    'id'=>$row['id'],
    'style'=>$style,
    'prio'=>$prio,
    'muclass'=>$muclass,
    'subj'=>make_html($row['subj'], 'no'),
    'msg'=>str_replace('"', "", cutstr_help_ret(make_html(strip_tags($row['msg'])), 'no')),
    'hashname'=>$row['hash_name'],
    'cut_subj'=>$cut_subj,
    'get_user_hash_by_id_client'=>get_user_hash_by_id($row['client_id']),
    'client'=>get_user_val_by_id($row['client_id'], 'fio'),
    'date_create'=>$row['date_create'],
    't_ago'=>$t_ago,
    'get_deadline_label'=>get_deadline_label($row['id']),
    'name_of_user_ret'=>nameshort(name_of_user_ret($row['user_init_id'])),
    'to_text'=>$to_text,
    'st'=>$st));

            }

$basedir = dirname(dirname(__FILE__)); 
            ////////////
    try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/views');
            
            // инициализируем Twig
            $twig = new Twig_Environment($loader);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('list_content_find.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                't_list_a_top' => lang('t_list_a_top'),
                't_LIST_prio'=>lang('t_LIST_prio'),
                't_LIST_subj'=>lang('t_LIST_subj'),
                't_LIST_worker'=>lang('t_LIST_worker'),
                't_LIST_create'=>lang('t_LIST_create'),
                't_LIST_ago'=>lang('t_LIST_ago'),
                't_LIST_init'=>lang('t_LIST_init'),
                't_LIST_to'=>lang('t_LIST_to'),
                't_LIST_status'=>lang('t_LIST_status'),
                'ar_res'=>$ar_res,
                'aha'=>$aha,
                'MSG_no_records'=>lang('MSG_no_records')


            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
        
    }
     
    if ($_POST['menu'] == 'in') {
        
        $page = ($_POST['page']);
        $ar_res=array();
        $perpage = '10';
        if (isset($_SESSION['hd.rustem_list_in'])) {
            $perpage = $_SESSION['hd.rustem_list_in'];
        }
        
        $start_pos = ($page - 1) * $perpage;
        
        $user_id = id_of_user($_SESSION['helpdesk_user_login']);
        $unit_user = unit_of_user($user_id);
        $priv_val = priv_status($user_id);
        
        //$unit_user = 1,2,3
        $units = explode(",", $unit_user);
        
        //$units = array[1,2,3]
        $units = implode("', '", $units);
        
        $ee = explode(",", $unit_user);
        foreach ($ee as $key => $value) {
            $in_query = $in_query . ' :val_' . $key . ', ';
        }
        $in_query = substr($in_query, 0, -2);
        foreach ($ee as $key => $value) {
            $vv[":val_" . $key] = $value;
        }
        


        $order_l="ok_by asc, prio desc, id desc";
        $order_l_var="";
        if (isset($_SESSION['zenlix_list_in_sort'])) {

            switch ($_SESSION['zenlix_list_in_sort']) {
                case 'id':
                    $order_l="id";
                    break;
                case 'prio':
                    $order_l="prio";
                    break;
                case 'subj':
                    $order_l="subj";
                    break;
                case 'client_id':
                    $order_l="client_id";
                    break;
                case 'date_create':
                    $order_l="date_create";
                    break;
                case 'date_create':
                    $order_l="date_create";
                    break;
                case 'user_init_id':
                    $order_l="user_init_id";
                    break;
                     default:
                     $order_l="ok_by asc, prio desc, id desc";
        }






    }

    if (isset($_SESSION['zenlix_list_in_sort_var'])) {
             switch ($_SESSION['zenlix_list_in_sort_var']) {

                case 'asc':
                    $order_l_var="asc";
                    break;
                case 'desc':
                    $order_l_var="desc";
                    break;

             }

    }

    $order_l=$order_l." ".$order_l_var;

        if ($priv_val == 0) {
            
            /*
            if (isset($_SESSION['hd.rustem_sort_in'])) {
            if ($_SESSION['hd.rustem_sort_in'] == "ok"){}
            else if ($_SESSION['hd.rustem_sort_in'] == "ilock"){}
            else if ($_SESSION['hd.rustem_sort_in'] == "lock"){}
            }
            
            if (!isset($_SESSION['hd.rustem_sort_in'])) {}
            
            */
            
            //нач отдела
            /*
            выбрать все заявки, которые состоят с моих отделах
            */
             
            if (isset($_SESSION['hd.rustem_sort_in'])) {
                
                if ($_SESSION['hd.rustem_sort_in'] == "ok") {
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where unit_id IN (' . $in_query . ')  and arch=:n and status=:s
                            limit :start_pos, :perpage');
                    $paramss = array(':n' => '0', ':s' => '1', ':start_pos' => $start_pos, ':perpage' => $perpage);
                    $stmt->execute(array_merge($vv, $paramss));
                } else if ($_SESSION['hd.rustem_sort_in'] == "free") {
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where unit_id IN (' . $in_query . ')  and arch=:n and status=:s and lock_by=:lb
                            limit :start_pos, :perpage');
                    $paramss = array(':n' => '0', ':s' => '0', ':lb' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                    $stmt->execute(array_merge($vv, $paramss));
                } else if ($_SESSION['hd.rustem_sort_in'] == "ilock") {
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where unit_id IN (' . $in_query . ')  and arch=:n and lock_by=:lb
                            limit :start_pos, :perpage');
                    $paramss = array(':n' => '0', ':lb' => $user_id, ':start_pos' => $start_pos, ':perpage' => $perpage);
                    $stmt->execute(array_merge($vv, $paramss));
                } else if ($_SESSION['hd.rustem_sort_in'] == "lock") {
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where unit_id IN (' . $in_query . ')  and arch=:n and (lock_by<>:lb and lock_by<>0) and (status=0)
                            limit :start_pos, :perpage');
                    
                    $paramss = array(':n' => '0', ':lb' => $user_id, ':start_pos' => $start_pos, ':perpage' => $perpage);
                    $stmt->execute(array_merge($vv, $paramss));
                }
            }
            
            if (!isset($_SESSION['hd.rustem_sort_in'])) {
                $stmt = $dbConnection->prepare('SELECT * from tickets
                            where unit_id IN (' . $in_query . ')  and arch=:n
                            order by '.$order_l.'
                            limit :start_pos, :perpage');
                
                $paramss = array(':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                $stmt->execute(array_merge($vv, $paramss));
            }
            
            $res1 = $stmt->fetchAll();
        } else if ($priv_val == 1) {
            
            if (isset($_SESSION['hd.rustem_sort_in'])) {
                if ($_SESSION['hd.rustem_sort_in'] == "ok") {
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where ((find_in_set(:user_id,user_to_id) and arch=:n) or
                            (find_in_set(:n1,user_to_id) and unit_id IN (' . $in_query . ') and arch=:n2)) and status=:s
                            limit :start_pos, :perpage');
                    $paramss = array(':user_id' => $user_id, ':s' => '1', ':n' => '0', ':n1' => '0', ':n2' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                    $stmt->execute(array_merge($vv, $paramss));
                } else if ($_SESSION['hd.rustem_sort_in'] == "free") {
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where ((find_in_set(:user_id,user_to_id) and arch=:n) or
                            (find_in_set(:n1,user_to_id) and unit_id IN (' . $in_query . ') and arch=:n2)) and lock_by=:lb and status=:s
                            limit :start_pos, :perpage');
                    $paramss = array(':user_id' => $user_id, ':lb' => '0', ':s' => '0', ':n' => '0', ':n1' => '0', ':n2' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                    $stmt->execute(array_merge($vv, $paramss));
                } else if ($_SESSION['hd.rustem_sort_in'] == "ilock") {
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where ((find_in_set(:user_id,user_to_id) and arch=:n) or
                            (find_in_set(:n1,user_to_id) and unit_id IN (' . $in_query . ') and arch=:n2)) and lock_by=:lb
                            limit :start_pos, :perpage');
                    $paramss = array(':user_id' => $user_id, ':lb' => $user_id, ':n' => '0', ':n1' => '0', ':n2' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                    $stmt->execute(array_merge($vv, $paramss));
                } else if ($_SESSION['hd.rustem_sort_in'] == "lock") {
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where ((find_in_set(:user_id,user_to_id) and arch=:n) or
                            (find_in_set(:n1,user_to_id) and unit_id IN (' . $in_query . ') and arch=:n2)) and (lock_by<>:lb and lock_by<>0) and (status=0)
                            limit :start_pos, :perpage');
                    $paramss = array(':user_id' => $user_id, ':lb' => $user_id, ':n' => '0', ':n1' => '0', ':n2' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                    $stmt->execute(array_merge($vv, $paramss));
                }
            }
            
            if (!isset($_SESSION['hd.rustem_sort_in'])) {
                $stmt = $dbConnection->prepare('SELECT * from tickets
                            where ((find_in_set(:user_id,user_to_id) and arch=:n) or
                            (find_in_set(:n1,user_to_id) and unit_id IN (' . $in_query . ') and arch=:n2))
                            order by '.$order_l.'
                            limit :start_pos, :perpage');
                $paramss = array(':user_id' => $user_id, ':n' => '0', ':n1' => '0', ':n2' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                $stmt->execute(array_merge($vv, $paramss));
            }
            
            $res1 = $stmt->fetchAll();
        } else if ($priv_val == 2) {
            
            //Главный начальник
            
            if (isset($_SESSION['hd.rustem_sort_in'])) {
                
                if ($_SESSION['hd.rustem_sort_in'] == "ok") {
                    
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where arch=:n
                            and status=:s
                            limit :start_pos, :perpage');
                    $stmt->execute(array(':n' => '0', ':s' => '1', ':start_pos' => $start_pos, ':perpage' => $perpage));
                } else if ($_SESSION['hd.rustem_sort_in'] == "free") {
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where arch=:n
                            and lock_by=:lb and status=:s
                            limit :start_pos, :perpage');
                    $stmt->execute(array(':n' => '0', ':s' => '0', ':lb' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
                } else if ($_SESSION['hd.rustem_sort_in'] == "ilock") {
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where arch=:n
                            and lock_by=:lb
                            limit :start_pos, :perpage');
                    $stmt->execute(array(':n' => '0', ':lb' => $user_id, ':start_pos' => $start_pos, ':perpage' => $perpage));
                } else if ($_SESSION['hd.rustem_sort_in'] == "lock") {
                    $stmt = $dbConnection->prepare('SELECT * from tickets
                            where arch=:n
                            and (lock_by<>:lb and lock_by<>0) and (status=0)
                            limit :start_pos, :perpage');
                    $stmt->execute(array(':n' => '0', ':lb' => $user_id, ':start_pos' => $start_pos, ':perpage' => $perpage));
                }
            }
            
            if (!isset($_SESSION['hd.rustem_sort_in'])) {




                $stmt = $dbConnection->prepare('SELECT * from tickets
                            where arch=:n
                            order by '.$order_l.'
                            limit :start_pos, :perpage');
                $stmt->execute(array(':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
            }
            
            $res1 = $stmt->fetchAll();
        }
        
        $aha = get_total_pages('in', $user_id);



if (!isset($_SESSION['hd.rustem_sort_in'])) {
        if (isset($_SESSION['zenlix_list_in_sort'])) {


         if (isset($_SESSION['zenlix_list_in_sort_var'])) {

            if ($_SESSION['zenlix_list_in_sort_var'] == "asc") { $r=" <i class='fa fa-sort-asc'></i>";}
            if ($_SESSION['zenlix_list_in_sort_var'] == "desc") { $r=" <i class='fa fa-sort-desc'></i>";}
         }


            switch ($_SESSION['zenlix_list_in_sort']) {
                    case 'id':
                        $sort_type_start['id']="<mark>";
                        $sort_type_stop['id']=$r."</mark>";
                    break;
                    case 'prio':
                        $sort_type_start['prio']="<mark>";
                        $sort_type_stop['prio']=$r."</mark>";
                    break;
                    case 'subj':
                        $sort_type_start['subj']="<mark>";
                        $sort_type_stop['subj']=$r."</mark>";
                    break;
                    case 'client_id':
                        $sort_type_start['client_id']="<mark>";
                        $sort_type_stop['client_id']=$r."</mark>";
                    break;
                    case 'date_create':
                        $sort_type_start['date_create']="<mark>";
                        $sort_type_stop['date_create']=$r."</mark>";
                    break;
                    case 'user_init_id':
                        $sort_type_start['user_init_id']="<mark>";
                        $sort_type_stop['user_init_id']=$r."</mark>";
                    break;
            }
        }

    }


            foreach ($res1 as $row) {
                $lb = $row['lock_by'];
                $ob = $row['ok_by'];
                
                $user_id_z = $_SESSION['helpdesk_user_id'];
                $unit_user_z = unit_of_user($user_id_z);
                $status_ok_z = $row['status'];
                $ok_by_z = $row['ok_by'];
                $lock_by_z = $row['lock_by'];
                
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
                            if ($row['status'] == "1") {
               
                $status_ok_status = "ok";
            }
            
            if ($row['status'] == "0") {
                
                $status_ok_status = "no_ok";
            }
                ////////////////////////////Показывает кому/////////////////////////////////////////////////////////////////
                if ($row['user_to_id'] <> 0) {
                    $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['user_to_id'])) . "</div>";
                }
                if ($row['user_to_id'] == 0) {
                    $to_text = "<strong data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . view_array(get_unit_name_return($row['unit_id'])) . "\">" . lang('t_list_a_all') . "</strong>";
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
                
                /////////если пользователь///////////////////////////////////////////////////////////////////////////////////////////
                if ($priv_val == 1) {
                    
                    //ЗАявка не выполнена ИЛИ выполнена мной
                    //ЗАявка не заблокирована ИЛИ заблокирована мной
                    $lo = "no";
                    
                    if ($row['user_init_id'] == $user_id_z) {
                        
                        $lo = "yes";
                    }
                    
                    if ($row['user_init_id'] <> $user_id_z) {
                        
                        if (($status_ok_z == 0) || (($status_ok_z == 1) && ($ok_by_z == $user_id_z))) {
                            if (($lock_by_z == 0) || ($lock_by_z == $user_id_z)) {
                                $lo = "yes";
                            }
                        }
                    }
                    
                    if ($lo == "yes") {
                        $lock_st = "";
                        $muclass = "";
                    } else if ($lo == "no") {
                        $lock_st = "disabled=\"disabled\"";
                        $muclass = "text-muted";
                    }
                }
                
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                
                /////////если нач отдела/////////////////////////////////////////////////////////////////////////////////////////////
                else if ($priv_val == 0) {
                    $lock_st = "";
                    $muclass = "";
                }
                
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                
                //////////главный админ//////////////////////////////////////////////////////////////////////////////////////////////
                else if ($priv_val == 2) {
                    $lock_st = "";
                    $muclass = "";
                }
                
                /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                

        ob_start();
        
        //Start output buffer
        cutstr(make_html($row['subj'], 'no'));
        $cut_subj = ob_get_contents();
        
        //Grab output
        ob_end_clean();



            
array_push($ar_res, array(

    'id'=>$row['id'],
    'style'=>$style,
    'prio'=>$prio,
    'muclass'=>$muclass,
    'subj'=>make_html($row['subj'], 'no'),
    'msg'=>str_replace('"', "", cutstr_help_ret(make_html(strip_tags($row['msg'])), 'no')),
    'hashname'=>$row['hash_name'],
    'cut_subj'=>$cut_subj,
    'get_user_hash_by_id_client'=>get_user_hash_by_id($row['client_id']),
    'client'=>get_user_val_by_id($row['client_id'], 'fio'),
    'date_create'=>$row['date_create'],
    't_ago'=>$t_ago,
    'get_deadline_label'=>get_deadline_label($row['id']),
    'name_of_user_ret'=>nameshort(name_of_user_ret($row['user_init_id'])),

    'init_hash'=>get_user_hash_by_id($row['user_init_id']),
    'init_fio'=>nameshort(name_of_user_ret($row['user_init_id'])),
    'to_text'=>$to_text,
    'st'=>$st,

    'get_b_lb'=>get_button_act_status(get_ticket_action_priv($row['id']), $lb_status),
    'lb_tooltip'=>$lb_tooltip,
    'user_id'=>$user_id,
    'lb_status'=>$lb_status,
    'lb_text'=>$lb_text,

    'get_b_ob'=>get_button_act_status(get_ticket_action_priv($row['id']), $status_ok_status),
    'ob_tooltip'=>$ob_tooltip,
    'ob_status'=>$ob_status,
    'ob_text'=>$ob_text









    ));


}
$basedir = dirname(dirname(__FILE__)); 
            ////////////
    try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/views');
            
            // инициализируем Twig
            $twig = new Twig_Environment($loader);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('list_content_in.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'get_total_pages_in' => get_total_pages('in', $user_id),
                'user_id'=>$user_id,
                'helpdesk_sort_id'=>$_SESSION['helpdesk_sort_id'],
                'sort_type_start_id'=>$sort_type_start['id'],
                'sort_type_stop_id'=>$sort_type_stop['id'],
                'id_icon'=>$id_icon,

                'helpdesk_sort_prio'=>$_SESSION['helpdesk_sort_prio'],

                'sort_type_start_prio'=>$sort_type_start['prio'],
                't_LIST_prio'=>lang('t_LIST_prio'),
                'sort_type_stop_prio'=>$sort_type_stop['prio'],
                'prio_icon'=>$prio_icon,

                'helpdesk_sort_subj'=>$_SESSION['helpdesk_sort_subj'],

                'sort_type_start_subj'=>$sort_type_start['subj'],
                't_LIST_subj'=>lang('t_LIST_subj'),
                'sort_type_stop_subj'=>$sort_type_stop['subj'],
                'subj_icon'=>$subj_icon,

                'helpdesk_sort_clientid'=>$_SESSION['helpdesk_sort_clientid'],

                'sort_type_start_client_id'=>$sort_type_start['client_id'],
                't_LIST_worker'=>lang('t_LIST_worker'),
                'sort_type_stop_client_id'=>$sort_type_stop['client_id'],
                'cli_icon'=>$cli_icon,


                'sort_type_start_date_create'=>$sort_type_start['date_create'],
                't_LIST_create'=>lang('t_LIST_create'),
                'sort_type_stop_date_create'=>$sort_type_stop['date_create'],

                't_LIST_ago'=>lang('t_LIST_ago'),

                'helpdesk_sort_userinitid'=>$_SESSION['helpdesk_sort_userinitid'],

                'sort_type_start_user_init_id'=>$sort_type_start['user_init_id'],
                't_LIST_init'=>lang('t_LIST_init'),
                'sort_type_stop_user_init_id'=>$sort_type_stop['user_init_id'],
                'init_icon'=>$init_icon,
                't_LIST_to'=>lang('t_LIST_to'),
                't_LIST_status'=>lang('t_LIST_status'),
                'ar_res'=>$ar_res,
                'aha'=>$aha,
                'MSG_no_records'=>lang('MSG_no_records'),
                't_LIST_action'=>lang('t_LIST_action'),


            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
    }
    if ($_POST['menu'] == 'arch') {
        
        $page = ($_POST['page']);
        $perpage = '10';
        if (isset($_SESSION['hd.rustem_list_arch'])) {
            $perpage = $_SESSION['hd.rustem_list_arch'];
        }
        $start_pos = ($page - 1) * $perpage;
        
        $user_id = id_of_user($_SESSION['helpdesk_user_login']);
        $unit_user = unit_of_user($user_id);
        $units = explode(",", $unit_user);
        $units = implode("', '", $units);
        $priv_val = priv_status($user_id);
        
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
        
        //$pp2=array_merge($vv,$vv2);
        
        if ($priv_val == 0) {
            
            $stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, ok_date
                            from tickets
                            where (unit_id IN (' . $in_query . ') or user_init_id=:user_id) and arch=:n
                            order by id DESC
                            limit :start_pos, :perpage');
            
            $paramss = array(':n' => '1', ':user_id' => $user_id, ':start_pos' => $start_pos, ':perpage' => $perpage);
            $stmt->execute(array_merge($vv, $paramss));
            $res1 = $stmt->fetchAll();
        } else if ($priv_val == 1) {
            
            $stmt = $dbConnection->prepare('
            SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, ok_date
                            from tickets
                            where (
                            (find_in_set(:user_id,user_to_id) and unit_id IN (' . $in_query . ') and arch=:n) or
                            (find_in_set(:n1,user_to_id) and unit_id IN (' . $in_query2 . ') and arch=:n2)
                            ) or (user_init_id=:user_id2 and arch=:n3)
                            order by id DESC
                            limit :start_pos, :perpage');
            
            $paramss = array(':n' => '1', ':n1' => '0', ':n2' => '1', ':n3' => '1', ':user_id' => $user_id, ':user_id2' => $user_id, ':start_pos' => $start_pos, ':perpage' => $perpage);
            
            $stmt->execute(array_merge($vv, $vv2, $paramss));
            $res1 = $stmt->fetchAll();
        } else if ($priv_val == 2) {
            
            $stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, ok_date
                            from tickets
                            where arch=:n
                            order by id DESC
                            limit :start_pos, :perpage');
            
            $stmt->execute(array(':n' => '1', ':start_pos' => $start_pos, ':perpage' => $perpage));
            $res1 = $stmt->fetchAll();
        }
        
        $aha = get_total_pages('arch', $user_id);
       
       $ar_res=array();
            
            foreach ($res1 as $row) {
                
                if ($row['user_to_id'] <> 0) {
                    $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['user_to_id'])) . "</div>";
                }
                if ($row['user_to_id'] == 0) {
                    $to_text = "<strong data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . view_array(get_unit_name_return($row['unit_id'])) . "\">" . lang('t_list_a_all') . "</strong>";
                }



ob_start();
        
        //Start output buffer
        cutstr(make_html($row['subj'], 'no'));
        $cut_subj = ob_get_contents();
        
        //Grab output
        ob_end_clean();


array_push($ar_res, array(

    'id'=>$row['id'],
    'muclass'=>$muclass,
    'subj'=>make_html($row['subj'], 'no'),
    'msg'=>str_replace('"', "", cutstr_help_ret(make_html(strip_tags($row['msg'])), 'no')),
    'hashname'=>$row['hash_name'],

    'cut_subj'=>$cut_subj,
    'get_user_hash_by_id_client'=>get_user_hash_by_id($row['client_id']),
    'client'=>get_user_val_by_id($row['client_id'], 'fio'),
    'date_create'=>$row['date_create'],
    't_ago'=>$t_ago,
    'get_deadline_label'=>get_deadline_label($row['id']),
    'name_of_user_ret'=>nameshort(name_of_user_ret($row['user_init_id'])),

    'init_hash'=>get_user_hash_by_id($row['user_init_id']),
    'init_fio'=>nameshort(name_of_user_ret($row['user_init_id'])),
    'to_text'=>$to_text,
    'ok_by'=>nameshort(name_of_user_ret($row['ok_by'])),
    'ok_date'=>$row['ok_date'],











    ));



            }



$basedir = dirname(dirname(__FILE__)); 
            ////////////
    try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/views');
            
            // инициализируем Twig
            $twig = new Twig_Environment($loader);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('list_content_arch.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'get_total_pages_arch' => get_total_pages('arch', $user_id),
                'user_id'=>$user_id,
                't_LIST_subj'=>lang('t_LIST_subj'),
                't_LIST_worker'=>lang('t_LIST_worker'),
                't_LIST_create'=>lang('t_LIST_create'),
                't_LIST_init'=>lang('t_LIST_init'),
                't_LIST_to'=>lang('t_LIST_to'),
                'ar_res'=>$ar_res,
                'aha'=>$aha,
                'MSG_no_records'=>lang('MSG_no_records'),
                't_list_a_user_ok'=>lang('t_list_a_user_ok'),
                't_list_a_date_ok'=>lang('t_list_a_date_ok')






            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }




    }
}
?>
