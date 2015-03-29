

<?php

include "head.inc.php";


include "navbar.inc.php";


?>
<div class="content-wrapper">
<section class="content">





<section class="invoice">
          <!-- title row -->
          <div class="row">



<div class="col-md-12">
<div class="box box-default">
                <div class="box-header with-border">
                  <center><h3 class="box-title"><?=lang('PORTAL_auth');?></h3></center>
<div class="box-tools pull-right">
                   
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                <div class="row">
                <div class="col-sm-12">
<center><img src="<?=get_logo_img(); ?>" width="128"></center><br><hr>
</div>
<?php
if ($CONF['main_portal'] == true) {
$link="auth";
}
else if ($CONF['main_portal'] == false) {
    $link="index.php";
}
?>
<form class="form-horizontal" action="<?php echo $CONF['hostname'] . $link; ?>" method="POST" autocomplete="off">


<div class="col-sm-4 col-sm-offset-4">
<input type="text" name="login" autocomplete="off" autocapitalize="off" autocorrect="off" class="form-control input-lg" placeholder="<?php echo lang('login'); ?>"/><br>
</div>

<div class="col-sm-4 col-sm-offset-4">
 <input type="password" name="password" class="form-control input-lg" placeholder="<?php echo lang('pass'); ?>"/>
</div>

<div class="col-sm-4 col-sm-offset-4">
 <div class="form-group">




                        <div class="checkbox">
                <center><label>
                
                    <input id="mc" name="remember_me" value="1" type="checkbox"> <?php echo lang('remember_me'); ?>
                
                </label></center>
            </div>
                    </div>
</div>

<div class="col-sm-4 col-sm-offset-4">
  <button class="btn btn-block btn-success btn-lg"><i class="fa fa-sign-in"></i>  <?php echo lang('log_in'); ?> </button>
</div>

<div class="col-sm-12" id="error_result">


<?php
if ($va == 'error') { ?>
            <div class="alert alert-danger" style="margin:20px;">
                <center><?php echo lang('error_auth'); ?></center>
            </div> <?php
} ?>
                    </div>
                </div>
                <input type="hidden" name="req_url" value="<?php
echo $_SERVER['REQUEST_URI']; ?>">
</form>

                </div><!-- /.footer -->
                </div>
                </div>

</div>






          </div>
          <!-- info row -->
          
        </section>




</section>
</div>


<?php
include "footer.inc.php";
?>