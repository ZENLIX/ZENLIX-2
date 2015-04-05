<?php
session_start();

include ("../functions.inc.php");
$rkeys = array_keys($_GET);

$CONF['title_header'] = lang('TICKET_name') . " #" . get_ticket_id_by_hash($rkeys[1]) . " - " . $CONF['name_of_firm'];

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    include ("head.inc.php");
    include ("navbar.inc.php");
    
    //echo $rkeys[1];
    //$hn=($_GET['hash']);
    $hn = $rkeys[1];
    $stmt = $dbConnection->prepare('SELECT 
                            * from tickets
                            where hash_name=:hn');
    $stmt->execute(array(':hn' => $hn));
    $res1 = $stmt->fetchAll();
    if (!empty($res1)) {
        foreach ($res1 as $row) {
            
            $lock_by = $row['lock_by'];
            $ok_by = $row['ok_by'];
            $ok_date = $row['ok_date'];
            $cid = $row['client_id'];
            $tid = $row['id'];
            $arch = $row['arch'];
            $subj = $row['subj'];
            $status_ok = $row['status'];
            $ms = $row['msg'];
            $pr = $row['prio'];
            $dcr=$row['date_create'];

            $sla_plan=$row['sla_plan_id'];
            
            if ($arch == 1) {
                $st = "<span class=\"label label-default\"><i class=\"fa fa-archive\"></i> " . lang('TICKET_status_arch') . "</span>";
            }
            if ($arch == 0) {
                if ($status_ok == 1) {
                    $st = "<span class=\"label label-success\"><i class=\"fa fa-check-circle\"></i> " . lang('TICKET_status_ok') . " " . nameshort(name_of_user_ret_nolink($ok_by)) . "</span>";
                }
                if ($status_ok == 0) {
                    if ($lock_by <> 0) {
                        $st = "<span class=\"label label-warning\"><i class=\"fa fa-gavel\"></i> " . lang('TICKET_status_lock') . " " . name_of_user_ret_nolink($lock_by) . "</span>";
                    }
                    if ($lock_by == 0) {
                        $st = "<span class=\"label label-primary\"><i class=\"fa fa-clock-o\"></i> " . lang('TICKET_status_new') . "</span>";
                    }
                }
            }
            
            if ($row['user_to_id'] <> 0) {
                $to_text = "<div class=''>" . name_of_user_ret($row['user_to_id']) . "</div>";
            }
            if ($row['user_to_id'] == 0) {
                $to_text = "<strong>" . lang('t_list_a_all') . "</strong> " . lang('T_from') . " " . view_array(get_unit_name_return($row['unit_id']));
            }
            
            if ($row['is_read'] == "0") {
                
                $res = $dbConnection->prepare("update tickets set is_read=:n where id=:tid");
                $res->execute(array(':n' => '1', ':tid' => $tid));
            }
            
            if ($lock_by <> "0") {
                if ($lock_by == $_SESSION['helpdesk_user_id']) {
                    $status_lock = "me";
                    
                    //$lock_disabled="";
                    $lock_text = "<i class=\"fa fa-unlock\"></i> " . lang('TICKET_action_unlock') . "";
                    $lock_status = "unlock";
                } else {
                    
                    $status_lock = "you";
                    
                    $lock_status = "unlock";
                    $lock_text = "<i class=\"fa fa-unlock\"></i> " . lang('TICKET_action_unlock') . "";
                }
            }
            if ($lock_by == "0") {
                
                $lock_text = "<i class=\"fa fa-lock\"></i> " . lang('TICKET_action_lock') . "";
                $lock_status = "lock";
            }
            
            if ($status_ok == "1") {
                $status_ok_text = lang('TICKET_action_nook');
                $status_ok_status = "ok";
            }
            
            if ($status_ok == "0") {
                $status_ok_text = "<i class=\"fa fa-check\"></i> " . lang('TICKET_action_ok') . "";
                $status_ok_status = "no_ok";
            }
            
            $inituserid_flag = 0;
            if ($row['user_init_id'] == $_SESSION['helpdesk_user_id']) {
                $inituserid_flag = 1;
            }
            
            $prio = "<span class=\"label label-info\"><i class=\"fa fa-minus\"></i> " . lang('t_list_a_p_norm') . "</span>";
            if ($row['prio'] == "1") {
                $prio_style['normal'] = "active";
            } else if ($row['prio'] == "0") {
                $prio = "<span class=\"label label-primary\"><i class=\"fa fa-arrow-down\"></i> " . lang('t_list_a_p_low') . "</span>";
                $prio_style['low'] = "active";
            } else if ($row['prio'] == "2") {
                $prio = "<span class=\"label label-danger\"><i class=\"fa fa-arrow-up\"></i> " . lang('t_list_a_p_high') . "</span>";
                $prio_style['high'] = "active";
            }
?>
            
            
            <section class="content-header">
                    <h1>
                        <i class="fa fa-ticket"></i> <?php echo lang('TICKET_name'); ?> <strong>#<?php echo $row['id'] ?></strong>
                        <small>
                            <?php echo make_html($row['subj'], 'no') ?>
                        </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('TICKET_name'); ?> #<?php echo $row['id'] ?></li>
                    </ol>
                </section>
            
            
            
            
            
            
            
            
            
            <section class="content">
                    <!-- title row -->
                    
                    
                    
                                <div class="row">

            <div class="col-md-8">
            <?php
            if (isset($_GET['refresh'])) { ?>
                <div class="alert alert-info">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="fa fa-refresh"></i> <?php echo lang('TICKET_msg_updated'); ?></div>
            <?php
            }

?>
            </div>
            </div>
                    
                    
                    
<div class="row">
<div class="col-md-8">
    <div class="row">
    <div class="col-md-12">
                    <div class="box">
                                <div class="box-header">
                                <h3 class="box-title">
                                    
                                    
                                <?php echo make_html($row['subj']) ?>
                                </h3>
                                <small class="text-muted">
                                <?=get_ticket_info_source($row['id']);?>
                                </small>
                                <small class="box-tools pull-right text-muted">
                                
                                <i class="fa fa-clock-o"></i>
                                <time id="c" datetime="<?php echo $row['date_create']; ?>"></time> <?=get_deadline_label($row['id']);?></small>
                                
                                </div>
                                <div class="box-body">
                                <table class="table table-bordered">
                <tbody>
                <tr style="width:50%">
                    <td ><small class="text-muted"><?php echo lang('TICKET_t_from'); ?>: </small></td>
                    <td><small><?php echo name_of_user_ret($row['user_init_id']) ?> </small></td>
                    
                    <td><small class="text-muted"><?php echo lang('TICKET_t_prio'); ?>:</small>
                    </td>
                    <td><small><?php echo $prio; ?></small>
                    </td>
                </tr>
                <tr>
                    <td ><small class="text-muted"><?php echo lang('TICKET_t_to'); ?>: </small></td>
                    <td><small><?php echo $to_text; ?> </small></td>
                    <td><small class="text-muted" ><?php echo lang('TICKET_t_status'); ?>:</small>
                    </td>
                    <td><small><?php echo $st; ?></small></td>
                </tr>



                </tbody>
            </table>

<?php
        $stmts = $dbConnection->prepare('SELECT * FROM ticket_data where ticket_hash=:n');
        $stmts->execute(array(':n' => $hn));
        $res11 = $stmts->fetchAll();


if (!empty($res11)) {
?><br>
<small class="text-muted"><?=lang('FIELD_add_title');?>: </small>
<table class="table table-bordered">
                <tbody>
<?php
        foreach ($res11 as $rown) { 

    $stmt2 = $dbConnection->prepare('SELECT name from ticket_fields where id=:tm and status=:s');
    $stmt2->execute(array(
        ':tm' => $rown['field_id'],
        ':s'=>'1'
    ));
    
    $tt = $stmt2->fetch(PDO::FETCH_ASSOC);

    


?>

        <tr>
                    <td style="width:150px"><small class="text-muted"><?php echo $rown['field_name']; ?>: </small></td>
                    <td><small><?php echo $rown['field_val']; ?> </small></td>
                    
                    
                </tr>


<?php

}
?>
 </tbody>
            </table>
<?php
}
?>

            <div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                <?php echo make_html($row['msg']); ?>
                            </div>
                            
                            <div class="row"><div class="col-md-12">
                            <small class="text-muted" >
                                <?=get_ticket_info($row['id']);?>
                            </small>
                                <a href="print_ticket?<?php echo $hn; ?>" class="btn btn-default btn-xs pull-right"><i class="fa fa-print"></i> <?=lang('TICKET_print');?></a>
                            <?php
            if (( ($inituserid_flag == 1) && ($arch == 0)) || (priv_status(id_of_user($_SESSION['helpdesk_user_login'])) == "2") || (priv_status(id_of_user($_SESSION['helpdesk_user_login'])) == "0") ) { ?><button type="button" class="btn btn-default btn-xs pull-right" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil"></i>  <?php echo lang('CONF_act_edit'); ?></button> <?php
            } ?>
                            </div>
                            </div>                            <?php
            
            $stmt = $dbConnection->prepare('SELECT * FROM files where ticket_hash=:tid');
            $stmt->execute(array(':tid' => $hn));
            $res1 = $stmt->fetchAll();
            if (!empty($res1)) {
?>
                    <hr style="margin:0px;">
                        <div class="row" style="padding:10px;">
                        <div class="col-md-3">
                            <center><small><strong><?php echo lang('TICKET_file_list') ?>:</strong></small></center>
                        </div>
                        <div class="col-md-9">
                            <table class="table table-hover">
                                    <tbody>
                                <?php
                foreach ($res1 as $r) { 


$fts = array(
                'image/jpeg',
                'image/gif',
                'image/png'
            );



            if (in_array($r['file_type'], $fts)) {
                
                $ct= ' <a class=\'fancybox\' href=\'' . $CONF['hostname'] . 'upload_files/' . $r['file_hash'] . '.' . $r['file_ext'] . '\'><img style=\'max-height:50px;\' src=\'' . $CONF['hostname'] . 'upload_files/' . $r['file_hash'] . '.' . $r['file_ext'] . '\'></a> ';
                $ic='';
            } else {
                $ct= ' <a href=\'' . $CONF['hostname'] . 'sys/download.php?' . $r['file_hash'] . '\'>' . $r['original_name'] . '</a>';
                $ic=get_file_icon($r['file_hash']);
            }



                    ?>
                                    
                                    
                                    
                    <tr>
                        <td style="width:20px;"><small><?php echo $ic; ?></small></td>
                        <td><small><?=$ct;?></small></td>
                        <td><small><?php
                    echo round(($r['file_size'] / (1024 * 1024)), 2); ?> Mb</small></td>
                    </tr>
<?php
                } ?>
                                    </tbody>
                            </table>

                        </div>
                        
                        
                        
                        
                        
                        
                    </div>


                <?php
            } ?>

                            


                                </div>
                    </div>
                    
                    
                    </div>
    </div>
    
                <?php
            $user_id = id_of_user($_SESSION['helpdesk_user_login']);
            $unit_user = unit_of_user($user_id);
            $ps = priv_status($user_id);
            
            $lo = "no";
            
            /////////если пользователь///////////////////////////////////////////////////////////////////////////////////////////
            if ($ps == 1) {
                
                //ЗАявка не выполнена ИЛИ выполнена мной
                //ЗАявка не заблокирована ИЛИ заблокирована мной
                if ($row['user_init_id'] == $user_id) {
                    
                    $lo = "yes";
                }
                
                if ($row['user_init_id'] <> $user_id) {
                    
                    if (($status_ok == 0) || (($status_ok == 1) && ($ok_by == $user_id))) {
                        
                        if (($lock_by == 0) || ($lock_by == $user_id)) {
                            $lo = "yes";
                        }
                    }
                }
            }
            
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            /////////если нач отдела/////////////////////////////////////////////////////////////////////////////////////////////
            if ($ps == 0) {
                $lo = "yes";
            }
            
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            //////////главный админ//////////////////////////////////////////////////////////////////////////////////////////////
            if ($ps == 2) {
                $lo = "yes";
            }
            
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            if ($lo == "no") {
                $lock_disabled = "disabled=\"disabled\"";
            } else if ($lo == "yes") {
                $lock_disabled = "";
            }


//echo get_ticket_action_priv($row['id']);

?>
    
    <div class="row">
    <div class="col-md-12">
<style>
.info-box {
  display: block;
  min-height: 90px;
  background: #fff;
  width: 100%;
  box-shadow: 0 1px 1px rgba(0,0,0,0.1);
  border-radius: 2px;
  margin-bottom: 15px;
}
.info-box-icon {
  border-top-left-radius: 2px;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 2px;
  display: block;
  float: left;
  height: 90px;
  width: 90px;
  text-align: center;
  font-size: 45px;
  line-height: 90px;
  background: rgba(0,0,0,0.2);
}
.info-box-content {
  padding: 5px 10px;
  margin-left: 90px;
}
.info-box-text {
  text-transform: uppercase;
}
.progress-description, .info-box-text {
  display: block;
  font-size: 14px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.info-box-number {
  display: block;
  font-weight: bold;
  font-size: 17px;
}
.info-box .progress, .info-box .progress .progress-bar {
  border-radius: 0;
}
.info-box .progress {
  background: rgba(0,0,0,0.2);
  margin: 5px -10px 5px -10px;
  height: 2px;
}
.progress-description {
  margin: 0;
}
.progress-description, .info-box-text {
  display: block;
  font-size: 14px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.info-box .progress .progress-bar {
  background: #fff;
}
.info-box .progress, .info-box .progress .progress-bar {
  border-radius: 0;
}
</style>




    </div>







    <div class="col-md-12">
    
    <div class="box box-danger">
                                
                                <div class="box-body">
                                   


                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button <?=get_button_act_status(get_ticket_action_priv($row['id']), 'refer');?> id="action_refer_to" value="0" type="button" class="btn btn btn-danger"><i class="fa fa-share"></i> <?php echo lang('TICKET_t_refer'); ?></button>
                    </div>



                    <div class="btn-group">
                        <button <?=get_button_act_status(get_ticket_action_priv($row['id']), $lock_status);?> id="action_lock" status="<?php echo $lock_status ?>" value="<?php echo $_SESSION['helpdesk_user_id'] ?>" tid="<?php echo $tid ?>" type="button" class="btn btn btn-danger"> <?php echo $lock_text ?></button>
                    </div><div class="btn-group">
                        <button <?=get_button_act_status(get_ticket_action_priv($row['id']), $status_ok_status);?> id="action_ok" status="<?php echo $status_ok_status ?>" value="<?php echo $_SESSION['helpdesk_user_id'] ?>" tid="<?php echo $tid ?>" type="button" class="btn btn btn-danger"><?php echo $status_ok_text ?> </button>
                    </div>
                </div>
                </div><!-- /.box-body -->
                            </div>


            
            <div id="refer_to" class="col-md-12 box box-danger" style="padding-bottom:10px;">

<div class="box-body">















                <div class="form-group" id="t_for_to" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_to_unit_desc'); ?></small>">
                    <label for="t_to" class="col-sm-3 control-label"><small><?php echo lang('TICKET_t_refer_to'); ?>: </small></label>
                    <div class="col-sm-5" style="">
                        <select <?php echo $lock_disabled ?> data-placeholder="<?php echo lang('NEW_to_unit'); ?>" class="chosen-select form-control input-sm" id="t_to" name="unit_id">
                            <option value="0"></option>
                            <?php
            
            $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
            $stmt->execute(array(':n' => '0', ':s' => '1'));
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
                
                //echo($row['label']);
                $row['label'] = $row['label'];
                $row['value'] = (int)$row['value'];
?>

                                <option value="<?php echo $row['value'] ?>"><?php echo $row['label'] ?></option>

                            <?php
            }
?>
                        </select>
                    </div>



                    <div class="col-sm-3" style="">


    <select data-placeholder="<?php echo lang('NEW_to_user'); ?>"  id="t_users_do" name="unit_id" class="form-control input-sm" multiple>
        <option></option>


<?php
            
            /* $qstring = "SELECT fio as label, id as value FROM users where status='1' and login !='system' order by fio ASC;";
                $result = mysql_query($qstring);//query the database for entries containing the term
            while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
            */
            
            $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and id !=:system and is_client=0 order by fio ASC');
            $stmt->execute(array(':n' => '1', ':system' => '1'));
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
                
                //echo($row['label']);
                $row['label'] = $row['label'];
                $row['value'] = (int)$row['value'];
                
                if (get_user_status_text($row['value']) == "online") {
                    $s = "online";
                } else if (get_user_status_text($row['value']) == "offline") {
                    $s = "offline";
                }
?>
                    <option data-foo="<?php echo $s; ?>" value="<?php echo $row['value'] ?>"><?php echo nameshort($row['label']) ?> </option>

                <?php
            }
?>
    </select>
    
    

                        <!--select <?php echo $lock_disabled ?> data-placeholder="<?php echo lang('NEW_to_user'); ?>" class="chosen-select form-control input-sm" id="t_users_do" name="unit_id">
                            <option value="0"></option>
                            <?php
            
            $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and login !=:system order by fio ASC');
            $stmt->execute(array(':n' => '1', ':system' => 'system'));
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
                
                //echo($row['label']);
                $row['label'] = $row['label'];
                $row['value'] = (int)$row['value'];
?>

                                <option value="<?php echo $row['value'] ?>"><?php echo nameshort($row['label']) ?></option>

                            <?php
            }
?>

                        </select-->
                        
                        
                        
                        
                        
                        
                        <p class="help-block"><small style="padding-left:30px;"><?php echo lang('TICKET_t_opt'); ?></small></p>

                    </div>
                    
                    
                    
                    
                    
                    
                    
                    
                    <div class="col-sm-1" style="">
                        <button id="ref_ticket" value="<?php echo $tid ?>" type="button" class="btn btn-default btn-sm" <?php echo $lock_disabled ?>><i class="fa fa-check"></i></button>
                    </div>
                    <div class="col-md-12" style="">
                        <textarea placeholder="<?php echo lang('NEW_MSG_ph_1'); ?>" class="form-control input-sm animated" name="msg1" id="msg1" rows="3"></textarea>
                    </div>

                </div>
</div>



            
                                
            
            
            
            
            
    </div>




    </div>


    



    </div>
    
    <div class="row">
    <div class="col-md-12">
    
<div id="msg"></div>
<div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tab_1" data-toggle="tab"><i class="fa fa-comments-o"></i> <?php echo lang('TICKET_t_comment'); ?></a></li>
                                    <li class=""><a href="#tab_2" data-toggle="tab"><?php echo lang('TICKET_t_history'); ?></a></li>
                                    
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_1">
                                        <div class="box box-solid" >
                                <div class="box-header">
                                    
                                    
                                </div>
                                <div class="box-body chat" id="comment_content">
                                <?php echo view_comment($tid); ?>
                                    <!-- chat item -->
                                    
                                </div><!-- /.chat -->
                                <div class="box-footer">
                                    <div class="" id="for_msg">
                                        


<textarea id="msg" name="msg" class="form-control" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="top" data-content="&lt;small&gt;<?php echo lang('TICKET_t_det_ticket'); ?>&lt;/small&gt;" placeholder="<?php echo lang('TICKET_t_comm_ph'); ?>"></textarea>
</div>
<div class="">
<div style="height: 30px;" class="">

                                        <div class="btn-group pull-right">
                                            <button value="<?php echo $hn ?>" id="do_comment" class="btn btn-success btn-sm"><i class="fa fa-comment"></i></button>
                                            
  <input type="file" id="do_comment_file" value="<?php echo $hn ?>" class="file-inputs" title="+">
                                            
                                            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                    </div><!-- /.tab-pane -->
                                    <div class="tab-pane" id="tab_2">
                                        <?php echo view_log($tid); ?>
                                    </div><!-- /.tab-pane -->
                                </div><!-- /.tab-content -->
                            </div>





    

    </div></div>
    
    
    
</div>
<div class="col-md-4">

<div class="row">
    
    <div class="col-md-12">
    <?php echo get_client_info_ticket($cid) ?>
    </div>
    
</div>

<?php
if (get_conf_param('sla_system') == "true") {


if ($sla_plan != "0") {


    $stmt_sla = $dbConnection->prepare('SELECT * from sla_plans where id=:uid');
    $stmt_sla->execute(array(':uid' => $sla_plan));
    $row_sla = $stmt_sla->fetch(PDO::FETCH_ASSOC);

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

if (get_ticket_time_reaction_sec($tid) == 0) {
$per=floor((get_ticket_time_reaction_sec_no_lock($tid)*100)/$sla_react);
}
else if (get_ticket_time_reaction_sec($tid) != 0) {
$per=floor((get_ticket_time_reaction_sec($tid)*100)/$sla_react);
}
if ($per > 100) { $per=100;}



if (get_ticket_time_lock_sec($tid) == 0) {
$perw=0;
}
else if (get_ticket_time_lock_sec($tid) != 0) {
    $perw=floor((get_ticket_time_lock_sec($tid)*100)/$sla_work);
}
if ($perw > 100) { $perw=100;}





if ($sla_react == "0") {
$sla_react=lang('SLA_not_sel');

}
else if ($sla_react != "0") {

    $sla_react="<time id=\"f\" datetime=\"".$sla_react."\"></time>";
}



if ($sla_work == "0") {
$sla_work=lang('SLA_not_sel');

}




else if ($sla_work != "0") {

    $sla_work="<time id=\"f\" datetime=\"".$sla_work."\"></time>";
}




if ($sla_deadline == "0") {
    $left_secr=lang('SLA_not_sel');
    $ls="false";
}



else if ($sla_deadline != "0") {
    

//$left_sec=(strtotime($dcr)+$sla_deadline)-time();


if ($status_ok == "0") {


$left_sec=(strtotime($dcr)+$sla_deadline)-time();
if ($left_sec < 0) {
    $left_secr=lang('SLA_time_old');
    $ls="false";
}
if ($left_sec >= 0) {
    $left_secr=lang('SLA_deadline_t').": "."<time id=\"f\" datetime=\"".$left_sec."\"></time>";
    $ls="true";
}


$perd=floor(((time()-strtotime($dcr))*100)/$sla_deadline);


}






if ($status_ok == "1") {

    $stmt_dl = $dbConnection->prepare('SELECT date_op from ticket_log where ticket_id=:tid and msg=:m order by date_op DESC limit 1');
    $stmt_dl->execute(array(
        ':tid' => $tid,
        ':m'=>'ok'
    ));
    $tts_dl = $stmt_dl->fetch(PDO::FETCH_ASSOC);


$left_sec=(strtotime($dcr)+$sla_deadline)-strtotime($tts_dl['date_op']);




$ok_by_time=(strtotime($tts_dl['date_op'])-strtotime($dcr));




$perd=floor(((strtotime($tts_dl['date_op'])-strtotime($dcr))*100)/$sla_deadline);





if ($left_sec < 0) {
    $left_secr=lang('SLA_time_old');
}


if ($left_sec > 0) {
 $left_secr=lang('SLA_deadline_ok_by')." "."<time id=\"f\" datetime=\"".$ok_by_time."\"></time>";
}
$ls="false";
    }
/*
Если выполнено

время выполнения - время создания = время всей заявки

время создания + время деадлайн = допустимое время заявки

если время всей заявки > допустимое время заявки то просрочено
иначе: то: время все заявки показать

---------------

если не выполнена
*/
//$left_sec=(strtotime($dcr)+$sla_deadline)-time();




}

if ($perd > 100) { $perd=100;}
    ?>
<div class="row">
    
    <div class="col-md-12">
<div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="fa fa-bolt"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?=lang('SLA_perf_reaction');?></span>
                  <span class="info-box-number" style="white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;">
<?=get_ticket_time_reaction($tid);?>
                  </span>
                  <div class="progress">
                    <div class="progress-bar" style="width: <?=$per;?>%"></div>
                  </div>
                  <span class="progress-description">
                    <?=lang('SLA_REGLAMENT');?>: <?=$sla_react;?>
                  </span>
                </div><!-- /.info-box-content -->
              </div>


<div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="fa fa-lock"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?=lang('SLA_perf_work_a');?></span>
<?php

if ($status_ok == 1) {
    $sl="false";
}
if ($status_ok == 0) {
if ($lock_status == "lock") {
    $sl="false";
}
else if ($lock_status == "unlock") {
    $sl="true";
}
}
    ?>
                  <span class="info-box-number" style="white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;" id="work_timer" value="<?=$sl;?>"><?=get_ticket_time_lock($tid);?></span>
                  <div class="progress">
                    <div class="progress-bar" style="width: <?=$perw;?>%"></div>
                  </div>
                  <span class="progress-description">
                    <?=lang('SLA_REGLAMENT');?>: <?=$sla_work;?>
                  </span>
                </div><!-- /.info-box-content -->
              </div>




<div class="info-box bg-orange " style="background-color: #D81B60 !important;">
                <span class="info-box-icon"><i class="fa fa-check-square"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"><?=lang('SLA_perf_deadline_short');?></span>
                  <span class="info-box-number" style="white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;" id="deadline_timer" value="<?=$ls;?>"><?=$left_secr;?>
                  </span>
                  <div class="progress">
                    <div class="progress-bar" style="width: <?=$perd;?>%"></div>
                  </div>
                  <span class="progress-description">
                    <?=lang('SLA_REGLAMENT');?>: <time id="f" datetime="<?=$sla_deadline;?>"></time>
                  </span>
                </div><!-- /.info-box-content -->
              </div>




</div>
</div>

<?php
}
}
if (validate_admin($_SESSION['helpdesk_user_id'])) { ?>

<div class="row">
    
    <div class="col-md-12">
<div class="box box-danger" >
<div class="box-body">


<button id="del_ticket" type="button" class="btn bg-maroon btn-flat btn-block pops2" data-content="<small><?=lang('TICKET_action_delete_info');?></small>" ><i class="fa fa-trash"></i> <?=lang('TICKET_action_delete');?></button>
</div>
</div>
    </div>
</div>

<?php 

}
?>






<div class="row">
    
    <div class="col-md-12">











    <?php 
            
            if ($arch == 1) {
?>
                
<div class="callout callout-danger">
                                        <h4><?php echo lang('MAIN_attention'); ?></h4>
                                        <p><?php echo lang('TICKET_t_in_arch'); ?></p>
                                    </div>
                            
                            
                            
                
                
                
                
                

            <?php
            }
            if ($arch == 0) {
                if ($status_ok == 1) {
?>
<div class="callout callout-warning">
                                        <h4><?php echo lang('MAIN_attention'); ?></h4>
                                        <p><i class="fa fa-check-circle"></i> <?php echo lang('TICKET_t_ok'); ?> <strong> <?php echo name_of_user_ret($ok_by) ?></strong> <?php echo $ok_date; ?>.<br> <?php echo lang('TICKET_t_ok_1'); ?></p>
                                    </div>



                <?php
                }
                if ($status_ok == 0) {
                    if ($lock_by <> 0) {
                        if ($status_lock == "you") {
?>
                            
                            <div class="callout callout-warning">
                                        <h4><?php echo lang('MAIN_attention'); ?></h4>
                                        <p><i class="fa fa-check-circle"></i> <?php echo lang('TICKET_t_lock'); ?> <strong> <?php echo name_of_user_ret($lock_by) ?></strong> .<br> <?php echo lang('TICKET_t_lock_1'); ?></p>
                                    </div>
                                    
                                    
                                    
                            
                            
                            
                           
                        <?php
                        }
                        if ($status_lock == "me") {
?>
                            
                            
                            
                            <div class="callout callout-warning">
                                        <h4><?php echo lang('MAIN_attention'); ?></h4>
                                        <p><i class="fa fa-check-circle"></i> <?php echo lang('TICKET_t_lock_i'); ?></p>
                                    </div>
                                    
                                    
                            
                            
                            
                        <?php
                        }
                    }
                }
            }
?>
    </div>
    
</div>

</div>

</div>












                                        
                    

                </section>
            
            
            
            
                <input type="hidden" id="prio" value="<?php echo $pr; ?>">

            <input type="hidden" id="ticket_hash" value="<?php echo $hn; ?>">
            <input type="hidden" id="ticket_id" value="<?php echo $tid; ?>">
            <input type="hidden" id="ticket_total" value="0">
            <div class="container">











<link rel="stylesheet" href="<?php echo $CONF['hostname'] ?>/css/ticket_style.css">


<?php
            if (($inituserid_flag == 1) && ($arch == 0) || (priv_status(id_of_user($_SESSION['helpdesk_user_login'])) == "2") || (priv_status(id_of_user($_SESSION['helpdesk_user_login'])) == "0")) { ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title"><?php echo lang('P_title'); ?></h4>
      </div>
      <div class="modal-body">
        
        
       <form class="form-horizontal" role="form">
       



<div class="control-group" id="for_prio">
    <div class="controls">
        <div class="form-group">
            <label for="" class="col-sm-2 control-label"><small><?php echo lang('NEW_prio'); ?>: </small></label>
            <div class="col-sm-10" style=" padding-top: 5px; ">

                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-xs <?php echo $prio_style['low']; ?>" id="prio_low"><i id="lprio_low" class=""></i><?php echo lang('NEW_prio_low'); ?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info btn-xs <?php echo $prio_style['normal']; ?>" id="prio_normal"><i id="lprio_norm" class=""></i> <?php echo lang('NEW_prio_norm'); ?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-xs <?php echo $prio_style['high']; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo lang('NEW_prio_high_desc'); ?>" id="prio_high"><i id="lprio_high" class=""></i><?php echo lang('NEW_prio_high'); ?></button>
                    </div>
                </div>
            </div></div></div></div>
            
            




<?php
                if ($CONF['fix_subj'] == "false") {
?>

<div class="control-group" id="for_s">
        <div class="controls">
          <div class="form-group">
    <label for="subj" class="col-sm-2 control-label"><small><?php echo lang('NEW_subj'); ?>: </small></label>
    <div class="col-sm-10">
      <input type="text" class="form-control input-sm" name="subj" id="subj" placeholder="<?php echo lang('NEW_subj'); ?>" value="<?php echo $subj; ?>">
    </div>
  </div></div></div>
<?php
                } else if ($CONF['fix_subj'] == "true") {
?>



<div class="control-group" id="for_subj" >
    <div class="controls">
        <div class="form-group">
            <label for="subj" class="col-sm-2 control-label"><small><?php echo lang('NEW_subj'); ?>: </small></label>
            <div class="col-sm-10" style="">
                <select data-placeholder="<?php echo lang('NEW_subj_det'); ?>" class="form-control input-sm" id="subj" name="subj">
                    <option value="0"></option>
                    <?php
                    
                    $stmts = $dbConnection->prepare('SELECT name FROM subj order by name COLLATE utf8_unicode_ci ASC');
                    $stmts->execute();
                    $res11 = $stmts->fetchAll();
                    foreach ($res11 as $rows) {
                        $sel_flag = "";
                        if ($rows['name'] == $subj) {
                            $sel_flag = "selected";
                        }
?>

                        <option <?php echo $sel_flag; ?> value="<?php echo $rows['name'] ?>"><?php echo $rows['name'] ?></option>

                    <?php
                    }
?>

                </select>
            </div>
        </div>

    </div>
</div>


<?php
                } ?>







  <div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_msg">
            <label for="msg" class="col-sm-2 control-label"><small><?php echo lang('NEW_MSG'); ?>:</small></label>
            <div class="col-sm-10">
                <textarea data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_MSG_msg'); ?></small>" placeholder="<?php echo lang('NEW_MSG_ph'); ?>" class="form-control input-sm animated" name="msg" id="msg_up" rows="3" required="" data-validation-required-message="<?php echo lang('EXT_fill_msg'); ?>" aria-invalid="false"><?php echo $ms; ?></textarea>
            </div>
        </div>
        <div class="help-block"></div></div></div>
       
       
       
       
       
       
       </form> 
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('TICKET_file_notupload_one'); ?></button>
        <button type="button" id="save_edit_ticket" class="btn btn-primary"><?php echo lang('JS_save'); ?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
            } ?>

           

            




            










            

            </div>
        <?php
        }
    } else {
?>
        <div class="well well-large well-transparent lead">
            <center><?php echo lang('TICKET_t_no'); ?></center>
        </div>
    <?php
    }
?>

    <?php
    include ("footer.inc.php");
} else {
    include 'auth.php';
}
?>
