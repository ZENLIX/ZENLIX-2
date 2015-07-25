<?php
session_start();
include_once ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        
        $CONF['title_header'] = lang('UNITS_title') . " - " . $CONF['name_of_firm'];
        
        include ("head.inc.php");
        include ("navbar.inc.php");
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
        
        $stmt = $dbConnection->prepare('select id, name from units');
        $stmt->execute();
        $res1 = $stmt->fetchAll();
?>      
      
      
      
<table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
        <thead>
          <tr>
            <th><center>ID</center></th>
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
?>
    <tr id="tr_<?php
            echo $row['id']; ?>">
    
    
    <td><small><center><?php
            echo $row['id']; ?></center></small></td>
    <td><small><?php
            echo $row['name']; ?></small></td>
<td><small><center><button id="units_del" type="button" class="btn btn-danger btn-xs" value="<?php
            echo $row['id']; ?>">del</button></center></small></td>
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
        include ("footer.inc.php");
?>

<?php
    }
} 
else {
    include '../auth.php';
}
?>