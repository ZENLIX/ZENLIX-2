<?php
session_start();
include ("../functions.inc.php");
include_once ("library/SimpleImage.php");
if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    //if (validate_admin($_SESSION['helpdesk_user_id'])) {
    include ("head.inc.php");
    include ("client.navbar.inc.php");
    
    
    if ($_FILES["file"]) {
        $output_dir = "upload_files/avatars/";
        $allowedExts = array("jpg", "jpeg", "gif", "png", "bmp");
        $extension = end(explode(".", $_FILES["file"]["name"]));
        $fhash = randomhash();
        $fileName = $_FILES["file"]["name"];
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileName_norm = $fhash . "." . $ext;
        
        //echo $_FILES["file"]["size"];
        
        if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] < 2000000) && in_array($extension, $allowedExts)) {
            
            if ($_FILES["file"]["error"] > 0) {
                
                //echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
                
                
            } else {
                
                move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir . $fileName_norm);
                $nf = $output_dir . $fileName_norm;
                
                $image = new abeautifulsite\SimpleImage($nf);
                $image->adaptive_resize(250, 250)->save($nf);
                
                $u = $_SESSION['helpdesk_user_id'];
                $stmt = $dbConnection->prepare('update users set usr_img = :uimg where id=:uid ');
                $stmt->execute(array(':uimg' => $fileName_norm, ':uid' => $u));
                
                //}
                
                //$_FILES["file"]["name"];
                
                
            }
        } else {
            
            //echo $_FILES["file"]["type"]."<br />";
            //echo "Invalid file";
            
            
        }
    }
?>

<section class="content-header">
                    <h1>
                        <i class="fa fa-user"></i> <?php echo lang('NAVBAR_profile'); ?>
                        <small><?php echo lang('NAVBAR_profile_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('NAVBAR_profile'); ?></li>
                    </ol>
                </section>



<input type="hidden" id="main_last_new_ticket" value="<?php echo get_last_ticket_new($_SESSION['helpdesk_user_id']); ?>">
<?php
    $usid = $_SESSION['helpdesk_user_id'];
    
    //$query = "SELECT fio, pass, login, status, priv, unit,email, lang from users where id='$usid'; ";
    //    $sql = mysql_query($query) or die(mysql_error());
    
    $stmt = $dbConnection->prepare('SELECT fio, pass, login, status, priv, unit,email, lang, tel, skype, adr from users where id=:usid');
    $stmt->execute(array(':usid' => $usid));
    $res1 = $stmt->fetchAll();
    
    //if (mysql_num_rows($sql) == 1) {
    //$row = mysql_fetch_assoc($sql);
    foreach ($res1 as $row) {
        
        $fio = $row['fio'];
        $login = $row['login'];
        $pass = $row['pass'];
        $email = $row['email'];
        $tel = $row['tel'];
        $skype = $row['skype'];
        $adr = $row['adr'];
        $langu = $row['lang'];
        
        if ($langu == "en") {
            $status_lang_en = "selected";
        } else if ($langu == "ru") {
            $status_lang_ru = "selected";
        } else if ($langu == "ua") {
            $status_lang_ua = "selected";
        }
    }
?>





<section class="content">



<div class="row">


<div class="col-md-3">

<div class="row">
  <div class="col-md-12">
                            <div class="box box-warning" >
                                <div class="box-header" >
                                
                                    <h4 style="text-align:center;"><?php echo $fio; ?><br><small><?php echo get_user_val('posada'); ?></small></h4>

                                </div>
                                <div class="box-body">
                                  
                        <center>
                            <img src="<?php echo get_user_img(); ?>" class="img-rounded" alt="User Image">
                        </center><br>
                        
                        
                                <form action="<?php echo $CONF['hostname'] ?>profile" method="post" id="form_avatar" enctype="multipart/form-data"> 
             
             <span class="file-input btn btn-block btn-default btn-file" style="width:100%">
                <?php echo lang('PROFILE_select_image'); ?> <input id="file_avatar" type="file" name="file">
            </span>
            <button id="del_profile_img" class="btn btn-block bg-maroon"><?php echo lang('PROFILE_del_image'); ?></button>



        </form>
        
        
       
        
                           
                                    
                                    
                                </div><!-- /.box-body -->
                            </div>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                          
  </div>
  
      
      
</div>


</div>

<div class="col-md-9">


<div class="row">

<div class="col-md-12">
                            <div class="box box-solid">
                                <div class="box-header">
                                    <h3 class="box-title"><i class="fa fa-user"></i> <?php echo lang('P_main'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    
                                    
     
      <div class="panel-body">
      


      
      <form class="form-horizontal" role="form">
      
          <div class="form-group">
    <label for="fio" class="col-sm-4 control-label"><small><?php echo lang('WORKER_fio'); ?></small></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="fio" type="text" class="form-control input-sm" id="fio" placeholder="fio" value="<?php echo $fio; ?>">
        </div>
  </div>
  
  
  
  
    <div class="form-group">
    <label for="mail" class="col-sm-4 control-label"><small><?php echo lang('P_mail'); ?></small></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="mail" type="text" class="form-control input-sm" id="mail" placeholder="<?php echo lang('P_mail'); ?>" value="<?php echo $email; ?>">
    <p class="help-block"><small><?php echo lang('P_mail_desc'); ?></small></p>
        </div>
  </div>
  
      <div class="form-group">
    <label for="tel" class="col-sm-4 control-label"><small><?php echo lang('WORKER_tel_full'); ?></small></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="tel" type="text" class="form-control input-sm" id="tel" placeholder="<?php echo lang('WORKER_tel_full'); ?>" value="<?php echo $tel; ?>">
    
        </div>
  </div>
  
        <div class="form-group">
    <label for="skype" class="col-sm-4 control-label"><small>Skype</small></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="skype" type="text" class="form-control input-sm" id="skype" placeholder="skype" value="<?php echo $skype; ?>">
    
        </div>
  </div>
  
            <div class="form-group">
    <label for="adr" class="col-sm-4 control-label"><small><?php echo lang('APPROVE_adr'); ?></small></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="adr" type="text" class="form-control input-sm" id="adr" placeholder="adr" value="<?php echo $adr; ?>">
    
        </div>
  </div>
  
  
  
          <div class="form-group">
    <label for="lang" class="col-sm-4 control-label"><small><?php echo lang('SYSTEM_lang'); ?></small></label>
        <div class="col-sm-8">
    <select data-placeholder="<?php echo lang('SYSTEM_lang'); ?>" class="chosen-select form-control input-sm" id="lang" name="lang">
                    <option value="0"></option>
                    
                        <option <?php echo $status_lang_en; ?> value="en">English</option>
                        <option <?php echo $status_lang_ru; ?> value="ru">Русский</option>
                        <option <?php echo $status_lang_ua; ?> value="ua">Українська</option>
</select>
        </div>
  </div>
  
  
    <div class="col-md-offset-3 col-md-6">
<center>
    <button type="submit" id="edit_profile_main_client" value="<?php echo $usid ?>" class="btn btn-success"><i class="fa fa-pencil"></i> <?php echo lang('P_edit'); ?></button>
</center>
</div>
      </form>
      
      
      
      
      
      </div>
      
      <div id="m_info"></div>
                                </div><!-- /.box-body -->
                            </div>
                            
                            
                            
                            
                            
                            
                          
</div>


<?php
        $stmt = $dbConnection->prepare('SELECT * FROM user_fields where status=:n and for_client=1');
        $stmt->execute(array(':n' => '1'));
        $res1 = $stmt->fetchAll();

        if (!empty($res1)) 
        {

?>


<div class="col-md-12">
<div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-bookmark-o"></i> <?=lang('FIELD_add_title');?></h3>

</div>
      <div class="box-body">
      <div class="panel-body">
      
 <!--######### ADDITIONAL FIELDS ############## -->

<form id="add_field_form" class="form-horizontal" role="form">
    <div >
<?php

        foreach ($res1 as $row) {


?>

                      <div class="" id="">
    <div class="">
        <div class="form-group">
            <label for="<?=$row['hash'];?>" class="col-sm-4 control-label"><small><?=$row['name'];?>: </small></label>

            <div class="col-sm-8" style=" padding-top: 5px; ">

<?php 
//echo get_user_add_field_val(get_user_val_by_hash($usid, 'id'), $row['id']);
if ($row['t_type'] == "text") {
    $v=get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);
    //if ($row['value'] == "0") {$v="";}
?>
<input type="text" class="form-control input-sm" name="<?=$row['hash'];?>" id="<?=$row['hash'];?>" placeholder="<?=$row['placeholder'];?>" value='<?=$v;?>'>
<?php } ?>


<?php 
if ($row['t_type'] == "textarea") {
$v=get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);
?>
<textarea rows="3" class="form-control input-sm animated" name="<?=$row['hash'];?>" id="<?=$row['hash'];?>" placeholder="<?=$row['placeholder'];?>"><?=$v;?></textarea>
<?php } ?>


<?php 
if ($row['t_type'] == "select") {
$vs=get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);


?>
<select data-placeholder="<?=$row['placeholder'];?>" class="chosen-select form-control" id="<?=$row['hash'];?>" name="<?=$row['hash'];?>">

<?php 
$v=explode(",", $row['value']);
$vs=explode(",", $vs);
 foreach ($v as $value) {
     # code...
 $sc="";
 if (in_array($value, $vs)) {$sc="selected";}
?>
                            <option value="<?=$value;?>" <?=$sc;?>><?=$value;?></option>

                            <?php
                        }
                            ?>
                
                        
            </select>
<?php } ?>

<?php 
if ($row['t_type'] == "multiselect") {
    $vs=get_user_add_field_val($_SESSION['helpdesk_user_id'], $row['id']);

?>





<select data-placeholder="<?=$row['placeholder'];?>" class="multi_field" id="<?=$row['hash'];?>" name="<?=$row['hash'];?>[]" multiple="multiple" >

<?php 
$v=explode(",", $row['value']);
$vs=explode(",", $vs);
 foreach ($v as $value) {
     # code...
     $sc="";
 if (in_array($value, $vs)) {$sc="selected";}
 
?>
                            <option value="<?=$value;?>" <?=$sc;?>><?=$value;?></option>

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
    <div class="col-md-offset-3 col-md-6">
<center>
    <button type="submit" id="edit_profile_ad_f" value="<?php echo $usid ?>" class="btn btn-success"><i class="fa fa-pencil"></i> <?php echo lang('P_edit'); ?></button>
</center>
</div>
</div><div id="ad_f_res"></div>

      </div>
      </div>
      </div>
<?php 

}
?>




<div class="col-md-12">
  <?php
    $ul = get_userlogin_byid($_SESSION['helpdesk_user_id']);
    if (get_user_authtype($login) == false) {
?>

                       <div class="box box-solid">
                                <div class="box-header">
                                    <h3 class="box-title"><i class="fa fa-key"></i> <?php echo lang('P_passedit'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                <div class="panel-body">
      <form class="form-horizontal" role="form">
      
              <div class="form-group">
    <label for="old_pass" class="col-sm-4 control-label"><small><?php echo lang('P_pass_old'); ?></small></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="old_pass" type="password" class="form-control input-sm" id="old_pass" placeholder="<?php echo lang('P_pass_old2'); ?>">
        </div>
  </div>
      
      
        <div class="form-group">
    <label for="new_pass" class="col-sm-4 control-label"><small><?php echo lang('P_pass_new'); ?></small></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="new_pass" type="password" class="form-control input-sm" id="new_pass" placeholder="<?php echo lang('P_pass_new2'); ?>">
        </div>
  </div>
  
          <div class="form-group">
    <label for="new_pass2" class="col-sm-4 control-label"><small><?php echo lang('P_pass_new_re'); ?></small></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="new_pass2" type="password" class="form-control input-sm" id="new_pass2" placeholder="<?php echo lang('P_pass_new_re2'); ?>">
        </div>
  </div>
  <div class="col-md-offset-3 col-md-6">
<center>
    <button type="submit" id="edit_profile_pass" value="<?php echo $usid ?>" class="btn btn-success"><i class="fa fa-pencil"></i> <?php echo lang('P_do_edit_pass'); ?></button>
</center>
</div>
  
  
      </form>
  
      </div>
      <div id="p_info"></div>
                                </div>
                       </div>
                                
                     

<?php
    } ?>
</div>
</div>


</div>


</div>


                    
                    
                    
                    
                    
                    
                    
                    
                    </div>





<?php
    include ("footer.inc.php");
?>

<?php
    
    //}
    
} else {
    include 'auth.php';
}
?>
