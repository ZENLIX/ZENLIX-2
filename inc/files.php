<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if (validate_admin($_SESSION['helpdesk_user_id'])) {
        include ("head.inc.php");
        include ("navbar.inc.php");
?>
<section class="content-header">
                    <h1>
                        <i class="fa fa-files-o"></i> <?php echo lang('FILES_title'); ?>
                        <small><?php echo lang('FILES_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('FILES_title'); ?></li>
                    </ol>
                </section>
                
                
                <section class="content">

                    <!-- row -->
                    <div class="row">
                    <div class="col-md-3">
                    <?php
        if ($CONF['file_uploads'] == "false") { ?>
                    
                    <div class="callout callout-danger">
                        <?php echo lang('FILES_off'); ?>
                    </div>
                    <?php
        } ?>
                    <div class="callout callout-info">
                                        
                                        <small> <i class="fa fa-info-circle"></i> 
<?php echo lang('FILES_info'); ?>
         </small>
                                    </div></div>
                    <div class="col-md-9">
                    
                    
                    
                    
                    <div class="box box-solid">
                                
                                <div class="box-body">
                                <div class="" id="content_files">
      
      
<?php
        
        $stmt = $dbConnection->prepare('select id, ticket_hash, original_name,file_hash,file_type,file_size,file_ext from files');
        $stmt->execute();
        $res1 = $stmt->fetchAll();
?>      
      
      
      
<table class="table table-bordered table-hover" style=" font-size: 14px; " id="">
        <thead>
          <tr>
            
            <th><center><?php echo lang('FILES_name'); ?></center></th>
            <th><center><?php echo lang('FILES_ticket'); ?></center></th>
            <th><center><?php echo lang('FILES_size'); ?></center></th>
            <th><center><?php echo lang('t_LIST_action'); ?></center></th>
          </tr>
        </thead>
        <tbody>     
        <?php
        
        foreach ($res1 as $row) {
?>
        <tr id="tr_<?php echo $row['id']; ?>">
        
        
        
        
        <td><small><?php echo get_file_icon($row['file_hash']); ?> <?php echo $row['original_name']; ?></small></td>
        <td><small><a href="./ticket?<?php echo $row['ticket_hash'] ?>">#<?php echo get_ticket_id_by_hash($row['ticket_hash']); ?></a></small></td>
        <td><small><?php echo round(($row['file_size'] / (1024 * 1024)), 2); ?> Mb</small></td>
<td><small><center>
<button id="files_del" type="button" class="btn btn-danger btn-xs" value="<?php echo $row['file_hash']; ?>" title="<?php echo lang('FILES_del'); ?>"><i class="fa fa-trash-o"></i> </button>
<a href="<?php echo $CONF['hostname']; ?>sys/download.php?<?php echo $row['file_hash']; ?>" class="btn btn-success btn-xs" title="<?php echo lang('FILES_down'); ?>"><i class="fa fa-download"></i> </a>
</center></small></td>


        </tr>
                <?php
        } ?>
        
        
            
        </tbody>
</table>
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
} else {
    include '../auth.php';
}
?>
