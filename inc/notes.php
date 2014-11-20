<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
?>




<section class="content-header">
                    <h1>
                        <i class="fa fa-book"></i> <?php echo lang('NOTES_title'); ?>
                        <small><?php echo lang('NOTES_title_ext'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('NOTES_title'); ?></li>
                    </ol>
                </section>



<section class="content">


<div class="row">

<div class="col-md-3">
<button id="create_new_note" type="submit" class="btn btn-success btn-sm btn-block"><i class="fa fa-file-o"></i> <?php echo lang('NOTES_create'); ?></button>
<br>
      <div class="">
      <div class="">
      <div id="table_list" style="margin-bottom: 0px; margin-bottom: 0px;">
  
</div>
      </div></div>
  
</div>
<div class="col-md-9">
  <div class="box box-solid">
      <div class="box-body">
    <div id="summernote">
                           <div class="text-muted well well-sm no-shadow">
  <p>                <center>
                    <?php echo lang('NOTES_cr'); ?>
                </center></p>
  
</div>
  </div>
  <div id="re">
  </div>
      </div></div>
</div>


<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><?php echo lang('NOTES_link'); ?></h4>
      </div>
      <div class="modal-body">
        <form role="form">
  <div class="form-group">
    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="" value="">
  </div>
        </form>
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
    include 'auth.php';
}
?>