<?php
session_start();
include ("../functions.inc.php");

if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {

                   $CONF['title_header'] = lang('CALENDAR_title') . " - " . $CONF['name_of_firm'];



        include ("head.inc.php");
        include ("navbar.inc.php");


        
?>




<section class="content-header">
                    <h1>
                        <i class="fa fa-calendar"></i> <?php echo lang('CALENDAR_title'); ?>
                        <small><?php echo lang('CALENDAR_title_desc'); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                       <li><a href="<?php echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active"><?php echo lang('CALENDAR_title'); ?></li>
                    </ol>
                </section>



        <section class="content">
          <div class="row">
            <div class="col-md-3">
              <div class="box box-solid">
                <div class="box-header with-border">
                  <h4 class="box-title"><?=lang('CALENDAR_dr_ev');?></h4>
                </div>
                <div class="box-body">
                  <!-- the events -->
                  <div id='external-events'>
                    <div class='external-event bg-green'> <?=lang('CALENDAR_ex_ev_3');?></div>
                    <div class='external-event bg-yellow'><?=lang('CALENDAR_ex_ev_2');?></div>
                    <div class='external-event bg-aqua'>  <?=lang('CALENDAR_ex_ev_4');?></div>
                    <div class='external-event bg-light-blue'><?=lang('CALENDAR_ex_ev_5');?></div>
                    <div class='external-event bg-red'>   <?=lang('CALENDAR_ex_ev_1');?></div>
                    <div class="checkbox">
                      <label for='drop-remove'>
                        <input type='checkbox' id='drop-remove' />
                        <?=lang('CALENDAR_del_after_drag');?>
                      </label>
                    </div>
                  </div>
                </div><!-- /.box-body -->
              </div><!-- /. box -->
              <div class="box box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('CALENDAR_create_event');?></h3>
                </div>
                <div class="box-body">
                  <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                    <!--<button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Color <span class="caret"></span></button>-->
                    <ul class="fc-color-picker" id="color-chooser">
                      <li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-blue" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>                                           
                      <li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-green" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-lime" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-red" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-muted" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>
                    </ul>
                  </div><!-- /btn-group -->
                  <div class="input-group">
                    <input id="new-event" type="text" class="form-control" placeholder="<?=lang('CALENDAR_name');?>">
                    <div class="input-group-btn">
                      <button id="add-new-event" type="button" class="btn btn-primary btn-flat"><?=lang('CALENDAR_add')?></button>
                    </div><!-- /btn-group -->
                  </div><!-- /input-group -->
                </div>
              </div>





              <div class="box box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title"><?=lang('CALENDAR_filter');?></h3>
                </div>
                <div class="box-body">



<form class="form-horizontal" role="form">
<div class="form-group">
  
  <div class="col-sm-12">
    <div class="checkbox">
    <label>
      <input type="checkbox" class="make_event_filter" value="0" checked> <small><?=lang('CALENDAR_private');?></small>
      
    </label>
  </div>
  </div>
    <div class="col-sm-12">
    <div class="checkbox">
    <label>
      <input type="checkbox" class="make_event_filter" value="1" checked> <small><?=lang('CALENDAR_dep');?></small>
      
    </label>
  </div>
  </div>
    <div class="col-sm-12">
    <div class="checkbox">
    <label>
      <input type="checkbox" class="make_event_filter" value="2" checked> <small><?=lang('CALENDAR_corp');?></small>
      
    </label>
  </div>
  </div>
</div>

<input type="hidden" id="filter_events" value="0,1,2">
</form>


                </div>
              </div>










            </div><!-- /.col -->
            <div class="col-md-9">
              <div class="box box-primary">
                <div class="box-body no-padding">
                  <!-- THE CALENDAR -->
                  <div id="calendar"></div>




<div class="modal fade" id="event_modal_info">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?=lang('CALENDAR_event');?></h4>
      </div>
      <div class="modal-body">
      <form class="form-horizontal" role="form">


              <div class="form-group">
    <label for="event_name" class="col-sm-2 control-label"><small><?=lang('CALENDAR_name');?></small></label>
        <div class="col-sm-10" id="ei_name">
   
        </div>
  </div>

              <div class="form-group">
    <label for="event_name" class="col-sm-2 control-label"><small><?=lang('CALENDAR_description');?></small></label>
        <div class="col-sm-10" id="ei_desc">
   
        </div>
  </div>



              <div class="form-group">
    <label for="event_name" class="col-sm-2 control-label"><small><?=lang('CALENDAR_period');?></small></label>
        <div class="col-sm-10" id="ei_period">
   
        </div>
  </div>

                <div class="form-group">
    <label for="event_name" class="col-sm-2 control-label"><small><?=lang('CALENDAR_author');?></small></label>
        <div class="col-sm-10" id="ei_author">
   
        </div>
  </div>



  </form>
      </div>
<div class="modal-footer">
      

        <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('CALENDAR_close');?></button>
        

        <input type="hidden" id="current_event_hash" value="">
        <input type="hidden" id="current_backgroundColor" value="">
        <input type="hidden" id="current_borderColor" value="">

        <input type="hidden" id="current_start" value="">
        <input type="hidden" id="current_end" value="">
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->







<div class="modal fade" id="event_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?=lang('CALENDAR_edit_event');?></h4>
      </div>
      <div class="modal-body">
        

<form class="form-horizontal" role="form">


              <div class="form-group">
    <label for="event_name" class="col-sm-2 control-label"><small><?=lang('CALENDAR_name');?></small></label>
        <div class="col-sm-10">
    <input autocomplete="off" name="event_name" type="text" class="form-control input-sm" id="event_name" placeholder="<?=lang('CALENDAR_name');?>">
        </div>
  </div>


              <div class="form-group">
    <label for="event_desc" class="col-sm-2 control-label"><small><?=lang('CALENDAR_description');?></small></label>
        <div class="col-sm-10">
   <textarea class="form-control input-sm" rows="3" name="event_desc" id="event_desc" placeholder="<?=lang('CALENDAR_description');?>"></textarea>
        </div>
  </div>


<div class="form-group">
  <label for="all_day" class="col-sm-2 control-label"><small><?=lang('CALENDAR_allday');?></small></label>
  <div class="col-sm-10">
    <div class="checkbox">
    <label>
      <input type="checkbox" id="all_day" value=""> <small><?=lang('CALENDAR_allday_desc');?></small>
      
    </label>
  </div>
  </div>
</div>


<div class="form-group">
<label for="period" class="col-sm-2 control-label"><small><?=lang('CALENDAR_period');?></small></label>
  <div class="col-sm-10">
  <input type="text" name="reservation" id="reservation" class="form-control input-sm"  value="" />
    
  </div>

</div>




    <div class="form-group">
    <label for="visibility" class="col-sm-2 control-label"><small><?=lang('CALENDAR_visibility');?></small></label>
    <div class="col-sm-10">
  <select class="form-control input-sm" id="visibility">
  <option value="0"><?=lang('CALENDAR_e_1');?></option>
  <option value="1"><?=lang('CALENDAR_e_2');?></option>
  <option value="2"><?=lang('CALENDAR_e_3');?></option>
</select>   </div>
  </div>

<div class="form-group">
<label for="visibility" class="col-sm-2 control-label"><small><?=lang('CALENDAR_color');?></small></label>
<div class="col-sm-10">

<span id="cur_color_event" class="label"><?=lang('CALENDAR_cur_color');?></span><br><br>
<div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                    <!--<button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Color <span class="caret"></span></button>-->
                    <ul class="fc-color-picker" id="color-chooser_event">
                      <li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-blue" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>                                           
                      <li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-green" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-lime" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-red" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-muted" href="#"><i class="fa fa-square"></i></a></li>
                      <li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>
                    </ul>
                  </div><!-- /btn-group -->
                  
</div>
</div>







</form>


      </div>
      <div class="modal-footer">
      <button id="cal_delete_current" class="btn btn-danger btn-sm pull-left"><?=lang('CALENDAR_del');?></button>

        <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('CALENDAR_close');?></button>
        <button type="button" class="btn btn-primary" id="event_save_action"><?=lang('CALENDAR_save');?></button>

        <input type="hidden" id="current_event_hash" value="">
        <input type="hidden" id="current_backgroundColor" value="">
        <input type="hidden" id="current_borderColor" value="">

        <input type="hidden" id="current_start" value="">
        <input type="hidden" id="current_end" value="">
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


                </div><!-- /.box-body -->
              </div><!-- /. box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->


        


<?php
        include ("footer.inc.php");
?>


<?php
    }
} else {
    include 'auth.php';
}
?>