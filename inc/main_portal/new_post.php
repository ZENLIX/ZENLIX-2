<?php

session_start();


$validate = false;
if ((validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) || (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code']))) {
$validate = true;
}

if ($validate == true) {


include "head.inc.php";


include "navbar.inc.php";









if ($_GET['session_key']) {

if ($_SESSION['zenlix_portal_post']) {
  $subj=$_SESSION['zenlix_portal_post'];
}

}



switch ($_GET['p']) {
  case '1':
    $type['1']="selected";
    break;
      case '2':
    $type['2']="selected";
    break;
      case '3':
    $type['3']="selected";
    break;
      case '4':
    $type['4']="selected";
    break;
  
  default:
    $type['1']="selected";
    break;
}


?>
<div class="content-wrapper">
<section class="content">

<style type="text/css">
  .note-editor .note-dropzone { opacity: 0 !important; }
</style>





<section class="invoice">
          <!-- title row -->
          <div class="row">



<div class="col-md-9">
<div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('PORTAL_new_msg');?></h3>
<div class="box-tools pull-right">
                    <h4> <i class="fa fa-comment"></i></h4>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                <div class="">


 <form class="form-horizontal">
  <div class="form-group">
    
    <div class="col-sm-9">
      <input type="text" class="form-control" id="subj" placeholder="<?=lang('PORTAL_subj');?>" value="<?=$subj;?>">
    </div>
        <div class="col-sm-3">
      <select class="form-control" id="type">
                        <option <?=$type['1'];?> value="1"><?=lang('PORTAL_idea_one');?> </option>
                        <option <?=$type['2'];?> value="2"><?=lang('PORTAL_trouble_one');?></option>
                        <option <?=$type['3'];?> value="3"><?=lang('PORTAL_question_one');?></option>
                        <option <?=$type['4'];?> value="4"><?=lang('PORTAL_thank_one');?></option>
                        
                      </select>
    </div>
  </div>

    <div class="form-group">
    
<div class="col-sm-12">
<div id="note"></div>
</div>
<div class="col-sm-12" >

<div class="text-muted well well-sm no-shadow" id="myid" >
  <div class="dz-message" data-dz-message>
<center class="text-muted"><?=lang('PORTAL_fileplace');?></center>
  </div>



<form action="upload.php" class="dropzone"></form>

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

<div class="col-sm-12" id="post_res">



</div>



<div class="col-sm-6 col-sm-offset-3">
<br>
<button class="btn btn-block btn-info" id="make_new_post_data" ><?=lang('PORTAL_news_create');?></button>
<input type="hidden" value="<?php echo md5(time()); ?>" id="post_hash">
</div>

  </div>


  </form>



                </div><!-- /.footer -->
                </div>
                </div>

</div>



<div class="col-md-3">











<?=view_stat_cat();?>


</div>


          </div>
          <!-- info row -->
          
        </section>




</section>
</div>


<?php
include "footer.inc.php";
}
else if ($validate == false) {

    header("Location: " . site_proto() . get_conf_param('hostname') . "auth");
}

?>