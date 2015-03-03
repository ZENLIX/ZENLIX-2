<?php
session_start();

//include("../functions.inc.php");
include_once ("../functions.inc.php");

if (isset($_POST['menu'])) {
    
    if ($_POST['menu'] == 'new') {
        if (get_user_val('priv_add_client') == "1") {
            
            if (isset($_GET['ok'])) {
?>
    <div class="alert alert-success">
        
        <?php echo lang('EXT_client_add_after'); ?>
        
    </div>
    <?php
            }
?>

<div class="box box-solid">
<div class="box-header">
                                    <h3 class="box-title"><?php echo lang('USERS_new_add'); ?></h3>
                                </div>
                                
                                
                                
    <div class="box-body">
<div id="form_message"></div>

<form class="form-horizontal" role="form">



  <div class="form-group" id="login_user_grp">
    <label for="login" class="col-sm-2 control-label"><small><?php echo lang('USERS_login'); ?></small></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="login_user" type="" class="form-control input-sm" id="login_user" placeholder="<?php echo lang('USERS_login'); ?>">
        </div>
  </div>
  
  
  
  
    
      <div class="form-group" id="fio_user_grp">
    <label for="fio" class="col-sm-2 control-label"><small><?php echo lang('USERS_fio'); ?></small></label>
    <div class="col-sm-10">
    <input autocomplete="off" id="fio_user" name="fio_user" type="" class="form-control input-sm" placeholder="<?php echo lang('USERS_fio_full'); ?>">
    </div>
  </div>
    
    
    
    
    
    
    
    <div class="form-group">
    <label for="mail" class="col-sm-2 control-label"><small><?php echo lang('USERS_mail'); ?></small></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="mail" type="text" class="form-control input-sm" id="mail" placeholder="<?php echo lang('USERS_mail'); ?>">
        </div>
  </div>
  
  

  
  
    <div class="form-group">
    <label for="tel" class="col-sm-2 control-label"><small><?php echo lang('WORKER_tel'); ?></small></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="tel" type="text" class="form-control input-sm" id="tel" placeholder="<?php echo lang('WORKER_tel'); ?>">
        </div>
  </div>
  
      <div class="form-group">
    <label for="skype" class="col-sm-2 control-label"><small>Skype</small></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="skype" type="text" class="form-control input-sm" id="skype" placeholder="skype">
        </div>
  </div>
  
      <div class="form-group">
    <label for="adr" class="col-sm-2 control-label"><small><?php echo lang('APPROVE_adr'); ?></small></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="adr" type="text" class="form-control input-sm" id="adr" placeholder="<?php echo lang('APPROVE_adr'); ?>">
        </div>
  </div>
  
  
   <div class="control-group">
    <div class="controls">
        <div class="form-group">
            <label for="posada" class="col-sm-2 control-label"><small><?php echo lang('WORKER_posada'); ?>: </small></label>
            <div class="col-sm-10" style="">
                <select name="posada" id="posada" data-placeholder="<?php echo lang('WORKER_posada'); ?>" class="chosen-select form-control input-sm">
                    <option value="0"></option>
                    <?php
            $stmt = $dbConnection->prepare('SELECT name FROM posada order by name COLLATE utf8_unicode_ci ASC');
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

                                
                                
 <div class="control-group">
    <div class="controls">
        <div class="form-group">
            <label for="pidrozdil" class="col-sm-2 control-label"><small><?php echo lang('WORKER_unit'); ?>: </small></label>
            <div class="col-sm-10" style="">
                <select name="pid" id="pidrozdil" data-placeholder="<?php echo lang('WORKER_unit'); ?>" class="chosen-select form-control input-sm">
                    <option value="0"></option>
                    <?php
            
            /*$qstring = "SELECT name FROM units order by name COLLATE utf8_unicode_ci ASC";
                    $result = mysql_query($qstring);                    
                    while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){*/
            
            $stmt = $dbConnection->prepare('SELECT name FROM units order by name COLLATE utf8_unicode_ci ASC');
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
  
  
 <div class=""><hr></div>
<div class="">
<center>
    <button type="submit" id="create_user_approve" class="btn btn-success"><?php echo lang('USERS_make_create'); ?></button>
</center>

<div id="res"></div>

</div>
</form>
    </div>
</div>

      


<?php
        }
    }
    
    if ($_POST['menu'] == 'list') {
        
        $page = ($_POST['page']);
        $perpage = '15';
        
        $start_pos = ($page - 1) * $perpage;
?>

<div class="box box-solid">
<div class="box-header">

                                </div>
                                
                                
                                
    <div class="box-body">
    
    

  
  <div class="panel-body">

      
        <?php
        
        //include("../dbconnect.inc.php");
        if (isset($_POST['t'])) {
            $t = ($_POST['t']);
            
            //$results = mysql_query("SELECT id, fio, login, tel, unit_desc, adr, email, posada from clients where ((fio like '%" . $t . "%') or (login like '%" . $t . "%')) limit $start_pos, $perpage;");
            
            $stmt = $dbConnection->prepare('SELECT id, fio, login, tel, unit, adr, email, posada, uniq_id, is_client,skype,status from users where ((fio like :t) or (login like :t2) or (tel like :t3)) limit :start_pos, :perpage');
            $stmt->execute(array(':t' => '%' . $t . '%', ':t2' => '%' . $t . '%', ':t3' => '%' . $t . '%', ':start_pos' => $start_pos, ':perpage' => $perpage));
            $res1 = $stmt->fetchAll();
            
            //foreach($res1 as $row) {
            
            
        }
        if (!isset($_POST['t'])) {
            
            //$results = mysql_query("SELECT id, fio, login, tel, unit_desc, adr, email, posada from clients limit $start_pos, $perpage;");
            
            $stmt = $dbConnection->prepare('SELECT id, fio, login, priv, unit, status, uniq_id,is_client,email,tel, adr, skype from users limit :start_pos, :perpage');
            $stmt->execute(array(':start_pos' => $start_pos, ':perpage' => $perpage));
            $res1 = $stmt->fetchAll();
        }
        
        //while ($row = mysql_fetch_assoc($results)) {
        foreach ($res1 as $row) {
            
            $unit = view_array(get_unit_name_return($row['unit']));
            $statuss = $row['status'];
            
            if ($row['is_client'] == "1") {
                $priv = lang('USERS_p_4');
            } else if ($row['is_client'] == "0") {
                
                if ($row['priv'] == "0") {
                    $priv = lang('USERS_p_1');
                } else if ($row['priv'] == "1") {
                    $priv = lang('USERS_p_2');
                } else if ($row['priv'] == "2") {
                    $priv = lang('USERS_p_3');
                }
            }
            if ($statuss != "1") {
                $r = "<span class=\"label label-danger\">disable</span>";
            } else if ($statuss == "1") {
                $r = "<span class=\"label label-success\">enable</span>";
            }
            
            $adr = $row['adr'];
            $tel = $row['tel'];
            $skype = $row['skype'];
            $mail = $row['email'];
            $uniq_id = $row['uniq_id'];
            
            //FIO
            
            
?>
    
    
    
    <div class="box box-solid">
                                <div class="box-header">
                                    <h3 class="box-title "><a href="<?php
            echo $CONF['hostname']; ?>view_user?<?php echo $row['uniq_id']; ?>" class="text-light-blue"><?php
            echo $row['fio']; ?></a></h3>
                                    <div class="box-tools pull-right">
<?php echo $r; ?><?php echo get_user_status($row['id']); ?>

                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                    
                                    <div class="col-md-2">
                                         <img style="max-height: 105px;" src="<?php echo get_user_img_by_id($row['id']); ?>" alt="user image" class="<?php echo get_user_status_text($row['id']); ?>"/>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row">

                                            <?php if ($row['login']) { ?>
                                            <div class="col-md-6"> <small class="text-muted"> <?php echo lang('USERS_login'); ?>: </small></div>
                                            <div class="col-md-6"> <small><?=$row['login']; ?></small></div>
                                            <?php } ?>

                                            <?php if ($adr) { ?>
                                            <div class="col-md-6"> <small class="text-muted"> <?=lang('APPROVE_adr');?>: </small></div>
                                            <div class="col-md-6"> <small><?=$adr; ?></small></div>
                                            <?php } ?>

                                            <?php if ($skype) { ?>
                                            <div class="col-md-6"> <small class="text-muted"> Skype: </small></div>
                                            <div class="col-md-6"> <small><?=$skype; ?></small></div>
                                            <?php } ?>

                                            <?php if ($tel) { ?>
                                            <div class="col-md-6"> <small class="text-muted"> <?=lang('APPROVE_tel');?>: </small></div>
                                            <div class="col-md-6"> <small><?=$tel; ?></small></div>          
                                            <?php } ?>

                                            <?php if ($mail) { ?>
                                            <div class="col-md-6"> <small class="text-muted"> <?=lang('APPROVE_mail');?>: </small></div>
                                            <div class="col-md-6"> <small><?=$mail; ?></small></div>        
                                            <?php } ?>                                                                                                                                                               

                                        </div>
                                        
                                       
                                        
                                        
                                    
                                       
                                        
                                    </div>
                                    <div class="col-md-3">
                                    
                                    </div>
                                    <div class="col-md-4">
                                        
                                        <!--button class="btn btn-default btn-block btn-xs"><i class="fa fa-envelope-o"></i> Отправить сообщение</button-->
                                        
<?php 

if (get_clients_total_ticket($row['id']) != 0) {


if (($row['priv'] == 2) || ($row['priv'] == 0)) {
?>


<a href="<?php
                echo $CONF['hostname']; ?>userinfo?user=<?php echo $row['uniq_id']; ?>" class="btn btn-default btn-xs btn-block" ><i class="fa fa-tag"></i> <?php echo lang('NAVBAR_all_tickets'); ?> (<?=get_clients_total_ticket($row['id']);?>)</a>


    <?php

}
}
?>



                                        <?php
            if (get_user_val('priv_edit_client') == "1") { ?>
                                                                                <a href="<?php
                echo $CONF['hostname']; ?>clients?edit=<?php echo $row['uniq_id']; ?>" class="btn btn-default btn-xs btn-block" ><i class="fa fa-pencil"></i> <?php echo lang('CONF_act_edit'); ?></a><?php
            } ?>
                                                                                
                                                                                 <a href="messages?to=<?php echo $uniq_id; ?>" class="btn btn-primary btn-block btn-xs"><i class="fa fa-comments"></i> <?php echo lang('EXT_do_write_message'); ?></a>
       

                                    </div>
                                    
                                    </div>
                                    
                                </div><!-- /.box-body -->
                            </div>
    
    
    
    
          <!--tr class="">
            <?php
            if (get_user_val('priv_edit_client') == "1") { ?>
            <td><small><a href="<?php
                echo $CONF['hostname']; ?>clients?edit=<?php echo $row['uniq_id']; ?>"><?php
                echo $row['fio']; ?></a></small></td>
            <?php
            } ?>
            <?php
            if (get_user_val('priv_edit_client') == "0") { ?>
            <td><small><a href="<?php
                echo $CONF['hostname']; ?>view_user?<?php echo $row['uniq_id']; ?>"><?php
                echo $row['fio']; ?></a></small></td>
            <?php
            } ?>
            <td><small><?php
            echo $row['login']; ?></small></td>
            <td><small><?php
            echo $row['email']; ?></small></td>
            <td><small><?php
            echo $row['tel']; ?></small></td>
           
            <td><small><center><?php echo $r; ?></center></small></td>
          </tr-->
          
          
          
          
          
          
          
          
          
          
          
          <?php
        } ?>

  </div>
</div>
</div>




<?php
    }
    if ($_POST['menu'] == 'edit') {
        
        //echo $_POST['id'];
        $usid = ($_POST['id']);
        
        /* $query = "SELECT fio, pass, login, status, priv, unit,email,messages,lang from users where id='$usid'; ";
        $sql = mysql_query($query) or die(mysql_error());
        if (mysql_num_rows($sql) == 1) {
        $row = mysql_fetch_assoc($sql);
        */
        
        $stmt = $dbConnection->prepare('SELECT id, fio, pass, login, status, priv, unit,email,messages,lang,priv_add_client,priv_edit_client,ldap_key,pb,tel,skype,adr, unit_desc, posada, is_client,messages_title from users where uniq_id=:usid');
        $stmt->execute(array(':usid' => $usid));
        $res1 = $stmt->fetchAll();
        
        foreach ($res1 as $row) {
            
            $priv_add_client = $row['priv_add_client'];
            $priv_edit_client = $row['priv_edit_client'];
            $fio = $row['fio'];
            $login = $row['login'];
            $pass = $row['pass'];
            $status = $row['status'];
            $priv = $row['priv'];
            $unit = $row['unit'];
            $email = $row['email'];
            $messages = $row['messages'];
            $langu = $row['lang'];
            $lk = $row['ldap_key'];
            $push = $row['pb'];
            $tel = $row['tel'];
            $skype = $row['skype'];
            $adr = $row['adr'];
            $msg_t = $row['messages_title'];
            
            $unitss = $row['unit_desc'];
            $posada = $row['posada'];
            
            $is_client = $row['is_client'];
            
            if ($priv_add_client == "1") {
                $priv_add_client = "checked";
            } else {
                $priv_add_client = "";
            }
            if ($priv_edit_client == "1") {
                $priv_edit_client = "checked";
            } else {
                $priv_edit_client = "";
            }
            
            if ($lk == "1") {
                $lk_status = "checked";
                $pd = "disabled";
            } else {
                $lk_status = "";
                $pd = "";
            }
            
            if ($langu == "en") {
                $status_lang_en = "selected";
            } else if ($langu == "ru") {
                $status_lang_ru = "selected";
            } else if ($langu == "ua") {
                $status_lang_ua = "selected";
            }
            
            if ($status == "0") {
                $status_lock = "selected";
            }
            if ($status == "1") {
                $status_unlock = "selected";
            }
            
            if ($is_client == "1") {
                $status_client = "checked";
            } else if ($is_client == "0") {
                
                if ($priv == "0") {
                    $status_admin = "checked";
                } else if ($priv == "1") {
                    $status_user = "checked";
                } else if ($priv == "2") {
                    $status_superadmin = "checked";
                }
            }
        }
        if (isset($_GET['ok'])) {
?>
        <div class="alert alert-success">
        
        <?php echo lang('EXT_client_add_after'); ?>
        
    </div>
    <?php
        }
?>




<div class="box box-solid">
<div class="box-header">
                                    <h3 class="box-title"><?php echo lang('USERS_make_edit_user'); ?></h3>
                                </div>
                                
                                
                                
    <div class="box-body">
    
    
    
    
    
    <form class="form-horizontal" role="form">



  <div class="form-group" id="login_user_grp">
    <label for="login" class="col-sm-2 control-label"><small><?php echo lang('USERS_login'); ?></small></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="login_user" type="" class="form-control input-sm" id="login_user2" exclude-param="<?php echo $login ?>" placeholder="<?php echo lang('USERS_login'); ?>" value="<?php echo $login ?>">
        </div>
  </div>
  

    
      <div class="form-group" id="fio_user_grp">
    <label for="fio" class="col-sm-2 control-label"><small><?php echo lang('USERS_fio'); ?></small></label>
    <div class="col-sm-10">
    <input autocomplete="off" id="fio_user" name="fio_user" type="" class="form-control input-sm" placeholder="<?php echo lang('USERS_fio_full'); ?>" value="<?php echo $fio ?>">
    </div>
  </div>
    
    
    
    
    
    
    
    <div class="form-group">
    <label for="mail" class="col-sm-2 control-label"><small><?php echo lang('USERS_mail'); ?></small></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="mail" type="text" class="form-control input-sm" id="mail" placeholder="<?php echo lang('USERS_mail'); ?>" value="<?php echo $email; ?>">
        </div>
  </div>
  

  
  
    <div class="form-group">
    <label for="tel" class="col-sm-2 control-label"><small><?php echo lang('WORKER_tel'); ?></small></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="tel" type="text" class="form-control input-sm" id="tel" placeholder="<?php echo lang('WORKER_tel'); ?>" value="<?php echo $tel; ?>">
        </div>
  </div>
  
      <div class="form-group">
    <label for="skype" class="col-sm-2 control-label"><small>Skype</small></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="skype" type="text" class="form-control input-sm" id="skype" placeholder="skype" value="<?php echo $skype; ?>">
        </div>
  </div>
  
      <div class="form-group">
    <label for="adr" class="col-sm-2 control-label"><small><?php echo lang('APPROVE_adr'); ?></small></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="adr" type="text" class="form-control input-sm" id="adr" placeholder="<?php echo lang('APPROVE_adr'); ?>" value="<?php echo $adr; ?>">
        </div>
  </div>
  
  
   <div class="control-group">
    <div class="controls">
        <div class="form-group">
            <label for="posada" class="col-sm-2 control-label"><small><?php echo lang('WORKER_posada'); ?>: </small></label>
            <div class="col-sm-10" style="">
                <select name="posada" id="posada" data-placeholder="<?php echo lang('WORKER_posada'); ?>" class="chosen-select form-control input-sm">
                    <option value="0"></option>
                    <?php
        $stmt = $dbConnection->prepare('SELECT name FROM posada order by name COLLATE utf8_unicode_ci ASC');
        $stmt->execute();
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {
            
            $se = "";
            if ($posada == $row['name']) {
                $se = "selected";
            }
?>

                        <option <?php echo $se; ?> value="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></option>

                    <?php
        }
?>

                </select>
            </div>
        </div>

    </div>
</div>

                                
                                
 <div class="control-group">
    <div class="controls">
        <div class="form-group">
            <label for="pidrozdil" class="col-sm-2 control-label"><small><?php echo lang('WORKER_unit'); ?>: </small></label>
            <div class="col-sm-10" style="">
                <select name="pid" id="pidrozdil" data-placeholder="<?php echo lang('WORKER_unit'); ?>" class="chosen-select form-control input-sm">
                    <option value="0"></option>
                    <?php
        
        /*$qstring = "SELECT name FROM units order by name COLLATE utf8_unicode_ci ASC";
                    $result = mysql_query($qstring);                    
                    while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){*/
        
        $stmt = $dbConnection->prepare('SELECT name FROM units order by name COLLATE utf8_unicode_ci ASC');
        $stmt->execute();
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {
            
            $se2 = "";
            if ($unitss == $row['name']) {
                $se2 = "selected";
            }
?>

                        <option <?php echo $se2; ?> value="<?php echo $row['name'] ?>"><?php echo $row['name'] ?></option>

                    <?php
        }
?>

                </select>
            </div>
        </div>

    </div>
</div>  
  

<div class=""><hr></div>
<div class="">
<?php
        if (get_user_val('priv_edit_client') == "1") { ?>
<center>
    <button type="submit" id="edit_user_approve" value="<?php echo $usid; ?>" class="btn btn-success"><?php echo lang('USERS_make_edit_user'); ?></button>
</center>
<?php
        } ?>
</div>


<div id="res"></div>


</form>
    

    

    </div>
</div>










<?php
    }
}
?>
