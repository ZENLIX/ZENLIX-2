<?php
session_start();
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');

//ok!
include_once ("functions.inc.php");
include ("library/DBbackup/dbu.class.php");
$main_portal = $CONF['main_portal'];
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        include ("app/controllers/head.inc.php");
        include ("app/controllers/navbar.inc.php");
        $rp = realpath(dirname(__FILE__));
        
        function rrmdir($dir) {
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($dir . "/" . $object) == "dir") rrmdir($dir . "/" . $object);
                        else unlink($dir . "/" . $object);
                    }
                }
                reset($objects);
                rmdir($dir);
            }
        }
        
        
        $myversion = get_conf_param('version');
        
        //echo $myversion;
        $content = file_get_contents($CONF['update_server'] . "/up.php");
        $data = json_decode($content, true);
        $getver = $data['version'];
        
        $myversion = str_replace('.', '', $myversion);
        $getver = str_replace('.', '', $getver);
        
        if ($myversion >= $getver) {
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-cog"></i>  <?php
            echo lang('UPGRADE_title'); ?>
                        <small><?php
            echo lang('UPGRADE_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php
            echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php
            echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php
            echo lang('UPGRADE_title'); ?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">
<div class="col-md-12">
<h2><center><?php
            echo lang('UPGRADE_version_already'); ?></center></h2>
</div>
</div>
<?php
        } 
        else if ($myversion < $getver) {
            
            if ($_GET['update_now']) {
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-cog"></i>  <?php
                echo lang('UPGRADE_title'); ?>
                        <small><?php
                echo lang('UPGRADE_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php
                echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php
                echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php
                echo lang('UPGRADE_title'); ?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">
<div class="col-md-12">
<div class="box box-danger">
<div class="box-body">
                                
                               
<?php
                $error_tag = false;
                $content = file_get_contents($CONF['update_server'] . "/up.php");
                $data = json_decode($content, true);
                
                $rp = realpath(dirname(__FILE__));
                
                //////create DB backup
                
                $date = new DateTime($CONF['now_dt']);
                $dform = $date->format('Ymd_His');
                
                $db = new DBBackup(array(
                    'driver' => 'mysql',
                    'host' => $CONF_DB['host'],
                    'user' => $CONF_DB['username'],
                    'password' => $CONF_DB['password'],
                    'database' => $CONF_DB['db_name']
                ));
                $backup = $db->backup();
                if (!$backup['error']) {
                    
                    $fpp = $rp . "/updates/backup/DB_zenlix_backup_" . $dform . ".sql";
                    $fp = fopen($fpp, 'a+');
                    fwrite($fp, $backup['msg']);
                    fclose($fp);
                    
                    ///////////////////////
                    
                    ////create files backup///////////
                    //Zip($rp . "/", $rp . "/updates/backup/file_zenlix_backup_".$dform.".zip");
                    //ExtendedZip::zipTree($rp . "/", $rp . "/updates/backup/file_zenlix_backup_".$dform.".zip", ZipArchive::CREATE);
                    //$fpp2=$rp . "/updates/backup/file_zenlix_backup_".$dform.".zip";
                    //////////////////////////////////
                    
                } 
                else {
                    echo "UPDATE ERROR";
                    $error_tag = true;
                }
                
                ////////////////
                $url = $CONF['update_server'] . "/zenlix.zip";
                $zipFile = $rp . "/updates/zenlix.zip";
                
                // Local Zip File Path
                
                if (file_exists($zipFile)) {
                    unlink($zipFile);
                }
                
                $zipResource = fopen($zipFile, "w");
                
                // Get The Zip File From Server
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_FAILONERROR, true);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_FILE, $zipResource);
                $page = curl_exec($ch);
                if (!$page) {
                    $error_tag = true;
                    echo "Error: " . curl_error($ch);
                }
                curl_close($ch);
                
                /* Extract Zip File */
                $zip = new ZipArchive;
                if ($zip->open($zipFile) === TRUE) {
                    $zip->extractTo($rp . "/");
                    $zip->close();
                    
                    //echo 'ok';
                    
                } 
                else {
                    echo 'error, please restart update';
                    $error_tag = true;
                }
                
                /* execute sql */
                $sql_file = file_get_contents($rp . '/sys/DB.update.sql');
                $qr = $dbConnection->exec($sql_file);
                
                unlink($zipFile);
?>

   <?php
                
                //file_backup
                
                
?>
   <table class="table table-bordered">
                                        <tbody>
                  </tbody></table><br>
  <?php
                if ($error_tag == true) {
?>
      <div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <?php
                    echo lang('UPGRADE_error'); ?>
                                    </div>
      <?php
                } 
                else if ($error_tag == false) {
                    update_val_by_key('version', $data['version']);
?>
  <div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <?php
                    echo lang('UPGRADE_success'); ?> <?php
                    echo $data['version']; ?>.
                                    </div>
  <?php
                } ?>
  
  </div></div>
</div></div></section>
  <?php
            } 
            else if (!$_GET['update_now']) {
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-cog"></i>  <?php
                echo lang('UPGRADE_title'); ?>
                        <small><?php
                echo lang('UPGRADE_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php
                echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php
                echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php
                echo lang('UPGRADE_title'); ?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">
<div class="col-md-12">
<?php
                $content = file_get_contents($CONF['update_server'] . "/up.php");
                $data = json_decode($content, true);
                $cl = explode('|', $data['change_log']);
?>
<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <?php
                echo lang('UPGRADE_new'); ?> <?php
                echo $data['version']; ?>
                                    </div>
                                    
                                    

<br>
</div>
<div class="col-md-12"><div class="box box-danger">
                                <div class="box-header">
                                    <i class="fa fa-warning"></i>
                                    <h3 class="box-title"><?php
                echo lang('UPGRADE_list'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                <ul>
                                           <?php
                foreach ($cl as $clr) {
                    echo "<li>" . $clr . "</li>";
                }
?>
                                </ul>
                                           </div><!-- /.box-body -->
                            </div></div>
<div class="col-md-12"><div class="box box-danger">
                                <div class="box-header">
                                    <i class="fa fa-warning"></i>
                                    <h3 class="box-title"><?php
                echo lang('UPGRADE_files'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                
                                <table class="table table-bordered">
                                        <tbody>
                                            <?php
                
                //
                
                $files_def = array(
                    '/functions.inc.php',
                    '/index.php',
                    '/.htaccess'
                );
                
                $directory_def = array(
                    '/css/',
                    
                    //subdirs
                    '/img/',
                    '/app/',
                    '/js/',
                    
                    //subdirs
                    '/lang/',
                    '/sys/',
                    '/library/',
                    '/static/'
                );
                
                //проверка файлов и директорий/субдиректорий/файлов на запись и
                //разархив зипа в директорию
                
                //если всё норм то ок иначе - вывести список проблемных файлов и сообщение
                
                $r = true;
                foreach ($files_def as $rfl) {
                    
                    ///files base
                    if (file_exists($rp . $rfl)) {
                        
                        if (is_writable($rp . $rfl)) {
                            $w = "<small class=\"label label-success\">ok</small>";
                        } 
                        else if (!is_writable($rp . $rfl)) {
                            $w = "<small class=\"label label-danger\">not writable</small>";
                            $r = false;
                        }
                    } 
                    else if (!file_exists($rp . $rfl)) {
                        $w = "<small class=\"label label-warning\">will be created</small>";
                    }
                    echo "<tr><td>" . $rfl . "</td><td>" . $w . "</td></tr>";
                }
                
                ///end files
                
                $all_subfiles = array();
                foreach ($directory_def as $dfl) {
                    
                    //echo $rp.$dfl."<br>";
                    
                    $dirs = array_filter(glob($rp . $dfl . "*"));
                    $all_subfiles = array_merge($all_subfiles, $dirs);
                    
                    //base dirs////
                    if (file_exists($rp . $dfl)) {
                        
                        if (is_writable($rp . $dfl)) {
                            $w = "<small class=\"label label-success\">ok</small>";
                        } 
                        else if (!is_writable($rp . $dfl)) {
                            $w = "<small class=\"label label-danger\">not writable</small>";
                            $r = false;
                        }
                    } 
                    else if (!file_exists($rp . $dfl)) {
                        $w = "<small class=\"label label-warning\">will be created</small>";
                    }
                    echo "<tr><td>" . $dfl . "</td><td>" . $w . "</td></tr>";
                    
                    ////end base dirs
                    
                    
                }
                
                foreach ($all_subfiles as $key) {
                    
                    // code...
                    // echo $key."<br>";
                    
                    if (file_exists($key)) {
                        
                        if (is_writable($key)) {
                            $w = "<small class=\"label label-success\">ok</small>";
                        } 
                        else if (!is_writable($key)) {
                            $w = "<small class=\"label label-danger\">not writable</small>";
                            echo "<tr><td>" . $key . "</td><td>" . $w . "</td></tr>";
                            $r = false;
                        }
                    } 
                    else if (!file_exists($key)) {
                        $w = "<small class=\"label label-warning\">will be created</small>";
                        echo "<tr><td>" . $key . "</td><td>" . $w . "</td></tr>";
                    }
                }
                
                //print_r($all_subfiles);
                
                if ($r == false) {
                    $s = "disabled";
                } 
                else if ($r == true) {
                    $s = "";
                }
?> 
                                        
                                        

                                    </tbody></table>
                                <p>
                                <br>


                                <?php
                if ($r == false) { ?>
                                <strong>
                                  <?php
                    echo lang('UPGRADE_att'); ?>
                                </strong>
                                <table class="table table-bordered">
                                        <tbody>
                                          <?php
                    foreach ($files_def as $f_value) {
                        echo "<tr><td>" . $f_value . "</td><td><small class=\"label label-default\">read/write</small></td></tr>";
                    }
                    foreach ($directory_def as $d_value) {
                        echo "<tr><td>" . $d_value . "* <small class=\"text-muted\">and all subdirs</small></td><td><small class=\"label label-default\">read/write</small></td></tr>";
                    }
?>
                                          
                                        </tbody>
                                </table>

                                <?php
                } ?>
                                </p>
                                
                                
                                                                   </div><!-- /.box-body -->
                            </div></div>
                            <div class="col-md-12"><hr>

<div class="alert alert-danger"><?php
                echo lang('BU_INFO_DNM'); ?></div>

                            </div>
                            <div class="col-md-6 col-md-offset-3">
                              <div class="box">
                              <div class="box-body"><a href="update.php?update_now=true" class="btn btn-success btn-block btn-sm " <?php
                echo $s; ?>><?php
                echo lang('UPGRADE_now'); ?></a></div>
                              </div>
                              
                            </div>
                            
                            
                            
</div>


<?php
            }
        }
        include ("app/controllers/footer.inc.php");
?>

<?php
    }
} 
else {
    include 'auth.php';
}
?>