<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        $CONF['title_header'] = lang('USERS_title') . " - " . $CONF['name_of_firm'];
        
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        if (isset($_GET['create'])) {
            $menu_opt = "create";
            $menu_active['create'] = "active";
        } 
        else if (isset($_GET['list'])) {
            $menu_opt = "list";
            $menu_active['list'] = "active";
        } 
        else if (isset($_GET['import'])) {
            $menu_opt = "import";
            $menu_active['import'] = "active";
        } 
        else if (isset($_GET['ad_f'])) {
            $menu_opt = "ad_f";
            $menu_active['ad_f'] = "active";
        }
?>

<section class="content-header">
                    <h1>
                       <i class="fa fa-users"></i> <?php
        echo lang('USERS_title'); ?>
                        <small><?php
        echo lang('USERS_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php
        echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php
        echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php
        echo lang('USERS_title'); ?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">

<div class="col-md-3">


<div class="row">
<?php
        if (isset($_GET['list'])) { ?>
<div class="col-md-12">
    <div class="box box-solid">
    <div class="box-body">
    
    <input type="text" class="form-control input-sm" id="fio_find_admin" autofocus placeholder="<?php
            echo lang('NEW_fio'); ?>">
    

    </div>
    </div>
</div>
<?php
        }
?>
<div class="col-md-12">
    <div class="box box-solid">
    <div class="box-body">
    


<div class="list-group">
<a href="users?list" class="list-group-item <?php echo $menu_active['list']; ?>"><?php
        echo lang('USERS_list'); ?></a>

  <a href="users?create" class="list-group-item <?php echo $menu_active['create']; ?>"><?php
        echo lang('USERS_create'); ?></a>
    
    <a href="users?import" class="list-group-item <?php echo $menu_active['import']; ?>"><?php
        echo lang('LDAP_IMPORT_user_t'); ?></a>
<a href="users?ad_f" class="list-group-item <?php echo $menu_active['ad_f']; ?>"><?php
        echo lang('FIELD_title'); ?></a>
  
</div>





    </div>
    </div>
</div>
</div>
</div>





<div class="col-md-9">




    
    
    <div id="">
    
    
    
      <?php
        if (isset($_GET['create'])) {
            
            //echo "in";
            $_POST['menu'] = "new";
            include_once ("users.inc.php");
        } 
        else if (isset($_GET['import'])) {
            
            $_POST['menu'] = "import";
            include_once ("users.inc.php");
        } 
        else if (isset($_GET['import_step_3'])) {
            
            $_POST['menu'] = "import_step_3";
            include_once ("users.inc.php");
        } 
        else if (isset($_GET['import_step_2'])) {
            
            $_POST['menu'] = "import_step_2";
            include_once ("users.inc.php");
        } 
        else if (isset($_GET['ad_f'])) {
            
            $_POST['menu'] = "ad_f";
            include_once ("users.inc.php");
        } 
        else if (isset($_GET['list'])) {
            
            //echo "in";
            
            
?>
        <div id="content_users">
        <?php
            $_POST['menu'] = "list";
            $_POST['page'] = "1";
            include_once ("users.inc.php");
?>
        </div>
        
        
        
        <div class="text-center"><ul id="example_users" class="pagination pagination-sm"></ul></div>
                    <input type="hidden" id="cur_page" value="1">
                    <input type="hidden" id="total_pages" value="<?php
            echo get_total_pages_workers(); ?>">
    
        <?php
        } 
        else if (isset($_GET['edit'])) {
            
            //echo "in";
            $_POST['menu'] = "edit";
            $_POST['id'] = $_GET['edit'];
            include_once ("users.inc.php");
        } 
        else {
?>
        <div id="content_users">
        <?php
            $_GET['list'] = "s";
            $_POST['menu'] = "list";
            $_POST['page'] = "1";
            include_once ("users.inc.php");
?>
        </div>
        
        
        
        <div class="text-center"><ul id="example_users" class="pagination pagination-sm"></ul></div>
                    <input type="hidden" id="cur_page" value="1">
                    <input type="hidden" id="total_pages" value="<?php
            echo get_total_pages_workers(); ?>">
    
        <?php
        }
?>
    
    </div>






</div>
</section>

<?php
        include ("footer.inc.php");
?>

<?php
    }
} 
else {
    include 'auth.php';
}
?>