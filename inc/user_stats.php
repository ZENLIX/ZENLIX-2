<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        $priv_val = priv_status($_SESSION['helpdesk_user_id']);
        if (($priv_val == "2") || ($priv_val == "0")) {





$ulist=array();
            $stmt = $dbConnection->prepare('SELECT fio as label, id as value, unit FROM users where id !=:system and is_client=0 and status!=2 order by fio ASC');
            $stmt->execute(array(':system' => '1'));
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
                $unit_user = unit_of_user($_SESSION['helpdesk_user_id']);
                $ee = explode(",", $unit_user);
                $ec = explode(",", $row['unit']);
                
                $result = array_intersect($ee, $ec);
                
                if ($result) {
                    
                    //echo($row['label']);
                    $row['label'] = $row['label'];
                    $row['value'] = (int)$row['value'];
                    
                    if (get_user_status_text($row['value']) == "online") {
                        $s = "online";
                    } else if (get_user_status_text($row['value']) == "offline") {
                        $s = "offline";
                    }

array_push($ulist, array(

's'=>$s,
'value'=>$row['value'],
'nameshort'=>nameshort($row['label'])

    ));


                }
            }








$basedir = dirname(dirname(__FILE__)); 
            ////////////
    try {
            
            // указывае где хранятся шаблоны
            $loader = new Twig_Loader_Filesystem($basedir.'/inc/views');
            
            // инициализируем Twig
            $twig = new Twig_Environment($loader);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate('user_stats.view.tmpl');
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render(array(
                'hostname'=>$CONF['hostname'],
                'name_of_firm'=>$CONF['name_of_firm'],
                'EXT_graph_user_ext'=>lang('EXT_graph_user_ext'),
                'EXT_graph_user'=>lang('EXT_graph_user'),
                't_LIST_worker'=>lang('t_LIST_worker'),
                'ulist'=>$ulist,
                'date'=>date("Y-m-d"),
                'STATS_make'=>lang('STATS_make'),
                'EXT_graph_user_ext2'=>lang('EXT_graph_user_ext2'),
                'EXT_stats_main_todo'=>lang('EXT_stats_main_todo')
                



            ));
        }
        catch(Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
/*
?>

<section class="content-header">
                    <h1>
                        <i class="fa fa-bar-chart-o"></i> <?php echo lang('EXT_graph_user'); ?>
                        <small><?php echo lang('EXT_graph_user_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('EXT_graph_user'); ?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">


<div class="col-md-3">
  <div class="row">
    <div class="col-md-12">
    
    <div class="box box-info">

                                <div class="box-body">
                                    
                                    
                                    <form class="form-horizontal" role="form">





                                        <div class="form-group">

<div class="col-md-12">

    <div class="input-group ">
      <span class="input-group-addon"><i class="fa fa-user"></i></span>
      
      
      <select data-placeholder="<?php echo lang('t_LIST_worker'); ?>" id="user_list" name="unit_id" class="form-control input-sm">
      <option></option>


<?php
            

            
            $stmt = $dbConnection->prepare('SELECT fio as label, id as value, unit FROM users where id !=:system and is_client=0 and status!=2 order by fio ASC');
            $stmt->execute(array(':system' => '1'));
            $res1 = $stmt->fetchAll();
            foreach ($res1 as $row) {
                $unit_user = unit_of_user($_SESSION['helpdesk_user_id']);
                $ee = explode(",", $unit_user);
                $ec = explode(",", $row['unit']);
                
                $result = array_intersect($ee, $ec);
                
                if ($result) {
                    
                    //echo($row['label']);
                    $row['label'] = $row['label'];
                    $row['value'] = (int)$row['value'];
                    
                    if (get_user_status_text($row['value']) == "online") {
                        $s = "online";
                    } else if (get_user_status_text($row['value']) == "offline") {
                        $s = "offline";
                    }
?>
                    <option data-foo="<?php echo $s; ?>" value="<?php echo $row['value'] ?>"><?php echo nameshort($row['label']) ?> </option>

                <?php
                }
            }
?>
    </select>
    
    
    
    </div></div>
  </div>
  
  
  <div class="form-group">
<div class="col-md-12">
    <div class="input-group ">
      <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" name="reservation" id="reservation" class="form-control input-sm"   value="<?php echo date("Y-m-d"); ?> - <?php echo date("Y-m-d"); ?>"/>
    </div>
</div>
  </div>
 <div class="form-group">
<div class="col-md-12">
    <button class="btn btn-info btn-block btn-sm" id="user_stat_make"><?=lang('STATS_make');?></button>
</div>
</div>
<input type="hidden" id="start_time" value="<?php echo date("Y-m-d"); ?>">
<input type="hidden" id="stop_time" value="<?php echo date("Y-m-d"); ?>">
</form>
                                    
                                    
                                    
                                                                    </div><!-- /.box-body -->
                            </div>
    
  </div>
    <div class="col-md-12"><div class="callout">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php echo lang('EXT_graph_user_ext2'); ?>
       </small>
                                    </div></div>
  </div>
  
</div>


<div class="col-md-9">
    
    <div class="box box-solid">
                                
                                <div class="box-body">
                                  
                                  
                                    <div id="content_stat">
                                      <div class="alert alert-info" style="margin-bottom: 0!important;">
                        <i class="fa fa-info"></i>
                        <?php echo lang('EXT_stats_main_todo'); ?>
                    </div>
                                    </div>
                                    
                                                                   </div><!-- /.box-body -->
                            </div>
    
  </div>

</div>



</section>













<?php
*/
            include ("footer.inc.php");
?>

<?php
        }
    }
} else {
    include '../auth.php';
}
?>
