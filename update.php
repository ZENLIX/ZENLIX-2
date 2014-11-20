<?php
session_start();
//ok ;)
include ("functions.inc.php");
include ("sys/dbu.class.php");
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        include ("inc/head.inc.php");
        include ("inc/navbar.inc.php");
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
        
        function Zip($source, $destination) {
            if (!extension_loaded('zip') || !file_exists($source)) {
                return false;
            }
            
            $zip = new ZipArchive();
            if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
                return false;
            }
            
            $source = str_replace('\\', '/', realpath($source));
            
            if (is_dir($source) === true) {
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
                
                foreach ($files as $file) {
                    $file = str_replace('\\', '/', $file);
                    
                    // Ignore "." and ".." folders
                    if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) continue;
                    
                    $file = realpath($file);
                    
                    if (is_dir($file) === true) {
                        $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                    } else if (is_file($file) === true) {
                        $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                    }
                }
            } else if (is_file($source) === true) {
                $zip->addFromString(basename($source), file_get_contents($source));
            }
            
            return $zip->close();
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
                        <i class="fa fa-cog"></i>  <?php echo lang('UPGRADE_title'); ?>
                        <small><?php echo lang('UPGRADE_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('UPGRADE_title'); ?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">
<div class="col-md-12">
<h2><center><?php echo lang('UPGRADE_version_already'); ?></center></h2>
</div>
</div></section>
<?php
        } else if ($myversion < $getver) {
            
            if ($_GET['update_now']) {
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-cog"></i>  <?php echo lang('UPGRADE_title'); ?>
                        <small><?php echo lang('UPGRADE_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('UPGRADE_title'); ?></li>
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
                $db = new DBBackup(array('driver' => 'mysql', 'host' => $CONF_DB['host'], 'user' => $CONF_DB['username'], 'password' => $CONF_DB['password'], 'database' => $CONF_DB['db_name']));
                $backup = $db->backup();
                if (!$backup['error']) {
                    $fpp = $rp . "/updates/backup/DB.sql";
                    $fp = fopen($fpp, 'a+');
                    fwrite($fp, $backup['msg']);
                    fclose($fp);
                    
                    ///////////////////////
                    
                    ////create files backup///////////
                    Zip($rp . "/", $rp . "/updates/backup/file_zenlix_backup.zip");
                    
                    //////////////////////////////////
                    
                    $url = $CONF['update_server'] . "/zenlix.zip";
                    $zipFile = $rp . "/updates/zenlix.zip";
                     // Local Zip File Path
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
                        echo "Error :- " . curl_error($ch);
                    }
                    curl_close($ch);
                    
                    $dir_ver = $rp . "/updates/" . $data['version'];
                    rrmdir($dir_ver);
                    mkdir($dir_ver, 0755);
                    
                    $zip = new ZipArchive;
                    $extractPath = $dir_ver;
                    if ($zip->open($zipFile) != "true") {
                        echo "Error :- Unable to open the Zip File";
                    }
                    
                    /* Extract Zip File */
                    
                    $zip->extractTo($rp . "/");
                    
                    $sql_file = file_get_contents($dir_ver . '/DB.sql');
                    $qr = $dbConnection->exec($sql_file);
                    
                    $zip->close();
                    
                    unlink($zipFile);
                    rrmdir($dir_ver);
?>
   <table class="table table-bordered">
                                        <tbody><tr>
<td><?php echo lang('UPGRADE_dbu'); ?></td>
<td><?php echo $fpp; ?></td>

                                        </tr>

                                    </tbody></table>
   <?php
                } else {
                    echo 'An error has ocurred.';
                    $error_tag = true;
                }
                
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
                                        <?php echo lang('UPGRADE_error'); ?>
                                    </div>
      <?php
                } else if ($error_tag == false) {
                    update_val_by_key('version', $data['version']);
?>
  <div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <?php echo lang('UPGRADE_success'); ?> <?php echo $data['version']; ?>.
                                    </div>
  <?php
                } ?>
  
  </div></div>
</div></div></section>
  <?php
            } else if (!$_GET['update_n-ow']) {
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-cog"></i>  <?php echo lang('UPGRADE_title'); ?>
                        <small><?php echo lang('UPGRADE_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('UPGRADE_title'); ?></li>
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
                                        <?php echo lang('UPGRADE_new'); ?> <?php echo $data['version']; ?>
                                    </div>
                                    
                                    

<br>
</div>
<div class="col-md-12"><div class="box box-danger">
                                <div class="box-header">
                                    <i class="fa fa-warning"></i>
                                    <h3 class="box-title"><?php echo lang('UPGRADE_list'); ?></h3>
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
                                    <h3 class="box-title"><?php echo lang('UPGRADE_files'); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                
                                <table class="table table-bordered">
                                        <tbody>
                                            <?php
                
                //
                 
                $files_def = array('/actions.php', '/functions.inc.php', '/index.php', '/update.php');
                
                $directory_def = array('/css/',
                 //subdirs
                '/img/', '/inc/', '/integration/', '/js/',
                 //subdirs
                '/lang/', '/sys/');
                
                //проверка файлов и директорий/субдиректорий/файлов на запись и
                //разархив зипа в директорию
                
                //если всё норм то ок иначе - вывести список проблемных файлов и сообщение
                
                $r = true;
                foreach ($files_def as $rfl) {
                    
                    ///files base
                    if (file_exists($rp . $rfl)) {
                        
                        if (is_writable($rp . $rfl)) {
                            $w = "<small class=\"label label-success\">ok</small>";
                        } else if (!is_writable($rp . $rfl)) {
                            $w = "<small class=\"label label-danger\">not writable</small>";
                            $r = false;
                        }
                    } else if (!file_exists($rp . $rfl)) {
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
                        } else if (!is_writable($rp . $dfl)) {
                            $w = "<small class=\"label label-danger\">not writable</small>";
                            $r = false;
                        }
                    } else if (!file_exists($rp . $dfl)) {
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
                        } else if (!is_writable($key)) {
                            $w = "<small class=\"label label-danger\">not writable</small>";
                            echo "<tr><td>" . $key . "</td><td>" . $w . "</td></tr>";
                            $r = false;
                        }
                    } else if (!file_exists($key)) {
                        $w = "<small class=\"label label-warning\">will be created</small>";
                        echo "<tr><td>" . $key . "</td><td>" . $w . "</td></tr>";
                    }
                }
                
                //print_r($all_subfiles);
                
                if ($r == false) {
                    $s = "disabled";
                } else if ($r == true) {
                    $s = "";
                }
?> 
                                        
                                        

                                    </tbody></table>
                                <p>
                                <br>


                                <?php
                if ($r == false) { ?>
                                <strong>
                                  <?php echo lang('UPGRADE_att'); ?>
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
                            <div class="col-md-12"><hr></div>
                            <div class="col-md-6 col-md-offset-3">
                              <div class="box">
                              <div class="box-body"><a href="update.php?update_now=true" class="btn btn-success btn-block btn-sm " <?php echo $s; ?>><?php echo lang('UPGRADE_now'); ?></a></div>
                              </div>
                              
                            </div>
                            
                            
                            
</div>
</section>

<?php
            }
        }
        include ("inc/footer.inc.php");
?>

<?php
    }
} else {
    include 'auth.php';
}
?>