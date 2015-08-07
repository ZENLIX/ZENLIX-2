<?php
session_start();
include_once ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        
        $CONF['title_header'] = lang('UNITS_title') . " - " . $CONF['name_of_firm'];
        
        include ("head.inc.php");
        include ("navbar.inc.php");


if (isset($_GET['edit'])) {


$unit_id=$_GET['edit'];


        $stmt = $dbConnection->prepare('select name, status, main_user,id from units where id=:uid');
        $stmt->execute(array(
            ':uid'=>$unit_id
            ));
        $tt2r = $stmt->fetch(PDO::FETCH_ASSOC);






    ?>

<section class="content-header">
                    <h1>
                        <i class="fa fa-building-o"></i> <?php
        echo lang('UNITS_title'); ?>
                        <small><?php
        echo $tt2r['name']; ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php
        echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php
        echo $CONF['name_of_firm'] ?></a></li>
                        <li>
                        <a href="units"><?php
        echo lang('UNITS_title'); ?></a></li>
        <li class="active"> <?php echo $tt2r['name']; ?> </li>
                    </ol>
                </section>

<section class="content">
<div class="col-md-12">
                    
                    
                    
                    
                    <div class="box box-solid">
                                
                                <div class="box-body">

<form class="form-horizontal" role="form">
    <div class="form-group">
    <label for="mail" class="col-sm-2 control-label"><?php
        echo lang('DEPS_n'); ?></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="mail" type="text" class="form-control input-sm" id="name" placeholder="<?php
        echo lang('DEPS_n'); ?>" value="<?php echo $tt2r['name'];?>">
        </div>
  </div>



      <div class="form-group">
    <label for="mail" class="col-sm-2 control-label"><?php
        echo lang('users_main_unit_user'); ?></label>
        <div class="col-sm-10">
    <select name="main_user" id="main_user" data-placeholder="<?php
        echo lang('users_main_unit_user'); ?>" class="chosen-select form-control input-sm">
                    <option value="NULL"></option>
                    <?php
        $stmt = $dbConnection->prepare('SELECT id,fio FROM users where is_client=1 and status=1 order by fio COLLATE utf8_unicode_ci ASC');
        $stmt->execute();
        $res1 = $stmt->fetchAll();
        foreach ($res1 as $row) {

$s='';
if ($tt2r['main_user'] == $row['id']) {
    $s="selected";
}


?>

                        <option value="<?php
            echo $row['id'] ?>" <?php echo $s; ?>><?php
            echo $row['fio'] ?></option>

                    <?php
        }
?>

                </select>
        </div>
  </div>


<center>
    <button type="submit" id="unit_save" class="btn btn-success" value="<?php
            echo $tt2r['id'] ?>"><i class="fa fa-pencil"></i> <?php echo lang('PORTAL_news_save');?></button>
    
</center>



</form>





                                </div>
                                </div>
                                </div>
</section>




    <?php
}
if (!isset($_GET['edit'])) {
    

?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-building-o"></i> <?php
        echo lang('UNITS_title'); ?>
                        <small><?php
        echo lang('UNITS_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php
        echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php
        echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php
        echo lang('UNITS_title'); ?></li>
                    </ol>
                </section>
                
                
                <section class="content">

                    <!-- row -->
                    <div class="row">
                    <div class="col-md-3">
                          <input type="text" class="form-control input-sm ui-autocomplete-input" id="units_text" placeholder="<?php
        echo lang('UNITS_name'); ?>" autocomplete="off">
      
        <button id="units_add" class="btn btn-default btn-sm btn-block" type="submit"><?php
        echo lang('UNITS_add'); ?></button>
      <br>
      
      
                    <div class="callout">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php
        echo lang('UNITS_info'); ?>
       </small>
                                    </div></div>
                    <div class="col-md-9">
                    
                    
                    
                    
                    <div class="box box-solid">
                                
                                <div class="box-body">
                                <div class="" id="content_units">
      
      
<?php
        
        //$results = mysql_query("select id, name from units;");
        
        $stmt = $dbConnection->prepare('select id, name, status from units');
        $stmt->execute();
        $res1 = $stmt->fetchAll();
?>      
      
      
      
<table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
        <thead>
          <tr>
            
            <th><center><?php
        echo lang('UNITS_n'); ?></center></th>
            <th><center><?php
        echo lang('UNITS_action'); ?></center></th>
          </tr>
        </thead>
    <tbody>   
    <?php
         
        //while ($row = mysql_fetch_assoc($results)) {
        foreach ($res1 as $row) {


if ($row['status'] == 1) {
    $l_a='units_lock';
    $l_i='unlock';
}
if ($row['status'] == 0) {
    $l_a='units_unlock';
    $l_i='lock';
}
?>
    <tr id="tr_<?php
            echo $row['id']; ?>">
    
    
    
    <td><small><a href="#" data-pk="<?php
            echo $row['id'] ?>" data-url="action" id="edit_units" data-type="text"><?php
            echo $row['name']; ?></a></small></td>
<td><small><center><button id="units_del" type="button" class="btn btn-danger btn-xs" value="<?php
            echo $row['id']; ?>"><i class="fa fa fa-trash"></i></button>


<button id="<?php echo $l_a; ?>" type="button" class="btn btn-default btn-xs" value="<?php
            echo $row['id']; ?>"><i class="fa fa fa-<?php echo $l_i; ?>"></i></button>


<a href="units?edit=<?php
            echo $row['id']; ?>"  class="btn btn-default btn-xs"><i class="fa fa-cog"></i></a>

            </center></small></td>
    </tr>
        <?php
        } ?>
    
    
      
    </tbody>
</table>
      <br>
      </div>
                                </div>
                    </div>
                    </div>
                    </div>
                </section>
                

<?php
}
        include ("footer.inc.php");
?>

<?php
    }
} 
else {
    include '../auth.php';
}
?>