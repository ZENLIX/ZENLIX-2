<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    //if (validate_admin($_SESSION['helpdesk_user_id'])) {
    include ("head.inc.php");
    include ("navbar.inc.php");
    
    class SimpleImage
    {
        
        var $image;
        var $image_type;
        
        function load($filename) {
            $image_info = getimagesize($filename);
            $this->image_type = $image_info[2];
            if ($this->image_type == IMAGETYPE_JPEG) {
                $this->image = imagecreatefromjpeg($filename);
            } elseif ($this->image_type == IMAGETYPE_GIF) {
                $this->image = imagecreatefromgif($filename);
            } elseif ($this->image_type == IMAGETYPE_PNG) {
                $this->image = imagecreatefrompng($filename);
            }
        }
        function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 70, $permissions = null) {
            if ($image_type == IMAGETYPE_JPEG) {
                imagejpeg($this->image, $filename, $compression);
            } elseif ($image_type == IMAGETYPE_GIF) {
                imagegif($this->image, $filename);
            } elseif ($image_type == IMAGETYPE_PNG) {
                imagepng($this->image, $filename);
            }
            if ($permissions != null) {
                chmod($filename, $permissions);
            }
        }
        function output($image_type = IMAGETYPE_JPEG) {
            if ($image_type == IMAGETYPE_JPEG) {
                imagejpeg($this->image);
            } elseif ($image_type == IMAGETYPE_GIF) {
                imagegif($this->image);
            } elseif ($image_type == IMAGETYPE_PNG) {
                imagepng($this->image);
            }
        }
        function getWidth() {
            return imagesx($this->image);
        }
        function getHeight() {
            return imagesy($this->image);
        }
        function resizeToHeight($height) {
            $ratio = $height / $this->getHeight();
            $width = $this->getWidth() * $ratio;
            $this->resize($width, $height);
        }
        function resizeToWidth($width) {
            $ratio = $width / $this->getWidth();
            $height = $this->getheight() * $ratio;
            $this->resize($width, $height);
        }
        function scale($scale) {
            $width = $this->getWidth() * $scale / 100;
            $height = $this->getheight() * $scale / 100;
            $this->resize($width, $height);
        }
        function resize($width, $height) {
            $new_image = imagecreatetruecolor($width, $height);
            imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
            $this->image = $new_image;
        }
    }
    
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
                
                $image = new SimpleImage();
                $image->load($nf);
                $image->resizeToHeight(200);
                $image->save($nf);
                
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
    
    $stmt = $dbConnection->prepare('SELECT fio, pass, login, status, priv, unit,email, lang, tel, skype, adr, unit_desc, posada from users where id=:usid');
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
        
        $unitss = $row['unit_desc'];
        $posada = $row['posada'];
        
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
  <div class="col-md-12">      
  
  
  
  <div class="box box-info">
                                
                                <div class="box-body">
                                    
                                    <strong ><small><?php echo lang('PROFILE_priv'); ?>:</small></strong><br>
                  <small><?php echo priv_status_name($usid); ?></small>
                                    <hr>
                                    <strong><small><?php echo lang('PROFILE_priv_unit'); ?>:</small></strong>
                                    <p><small><?php echo view_array(get_unit_name_return(unit_of_user($_SESSION['helpdesk_user_id']))); ?></small></p>
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
      

  
      <div class="form-group" id="fio_user_grp">
    <label for="fio" class="col-sm-4 control-label"><small><?php echo lang('WORKER_fio'); ?></small></label>
        <div class="col-sm-8">
    <input autocomplete="off" name="fio" type="text" class="form-control input-sm" id="fio" placeholder="<?php echo lang('WORKER_fio'); ?>" value="<?php echo $fio; ?>">
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
    <input autocomplete="off" name="adr" type="text" class="form-control input-sm" id="adr" placeholder="<?php echo lang('APPROVE_adr'); ?>" value="<?php echo $adr; ?>">
    
        </div>
  </div>
  
  
  
  
     <div class="control-group">
    <div class="controls">
        <div class="form-group">
            <label for="posada" class="col-sm-4 control-label"><small><?php echo lang('WORKER_posada'); ?>: </small></label>
            <div class="col-sm-8" style="">
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
            <label for="pidrozdil" class="col-sm-4 control-label"><small><?php echo lang('WORKER_unit'); ?>: </small></label>
            <div class="col-sm-8" style="">
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
    <button type="submit" id="edit_profile_main" value="<?php echo $usid ?>" class="btn btn-success"><i class="fa fa-pencil"></i> <?php echo lang('P_edit'); ?></button>
</center>
</div>
      </form>
      
      
      
      
      
      </div>
      
      <div id="m_info"></div>
                                </div><!-- /.box-body -->
                            </div>
                            
                            
                            
                            
                            
                            
                          
</div>
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
