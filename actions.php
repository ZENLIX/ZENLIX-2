<?php
session_start();
include ("functions.inc.php");
if (isset($_POST['mode'])) {
    
    $mode = ($_POST['mode']);
    
    if ($mode == "get_host_conf") {
        
        print ($CONF['hostname']);
    }
    
    if ($mode == "get_lang_param") {
        $p = ($_POST['param']);
        $r = lang($p);
        print ($r);
    }
    
    if ($mode == "register_new") {
        
        $fio = $_POST['fio'];
        $login = $_POST['login'];
        $mail = $_POST['mail'];
        
        $errors = false;
        
        if (validate_exist_login($login) == false) {
            $errors = true;
            $el = lang('ticket_login_error') . "<br>";
        }
        if (!validate_email($mail)) {
            $errors = true;
            $el.= lang('PROFILE_msg_error') . "<br>";
        }
        if (validate_exist_mail_not_auth($mail) == false) {
            $errors = true;
            $el.= lang('PROFILE_msg_error') . "(already exist)<br>";
        }
        
        if ($errors == true) {
            $check_error = "false";
            $msg = "<div class=\"body bg-gray\">";
            $msg.= "<div class=\"alert alert-danger alert-dismissable\">
                                        <i class=\"fa fa-ban\"></i>
                                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>";
            $msg.= $el;
            $msg.= "</div>";
            $msg.= "</div>";
        } else if ($errors == false) {
            $check_error = "true";
            $msg = "<div class=\"body bg-gray\">";
            $msg.= "<div class=\"callout callout-info\">";
            $msg.= lang('REG_msg');
            $msg.= "</div>";
            $msg.= "</div>";
        }
        
        $results[] = array('check_error' => $check_error, 'msg' => $msg);
        print json_encode($results);
        
        //
        $pass = generatepassword();
        
        $stmt = $dbConnection->prepare('insert into users 
             (fio, 
             login, 
             email, 
             priv,
             is_client,
             uniq_id,
             status,
             pass) 
             VALUES         
             (
             :client_fio, 
             :client_login,   
             :client_mail, 
             :priv,
             :is_client,
             :uniq_id,
             :status,
             :pass)');
        
        $stmt->execute(array(':client_fio' => $fio, ':client_login' => $login, ':client_mail' => $mail, ':priv' => '1', ':is_client' => '1', ':uniq_id' => $hn, ':status' => '1', ':pass' => md5($pass)));
        
        //send mail to user & admin
        
        $subject = $CONF['name_of_firm'] . " - registration successfull";
        $message = <<<EOBODY
<div style="background: #ffffff; border: 1px solid gray; border-radius: 6px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; margin: 9px 17px 13px 17px; padding: 11px;">
<p style="font-family: Arial, Helvetica, sans-serif; font-size:18px; text-align:center;">REGISTRATION INFORMATION!</p>

<br />
<table width="100%" cellspacing="0" cellpadding="3" style="">
  
  <tr>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;">Login:</td>
    <td style="border: 1px solid #ddd; font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;">{$login}</td>
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
    }
    
    if ((validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) || (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code']))) {
        
        if ($mode == "get_list_notes") {
            $userid = $_SESSION['helpdesk_user_id'];
            
            $stmt = $dbConnection->prepare('SELECT id, hashname, message from notes where user_id=:userid order by dt DESC');
            $stmt->execute(array(':userid' => $userid));
            $res = $stmt->fetchAll();
?>
            
            
            
            
            
            
            <div class="box">

                                <div class="box-body no-padding">
                                    
                                    

            
            <ul class="nav nav-pills nav-stacked" id="table_list">
                
           
                                               
            
            
            
            
            
            
            <!--table class="table table-hover" style="margin-bottom: 0px; margin-bottom: 0px;" id="table_list"-->


            <?php
            if (empty($res)) {
                echo lang('empty');
            } else if (!empty($res)) {
                
                foreach ($res as $row) {
                    
                    $t_msg = cutstr_ret(strip_tags($row['message']));
                    
                    if (strlen($t_msg) < 2) {
                        $t_msg = "<em>" . lang('NOTES_single') . "</em>";
                    }
?>
                    
                    <li class="tr_<?php echo $row['id']; ?>">
<a style=" cursor: pointer; " id="to_notes" value="<?php echo $row['hashname']; ?>"><?php echo $t_msg; ?>

<span class="badge pull-right bg-red" id="del_notes" value="<?php echo $row['hashname']; ?>">
<i class="glyphicon glyphicon-trash"></i></span>

</a>


                    </li>
                    
                    
                    
                    

                    
                    
                    
                    
                    
                <?php
                }
?><!--/table-->
            </ul>                                </div><!-- /.box-body -->
                            </div><?php
            }
        }
        
        if ($mode == "check_login") {
            
            $l = $_POST['login'];
            
            if ($_POST['exclude']) {
                
                $t = $_POST['exclude'];
                if (validate_exist_login_ex($l, $t) == true) {
                    $r['check_login_status'] = true;
                } else if (validate_exist_login_ex($l, $t) == false) {
                    $r['check_login_status'] = false;
                }
            } else if (!$_POST['exclude']) {
                if (validate_exist_login($l) == true) {
                    $r['check_login_status'] = true;
                } else if (validate_exist_login($l) == false) {
                    $r['check_login_status'] = false;
                }
            }
            
            $row_set[] = $r;
            echo json_encode($row_set);
        }
        
        if ($mode == "add_cron") {
	        /*
        $user_to_id 	= $_POST['s2id_users_do'];
        $subj 			= $_POST['subj'];
        $msg			= $_POST['msg'];
        $client_id		= $_POST['client_id_param'];
        $unit_id 		= $_POST['to'];
        $period 		= $_POST['period'];
        $action_time 	= $_POST['time_action'];
        $dt_start 		= $_POST['action_start'];
        $dt_stop 		= $_POST['action_stop'];
        $prio			= $_POST['prio'];
                        */
                        
                        $status_action=$_POST['status_action'];
                        
                        
                        $errors=false;
                        
                                                
                        if ($_POST['period'] == "day") {$p_arr=$_POST['day_field']; }
                        else if ($_POST['period'] == "week") {$p_arr=$_POST['week_select']; }
                        else if ($_POST['period'] == "month") {$p_arr=$_POST['month_select']; }
                        
        $stmt = $dbConnection->prepare('insert into scheduler_ticket
        (user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, period, period_arr, action_time, dt_start, dt_stop, prio) values (
        :user_init_id, 
        :user_to_id, 
        :date_create, 
        :subj, 
        :msg, 
        :client_id, 
        :unit_id, 
        :period, 
        :period_arr, 
        :action_time, 
        :dt_start, 
        :dt_stop,  
        :prio)');
        
        $stmt->execute(array(
        ':user_init_id' => '1', 
        ':user_to_id' 	=> $_POST['s2id_users_do'],
        ':date_create' 	=> $CONF['now_dt'],
        ':subj' 		=> $_POST['subj'],
        ':msg' 			=> $_POST['msg'],
        ':client_id' 	=> $_POST['client_id_param'],
        ':unit_id' 		=> $_POST['to'],
        ':period' 		=> $_POST['period'],
        ':period_arr' 	=> $p_arr,
        ':action_time' 	=> $_POST['time_action'],
        ':dt_start' 	=> $_POST['action_start'],
        ':dt_stop' 		=> $_POST['action_stop'],
        ':prio'			=> $_POST['prio']
        ));

	        ?>
	        
	        
	        
	        <?php
	        
	        
        }
        
        if ($mode == "save_notes") {
            $noteid = ($_POST['hn']);
            $message = ($_POST['msg']);
            $message = str_replace("\r\n", "\n", $message);
            $message = str_replace("\r", "\n", $message);
            $message = str_replace("&nbsp;", " ", $message);
            
            $stmt = $dbConnection->prepare('update notes set message=:message, dt=:n where hashname=:noteid');
            $stmt->execute(array(':message' => $message, ':noteid' => $noteid, ':n' => $CONF['now_dt']));
            
            print_r($_POST['msg']);
        }
        
        if ($mode == "get_first_note") {
            $noteid = ($_POST['hn']);
            $uid = $_SESSION['helpdesk_user_id'];
            
            $stmt = $dbConnection->prepare('select hashname, message from notes where user_id=:uid order by dt DESC limit 1');
            $stmt->execute(array(':uid' => $uid));
            
            $res = $stmt->fetchAll();
            
            if (empty($res)) {
                echo "no";
            } else if (!empty($res)) {
                
                foreach ($res as $row) {
                    echo $row['message'];
                }
            }
        }
        
        if ($mode == "attach_file_comment") {
            
            $flag = false;
            $output_dir = "upload_files/";
            $fhash = randomhash();
            $user_comment = $_SESSION['helpdesk_user_id'];
            $th = $_POST['tid'];
            $tid_comment = get_ticket_id_by_hash($_POST['tid']);
            $ms = 30097152;
            
            $fileName = $_FILES["file"]["name"];
            $filetype = $_FILES["file"]["type"];
            $filesize = $_FILES["file"]["size"];
            
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileName_norm = $fhash . "." . $ext;
            
            if ($_FILES["file"]["size"] > $ms) {
                $flag = true;
            }
            
            if ($flag == false) {
                
                move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir . $fileName_norm);
                
                $stmt = $dbConnection->prepare('insert into files 
        (ticket_hash, original_name, file_hash, file_type, file_size, file_ext) values 
        (:ticket_hash, :original_name, :file_hash, :file_type, :file_size, :file_ext)');
                $stmt->execute(array(':ticket_hash' => $th, ':original_name' => $fileName, ':file_hash' => $fhash, ':file_type' => $filetype, ':file_size' => $filesize, ':file_ext' => $ext));
                
                ///comment
                $stmt = $dbConnection->prepare('INSERT INTO comments (t_id, user_id, comment_text, dt)
                                            values (:tid_comment, :user_comment, :text_comment, :n)');
                $stmt->execute(array(':tid_comment' => $tid_comment, ':user_comment' => $user_comment, ':text_comment' => '[file:' . $fhash . ']', ':n' => $CONF['now_dt']));
                
                ///comment end
                
                ///add log////
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:comment, :n, :user_comment, :tid_comment)');
                $stmt->execute(array(':tid_comment' => $tid_comment, ':user_comment' => $user_comment, ':comment' => 'comment', ':n' => $CONF['now_dt']));
                
                ////add log end///
                
                send_notification('ticket_comment', $tid_comment);
                
                $stmt = $dbConnection->prepare('update tickets set last_update=:n where id=:tid_comment');
                $stmt->execute(array(':tid_comment' => $tid_comment, ':n' => $CONF['now_dt']));
                view_comment($tid_comment);
            } else if ($flag == true) {
                view_comment($tid_comment);
?>
            <div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-ban"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <?php echo lang('upload_errorsize'); ?>
            </div>
                                    
                                    
           
            <?php
            }
        }
        
        if ($mode == "get_user_stat") {
            
            //print_r($_POST);
            
            if ($_POST['uid']) {
                $start = $_POST['start'] . " 00:00:00";
                $end = $_POST['end'] . " 23:59:00";
                $uid = $_POST['uid'];
                
                /*
                вывести весь лог всех действий пользователя
                
                */
                
                $stmt = $dbConnection->prepare('SELECT date_op, msg, init_user_id, to_user_id, to_unit_id, ticket_id from ticket_log where init_user_id=:iud and date_op between :start AND :end order by id DESC');
                $stmt->execute(array(':iud' => $uid, ':start' => $start, ':end' => $end));
                $re = $stmt->fetchAll();
                
                $res = $dbConnection->prepare('SELECT count(*) from tickets where user_init_id=:uid and date_create between :start AND :end');
                $res->execute(array(':uid' => $uid, ':start' => $start, ':end' => $end));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_create = $count[0];
                
                $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                $res->execute(array(':uid' => $uid, ':start' => $start, ':end' => $end, ':refer' => 'refer'));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_refer = $count[0];
                
                $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                $res->execute(array(':uid' => $uid, ':start' => $start, ':end' => $end, ':refer' => 'ok'));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_ok = $count[0];
                
                $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                $res->execute(array(':uid' => $uid, ':start' => $start, ':end' => $end, ':refer' => 'lock'));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_lock = $count[0];
                
                $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                $res->execute(array(':uid' => $uid, ':start' => $start, ':end' => $end, ':refer' => 'unlock'));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_unlock = $count[0];
                
                $res = $dbConnection->prepare('SELECT count(DISTINCT ticket_id) from ticket_log where init_user_id=:uid and msg=:refer and date_op between :start AND :end');
                $res->execute(array(':uid' => $uid, ':start' => $start, ':end' => $end, ':refer' => 'no_ok'));
                $count = $res->fetch(PDO::FETCH_NUM);
                $get_total_tickets_no_ok = $count[0];
                
                if (!empty($re)) { ?>
                        
                        <div class="box box-info">
                            <div class="box-header">
                                    <h4 class="box-title"><?php echo lang('EXT_stat_title'); ?> <time id="c" datetime="<?php echo $start ?>"></time> - <time id="c" datetime="<?php echo $end ?>"></time></h4>
                                </div>
                            <div class="panel-body" style="max-height: 400px; scroll-behavior: initial; overflow-y: scroll;">

                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th><center><small><?php echo lang('TICKET_t_date'); ?></small></center>    </th>
                                                <th><center><small><?php echo lang('TICKET_name'); ?>   </small></center></th>
                                                <th><center><small><?php echo lang('TICKET_t_action'); ?>   </small></center></th>
                                                <th><center><small><?php echo lang('TICKET_t_desc'); ?> </small></center></th>
                                                

                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php
                    foreach ($re as $row) {
                        
                        $t_action = $row['msg'];
                        
                        if ($t_action == 'refer') {
                            $icon_action = "fa fa-long-arrow-right";
                            $text_action = "" . lang('TICKET_t_a_refer') . " " . view_array(get_unit_name_return($row['to_unit_id'])) . "<br>" . name_of_user_ret($row['to_user_id']);
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
                        
                        if ($t_action == 'comment') {
                            $icon_action = "fa fa-comment";
                            $text_action = lang('TICKET_t_a_com');
                        }
                        
                        $ru = name_of_user_ret($row['init_user_id']);
?>
                                                <tr>
                                                    <td style="width: 100px; vertical-align: inherit;"><small><center>
                                                    
                                                    <time id="c" datetime="<?php echo $row['date_op'] ?>"></time>
                                                    
                                                    </center></small></td>
                                                    <td style=" width: 70px; vertical-align: inherit;"><center><small>
                                                       <a href="ticket?<?php echo get_ticket_hash_by_id($row['ticket_id']) ?>"> #<?php echo $row['ticket_id'] ?></a>
                                                        </small></center></td>
                                                    <td style=" width: 50px; vertical-align: inherit;"><small><center><i class="<?php echo $icon_action; ?>"></i>  </center></small></td>
                                                    <td style=" width: 200px; vertical-align: inherit;"><small><?php echo $text_action ?></small></td>

                                                    
                                                </tr>
                                            <?php
                    } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                
                        
                        <div class="box-body"></div></div>
                            
                            
                            
                            
                            <div class="box box-info">
                                <div class="box-header">
                                    <h4 class="box-title"><?php echo lang('EXT_stats_main'); ?></h4>
                                </div>
                                
                                <div class="row">
                            <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="<?php echo $get_total_tickets_create; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php echo (get_total_tickets_count()); ?>" data-fgColor="#39CCCC"/>
                                            <div class="knob-label"><?php echo lang('EXT_t_created'); ?></div>
                                        </div><!-- ./col -->
                            <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="<?php echo $get_total_tickets_refer; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php echo (get_total_tickets_count()); ?>" data-fgColor="#932AB6"/>
                                            <div class="knob-label"><?php echo lang('EXT_stats_refer'); ?></div>
                                        </div><!-- ./col -->
                            <div class="col-xs-4 text-center">
                                            <input type="text" class="knob" data-readonly="true" value="<?php echo $get_total_tickets_ok; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php echo (get_total_tickets_count()); ?>" data-fgColor="#39CC57"/>
                                            <div class="knob-label"><?php echo lang('EXT_t_oked'); ?></div>
                                        </div><!-- ./col -->
                                </div>
                            <div class="row">
                               <div class="col-xs-12"> <hr></div>
                            <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="<?php echo $get_total_tickets_lock; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php echo (get_total_tickets_count()); ?>" data-fgColor="#F39C12"/>
                                            <div class="knob-label"><?php echo lang('EXT_stats_lock'); ?></div>
                                        </div><!-- ./col -->
                            <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="<?php echo $get_total_tickets_unlock; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php echo (get_total_tickets_count()); ?>" data-fgColor="#001F3F"/>
                                            <div class="knob-label"><?php echo lang('EXT_stats_unlock'); ?></div>
                                        </div><!-- ./col -->
                            <div class="col-xs-4 text-center">
                                            <input type="text" class="knob" data-readonly="true" value="<?php echo $get_total_tickets_no_ok; ?>" data-width="100" data-height="100" data-max="50" data-max="<?php echo (get_total_tickets_count()); ?>" data-fgColor="#F56954"/>
                                            <div class="knob-label"><?php echo lang('EXT_stats_no_ok'); ?></div>
                                        </div><!-- ./col -->
                                </div>
                            </div>
<?php
                }
            } else {
                echo "no selected user";
            }
        }
        
        if ($mode == "check_version") {
            
            $myversion = get_conf_param('version');
            
            //echo $myversion;
            $content = file_get_contents($CONF['update_server'] . "/up.php");
            $data = json_decode($content, true);
            $getver = $data['version'];
            
            $myversion = str_replace('.', '', $myversion);
            $getver = str_replace('.', '', $getver);
            
            //print_r($data);
            //echo $getver;
            if ($myversion >= $getver) {
                echo "<br><center>" . "You have latest version." . "</center>";
            } else if ($myversion < $getver) {
                echo "<br><center>" . $data['msg'] . "</center><br>";
                echo "<a href=\"update.php\" class=\"btn btn-success btn-block btn-sm\">update now</a>";
            }
        }
        
        if ($mode == "get_notes") {
            $noteid = ($_POST['hn']);
            
            $stmt = $dbConnection->prepare('select hashname, message from notes where hashname=:noteid');
            $stmt->execute(array(':noteid' => $noteid));
            $res = $stmt->fetchAll();
            
            foreach ($res as $row) {
                echo $row['message'];
            }
        }
        
        if ($mode == "del_notes") {
            $noteid = ($_POST['nid']);
            $stmt = $dbConnection->prepare('delete from notes where hashname=:noteid');
            $stmt->execute(array(':noteid' => $noteid));
        }
        
        if ($mode == "create_notes") {
            $uid = $_SESSION['helpdesk_user_id'];
            $hn = md5(time());
            $stmt = $dbConnection->prepare('insert into notes (message, hashname, user_id, dt) values (:nr, :hn, :uid, :n)');
            $stmt->execute(array(':nr' => 'new record', ':hn' => $hn, ':uid' => $uid, ':n' => $CONF['now_dt']));
            
            echo $hn;
        }
        
        if ($mode == "find_client") {
            
            $term = trim(strip_tags(($_POST['name'])));
            
            $stmt = $dbConnection->prepare('SELECT id FROM users WHERE ((fio = :term) or (login = :term2) or (tel = :term3)) and id!=1 and is_client=1 limit 1');
            $stmt->execute(array(':term' => $term, ':term2' => $term, ':term3' => $term));
            
            $res1 = $stmt->fetchAll();
            
            if (!empty($res1)) {
                foreach ($res1 as $row) {
                    $r['res'] = true;
                    $r['p'] = $row['id'];
                }
            }
            
            if (empty($res1)) {
                $r['res'] = false;
                
                //user priv to add client in new ticket
                $pa = get_user_val('priv_add_client');
                
                
                if (isset($_POST['cron'])) {
	                $r['priv'] = false;
	                $r['msg_error'] = "<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">
  <button type=\"button\" class=\"close\" data-dismiss=\"alert\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">Close</span></button>
  User must be created.
</div>";
                }
                else if (!isset($_POST['cron'])) {
                if ($pa == 1) {
                    $r['priv'] = true;
                    $r['msg_error'] = "";

                }
                if ($pa == 0) {
                    $r['priv'] = false;
                    $r['msg_error'] = "<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">
  <button type=\"button\" class=\"close\" data-dismiss=\"alert\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">Close</span></button>
  " . lang('TICKET_error_msg') . "
</div>";

                }
                }
                            }
            
            $row_set[] = $r;
            echo json_encode($row_set);
        }
        
        if ($mode == "get_client_from_new_t") {
            if (isset($_POST['get_client_info'])) {
                
                $client_id = ($_POST['get_client_info']);
                
                $tc = get_user_val_by_id($client_id, 'is_client');
                
                if ($tc == "1") {
                    
                    get_client_info($client_id);
                } else {
?>
                    <?php echo get_client_info_ticket($client_id) ?>
                    <?php
                }
            } else if (isset($_POST['get_my_info'])) {
                
                get_my_info();
            } else if (isset($_POST['new_client_info'])) {
                $fio = ($_POST['new_client_info']);
                $u_l = ($_POST['new_client_login']);
?>


<div class="box box-info">
                                <div class="box-header">
                                    <i class="fa fa-user"></i>
                                    <h3 class="box-title"> <?php echo lang('WORKER_TITLE'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body" >

                        <div class="">


<div class="callout callout-warning">
                                        
                                        <p><?php echo lang('msg_created_new_user'); ?></p>
                                    </div>

                            <table class="table  ">
                                <tbody>
                                <tr>
                                    <td style=" width: 30px; "><small><?php echo lang('WORKER_fio'); ?>:</small></td>
                                    <td><small>
                                            <a href="#" id="username" data-type="text" data-pk="1" data-title="Enter username"><?php echo $fio ?></a>
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?php echo lang('WORKER_login'); ?>:</small></td>
                                    <td><small><a href="#" id="new_login" data-type="text"  data-pk="1" data-title="Enter username"><?php echo $u_l ?></a></small></td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?php echo lang('WORKER_posada'); ?>:</small></td>
                                    <td><small><a href="#" id="new_posada"  data-type="select" data-source="<?php echo $CONF['hostname']; ?>/inc/json.php?posada" data-pk="1" data-title="<?php echo lang('WORKER_posada'); ?>"></a></small></td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?php echo lang('WORKER_unit'); ?>:</small></td>
                                    <td><small><a href="#" id="new_unit" data-type="select" data-source="<?php echo $CONF['hostname']; ?>/inc/json.php?units" data-pk="1" data-title="<?php echo lang('NEW_to_unit'); ?>"></a></small></td>
                                </tr>

                                <tr>
                                    <td style=" width: 30px; "><small><?php echo lang('WORKER_tel'); ?>:</small></td>
                                    <td><small><a href="#" id="new_tel" data-type="text" data-pk="1" data-title="Enter username"></a></small></td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?php echo lang('WORKER_room'); ?>:</small></td>
                                    <td><small><a href="#" id="new_adr" data-type="text" data-pk="1" data-title="Enter username"></a></small></td>
                                </tr>
                                <tr>
                                    <td style=" width: 30px; "><small><?php echo lang('WORKER_mail'); ?>:</small></td>
                                    <td><small><a href="#" id="new_mail" data-type="text" data-pk="1" data-title="Enter username"></a></small></td>
                                </tr>

                                </tbody>
                            </table>

                        </div>
                    
                                
                                </div>
</div>



                                    
                                    
                                    


            <?php
            }
        }
        
        if ($mode == "verify_login_nt") {
            
            $l = $_POST['value'];
            
            if (validate_exist_login($l) == true) {
                echo "";
            } else if (validate_exist_login($l) == false) {
                header('HTTP 400 Bad Request', true, 400);
                echo lang('ticket_login_error');
            }
            
            //header('HTTP 400 Bad Request', true, 400);
            //echo lang('ticket_login_error');
            
        }
        
        if ($mode == "get_unit_id") {
            $uid = ($_POST['uid']);
            
            $u = unit_of_user($uid);
            $units = explode(",", $u);
            echo $units[0];
        }
        
        if ($mode == "get_ticket_body") {
        }
        
        if ($mode == "view_unread_msgs_labels") {
            $r = get_total_unread_messages();
            
            if ($r != 0) {
                echo $r;
            } else if ($r == 0) {
                echo "";
            }
        }
        
        if ($mode == "view_unread_msgs_total") {
            
            $tm = get_total_unread_messages();
            if ($tm != 0) {
                $title = lang('EXT_unread_msg1') . " <strong class=\"label_unread_msg\">" . $tm . "</strong> " . lang('EXT_unread_msg2');
            } else if ($tm == 0) {
                $title = lang('EXT_no_unread_msg');
            }
            
            echo $title;
        }
        
        if ($mode == "view_unread_msgs") {
            $stmt = $dbConnection->prepare('SELECT user_from, msg, date_op from messages where user_to=:uto and is_read=0');
            $stmt->execute(array(':uto' => $_SESSION['helpdesk_user_id']));
            
            $re = $stmt->fetchAll();
            
            foreach ($re as $rews) {
?>
                                    
                                    
                                        <li><!-- start message -->
                                            <a href="messages?to=<?php echo get_user_val_by_id($rews['user_from'], 'uniq_id'); ?>">
                                                <div class="pull-left">
                                                    <img src="<?php echo get_user_img_by_id($uniq_id); ?>" class="img-circle" alt="User Image"/>
                                                </div>
                                                <h4>
                                                    <?php echo nameshort(name_of_user_ret_nolink($rews['user_from'])); ?>
                                                    
                                                    <small><i class="fa fa-clock-o"></i> <time id="b" datetime="<?php echo $rews['date_op']; ?>"></time> </time></small>
                                                </h4>
                                                <p><?php echo make_html($rews['msg'], 'no'); ?></p>
                                            </a>
                                        </li><!-- end message -->
                                        <?php
            }
        }
        
        if ($mode == "count_online_users") {
            
            echo get_total_users_online();
        }
        
        if ($mode == "show_online_users") {
            
            $stmt = $dbConnection->prepare('select fio,id,uniq_id from users where last_time >= DATE_SUB(:n,INTERVAL 2 MINUTE)');
            $stmt->execute(array(':n' => $CONF['now_dt']));
            $re = $stmt->fetchAll();
            
            foreach ($re as $rews) {
?>
<li><!-- start message -->
                                            <a href="view_user?<?php echo $rews['uniq_id']; ?>">
                                                <div class="pull-left">
                                                    <img src="<?php echo get_user_img_by_id($rews['id']); ?>" class="img-circle" alt="User Image"/>
                                                </div>
                                                <h4>
                                                    <?php echo nameshort(name_of_user_ret_nolink($rews['id'])); ?>
                                                    
                                                    
                                                </h4>
                                                <p><?php echo get_user_val_by_id($rews['id'], 'posada'); ?></p>
                                            </a>
                                        </li><!-- end message -->
                                       <?php
            }
        }
        
        if ($mode == "get_chat_message") {
            $msgid = $_POST['msg_id'];
            
            $stmt = $dbConnection->prepare('select user_from, date_op, msg from messages where id=:msgid');
            $stmt->execute(array(':msgid' => $msgid));
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $uuniq_id = get_user_val_by_id($r['user_from'], 'uniq_id');
            $user_from = nameshort(name_of_user_ret_nolink($r['user_from']));
            $msgtext = $r['msg'];
            
            $results[] = array('uniq_id' => $uuniq_id, 'new_msg_text' => lang('EXT_new_message'), 'time_op' => "<time id=\"b\" datetime=\"" . date("Y-m-d H:i:s") . "\"></time>", 'user_from' => $user_from, 'user_chat' => $msgtext);
            print json_encode($results);
        }
        
        if ($mode == "update_dashboard_labels") {
            $results[] = array('tool1' => get_total_tickets_free(), 'tool2' => get_total_tickets_lock(), 'tool3' => get_total_tickets_out_and_success(), 'tool4' => get_total_tickets_ok());
            print json_encode($results);
        }
        
        if ($mode == "update_list_labels") {
            $newt = get_total_tickets_free();
            
            if ($newt != 0) {
                $newtickets = "(" . $newt . ")";
            }
            if ($newt == 0) {
                $newtickets = "";
            }
            $outt = get_total_tickets_out_and_success();
            if ($outt != 0) {
                $out_tickets = "(" . $outt . ")";
            }
            if ($outt == 0) {
                $out_tickets = "";
            }
            
            $results[] = array('in' => $newtickets, 'out' => $out_tickets);
            print json_encode($results);
        }
        if ($mode == "check_update_one") {
            $lu = ($_POST['last_update']);
            $ticket_id = ($_POST['id']);
            
            $stmt = $dbConnection->prepare('SELECT last_update,hash_name FROM tickets where id=:ticket_id');
            $stmt->execute(array(':ticket_id' => $ticket_id));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $db_lu = $fio['last_update'];
            $db_hn = $fio['hash_name'];
            $at = get_last_action_type($ticket_id);
            
            if (strtotime($db_lu) > strtotime($lu)) {
                if ($at == 'comment') {
                    $todo = "comment";
                } else {
                    $todo = "update";
                }
            }
            if (strtotime($db_lu) <= strtotime($lu)) {
                $todo = "no";
            }
            
            $results[] = array('type' => $todo, 'time' => $db_lu, 'hash' => $db_hn);
            
            print json_encode($results);
        }
        
        if ($mode == "get_users_list") {
            $idzz = ($_POST['unit']);
            
            $stmt = $dbConnection->prepare('SELECT fio, id, unit FROM users where id != 1 and status =1 and is_client=0');
            $stmt->execute();
            $result = $stmt->fetchAll();
            
            foreach ($result as $row) {
                
                if ($idzz == "0") {
                    $un = $row['fio'];
                    $ud = (int)$row['id'];
                    if (get_user_status_text($row['value']) == "online") {
                        $s = "online";
                    } else if (get_user_status_text($row['value']) == "offline") {
                        $s = "offline";
                    }
                    
                    $results[] = array('name' => nameshort($un), 'stat' => $s, 'co' => $ud);
                } else if ($idzz <> "0") {
                    $un = $row['fio'];
                    $ud = (int)$row['id'];
                    $u = explode(",", $row['unit']);
                    
                    if (in_array($idzz, $u)) {
                        
                        if (get_user_status_text($row['value']) == "online") {
                            $s = "online";
                        } else if (get_user_status_text($row['value']) == "offline") {
                            $s = "offline";
                        }
                        
                        $results[] = array('name' => nameshort($un), 'stat' => $s, 'co' => $ud);
                    }
                }
            }
            
            print json_encode($results);
        }
        
        if ($mode == "edit_helper") {
            $hn = ($_POST['hn']);
            
            $stmt = $dbConnection->prepare('select id, user_init_id, unit_to_id, dt, title, message, hashname,client_flag from helper where hashname=:hn');
            $stmt->execute(array(':hn' => $hn));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $isclient_status = $fio['client_flag'];
            
            if ($isclient_status == "1") {
                $isclient_status = "checked";
            } else {
                $isclient_status = "";
            }
            
            $u = $fio['unit_to_id'];
?>
            <div class="box box-solid">
            <div class="box-body">
            <form class="form-horizontal" role="form">




                <div class="form-group">
                    <label for="u" class="col-md-2 control-label"><small><?php echo lang('NEW_to'); ?>: </small></label>
                    <div class="col-md-10">
                        <select data-placeholder="<?php echo lang('NEW_to_unit'); ?>" class="chosen-select form-control" id="u" name="unit_id" multiple>
                        <option value="0"><?php echo lang('HELP_all'); ?></option>
                            <?php
            $u = explode(",", $u);
            $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps');
            $stmt->execute();
            $result = $stmt->fetchAll();
            
            foreach ($result as $row) {
                
                $row['label'] = $row['label'];
                $row['value'] = (int)$row['value'];
                
                $opt_sel = '';
                foreach ($u as $val) {
                    if ($val == $row['value']) {
                        $opt_sel = "selected";
                    }
                }
?>

                                <option <?php echo $opt_sel; ?> value="<?php echo $row['value'] ?>"><?php echo $row['label'] ?></option>

<?php
                
                //
                
            }
?>

                        </select>
                    </div>
                </div>
                <div class="">
                    <div class="">
                        <div class="form-group">

                            <label for="t" class="col-sm-2 control-label"><small><?php echo lang('HELP_desc'); ?>: </small></label>

                            <div class="col-sm-10">


                                <input  type="text" name="fio" class="form-control input-sm" id="t" placeholder="<?php echo lang('HELP_desc'); ?>" value="<?php echo $fio['title']; ?>">



                            </div>



                        </div></div>
                        
                        
                        <div class="form-group">
  <label for="is_client" class="col-sm-2 control-label"><small><?php echo lang('EXT_for_clients'); ?></small></label>
  <div class="col-sm-10">
  
  
  
      <div class="col-sm-10">
      <div class="checkbox">
    <label>
      <input type="checkbox" id="is_client" <?php echo $isclient_status; ?>> <?php echo lang('CONF_true'); ?>
      <p class="help-block"><small><?php echo lang('EXT_for_clients_ext'); ?></small></p>
    </label>
  </div>
      </div>
  </div>
    </div>
    
    
                        
                    <div class="form-group">

                        <label for="t2" class="col-sm-2 control-label"><small><?php echo lang('HELP_do'); ?>: </small></label>

                        <div class="col-sm-10">


                            <div id="summernote_help"><?php echo $fio['message']; ?></div>



                        </div>
                        <div class="col-md-12"><hr></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <button id="do_save_help" value="<?php echo $hn ?>" class="btn btn-success" type="submit"><i class="fa fa-check-circle-o"></i> <?php echo lang('HELP_save'); ?></button>
                                </div>
                                <div class="btn-group">
                                    <a href="helper" class="btn btn-default" type="submit"><i class="fa fa-reply"></i> <?php echo lang('HELP_back'); ?></a>
                                </div>
                            </div>


                        </div>
            </form>
            </div></div>
        <?php
        }
        
        if ($mode == "create_helper") {
?>
            <div class="box box-solid">
            <div class="box-body">
            <form class="form-horizontal" role="form">




                <div class="form-group">
                    <label for="u" class="col-md-2 control-label"><small><?php echo lang('NEW_to'); ?>: </small></label>
                    <div class="col-md-10">
                        <select style="height: 34px;" data-placeholder="<?php echo lang('NEW_to_unit'); ?>" class="chosen-select form-control" id="u" name="unit_id" multiple>
                            <option value="0"><?php echo lang('HELP_all'); ?></option>
                            <?php
            $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
            $stmt->execute(array(':n' => '0', ':s' => '1'));
            $result = $stmt->fetchAll();
            foreach ($result as $row) {
                
                $row['label'] = $row['label'];
                $row['value'] = (int)$row['value'];
?>

                                <option value="<?php echo $row['value'] ?>"><?php echo $row['label'] ?></option>

                            <?php
            }
?>

                        </select>
                    </div>
                </div>
                <div class="">
                    <div class="">
                        <div class="form-group">

                            <label for="t" class="col-sm-2 control-label"><small><?php echo lang('HELP_desc'); ?>: </small></label>

                            <div class="col-sm-10">


                                <input  type="text" name="fio" class="form-control input-sm" id="t" placeholder="<?php echo lang('HELP_desc'); ?>">



                            </div>



                        </div></div>
                        
                        
                        
                        <div class="form-group">
  <label for="is_client" class="col-sm-2 control-label"><small><?php echo lang('EXT_for_clients'); ?></small></label>
  <div class="col-sm-10">
  
  
  
      <div class="col-sm-10">
      <div class="checkbox">
    <label>
      <input type="checkbox" id="is_client"> <?php echo lang('CONF_true'); ?>
      <p class="help-block"><small><?php echo lang('EXT_for_clients_ext'); ?></small></p>
    </label>
  </div>
      </div>
  </div>
    </div>
                        
                        
                    <div class="form-group">

                        <label for="t2" class="col-sm-2 control-label"><small><?php echo lang('HELP_do'); ?>: </small></label>

                        <div class="col-sm-10">


                            <div id="summernote_help"></div>



                        </div>
                        <div class="col-md-12"><hr></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-10">
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                    <button id="do_create_help" class="btn btn-success" type="submit"><i class="fa fa-check-circle-o"></i> <?php echo lang('HELP_create'); ?></button>
                                </div>
                                <div class="btn-group">
                                    <a href="helper" class="btn btn-default" type="submit"><i class="fa fa-reply"></i> <?php echo lang('HELP_back'); ?></a>
                                </div>
                            </div>


                        </div>
            </form>
            </div></div>
        <?php
        }
        
        if ($mode == "find_help") {
            $t = ($_POST['t']);
            $user_id = id_of_user($_SESSION['helpdesk_user_login']);
            $unit_user = unit_of_user($user_id);
            $priv_val = priv_status($user_id);
            
            $units = explode(",", $unit_user);
            array_push($units, "0");
            
            $is_client = get_user_val('is_client');
            
            if ($is_client == "1") {
                
                $stmt = $dbConnection->prepare("SELECT 
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where (title like :t or message like :t2) and client_flag=:cf
                            order by dt desc");
                $stmt->execute(array(':t' => '%' . $t . '%', ':t2' => '%' . $t . '%', ':cf' => '1'));
                $result = $stmt->fetchAll();
?>
            <div class="box box-solid">
            <div class="box-body">
            <?php
                
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
                            from helper where title like :t or message like :t2
                            order by dt desc");
                $stmt->execute(array(':t' => '%' . $t . '%', ':t2' => '%' . $t . '%'));
                $result = $stmt->fetchAll();
?>
            <div class="box box-solid">
            <div class="box-body">
            <?php
                
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
            <button id=\"edit_helper\" value=\"" . $row['hashname'] . "\" type=\"button\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-pencil\"></i></button>
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
                }
?></div></div><?php
            }
        }
        
        if ($mode == "del_help") {
            $hn = ($_POST['hn']);
            
            $stmt = $dbConnection->prepare('delete from helper where hashname=:hn');
            $stmt->execute(array(':hn' => $hn));
        }
        
        if ($mode == "list_help") {
            
            $is_client = get_user_val('is_client');
            
            if ($is_client == "1") {
                $stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where client_flag=:cf
                            order by dt desc');
                $stmt->execute(array(':cf' => '1'));
                $result = $stmt->fetchAll();
?>
            <div class="box box-solid">
            <div class="box-body">
            <?php
                
                if (empty($result)) {
?>
                 <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">

                    <center><?php echo lang('MSG_no_records'); ?></center></p>



            <?php
                } else if (!empty($result)) {
                    
                    foreach ($result as $row) {
?>
<div class="box box-solid">
                                <div class="box-header">
                                    <h5 class="box-title"><small><i class="fa fa-file-text-o"></i> </small><a style="font-size: 18px;" class="text-light-blue" href="helper?h=<?php echo $row['hashname']; ?>"><?php echo $row['title']; ?></a></h5>
                                </div>
                                <div class="box-body">
                                    <small><?php echo cutstr_help_ret(strip_tags($row['message'])); ?>
                            </small>                                </div><!-- /.box-body -->
                            </div>
                        <?php
                    }
                }
?>
                
            </div></div>
                
                <?php
            } else if ($is_client == "0") {
                
                $user_id = id_of_user($_SESSION['helpdesk_user_login']);
                $unit_user = unit_of_user($user_id);
                $priv_val = priv_status($user_id);
                
                $units = explode(",", $unit_user);
                array_push($units, "0");
                
                $stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper 
                            order by dt desc');
                $stmt->execute();
                $result = $stmt->fetchAll();
?>
            <div class="box box-solid">
            <div class="box-body">
            <?php
                if (empty($result)) {
?>
                <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">

                    <center><?php echo lang('MSG_no_records'); ?></center></p>

                



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
            <button id=\"edit_helper\" value=\"" . $row['hashname'] . "\" type=\"button\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-pencil\"></i></button>
            <button id=\"del_helper\" value=\"" . $row['hashname'] . "\"type=\"button\" class=\"btn btn-default btn-xs\"><i class=\"fa fa-trash-o\"></i></button>
            </div>
            ";
                            } ?></small>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <small><?php echo cutstr_help_ret(strip_tags($row['message'])); ?>
                            </small>                                </div><!-- /.box-body -->
                            </div>



                        
                        
                        
                        
                        
                        
                        
                    <?php
                        }
                    }
?>
            </div></div>
                 <?php
                }
            }
        }
        
        ///////
        if ($mode == "do_save_help") {
            $u = $_POST['u'];
            $beats = implode(',', $u);
            $hn = ($_POST['hn']);
            
            $t = ($_POST['t']);
            $user_id_z = $_SESSION['helpdesk_user_id'];
            
            $is_client = $_POST['is_client'];
            
            if ($is_client == "true") {
                $is_client = 1;
            } else {
                $is_client = 0;
            }
            
            $message = ($_POST['msg']);
            $message = str_replace("\r\n", "\n", $message);
            $message = str_replace("\r", "\n", $message);
            $message = str_replace("&nbsp;", " ", $message);
            
            $stmt = $dbConnection->prepare('update helper set user_init_id=:user_id_z, unit_to_id=:beats, dt=:n, title=:t, message=:message, client_flag=:cf where hashname=:hn');
            $stmt->execute(array(':hn' => $hn, ':user_id_z' => $user_id_z, ':beats' => $beats, ':t' => $t, ':message' => $message, ':cf' => $is_client, ':n' => $CONF['now_dt']));
        }
        
        if ($mode == "do_create_help") {
            $u = $_POST['u'];
            $beats = implode(',', $u);
            
            $is_client = $_POST['is_client'];
            if ($is_client == "true") {
                $is_client = 1;
            } else {
                $is_client = 0;
            }
            $t = ($_POST['t']);
            $user_id_z = $_SESSION['helpdesk_user_id'];
            
            $hn = md5(time());
            $message = ($_POST['msg']);
            $message = str_replace("\r\n", "\n", $message);
            $message = str_replace("\r", "\n", $message);
            $message = str_replace("&nbsp;", " ", $message);
            
            $stmt = $dbConnection->prepare('insert into helper (hashname, user_init_id,unit_to_id, dt, title,message,client_flag) values 
        (:hn,:user_id_z,:beats, :n, :t,:message, :cf)');
            $stmt->execute(array(':hn' => $hn, ':user_id_z' => $user_id_z, ':beats' => $beats, ':t' => $t, ':message' => $message, ':cf' => $is_client, ':n' => $CONF['now_dt']));
        }
        
        if ($mode == "dashboard_t") {
            
            $page = 1;
            $perpage = '5';
            
            if (isset($_POST['p'])) {
                $perpage = $_POST['p'];
            }
            
            $start_pos = ($page - 1) * $perpage;
            
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
            
            // find_in_set('44',unit_to_id) <> 0
            
            if ($priv_val == 0) {
                
                $stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, prio, last_update
                            from tickets
                            where unit_id IN (' . $in_query . ')  and arch=:n
                            order by ok_by asc, prio desc, id desc
                            limit :start_pos, :perpage');
                
                $paramss = array(':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage);
                $stmt->execute(array_merge($vv, $paramss));
                $results = $stmt->fetchAll();
            } else if ($priv_val == 1) {
                
                //find_in_set(:user_id,user_to_id) <> 0
                /*
                $arr = array(
                'p1' => $user_id
                );
                ('.implode(' OR ', array_map(fis('user_to_id'), array_keys($arr))).')
                */
                
                $stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, prio, last_update
                            from tickets
                            where ((find_in_set(:user_id,user_to_id) and arch=:n) or
                            (find_in_set(:n1,user_to_id) and unit_id IN (' . $in_query . ') and arch=:n2))
                            order by ok_by asc, prio desc, id desc
                            limit :start_pos, :perpage');
                
                $paramss = array(':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage, ':user_id' => $user_id, ':n1' => '0', ':n2' => '0');
                
                $stmt->execute(array_merge($vv, $paramss));
                
                $results = $stmt->fetchAll();
            } else if ($priv_val == 2) {
                
                $stmt = $dbConnection->prepare('SELECT 
                            id, user_init_id, user_to_id, date_create, subj, msg, client_id, unit_id, status, hash_name, is_read, lock_by, ok_by, prio, last_update
                            from tickets
                            where arch=:n
                            order by ok_by asc, prio desc, id desc
                            limit :start_pos, :perpage');
                $stmt->execute(array(':n' => '0', ':start_pos' => $start_pos, ':perpage' => $perpage));
                $results = $stmt->fetchAll();
            }
            
            $aha = get_total_pages('dashboard', $user_id);
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








                <div class="box-body table-responsive no-padding">
                <table class="table table-hover table-bordered " style=" font-size: 14px; ">
                <thead>
                <tr>
                    <th><center><div id="sort_id" >#<?php echo $id_icon; ?></div></center></th>
                    <th><center><div id="sort_prio"><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('t_LIST_prio'); ?>"></i><?php echo $prio_icon; ?></div></center></th>
                    <th><center><div id="sort_subj"><?php echo lang('t_LIST_subj'); ?><?php echo $subj_icon; ?></div></center></th>
                    <th><center><div id="sort_cli"><?php echo lang('t_LIST_worker'); ?><?php echo $cli_icon; ?></div></center></th>
                    <th><center><?php echo lang('t_LIST_create'); ?></center></th>
                    <th><center><?php echo lang('t_LIST_ago'); ?></center></th>
                    <th><center><div id="sort_init"><?php echo lang('t_LIST_init'); ?><?php echo $init_icon; ?></div></center></th>
                    <th><center><?php echo lang('t_LIST_to'); ?></center></th>
                    <th><center><?php echo lang('t_LIST_status'); ?></center></th>

                </tr>
                </thead>
                <tbody>

                <?php
                foreach ($results as $row) {
                    
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
                    
                    
?>




                    <tr id="tr_<?php
                    echo $row['id']; ?>" class="<?php echo $style ?>">
                        <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center><?php
                    echo $row['id']; ?></center></small></td>
                        <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center><?php echo $prio ?></center></small></td>
                        
                        <td style=" vertical-align: middle; "><a class="<?php echo $muclass; ?> pops"  
                    title="<?php echo make_html($row['subj'], 'no'); ?>"
                    data-content="<small><?php echo str_replace('"', "", make_html(strip_tags($row['msg']), 'no')); ?></small>" 
                    
                    
                    href="ticket?<?php
                    echo $row['hash_name']; ?>"><?php
                    cutstr(make_html($row['subj'], 'no')); ?></a></td>
                        
                        
                        <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>">
                        <a href="view_user?<?php echo get_user_hash_by_id($row['client_id']); ?>">
                        <?php echo get_user_val_by_id($row['client_id'], 'fio'); ?>
                        </a>
                        </small></td>
                        <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center><time id="c" datetime="<?php echo $row['date_create']; ?>"></time></center></small></td>
                        <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>"><center><time id="a" datetime="<?php echo $t_ago; ?>"></time></center></small></td>

                        <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>">
                        <a href="view_user?<?php echo get_user_hash_by_id($row['user_init_id']); ?>">
                        <?php
                    echo nameshort(name_of_user_ret($row['user_init_id'])); ?>
                        </a>
                        </small></td>

                        <td style=" vertical-align: middle; "><small class="<?php echo $muclass; ?>">
                                <?php echo make_html($to_text, 'no') ?>
                            </small></td>
                        <td style=" vertical-align: middle; "><small><center>
                                    <?php echo $st; ?> </center>
                            </small></td>

                    </tr>
                <?php
                }
?>
                </tbody>
                </table>

                </div>



            <?php
            }
        }
        if ($mode == "set_list_count") {
            $pt = $_POST['pt'];
            $v = $_POST['v'];
            if ($pt == "in") {
                $_SESSION['hd.rustem_list_in'] = $v;
            } else if ($pt == "out") {
                $_SESSION['hd.rustem_list_out'] = $v;
            } else if ($pt == "arch") {
                $_SESSION['hd.rustem_list_arch'] = $v;
            }
        }
        
        if ($mode == "sort_list") {
            $pt = $_POST['pt'];
            $sort_type = $_POST['st'];
            
            if ($pt == "in") {
                
                switch ($sort_type) {
                    case 'main':
                        unset($_SESSION['hd.rustem_sort_in']);
                        break;

                    case 'free':
                        $_SESSION['hd.rustem_sort_in'] = "free";
                        break;

                    case 'ok':
                        $_SESSION['hd.rustem_sort_in'] = "ok";
                        break;

                    case 'ilock':
                        $_SESSION['hd.rustem_sort_in'] = "ilock";
                        break;

                    case 'lock':
                        $_SESSION['hd.rustem_sort_in'] = "lock";
                        break;

                    default:
                        unset($_SESSION['hd.rustem_sort_in']);
                }
            } else if ($pt == "out") {
                switch ($sort_type) {
                    case 'main':
                        unset($_SESSION['hd.rustem_sort_out']);
                        break;

                    case 'free':
                        $_SESSION['hd.rustem_sort_out'] = "free";
                        break;

                    case 'ok':
                        $_SESSION['hd.rustem_sort_out'] = "ok";
                        break;

                    case 'ilock':
                        $_SESSION['hd.rustem_sort_out'] = "ilock";
                        break;

                    case 'lock':
                        $_SESSION['hd.rustem_sort_out'] = "lock";
                        break;

                    default:
                        unset($_SESSION['hd.rustem_sort_out']);
                }
            }
        }
        
        if ($mode == "last_news") {
            
            $uid = $_SESSION['helpdesk_user_id'];
            $unit_user = unit_of_user($uid);
            $priv_val = priv_status($uid);
            $c = 4;
            $start = 10;
            
            if (isset($_POST['v'])) {
                $c = $_POST['v'];
                $start = ($_POST['v'] + 5);
            }
            
            //$_POST['v']
            
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
            
            $u_type = get_user_val('is_client');
            
            if ($u_type == "0") {
                
                /*
                
                if ($priv_val == "0") {
                
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (unit_id IN ('.$in_query.') or user_init_id=:uid) order by last_update DESC limit :c');
                $paramss=array(':uid'=>$uid, ':c'=>$c);
                $stmt->execute(array_merge($vv,$paramss));
                $res1 = $stmt->fetchAll();
                
                
                
                foreach($res1 as $rews) {
                    $at=get_last_action_ticket($rews['id']);
                
                    $who_action=get_who_last_action_ticket($rews['id']);
                    $results[] = array(
                        'name' => $rews['id'],
                        'at' => $at,
                        'hash' => $rews['hash_name'],
                        'time' => $rews['last_update']
                    );
                
                
                }
                }
                else if ($priv_val == "1") {
                
                
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (
                ((find_in_set(:uid,user_to_id)) or (find_in_set(:n,user_to_id) and unit_id IN ('.$in_query.')))
                or user_init_id=:uid2) order by last_update DESC limit :c');
                $paramss=array(':uid'=>$uid, ':n'=>'0', ':uid2'=>$uid, ':c'=>$c);
                $stmt->execute(array_merge($vv,$paramss));
                
                
                
                $stmt->execute(array_merge($paramss));
                
                
                
                
                
                
                
                $res1 = $stmt->fetchAll();
                
                
                
                
                foreach($res1 as $rews) {
                
                
                    $at=get_last_action_ticket($rews['id']);
                    $who_action=get_who_last_action_ticket($rews['id']);
                
                
                    $results[] = array(
                        'name' => $rews['id'],
                        'at' => $at,
                        'hash' => $rews['hash_name'],
                        'time' => $rews['last_update']
                    );
                
                }
                
                
                
                }
                else if ($priv_val == "2") {
                
                
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets order by last_update DESC limit :c');
                $stmt->execute(array(':c'=>$c));
                $res1 = $stmt->fetchAll();
                
                
                
                
                
                foreach($res1 as $rews) {
                    $at=get_last_action_ticket($rews['id']);
                    $who_action=get_who_last_action_ticket($rews['id']);
                
                
                    $results[] = array(
                        'name' => $rews['id'],
                        'at' => $at,
                        'hash' => $rews['hash_name'],
                        'time' => $rews['last_update']
                    );
                
                }
                
                
                
                }
                */
            } else if ($u_type == "1") {
                
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where user_init_id=:cid and client_id=:cid2 order by last_update DESC limit :c');
                
                $stmt->execute(array(':cid' => $uid, ':cid2' => $uid, ':c' => $c));
                $res1 = $stmt->fetchAll();
                
                foreach ($res1 as $rews) {
                    $at = get_last_action_ticket($rews['id']);
                    
                    $who_action = get_who_last_action_ticket($rews['id']);
                    $results[] = array('name' => $rews['id'], 'at' => $at, 'hash' => $rews['hash_name'], 'time' => $rews['last_update']);
                }
            }
        }
        
        if ($mode == "update_status_time") {
            $uid = $_SESSION['helpdesk_user_id'];
            $stmt = $dbConnection->prepare('update users set last_time=:n where id=:cid');
            $stmt->execute(array(':cid' => $uid, ':n' => $CONF['now_dt']));
        }
        
        if ($mode == "check_update") {
            $pm = ($_POST['type']);
            $uid = $_SESSION['helpdesk_user_id'];
            $lu = ($_POST['last_update']);
            
            $current_ticket_update = get_last_ticket($pm, $uid);
            
            if (strtotime($current_ticket_update) > strtotime($lu)) {
                echo $current_ticket_update;
            }
            if (strtotime($current_ticket_update) <= strtotime($lu)) {
                echo "no";
            }
            
            //update
            $stmt = $dbConnection->prepare('update users set last_time=:n where id=:cid');
            $stmt->execute(array(':cid' => $uid, ':n' => $CONF['now_dt']));
        }
        
        if ($mode == "get_noty_actions") {
            $type_op = ($_POST['type']);
            $uid = $_SESSION['helpdesk_user_id'];
            $ticket_id = $_POST['ticket_id'];
            
            $priv_val = priv_status($uid);
            
            switch ($type_op) {
                case 'ticket_create':
                    $at = get_last_msg_ticket($ticket_id, 'create');
                    break;

                case 'ticket_refer':
                    $at = get_last_msg_ticket($ticket_id, 'refer');
                    break;

                case 'ticket_ok':
                    $at = get_last_msg_ticket($ticket_id, 'ok');
                    break;

                case 'ticket_no_ok':
                    $at = get_last_msg_ticket($ticket_id, 'no_ok');
                    break;

                case 'ticket_lock':
                    $at = get_last_msg_ticket($ticket_id, 'lock');
                    break;

                case 'ticket_unlock':
                    $at = get_last_msg_ticket($ticket_id, 'unlock');
                    break;

                case 'ticket_comment':
                    $at = get_last_msg_ticket($ticket_id, 'comment');
                    break;
            }
            
            $results[] = array('url' => $CONF['hostname'], 'up' => lang('JS_up'),
             //обновлено
            'ticket' => lang('JS_ticket'),
             //Заявка
            'name' => $ticket_id, 'at' => $at,
             //слова
            'hash' => get_ticket_hash_by_id($ticket_id), 'time' => "<time id=\"b\" datetime=\"" . date("Y-m-d H:i:s") . "\"></time>"
             //время
            );
            
            /*
            if ($priv_val == "0") {
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where id=:tid');
                
                $paramss=array(':uid'=>$uid, ':lu'=>$lu);
                $stmt->execute(array_merge($vv,$paramss));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {
            
                    $at=get_last_action_ticket($rews['id']);
            
                    $who_action=get_who_last_action_ticket($rews['id']);
                    if ($who_action <> $uid) {
                        $results[] = array(
                            'url' => $CONF['hostname'],
                            'up' => lang('JS_up'),
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'at' => $at,
                            'hash' => $rews['hash_name'],
                            'time' => "<time id=\"b\" datetime=\"".$rews['last_update']."\"></time>"
                        );
                    }
            
                }
            }
            
            
            else if ($priv_val == "1") {
            //find_in_set(:uid,user_to_id)
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where (
            ((find_in_set(:uid,user_to_id)) or (find_in_set(:n,user_to_id) and unit_id IN ('.$in_query.')))
            or user_init_id=:uid2) and last_update > :lu');
                $paramss=array(':uid'=>$uid, ':lu'=>$lu, ':uid2'=>$uid, ':n'=>'0');
                $stmt->execute(array_merge($vv,$paramss));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {
            
            
                    $at=get_last_action_ticket($rews['id']);
                    $who_action=get_who_last_action_ticket($rews['id']);
                    if ($who_action <> $uid) {
            
                        $results[] = array(
                            'url' => $CONF['hostname'],
                            'up' => lang('JS_up'),
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'at' => $at,
                            'hash' => $rews['hash_name'],
                            'time' => "<time id=\"b\" datetime=\"".$rews['last_update']."\"></time>"
                        );
                    }
                }
            
            
            
            }
            else if ($priv_val == "2") {
            
                $stmt = $dbConnection->prepare('SELECT id, hash_name, last_update from tickets where last_update > :lu');
                $stmt->execute(array(':lu'=>$lu));
                $res1 = $stmt->fetchAll();
                foreach($res1 as $rews) {
            
            
                    $at=get_last_action_ticket($rews['id']);
                    $who_action=get_who_last_action_ticket($rews['id']);
                    if ($who_action <> $uid) {
            
                        $results[] = array(
                            'url' => $CONF['hostname'],
                            'up' => lang('JS_up'),
                            'ticket' => lang('JS_ticket'),
                            'name' => $rews['id'],
                            'at' => $at,
                            'hash' => $rews['hash_name'],
                            
                            'time' => "<time id=\"b\" datetime=\"".$rews['last_update']."\"></time>"
                        );
                    }
                }
            
            
            
            }
            */
            
            print json_encode($results);
        }
        if ($mode == "push_msg_action2user") {
            
            push_msg_action2user($_POST['user'], $_POST['op']);
        }
        
        if ($mode == "aprove_yes") {
            $id = ($_POST['id']);
            
            $stmt = $dbConnection->prepare('SELECT 
            id,fio,tel,unit_desc,adr ,email,login, posada, email,client_id,type_op,skype FROM approved_info where id=:id');
            $stmt->execute(array(':id' => $id));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $q_fio = ($fio['fio']);
            $q_login = ($fio['login']);
            $q_tel = ($fio['tel']);
            $q_pod = ($fio['unit_desc']);
            $q_adr = ($fio['adr']);
            $q_type_op = $fio['type_op'];
            $q_mail = ($fio['email']);
            $q_posada = ($fio['posada']);
            $q_skype = ($fio['skype']);
            $q_cid = ($fio['client_id']);
            
            if ($q_type_op == "edit") {
                
                $stmt = $dbConnection->prepare('update users set 
    fio=:qfio, 
    tel=:qtel, 
    login=:qlogin, 
    unit_desc=:qpod,
    adr=:qadr, 
    email=:qemail,
    skype=:qskype, 
    posada=:qposada 
    where id=:cid');
                
                $stmt->execute(array(':qfio' => $q_fio, ':qtel' => $q_tel, ':qlogin' => $q_login, ':qpod' => $q_pod, ':qadr' => $q_adr, ':qemail' => $q_mail, ':qposada' => $q_posada, ':qskype' => $q_skype, ':cid' => $q_cid));
            } else if ($q_type_op == "add") {
                
                $hn = md5(time());
                $stmt = $dbConnection->prepare('INSERT INTO users 
            (fio, 
            login, 
            status, 
            priv, 
            email, 
            uniq_id,
            posada,
            tel,
            skype,
            unit_desc,
            adr,
            is_client
            )
values 
            (
            :fio, 
            :login, 
            :status, 
            :priv, 
            :email, 
            :uniq_id,
            :posada,
            :tel,
            :skype,
            :unit_desc,
            :adr,
            :is_client
            )');
                $stmt->execute(array(':fio' => $q_fio, ':login' => $q_login, ':status' => '0', ':priv' => '1', ':email' => $q_mail, ':uniq_id' => $hn, ':posada' => $q_posada, ':tel' => $q_tel, ':skype' => $q_skype, ':unit_desc' => $q_pod, ':adr' => $q_adr, ':is_client' => '1'));
            }
            
            $stmt = $dbConnection->prepare('delete from approved_info where id=:id');
            $stmt->execute(array(':id' => $id));
        }
        if ($mode == "aprove_no") {
            $id = ($_POST['id']);
            
            $stmt = $dbConnection->prepare('delete from approved_info where id=:id');
            $stmt->execute(array(':id' => $id));
        }
        
        if ($mode == "conf_edit_pb") {
            update_val_by_key("pb_api", $_POST['api']);
?>
                <div class="alert alert-success">
                    Ok!
                </div>
        <?php
        }
        
        if ($mode == "conf_edit_mail") {
            update_val_by_key("mail_type", $_POST['type']);
            update_val_by_key("mail_active", $_POST['mail_active']);
            update_val_by_key("mail_host", $_POST['host']);
            update_val_by_key("mail_port", $_POST['port']);
            update_val_by_key("mail_auth", $_POST['auth']);
            update_val_by_key("mail_auth_type", $_POST['auth_type']);
            update_val_by_key("mail_username", $_POST['username']);
            update_val_by_key("mail_password", $_POST['password']);
            update_val_by_key("mail_from", $_POST['from']);
            
            //update_val_by_key("mail_debug", $_POST['debug']);
            
            
?>
                <div class="alert alert-success">
                    <?php echo lang('PROFILE_msg_ok'); ?>
                </div>
        <?php
        }
        
        if ($mode == "conf_edit_main") {
            update_val_by_key("ldap_ip", $_POST['ldap']);
            update_val_by_key("ldap_domain", $_POST['ldapd']);
            update_val_by_key("name_of_firm", $_POST['name_of_firm']);
            update_val_by_key("title_header", $_POST['title_header']);
            update_val_by_key("hostname", $_POST['hostname']);
            update_val_by_key("days2arch", $_POST['days2arch']);
            update_val_by_key("first_login", $_POST['first_login']);
            update_val_by_key("fix_subj", $_POST['fix_subj']);
            update_val_by_key("file_uploads", $_POST['file_uploads']);
            update_val_by_key("node_port", $_POST['node_port']);
            update_val_by_key("time_zone", $_POST['time_zone']);
            update_val_by_key("allow_register", $_POST['allow_register']);
            $bodytag = str_replace(",", "|", $_POST['file_types']);
            
            update_val_by_key("file_types", $bodytag);
            update_val_by_key("file_size", $_POST['file_size']);
            update_val_by_key("mail", $_POST['mail']);
?>
                <div class="alert alert-success">
                    <?php echo lang('PROFILE_msg_ok'); ?>
                </div>
        <?php
        }
        
        //del_profile_img
        if ($mode == "del_profile_img") {
            
            $id = $_SESSION['helpdesk_user_id'];
            $stmt = $dbConnection->prepare('update users set usr_img=:s where id=:id');
            $stmt->execute(array(':id' => $id, ':s' => ''));
        }
        
        if ($mode == "edit_profile_main_client") {
            $fio = ($_POST['fio']);
            $m = ($_POST['mail']);
            $id = $_SESSION['helpdesk_user_id'];
            $langu = ($_POST['lang']);
            $skype = ($_POST['skype']);
            $tel = ($_POST['tel']);
            $adr = ($_POST['adr']);
            
            $ec = 0;
            
            if (!validate_email($m)) {
                $ec = 1;
            }
            if (!validate_exist_mail($m)) {
                $ec = 1;
            }
            
            if ($ec == 0) {
                $stmt = $dbConnection->prepare('update users set fio=:fio, skype=:s, tel=:t, email=:m, lang=:langu,
                adr=:adr,posada=:posada,unit_desc=:unitss where id=:id');
                $stmt->execute(array(':id' => $id, ':m' => $m, ':langu' => $langu, ':s' => $skype, ':t' => $tel, ':adr' => $adr, ':fio' => $fio));
?>
                <div class="alert alert-success">
                    <?php echo lang('PROFILE_msg_ok'); ?>
                </div>
            <?php
            }
            if ($ec == 1) {
?>
                <div class="alert alert-danger">
                    <?php echo lang('PROFILE_msg_error'); ?>
                </div>
            <?php
            }
        }
        
        if ($mode == "edit_profile_main") {
            $m = ($_POST['mail']);
            $id = $_SESSION['helpdesk_user_id'];
            $langu = ($_POST['lang']);
            $skype = ($_POST['skype']);
            $tel = ($_POST['tel']);
            $adr = ($_POST['adr']);
            $fio = ($_POST['fio']);
            $posada = ($_POST['posada']);
            $unitss = ($_POST['unit']);
            
            $ec = 0;
            if (!validate_email($m)) {
                $ec = 1;
            }
            if (!validate_exist_mail($m)) {
                $ec = 1;
            }
            
            if ($ec == 0) {
                $stmt = $dbConnection->prepare('update users set fio=:fio, skype=:s, tel=:t, email=:m, lang=:langu,
                adr=:adr,posada=:posada,unit_desc=:unitss where id=:id');
                $stmt->execute(array(':id' => $id, ':m' => $m, ':langu' => $langu, ':s' => $skype, ':t' => $tel, ':adr' => $adr, ':posada' => $posada, ':unitss' => $unitss, ':fio' => $fio));
?>
                <div class="alert alert-success">
                    <?php echo lang('PROFILE_msg_ok'); ?>
                </div>
            <?php
            }
            if ($ec == 1) {
?>
                <div class="alert alert-danger">
                    <?php echo lang('PROFILE_msg_error'); ?>
                </div>
            <?php
            }
        }
        
        if ($mode == "edit_profile_pass") {
            $p_old = md5(($_POST['old_pass']));
            $p_new = md5(($_POST['new_pass']));
            $p_new2 = md5(($_POST['new_pass2']));
            $id = ($_POST['id']);
            
            $stmt = $dbConnection->prepare('select pass from users where id=:id');
            $stmt->execute(array(':id' => $id));
            $total_ticket = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $pass_orig = $total_ticket['pass'];
            
            $ec = 0;
            
            if ($pass_orig <> $p_old) {
                $ec = 1;
                $text = lang('PROFILE_msg_pass_err');
            }
            
            if ($p_new <> $p_new2) {
                $ec = 1;
                $text = lang('PROFILE_msg_pass_err2');
            }
            
            if (strlen($p_new) < 3) {
                $ec = 1;
                $text = lang('PROFILE_msg_pass_err3');
            }
            
            if ($ec == 0) {
                
                $stmt = $dbConnection->prepare('update users set pass=:p_new where id=:id');
                $stmt->execute(array(':id' => $id, ':p_new' => $p_new));
                
                session_destroy();
                unset($_SESSION);
                session_unset();
                setcookie('authhash_uid', "");
                setcookie('authhash_code', "");
                unset($_COOKIE['authhash_uid']);
                unset($_COOKIE['authhash_code']);
?>
                <div class="alert alert-success">
                    <?php echo lang('PROFILE_msg_pass_ok'); ?>
                </div>
            <?php
            }
            if ($ec == 1) {
?>
                <div class="alert alert-danger">
                    <?php echo lang('PROFILE_msg_te'); ?> <?php echo $text; ?>
                </div>
            <?php
            }
        }
        
        if ($mode == "subj_del") {
            $id = ($_POST['id']);
            
            $stmt = $dbConnection->prepare('delete from subj where id=:id');
            $stmt->execute(array(':id' => $id));
            
            $stmt = $dbConnection->prepare('select id, name from subj');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?php echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
            foreach ($res1 as $row) {
?>
                    <tr id="tr_<?php echo $row['id']; ?>">


                        <td><small><center><?php echo $row['id']; ?></center></small></td>
                        <td><small><?php echo $row['name']; ?></small></td>
                        <td><small><center><button id="subj_del" type="button" class="btn btn-danger btn-xs" value="<?php echo $row['id']; ?>">del</button></center></small></td>
                    </tr>
                <?php
            } ?>



                </tbody>
            </table>
            <br>
        <?php
        }
        if ($mode == "deps_add") {
            $t = ($_POST['text']);
            
            $stmt = $dbConnection->prepare('insert into deps (name) values (:t)');
            $stmt->execute(array(':t' => $t));
            
            $stmt = $dbConnection->prepare('select id, name, status from deps where id!=:n');
            $stmt->execute(array(':n' => '0'));
            $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    
                    <th><center><?php echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
            
            //while ($row = mysql_fetch_assoc($results)) {
            foreach ($res1 as $row) {
                $cl = "";
                if ($row['status'] == "0") {
                    $id_action = "deps_show";
                    $icon = "<i class=\"fa fa-eye-slash\"></i>";
                    $cl = "active";
                }
                if ($row['status'] == "1") {
                    $id_action = "deps_hide";
                    $icon = "<i class=\"fa fa-eye\"></i>";
                    $cl = "";
                }
?>
                    <tr id="tr_<?php echo $row['id']; ?>" class="<?php echo $cl; ?>">


                        
                        <td><small><a href="#" data-pk="<?php echo $row['id'] ?>" data-url="actions.php" id="edit_deps" data-type="text"><?php echo $row['name']; ?></a></small></td>
                        <td><small><center>
                        <button id="deps_del" type="button" class="btn btn-danger btn-xs" value="<?php echo $row['id']; ?>">del</button>
                        <button id="<?php echo $id_action; ?>" type="button" class="btn btn-default btn-xs" value="<?php echo $row['id']; ?>"><?php echo $icon; ?></button>
                        
                        </center></small></td>
                    </tr>
                <?php
            } ?>



                </tbody>
            </table>
            <br>
        <?php
        }
        
        if ($mode == "files_del") {
            $id = ($_POST['id']);
            
            $stmt2 = $dbConnection->prepare('SELECT file_ext from files where file_hash=:id');
            $stmt2->execute(array(':id' => $id));
            $max = $stmt2->fetch(PDO::FETCH_NUM);
            $ext = $max[0];
            
            unlink(realpath(dirname(__FILE__)) . "/upload_files/" . $id . "." . $ext);
            $stmt = $dbConnection->prepare('delete from files where file_hash=:id');
            $stmt->execute(array(':id' => $id));
        }
        
        if ($mode == "deps_del") {
            $id = ($_POST['id']);
            
            $stmt = $dbConnection->prepare('delete from deps where id=:id');
            $stmt->execute(array(':id' => $id));
            
            /*
            найти всех пользователей у которых есть этот отдел
            обновить пользователя
            */
            
            $stmt = $dbConnection->prepare('select id, name, status from deps where id!=:n');
            $stmt->execute(array(':n' => '0'));
            $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    
                    <th><center><?php echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
            foreach ($res1 as $row) {
                $cl = "";
                if ($row['status'] == "0") {
                    $id_action = "deps_show";
                    $icon = "<i class=\"fa fa-eye-slash\"></i>";
                    $cl = "active";
                }
                if ($row['status'] == "1") {
                    $id_action = "deps_hide";
                    $icon = "<i class=\"fa fa-eye\"></i>";
                    $cl = "";
                }
?>
                    <tr id="tr_<?php echo $row['id']; ?>" class="<?php echo $cl; ?>">


                        
                        <td><small><a href="#" data-pk="<?php echo $row['id'] ?>" data-url="actions.php" id="edit_deps" data-type="text"><?php echo $row['name']; ?></a></small></td>
                        <td><small><center><button id="deps_del" type="button" class="btn btn-danger btn-xs" value="<?php echo $row['id']; ?>">del</button> <button id="<?php echo $id_action; ?>" type="button" class="btn btn-default btn-xs" value="<?php echo $row['id']; ?>"><?php echo $icon; ?></button></center></small></center></small></td>
                    </tr>
                <?php
            } ?>



                </tbody>
            </table>
            <br>
        <?php
        }
        
        if ($mode == "subj_edit") {
            $v = ($_POST['v']);
            $sid = ($_POST['id']);
            
            $stmt = $dbConnection->prepare('update subj set name=:v where id=:sid');
            $stmt->execute(array(':sid' => $sid, ':v' => $v));
            
            $stmt = $dbConnection->prepare('select id, name from subj');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?php echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
            foreach ($res1 as $row) {
?>
                    <tr id="tr_<?php echo $row['id']; ?>">


                        <td><small><center><?php echo $row['id']; ?></center></small></td>
                        <td><small><?php echo $row['name']; ?></small></td>
                        <td><small><center><button id="subj_del" type="button" class="btn btn-danger btn-xs" value="<?php echo $row['id']; ?>">del</button></center></small></td>
                    </tr>
                <?php
            } ?>



                </tbody>
            </table>
            <br>
        <?php
        }
        
        if ($mode == "subj_add") {
            $t = ($_POST['text']);
            
            $stmt = $dbConnection->prepare('insert into subj (name) values (:t)');
            $stmt->execute(array(':t' => $t));
            
            $stmt = $dbConnection->prepare('select id, name from subj');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?php echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
            foreach ($res1 as $row) {
?>
                    <tr id="tr_<?php echo $row['id']; ?>">


                        <td><small><center><?php echo $row['id']; ?></center></small></td>
                        <td><small><?php echo $row['name']; ?></small></td>
                        <td><small><center><button id="subj_del" type="button" class="btn btn-danger btn-xs" value="<?php echo $row['id']; ?>">del</button></center></small></td>
                    </tr>
                <?php
            } ?>



                </tbody>
            </table>
            <br>
        <?php
        }
        
        if ($mode == "posada_add") {
            $t = ($_POST['text']);
            
            $stmt = $dbConnection->prepare('insert into posada (name) values (:t)');
            $stmt->execute(array(':t' => $t));
            
            $stmt = $dbConnection->prepare('select id, name from posada');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?php echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
            foreach ($res1 as $row) {
?>
                    <tr id="tr_<?php echo $row['id']; ?>">


                        <td><small><center><?php echo $row['id']; ?></center></small></td>
                        <td><small><?php echo $row['name']; ?></small></td>
                        <td><small><center><button id="posada_del" type="button" class="btn btn-danger btn-xs" value="<?php echo $row['id']; ?>">del</button></center></small></td>
                    </tr>
                <?php
            } ?>



                </tbody>
            </table>
            <br>
        <?php
        }
        
                if ($mode == "cron_del") {
            $id = ($_POST['id']);
            
            $stmt = $dbConnection->prepare('delete from scheduler_ticket where id=:id');
            $stmt->execute(array(':id' => $id));
            }
        
        if ($mode == "posada_del") {
            $id = ($_POST['id']);
            
            $stmt = $dbConnection->prepare('delete from posada where id=:id');
            $stmt->execute(array(':id' => $id));
            
            $stmt = $dbConnection->prepare('select id, name from posada');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?php echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
            foreach ($res1 as $row) {
?>
                    <tr id="tr_<?php echo $row['id']; ?>">


                        <td><small><center><?php echo $row['id']; ?></center></small></td>
                        <td><small><?php echo $row['name']; ?></small></td>
                        <td><small><center><button id="posada_del" type="button" class="btn btn-danger btn-xs" value="<?php echo $row['id']; ?>">del</button></center></small></td>
                    </tr>
                <?php
            } ?>



                </tbody>
            </table>
            <br>
        <?php
        }
        
        if ($mode == "units_add") {
            $t = ($_POST['text']);
            
            $stmt = $dbConnection->prepare('insert into units (name) values (:t)');
            $stmt->execute(array(':t' => $t));
            
            $stmt = $dbConnection->prepare('select id, name from units');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?php echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
            foreach ($res1 as $row) {
?>
                    <tr id="tr_<?php echo $row['id']; ?>">


                        <td><small><center><?php echo $row['id']; ?></center></small></td>
                        <td><small><?php echo $row['name']; ?></small></td>
                        <td><small><center><button id="units_del" type="button" class="btn btn-danger btn-xs" value="<?php echo $row['id']; ?>">del</button></center></small></td>
                    </tr>
                <?php
            } ?>



                </tbody>
            </table>
            <br>
        <?php
        }
        if ($mode == "units_del") {
            $id = ($_POST['id']);
            
            $stmt = $dbConnection->prepare('delete from units where id=:id');
            $stmt->execute(array(':id' => $id));
            
            $stmt = $dbConnection->prepare('select id, name from units');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
?>



            <table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
                <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center><?php echo lang('TABLE_name'); ?></center></th>
                    <th><center><?php echo lang('TABLE_action'); ?></center></th>
                </tr>
                </thead>
                <tbody>
                <?php
            foreach ($res1 as $row) {
?>
                    <tr id="tr_<?php echo $row['id']; ?>">


                        <td><small><center><?php echo $row['id']; ?></center></small></td>
                        <td><small><?php echo $row['name']; ?></small></td>
                        <td><small><center><button id="units_del" type="button" class="btn btn-danger btn-xs" value="<?php echo $row['id']; ?>">del</button></center></small></td>
                    </tr>
                <?php
            } ?>



                </tbody>
            </table>
            <br>
        <?php
        }
        if ($mode == "add_user_approve") {
            
            $fio = ($_POST['fio']);
            $login = ($_POST['login']);
            $posada = ($_POST['posada']);
            $pid = ($_POST['pidrozdil']);
            $tel = ($_POST['tel']);
            $adr = ($_POST['adr']);
            $mail = ($_POST['mail']);
            $skype = ($_POST['skype']);
            $uf = $_SESSION['helpdesk_user_id'];
            
            $stmt = $dbConnection->prepare('insert into approved_info
(fio,login,tel, unit_desc, adr, email, posada,skype,type_op, user_from, date_app)
VALUES (:fio, :login, :tel, :unit_desc, :adr, :email, :posada,:skype,:type_op, :user_from,  :n)');
            
            $stmt->execute(array(':fio' => $fio, ':tel' => $tel, ':login' => $login, ':unit_desc' => $pid, ':adr' => $adr, ':email' => $mail, ':posada' => $posada, ':skype' => $skype, ':type_op' => 'add', ':user_from' => $uf, ':n' => $CONF['now_dt']));
?>
            <div class="alert alert-success">
                <?php echo lang('PROFILE_msg_send'); ?>
            </div>
        
        <?php
        }
        
        if ($mode == "edit_user_approve") {
            
            $fio = ($_POST['fio']);
            $login = ($_POST['login']);
            $posada = ($_POST['posada']);
            $pid = ($_POST['pidrozdil']);
            $tel = ($_POST['tel']);
            $adr = ($_POST['adr']);
            $mail = ($_POST['mail']);
            $skype = ($_POST['skype']);
            $uf = $_SESSION['helpdesk_user_id'];
            $cid = get_user_val_by_hash($_POST['cid'], 'id');
            
            $stmt = $dbConnection->prepare('insert into approved_info
(fio,login,tel, unit_desc, adr, email, posada,skype,type_op, user_from, client_id, date_app)
VALUES (:fio, :login, :tel, :unit_desc, :adr, :email, :posada,:skype,:type_op, :user_from, :cid,  :n)');
            
            $stmt->execute(array(':fio' => $fio, ':tel' => $tel, ':login' => $login, ':unit_desc' => $pid, ':adr' => $adr, ':email' => $mail, ':posada' => $posada, ':skype' => $skype, ':type_op' => 'edit', ':user_from' => $uf, ':cid' => $cid, ':n' => $CONF['now_dt']));
?>
            <div class="alert alert-success">
                <?php echo lang('PROFILE_msg_send'); ?>
            </div>
        
        <?php
        }
        
        if ($mode == "arch_now") {
            $user = ($_POST['user']);
            $tid = ($_POST['tid']);
            
            $stmt = $dbConnection->prepare('SELECT arch FROM tickets where id=:tid');
            $stmt->execute(array(':tid' => $tid));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $s = $fio['arch'];
            
            if ($s == "0") {
                
                $stmt = $dbConnection->prepare('update tickets set arch=:n1, last_update=:n where id=:tid');
                $stmt->execute(array(':tid' => $tid, ':n1' => '1', ':n' => $CONF['now_dt']));
            }
            if ($s == "1") {
                $stmt = $dbConnection->prepare('update tickets set arch=:n1, last_update=:n where id=:tid');
                $stmt->execute(array(':tid' => $tid, ':n1' => '0', ':n' => $CONF['now_dt']));
            }
            
            $unow = $_SESSION['helpdesk_user_id'];
            
            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:ar, :n, :unow, :tid)');
            $stmt->execute(array(':tid' => $tid, ':unow' => $unow, ':ar' => 'arch', ':n' => $CONF['now_dt']));
        }
        
        if ($mode == "status_no_ok") {
            $user = ($_POST['user']);
            $tid = ($_POST['tid']);
            
            $stmt = $dbConnection->prepare('SELECT status, ok_by FROM tickets where id=:tid');
            $stmt->execute(array(':tid' => $tid));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $st = $fio['status'];
            $ob = $fio['ok_by'];
            
            $ps = priv_status($ob);
            
            if ($st == "0") {
                $stmt = $dbConnection->prepare('update tickets set ok_by=:user, status=:s, ok_date=:n, last_update=:nz where id=:tid');
                $stmt->execute(array(':s' => '1', ':tid' => $tid, ':user' => $user, ':n' => $CONF['now_dt'], ':nz' => $CONF['now_dt']));
                
                $unow = $_SESSION['helpdesk_user_id'];
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log 
            (msg, date_op, init_user_id, ticket_id)
            values (:ok, :n, :unow, :tid)');
                $stmt->execute(array(':ok' => 'ok', ':tid' => $tid, ':unow' => $unow, ':n' => $CONF['now_dt']));
                send_notification('ticket_ok', $tid);
?>

                <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo lang('TICKET_msg_OK'); ?></div>

            <?php
            }
            if ($st == "1") {
?>

                <div class="alert alert-danger"><?php echo lang('TICKET_msg_OK_error'); ?> <?php echo name_of_user($ob); ?></div>

            <?php
            }
        }
        if ($mode == "status_ok") {
            
            $user = ($_POST['user']);
            $tid = ($_POST['tid']);
            
            $stmt = $dbConnection->prepare('SELECT status, ok_by, user_init_id FROM tickets where id=:tid');
            $stmt->execute(array(':tid' => $tid));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $st = $fio['status'];
            $ob = $fio['ok_by'];
            $uinitd = $fio['user_init_id'];
            
            $ps = priv_status($user);
            
            if ($st == "1") {
                
                if (($ob == $user) || ($ps == "0") || ($ps == "2") || ($uinitd == $user)) {
                    
                    $stmt = $dbConnection->prepare('update tickets set ok_by=:n, status=:n1, last_update=:nz where id=:tid');
                    $stmt->execute(array(':tid' => $tid, ':n' => '0', ':n1' => '0', ':nz' => $CONF['now_dt']));
                    
                    $unow = $_SESSION['helpdesk_user_id'];
                    
                    $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:no_ok, :n, :unow, :tid)');
                    $stmt->execute(array(':tid' => $tid, ':unow' => $unow, ':no_ok' => 'no_ok', ':n' => $CONF['now_dt']));
                    
                    send_notification('ticket_no_ok', $tid);
?>

                    <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo lang('TICKET_msg_unOK'); ?></div>

                <?php
                }
            }
            if ($st == "0") {
?>
                <div class="alert alert-danger"><?php echo lang('TICKET_msg_unOK_error'); ?></div>
            <?php
            }
        }
        
        if ($mode == "lock") {
            $user = ($_POST['user']);
            $tid = ($_POST['tid']);
            
            $stmt = $dbConnection->prepare('SELECT lock_by FROM tickets where id=:tid');
            $stmt->execute(array(':tid' => $tid));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $lb = $fio['lock_by'];
            
            $ps = priv_status($lb);
            
            if ($lb == "0") {
                
                $stmt = $dbConnection->prepare('update tickets set lock_by=:user, last_update=:n where id=:tid');
                $stmt->execute(array(':tid' => $tid, ':user' => $user, ':n' => $CONF['now_dt']));
                
                $unow = $_SESSION['helpdesk_user_id'];
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:lock, :n, :unow, :tid)');
                $stmt->execute(array(':tid' => $tid, ':unow' => $unow, ':lock' => 'lock', ':n' => $CONF['now_dt']));
                
                send_notification('ticket_lock', $tid);
?>

                <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo lang('TICKET_msg_lock'); ?></div>

            <?php
            }
            if ($lb <> "0") {
?>
                <div class="alert alert-danger"><?php echo lang('TICKET_msg_lock_error'); ?> <?php echo name_of_user($lb); ?></div>
            <?php
            }
        }
        if ($mode == "unlock") {
            $tid = ($_POST['tid']);
            
            $stmt = $dbConnection->prepare('update tickets set lock_by=:n, last_update=:nz where id=:tid');
            $stmt->execute(array(':tid' => $tid, ':n' => '0', ':nz' => $CONF['now_dt']));
            
            $unow = $_SESSION['helpdesk_user_id'];
            
            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:unlock, :n, :unow, :tid)');
            $stmt->execute(array(':tid' => $tid, ':unow' => $unow, ':unlock' => 'unlock', ':n' => $CONF['now_dt']));
            send_notification('ticket_unlock', $tid);
?>

            <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo lang('TICKET_msg_unlock'); ?></div>

        <?php
        }
        
        if ($mode == "update_to") {
            
            $tid = ($_POST['ticket_id']);
            $to = ($_POST['to']);
            $tou = ($_POST['tou']);
            $tom = ($_POST['tom']);
            
            if (strlen($tom) > 2) {
                
                $x_refer_comment = '<strong><small class=\'text-danger\'>' . nameshort(name_of_user_ret($_SESSION['helpdesk_user_id'])) . ' ' . lang('REFER_comment_add') . ' (' . date(' d.m.Y h:i:s') . '):</small> </strong>' . strip_tags(xss_clean(($_POST['tom'])));
                
                $stmt = $dbConnection->prepare('update tickets set 
            unit_id=:to, 
            user_to_id=:tou, 
            msg=concat(msg,:br,:x_refer_comment), 
            lock_by=:n, 
            last_update=:nz where id=:tid');
                $stmt->execute(array(':to' => $to, ':tou' => $tou, ':br' => '<br>', ':x_refer_comment' => $x_refer_comment, ':tid' => $tid, ':n' => '0', ':nz' => $CONF['now_dt']));
            } else if (strlen($tom) <= 2) {
                
                $stmt = $dbConnection->prepare('update tickets set 
            unit_id=:to, 
            user_to_id=:tou, 
            lock_by=:n, 
            last_update=:nz where id=:tid');
                $stmt->execute(array(':to' => $to, ':tou' => $tou, ':tid' => $tid, ':n' => '0', ':nz' => $CONF['now_dt']));
            }
            
            $unow = $_SESSION['helpdesk_user_id'];
            
            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, to_user_id, ticket_id, to_unit_id) values (:refer, :n, :unow, :tou, :tid, :to)');
            $stmt->execute(array(':to' => $to, ':tou' => $tou, ':refer' => 'refer', ':tid' => $tid, ':unow' => $unow, ':n' => $CONF['now_dt']));
            
            send_notification('ticket_refer', $tid);
?>
            <div class="alert alert-success"><?php echo lang('TICKET_msg_refer'); ?></div>
        <?php
        }
        if ($mode == "edit_user") {
            $usid = ($_POST['idu']);
            $status = ($_POST['status']);
            
            $fio = ($_POST['fio']);
            $login = ($_POST['login']);
            $pass = md5($_POST['pass']);
            $priv = ($_POST['priv']);
            $mail = ($_POST['mail']);
            $mess = ($_POST['mess']);
            $mess_title = ($_POST['mess_t']);
            $tel = $_POST['tel'];
            $skype = $_POST['skype'];
            $adr = $_POST['adr'];
            $push = $_POST['push'];
            $lang = ($_POST['lang']);
            $pidrozdil = $_POST['pidrozdil'];
            $posada = $_POST['posada'];
            
            $unit = ($_POST['unit']);
            
            if ($priv == "4") {
                $is_client = "1";
                $privs = "1";
            } else if ($priv != "4") {
                $is_client = "0";
                $privs = $priv;
            }
            
            $priv_add_client = $_POST['priv_add_client'];
            $priv_edit_client = $_POST['priv_edit_client'];
            $ldap_key = $_POST['ldap_auth_key'];
            if ($ldap_key == "true") {
                $ldap_key = 1;
            } else {
                $ldap_key = 0;
            }
            if ($priv_add_client == "true") {
                $priv_add_client = 1;
            } else {
                $priv_add_client = 0;
            }
            if ($priv_edit_client == "true") {
                $priv_edit_client = 1;
            } else {
                $priv_edit_client = 0;
            }
            
            if (strlen($_POST['pass']) > 1) {
                
                $stmt = $dbConnection->prepare('update users set
                fio=:fio, 
                login=:login,
                pass=:pass,
                status=:status, 
                priv=:priv, 
                unit=:unit, 
                email=:mail, 
                messages=:mess, 
                lang=:lang, 
                ldap_key=:lk,
                priv_add_client=:priv_add_client,
                priv_edit_client=:priv_edit_client,
                pb=:pb,
                messages_title=:messages_title,
                uniq_id=:uniq_id,
                posada=:posada,
                tel=:tel,
                skype=:skype,
                unit_desc=:unit_desc,
                adr=:adr,
                is_client=:is_client
                where uniq_id=:usid
                ');
                $stmt->execute(array(':fio' => $fio, ':login' => $login, ':status' => $status, ':priv' => $privs, ':unit' => $unit, ':mail' => $mail, ':mess' => $mess, ':lang' => $lang, ':usid' => $usid, ':lk' => $ldap_key, ':pass' => $pass, ':priv_add_client' => $priv_add_client, ':priv_edit_client' => $priv_edit_client, ':pb' => $push, ':messages_title' => $mess_title, ':uniq_id' => $usid, ':posada' => $posada, ':tel' => $tel, ':skype' => $skype, ':unit_desc' => $pidrozdil, ':adr' => $adr, ':is_client' => $is_client));
            } else {
                $stmt = $dbConnection->prepare('update users set
                fio=:fio, 
                login=:login,
                status=:status, 
                priv=:priv, 
                unit=:unit, 
                email=:mail, 
                messages=:mess, 
                lang=:lang, 
                ldap_key=:lk,
                priv_add_client=:priv_add_client,
                priv_edit_client=:priv_edit_client,
                pb=:pb,
                messages_title=:messages_title,
                uniq_id=:uniq_id,
                posada=:posada,
                tel=:tel,
                skype=:skype,
                unit_desc=:unit_desc,
                adr=:adr,
                is_client=:is_client
                where uniq_id=:usid
                ');
                $stmt->execute(array(':fio' => $fio, ':login' => $login, ':status' => $status, ':priv' => $privs, ':unit' => $unit, ':mail' => $mail, ':mess' => $mess, ':lang' => $lang, ':usid' => $usid, ':lk' => $ldap_key, ':priv_add_client' => $priv_add_client, ':priv_edit_client' => $priv_edit_client, ':pb' => $push, ':messages_title' => $mess_title, ':uniq_id' => $usid, ':posada' => $posada, ':tel' => $tel, ':skype' => $skype, ':unit_desc' => $pidrozdil, ':adr' => $adr, ':is_client' => $is_client));
            }
            
            /*
            $fio=($_POST['fio']);
            $login=($_POST['login']);
            
            $unit=($_POST['unit']);
            $priv=($_POST['priv']);
            $status=($_POST['status']);
            $usid=($_POST['idu']);
            $mail=($_POST['mail']);
            $mess=($_POST['mess']);
            $lang=($_POST['lang']);
            $priv_add_client=$_POST['priv_add_client'];
            $priv_edit_client=$_POST['priv_edit_client'];
            $ldap_key=$_POST['ldap_auth_key'];
            if ($ldap_key == "true") {$ldap_key=1;} else {$ldap_key=0;}
            if ($priv_add_client == "true") {$priv_add_client=1;} else {$priv_add_client=0;}
            if ($priv_edit_client == "true") {$priv_edit_client=1;} else {$priv_edit_client=0;}
            
            if (strlen($_POST['pass'])>1) {
                $p=md5($_POST['pass']);
                
                $stmt = $dbConnection->prepare('update users set 
                fio=:fio, 
                login=:login,
                pass=:pass,
                status=:status, 
                priv=:priv, 
                unit=:unit, 
                email=:mail, 
                messages=:mess, 
                lang=:lang, 
                ldap_key=:lk,
                priv_add_client=:priv_add_client,
                priv_edit_client=:priv_edit_client  
                where id=:usid');
                $stmt->execute(array(
                ':fio'=>$fio, 
                ':login'=>$login, 
                ':status'=>$status, 
                ':priv'=>$priv, 
                ':unit'=>$unit, 
                ':mail'=>$mail, 
                ':mess'=>$mess, 
                ':lang'=>$lang, 
                ':usid'=>$usid, 
                ':lk'=>$ldap_key,
                ':pass'=>$p,
                ':priv_add_client'=>$priv_add_client,
                ':priv_edit_client'=>$priv_edit_client));
            
            }
            else { $p="";
                $stmt = $dbConnection->prepare('update users set fio=:fio, login=:login, status=:status, priv=:priv, unit=:unit, email=:mail, messages=:mess, lang=:lang, ldap_key=:lk,priv_add_client=:priv_add_client,priv_edit_client=:priv_edit_client where id=:usid');
                $stmt->execute(array(':fio'=>$fio, ':login'=>$login, ':status'=>$status, ':priv'=>$priv, ':unit'=>$unit, ':mail'=>$mail, ':mess'=>$mess, ':lang'=>$lang, ':usid'=>$usid,':lk'=>$ldap_key,':priv_add_client'=>$priv_add_client,':priv_edit_client'=>$priv_edit_client));
            
            }
            
            */
        }
        
        if ($mode == "add_user") {
            $fio = ($_POST['fio']);
            $login = ($_POST['login']);
            $pass = md5($_POST['pass']);
            $priv = ($_POST['priv']);
            $mail = ($_POST['mail']);
            $mess = ($_POST['mess']);
            $mess_title = ($_POST['mess_t']);
            $tel = $_POST['tel'];
            $skype = $_POST['skype'];
            $adr = $_POST['adr'];
            $push = $_POST['push'];
            $lang = ($_POST['lang']);
            $pidrozdil = $_POST['pidrozdil'];
            $posada = $_POST['posada'];
            
            //$hidden=array();
            //$hidden = ($_POST['unit']);
            //print_r($hidden);
            $unit = ($_POST['unit']);
            
            if ($priv == "4") {
                $is_client = "1";
                $privs = "1";
            } else if ($priv != "4") {
                $is_client = "0";
                $privs = $priv;
            }
            
            $priv_add_client = $_POST['priv_add_client'];
            $priv_edit_client = $_POST['priv_edit_client'];
            $ldap_key = $_POST['ldap_auth_key'];
            if ($ldap_key == "true") {
                $ldap_key = 1;
            } else {
                $ldap_key = 0;
            }
            if ($priv_add_client == "true") {
                $priv_add_client = 1;
            } else {
                $priv_add_client = 0;
            }
            if ($priv_edit_client == "true") {
                $priv_edit_client = 1;
            } else {
                $priv_edit_client = 0;
            }
            
            $hn = md5(time());
            $stmt = $dbConnection->prepare('INSERT INTO users 
            (fio, 
            login, 
            pass, 
            status, 
            priv, 
            unit, 
            email, 
            messages, 
            lang, 
            priv_add_client, 
            priv_edit_client, 
            ldap_key,
            pb,
            messages_title,
            uniq_id,
            posada,
            tel,
            skype,
            unit_desc,
            adr,
            is_client
            )
values 
            (:fio, 
            :login, 
            :pass, 
            :one, 
            :priv, 
            :unit, 
            :mail, 
            :mess, 
            :lang, 
            :priv_add_client, 
            :priv_edit_client, 
            :lk,
            :pb,
            :messages_title,
            :uniq_id,
            :posada,
            :tel,
            :skype,
            :unit_desc,
            :adr,
            :is_client
            )');
            $stmt->execute(array(':fio' => $fio, ':login' => $login, ':pass' => $pass, ':one' => '1', ':priv' => $privs, ':unit' => $unit, ':mail' => $mail, ':mess' => $mess, ':lang' => $lang, ':priv_add_client' => $priv_add_client, ':priv_edit_client' => $priv_edit_client, ':lk' => $ldap_key, ':pb' => $push, ':messages_title' => $mess_title, ':uniq_id' => $hn, ':posada' => $posada, ':tel' => $tel, ':skype' => $skype, ':unit_desc' => $pidrozdil, ':adr' => $adr, ':is_client' => $is_client));
        }
        if ($mode == "save_edit_ticket") {
            
            $t_hash = $_POST['t_hash'];
            $subj = $_POST['subj'];
            $msg = $_POST['msg'];
            $prio = $_POST['prio'];
            
            $stmt = $dbConnection->prepare('SELECT id, subj, msg, prio FROM tickets where hash_name=:hn');
            $stmt->execute(array(':hn' => $t_hash));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
            $pk = $fio['id'];
            
            if ($prio != $fio['prio']) {
                $stmt = $dbConnection->prepare('update tickets set prio=:v, last_edit=:n, last_update=:nz where hash_name=:pk');
                $stmt->execute(array(':v' => $prio, ':pk' => $t_hash, ':n' => $CONF['now_dt'], ':nz' => $CONF['now_dt']));
                $unow = $_SESSION['helpdesk_user_id'];
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:edit_subj, :n, :unow, :pk)');
                $stmt->execute(array(':edit_subj' => 'edit_prio', ':pk' => $pk, ':unow' => $unow, ':n' => $CONF['now_dt']));
            }
            
            if ($subj != $fio['subj']) {
                $stmt = $dbConnection->prepare('update tickets set subj=:v, last_edit=:n, last_update=:nz where hash_name=:pk');
                $stmt->execute(array(':v' => $subj, ':pk' => $t_hash, ':n' => $CONF['now_dt'], ':nz' => $CONF['now_dt']));
                
                $unow = $_SESSION['helpdesk_user_id'];
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:edit_subj, :n, :unow, :pk)');
                $stmt->execute(array(':edit_subj' => 'edit_subj', ':pk' => $pk, ':unow' => $unow, ':n' => $CONF['now_dt']));
            }
            
            if ($msg != $fio['msg']) {
                
                $stmt = $dbConnection->prepare('update tickets set msg=:v, last_edit=:n, last_update=:nz where hash_name=:pk');
                $stmt->execute(array(':v' => $msg, ':pk' => $t_hash, ':n' => $CONF['now_dt'], ':nz' => $CONF['now_dt']));
                
                $unow = $_SESSION['helpdesk_user_id'];
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:edit_msg, :n, :unow, :pk)');
                $stmt->execute(array(':edit_msg' => 'edit_msg', ':pk' => $pk, ':unow' => $unow, ':n' => $CONF['now_dt']));
            }
        }
        
        if ($mode == "deps_hide") {
            $id = ($_POST['id']);
            $stmt = $dbConnection->prepare('update deps set status=:v where id=:id');
            $stmt->execute(array(':v' => '0', ':id' => $id));
        }
        if ($mode == "deps_show") {
            $id = ($_POST['id']);
            $stmt = $dbConnection->prepare('update deps set status=:v where id=:id');
            $stmt->execute(array(':v' => '1', ':id' => $id));
        }
        
        if ($mode == "edit_deps") {
            $v = ($_POST['value']);
            $pk = ($_POST['pk']);
            
            $stmt = $dbConnection->prepare('update deps set name=:v where id=:pk');
            $stmt->execute(array(':v' => $v, ':pk' => $pk));
        }
        
        if ($mode == "recalculate_messages") {
            $tm = get_total_unread_messages();
            if ($tm != 0) {
                $atm = "
    <small class=\"badge pull-right bg-yellow\">" . $tm . "</small>";
            } else if ($tm == 0) {
                $atm = "";
            }
            
            echo $atm;
        }
        
        if ($mode == "messages_title_username") {
            $uid = $_POST['uid'];
            
            echo "Переписка с " . get_user_val_by_id($uid, 'fio');
        }
        
        if ($mode == "recalculate_messages_ul") {
            $uniq_id = $_POST['uid'];
            
            $stmt = $dbConnection->prepare('SELECT count(id) as cou from messages where
        ((user_from=:ufrom and user_to=:uto)) and is_read=0
         ');
            $stmt->execute(array(':ufrom' => $uniq_id, ':uto' => $_SESSION['helpdesk_user_id']));
            
            $tt = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($tt['cou'] != 0) {
                $tt = "<small class=\"badge pull-right\">" . $tt['cou'] . "</small>";
            } else {
                $tt = "";
            }
            
            echo $tt;
        }
        
        if ($mode == "get_tt_label") {
            
            $newt = get_total_tickets_free();
            
            if ($newt != 0) {
                $newtickets = " <small class=\"badge pull-right bg-red\">" . $newt . "</small>";
            } else if ($newt == 0) {
                $newtickets = "";
            }
            echo $newtickets;
        }
        
        if ($mode == "total_msgs_main") {
            
            echo get_total_msgs_main();
        }
        
        if ($mode == "message_user_list") {
            $t = $_POST['t'];
            if ($_POST['t']) {
                $stmt = $dbConnection->prepare('SELECT id, fio from users where fio like :t and id!=:uid order by fio ASC limit 10');
                $stmt->execute(array(':t' => '%' . $t . '%', ':uid' => $_SESSION['helpdesk_user_id']));
                
                $re = $stmt->fetchAll();
?>
                                    
                                            
                                                <ul class="nav nav-pills nav-stacked">
                                                    <?php
                
                if (empty($re)) {
                    
                    echo "no ";
                } else if (!empty($re)) {
                    
                    foreach ($re as $rews) {
                        $uniq_id = $rews['id'];
                        
                        $stmt = $dbConnection->prepare('SELECT count(id) as cou from messages where
        ((user_from=:ufrom and user_to=:uto)) and is_read=0
         ');
                        $stmt->execute(array(':ufrom' => $uniq_id, ':uto' => $_SESSION['helpdesk_user_id']));
                        
                        $tt = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($tt['cou'] != 0) {
                            $tt = "<small id=\"ul_label_" . $uniq_id . "\"><small class=\"badge pull-right\">" . $tt['cou'] . "</small></small>";
                        } else {
                            $tt = "<small id=\"ul_label_" . $uniq_id . "\"></small>";
                        }
?>
                                                    <li class="user_li" user-id="<?php echo $uniq_id; ?>">
                                                    <a href="#">
                                                        <img style="width: 25px;height: 25px;" src="<?php echo get_user_img_by_id($uniq_id); ?>" class="img-circle" alt="User Image">
                                                     <?php echo nameshort(name_of_user_ret_nolink($uniq_id)); ?>
                                                     
                                                     <?php echo $tt; ?>
                                                     </a>
                                                     </li>
                                                    <?php
                    }
                }
?>
                                                </ul>
                                                
<?php
            } else if (!$_POST['t']) {
                $stmt = $dbConnection->prepare('SELECT id, user_from,user_to from messages where
                        (user_to=:u_to)
                        order by is_read, date_op ASC');
                $stmt->execute(array(':u_to' => $_SESSION['helpdesk_user_id']));
                
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
?>
                                        
                                        <ul class="nav nav-pills nav-stacked">
                                                    <?php
                
                foreach ($user_arr as $uniq_id) {
                    
                    $stmt = $dbConnection->prepare('SELECT count(id) as cou from messages where
        ((user_from=:ufrom and user_to=:uto)) and is_read=0
         ');
                    $stmt->execute(array(':ufrom' => $uniq_id, ':uto' => $_SESSION['helpdesk_user_id']));
                    
                    $tt = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($tt['cou'] != 0) {
                        $tt = "<small id=\"ul_label_" . $uniq_id . "\"><small class=\"badge pull-right\">" . $tt['cou'] . "</small></small>";
                    } else {
                        $tt = "<small id=\"ul_label_" . $uniq_id . "\"></small>";
                    }
?>
                                                    <li class="user_li" user-id="<?php echo $uniq_id; ?>">
                                                    <a href="#">
                                                        <img style="width: 25px;height: 25px;" src="<?php echo get_user_img_by_id($uniq_id); ?>" class="img-circle" alt="User Image">
                                                     <?php echo nameshort(name_of_user_ret_nolink($uniq_id)); ?>
                                                     
                                                     <?php echo $tt; ?>
                                                     </a>
                                                     </li>
                                                    <?php
                }
?>
                                                    
                                                    
                                                </ul>
<?php
            }
        }
        
        if ($mode == "messages_send") {
            
            $user_comment = $_SESSION['helpdesk_user_id'];
            $text_comment = $_POST['textmsg'];
            $target = $_POST['target'];
            
            //chat_msg_id
            
            $stmt_m = $dbConnection->prepare("SELECT MAX(id) max_id FROM messages");
            $stmt_m->execute();
            $max_id_msgs = $stmt_m->fetch(PDO::FETCH_NUM);
            
            $max_id_res_msgs = $max_id_msgs[0] + 1;
            
            if ($target == "main") {
                $a = "0";
                $b = "main";
            } else if ($target != "main") {
                $a = $target;
                $b = "0";
                
                $unid = get_user_val_by_id($target, 'uniq_id');
                
                $stmt = $dbConnection->prepare('INSERT INTO notification_msg_pool (delivers_id,type_op,ticket_id,dt,chat_msg_id)
                    values (:delivers_id,:type_op,:ticket_id,:n,:chat_msg_id)');
                $stmt->execute(array(':delivers_id' => $unid, ':type_op' => 'message_send', ':ticket_id' => $user_comment, ':chat_msg_id' => $max_id_res_msgs, ':n' => $CONF['now_dt']));
            }
            
            $stmt = $dbConnection->prepare('INSERT INTO messages (id, user_from,user_to,date_op,msg,type_msg,is_read)
                    values (:ida, :user_from, :user_to, :n, :msg, :type_msg, :is_read)');
            $stmt->execute(array(':ida' => $max_id_res_msgs, ':user_from' => $user_comment, ':user_to' => $a, ':msg' => $text_comment, ':type_msg' => $b, ':is_read' => '0', ':n' => $CONF['now_dt']));
            
            view_messages($target);
            
            //echo $target.'=='.$_SESSION['helpdesk_user_id'];
            
        }
        
        if ($mode == "messages_view") {
            $target = $_POST['target'];
            
            view_messages($target);
        }
        
        if ($mode == "view_comment") {
            
            $tid_comment = ($_POST['tid']);
            view_comment($tid_comment);
        }
        
        if ($mode == "add_comment") {
            
            $user_comment = $_SESSION['helpdesk_user_id'];
            $tid_comment = get_ticket_id_by_hash($_POST['tid']);
            
            //$text_comment=strip_tags(xss_clean(($_POST['textmsg'])),"<b><a><br>");
            $text_comment = $_POST['textmsg'];
            
            //if ($_SESSION['helpdesk_user_type'] == "user") {
            
            $stmt = $dbConnection->prepare('INSERT INTO comments (t_id, user_id, comment_text, dt)
values (:tid_comment, :user_comment, :text_comment, :n)');
            $stmt->execute(array(':tid_comment' => $tid_comment, ':user_comment' => $user_comment, ':text_comment' => $text_comment, ':n' => $CONF['now_dt']));
            
            $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id)
values (:comment, :n, :user_comment, :tid_comment)');
            $stmt->execute(array(':tid_comment' => $tid_comment, ':user_comment' => $user_comment, ':comment' => 'comment', ':n' => $CONF['now_dt']));
            
            send_notification('ticket_comment', $tid_comment);
            
            //}
            
            $stmt = $dbConnection->prepare('update tickets set last_update=:n where id=:tid_comment');
            $stmt->execute(array(':tid_comment' => $tid_comment, ':n' => $CONF['now_dt']));
            
            view_comment($tid_comment);
        }
        
        if ($mode == "upload_file") {
            $name = $_POST['name'];
            $hn = $_POST['hn'];
            
            $stmt = $dbConnection->prepare('insert into files (name, h_name) VALUES (:name, :hn)');
            $stmt->execute(array(':name' => $name, ':hn' => $hn));
        }
        if ($mode == "conf_test_mail") {
            
            /*
            
            if (get_conf_param('mail_auth_type') != "none")
            {
            $mail->SMTPSecure = $CONF_MAIL['auth_type'];
            }
            
            
            sendmail?
            SMTP?
            
            */
            if (get_conf_param('mail_type') == "sendmail") {
                $mail = new PHPMailer(true);
                $mail->IsSendmail();
                 // telling the class to use SendMail transport
                
                try {
                    $mail->AddReplyTo($CONF_MAIL['from'], $CONF['name_of_firm']);
                    $mail->AddAddress($CONF['mail'], 'admin helpdesk');
                    $mail->SetFrom($CONF_MAIL['from'], $CONF['name_of_firm']);
                    $mail->Subject = 'test message';
                    $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
                     // optional - MsgHTML will create an alternate automatically
                    $mail->MsgHTML('Test message via sendmail');
                    $mail->Send();
                    echo "Message Sent OK<p></p>\n";
                }
                catch(phpmailerException $e) {
                    echo $e->errorMessage();
                     //Pretty error messages from PHPMailer
                    
                }
                catch(Exception $e) {
                    echo $e->getMessage();
                     //Boring error messages from anything else!
                    
                }
            } else if (get_conf_param('mail_type') == "SMTP") {
                
                $mail = new PHPMailer(true);
                 // the true param means it will throw exceptions on errors, which we need to catch
                
                $mail->IsSMTP();
                 // telling the class to use SMTP
                
                try {
                    $mail->SMTPDebug = 2;
                     // enables SMTP debug information (for testing)
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
                    $mail->AddAddress($CONF['mail'], 'admin helpdesk');
                    $mail->SetFrom($CONF_MAIL['from'], $CONF['name_of_firm']);
                    $mail->Subject = 'test message via smtp';
                    $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
                     // optional - MsgHTML will create an alternate automatically
                    $mail->MsgHTML("test message");
                    $mail->Send();
                    echo "Message Sent OK<p></p>\n";
                }
                catch(phpmailerException $e) {
                    echo $e->errorMessage();
                     //Pretty error messages from PHPMailer
                    
                }
                catch(Exception $e) {
                    echo $e->getMessage();
                     //Boring error messages from anything else!
                    
                }
            }
        }
        if ($mode == "add_ticket") {
            $type = ($_POST['type_add']);
            
            $user_init_id = ($_POST['user_init_id']);
            $user_to_id = ($_POST['user_do']);
            $subj = strip_tags(xss_clean(($_POST['subj'])));
            $msg = strip_tags(xss_clean(($_POST['msg'])));
            $status = '0';
            $unit_id = ($_POST['unit_id']);
            $prio = ($_POST['prio']);
            
            $client_fio = strip_tags(xss_clean(($_POST['fio'])));
            $client_tel = strip_tags(xss_clean(($_POST['tel'])));
            $client_login = strip_tags(xss_clean(($_POST['login'])));
            $unit_desc = strip_tags(xss_clean(($_POST['pod'])));
            
            $client_adr = strip_tags(xss_clean(($_POST['adr'])));
            $client_mail = strip_tags(xss_clean(($_POST['mail'])));
            $client_posada = strip_tags(xss_clean(($_POST['posada'])));
            
            $client_id_param = ($_POST['client_id_param']);
            
            if ($client_fio == "пусто") {
                $client_fio = "";
            }
            if ($client_tel == "пусто") {
                $client_tel = "";
            }
            if ($client_login == "пусто") {
                $client_login = "";
            }
            if ($unit_desc == "пусто") {
                $unit_desc = "";
            }
            if ($client_adr == "пусто") {
                $client_adr = "";
            }
            if ($client_mail == "пусто") {
                $client_mail = "";
            }
            if ($client_posada == "пусто") {
                $client_posada = "";
            }
            
            /*
            На этом месте можно дописывать код, для обработки создания заявки.
            Например SMS-информирование, подключать API и тд и тп
            Доступны переменные:
            $user_init_id   ID-пользователя, который создал заявку
            $user_to_id     ID-пользователя, которому назначена заявку
            $subj           Тема заявки
            $msg            Сообщение
            $unit_id        ID-подразделения, на которое назначена заявка
            $prio           Приоритет заявки
            $client_fio     ФИО клиента
            $client_tel     Тел клиента
            $client_login   Логин клиента
            $unit_desc      Подразделение клиента
            $client_adr     Адрес клиента
            $client_mail    Почта клиента
            $client_posada  Должность клиента
            */
            
            if ($type == "add") {
                
                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM users");
                $stmt->execute();
                $max = $stmt->fetch(PDO::FETCH_NUM);
                
                $max_id = $max[0] + 1;
                $hashname = ($_POST['hashname']);
                
                $hn = md5(time());
                
                $stmt = $dbConnection->prepare('insert into users 
             (id, 
             fio, 
             tel, 
             login, 
             unit_desc, 
             adr, 
             email, 
             posada,
             priv,
             is_client,
             uniq_id) 
             VALUES         
             (:max_id, 
             :client_fio, 
             :client_tel, 
             :client_login, 
             :unit_desc, 
             :client_adr,  
             :client_mail, 
             :client_posada,
             :priv,
             :is_client,
             :uniq_id)');
                
                $stmt->execute(array(':max_id' => $max_id, ':client_fio' => $client_fio, ':client_tel' => $client_tel, ':client_login' => $client_login, ':unit_desc' => $unit_desc, ':client_adr' => $client_adr, ':client_mail' => $client_mail, ':client_posada' => $client_posada, ':priv' => '1', ':is_client' => '1', ':uniq_id' => $hn));
                
                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
                $stmt->execute();
                $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);
                
                $max_id_res_ticket = $max_id_ticket[0] + 1;
                
                $stmt = $dbConnection->prepare('INSERT INTO tickets
                                (id, user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio, last_update) VALUES (:max_id_res_ticket, :user_init_id, :user_to_id, :n,:subj, :msg,:max_id,:unit_id, :status, :hashname, :prio, :nz)');
                $stmt->execute(array(':max_id_res_ticket' => $max_id_res_ticket, ':user_init_id' => $user_init_id, ':user_to_id' => $user_to_id, ':subj' => $subj, ':msg' => $msg, ':max_id' => $max_id, ':unit_id' => $unit_id, ':status' => $status, ':hashname' => $hashname, ':prio' => $prio, ':n' => $CONF['now_dt'], ':nz' => $CONF['now_dt']));
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, :n, :unow, :max_id_res_ticket, :user_to_id, :unit_id)');
                
                $stmt->execute(array(':create' => 'create', ':unow' => $unow, ':max_id_res_ticket' => $max_id_res_ticket, ':user_to_id' => $user_to_id, ':unit_id' => $unit_id, ':n' => $CONF['now_dt']));
                
                //if ($CONF_MAIL['active'] == "true") {
                send_notification('ticket_create', $max_id_res_ticket);
                
                //              }
                
                echo ($hashname);
            }
            if ($type == "edit") {
                
                $hashname = ($_POST['hashname']);
                $if_cl = get_user_val_by_id($client_id_param, 'is_client');
                
                if ($if_cl == "1") {
                    
                    $stmt = $dbConnection->prepare('update users set tel=:client_tel, login=:client_login, unit_desc=:unit_desc, adr=:client_adr, email=:client_mail, posada=:client_posada where id=:client_id_param');
                    
                    $stmt->execute(array(':client_tel' => $client_tel, ':client_login' => $client_login, ':unit_desc' => $unit_desc, ':client_adr' => $client_adr, ':client_mail' => $client_mail, ':client_posada' => $client_posada, ':client_id_param' => $client_id_param));
                }
                
                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
                $stmt->execute();
                $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);
                
                $max_id_res_ticket = $max_id_ticket[0] + 1;
                
                $stmt = $dbConnection->prepare('INSERT INTO tickets
                                (id, user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio, last_update) VALUES (:max_id_res_ticket, :user_init_id, :user_to_id, :n,:subj, :msg,:max_id,:unit_id, :status, :hashname, :prio, :nz)');
                $stmt->execute(array(':max_id_res_ticket' => $max_id_res_ticket, ':user_init_id' => $user_init_id, ':user_to_id' => $user_to_id, ':subj' => $subj, ':msg' => $msg, ':max_id' => $client_id_param, ':unit_id' => $unit_id, ':status' => $status, ':hashname' => $hashname, ':prio' => $prio, ':n' => $CONF['now_dt'], ':nz' => $CONF['now_dt']));
                
                $unow = $_SESSION['helpdesk_user_id'];
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, :n, :unow, :max_id_res_ticket, :user_to_id, :unit_id)');
                
                $stmt->execute(array(':create' => 'create', ':unow' => $unow, ':max_id_res_ticket' => $max_id_res_ticket, ':user_to_id' => $user_to_id, ':unit_id' => $unit_id, ':n' => $CONF['now_dt']));
                
                //echo("dd");
                //if ($CONF_MAIL['active'] == "true") {
                send_notification('ticket_create', $max_id_res_ticket);
                
                //                }
                echo ($hashname);
            }
            
            if ($type == "client") {
                
                $hashname = ($_POST['hashname']);
                $user_init_id = $_SESSION['helpdesk_user_id'];
                $stmt = $dbConnection->prepare("SELECT MAX(id) max_id FROM tickets");
                $stmt->execute();
                $max_id_ticket = $stmt->fetch(PDO::FETCH_NUM);
                
                $max_id_res_ticket = $max_id_ticket[0] + 1;
                
                $stmt = $dbConnection->prepare('INSERT INTO tickets
                                (id, user_init_id,user_to_id,date_create,subj,msg, client_id, unit_id, status, hash_name, prio, last_update) VALUES (:max_id_res_ticket, :user_init_id, :user_to_id, :n,:subj, :msg,:max_id,:unit_id, :status, :hashname, :prio, :nz)');
                
                $stmt->execute(array(':max_id_res_ticket' => $max_id_res_ticket, ':user_init_id' => $user_init_id, ':user_to_id' => $user_to_id, ':subj' => $subj, ':msg' => $msg, ':max_id' => $_SESSION['helpdesk_user_id'], ':unit_id' => $unit_id, ':status' => $status, ':hashname' => $hashname, ':prio' => $prio, ':n' => $CONF['now_dt'], ':nz' => $CONF['now_dt']));
                
                $unow = $_SESSION['helpdesk_user_id'];
                
                $stmt = $dbConnection->prepare('INSERT INTO ticket_log (msg, date_op, init_user_id, ticket_id, to_user_id, to_unit_id) values (:create, :n, :unow, :max_id_res_ticket, :user_to_id, :unit_id)');
                
                $stmt->execute(array(':create' => 'create', ':unow' => $unow, ':max_id_res_ticket' => $max_id_res_ticket, ':user_to_id' => $user_to_id, ':unit_id' => $unit_id, ':n' => $CONF['now_dt']));
                
                //??????????????????????????????????????????????????????????????
                
                //echo("dd");
                //if ($CONF_MAIL['active'] == "true") {
                send_notification('ticket_create', $max_id_res_ticket);
                
                //  }
                echo ($hashname);
            }
            
            check_unlinked_file();
        }
    }
}
?>