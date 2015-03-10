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
        if ($aha == "0") {
?>
            <div id="spinner" class="well well-large well-transparent lead">
                <center><?php echo lang('MSG_no_records'); ?></center>
            </div>
        <?php
        }
        if ($aha <> "0") { 





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

?>



            <input type="hidden" value="<?php
            echo get_total_pages('out', $user_id); ?>" id="val_menu">


            <table class="table table-bordered table-hover" style=" font-size: 14px; ">
                <thead>
                <tr>
                    <th><center>

                    <a href="#" style="color: black;" value="id" id="make_sort"> <?=$sort_type_start['id'];?>#<?=$sort_type_stop['id'];?>
                    </a>
                    </center></th>
                    <th><center>
                    <a href="#" style="color: black;" value="prio" id="make_sort"> 
                    <?=$sort_type_start['prio'];?>
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('t_LIST_prio'); ?>"></i>
                    <?=$sort_type_stop['prio'];?>
                    </a></center></th>
                    <th><center>
                    <a href="#" style="color: black;" value="subj" id="make_sort"> 
                    <?=$sort_type_start['subj'];?>
                    <?php echo lang('t_LIST_subj'); ?>
                    <?=$sort_type_stop['subj'];?>
                    </a></center></th>
                    <th><center>
                    <a href="#" style="color: black;" value="client_id" id="make_sort"> 
                    <?=$sort_type_start['client_id'];?>
                    <?php echo lang('t_LIST_worker'); ?>
                    <?=$sort_type_stop['client_id'];?>
                    </a>
                    </center></th>
                    <th><center>
<a href="#" style="color: black;" value="date_create" id="make_sort"> 
<?=$sort_type_start['date_create'];?>
<?php echo lang('t_LIST_create'); ?>
<?=$sort_type_stop['date_create'];?>
</a></center></th>
                    <th><center><?php echo lang('t_LIST_ago'); ?></center></th>
                    <th><center>
                        <a href="#" style="color: black;" value="user_init_id" id="make_sort"> 
                        <?=$sort_type_start['user_init_id'];?>
                    <?php echo lang('t_LIST_init') ?>
                    <?=$sort_type_stop['user_init_id'];?>
                    </a></center></th>
                    <th><center><?php echo lang('t_LIST_to'); ?></center></th>
                    <th><center><?php echo lang('t_LIST_status'); ?></center></th>
                    <th><center><?php echo lang('t_LIST_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>

                <?php
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
                
                
?>

                    <tr id="tr_<?php
                echo $row['id']; ?>" class="<?php echo $style ?>">
                        <td style=" vertical-align: middle; "><small><center><?php
                echo $row['id']; ?></center></small></td>
                        <td style=" vertical-align: middle; "><small><center><?php echo $prio ?></center></small></td>
                        <td style=" vertical-align: middle; "><a class="<?php echo $muclass; ?> pops"  
                    title="<?php echo make_html($row['subj'], 'no'); ?>"
                    data-content="<small><?php echo str_replace('"', "", cutstr_help_ret(make_html(strip_tags($row['msg'])), 'no')); ?></small>" 
                    
                    
                    href="ticket?<?php
                echo $row['hash_name']; ?>"><?php
                cutstr(make_html($row['subj'], 'no')); ?></a></td>
                        <td style=" vertical-align: middle; "><small>
                        <a href="view_user?<?php echo get_user_hash_by_id($row['client_id']); ?>">
                        <?php echo get_user_val_by_id($row['client_id'], 'fio'); ?>
                        </a>
                        </small></td>
                        <td style=" vertical-align: middle; "><small><center><time id="c" datetime="<?php echo $row['date_create']; ?>"></time></center></small></td>
                        <td style=" vertical-align: middle; "><small><center><time id="a" datetime="<?php echo $t_ago; ?>"></time>

                        <?=get_deadline_label($row['id']);?></center></small></td>


<td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><?php
                echo nameshort(name_of_user_ret($row['user_init_id'])); ?></small></td>


                        <td style=" vertical-align: middle; ">
                            <small><?php echo $to_text; ?></small>
                            
                        </td>
                        
                        
                        <td style=" vertical-align: middle; "><small><center><?php echo $st; ?></center>
                            </small></td>
                        <td style=" vertical-align: middle; ">
                            <center>
                                <div class="btn-group btn-group-xs actions">
                                    <button data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('t_list_a_ok_no'); ?>" type="button" <?php echo $dis_status; ?> class="btn btn-success" user="<?php echo $user_id ?>" value="<?php
                echo $row['id']; ?>" id="action_list_ok" status="<?php echo $ob_status ?>"><?php echo $ob_text ?></button>
                                </div>
                            </center>
                        </td>
                    </tr>
                <?php
            }
?>
                </tbody>
            </table>






        <?php
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
        
        if (empty($res1)) {
?>
            <div class="well well-large well-transparent lead">
                <center>
                    <?php echo lang('MSG_no_records') ?>
                </center>
            </div>
        <?php
        } else if (!empty($res1)) {
?>
            <center><small class="text-mutted"><em><?php echo lang('t_list_a_top') ?></em></small></center>
            <table class="table table-bordered table-hover" style=" font-size: 14px; ">
            <thead>
            <tr>
                <th><center>#</center></th>
                <th><center><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('t_LIST_prio') ?>"></i></center></th>
                <th><center><?php echo lang('t_LIST_subj') ?></center></th>
                <th><center><?php echo lang('t_LIST_worker') ?></center></th>
                <th><center><?php echo lang('t_LIST_create') ?></center></th>
                <th><center><?php echo lang('t_LIST_ago') ?></center></th>
                <th><center><?php echo lang('t_LIST_init') ?></center></th>
                <th><center><?php echo lang('t_LIST_to') ?></center></th>
                <th><center><?php echo lang('t_LIST_status') ?></center></th>
            </tr>
            </thead>
            <tbody>
            <?php
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
?>
                <tr id="tr_<?php
                echo $row['id']; ?>" class="<?php echo $style ?>">
                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center>
                   <?=$row['id'];?>


                </center></small></td>
                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center><?php echo $prio ?></center></small></td>
                    <td style=" vertical-align: middle; "><a class="<?php echo $muclass; ?> pops"  
                    title="<?php echo make_html($row['subj'], 'no'); ?>"
                    data-content="<small><?php echo str_replace('"', "", cutstr_help_ret(make_html(strip_tags($row['msg']), 'no'))); ?></small>" 
                    
                    
                    href="ticket?<?php
                echo $row['hash_name']; ?>"><?php
                cutstr(make_html($row['subj'], 'no')); ?></a></td>
                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>">
                    <a href="view_user?<?php echo get_user_hash_by_id($row['client_id']); ?>">
                    <?php echo get_user_val_by_id($row['client_id'], 'fio'); ?>
                    </a>
                    </small></td>
                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center><time id="c" datetime="<?php echo $row['date_create']; ?>"></time></center></small></td>
                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center>
                    <time id="a" datetime="<?php echo $t_ago; ?>"></time>
                     <?=get_deadline_label($row['id']);?>
                    </center></small></td>

                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><?php
                echo nameshort(name_of_user_ret($row['user_init_id'])); ?></small></td>

                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>">
                            <?php echo $to_text
?>
                        </small></td>
                    <td style=" vertical-align: middle; ">
                        <center><small>
                                <?php echo $st; ?>
                            </small>
                        </center>
                    </td>
                </tr>
            <?php
            }
?>
            </tbody>
            </table>



        <?php
        }
    }
     
    if ($_POST['menu'] == 'in') {
        
        $page = ($_POST['page']);
        
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
        if ($aha == "0") {
?>
            <div id="spinner" class="well well-large well-transparent lead">
                <center>
                    <?php echo lang('MSG_no_records'); ?>
                </center>
            </div>
        <?php
        }
        if ($aha <> "0") {
?>

            <input type="hidden" value="<?php
            echo get_total_pages('in', $user_id); ?>" id="val_menu">
            <input type="hidden" value="<?php
            echo $user_id; ?>" id="user_id">
            <input type="hidden" value="" id="total_tickets">
            <input type="hidden" value="" id="last_total_tickets">





<?php

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

?>


 
            <table class="table table-bordered table-hover" style=" font-size: 14px; ">
            <thead>
            <tr>
                <th><center><div id="sort_id" action="<?php echo $_SESSION['helpdesk_sort_id']; ?>">
<a href="#" style="color: black;" value="id" id="make_sort"> 
<?=$sort_type_start['id'];?>
                #<?php echo $id_icon; ?>
<?=$sort_type_stop['id'];?>
</a>

                </div></center></th>
                <th><center><div id="sort_prio" action="<?php echo $_SESSION['helpdesk_sort_prio']; ?>">

                    <a href="#" style="color: black;" value="prio" id="make_sort"> 
                    <?=$sort_type_start['prio'];?>
                <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('t_LIST_prio'); ?>"></i>
                <?=$sort_type_stop['prio'];?>
                </a>
                <?php echo $prio_icon; ?></div></center></th>
                <th><center><div id="sort_subj" action="<?php echo $_SESSION['helpdesk_sort_subj']; ?>">

                <a href="#" style="color: black;" value="subj" id="make_sort">
<?=$sort_type_start['subj'];?>
                <?php echo lang('t_LIST_subj'); ?>
<?=$sort_type_stop['subj'];?>
</a>
                <?php echo $subj_icon; ?>

                </div></center></th>
                <th><center><div id="sort_cli" action="<?php echo $_SESSION['helpdesk_sort_clientid']; ?>">
                    <a href="#" style="color: black;" value="client_id" id="make_sort">
                    <?=$sort_type_start['client_id'];?>
                <?php echo lang('t_LIST_worker'); ?>
                <?=$sort_type_stop['client_id'];?>
                    </a>

                <?php echo $cli_icon; ?></div></center></th>
                <th><center>
                <a href="#" style="color: black;" value="date_create" id="make_sort">
                <?=$sort_type_start['date_create'];?>
                <?php echo lang('t_LIST_create'); ?>
                <?=$sort_type_stop['date_create'];?>
                </a>
                </center></th>
                <th><center><?php echo lang('t_LIST_ago'); ?></center></th>
                <th><center><div id="sort_init" action="<?php echo $_SESSION['helpdesk_sort_userinitid']; ?>">
<a href="#" style="color: black;" value="user_init_id" id="make_sort">
<?=$sort_type_start['user_init_id'];?>
                <?php echo lang('t_LIST_init'); ?>
<?=$sort_type_stop['user_init_id'];?>
</a>

                <?php echo $init_icon; ?></div></center></th>
                <th><center><?php echo lang('t_LIST_to'); ?></center></th>
                <th><center><?php echo lang('t_LIST_status'); ?></center></th>
                <th style="width:60px;"><center><?php echo lang('t_LIST_action'); ?></center></th>
            </tr>
            </thead>
            <tbody>

            <?php
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
                
?>

                <tr id="tr_<?php
                echo $row['id']; ?>" class="<?php echo $style ?>">
                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center><?php
                echo $row['id']; ?></center></small></td>
                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center><?php echo $prio ?></center></small></td>
                    <td style=" vertical-align: middle; "><a class="<?php echo $muclass; ?> pops"  
                    title="<?php echo make_html($row['subj'], 'no'); ?>"
                    data-content="<small><?php echo str_replace('"', "", cutstr_help_ret(make_html(strip_tags($row['msg']), 'no'))); ?></small>" 
                    
                    
                    href="ticket?<?php
                echo $row['hash_name']; ?>"><?php
                cutstr(make_html($row['subj'], 'no')); ?></a></td>
                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>">
                    <a href="view_user?<?php echo get_user_hash_by_id($row['client_id']); ?>">
                    <?php echo get_user_val_by_id($row['client_id'], 'fio'); ?>
                    </a>
                    
                    </small></td>
                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center><time id="c" datetime="<?php echo $row['date_create']; ?>"></time></center></small></td>
                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center><time id="a" datetime="<?php echo $t_ago; ?>"></time>

                    <?=get_deadline_label($row['id']);?>

                    </center></small></td>

                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>">
                    <a href="view_user?<?php echo get_user_hash_by_id($row['user_init_id']); ?>">
                    <?php
                echo nameshort(name_of_user_ret($row['user_init_id'])); ?>
                    </a>
                    </small></td>

                    <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>">
                            <?php echo $to_text
?>
                        </small></td>
                    <td style=" vertical-align: middle; "><small><center>
                                <?php echo $st; ?> </center>
                        </small></td>
                    <td style=" vertical-align: middle; ">
                        <center>
                            <div class="btn-group btn-group-xs actions">
                                <button <?php echo $lock_st ?> data-toggle="tooltip" data-placement="bottom" title="<?php echo $lb_tooltip ?>" type="button" class="btn btn-warning" user="<?php echo $user_id ?>" value="<?php
                echo $row['id']; ?>" id="action_list_lock" status="<?php echo $lb_status ?>"><?php echo $lb_text ?></button>

                                <button <?php echo $lock_st ?> data-toggle="tooltip" data-placement="bottom" title="<?php echo $ob_tooltip ?>" type="button" class="btn btn-success" user="<?php echo $user_id ?>" value="<?php
                echo $row['id']; ?>" id="action_list_ok" status="<?php echo $ob_status ?>"><?php echo $ob_text ?></button>
                            </div>
                        </center>
                    </td>
                </tr>
            <?php
            }
?>
            </tbody>
            </table>





        <?php
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
        if ($aha == "0") {
?>
            <div id="spinner" class="well well-large well-transparent lead">
                <center>
                    <?php echo lang('MSG_no_records'); ?>
                </center>
            </div>
        <?php
        } else if ($aha <> "0") {
?>

            <input type="hidden" value="<?php
            echo get_total_pages('arch', $user_id); ?>" id="val_menu">
            <input type="hidden" value="<?php
            echo $user_id; ?>" id="user_id">
            <input type="hidden" value="" id="total_tickets">
            <input type="hidden" value="" id="last_total_tickets">

            <table class="table table-bordered table-hover" style=" font-size: 14px; ">
                <thead>
                <tr>
                    <th><center>#</center></th>
                    <th><center><?php echo lang('t_LIST_subj'); ?></center></th>
                    <th><center><?php echo lang('t_LIST_worker'); ?></center></th>
                    <th><center><?php echo lang('t_LIST_create'); ?></center></th>
                    <th><center><?php echo lang('t_LIST_init'); ?></center></th>
                    <th><center><?php echo lang('t_LIST_to'); ?></center></th>
                    <th><center><?php echo lang('t_list_a_user_ok'); ?></center></th>
                    <th><center><?php echo lang('t_list_a_date_ok'); ?></center></th>
                </tr>
                </thead>
                <tbody>

                <?php
            
            foreach ($res1 as $row) {
                
                if ($row['user_to_id'] <> 0) {
                    $to_text = "<div class=''>" . nameshort(name_of_user_ret($row['user_to_id'])) . "</div>";
                }
                if ($row['user_to_id'] == 0) {
                    $to_text = "<strong data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"" . view_array(get_unit_name_return($row['unit_id'])) . "\">" . lang('t_list_a_all') . "</strong>";
                }
?>
                    <tr >
                        <td style=" vertical-align: middle; "><small><center><?php
                echo $row['id']; ?></center></small></td>
                        <td style=" vertical-align: middle; "><a class="<?php echo $muclass; ?> pops"  
                    title="<?php echo make_html($row['subj'], 'no'); ?>"
                    data-content="<small><?php echo str_replace('"', "", cutstr_help_ret(make_html(strip_tags($row['msg']), 'no'))); ?></small>" 
                    
                    
                    href="ticket?<?php
                echo $row['hash_name']; ?>"><?php
                cutstr(make_html($row['subj'], 'no')); ?></a></td>
                        <td style=" vertical-align: middle; "><small><?php echo get_user_val_by_id($row['client_id'], 'fio'); ?></small></td>
                        <td style=" vertical-align: middle; "><small><center><time id="c" datetime="<?php echo $row['date_create']; ?>"></time></center></small></td>
                        <td style=" vertical-align: middle; "><small><?php echo nameshort(name_of_user_ret($row['user_init_id'])); ?></small></td>

                        <td style=" vertical-align: middle; "><small>
                                <?php echo $to_text
?>
                            </small></td>
                        <td style=" vertical-align: middle; "><small>
                                <?php echo nameshort(name_of_user_ret($row['ok_by'])); ?>
                            </small></td>
                        <td style=" vertical-align: middle; "><small><center>
                        <time id="c" datetime="<?php echo $row['ok_date']; ?>"></time>
                        </center></small></td>
                    </tr>
                <?php
            }
?>
                </tbody>
            </table>
        <?php
        }
    }
}
?>
