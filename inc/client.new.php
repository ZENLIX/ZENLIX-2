<?php
session_start();
include_once ("../functions.inc.php");
$CONF['title_header'] = lang('NEW_title') . " - " . $CONF['name_of_firm'];
if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("client.navbar.inc.php");
        
        //check_unlinked_file();
        
         


class new_ticket_form
        {
            
            public function get_to_unit_list() {
                global $dbConnection;
                
                $res = array();
                $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
                $stmt->execute(array(
                    ':n' => '0',
                    ':s' => '1'
                ));
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                    
                    //echo($row['label']);
                    $row['label'] = $row['label'];
                    $row['value'] = (int)$row['value'];
                    
                    $s1 = "";
                    if (get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_unit_id') == $row['value']) {
                        $s1 = "selected";
                    }
                    
                    array_push($res, array(
                        'label' => $row['label'],
                        'value' => $row['value'],
                        'sel' => $s1
                    ));
                }
                
                return $res;
            }
            
            public function get_to_user_list() {
                
                global $dbConnection;
                
                $res = array();
                
                $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and id !=:system and is_client=0 order by fio ASC');
                $stmt->execute(array(
                    ':n' => '1',
                    ':system' => '1'
                ));
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                    
                    //echo($row['label']);
                    $row['label'] = $row['label'];
                    $row['value'] = (int)$row['value'];
                    
                    $st_sel = "";
                    $mass = explode(",", get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_user_id'));
                    if (in_array($row['value'], $mass)) {
                        $st_sel = "selected";
                    }
                    
                    if (get_user_status_text($row['value']) == "online") {
                        $s = "online";
                    } 
                    else if (get_user_status_text($row['value']) == "offline") {
                        $s = "offline";
                    }
                    
                    array_push($res, array(
                        
                        'label' => nameshort($row['label']) ,
                        'value' => $row['value'],
                        'st_sel' => $st_sel,
                        'df' => $s
                    ));
                }
                return $res;
            }
            
            public function get_subj_list() {
                global $dbConnection;
                $res = array();
                $stmt = $dbConnection->prepare('SELECT name FROM subj order by sort_id ASC');
                $stmt->execute();
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                    array_push($res, array(
                        'name' => $row['name']
                    ));
                }
                return $res;
            }
            
            public function get_fields_forms() {
                global $dbConnection;
                $res = array();
                $stmt = $dbConnection->prepare('SELECT * FROM ticket_fields where status=:n');
                $stmt->execute(array(
                    ':n' => '1'
                ));
                $res1 = $stmt->fetchAll();
                foreach ($res1 as $row) {
                    
                    if ($row['t_type'] == "text") {
                        $v = $row['value'];
                        if ($row['value'] == "0") {
                            $v = "";
                        }
                        $vr = $v;
                    } 
                    else if ($row['t_type'] == "textarea") {
                        $v = $row['value'];
                        if ($row['value'] == "0") {
                            $v = "";
                        }
                        $vr = $v;
                    } 
                    else if ($row['t_type'] == "select") {
                        $vr = array();
                        $v = $row['value'];
                        if ($row['value'] == "0") {
                            $v = "";
                        }
                        $v = explode(",", $row['value']);
                        foreach ($v as $value) {
                            array_push($vr, $value);
                        }
                    } 
                    else if ($row['t_type'] == "multiselect") {
                        $vr = array();
                        $v = $row['value'];
                        if ($row['value'] == "0") {
                            $v = "";
                        }
                        $v = explode(",", $row['value']);
                        foreach ($v as $value) {
                            array_push($vr, $value);
                        }
                    }
                    
                    array_push($res, array(
                        'name' => $row['name'],
                        'hash' => $row['hash'],
                        't_type' => $row['t_type'],
                        'value' => $vr,
                        'placeholder' => $row['placeholder']
                    ));
                }
                return $res;
            }
        }
        
        $new_ticket_form = new new_ticket_form();
        
        $to_unit_list = $new_ticket_form->get_to_unit_list();
        $to_user_list = $new_ticket_form->get_to_user_list();
        $subj_list = $new_ticket_form->get_subj_list();
        
        $fields_forms = $new_ticket_form->get_fields_forms();
        
        $ok_msg = false;
        if (isset($_GET['ok'])) {
            if (isset($_GET['h'])) {
                $h = $_GET['h'];
                $ok_msg = true;
            }
        }
        
        if ($CONF['fix_subj'] == "true") {
            $mut = "";
        }
        if ($CONF['fix_subj'] == "true_multiple") {
            $mut = "multiple";
        }
        
        ob_start();
        
        //Start output buffer
        get_sla_view_select_box();
        $get_sla_view_select_box = ob_get_contents();
        
        //Grab output
        ob_end_clean();
        
        try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem('inc/views');
            
            // инициализируем Twig
            $twig = new Twig_Environment($loader);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('client.new.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'NEW_title' => lang('NEW_title') ,
                'hostname' => $CONF['hostname'],
                'name_of_firm' => $CONF['name_of_firm'],
                'ok_msg' => $ok_msg,
                'h' => $h,
                'NEW_ok' => lang('NEW_ok') ,
                'NEW_ok_1' => lang('NEW_ok_1') ,
                'NEW_ok_2' => lang('NEW_ok_2') ,
                'NEW_ok_3' => lang('NEW_ok_3') ,
                'NEW_ok_4' => lang('NEW_ok_4') ,
                'NEW_from' => lang('NEW_from') ,
                'NEW_from_desc' => lang('NEW_from_desc') ,
                'NEW_fio' => lang('NEW_fio') ,
                'NEW_fio_desc' => lang('NEW_fio_desc') ,
                'uniq_id' => get_user_val('uniq_id') ,
                'CREATE_TICKET_ME' => lang('CREATE_TICKET_ME') ,
                'NEW_to_desc' => lang('NEW_to_desc') ,
                'def_unit_id' => get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_unit_id') ,
                'NEW_to' => lang('NEW_to') ,
                'NEW_to_unit' => lang('NEW_to_unit') ,
                'to_unit_list' => $to_unit_list,
                'NEW_to_user' => lang('NEW_to_user') ,
                'to_user_list' => $to_user_list,
                'NEW_prio' => lang('NEW_prio') ,
                'NEW_prio_low' => lang('NEW_prio_low') ,
                'NEW_prio_norm' => lang('NEW_prio_low') ,
                'NEW_prio_high' => lang('NEW_prio_high') ,
                'NEW_prio_high_desc' => lang('NEW_prio_high_desc') ,
                'sla_system' => get_conf_param('sla_system') ,
                'fix_subj' => $CONF['fix_subj'],
                'mut' => $mut,
                'NEW_subj' => lang('NEW_subj') ,
                'NEW_subj_msg' => lang('NEW_subj_msg') ,
                'subj_list' => $subj_list,
                'get_sla_view_select_box' => $get_sla_view_select_box,
                'NEW_MSG' => lang('NEW_MSG') ,
                'NEW_MSG_msg' => lang('NEW_MSG_msg') ,
                'NEW_MSG_ph' => lang('NEW_MSG_ph') ,
                'EXT_fill_msg' => lang('EXT_fill_msg') ,
                'ticket_last_time' => lang('ticket_last_time') ,
                'TICKET_deadline_text' => lang('TICKET_deadline_text') ,
                'date_dl' => date("Y-m-d H:i:s") ,
                'file_uploads' => $CONF['file_uploads'],
                'TICKET_file_add' => lang('TICKET_file_add') ,
                'PORTAL_fileplace' => lang('PORTAL_fileplace') ,
                'add_fields_forms' => $fields_forms,
                'NEW_button_create' => lang('NEW_button_create') ,
                'NEW_button_reset' => lang('NEW_button_reset') ,
                'hashname' => md5(time()) ,
                'user_init_id' => $_SESSION['helpdesk_user_id'],
                'user_name_login' => get_user_val('login') ,
                'ftypes' => $CONF['file_types'],
                'file_size' => $CONF['file_size']
            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }































/*
?>

                <section class="content-header">
                    <h1>
                        <i class="fa fa-tag"></i> <?php echo lang('NEW_title'); ?>
                        
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $CONF['hostname'] ?>"><span class="icon-svg"></span>  <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('NEW_title'); ?></li>
                    </ol>
                </section>
                
                
                <section class="content">

<div class="row">
            <div class="col-md-8 col-md-offset-2">   

<div class="box box-solid">
                                
                                <div class="box-body">
                                
                                
                                
                                
                                
                                <div class="" id="div_new">
<?php
        if (isset($_GET['ok'])) {
            if (isset($_GET['h'])) {
                $h = $_GET['h'];
            }
?>
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong><i class="fa fa-check"></i> <?php echo lang('NEW_ok'); ?></strong> <?php echo lang('NEW_ok_1'); ?> <a class="" href="<?php echo $CONF['hostname'] ?>ticket&<?php echo $h; ?>"><?php echo lang('NEW_ok_2'); ?></a> <?php echo lang('NEW_ok_3'); ?>
        <a class="" href="<?php echo $CONF['hostname'] ?>print_ticket&<?php echo $h; ?>"target="_blank"> <?php echo lang('NEW_ok_4'); ?></a>.
    </div>
<?php
        }
?>
<div class="" style="padding:20px;">
<div class="panel-body">

<div class="form-horizontal" id="main_form" novalidate="" action="" method="post">


<div class="control-group">



<?php
if (get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_unit_id') == "0") {
?>

    <div class="form-group" id="for_to" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right">
        <label for="to" class="col-md-2 control-label" data-toggle="tooltip" data-placement="top" title="<?php echo lang('NEW_to_desc'); ?>"><small><?php echo lang('NEW_to'); ?>: </small></label>
        <div class="col-md-6">
            <select data-placeholder="<?php echo lang('NEW_to_unit'); ?>" class="chosen-select form-control" id="to" name="unit_id">
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




        <div class="col-md-4" style="" id="dsd" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_to_unit_desc'); ?></small>">

<select data-placeholder="<?php echo lang('NEW_to_user'); ?>"  id="users_do" name="unit_id" multiple>
        <option></option>
                <?php
        

        
        $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and is_client=0 and id !=:system order by fio ASC');
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


        </div>

    </div>

<?php }



else if (get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_unit_id') != "0") {
?>

    <div class="form-group" id="for_to" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right">
        <label for="to" class="col-md-2 control-label" data-toggle="tooltip" data-placement="top" title="<?php echo lang('NEW_to_desc'); ?>"><small><?php echo lang('NEW_to'); ?>: </small></label>
        <div class="col-md-6">
            <select data-placeholder="<?php echo lang('NEW_to_unit'); ?>" class="chosen-select form-control" id="to" name="unit_id" disabled>
                <option value="0"></option>
                <?php
        

        
        $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
        $stmt->execute(array(':n' => '0', ':s' => '1'));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {
            
            //echo($row['label']);
            $row['label'] = $row['label'];
            $row['value'] = (int)$row['value'];


$s1="";
if (get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_unit_id') == $row['value']) {
  $s1="selected";
}


?>

                            <option value="<?php echo $row['value'] ?>" <?=$s1;?>><?php echo $row['label'] ?></option>

                        <?php
        }
?>

            </select>
        </div>




        <div class="col-md-4" style="" id="dsd" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_to_unit_desc'); ?></small>">

<select data-placeholder="<?php echo lang('NEW_to_user'); ?>"  id="users_do" name="unit_id" disabled multiple>
        <option></option>
                <?php
        

        
        $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and is_client=0 and id !=:system order by fio ASC');
        $stmt->execute(array(':n' => '1', ':system' => '1'));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {
            


$st_sel="";
$mass=explode(",", get_user_val_by_id($_SESSION['helpdesk_user_id'], 'def_user_id'));
if (in_array($row['value'], $mass)) {$st_sel="selected";}

            //echo($row['label']);
            $row['label'] = $row['label'];
            $row['value'] = (int)$row['value'];
            if (get_user_status_text($row['value']) == "online") {
                $s = "online";
            } else if (get_user_status_text($row['value']) == "offline") {
                $s = "offline";
            }
?>

<option data-foo="<?php echo $s; ?>" value="<?php echo $row['value'] ?>" <?=$st_sel;?>><?php echo nameshort($row['label']) ?> </option>

                <?php
        }
?>

            </select>


        </div>

    </div>
<?php } ?>
</div>




<div class="control-group" id="for_prio">
    <div class="controls">
        <div class="form-group">
            <label for="" class="col-sm-2 control-label"><small><?php echo lang('NEW_prio'); ?>: </small></label>
            <div class="col-sm-10" style=" padding-top: 5px; ">

                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-xs" id="prio_low"><i id="lprio_low" class=""></i><?php echo lang('NEW_prio_low'); ?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info btn-xs active" id="prio_normal"><i id="lprio_norm" class="fa fa-check"></i> <?php echo lang('NEW_prio_norm'); ?></button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="<?php echo lang('NEW_prio_high_desc'); ?>" id="prio_high"><i id="lprio_high" class=""></i><?php echo lang('NEW_prio_high'); ?></button>
                    </div>
                </div>
            </div></div></div></div>
<?php
        

if (get_conf_param('sla_system') == "true") { ?>

<div class="control-group " >
    <div class="controls">
        <div class="form-group " id="for_subj" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_subj_msg'); ?></small>">
            <label for="subj" class="col-sm-2 control-label"><small><?php echo lang('NEW_subj'); ?>: </small></label>
            <div class="col-sm-10 " style="">
                <select data-placeholder="<?php echo lang('NEW_subj_det'); ?>" class="chosen-select form-control input-sm " id="subj" name="subj">
               
                    <option value="0"></option>
                    <?php
            echo get_sla_view_select_box();

?>



                </select>
            </div>
        </div>

    </div>
</div>


<?php

}

else if (get_conf_param('sla_system') == "false") {
        
        if ($CONF['fix_subj'] == "false") {
?>

<div class="control-group" id="for_subj">
        <div class="controls">
          <div class="form-group">
    <label for="subj" class="col-sm-2 control-label"><small><?php echo lang('NEW_subj'); ?>: </small></label>
    <div class="col-sm-10">
      <input type="text" class="form-control input-sm" name="subj" id="subj" placeholder="<?php echo lang('NEW_subj'); ?>" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_subj_msg'); ?></small>">
    </div>
  </div></div></div>
<?php
        } else if (($CONF['fix_subj'] == "true") || ($CONF['fix_subj'] == "true_multiple")) {
            $mut="";
            if ($CONF['fix_subj'] == "true_multiple") {
                $mut="multiple";
            }
?>



<div class="control-group" id="for_subj" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_subj_msg'); ?></small>">
    <div class="controls">
        <div class="form-group">
            <label for="subj" class="col-sm-2 control-label"><small><?php echo lang('NEW_subj'); ?>: </small></label>
            <div class="col-sm-10" style="">
                <select data-placeholder="<?php echo lang('NEW_subj_det'); ?>" class="chosen-select form-control input-sm" id="subj" name="subj" <?=$mut;?>>
                    <option value="0"></option>
                    <?php
            

            
            $stmt = $dbConnection->prepare('SELECT name FROM subj order by sort_id ASC');
            $stmt->execute();
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
?>

                        <option value="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></option>

                    <?php
            }
?>

                </select>
            </div>
        </div>

    </div>
</div>


<?php
        } 
        }
        ?>







<div class="control-group">
    <div class="controls">
        <div class="form-group" id="for_msg">
            <label for="msg" class="col-sm-2 control-label"><small><?php echo lang('NEW_MSG'); ?>:</small></label>
            <div class="col-sm-10">
                <textarea data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right" data-content="<small><?php echo lang('NEW_MSG_msg'); ?></small>" placeholder="<?php echo lang('NEW_MSG_ph'); ?>" class="form-control input-sm animated" name="msg" id="msg" rows="3" required="" data-validation-required-message="<?php echo lang('EXT_fill_msg'); ?>" aria-invalid="false"></textarea>
            </div>
        </div>
        <div class="help-block"></div></div></div>
<!--######### INPUT FOR DATE-FINISH ############## -->

    <?php
        if (get_conf_param('ticket_last_time') == "true") { ?>

            
                      <div class="control-group" id="for_prio">
    <div class="controls">
        <div class="form-group">
            <label for="d_finish" class="col-sm-2 control-label"><small><?=lang('TICKET_deadline_text');?>: </small></label>

            <div class="col-sm-10" style=" padding-top: 5px; ">

<div class='input-group date' id='date_finish'>

                    <input id="d_finish" type='text' class="form-control input-sm" data-date-format="YYYY-MM-DD HH:mm:ss" value="<?php echo date("Y-m-d H:i:s"); ?>" />
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                
            </div>
            
        </div>
    </div>
    
    </div>  
            
            <?php }?>
     
            
            <!--######### INPUT FOR DATE-FINISH ############## -->

<?php
        if ($CONF['file_uploads'] == "true") { ?>


<div class="control-group">
    <div class="controls">
    <div class="form-group">
    
    <label for="" class="col-sm-2 control-label"><small><?php echo lang('TICKET_file_add'); ?>:</small></label>

    <div class="col-sm-10">
<div class="text-muted well well-sm no-shadow" id="myid" >
  <div class="dz-message" data-dz-message>
<center class="text-muted"><?=lang('PORTAL_fileplace');?></center>
  </div>

<style type="text/css">
  .note-editor .note-dropzone { opacity: 0 !important; }
</style>

<form action="upload.php" class=""></form>

<div class="table table-striped" class="files" id="previews">
 
  <div id="template" class="file-row">
    <!-- This is used as the file preview template -->



<table class="table" style="margin-bottom: 0px;">
                  <tbody><tr>
                    <td style="width:50%"><small><p class="name" data-dz-name></p> </small></td>
                    <td><small class="text-muted"><p class="size" data-dz-size></p></small></td>
                    <td style="width:30%"><div class="progress progress-striped progress-sm" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
          <div class="progress-bar progress-bar-success progress-sm" style="width:0%;" data-dz-uploadprogress></div>
        </div></td>
                    <td class="pull-right"><button data-dz-remove class="btn btn-xs btn-danger delete">
        <i class="glyphicon glyphicon-trash"></i>
        <span>Delete</span>
      </button></td>
                  </tr>

                </tbody></table>

</div>
  </div>
 
</div>

</div></div></div></div>





<?php
        } ?>




<!--######### ADDITIONAL FIELDS ############## -->

<form id="add_field_form">
    <div >
<?php
        $stmt = $dbConnection->prepare('SELECT * FROM ticket_fields where status=:n and for_client=:c');
        $stmt->execute(array(':n' => '1',':c' => '1'));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {


?>

                      <div class="control-group" id="">
    <div class="controls">
        <div class="form-group">
            <label for="<?=$row['hash'];?>" class="col-sm-2 control-label"><small><?=$row['name'];?>: </small></label>

            <div class="col-sm-10" style=" padding-top: 5px; ">

<?php 
if ($row['t_type'] == "text") {
    $v=$row['value'];
    if ($row['value'] == "0") {$v="";}
?>
<input type="text" class="form-control input-sm" name="<?=$row['hash'];?>" id="<?=$row['hash'];?>" placeholder="<?=$row['placeholder'];?>" value='<?=$v;?>'>
<?php } ?>
<?php 
if ($row['t_type'] == "textarea") {
    $v=$row['value'];
    if ($row['value'] == "0") {$v="";}
?>
<textarea rows="3" class="form-control input-sm animated" name="<?=$row['hash'];?>" id="<?=$row['hash'];?>" placeholder="<?=$row['placeholder'];?>"><?=$v;?></textarea>
<?php } ?>
<?php 
if ($row['t_type'] == "select") {
    $v=$row['value'];
    if ($row['value'] == "0") {$v="";}
?>
<select data-placeholder="<?=$row['placeholder'];?>" class="chosen-select form-control" id="<?=$row['hash'];?>" name="<?=$row['hash'];?>">

<?php 
$v=explode(",", $row['value']);
 foreach ($v as $value) {
     # code...
 
?>
                            <option value="<?=$value;?>"><?=$value;?></option>

                            <?php
                        }
                            ?>
                
                        
            </select>
<?php } ?>

<?php 
if ($row['t_type'] == "multiselect") {
    $v=$row['value'];
    if ($row['value'] == "0") {$v="";}
?>





<select data-placeholder="<?=$row['placeholder'];?>" class="multi_field" id="<?=$row['hash'];?>" name="<?=$row['hash'];?>[]" multiple="multiple" >

<?php 
$v=explode(",", $row['value']);
 foreach ($v as $value) {
     # code...
 
?>
                            <option value="<?=$value;?>"><?=$value;?></option>

                            <?php
                        }
                            ?>
                
                        
            </select>
<?php } ?>
                
            </div>
            
        </div>
    </div>
    
    </div> 

    <?php
}
    ?>
</div>
    </form>
    
<!--######### ADDITIONAL FIELDS ############## -->






<div class="col-md-2"></div>
<div class="col-md-10" id="processing">
    <div class="btn-group btn-group-justified">
        <div class="btn-group">
            <button id="enter_ticket_client" class="btn btn-success" type="button"><i class="fa fa-check-circle-o"></i> <?php echo lang('NEW_button_create'); ?></button>
        </div>
        <div class="btn-group">
            <button id="reset_ticket" class="btn btn-default" type="submit"><i class="fa fa-eraser"></i> <?php echo lang('NEW_button_reset'); ?></button>
        </div>
    </div>
    <input type="hidden" id="file_array" value="">
    <input type="hidden" id="d_finish_val" value="NULL">
    <input type="hidden" id="client_id_param" value="<?php
        echo $_SESSION['helpdesk_user_id']; ?>">
    <input type="hidden" id="hashname" value="<?php echo md5(time()); ?>">
    <input type="hidden" id="status_action" value="">
    <input type="hidden" id="prio" value="1">
    <input type="hidden" value="<?php
        echo $_SESSION['helpdesk_user_id']; ?>" id="user_init_id">

<input type="hidden" id="file_types" value="<?php echo $CONF['file_types'] ?>">
<input type="hidden" id="file_size" value="<?php echo $CONF['file_size'] ?>">





</div>


</div>
</div>
</div>

    <br>

</div>
                                
                                
                                
                                
                                
                                
                                </div>
</div>



            </div>
</div>
                </section>












<?php

*/
        include ("footer.inc.php");

    }
} else {
    include 'auth.php';
}
?>
