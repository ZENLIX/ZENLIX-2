<?php
session_start();
include_once "../functions.inc.php";

if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("client.navbar.inc.php");
        






if (isset($_GET['h'])) {
$get_type="h";
$h = ($_GET['h']);
            
            $stmt = $dbConnection->prepare('select id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where hashname=:h');
            $stmt->execute(array(':h' => $h));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
}
else if (isset($_GET['cat'])) {
$get_type="cat";
$cat_id=$_GET['cat'];

    $stmt = $dbConnection->prepare('SELECT name from helper_cat where id=:p_id');
    $stmt->execute(array(':p_id' => $cat_id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
ob_start();
show_item_helper_cat($cat_id);
$show_item_helper_cat = ob_get_contents();
ob_end_clean();

}
else {
$get_type="else";
    }







$basedir = dirname(dirname(__FILE__)); 

 try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/views');
            
            // инициализируем Twig
if (get_conf_param('twig_cache') == "true") {
$twig = new Twig_Environment($loader,array(
    'cache' => $basedir.'/inc/cache',
));
            }
            else {
$twig = new Twig_Environment($loader);
            }
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('client.helper.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
'hostname'=>$CONF['hostname'],
'name_of_firm'=>$CONF['name_of_firm'],
'get_type'=>$get_type,
'HELPER_title'=>lang('HELPER_title'),
'HELPER_back'=>lang('HELPER_back'),
'title'=>make_html($fio['title']),
'message'=>$fio['message'],
'HELPER_pub'=>lang('HELPER_pub'),
'user_init_id'=>nameshort(name_of_user_ret($fio['user_init_id'])),
'dt'=>$fio['dt'],
'HELPER_print'=>lang('HELPER_print'),
'name'=>$row['name'],
'show_item_helper_cat'=>$show_item_helper_cat,
'HELPER_desc'=>lang('HELPER_desc'),
'HELPER_find'=>lang('HELPER_find'),
'HELPER_info'=>lang('HELPER_info')





            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }













/*

        if (isset($_GET['h'])) {
            
            $h = ($_GET['h']);
            
            $stmt = $dbConnection->prepare('select id, user_init_id, unit_to_id, dt, title, message, hashname
                            from helper where hashname=:h');
            $stmt->execute(array(':h' => $h));
            $fio = $stmt->fetch(PDO::FETCH_ASSOC);
?>

        <section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?php echo lang('HELPER_title'); ?>
                        
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('HELPER_title'); ?></li>
                    </ol>
                </section>
                
                
                
            <section class="content">


<div class="row">
    <div class="col-md-1">
        <a id="go_back" class="btn btn-primary btn-sm btn-block"><i class="fa fa-reply"></i> <?php echo lang('HELPER_back'); ?></a>
    </div>
    
    
    <div class="col-md-11">
        <div class="box box-solid">
            <div class="box-body">
            <h3 style=" margin-top: 0px; "><?php echo make_html($fio['title']) ?></h3>
    <p><?php echo ($fio['message']) ?></p>
    <hr>
    
    <p class="text-right"><small class="text-muted"><?php echo lang('HELPER_pub'); ?>: <?php echo nameshort(name_of_user_ret($fio['user_init_id'])); ?></small><br><small class="text-muted"> <time id="c" datetime="<?php echo $fio['dt']; ?>"></time></small>
    <br><a id="print_t" class="btn btn-default btn-xs"> <i class="fa fa-print"></i> <?php echo lang('HELPER_print'); ?></a>
        </p>
            </div>
        </div>
    </div>
</div>
            </section>
    
    
    
    
    

    
    <?php
        } 


else if (isset($_GET['cat'])) {


$cat_id=$_GET['cat'];

    $stmt = $dbConnection->prepare('SELECT name from helper_cat where id=:p_id');
    $stmt->execute(array(':p_id' => $cat_id));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

   // $row['name'];

?>
 <section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?php echo lang('HELPER_title'); ?>
                        <small><?=$row['name'];?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><a href="helper"><?php echo lang('HELPER_title'); ?></a></li>
                        <li class="active"><?=$row['name'];?></li>
                    </ol>
</section>
                
                
                
            <section class="content">







                    <!-- row -->
                    <div class="row">
                    
                    
                    
                                        <div class="col-md-3">
                    

                    
                                    
                                    
                                    
                    
                    
                    
                    </div>

                    
                    <div class="col-md-9">
                         <div class="box box-solid">


                                <div class="">
                                   <?=show_item_helper_cat($cat_id);?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>





<?php
}






        else {
?>

    <section class="content-header">
                    <h1>
                        <i class="fa fa-globe"></i> <?php echo lang('HELPER_title'); ?>
                        <small><?php echo lang('HELPER_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('HELPER_title'); ?></li>
                    </ol>
                </section>
                
                
                
            <section class="content">


<div class="row">
    
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-body"><div class="input-group">
                        <input type="text" class="form-control input-sm" id="find_helper" autofocus placeholder="<?php echo lang('HELPER_desc'); ?>">
      <span class="input-group-btn">
        <button id="" class="btn btn-default btn-sm" type="submit"><i class="fa fa-search"></i> <?php echo lang('HELPER_find'); ?></button>
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
<?php echo lang('HELPER_info'); ?>
         </small>
                                    </div>
                                    
                                    
                                    
                    
                    
                    
                    </div>
                    
                    <div class="col-md-9" id="help_content">
                    
                    </div>
                    
                    
                    
                    
                    
                    </div>
            </section>    
                
                
                
                
                


        


<?php
*/
        
        include ("footer.inc.php");
?>


<?php
    }
} else {
    include 'auth.php';
}
?>
