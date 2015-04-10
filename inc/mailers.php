<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        include ("head.inc.php");
        include ("navbar.inc.php");
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-paper-plane-o"></i> <?php echo lang('PORTAL_mailers'); ?>
                        <small><?php echo lang('PORTAL_mailers_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('PORTAL_mailers'); ?></li>
                    </ol>
                </section>
                
                
                <section class="content">

                    <!-- row -->
                    

<div class="row">
                    <div class="col-md-3">
                    <div class="callout">
                                        
                                        <small> <i class="fa fa-info-circle"></i> <?php echo lang('PORTAL_mailers_help'); ?>
                                        </small>
                                        </div>
                    </div>
                    <div class="col-md-9">
 <div class="box box-solid">
                                <div class="box-header">
<h3 class="box-title"><i class="fa fa-bell"></i> <?=lang('MAILERS_p_master');?> </h3>
</div>
                                <div class="box-body">
                                





<div class="form-horizontal" role="form">

<!--
1. Пользователи системы
  - По привилегиям (клиент/нач/нач отдела/пользователь)
  - По статусу: заблокирован/разблокирован
  - По Отделам: Опр отдел или несколько
2. Конкретно кому-то или ВСЕМ

ВСЕМ с УСЛОВИЯМ - 
-->

<div class="radio col-sm-6">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios1" value="1" checked>
    <?=lang('MAILERS_p_u_list');?>
    <p class="help-block"><small><?=lang('MAILERS_p_u_list_ext');?></small></p>
  </label>
</div>
<div class="radio col-sm-6">

 <div class="form-group">
  
  <div class="col-sm-12">
  <select multiple="multiple" id="users_list" name="unit[]" class="msel" >
<?php
        
        /*$qstring = "SELECT name as label, id as value FROM deps where id !='0' ;";
                        $result = mysql_query($qstring);
                        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
        */
        
        $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where status=1 AND email REGEXP :r');
        $stmt->execute(array(':r'=>'^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$'));

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

    </select>
  </div>
  </div>


</div>

<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios"  id="optionsRadios2" value="2">
   <?=lang('MAILERS_p_all');?>
    <p class="help-block"><small><?=lang('MAILERS_p_all_ext');?></small></p>
  </label>
</div>

<div class="col-sm-3">
<strong><small> <?=lang('MAILERS_p_priv');?></small></strong>
</div>
<div class="col-sm-9">

<div class="form-group">
  
  <div class="col-sm-12">
  <select multiple="multiple" id="users_priv" class="msel" name="unit[]" disabled>

<option value="2"><?=lang('PORTAL_mailers_priv2');?></option>
<option value="0"><?=lang('PORTAL_mailers_priv0');?></option>
<option value="1"><?=lang('PORTAL_mailers_priv1');?></option>
<option value="client"><?=lang('PORTAL_mailers_privclient');?></option>
    </select>
    <p class="help-block"><small><?=lang('MAILERS_p_help');?></small></p>
  </div>
  </div>

</div>
<div class="col-sm-12"><br></div>
<div class="col-sm-3">
<strong><small><?=lang('MAILERS_p_units');?></small></strong>
</div>
<div class="col-sm-9">
 <div class="form-group">
  <div class="col-sm-12">
  <select multiple="multiple" id="users_units" name="unit[]" class="msel" disabled>
<?php
        
        
        
        $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n');
        $stmt->execute(array(':n' => '0'));
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {
            
            //echo($row['label']);
            $row['label'] = $row['label'];
            $row['value'] = (int)$row['value'];
?>

                            <option value="<?php echo $row['value'] ?>"><?php echo $row['label'] ?></option>

                        <?php
        }



            $stmt22 = $dbConnection->prepare('SELECT value FROM perf where param=:tid');
            $stmt22->execute(array(
                ':tid' => 'mailers_text'
            ));
            $mm = $stmt22->fetch(PDO::FETCH_ASSOC);
            $mmm=$mm['value'];
?>

    </select>
    <p class="help-block"><small><?=lang('MAILERS_p_help');?></small></p>
  </div>
  </div>
</div>

<div class="col-sm-3">
<button class="btn btn-block btn-default btn-xs" id="check_mailers"><?=lang('PORTAL_mailers_check');?></button>
</div>
<div class="col-sm-9">
<div id="mailers_check_res"></div>
</div>

<div class="col-sm-12">

<hr>
</div>
<div class="form-group">
<div class="col-sm-12">
<input class="form-control" id="subj_mailers" type="text" placeholder="<?=lang('POST_MAIL_subj');?>" value="<?=get_conf_param('mailers_subj');?>">
</div>
</div>
  <div class="form-group">
<div class="col-sm-12" >
<div id="mailers_msg"><?=$mmm;?></div>
</div>
</div>






  <?php

            $stmt223 = $dbConnection->prepare('SELECT count(id) as cid FROM notification_pool where type_op=:tid');
            $stmt223->execute(array(
                ':tid' => 'mailers'
            ));
            $mc = $stmt223->fetch(PDO::FETCH_ASSOC);
            
if ($mc['cid'] > 0) {
  ?>
<div class="alert alert-danger"><?=lang('MAILERS_ERROR2');?></div>
  <?php
}
else if ($mc['cid'] == 0) {
  ?>

  
<center>
    <button type="submit" id="send_mail" class="btn btn-success"><i class="fa fa-paper-plane-o"></i> <?=lang('MAILERS_p_make');?></button>
    
</center>
  <?php
}
  ?>



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
