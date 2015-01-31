<?php
session_start();

//include("../functions.inc.php");
include_once ("../functions.inc.php");

if (isset($_POST['menu'])) {
    
    if ($_POST['menu'] == 'new') {
        
        if (isset($_GET['ok'])) {
?>
  <div class="alert alert-success"><?php echo lang('USERS_msg_add'); ?></div>
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
    <label for="login" class="col-sm-2 control-label"><?php echo lang('USERS_login'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="login_user" type="" class="form-control input-sm" id="login_user" placeholder="<?php echo lang('USERS_login'); ?>">
        </div>
  </div>
  <div class="form-group" id="pass_user_grp">
    <label for="exampleInputPassword1" class="col-sm-2 control-label"><?php echo lang('USERS_pass'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="password" type="password" class="form-control input-sm" id="exampleInputPassword1" placeholder="<?php echo lang('USERS_pass'); ?>">
        </div>
  </div>
  <div class="form-group">
  <label for="ldap_auth_key" class="col-sm-2 control-label">LDAP-auth</label>
  <div class="col-sm-10">
  
  
  
    <div class="col-sm-10">
    <div class="checkbox">
    <label>
      <input type="checkbox" id="ldap_auth_key"> <?php echo lang('CONF_true'); ?>
      <p class="help-block"><small><?php echo lang('EXT_perf_must_ldap'); ?> </small></p>
    </label>
  </div>
    </div>
  </div>
    </div>
    
    
    <hr>
    
      <div class="form-group" id="fio_user_grp">
    <label for="fio" class="col-sm-2 control-label"><?php echo lang('USERS_fio'); ?></label>
    <div class="col-sm-10">
    <input autocomplete="off" id="fio_user" name="fio_user" type="" class="form-control input-sm" placeholder="<?php echo lang('USERS_fio_full'); ?>">
    </div>
  </div>
    
    
    
    
    
    
    
    <div class="form-group">
    <label for="mail" class="col-sm-2 control-label"><?php echo lang('USERS_mail'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="mail" type="text" class="form-control input-sm" id="mail" placeholder="<?php echo lang('USERS_mail'); ?>">
        </div>
  </div>
  
  
      <div class="form-group">
    <label for="push" class="col-sm-2 control-label">Push</label>
        <div class="col-sm-10">
    <input autocomplete="off" name="push" type="text" class="form-control input-sm" id="push" placeholder="push">
        </div>
  </div>
  
  
    <div class="form-group">
    <label for="tel" class="col-sm-2 control-label"><?php echo lang('APPROVE_tel'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="tel" type="text" class="form-control input-sm" id="tel" placeholder="<?php echo lang('APPROVE_tel'); ?>">
        </div>
  </div>
  
      <div class="form-group">
    <label for="skype" class="col-sm-2 control-label">Skype</label>
        <div class="col-sm-10">
    <input autocomplete="off" name="skype" type="text" class="form-control input-sm" id="skype" placeholder="skype">
        </div>
  </div>
  
      <div class="form-group">
    <label for="adr" class="col-sm-2 control-label"><?php echo lang('APPROVE_adr'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="adr" type="text" class="form-control input-sm" id="adr" placeholder="<?php echo lang('APPROVE_adr'); ?>">
        </div>
  </div>
  
  
   <div class="control-group">
    <div class="controls">
        <div class="form-group">
            <label for="posada" class="col-sm-2 control-label"><?php echo lang('WORKER_posada'); ?>: </label>
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
            <label for="pidrozdil" class="col-sm-2 control-label"><?php echo lang('WORKER_unit'); ?>: </label>
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
  
  
  <hr>
  
  <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?php echo lang('USERS_profile_priv'); ?></label>
  <div class="col-sm-10">
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios3" value="2" >
    <strong class="text-warning"><?php echo lang('USERS_nach1'); ?></strong>
    <p class="help-block"><small><?php echo lang('USERS_nach1_desc'); ?></small></p>
  </label>
</div>

<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios1" value="0" >
    <strong class="text-success"><?php echo lang('USERS_nach'); ?></strong>
    <p class="help-block"><small><?php echo lang('USERS_nach_desc'); ?></small></p>
  </label>
</div>
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios2" value="1">
    <strong class="text-info"><?php echo lang('USERS_wo'); ?></strong>
    <p class="help-block"><small><?php echo lang('USERS_wo_desc'); ?></small></p>
  </label>
  
</div>

<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios4" value="4" checked="checked">
    <strong class="text-default"><?php echo lang('EXT_client'); ?></strong>
    <p class="help-block"><small><?php echo lang('EXT_client_what'); ?></small></p>
  </label>
  
</div>
  </div>
  </div>
  
  
    <div class="form-group">
  <label for="my-select" class="col-sm-2 control-label"><?php echo lang('USERS_units'); ?></label>
  <div class="col-sm-10">
  <select multiple="multiple" id="my-select" name="unit[]" disabled>
<?php
        
        /*$qstring = "SELECT name as label, id as value FROM deps where id !='0' ;";
                        $result = mysql_query($qstring);
                        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
        */
        
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
?>

    </select>
  </div>
  </div>
  <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?php echo lang('USERS_privs'); ?></label>
  <div class="col-sm-10">
  
  
  
    <div class="col-sm-6">
    <div class="checkbox">
    <label>
      <input type="checkbox" id="priv_add_client" checked="checked" disabled> <?php echo lang('TICKET_p_add_client'); ?>
    </label>
  </div>
    </div>
    
        <div class="col-sm-6">
    <div class="checkbox">
    <label>
      <input type="checkbox" id="priv_edit_client" checked="checked" disabled> <?php echo lang('TICKET_p_edit_client'); ?>
    </label>
  </div>
    </div>
    
  </div>
    </div>
      <div class="form-group">
    <label for="lang" class="col-sm-2 control-label"><?php echo lang('SYSTEM_lang'); ?></label>
        <div class="col-sm-10">
    <select data-placeholder="<?php echo lang('SYSTEM_lang'); ?>" class="chosen-select form-control input-sm" id="lang" name="lang">
                    <option value="0"></option>
                    
                        <option value="en">English</option>
                        <option value="ru">Русский</option>
                        <option value="ua">Українська</option>
</select>
        </div>
  </div>
  
  
  

  
  <div class="form-group">
    <label for="msg_title" class="col-sm-2 control-label"><?php echo lang('EXT_perf_msg_t'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="msg_title" type="text" class="form-control input-sm" id="msg_title" placeholder="<?php echo lang('EXT_perf_msg_t'); ?>">
        </div>
  </div>
  

      <div class="form-group">
    <label for="mess" class="col-sm-2 control-label"><?php echo lang('MAIL_msg'); ?></label>
        <div class="col-sm-10">
        <textarea placeholder="<?php echo lang(''); ?>" class="form-control input-sm animated" name="mess" id="mess" rows="3"></textarea>
        

        </div>
  </div>
  
    <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?=lang('CONF_messages_type');?></label>
  <div class="col-sm-10">
    <div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_1" value="0" >
    <strong class="text-info">Info</strong>
  </label>
</div>
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_0" value="1" >
    <strong class="text-warning">Warning</strong>
  </label>
</div>

<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_2" value="2" >
    <strong class="text-danger">Danger</strong>
  </label>
  
</div>

  </div>
  </div>
  
    

<div class=""><hr></div>
<div class="">
<center>
    <button type="submit" id="create_user" class="btn btn-success"><?php echo lang('USERS_make_create'); ?></button>
</center>
</div>
</form>
  </div>
</div>

    


<?php
    }
    
    if ($_POST['menu'] == 'list') {
        
        $page = ($_POST['page']);
        $perpage = '15';
        
        $start_pos = ($page - 1) * $perpage;
?>

<div class="box box-solid">
<div class="box-header">
                                    <h3 class="box-title"><?php echo lang('USERS_make_edit_user'); ?></h3>
                                </div>
                                
                                
                                
  <div class="box-body">
  
  

  
  <div class="panel-body">
  <table class="table table-hover table-bordered">
        <thead>
          <tr>
      <th><center><small><?php echo lang('USERS_login'); ?>   </small></center></th>
            <th><center><small><?php echo lang('USERS_fio'); ?>     </small></center></th>
            <th><center><small>E-mail             </small></center></th>
            <th><center><small><?php echo lang('USERS_privs'); ?>   </small></center></th>
            <th><center><small><?php echo lang('USERS_unit'); ?>        </small></center></th>
            <th><center><small>Status               </small></center></th>
            <th><center><small>kill                 </small></center></th>
          </tr>
        </thead>
        <tbody>
        <?php
        
        //include("../dbconnect.inc.php");
        if (isset($_POST['t'])) {
            $t = ($_POST['t']);
            
            //$results = mysql_query("SELECT id, fio, login, tel, unit_desc, adr, email, posada from clients where ((fio like '%" . $t . "%') or (login like '%" . $t . "%')) limit $start_pos, $perpage;");
            
            $stmt = $dbConnection->prepare('SELECT id, fio, login, priv, tel, unit, adr, email, posada, uniq_id, is_client,status from users where ((fio like :t) or (login like :t2)) limit :start_pos, :perpage');
            $stmt->execute(array(':t' => '%' . $t . '%', ':t2' => '%' . $t . '%', ':start_pos' => $start_pos, ':perpage' => $perpage));
            $res1 = $stmt->fetchAll();
            
            //foreach($res1 as $row) {
            
            
        }
        if (!isset($_POST['t'])) {
            
            //$results = mysql_query("SELECT id, fio, login, tel, unit_desc, adr, email, posada from clients limit $start_pos, $perpage;");
            
            $stmt = $dbConnection->prepare('SELECT id, fio, login, priv, unit, status, uniq_id,is_client,email from users limit :start_pos, :perpage');
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
?>
          <tr class="">
            <td><small><?php
            echo $row['login']; ?></small></td>
            <td><small><a href="<?php
            echo $CONF['hostname']; ?>users?edit=<?php echo $row['uniq_id']; ?>"><?php
            echo $row['fio']; ?></a></small></td>
            <td><small><?php
            echo $row['email']; ?></small></td>
            <td><small><?php
            echo $priv; ?></small></td>
            <td><small><span data-toggle="tooltip" data-html="true" data-placement="right" title="<?php echo $unit; ?>"><?php echo lang('LIST_pin') ?> <?php echo count(get_unit_name_return($row['unit'])); ?> </span></small></td>
            <td><small><center><?php echo $r; ?></center></small></td>
            <td><small><center><button id="make_logout_user" value="<?php echo $row['uniq_id']; ?>" class="btn btn-warning btn-xs">logout</button></center></small></td>
          </tr>
          <?php
        } ?>
       </tbody>
      </table>
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
        
        $stmt = $dbConnection->prepare('SELECT fio, pass, login, status, priv, unit,email,messages,lang,priv_add_client,priv_edit_client,ldap_key,pb,tel,skype,adr, unit_desc, posada, is_client,messages_title, messages_type from users where uniq_id=:usid');
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
            $msg_type = $row['messages_type'];
            
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

            if ($msg_type == "0") { $msg_type_0="checked";}
            else if ($msg_type == "1") {$msg_type_1="checked";}
            else if ($msg_type == "2") {$msg_type_2="checked";}


        }
        if (isset($_GET['ok'])) {
?>
  <div class="alert alert-success"><?php echo lang('USERS_msg_edit_ok'); ?></div>
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
    <label for="login" class="col-sm-2 control-label"><?php echo lang('USERS_login'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="login_user" type="" class="form-control input-sm" id="login_user2" exclude-param="<?php echo $login ?>" placeholder="<?php echo lang('USERS_login'); ?>" value="<?php echo $login ?>">
        </div>
  </div>
  <div class="form-group" id="pass_user_grp">
    <label for="exampleInputPassword1" class="col-sm-2 control-label"><?php echo lang('USERS_pass'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="password" type="password" class="form-control input-sm" id="exampleInputPassword1" placeholder="<?php echo lang('USERS_pass'); ?>" <?php echo $pd ?>>
        </div>
  </div>
  <div class="form-group">
  <label for="ldap_auth_key" class="col-sm-2 control-label">LDAP-auth</label>
  <div class="col-sm-10">
  
  
  
    <div class="col-sm-10">
    <div class="checkbox">
    <label>
      <input type="checkbox" id="ldap_auth_key" <?php echo $lk_status; ?>> <?php echo lang('CONF_true'); ?>
      <p class="help-block"><small><?php echo lang('EXT_perf_must_ldap'); ?> </small></p>
    </label>
  </div>
    </div>
  </div>
    </div>
    
    
    <hr>
    
      <div class="form-group" id="fio_user_grp">
    <label for="fio" class="col-sm-2 control-label"><?php echo lang('USERS_fio'); ?></label>
    <div class="col-sm-10">
    <input autocomplete="off" id="fio_user" name="fio_user" type="" class="form-control input-sm" placeholder="<?php echo lang('USERS_fio_full'); ?>" value="<?php echo $fio ?>">
    </div>
  </div>
    
    
    
    
    
    
    
    <div class="form-group">
    <label for="mail" class="col-sm-2 control-label"><?php echo lang('USERS_mail'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="mail" type="text" class="form-control input-sm" id="mail" placeholder="<?php echo lang('USERS_mail'); ?>" value="<?php echo $email; ?>">
        </div>
  </div>
  
  
      <div class="form-group">
    <label for="push" class="col-sm-2 control-label">Push</label>
        <div class="col-sm-10">
    <input autocomplete="off" name="push" type="text" class="form-control input-sm" id="push" placeholder="push" value="<?php echo $push; ?>">
        </div>
  </div>
  
  
    <div class="form-group">
    <label for="tel" class="col-sm-2 control-label"><?php echo lang('APPROVE_tel'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="tel" type="text" class="form-control input-sm" id="tel" placeholder="<?php echo lang('APPROVE_tel'); ?>" value="<?php echo $tel; ?>">
        </div>
  </div>
  
      <div class="form-group">
    <label for="skype" class="col-sm-2 control-label">Skype</label>
        <div class="col-sm-10">
    <input autocomplete="off" name="skype" type="text" class="form-control input-sm" id="skype" placeholder="skype" value="<?php echo $skype; ?>">
        </div>
  </div>
  
      <div class="form-group">
    <label for="adr" class="col-sm-2 control-label"><?php echo lang('APPROVE_adr'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="adr" type="text" class="form-control input-sm" id="adr" placeholder="<?php echo lang('APPROVE_adr'); ?>" value="<?php echo $adr; ?>">
        </div>
  </div>
  
  
   <div class="control-group">
    <div class="controls">
        <div class="form-group">
            <label for="posada" class="col-sm-2 control-label"><?php echo lang('WORKER_posada'); ?>: </label>
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
            <label for="pidrozdil" class="col-sm-2 control-label"><?php echo lang('WORKER_unit'); ?>: </label>
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
  
  
  <hr>
  
  <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?php echo lang('USERS_profile_priv'); ?></label>
  <div class="col-sm-10">
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios3" value="2" <?php echo $status_superadmin ?>>
    <strong class="text-warning"><?php echo lang('USERS_nach1'); ?></strong>
    <p class="help-block"><small><?php echo lang('USERS_nach1_desc'); ?></small></p>
  </label>
</div>

<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios1" value="0" <?php echo $status_admin ?>>
    <strong class="text-success"><?php echo lang('USERS_nach'); ?></strong>
    <p class="help-block"><small><?php echo lang('USERS_nach_desc'); ?></small></p>
  </label>
</div>
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios2" value="1" <?php echo $status_user ?>>
    <strong class="text-info"><?php echo lang('USERS_wo'); ?></strong>
    <p class="help-block"><small><?php echo lang('USERS_wo_desc'); ?></small></p>
  </label>
  
</div>

<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios" id="optionsRadios4" value="4" <?php echo $status_client ?>>
    <strong class="text-default"><?php echo lang('EXT_client'); ?></strong>
    <p class="help-block"><small><?php echo lang('EXT_client_what'); ?></small></p>
  </label>
  
</div>
  </div>
  </div>
  
  
    <div class="form-group">
  <label for="my-select" class="col-sm-2 control-label"><?php echo lang('USERS_units'); ?></label>
  <div class="col-sm-10">
  <select multiple="multiple" id="my-select" name="unit[]">
<?php
        $u = explode(",", $unit);
        
        /* $qstring = "SELECT name as label, id as value FROM deps where id !='0' ;";
                        $result = mysql_query($qstring);
                        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){*/
        
        $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n');
        $stmt->execute(array(':n' => '0'));
        $res1 = $stmt->fetchAll();
        
        foreach ($res1 as $row) {
            
            //echo($row['label']);
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
  
  
  
  <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?php echo lang('USERS_privs'); ?></label>
  <div class="col-sm-10">
  
  
  
    <div class="col-sm-6">
    <div class="checkbox">
    <label>
      <input type="checkbox" id="priv_add_client" <?php echo $priv_add_client ?>> <?php echo lang('TICKET_p_add_client'); ?>
    </label>
  </div>
    </div>
    
        <div class="col-sm-6">
    <div class="checkbox">
    <label>
      <input type="checkbox" id="priv_edit_client" <?php echo $priv_edit_client ?>> <?php echo lang('TICKET_p_edit_client'); ?>
    </label>
  </div>
    </div>
    
  </div>
    </div>
      <div class="form-group">
    <label for="lang" class="col-sm-2 control-label"><?php echo lang('SYSTEM_lang'); ?></label>
        <div class="col-sm-10">
    <select data-placeholder="<?php echo lang('SYSTEM_lang'); ?>" class="chosen-select form-control input-sm" id="lang" name="lang">
                    <option value="0"></option>
                    
                        <option value="en" <?php echo $status_lang_en; ?>>English</option>
                        <option value="ru" <?php echo $status_lang_ru; ?>>Русский</option>
                        <option value="ua" <?php echo $status_lang_ua; ?>>Українська</option>
</select>
        </div>
  </div>
  
  
  

  
  <div class="form-group">
    <label for="msg_title" class="col-sm-2 control-label"><?php echo lang('EXT_perf_msg_t'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="msg_title" type="text" class="form-control input-sm" id="msg_title" placeholder="<?php echo lang('EXT_perf_msg_t'); ?>" value="<?php echo $msg_t; ?>">
        </div>
  </div>
  

      <div class="form-group">
    <label for="mess" class="col-sm-2 control-label"><?php echo lang('MAIL_msg'); ?></label>
        <div class="col-sm-10">
        <textarea placeholder="<?php echo lang(''); ?>" class="form-control input-sm animated" name="mess" id="mess" rows="3"><?php echo $messages; ?>
        </textarea>
        

        </div>
  </div>



  <div class="form-group">
  <label for="mess" class="col-sm-2 control-label"><?=lang('CONF_messages_type');?></label>
  <div class="col-sm-10">
    <div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_1" value="0" <?php echo $msg_type_0; ?>>
    <strong class="text-info">Info</strong>
  </label>
</div>
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_0" value="1" <?php echo $msg_type_1; ?>>
    <strong class="text-warning">Warning</strong>
  </label>
</div>

<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_2" value="2" <?php echo $msg_type_2; ?>>
    <strong class="text-danger">Danger</strong>
  </label>
  
</div>

  </div>
  </div>




  
    
  <div class="form-group">
    <label for="lock" class="col-sm-2 control-label"><?php echo lang('USERS_acc'); ?></label>
        <div class="col-sm-10">
    
    <select class="form-control input-sm" name="lock" id="lock">
  <option <?php echo $status_lock ?> value="0"><?php echo lang('USERS_not_active'); ?></option>
  <option <?php echo $status_unlock ?> value="1"><?php echo lang('USERS_active'); ?></option>
    </select>
    
        </div>
  </div>
    

<div class=""><hr></div>
<div class="">
<center>
    <button type="submit" id="edit_user" value="<?php echo $usid; ?>" class="btn btn-success"><?php echo lang('USERS_make_edit_user'); ?></button>
</center>
</div>
</form>
  

  

  </div>
</div>










<?php
    }
}
?>
