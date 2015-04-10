<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    
    include ("head.inc.php");
    include ("navbar.inc.php");
    
    /*
    
    если есть права на добавление или редактирование то показывать страницу
    
    если есть на добавление - кнопка добавить пользователя - форма (аппрув)
    если есть на редактирование -  кнопка напротив редактировать - форма редактирования (аппрув)
    
    get_user_val('priv_add_client')
    get_user_val('priv_edit_client')
    
    
    */
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-users"></i> <?php echo lang('USERS_list'); ?>
                        <small><?php echo lang('UNITS_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('USERS_list'); ?></li>
                    </ol>
                </section>
                
                
<section class="content">


<div class="row">

<div class="col-md-3">


<div class="row">

<div class="col-md-12">
	<div class="box box-solid">
	<div class="box-body">
	<?php
    if (get_user_val('priv_add_client') == "1") { ?>
	<a href="?add" class="btn btn-success btn-block"><i class="fa fa-male"></i> <?php echo lang('USERS_create'); ?></a>
	<?php
    } ?>
	<a href="?list" class="btn btn-primary btn-block"><i class="fa fa-list-alt"></i> <?php echo lang('USERS_list'); ?></a>
	
	

	</div>
	</div>
</div>
<div class="col-md-12">
                    

                    <div class="callout ">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php echo lang('WORKERS_info'); ?>
	     </small>
                                    </div>
                                    
                                    
                                    
                    
                    
                    
                    </div>
                    
                    
</div>
</div>





<div class="col-md-9">

<div class="row">
	<?php
    if ((!isset($_GET['add']) && (!isset($_GET['edit'])))) { ?> 
<div class="col-md-12">
	<div class="box box-solid">
	<div class="box-body">
	
	<input type="text" class="form-control input-sm" id="fio_find_admin" autofocus placeholder="<?php echo lang('NEW_fio'); ?>">
	

	</div>
	</div>
</div>
<?php
    }
?>
	
<div class="col-md-12">
	
	
	
	  <?php
    if (isset($_GET['add'])) {
        
        //echo "in";
        $_POST['menu'] = "new";
        include_once ("clients.inc.php");
    } else if (isset($_GET['list'])) {
        
        //echo "in";
        
?>
		<div id="content_clients">
		<?php
        $_POST['menu'] = "list";
        $_POST['page'] = "1";
        include_once ("clients.inc.php");
?>
		</div>
		
		
		
		<div class="text-center"><ul id="example_clients" class="pagination pagination-sm"></ul></div>
                    <input type="hidden" id="cur_page" value="1">
                    <input type="hidden" id="total_pages" value="<?php echo get_total_pages_clients(); ?>">
	
		<?php
    } else if (isset($_GET['edit'])) {
        
        //echo "in";
        $_POST['menu'] = "edit";
        $_POST['id'] = $_GET['edit'];
        include_once ("clients.inc.php");
    } else {
?>
		<div id="content_clients">
		<?php
        $_GET['list'] = "s";
        $_POST['menu'] = "list";
        $_POST['page'] = "1";
        include_once ("clients.inc.php");
?>
		</div>
		
		
		
		<div class="text-center"><ul id="example_clients" class="pagination pagination-sm"></ul></div>
                    <input type="hidden" id="cur_page" value="1">
                    <input type="hidden" id="total_pages" value="<?php echo get_total_pages_clients(); ?>">
	
		<?php
    }
?>
	
	</div>
</div>





</div>
</section>
                

<?php
    include ("footer.inc.php");
?>

<?php
} else {
    include '../auth.php';
}
?>