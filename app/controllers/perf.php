<?php
//session_start();
include_once ("functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        
        $CONF['title_header'] = lang('CONF_title') . " - " . $CONF['name_of_firm'];
        
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        //print_r(generate_timezone_list());
        
        class SimpleImage
        {
            
            var $image;
            var $image_type;
            
            function load($filename) {
                $image_info = getimagesize($filename);
                $this->image_type = $image_info[2];
                if ($this->image_type == IMAGETYPE_JPEG) {
                    $this->image = imagecreatefromjpeg($filename);
                } 
                elseif ($this->image_type == IMAGETYPE_GIF) {
                    $this->image = imagecreatefromgif($filename);
                } 
                elseif ($this->image_type == IMAGETYPE_PNG) {
                    $this->image = imagecreatefrompng($filename);
                }
            }
            function save($filename, $image_type = IMAGETYPE_PNG, $compression = 100, $permissions = null) {
                if ($image_type == IMAGETYPE_JPEG) {
                    imagejpeg($this->image, $filename, $compression);
                } 
                elseif ($image_type == IMAGETYPE_GIF) {
                    imagegif($this->image, $filename);
                } 
                elseif ($image_type == IMAGETYPE_PNG) {
                    imagepng($this->image, $filename);
                }
                if ($permissions != null) {
                    chmod($filename, $permissions);
                }
            }
            function output($image_type = IMAGETYPE_JPEG) {
                if ($image_type == IMAGETYPE_JPEG) {
                    imagejpeg($this->image);
                } 
                elseif ($image_type == IMAGETYPE_GIF) {
                    imagegif($this->image);
                } 
                elseif ($image_type == IMAGETYPE_PNG) {
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
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
                imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth() , $this->getHeight());
                $this->image = $new_image;
            }
        }
        
        if (isset($_FILES["file"])) {
            $output_dir = "upload_files/avatars/";
            $allowedExts = array(
                "jpg",
                "jpeg",
                "gif",
                "png",
                "bmp"
            );
            $extension = end(explode(".", $_FILES["file"]["name"]));
            $fhash = randomhash();
            $fileName = $_FILES["file"]["name"];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileName_norm = $fhash . "." . $ext;
            $fileName_norm_logo = $fhash . "_logo." . $ext;
            
            //echo $_FILES["file"]["size"];
            
            if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] < 2000000) && in_array($extension, $allowedExts)) {
                
                if ($_FILES["file"]["error"] > 0) {
                    
                    //echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
                    
                    
                } 
                else {
                    
                    move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir . $fileName_norm);
                    $nf = $output_dir . $fileName_norm;
                    $nf_logo = $output_dir . $fileName_norm_logo;
                    $image = new SimpleImage();
                    $image->load($nf);
                    $image->resizeToHeight(128);
                    $image->save($nf);
                    
                    $image_logo = new SimpleImage();
                    $image_logo->load($nf);
                    $image_logo->resizeToHeight(40);
                    $image_logo->save($nf_logo);
                    
                    //$u = $_SESSION['helpdesk_user_id'];
                    //$stmt = $dbConnection->prepare('update users set usr_img = :uimg where id=:uid ');
                    //$stmt->execute(array(':uimg' => $fileName_norm, ':uid' => $u));
                    update_val_by_key("logo_img", $fileName_norm);
                    
                    //}
                    
                    //$_FILES["file"]["name"];
                    
                    
                }
            } 
            else {
                
                //echo $_FILES["file"]["type"]."<br />";
                //echo "Invalid file";
                
                
            }
        }
        
        $langu = get_conf_param('lang_def');
        
        if ($langu == "en") {
            $status_lang_en = "selected";
        } 
        else if ($langu == "ru") {
            $status_lang_ru = "selected";
        } 
        else if ($langu == "ua") {
            $status_lang_ua = "selected";
        }
        
        if (isset($_GET['ti_conf'])) {
            $menu_opt = "ti_conf";
            $menu_active['tickets'] = "active";
        } 
        else if (isset($_GET['notify'])) {
            $menu_opt = "notify";
            $menu_active['notify'] = "active";
        } 
        else if (isset($_GET['inform'])) {
            $menu_opt = "inform";
            $menu_active['inform'] = "active";
        } 
        else if (isset($_GET['subj'])) {
            $menu_opt = "subj";
            $menu_active['subj'] = "active";
        } 
        else if (isset($_GET['slaplans'])) {
            $menu_opt = "sla";
            $menu_active['sla'] = "active";
        } 
        else {
            $menu_opt = "main";
            $menu_active['main'] = "active";
        }
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-cog"></i>  <?php
        echo lang('CONF_title'); ?>
                        <small><?php
        echo lang('CONF_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php
        echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php
        echo $CONF['name_of_firm'] ?></a></li>
                    
                    <li class="active"><?php
        echo lang('CONF_title'); ?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">


<div class="col-md-3">


<div class="list-group">
  <a href="config" class="list-group-item <?php echo $menu_active['main']; ?>">
    <?php echo lang('PERF_menu_main_conf'); ?>
  </a>
  <a href="config?ti_conf" class="list-group-item <?php echo $menu_active['tickets']; ?>"><?php echo lang('PERF_menu_ticket_conf'); ?></a>
    <a href="config?slaplans" class="list-group-item <?php echo $menu_active['sla']; ?>"><?php echo lang('PERF_menu_sla'); ?></a>
    <a href="config?subj" class="list-group-item <?php echo $menu_active['subj']; ?>"><?php
        echo lang('NAVBAR_subjs'); ?></a>
  <a href="config?notify" class="list-group-item <?php echo $menu_active['notify']; ?>"><?php echo lang('PERF_menu_notify_conf'); ?></a>
  <a href="config?inform" class="list-group-item <?php echo $menu_active['inform']; ?>"><?php echo lang('PERF_menu_info_conf'); ?></a>
  
</div>











<div class="box box-solid bg-blue">
                                <div class="box-header">
                                    <h3 class="box-title">ZENLIX v.<?php
        echo get_conf_param('version'); ?></h3>
                                </div>
                                <div class="box-body">
                                Coding by ZENLIX (c) 2015
                                   <p>
      <i class="fa fa-envelope"></i> support@zenlix.com
      </p>
      <hr>
      <button id="check_update" class="btn btn-default btn-block btn-sm">Check updates</button>
      <div id="result_update"></div>
                                </div><!-- /.box-body -->
                            </div>


<div class="callout">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php
        echo lang('CONF_info'); ?>
       </small>
                                    </div>

                                    
                                    
                                    
                                    
                                    
</div>

<?php
        if ($menu_opt == "subj") { ?>


<style type="text/css">



        

        pre, code {
            font-size: 12px;
        }

        pre {
            width: 100%;
            overflow: auto;
        }

        small {
            font-size: 90%;
        }

        small code {
            font-size: 11px;
        }

        .placeholder {
            outline: 1px dashed #4183C4;
            /*-webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            margin: -1px;*/
            height: 20px;
        }

        .mjs-nestedSortable-error {
            background: #fbe3e4;
            border-color: transparent;
        }

        ul {
            margin: 0;
            padding: 0;
            padding-left: 30px;
        }

        ul.sortable, ul.sortable ul {
            margin: 0 0 0 25px;
            padding: 0;
            list-style-type: none;
        }

        ul.sortable {
            margin: 4em 0;
        }

        .sortable li {
            margin: 5px 0 0 0;
            padding: 0;
        }

        .sortable li div  {
            /*
            border: 1px solid #d4d4d4;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            border-color: #D4D4D4 #D4D4D4 #BCBCBC;
            padding: 6px;
            margin: 0;
            cursor: move;
            background: #f6f6f6;
            background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #ededed 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(47%,#f6f6f6), color-stop(100%,#ededed));
            background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ededed',GradientType=0 );
            */
        }

        .sortable li.mjs-nestedSortable-branch div {
           /* background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #f0ece9 100%);
            background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#f0ece9 100%);
            */
            list-style-type: none;

        }

        .sortable li.mjs-nestedSortable-leaf div {


        }

        li.mjs-nestedSortable-collapsed.mjs-nestedSortable-hovering div {
            border-color: #999;
            background: #fafafa;
        }

        .disclose {
            cursor: pointer;
            width: 10px;
            display: none;
        }

        .sortable li.mjs-nestedSortable-collapsed > ul {
            display: none;
        }

        .sortable li.mjs-nestedSortable-branch > div > .disclose {
            display: inline-block;
        }

        .sortable li.mjs-nestedSortable-collapsed > div > .disclose > span:before {
            content: '+ ';
        }

        .sortable li.mjs-nestedSortable-expanded > div > .disclose > span:before {
            content: '- ';
        }

        

        p, ol, ul, pre, form {
            margin-top: 0;
            margin-bottom: 1em;
        }

        dl {
            margin: 0;
        }

        dd {
            margin: 0;
            padding: 0 0 0 1.5em;
        }

        code {
            background: #e5e5e5;
        }

        input {
            vertical-align: text-bottom;
        }

        .notice {
            color: #c33;
        }

    </style>
<div class="col-md-9">

<div class="row">
<div class="col-md-12">
<div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-tags"></i> <?php
            echo lang('SUBJ_title'); ?>
<small></small></h3>
</div>
      <div class="box-body">

<div class="" id="content_subj">
      

<?php echo showMenu_sla(); ?>



      <br>
      </div>
      <br>
<button id="subj_add" class="btn btn-default btn-sm btn-block" type="submit"><?php
            echo lang('SUBJ_add'); ?></button>
      </div>

      </div>
      </div>
      </div>
      </div>




<?php
        } 
        else if ($menu_opt == "sla") {
            
            if ($_GET['item']) {
                
                $m = get_sla_plans_times($_GET['item']);
                
                $stmt = $dbConnection->prepare('SELECT * from sla_plans where uniq_id=:uid');
                $stmt->execute(array(
                    ':uid' => $_GET['item']
                ));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<div class="col-md-9">

<div class="row">
<div class="col-md-12">
<div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-bolt"></i> <?php echo $row['name']; ?> 
<small><?php echo lang('PERF_menu_sla'); ?> </small></h3>
</div>
      <div class="box-body">
      <form class="form-horizontal" role="form">



<div class="box box-info">
                                <div class="box-header">
                                    <h3 class="box-title"><?php echo lang('SLA_perf_reaction'); ?></h3>
                                </div>
                                <div class="box-body">
                                    <!-- Color Picker -->




  <div class="form-group">
    <label for="email_gate_mailbox" class="col-sm-4 control-label"><small><?php echo lang('SLA_perf_1'); ?></small></label>
    <div class="col-sm-8">


<div class="input-group col-sm-12">
                                        <input id="react_low_1" type="text" class="form-control input-sm" value="<?php echo $m['react_low_1']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_DAY'); ?></small></span>
                                        
                                        <input id="react_low_2" type="text" class="form-control input-sm" value="<?php echo $m['react_low_2']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_HOUR'); ?></small></span>

                                        <input id="react_low_3" type="text" class="form-control input-sm" value="<?php echo $m['react_low_3']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_MINUTE'); ?></small></span>

                                        <input id="react_low_4" type="text" class="form-control input-sm" value="<?php echo $m['react_low_4']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_SEC'); ?></small></span>
                                    </div>


    </div>
  </div>

    <div class="form-group">
    <label for="email_gate_mailbox" class="col-sm-4 control-label"><small><?php echo lang('SLA_perf_2'); ?></small></label>
    <div class="col-sm-8">
      <div class="input-group col-sm-12">
                                        <input id="react_def_1" type="text" class="form-control input-sm" value="<?php echo $m['react_def_1']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_DAY'); ?></small></span>
                                        
                                        <input id="react_def_2" type="text" class="form-control input-sm" value="<?php echo $m['react_def_2']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_HOUR'); ?></small></span>

                                        <input id="react_def_3" type="text" class="form-control input-sm" value="<?php echo $m['react_def_3']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_MINUTE'); ?></small></span>

                                        <input id="react_def_4" type="text" class="form-control input-sm" value="<?php echo $m['react_def_4']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_SEC'); ?></small></span>
                                    </div>
    </div>
  </div>

    <div class="form-group">
    <label for="email_gate_mailbox" class="col-sm-4 control-label"><small><?php echo lang('SLA_perf_3'); ?></small></label>
    <div class="col-sm-8">
     <div class="input-group col-sm-12">
                                        <input id="react_high_1" type="text" class="form-control input-sm" value="<?php echo $m['react_high_1']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_DAY'); ?></small></span>
                                        
                                        <input id="react_high_2" type="text" class="form-control input-sm" value="<?php echo $m['react_high_2']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_HOUR'); ?></small></span>

                                        <input id="react_high_3" type="text" class="form-control input-sm" value="<?php echo $m['react_high_3']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_MINUTE'); ?></small></span>

                                        <input id="react_high_4" type="text" class="form-control input-sm" value="<?php echo $m['react_high_4']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_SEC'); ?></small></span>
                                    </div>
    </div>
  </div>




                                </div><!-- /.box-body -->
                            </div>




<div class="box box-warning">
                                <div class="box-header">
                                    <h3 class="box-title"><?php echo lang('SLA_perf_work'); ?></h3>
                                </div>
                                 <div class="box-body">
                                    <!-- Color Picker -->




  <div class="form-group">
    <label for="email_gate_mailbox" class="col-sm-4 control-label"><small><?php echo lang('SLA_perf_1'); ?></small></label>
    <div class="col-sm-8">


<div class="input-group col-sm-12">
                                        <input id="work_low_1" type="text" class="form-control input-sm" value="<?php echo $m['work_low_1']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_DAY'); ?></small></span>
                                        
                                        <input id="work_low_2" type="text" class="form-control input-sm" value="<?php echo $m['work_low_2']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_HOUR'); ?></small></span>

                                        <input id="work_low_3" type="text" class="form-control input-sm" value="<?php echo $m['work_low_3']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_MINUTE'); ?></small></span>

                                        <input id="work_low_4" type="text" class="form-control input-sm" value="<?php echo $m['work_low_4']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_SEC'); ?></small></span>
                                    </div>


    </div>
  </div>

    <div class="form-group">
    <label for="email_gate_mailbox" class="col-sm-4 control-label"><small><?php echo lang('SLA_perf_2'); ?></small></label>
    <div class="col-sm-8">
      <div class="input-group col-sm-12">
                                        <input id="work_def_1" type="text" class="form-control input-sm" value="<?php echo $m['work_def_1']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_DAY'); ?></small></span>
                                        
                                        <input id="work_def_2" type="text" class="form-control input-sm" value="<?php echo $m['work_def_2']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_HOUR'); ?></small></span>

                                        <input id="work_def_3" type="text" class="form-control input-sm" value="<?php echo $m['work_def_3']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_MINUTE'); ?></small></span>

                                        <input id="work_def_4" type="text" class="form-control input-sm" value="<?php echo $m['work_def_4']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_SEC'); ?></small></span>
                                    </div>
    </div>
  </div>

    <div class="form-group">
    <label for="email_gate_mailbox" class="col-sm-4 control-label"><small><?php echo lang('SLA_perf_3'); ?></small></label>
    <div class="col-sm-8">
     <div class="input-group col-sm-12">
                                        <input id="work_high_1" type="text" class="form-control input-sm" value="<?php echo $m['work_high_1']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_DAY'); ?></small></span>
                                        
                                        <input id="work_high_2" type="text" class="form-control input-sm" value="<?php echo $m['work_high_2']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_HOUR'); ?></small></span>

                                        <input id="work_high_3" type="text" class="form-control input-sm" value="<?php echo $m['work_high_3']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_MINUTE'); ?></small></span>

                                        <input id="work_high_4" type="text" class="form-control input-sm" value="<?php echo $m['work_high_4']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_SEC'); ?></small></span>
                                    </div>
    </div>
  </div>




                                </div><!-- /.box-body -->
                            </div>








<div class="box box-danger">
                                <div class="box-header">
                                    <h3 class="box-title"><?php echo lang('SLA_perf_deadline'); ?></h3>
                                </div>
                                 <div class="box-body">
                                    <!-- Color Picker -->




  <div class="form-group">
    <label for="email_gate_mailbox" class="col-sm-4 control-label"><small><?php echo lang('SLA_perf_1'); ?></small></label>
    <div class="col-sm-8">


<div class="input-group col-sm-12">
                                        <input id="deadline_low_1" type="text" class="form-control input-sm" value="<?php echo $m['deadline_low_1']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_DAY'); ?></small></span>
                                        
                                        <input id="deadline_low_2" type="text" class="form-control input-sm" value="<?php echo $m['deadline_low_2']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_HOUR'); ?></small></span>

                                        <input id="deadline_low_3" type="text" class="form-control input-sm" value="<?php echo $m['deadline_low_3']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_MINUTE'); ?></small></span>

                                        <input id="deadline_low_4" type="text" class="form-control input-sm" value="<?php echo $m['deadline_low_4']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_SEC'); ?></small></span>
                                    </div>


    </div>
  </div>

    <div class="form-group">
    <label for="email_gate_mailbox" class="col-sm-4 control-label"><small><?php echo lang('SLA_perf_2'); ?></small></label>
    <div class="col-sm-8">
      <div class="input-group col-sm-12">
                                        <input id="deadline_def_1" type="text" class="form-control input-sm" value="<?php echo $m['deadline_def_1']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_DAY'); ?></small></span>
                                        
                                        <input id="deadline_def_2" type="text" class="form-control input-sm" value="<?php echo $m['deadline_def_2']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_HOUR'); ?></small></span>

                                        <input id="deadline_def_3" type="text" class="form-control input-sm" value="<?php echo $m['deadline_def_3']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_MINUTE'); ?></small></span>

                                        <input id="deadline_def_4" type="text" class="form-control input-sm" value="<?php echo $m['deadline_def_4']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_SEC'); ?></small></span>
                                    </div>
    </div>
  </div>

    <div class="form-group">
    <label for="email_gate_mailbox" class="col-sm-4 control-label"><small><?php echo lang('SLA_perf_3'); ?></small></label>
    <div class="col-sm-8">
     <div class="input-group col-sm-12">
                                        <input id="deadline_high_1" type="text" class="form-control input-sm" value="<?php echo $m['deadline_high_1']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_DAY'); ?></small></span>
                                        
                                        <input id="deadline_high_2" type="text" class="form-control input-sm" value="<?php echo $m['deadline_high_2']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_HOUR'); ?></small></span>

                                        <input id="deadline_high_3" type="text" class="form-control input-sm" value="<?php echo $m['deadline_high_3']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_MINUTE'); ?></small></span>

                                        <input id="deadline_high_4" type="text" class="form-control input-sm" value="<?php echo $m['deadline_high_4']; ?>">
                                        <span class="input-group-addon"><small><?php echo lang('MOMENTJS_SEC'); ?></small></span>
                                    </div>
    </div>
  </div>




                                </div><!-- /.box-body -->
                            </div>







      </form>
                      <div class="box-footer clearfix no-border">
                                    <center><button id="save_sla_plan" value="<?php echo $_GET['item']; ?>" class="btn btn-success"><i class="fa fa-check"></i> <?php echo lang('PORTAL_save'); ?></button></center>
                                </div>
                                <div id="plan_res"></div>
      </div>
      </div>
      </div>
      </div>
      </div>
<?php
            } 
            else if (!$_GET['item']) {
                
                $c1 = "";
                if (get_conf_param('sla_system') == "true") {
                    $c1 = "checked";
                }
?>
<style type="text/css">



        

        pre, code {
            font-size: 12px;
        }

        pre {
            width: 100%;
            overflow: auto;
        }

        small {
            font-size: 90%;
        }

        small code {
            font-size: 11px;
        }

        .placeholder {
            outline: 1px dashed #4183C4;
            /*-webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            margin: -1px;*/
            height: 20px;
        }

        .mjs-nestedSortable-error {
            background: #fbe3e4;
            border-color: transparent;
        }

        ul {
            margin: 0;
            padding: 0;
            padding-left: 30px;
        }

        ul.sortable, ul.sortable ul {
            margin: 0 0 0 25px;
            padding: 0;
            list-style-type: none;
        }

        ul.sortable {
            margin: 4em 0;
        }

        .sortable li {
            margin: 5px 0 0 0;
            padding: 0;
        }

        .sortable li div  {
            /*
            border: 1px solid #d4d4d4;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            border-color: #D4D4D4 #D4D4D4 #BCBCBC;
            padding: 6px;
            margin: 0;
            cursor: move;
            background: #f6f6f6;
            background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #ededed 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(47%,#f6f6f6), color-stop(100%,#ededed));
            background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 47%,#ededed 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ededed',GradientType=0 );
            */
        }

        .sortable li.mjs-nestedSortable-branch div {
           /* background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 47%, #f0ece9 100%);
            background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 47%,#f0ece9 100%);
            */
            list-style-type: none;

        }

        .sortable li.mjs-nestedSortable-leaf div {


        }

        li.mjs-nestedSortable-collapsed.mjs-nestedSortable-hovering div {
            border-color: #999;
            background: #fafafa;
        }

        .disclose {
            cursor: pointer;
            width: 10px;
            display: none;
        }

        .sortable li.mjs-nestedSortable-collapsed > ul {
            display: none;
        }

        .sortable li.mjs-nestedSortable-branch > div > .disclose {
            display: inline-block;
        }

        .sortable li.mjs-nestedSortable-collapsed > div > .disclose > span:before {
            content: '+ ';
        }

        .sortable li.mjs-nestedSortable-expanded > div > .disclose > span:before {
            content: '- ';
        }

        

        p, ol, ul, pre, form {
            margin-top: 0;
            margin-bottom: 1em;
        }

        dl {
            margin: 0;
        }

        dd {
            margin: 0;
            padding: 0 0 0 1.5em;
        }

        code {
            background: #e5e5e5;
        }

        input {
            vertical-align: text-bottom;
        }

        .notice {
            color: #c33;
        }

    </style>

<div class="col-md-9">

<div class="row">
<div class="col-md-12">
<div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-bolt"></i> <?php echo lang('PERF_menu_sla'); ?></h3>
</div>
      <div class="box-body">
      <form class="form-horizontal" role="form">

<p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                <label style="padding-right:20px;">
                    <input id="make_sla_active" name="" value="<?php echo $row['id']; ?>" type="checkbox" <?php echo $c1; ?>> <small><?php echo lang('SLA_activate_info'); ?></small>
                </label>
                            </p>

<div id="content_sla_plans">
<?php echo get_sla_view(); ?>
</div>









      </form>
                      <div class="box-footer clearfix no-border">
                                    <button id="add_slaplan_item" class="btn btn-default btn-block btn-sm"><i class="fa fa-plus"></i> <?php echo lang('NOTES_create'); ?></button>
                                </div>
      </div>
      </div>
      </div>
      </div>
      </div>







<?php
            }
        } 
        else if ($menu_opt == "ti_conf") {
?>

<div class="col-md-9">

<div class="row">
<div class="col-md-12">
<div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-tag"></i> <?php echo lang('PERF_menu_ticket_conf'); ?></h3>
</div>
      <div class="box-body">
      <form class="form-horizontal" role="form">


<div class="form-group">
    <label for="days2arch" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_2arch'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="days2arch" placeholder="<?php
            echo lang('CONF_2arch'); ?>" value="<?php
            echo get_conf_param('days2arch'); ?>">
     
    </div>
  </div>
  


        <div class="form-group">
    <label for="fix_subj" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_subj'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="fix_subj">
  <option value="true" <?php
            if (get_conf_param('fix_subj') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_fix_list'); ?></option>


  <option value="true_multiple" <?php
            if (get_conf_param('fix_subj') == "true_multiple") {
                echo "selected";
            } ?>><?php
            echo lang('FIELD_type_multiselect'); ?></option>

  <option value="false" <?php
            if (get_conf_param('fix_subj') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_subj_text'); ?></option>
</select>    
<p class="help-block"><small>
<?php
            echo lang('CONF_subj_info'); ?>
</small></p>
</div>
  </div>
          <div class="form-group">
    <label for="file_uploads" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_fup'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="file_uploads">
  <option value="true" <?php
            if (get_conf_param('file_uploads') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('file_uploads') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>    
<p class="help-block"><small>
<?php
            echo lang('CONF_fup_info'); ?>
</small></p>
</div>
  </div>
  
  
  
  <div class="form-group">
    <label for="file_types" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_file_types'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="file_types" placeholder="gif,jpe?g,png,doc,xls,rtf,pdf,zip,rar,bmp,docx,xlsx" value="<?php
            $bodytag = str_replace("|", ",", get_conf_param('file_types'));
            echo $bodytag;
?>">

    </div>
  </div>
  
    <div class="form-group">
    <label for="file_size" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_file_size'); ?></small></label>
    <div class="col-sm-8">
    <div class="input-group">
      <input type="text" class="form-control input-sm" id="file_size" placeholder="5" value="<?php
            echo round(get_conf_param('file_size') / 1024 / 1024); ?>">
<span class="input-group-addon">Mb</span>
    </div>
    </div>
  </div>
  



   <div class="form-group">
    <label for="ticket_last_time" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_tlt'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="ticket_last_time">
  <option value="true" <?php
            if (get_conf_param('ticket_last_time') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('ticket_last_time') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>    
<p class="help-block"><small>
<?php
            echo lang('CONF_tlt_info'); ?>
</small></p>
</div>
  </div>





  
  
<center>
    <button type="submit" id="conf_edit_ticket" class="btn btn-success"><i class="fa fa-pencil"></i> <?php
            echo lang('JS_save'); ?></button>
    
</center>
      </form>
      </div>
      <div id="conf_edit_ticket_res"></div>
      </div>
      </div>


<div class="col-md-12">
<div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-bookmark-o"></i> <?php echo lang('FIELD_title'); ?></h3>
<div class="box-tools pull-right"><button id="ticket_field_plus" class="btn btn-success btn-xs" type="submit"><?php echo lang('FIELD_new'); ?></button></div>
</div>
      <div class="box-body">
      <form class="form-horizontal" role="form">

<div id="ticket_fields_res">
<?php echo get_ticket_form_view(); ?> 
</div>

<!--center>
    <button type="submit" id="conf_edit_ticket_fields" class="btn btn-success"><i class="fa fa-pencil"></i> </button>
    
</center-->
      </form>
      </div>
      </div>
      </div>


<div class="col-md-12">
<div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-envelope-o"></i> <?php echo lang('CONF_EMAIL_GATE_title'); ?></h3>
</div>
      <div class="box-body">
      <form class="form-horizontal" role="form">
      



<div class="form-group">
    <label for="email_gate_status" class="col-sm-4 control-label"><small><?php echo lang('t_LIST_status'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="email_gate_status">
  <option value="true" <?php
            if (get_conf_param('email_gate_status') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('email_gate_status') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>    

</div>
  </div>



<div class="form-group">
    <label for="email_gate_all" class="col-sm-4 control-label"><small><?php echo lang('CONF_EMAIL_GATE_all'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="email_gate_all">
  <option value="true" <?php
            if (get_conf_param('email_gate_all') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('email_gate_all') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>    
<p class="help-block"><small><?php echo lang('CONF_EMAIL_GATE_all_info'); ?></small></p>
</div>
  </div>




    <div class="form-group" id="for_to" data-toggle="popover" data-html="true" data-trigger="manual" data-placement="right">
        <label for="to" class="col-md-4 control-label" ><small><?php echo lang('CONF_EMAIL_GATE_to'); ?> </small></label>
        <div class="col-md-4">
            <select data-placeholder="<?php
            echo lang('NEW_to_unit'); ?>" class="chosen-select form-control" id="to" name="unit_id">
                
                <?php
            $stmt = $dbConnection->prepare('SELECT name as label, id as value FROM deps where id !=:n AND status=:s');
            $stmt->execute(array(
                ':n' => '0',
                ':s' => '1'
            ));
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
                
                $s = "";
                
                if (get_conf_param('email_gate_unit_id') == $row['value']) {
                    $s = "selected";
                }
                
                //echo($row['label']);
                $row['label'] = $row['label'];
                $row['value'] = (int)$row['value'];
?>

                            <option value="<?php
                echo $row['value'] ?>" <?php echo $s; ?>><?php
                echo $row['label'] ?></option>

                        <?php
            }
?>

            </select>
        </div>




        <div class="col-md-4" style="" id="dsd">
    
    
    <select data-placeholder="<?php
            echo lang('NEW_to_user'); ?>" id="users_do" name="unit_id" class="form-control input-sm" multiple>
        <option></option>


<?php
            
            /* $qstring = "SELECT fio as label, id as value FROM users where status='1' and login !='system' order by fio ASC;";
                $result = mysql_query($qstring);//query the database for entries containing the term
            while ($row = mysql_fetch_array($result,MYSQL_ASSOC)){
            */
            
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
                
                if (get_user_status_text($row['value']) == "online") {
                    $s = "online";
                } 
                else if (get_user_status_text($row['value']) == "offline") {
                    $s = "offline";
                }
                
                $st_sel = "";
                $mass = explode(",", get_conf_param('email_gate_user_id'));
                if (in_array($row['value'], $mass)) {
                    $st_sel = "selected";
                }
?>
                    <option data-foo="<?php
                echo $s; ?>" value="<?php
                echo $row['value'] ?>" <?php echo $st_sel; ?>><?php
                echo nameshort($row['label']) ?> </option>

                <?php
            }
?>
    </select>
            

        </div>

    </div>











  <div class="form-group">
    <label for="email_gate_mailbox" class="col-sm-4 control-label"><small><?php echo lang('CONF_EMAIL_GATE_mailbox'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="email_gate_mailbox" placeholder="<?php echo lang('CONF_EMAIL_GATE_mailbox'); ?>" value="<?php
            echo get_conf_param('email_gate_mailbox') ?>">
        <p class="help-block"><small><?php echo lang('CONF_EMAIL_GATE_mailbox_info'); ?></small></p>
    </div>
  </div>

  <div class="form-group">
    <label for="email_gate_filter" class="col-sm-4 control-label"><small><?php echo lang('CONF_EMAIL_GATE_filter'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="email_gate_filter" placeholder="<?php echo lang('CONF_EMAIL_GATE_filter'); ?>" value="<?php
            echo get_conf_param('email_gate_filter') ?>">
        <p class="help-block"><small><?php echo lang('CONF_EMAIL_GATE_filter_ex'); ?>:   
<pre>
   *    ALL - return all mails matching the rest of the criteria
   *    ANSWERED - match mails with the \\ANSWERED flag set
   *    BCC "string" - match mails with "string" in the Bcc: field
   *    BEFORE "date" - match mails with Date: before "date"
   *    BODY "string" - match mails with "string" in the body of the mail
   *    CC "string" - match mails with "string" in the Cc: field
   *    DELETED - match deleted mails
   *    FLAGGED - match mails with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
   *    FROM "string" - match mails with "string" in the From: field
   *    KEYWORD "string" - match mails with "string" as a keyword
   *    NEW - match new mails
   *    OLD - match old mails
   *    ON "date" - match mails with Date: matching "date"
   *    RECENT - match mails with the \\RECENT flag set
   *    SEEN - match mails that have been read (the \\SEEN flag is set)
   *    SINCE "date" - match mails with Date: after "date"
   *    SUBJECT "string" - match mails with "string" in the Subject:
   *    TEXT "string" - match mails with text "string"
   *    TO "string" - match mails with "string" in the To:
   *    UNANSWERED - match mails that have not been answered
   *    UNDELETED - match mails that are not deleted
   *    UNFLAGGED - match mails that are not flagged
   *    UNKEYWORD "string" - match mails that do not have the keyword "string"
   *    UNSEEN - match mails which have not been read yet
</pre>
   </small></p>
    </div>
  </div>

<div class="form-group">
    <label for="email_gate_host" class="col-sm-4 control-label"><small><?php echo lang('CONF_EMAIL_GATE_host'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="email_gate_host" placeholder="<?php echo lang('CONF_EMAIL_GATE_host'); ?>" value="<?php
            echo get_conf_param('email_gate_host'); ?>">
    </div>
  </div>

<div class="form-group">
    <label for="email_gate_cat" class="col-sm-4 control-label"><small><?php echo lang('CONF_EMAIL_GATE_cat'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="email_gate_cat" placeholder="<?php echo lang('CONF_EMAIL_GATE_cat'); ?>" value="<?php
            echo get_conf_param('email_gate_cat') ?>">
    </div>
  </div>

  <div class="form-group">
    <label for="email_gate_connect_param" class="col-sm-4 control-label"><small><?php echo lang('CONF_EMAIL_GATE_cp'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="email_gate_connect_param" placeholder="<?php echo lang('CONF_EMAIL_GATE_cp'); ?>" value="<?php
            echo get_conf_param('email_gate_connect_param') ?>">
        <p class="help-block"><small>Ex.:   
<pre>
/pop3                     - for POP3 protocol connection
/imap/ssl                 - for IMAP secure connection
/pop3/ssl/novalidate-cert - POP3 with self-signed cert
/ssl                      - use the Secure Socket Layer to encrypt the session
/validate-cert            - validate certificates from TLS/SSL server (this is the default behavior)
/novalidate-cert          - do not validate certificates from TLS/SSL server, needed if server uses self-signed certificates
/tls                      - force use of start-TLS to encrypt the session, and reject connection to servers that do not support it
/nntp                     - NNTP protocol connection
</pre>
</small>
</p>
    </div>
  </div>

    <div class="form-group">
    <label for="email_gate_port" class="col-sm-4 control-label"><small><?php echo lang('CONF_EMAIL_GATE_port'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="email_gate_port" placeholder="<?php echo lang('CONF_EMAIL_GATE_port'); ?>" value="<?php
            echo get_conf_param('email_gate_port') ?>">
    </div>
  </div>
 <div class="form-group">
    <label for="email_gate_login" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail_login'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="email_gate_login" placeholder="<?php
            echo lang('CONF_mail_login'); ?>" value="<?php
            echo get_conf_param('email_gate_login') ?>">
    </div>
  </div>
  
      <div class="form-group">
    <label for="email_gate_pass" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail_pass'); ?></small></label>
    <div class="col-sm-8">
      <input type="password" class="form-control input-sm" id="email_gate_pass" placeholder="<?php
            echo lang('CONF_mail_pass'); ?>" value="<?php
            echo get_conf_param('email_gate_pass') ?>">
    </div>
  </div>


<center>
    <button type="submit" id="conf_edit_email_gate" class="btn btn-success"><i class="fa fa-pencil"></i> <?php
            echo lang('JS_save'); ?></button>
</center>


      <div class="" id="conf_edit_email_gate_res"></div>
      <div class="" id="conf_test_email_gate_res"></div>







      </form>
      </div>
      </div>
      </div>











      </div>
      </div>


<?php
        } 
        else if ($menu_opt == "notify") {
?>



<div class="col-md-9">

<div class="row">

<div class="col-md-12">
  <div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-bell"></i> <?php echo lang('PERF_GM_title'); ?></h3>
</div>
      <div class="box-body">
      <form class="form-horizontal" role="form">
   

    <div class="form-group">
    <label for="gm_active" class="col-sm-4 control-label"><small><?php echo lang('t_LIST_status'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="gm_active">
  <option value="1" <?php
            if (get_conf_param('global_msg_status') == "1") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="0" <?php
            if (get_conf_param('global_msg_status') == "0") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>   </div>
  </div>


<?php
            if (get_conf_param('global_msg_to') == "all") {
                $s_all = "checked=\"checked\"";
            } 
            else {
                $s_list = "checked=\"checked\"";
            }
?>

     <div class="form-group">
    <label for="to_msg" class="col-sm-4 control-label"><small><?php
            echo lang('NEW_to'); ?></small></label>
    <div class="col-sm-4">
    
    <div class="radio">
    <label>
      <input type="radio" name="optionsRadios1" id="optionsRadios1" value="0" <?php echo $s_list; ?>> <?php echo lang('PERF_GM_to_users'); ?>
    </label>
  </div>

    </div>


    <div class="col-sm-4">
    
    <div class="radio">
    <label>
      <input type="radio" name="optionsRadios1" id="optionsRadios2" value="1" <?php echo $s_all; ?>> <?php echo lang('t_list_a_all'); ?>
    </label>
  </div>

    </div>


  </div>

  <div class="form-group">
    <label for="to_msg" class="col-sm-4 control-label"></label>
    <div class="col-sm-8">
    
    <select data-placeholder="<?php echo lang('NAVBAR_users'); ?>" class="chosen-select form-control" id="to_msg" name="" multiple>
                <option value="0"></option>
                <?php
            $stmt = $dbConnection->prepare('SELECT fio as label, id as value FROM users where id !=:n AND status=:s');
            $stmt->execute(array(
                ':n' => '0',
                ':s' => '1'
            ));
            $res1 = $stmt->fetchAll();
            
            $list_sel = array();
            if (get_conf_param('global_msg_to') != "all") {
                $list_sel = get_conf_param('global_msg_to');
                $list_sel = explode(",", $list_sel);
            }
            
            foreach ($res1 as $row) {
                $opt = "";
                if (in_array($row['value'], $list_sel)) {
                    $opt = "selected";
                }
                
                //echo($row['label']);
                $row['label'] = $row['label'];
                $row['value'] = (int)$row['value'];
?>

                            <option value="<?php
                echo $row['value'] ?>" <?php echo $opt; ?>><?php
                echo $row['label'] ?></option>

                        <?php
            }
            
            if (get_conf_param('global_msg_type') == "info") {
                $gm_type['0'] = "checked";
            } 
            else if (get_conf_param('global_msg_type') == "warning") {
                $gm_type['1'] = "checked";
            } 
            else if (get_conf_param('global_msg_type') == "danger") {
                $gm_type['2'] = "checked";
            }
?>

            </select>

    </div>
  </div>

    <div class="form-group">
  <label for="mess" class="col-sm-4 control-label"><small><?php echo lang('CONF_messages_type'); ?></small></label>
  <div class="col-sm-8">
    <div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_1" value="0" <?php echo $gm_type['0']; ?> >
    <strong class="text-info">Info</strong>
  </label>
</div>
<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_0" value="1" <?php echo $gm_type['1']; ?>>
    <strong class="text-warning">Warning</strong>
  </label>
</div>

<div class="radio col-sm-12">
  <label>
    <input type="radio" name="optionsRadios_msg" id="msg_type_2" value="2" <?php echo $gm_type['2']; ?>>
    <strong class="text-danger">Danger</strong>
  </label>
  
</div>

  </div>
  </div>

  
  <div class="form-group">
    <label for="from" class="col-sm-4 control-label"><small><?php
            echo lang('MAIL_msg'); ?></small></label>
    <div class="col-sm-8">
    <textarea placeholder="" class="form-control input-sm" name="gm_text" id="gm_text" rows="3"><?php echo get_conf_param('global_msg_data'); ?></textarea>

    </div>
  </div>



<div class="">
<center>
    <button type="submit" id="conf_edit_global_message" class="btn btn-success"><i class="fa fa-pencil"></i> <?php
            echo lang('JS_save'); ?></button>
<div class="" id="conf_edit_gm_res"></div>
</center>
</div>
  </form>
      
      </div>
  </div>
  
  
</div>



<div class="col-md-12">
<div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-bell"></i> <?php
            echo lang('CONF_mail_name'); ?></h3>
</div>
      <div class="box-body">
      <form class="form-horizontal" role="form">
    
    <div class="form-group">
    <label for="mail_active" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail_status'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="mail_active">
  <option value="true" <?php
            if (get_conf_param('mail_active') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('mail_active') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>   </div>
  </div>
  
  <div class="form-group">
    <label for="from" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail_from'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="from" placeholder="<?php
            echo lang('CONF_mail_from'); ?>" value="<?php
            echo get_conf_param('mail_from') ?>">
    </div>
  </div>
      <div class="form-group">
    <label for="mail_type" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail_type'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="mail_type">
  <option value="sendmail" <?php
            if (get_conf_param('mail_type') == "sendmail") {
                echo "selected";
            } ?>>sendmail</option>
  <option value="SMTP" <?php
            if (get_conf_param('mail_type') == "SMTP") {
                echo "selected";
            } ?>>SMTP</option>
</select>    </div>
  </div>
  
  <div id="smtp_div">

    <div class="form-group">
    <label for="host" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail_host'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="host" placeholder="<?php
            echo lang('CONF_mail_host'); ?>" value="<?php
            echo get_conf_param('mail_host') ?>">
    </div>
  </div>

    <div class="form-group">
    <label for="port" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail_port'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="port" placeholder="<?php
            echo lang('CONF_mail_port'); ?>" value="<?php
            echo get_conf_param('mail_port') ?>">
    </div>
  </div>
  
  <div class="form-group">
    <label for="auth" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail_auth'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="auth">
  <option value="true" <?php
            if (get_conf_param('mail_auth') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('mail_auth') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>    </div>
  </div>
  
  <div class="form-group">
    <label for="auth_type" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail_type'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="auth_type">
  <option value="none" <?php
            if (get_conf_param('mail_auth_type') == "none") {
                echo "selected";
            } ?>>no</option>
  <option value="ssl" <?php
            if (get_conf_param('mail_auth_type') == "ssl") {
                echo "selected";
            } ?>>SSL</option>
  <option value="tls" <?php
            if (get_conf_param('mail_auth_type') == "tls") {
                echo "selected";
            } ?>>TLS</option>
</select>    </div>
  </div>
  
      <div class="form-group">
    <label for="username" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail_login'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="username" placeholder="<?php
            echo lang('CONF_mail_login'); ?>" value="<?php
            echo get_conf_param('mail_username') ?>">
    </div>
  </div>
  
      <div class="form-group">
    <label for="password" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail_pass'); ?></small></label>
    <div class="col-sm-8">
      <input type="password" class="form-control input-sm" id="password" placeholder="<?php
            echo lang('CONF_mail_pass'); ?>" value="<?php
            echo get_conf_param('mail_password') ?>">
    </div>
  </div>
  
  </div>
  

  <button type="submit" id="conf_test_mail" class="btn btn-default btn-sm pull-right"> test</button>
<center>
    <button type="submit" id="conf_edit_mail" class="btn btn-success"><i class="fa fa-pencil"></i> <?php
            echo lang('JS_save'); ?></button>
</center>


      <div class="" id="conf_edit_mail_res"></div>
      <div class="" id="conf_test_mail_res"></div>
    </form>
    
      </div>
</div>
</div>
<div class="col-md-12">
  <div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-bell"></i> <?php
            echo lang('EXT_sms_noti'); ?></h3>
</div>
      <div class="box-body">
      <form class="form-horizontal" role="form">
   



    <div class="form-group">
    <label for="smsc_active" class="col-sm-4 control-label"><small><?php echo lang('t_LIST_status'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="smsc_active">
  <option value="true" <?php
            if (get_conf_param('smsc_active') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('smsc_active') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>   <p class="help-block"><small>Supported by <a target="_blank" href="http://smscentre.com/">SMSC</a>.</small></p></div>
  </div>




  
  <div class="form-group">
    <label for="smsc_login" class="col-sm-4 control-label"><small><?php
            echo lang('EXT_sms_noti_login'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="smsc_login" placeholder="<?php
            echo lang('EXT_sms_noti_login'); ?>" value="<?php
            echo get_conf_param('smsc_login') ?>">
    </div>
  </div>
  <div class="form-group">
    <label for="smsc_pass" class="col-sm-4 control-label"><small><?php
            echo lang('EXT_sms_noti_pass'); ?></small></label>
    <div class="col-sm-8">
      <input type="password" class="form-control input-sm" id="smsc_pass" placeholder="<?php
            echo lang('EXT_sms_noti_pass'); ?>" value="<?php
            echo get_conf_param('smsc_pass') ?>">
    </div>
  </div>



 <div class="form-group">
    <label for="sms_nf" class="col-sm-4 control-label"><small><?php
            echo lang('EXT_sms_noti_list'); ?></small></label>
        <div class="col-sm-8">
    <select data-placeholder="<?php
            echo lang('CONF_mail_status'); ?>" class="chosen-select form-control" id="sms_nf" name="sms_nf[]" multiple="multiple" >

<?php
            
            $nl = get_notify_opt_list();
            
            foreach ($nl as $key => $value) {
                // code...
                
                $sc = "";
                
                $opt_ac_list = explode(",", get_conf_param('smsc_list_action'));
                
                if (in_array($key, $opt_ac_list)) {
                    $sc = "selected";
                }
?>
                            <option value="<?php echo $key; ?>" <?php echo $sc; ?>><?php echo $value; ?></option>


              <?php
            }
?>  
                        
            </select>
        </div>
  </div>


<div class="">
<center>
    <button type="submit" id="conf_edit_sms" class="btn btn-success"><i class="fa fa-pencil"></i> <?php
            echo lang('JS_save'); ?></button>
<div class="" id="conf_edit_sms_res"></div>
</center>
</div>
  </form>
      
      </div>
  </div>
  
  
</div>
<div class="col-md-12">
  <div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-bell"></i> <?php
            echo lang('EXT_pb_noti'); ?></h3>
</div>
      <div class="box-body">
      <form class="form-horizontal" role="form">
   


    <div class="form-group">
    <label for="pb_active" class="col-sm-4 control-label"><small><?php echo lang('t_LIST_status'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="pb_active">
  <option value="true" <?php
            if (get_conf_param('pb_active') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('pb_active') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>   
</div>
  </div>




  
  <div class="form-group">
    <label for="from" class="col-sm-4 control-label"><small><?php
            echo lang('EXT_pb_api_key'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="pb_api" placeholder="<?php
            echo lang('EXT_pb_api_key'); ?>" value="<?php
            echo get_conf_param('pb_api') ?>">
        <p class="help-block"><small>Supported by <a target="_blank" href="https://www.pushbullet.com/">Pushbullet</a>.</small></p>
    </div>
  </div>
<div class="">
<center>
    <button type="submit" id="conf_edit_pb" class="btn btn-success"><i class="fa fa-pencil"></i> <?php
            echo lang('JS_save'); ?></button>
<div class="" id="conf_edit_pb_res"></div>
</center>
</div>
  </form>
      
      </div>
  </div>
  
  
</div>







</div>

</div>



<?php
        } 
        else if ($menu_opt == "inform") {
?>

<div class="col-md-9">
<div class="row">
<div class="col-md-12">

<div class="box box-solid">

      <div class="box-body">

<div class="box box-solid">
                                <div class="box-header">
                                    <h3 class="box-title">CRON</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
 <p class="help-block"><small><?php
            echo lang('CONF_2arch_info'); ?> <br>
<pre>* * * * * /usr/bin/php5 -f <?php
            echo realpath(dirname(dirname(dirname(__FILE__)))) . "/sys/4cron.php" ?> > <?php
            echo realpath(dirname(dirname(dirname(__FILE__)))) . "/4cron.log" ?> 2>&1</pre></small></p>

<p class="help-block"><small><?php
            echo lang('CONF_2noty_info'); ?> <br>
      <pre>* * * * * /usr/bin/php5 -f <?php
            echo realpath(dirname(dirname(dirname(__FILE__)))) . "/sys/4cron_notify.php" ?> > <?php
            echo realpath(dirname(dirname(dirname(__FILE__)))) . "/4cron.log" ?> 2>&1</pre></small></p> 


                                </div><!-- /.box-body -->
                            </div>


<div class="box box-solid">
                                <div class="box-header">
                                    <h3 class="box-title">NODEJS</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
 <p class="help-block"><small><?php
            echo lang('CONF_node_info'); ?> <br>
<pre>sudo pm2 start <?php
            echo realpath(dirname(dirname(dirname(__FILE__)))) . "/nodejs/server.js" ?></pre></small></p>




                                </div><!-- /.box-body -->
                            </div>



      </div>
      </div>
      </div>
      </div>
      </div>

<?php
        } 
        else {
?>



<div class="col-md-9">
<div class="row">
<div class="col-md-12">

<div class="box box-solid">
<div class="box-header">
<h3 class="box-title"><i class="fa fa-cog"></i> <?php
            echo lang('CONF_mains'); ?></h3>
</div>
      <div class="box-body">
          <div class="form-horizontal" role="form">
    <div class="form-group">
    <label for="name_of_firm" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_name'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="name_of_firm" placeholder="<?php
            echo lang('CONF_name'); ?>" value="<?php
            echo get_conf_param('name_of_firm'); ?>">
    </div>
  </div>  
    <div class="form-group">
    <label for="title_header" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_title_org'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="title_header" placeholder="<?php
            echo lang('CONF_title_org'); ?>" value="<?php
            echo get_conf_param('title_header'); ?>">
    </div>
  </div>


  <div class="form-group">
    <label for="image_logo" class="col-sm-4 control-label"><small><?php echo lang('CONF_logo_image'); ?></small></label>
    <div class="col-sm-2">
      <img src="<?php echo get_logo_img('small'); ?>" >
    </div>
    <div class="col-sm-3">
        <form action="<?php
            echo $CONF['hostname'] ?>action" method="post" id="form_logo" enctype="multipart/form-data"> 
             <input type="hidden" value="set_zenlix_logo" name="mode">
             <span class="file-input btn btn-block btn-default btn-file" style="width:100%">
                <?php
            echo lang('PROFILE_select_image'); ?> <input id="file_logo" type="file" name="file">
            </span>
        </form>
    </div>
    <div class="col-sm-3"><button id="del_logo_img" class="btn btn-block bg-maroon"><?php
            echo lang('PROFILE_del_image'); ?></button></div>
  </div>


    <div class="form-group">
    <label for="mail" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_mail'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="mail" placeholder="<?php
            echo lang('CONF_mail'); ?>" value="<?php
            echo get_conf_param('mail'); ?>">
    </div>
  </div>
  


          <div class="form-group">
    <label for="lang" class="col-sm-4 control-label"><small><?php
            echo lang('SYSTEM_lang'); ?></small></label>
        <div class="col-sm-8">
    <select data-placeholder="<?php
            echo lang('SYSTEM_lang'); ?>" class="chosen-select form-control input-sm" id="lang" name="lang">
                    <option value="0"></option>
                    
                        <option <?php
            echo $status_lang_en; ?> value="en">English</option>
                        <option <?php
            echo $status_lang_ru; ?> value="ru">Русский</option>
                        <option <?php
            echo $status_lang_ua; ?> value="ua">Українська</option>
</select>
        </div>
  </div>




  <div class="form-group">
    <label for="ldap_ip" class="col-sm-4 control-label"><small><?php
            echo lang('EXT_ldap_ip'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="ldap_ip" placeholder="<?php
            echo lang('EXT_ldap_ip'); ?>" value="<?php
            echo get_conf_param('ldap_ip') ?>">
    </div>
  </div>
    <div class="form-group">
    <label for="ldap_domain" class="col-sm-4 control-label"><small><?php
            echo lang('EXT_ldap_domain'); ?></small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="ldap_domain" placeholder="<?php
            echo lang('EXT_ldap_domain'); ?>" value="<?php
            echo get_conf_param('ldap_domain') ?>">
    </div>
  </div>
  
      <div class="form-group">
    <label for="node_port" class="col-sm-4 control-label"><small>NodeJS/socket.io URL</small></label>
    <div class="col-sm-8">
      <input type="text" class="form-control input-sm" id="node_port" placeholder="ex. http://domain.com:3001" value="<?php
            echo get_conf_param('node_port') ?>">
    </div>
  </div>
  
  
  

  <div class="form-group">
    <label for="hostname" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_url'); ?></small></label>
    <div class="col-sm-8">
    <div class="input-group">
    <span class="input-group-addon"><small><?php
            echo site_proto(); ?></small></span>
      <input type="text" class="form-control input-sm" id="hostname" placeholder="<?php
            $pos = strrpos($_SERVER['REQUEST_URI'], '/');
            echo $_SERVER['HTTP_HOST'] . substr($_SERVER['REQUEST_URI'], 0, $pos + 1); ?>" value="<?php
            echo get_conf_param('hostname'); ?>">
    </div>
    </div>
  </div>



<div class="form-group">
    <label for="time_zone" class="col-sm-4 control-label"><small><?php
            echo lang('CONF_timezone'); ?></small></label>
    <div class="col-sm-8">
     


<select class="form-control input-sm" id="time_zone">
  <?php
            foreach (generate_timezone_list() as $key => $value) {
?>
    <option value="<?php
                echo $key; ?>" <?php
                if (get_conf_param('time_zone') == $key) {
                    echo "selected";
                } ?> ><?php
                echo $value; ?></option>
    <?php
            } ?>
  
</select> 





    </div>
  </div>


  
    
  
  
  
  
  <div class="form-group">
    <label for="allow_register" class="col-sm-4 control-label"><small><?php
            echo lang('REG_new'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="allow_register">
  <option value="true" <?php
            if (get_conf_param('allow_register') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('allow_register') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>    
</div>
  </div>


  <div class="form-group">
    <label for="allow_forgot" class="col-sm-4 control-label"><small><?php
            echo lang('MAIL_forgot'); ?></small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="allow_forgot">
  <option value="true" <?php
            if (get_conf_param('allow_forgot') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('allow_forgot') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>    
</div>
  </div>



  <div class="form-group">
    <label for="api_status" class="col-sm-4 control-label"><small>API</small></label>
    <div class="col-sm-8">
  <select class="form-control input-sm" id="api_status">
  <option value="true" <?php
            if (get_conf_param('api_status') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('api_status') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>    
</div>
  </div>

  <div class="form-group">
    <label for="api_status" class="col-sm-4 control-label"><small><?php echo lang('twig_cache'); ?></small></label>
    <div class="col-sm-6">
  <select class="form-control input-sm" id="twig_cache">
  <option value="true" <?php
            if (get_conf_param('twig_cache') == "true") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_true'); ?></option>
  <option value="false" <?php
            if (get_conf_param('twig_cache') == "false") {
                echo "selected";
            } ?>><?php
            echo lang('CONF_false'); ?></option>
</select>    
</div>
<div class="col-sm-2"><button id="clear_cache" class="btn btn-block btn-default btn-sm">clear cache</button>
</div>
  </div>
  
  
<center>
    <button type="submit" id="conf_edit_main" class="btn btn-success"><i class="fa fa-pencil"></i> <?php
            echo lang('JS_save'); ?></button>
    
</center>


  
    </div>
  
  
      
      </div><div id="conf_edit_main_res"></div>
</div>





</div>





















</div></div>

<?php
        }
?>


</div></section>


<?php
        include ("footer.inc.php");
?>

<?php
    }
} 
else {
    include '../auth.php';
}
?>