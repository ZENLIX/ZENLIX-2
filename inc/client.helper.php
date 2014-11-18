<?php
session_start();
include_once "../functions.inc.php";

if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
if ($_SESSION['helpdesk_user_id']) {
   include("head.inc.php");
   include("client.navbar.inc.php");
   
  
if (isset($_GET['h'])) {

$h=($_GET['h']);
    
    
    
    $stmt = $dbConnection->prepare('select id, user_init_id, unit_to_id, dt, title, message, hashname
							from helper where hashname=:h');
	$stmt->execute(array(':h' => $h));
	$fio = $stmt->fetch(PDO::FETCH_ASSOC);


	?>

		<section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?=lang('HELPER_title');?>
                        
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?=$CONF['hostname']?>index.php"><span class="icon-svg"></span> <?=$CONF['name_of_firm']?></a></li>
                        <li class="active"><?=lang('HELPER_title');?></li>
                    </ol>
                </section>
                
                
                
            <section class="content">


<div class="row">
	<div class="col-md-1">
		<a id="go_back" class="btn btn-primary btn-sm btn-block"><i class="fa fa-reply"></i> <?=lang('HELPER_back');?></a>
	</div>
	
	
	<div class="col-md-11">
		<div class="box box-solid">
			<div class="box-body">
			<h3 style=" margin-top: 0px; "><?=make_html($fio['title'])?></h3>
	<p><?=($fio['message'])?></p>
	<hr>
	
	<p class="text-right"><small class="text-muted"><?=lang('HELPER_pub');?>: <?=nameshort(name_of_user_ret($fio['user_init_id']));?></small><br><small class="text-muted"> <time id="c" datetime="<?=$fio['dt'];?>"></time></small>
	<br><a id="print_t" class="btn btn-default btn-xs"> <i class="fa fa-print"></i> <?=lang('HELPER_print');?></a>
    	</p>
			</div>
		</div>
	</div>
</div>
            </section>
	
	
	
	
	

	
	<?php
}
else if (!isset($_GET['h'])) {
?>

	<section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?=lang('HELPER_title');?>
                        <small><?=lang('HELPER_title_ext');?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?=$CONF['hostname']?>index.php"><span class="icon-svg"></span> <?=$CONF['name_of_firm']?></a></li>
                        <li class="active"><?=lang('HELPER_title');?></li>
                    </ol>
                </section>
                
                
                
            <section class="content">


<div class="row">
	
	<div class="col-md-12">
		<div class="box box-solid">
			<div class="box-body"><div class="input-group">
                        <input type="text" class="form-control input-sm" id="find_helper" autofocus placeholder="<?=lang('HELPER_desc');?>">
      <span class="input-group-btn">
        <button id="" class="btn btn-default btn-sm" type="submit"><i class="fa fa-search"></i> <?=lang('HELPER_find');?></button>
      </span>
                    </div>
			</div>
		</div>
	</div>
</div>



                    <!-- row -->
                    <div class="row">
                    
                    
                    
                                        <div class="col-md-3">
                    <div class="callout callout-info">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?=lang('HELPER_info');?>
	     </small>
                                    </div>
                                    
                                    
                                    
                    
                    
                    
                    </div>
                    
                    <div class="col-md-9" id="help_content">
                    
                    </div>
                    
                    
                    
                    
                    
                    </div>
            </section>    
                
                
                
                
                


        


<?php
}
 include("footer.inc.php");
?>


<?php
	
	}

}

else {
    include 'auth.php';
}
?>
