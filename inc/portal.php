<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
      $CONF['title_header'] = lang('PORTAL_title') . " - " . $CONF['name_of_firm'];


        include ("head.inc.php");
        include ("navbar.inc.php");
?>
<section class="content-header">
                    <h1>
                        <i class="icon-svg" style=" padding-right: 6px;"></i> <?php echo lang('PORTAL_title'); ?>
                        <small><?php echo lang('PORTAL_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('PORTAL_title'); ?></li>
                    </ol>
                </section>
                
                
                <section class="content">

                    <!-- row -->
                    

<div class="row">
                    <div class="col-md-3">
                    <div class="callout">
                                        
                                        <small> <i class="fa fa-info-circle"></i> <?php echo lang('PORTAL_helper'); ?>
                                        </small>
                                        </div>
                    </div>
                    <div class="col-md-9">
 <div class="box box-solid">
                                <div class="box-header">
<h3 class="box-title"><i class="fa fa-cog"></i> <?=lang('PERF_menu_main_conf');?> </h3>
</div>
                                <div class="box-body">
                                





<div class="form-horizontal" role="form">







<div class="form-group">
    <label for="portal_status" class="col-sm-4 control-label"><small><?=lang('cron_active');?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="portal_status">
  <option value="true" <?php
        if (get_conf_param('portal_status') == "true") {
            echo "selected";
        } ?>><?php
        echo lang('CONF_true'); ?></option>
  <option value="false" <?php
        if (get_conf_param('portal_status') == "false") {
            echo "selected";
        } ?>><?php
        echo lang('CONF_false'); ?></option>
</select>    

</div>
  </div>
<hr>
<div class="form-group">
    <label for="portal_msg_status" class="col-sm-4 control-label"><small><?=lang('PORTAL_msg_status');?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="portal_msg_status">
  <option value="true" <?php
        if (get_conf_param('portal_msg_status') == "true") {
            echo "selected";
        } ?>><?php
        echo lang('CONF_true'); ?></option>
  <option value="false" <?php
        if (get_conf_param('portal_msg_status') == "false") {
            echo "selected";
        } ?>><?php
        echo lang('CONF_false'); ?></option>
</select>    

</div>
  </div>

<div class="form-group">
    <label for="msg_title" class="col-sm-4 control-label"><small><?php echo lang('EXT_perf_msg_t'); ?></small></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="msg_title" type="text" class="form-control input-sm" id="msg_title" placeholder="<?php echo lang('EXT_perf_msg_t'); ?>" value="<?=get_conf_param('portal_msg_title');?>">
        </div>
  </div>

  <div class="form-group">
    <label for="mess" class="col-sm-4 control-label"><small><?php echo lang('MAIL_msg'); ?></small></label>
        <div class="col-sm-8">
        <textarea placeholder="" class="form-control input-sm animated" name="mess" id="mess" rows="3"><?=get_conf_param('portal_msg_text');?></textarea>
        

        </div>
  </div>
<?php

if (get_conf_param('portal_msg_type') == "info") { $mp['info']="checked";}
else if (get_conf_param('portal_msg_type') == "warning") { $mp['warning']="checked";}
else if (get_conf_param('portal_msg_type') == "danger") { $mp['danger']="checked";}

?>

   <div class="form-group">
  <label for="mess" class="col-sm-4 control-label"><small><?=lang('CONF_messages_type');?></small></label>
  <div class="col-sm-8">
    <div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_1" value="info" <?=$mp['info'];?>>
    <strong class="text-info">Info</strong>
  </label>
</div>
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_0" value="warning" <?=$mp['warning'];?>>
    <strong class="text-warning">Warning</strong>
  </label>
</div>

<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_2" value="danger" <?=$mp['danger'];?>>
    <strong class="text-danger">Danger</strong>
  </label>
  
</div>

  </div>
  </div>

<hr>
   <div class="form-group">
  <label for="mess" class="col-sm-4 control-label"><small><?=lang('PORTAL_nf_users_list');?></small></label>
        <div class="col-md-8" style="" id="dsd">
    
    
    <select data-placeholder="<?php echo lang('NAVBAR_users'); ?>" id="users_do" name="unit_id" class="form-control input-sm" multiple>
        <option></option>


<?php
        
        /* $qstring = "SELECT fio as label, id as value FROM users where status='1' and login !='system' order by fio ASC;";
                $result = mysql_query($qstring);//query the database for entries containing the term
        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
        */
        
        $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=:n and is_client=0 order by fio ASC');
        $stmt->execute(array(':n' => '1'));
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


$ulist=get_conf_param('portal_posts_mail_users');
$ulist=explode(",", $ulist);
$c="";
if (in_array($row['value'], $ulist)) {
  $c="selected";
}

?>
                    <option data-foo="<?php echo $s; ?>" value="<?php echo $row['value'] ?>" <?=$c;?>><?php echo nameshort($row['label']) ?> </option>

                <?php
        }
?>
    </select>
            

        </div>
  </div>






  
  
<center>
    <button type="submit" id="conf_edit_portal" class="btn btn-success"><i class="fa fa-pencil"></i> <?php
        echo lang('JS_save'); ?></button>
    
</center>

<div id="conf_edit_portal_res"></div>
  
    </div>






                                </div>
                                </div>
                    </div>

</div>






                </section>














<?php
        include ("footer.inc.php");
?>

<?php
    }
} else {
    include '../auth.php';
}
?>
