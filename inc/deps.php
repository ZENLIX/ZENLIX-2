<?php
session_start();
include("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
if (validate_admin($_SESSION['helpdesk_user_id'])) {
   include("head.inc.php");
   include("navbar.inc.php");
   
  

?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-sitemap"></i> <?=lang('DEPS_title');?>
                        <small><?=lang('DEPS_title_ext');?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?=$CONF['hostname']?>index.php"><span class="icon-svg"></span> <?=$CONF['name_of_firm']?></a></li>
                        <li class="active"><?=lang('DEPS_title');?></li>
                    </ol>
                </section>
                
                
                <section class="content">

                    <!-- row -->
                    <div class="row">
                    <div class="col-md-3">
                    <input type="text" class="form-control input-sm ui-autocomplete-input" id="deps_text" placeholder="<?=lang('DEPS_name');?>" autocomplete="off">
                    <button id="deps_add" class="btn btn-block btn-default btn-sm" type="submit"><?=lang('DEPS_add');?></button><br>
                   

                    <div class="callout callout-info">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?=lang('DEPS_info');?>
	     </small>
                                    </div>
                                    </div>
                    <div class="col-md-9">
                    
                    
                    
                    
                    <div class="box box-solid">
                                
                                <div class="box-body">
                                
                                
                                
                                <div class="" id="content_deps">
      
      
<?php 
	
		
	
	
			$stmt = $dbConnection->prepare('select id, name, status from deps where id!=:n');
			$stmt->execute(array(':n' => '0'));
			$res1 = $stmt->fetchAll();
	
	
?>      
      
      
      
<table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
        <thead>
          <tr>
          	
            <th><center><?=lang('DEPS_n');?></center></th>
            <th><center><?=lang('DEPS_action');?></center></th>
          </tr>
        </thead>
		<tbody>		
		<?php 
		
			foreach($res1 as $row) {
			
			$cl="";
			if ($row['status'] == "0") {$id_action="deps_show"; $icon="<i class=\"fa fa-eye-slash\"></i>"; $cl="active";}
			if ($row['status'] == "1") {$id_action="deps_hide"; $icon="<i class=\"fa fa-eye\"></i>"; $cl="";}
			
			
		?>
		<tr id="tr_<?=$row['id'];?>" class="<?=$cl;?>">
		
		
		
		<td><small><a href="#" data-pk="<?=$row['id']?>" data-url="actions.php" id="edit_deps" data-type="text"><?=$row['name'];?></a></small></td>
<td>

<small><center><button id="deps_del" type="button" class="btn btn-danger btn-xs" value="<?=$row['id'];?>">del</button> 

<button id="<?=$id_action;?>" type="button" class="btn btn-default btn-xs" value="<?=$row['id'];?>"><?=$icon;?></button></center></small>

</td>
		</tr>
				<?php } ?>
		
		
			
		</tbody>
</table>
      </div>
                                
                                
                                
                                
                                
                                
                                </div>
                    </div>
                    </div>
                    </div>
                </section>









<?php
 include("footer.inc.php");
?>

<?php
	}
	}
else {
    include '../auth.php';
}
?>