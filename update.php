<?php
session_start();
include("functions.inc.php");
include("sys/dbu.class.php");
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
if (validate_admin($_SESSION['helpdesk_user_id'])) {
   include("inc/head.inc.php");
   include("inc/navbar.inc.php");
   $rp=realpath(dirname(__FILE__));
  
  
  
  
  $myversion=get_conf_param('version');
//echo $myversion;
$content=file_get_contents("http://update.zenlix.com/up.php");
$data=json_decode($content,true);
$getver=$data['version'];

$myversion=str_replace('.', '', $myversion);
$getver=str_replace('.', '', $getver);



if ($myversion >= $getver) {
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-cog"></i>  <?=lang('UPGRADE_title');?>
                        <small><?=lang('UPGRADE_title_ext');?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?=$CONF['hostname']?>index.php"><span class="icon-svg"></span> <?=$CONF['name_of_firm']?></a></li>
                        <li class="active"><?=lang('UPGRADE_title');?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">
<div class="col-md-12">
<h2><center><?=lang('UPGRADE_version_already');?></center></h2>
</div>
</div></section>
<?php }
else if ($myversion < $getver) {
  
  
  
  
  
if ($_GET['update_now']) {
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-cog"></i>  <?=lang('UPGRADE_title');?>
                        <small><?=lang('UPGRADE_title_ext');?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?=$CONF['hostname']?>index.php"><span class="icon-svg"></span> <?=$CONF['name_of_firm']?></a></li>
                        <li class="active"><?=lang('UPGRADE_title');?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">
<div class="col-md-12">
<div class="box box-danger">
<div class="box-body">
                                
                               
<?php
$error_tag=false;
	$content=file_get_contents("http://update.zenlix.com/up.php");
	$data=json_decode($content,true);

	$fl=explode('|', $data['files_list']);
	
	$rp=realpath(dirname(__FILE__));
	
	
	function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 }
 
 
$url = "http://update.zenlix.com/updates/".$data['version']."/zenlix.zip";
$zipFile = $rp."/updates/zenlix.zip"; // Local Zip File Path
$zipResource = fopen($zipFile, "w");
// Get The Zip File From Server
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt($ch, CURLOPT_FILE, $zipResource);
$page = curl_exec($ch);
if(!$page) {
	$error_tag=true;
 echo "Error :- ".curl_error($ch);
}
curl_close($ch);

$dir_ver=$rp."/updates/".$data['version'];
rrmdir($dir_ver);
mkdir($dir_ver, 0755);

$zip = new ZipArchive;
$extractPath = $dir_ver;
if($zip->open($zipFile) != "true"){
 echo "Error :- Unable to open the Zip File";
} 
/* Extract Zip File */
$zip->extractTo($extractPath);
$zip->close();	
	

$db = new DBBackup(array( 
    'driver' => 'mysql', 
    'host' => $CONF_DB['host'], 
    'user' => $CONF_DB['username'], 
    'password' => $CONF_DB['password'], 
    'database' => $CONF_DB['db_name'] 
)); 
$backup = $db->backup(); 
if(!$backup['error']){ 
    // If there isn't errors, show the content 
    // The backup will be at $var['msg'] 
    // You can do everything you want to. Like save in a file. 
     
    $fpp=$rp."/updates/backup/DB.sql";
    $fp = fopen($fpp, 'a+');
    fwrite($fp, $backup['msg']);
    fclose($fp); 
   ?>
   <table class="table table-bordered">
                                        <tbody><tr>
<td><?=lang('UPGRADE_dbu');?></td>
<td><?=$fpp;?></td>

                                        </tr>

                                    </tbody></table>
   <?php
} else { 
    echo 'An error has ocurred.'; 
    $error_tag=true;
} 

	//file_backup
	

	
	
	
	?>
	 <table class="table table-bordered">
                                        <tbody>
	<?php
	
	foreach ($fl as $flr) {
	
	$nw=$dir_ver.$flr; // file version
	$ov=$rp.$flr;//old version
	?>
	<tr><td><?=$nw;?></td><td>==></td><td><?=$ov;?></td></tr>
	<?php
	//echo $nw." to ".$ov."<br>";
	try {
	copy($nw, $ov);
	}
	 catch (Exception $e) {
	 $error_tag=true;
	 }
	
	}
	

	?></tbody></table><br>
	<?php 
	
		if ($error_tag == true) {
			?>
			<div class="alert alert-danger alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <?=lang('UPGRADE_error');?>
                                    </div>
			<?php
			
			
		}
	else if ($error_tag == false) {
	update_val_by_key('version', $data['version']);
	
	
	
	?>
	<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <?=lang('UPGRADE_success');?> <?=$data['version'];?>.
                                    </div>
	<?php } ?>
	
	</div></div>
</div></div></section>
	<?php
}
else if (!$_GET['update_now']) {
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-cog"></i>  <?=lang('UPGRADE_title');?>
                        <small><?=lang('UPGRADE_title_ext');?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?=$CONF['hostname']?>index.php"><span class="icon-svg"></span> <?=$CONF['name_of_firm']?></a></li>
                        <li class="active"><?=lang('UPGRADE_title');?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">
<div class="col-md-12">
<?php 
$content=file_get_contents("http://localhost/web/xcode.php");
$data=json_decode($content,true);
$cl=explode('|', $data['change_log']);
$fl=explode('|', $data['files_list']);
?>
<div class="alert alert-success alert-dismissable">
                                        <i class="fa fa-check"></i>
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <?=lang('UPGRADE_new');?> <?=$data['version'];?>
                                    </div>
                                    
                                    

<br>
</div>
<div class="col-md-6"><div class="box box-danger">
                                <div class="box-header">
                                    <i class="fa fa-warning"></i>
                                    <h3 class="box-title"><?=lang('UPGRADE_list');?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                <ul>
                                           <?php
                                           
                                           foreach ($cl as $clr) {
	                                           echo "<li>".$clr."</li>";
                                           }
                                           
                                            ?>
                                </ul>
                                           </div><!-- /.box-body -->
                            </div></div>
<div class="col-md-6"><div class="box box-danger">
                                <div class="box-header">
                                    <i class="fa fa-warning"></i>
                                    <h3 class="box-title"><?=lang('UPGRADE_files');?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                
                                <table class="table table-bordered">
                                        <tbody>
                                            <?php
                                           $r=true;
                                           foreach ($fl as $rfl) {
                                           
                                           
                                           
                                           if (file_exists($rp.$rfl)) {
	                                           if (is_writable($rp.$rfl)) {$w="<small class=\"label label-success\">ok</small>"; } else if (!is_writable($rp.$rfl)) {$w="<small class=\"label label-danger\">not writable</small>";
	                                           $r=false;
	                                            }
                                           }
                                           else if (!file_exists($rp.$rfl)) {$w="<small class=\"label label-warning\">will be created</small>";}
                                           
                                           
                                           
                                           
                                           
	                                           echo "<tr><td>".$rfl."</td><td>".$w."</td></tr>";
	                                           
                                           }
                                           if ($r == false) {$s="disabled";} else if ($r == true) {$s="";}
                                            ?> 
                                        
                                        

                                    </tbody></table>
                                <p>
                                <br>
                                <strong>
	                                <?=lang('UPGRADE_att');?>
                                </strong>
                                </p>
                                
                                
                                                                   </div><!-- /.box-body -->
                            </div></div>
                            <div class="col-md-12"><hr></div>
                            <div class="col-md-6 col-md-offset-3">
	                            <div class="box">
	                            <div class="box-body"><a href="update.php?update_now=true" class="btn btn-success btn-block btn-sm " ><?=lang('UPGRADE_now');?></a></div>
	                            </div>
	                            
                            </div>
                            
                            
                            
</div>
</section>

<?php
} }
 include("inc/footer.inc.php");
?>

<?php
	}
	}
else {
    include 'auth.php';
}
?>