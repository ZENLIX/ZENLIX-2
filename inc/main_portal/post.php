<?php
session_start();



$rkeys = array_keys($_GET);


$hn = $rkeys[1];

    $stmt = $dbConnection->prepare('SELECT * from portal_posts where uniq_id=:hn');
    $stmt->execute(array(':hn' => $hn));
    

    $post = $stmt->fetch(PDO::FETCH_ASSOC);


$validate = false;
if ((validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) || (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code']))) {
$validate = true;
}


  switch ($post['type']) {
    case '1':
      $icon='<i class="fa fa-lightbulb-o"></i>';


      break;
        case '2':
      $icon='<i class="fa fa-exclamation-triangle"></i>';


      break;
          case '3':
      $icon='<i class="fa fa-question-circle"></i>';
      break;
          case '4':
      $icon='<i class="fa fa-heart"></i>';
      break;
    default:
      # code...
      break;
  }

if (!$_GET['p']) {
  $p=1;
}
else if ($_GET['p']) {
  $p=$_GET['p'];
}


        $page = ($p);
        $perpage = '10';
        $start_pos = ($page - 1) * $perpage;




        $CONF['title_header']=get_conf_param('name_of_firm')." - ".$post['subj'];
        
include "head.inc.php";


include "navbar.inc.php";
?>
<div class="content-wrapper">
<section class="content">







<section class="invoice">
          <!-- title row -->
          <div class="row">



<div class="col-md-9">
<div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"> <?=$icon;?> <?=$post['subj'];?></h3>
<div class="box-tools pull-right">
                    <h4 style="  margin-top: 0px;"> <?=get_post_status($post['uniq_id'], 'e');?> </h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body" style="
    line-height: 23px;
    font-size: 15px;
    padding: 10px 20px;
">




<div id="<?=$post['uniq_id'];?>" value="post">                  

<div class="editable_text">

<?=$post['msg'];?>
</div>



<?=view_attach_files($post['uniq_id'], 'post');?>


<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<div class="edit-bar" style="display: none;">
<div class="btn-group ">
<button class="btn btn-xs btn-primary main-cancel-edit" value="<?=$post['uniq_id'];?>"><?=lang('PORTAL_cancel');?></button> 
<button class="btn btn-xs btn-success main-save-edit" value="<?=$post['uniq_id'];?>"><?=lang('PORTAL_save');?></button>
 </div>

</div>
<? } ?>
<div class="comment-bar"> <small class="subclass" style="display: none;">


</small></div>


</div>




                </div>

<div class="box-footer">
<small class="text-muted"><i class="fa fa-clock-o"></i> <time id="c" datetime="<?=$post['dt'];?>"></time> <a style="padding-left:20px;" href="#<?=$post['uniq_id'];?>"><i class="fa fa-link"></i> <?=lang('PORTAL_adr');?> </a> </small> <small style="padding-left:20px;"> <?=get_post_rate_post($post['uniq_id']);?></small>


<small class="text-muted pull-right"> <?php if ($validate == true) { ?> <?=view_likes_button($post['id']);?> <?php } ?> </small>
</div>

                </div>

<?php

            $stmt = $dbConnection->prepare('SELECT * from post_comments where p_id=:pid and official=1 order by dt asc');
            $stmt->execute(array(':pid' => $post['id']));
            $res1 = $stmt->fetchAll();
            if (!empty($res1)) {
 
?>

<div class="box box-success box-solid direct-chat direct-chat-primary">
                <div class="box-header with-border">
                  <h4 class="box-title"><?=lang('PORTAL_oa');?></h4>
<div class="box-tools pull-right">
                    
                  </div>
                </div><!-- /.box-header -->
<div class="box-body" style="display: block;">
                  <!-- Conversations are loaded here -->
                  <div class="direct-chat-messages">
                    <!-- Message. Default to the left -->

<?php

foreach ($res1 as $r) {
?>

                    <div class="direct-chat-msg">
                      <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name pull-left"><?=nameshort(name_of_user_ret_nolink($r['user_id']));?></span>
                        <span class="direct-chat-timestamp pull-right"></span>
                      </div><!-- /.direct-chat-info -->
                      <img class="direct-chat-img" src="<?php
    echo get_user_img_by_id($r['user_id']); ?>" alt="message user image"><!-- /.direct-chat-img -->
                      <div class="direct-chat-text" style="  background: #FCFCFC;
  border: 1px solid #FCFCFC;  margin-bottom: 15px;">
                        <div id="<?=$r['uniq_hash'];?>" value="comment">                  

<div class="editable_text">
                        <?=$r['comment_text'];?>
</div>





<?=view_attach_files($r['uniq_hash'], 'comment');?>
<br>
<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<div class="edit-bar" style="display: none;">
<div class="btn-group ">
<button class="btn btn-xs btn-primary cancel-edit" value="<?=$r['uniq_hash'];?>"><?=lang('PORTAL_cancel');?></button> 
<button class="btn btn-xs btn-success save-edit" value="<?=$r['uniq_hash'];?>"><?=lang('PORTAL_save');?></button>
 </div>

</div>
<?php } ?>
<div class="comment-bar"> <small class="subclass" style="display: none;">


<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<div class="btn-group pull-right">
<button class="btn btn-xs bg-maroon post-del" value="<?=$r['uniq_hash'];?>"><?=lang('PORTAL_act_del');?></button> 
<button class="btn btn-xs bg-orange btn-flat post-edit" value="<?=$r['uniq_hash'];?>"><?=lang('PORTAL_act_edit');?></button>
 </div>
 <?php } ?>
</small></div>
</div>
                      </div><!-- /.direct-chat-text -->
 </div>
<?php } ?>
  </div><!--/.direct-chat-messages-->


                </div>


<div class="box-footer">

<small class="text-muted"><i class="fa fa-clock-o"></i> 
<time id="c" datetime="<?=$r['dt'];?>"></time>
</small><small style="padding-left:20px;"><a href="#<?=$r['uniq_hash'];?>"><i class="fa fa-link"></i> <?=lang('PORTAL_adr');?> </a></small>
</div>

</div>

<?php 

}

?>




<?php

            $stmt = $dbConnection->prepare('SELECT * from post_comments where p_id=:pid and official=0  order by dt asc 
              limit :start_pos, :perpage');
            $stmt->execute(array(':pid' => $post['id'],
              ':start_pos' => $start_pos, ':perpage' => $perpage));
            $res1 = $stmt->fetchAll();

            if (!empty($res1)) {
 

?>


<div class="box box-default direct-chat direct-chat-primary">
                <div class="box-header with-border">
                  <h4 class="box-title"><?=lang('PORTAL_com');?></h4>
<div class="box-tools pull-right">
                    
                  </div>
                </div><!-- /.box-header -->
                




<div class="box-body" style="display: block;">
                  <!-- Conversations are loaded here -->
                  <div class="direct-chat-messages">
                    <!-- Message. Default to the left -->

<?php  


$h=0; 
foreach ($res1 as $r) { 
$line="<hr>";
if ($h == 0) { $line="";}


echo $line;
  ?>
                    <div class="direct-chat-msg">
                    <a name="<?=$r['uniq_hash'];?>"></a>
                      <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name pull-left"><?=nameshort(name_of_user_ret_nolink($r['user_id']));?></span>
                        <span class="direct-chat-timestamp pull-right">
                        <small><a href="#<?=$r['uniq_hash'];?>"><i class="fa fa-link"></i> <?=lang('PORTAL_adr');?> </a></small>
<small style="padding-left:10px;" class="text-muted"><i class="fa fa-clock-o"></i>
                        <time id="c" datetime="<?=$r['dt'];?>"></time>
</small>
                        </span>
                      </div><!-- /.direct-chat-info -->
                      <img class="direct-chat-img" src="<?php
    echo get_user_img_by_id($r['user_id']); ?>" alt="message user image"><!-- /.direct-chat-img -->
                      <div class="direct-chat-text" style="  background: #FCFCFC;
  border: 1px solid #FCFCFC;  margin-bottom: 15px;">
                       



     <div id="<?=$r['uniq_hash'];?>" value="comment">                  

<div class="editable_text">
                        <?=$r['comment_text'];?>
</div>





<?=view_attach_files($r['uniq_hash'], 'comment');?>
<br>
<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<div class="edit-bar" style="display: none;">
<div class="btn-group ">
<button class="btn btn-xs btn-primary cancel-edit" value="<?=$r['uniq_hash'];?>"><?=lang('PORTAL_cancel');?></button> 
<button class="btn btn-xs btn-success save-edit" value="<?=$r['uniq_hash'];?>"><?=lang('PORTAL_save');?></button>
 </div>

</div>
<?php } ?>
<div class="comment-bar"> <small class="subclass" style="display: none;">


<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<div class="btn-group pull-right">
<button class="btn btn-xs bg-maroon post-del" value="<?=$r['uniq_hash'];?>"><?=lang('PORTAL_act_del');?></button> 
<button class="btn btn-xs bg-orange btn-flat post-edit" value="<?=$r['uniq_hash'];?>"><?=lang('PORTAL_act_edit');?></button>
 </div>
 <?php } ?>
</small></div>
</div>
                      </div><!-- /.direct-chat-text -->
                    </div><!-- /.direct-chat-msg -->
<?php 
$h++;
} ?>





                  </div><!--/.direct-chat-messages-->


                </div>


<div class="box-footer">
                  <div class="row">
                    <div class="col-sm-12">
                    <ul class="nav nav-pills nav-stacked">
                      <li class="pull-right">


<ul id="comm_pages" class="pagination pagination-sm pull-right no-margin "></ul>



                    </li>
                    </ul>

            <input type="hidden" id="curent_page" value="<?=$p;?>">
            <input type="hidden" id="cur_page" value="<?=$p;?>">
            <input type="hidden" id="post" value="<?=$post['uniq_id'];?>">


            <input type="hidden" id="total_pages" value="<?php
        echo get_total_pages_comments($post['uniq_id']); ?>">


                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div>


                </div>


<?php
}

if ($validate == false) {
?>
<div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">

                    <center><?=lang('PORTAL_must_reg');?></center></div>
<?php
}

else if ($validate == true) {
?>







<div class="box box-default">
                <div class="box-header with-border">
                  <h4 class="box-title"><?=lang('PORTAL_add_comm');?></h4>
<div class="box-tools pull-right">
                    
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
<div class="row">
<div class="col-md-2" style="
    padding-right: 0px;
">
<center>
<img class="img-rounded"  src="<?php
        echo get_user_img(); ?>" alt="" height="120"><p>
                     <small> <?php echo nameshort(get_user_val('fio')); ?> </small>
                    </p>
</center>
        

<hr>

<?php 
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
?>
 <div class="checkbox">
                <center><label>
                
                    <input id="mc" name="remember_me" value="1" type="checkbox"> <br><?=lang('PORTAL_oa');?>
                
                </label></center>
            </div>

            <?php
}
else {
  ?>
<input id="mc" name="remember_me" value="0" type="hidden">
  <?php
}
            ?>
</div>
<div class="col-md-10" style="
    padding-left: 0px;
">


<div class="col-md-12">
<div id="notes"></div>
</div>
<div class="col-sm-12" >

<div class="text-muted well well-sm no-shadow" id="myid" >
  <div class="dz-message" data-dz-message>
<center class="text-muted"><?=lang('PORTAL_fileplace');?></center>
  </div>

<style type="text/css">
  .note-editor .note-dropzone { opacity: 0 !important; }
</style>

<form action="upload.php" class=""></form>

<div class="table table-striped" class="files" id="previews">
 
  <div id="template" class="file-row">
    <!-- This is used as the file preview template -->



<table class="table" style="margin-bottom: 0px;">
                  <tbody><tr>
                    <th style="width:50%"><p class="name" data-dz-name></p> </th>
                    <td><small class="text-muted"><p class="size" data-dz-size></p></small></td>
                    <td style="width:30%"><div class="progress progress-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
        </div></td>
                    <td class="pull-right"><button data-dz-remove class="btn btn-sm btn-danger delete">
        <i class="glyphicon glyphicon-trash"></i>
        <span>Delete</span>
      </button></td>
                  </tr>

                </tbody></table>










</div>
  </div>
 
</div>
</div>

<div class="col-md-12">
<br>
<input type="hidden" value="<?php echo md5(time()); ?>" id="comment_hash">
<input type="hidden" value="<?=$post['uniq_id']; ?>" id="post_hash">
<button class="btn btn-block btn-info" id="add_comment"><?=lang('PORTAL_stay_comm');?></button>

<div id="post_res"></div>
</div>




</div>

</div>
                </div>
                </div>




                <?php } ?>


</div>



<div class="col-md-3">











<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_author');?></h3>

                </div><!-- /.box-header -->
                <div class="box-body">
   <div class="row">
            <div class="col-md-12">
              
<div class="row">
                                <div class="col-md-4"><img class="img-rounded" src="<?php
    echo get_user_img_by_id($post['author_id']); ?>" height="60"></div>
                                <div class="col-md-8">
                                <center> <h4><?php
    echo get_user_val_by_id($post['author_id'], 'fio'); ?><br><small><?php echo get_user_val_by_id($post['author_id'], 'posada'); ?></small>
    </h4>

    </center>
                               </div>
                               </div>


            </div><!-- /.col -->
            
          </div>

                </div><!-- /.box-body -->

              </div>

<?php if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) { ?> 
<?=view_admin_menu($post['uniq_id']);?>
<?php } ?>


<?=view_maybe_block($post['uniq_id']);?>



<?=view_stat_cat();?>


</div>


          </div>
          <!-- info row -->
          
        </section>




</section>
</div>


<?php
include "footer.inc.php";
?>