<?php
include_once("head.inc.php");
//include("dbconnect.inc.php");
?>


<body class="bg-navy">

        <div class="form-box" id="login-box">
            <div class="header bg-light-blue" style="">
            <center><img src="<?=$CONF['hostname']?>img/helpdesk-logo.png" width="128"></center>
            <?=lang('MAIN_TITLE');?></div>
            <form class="form-signin" autocomplete="off" id="main_form_register">
                <div class="body bg-gray">
                <div id="error_result"></div>
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder="<?=lang('USERS_fio_full');?>" id="login_fio">
                    </div>
                                        <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder="<?=lang('USERS_login');?>" id="login_name">
                    </div>
                    <div class="form-group">
                        <input type="text" name="userid" class="form-control" placeholder="E-mail" id="login_mail">
                    </div>

                </div>
                <div class="footer bg-gray">                                                               
                    <button id="register_new" class="btn btn-success btn-block"><i class="fa fa-check-circle"></i>  <?=lang('REG_button');?></button>  
                   <center> <a href="index.php" class="text-center"><?=lang('REG_already');?></a></center>
                    <!--p>Используйте Ваши LDAP-учётные данные для входа</p-->
                    <?php if ($va == 'error') { ?>
            <div class="alert alert-danger">
                <center><?=lang('error_auth');?></center>
            </div> <?php } ?>
                    
                </div>
                <input type="hidden" name="req_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
            </form>

            
        </div>















</div>

<script src="<?=$CONF['hostname']?>js/jquery-1.11.0.min.js"></script>
<script src="<?=$CONF['hostname']?>js/bootstrap/js/bootstrap.min.js"></script>
<script src="<?=$CONF['hostname']?>js/app.js"></script>
<script>
$(document).ready(function() {
		    $('body').on('click', 'button#register_new', function(event) {
		    event.preventDefault();
		    	$.ajax({
                    type: "POST",
                    url: "actions.php",
                    dataType: "json",
                    data: "mode=register_new"+
                        "&fio="+$('#login_fio').val()+
                        "&login="+$('#login_name').val()+
                        "&mail="+$('#login_mail').val(),
                    success: function(html){
                    	
                    	if (html) {
                       		 $.each(html, function(i, item) {
                        		if (item.check_error == "true") {
	                        		$("#main_form_register").html(item.msg);
	                        		setTimeout(function() {window.location = "./";}, 5000);
                        		}
                        		else if (item.check_error == "false") {
                        			$("#error_result").html(item.msg);
                        		}
                        	});
                        	
                        	}

					   		 }
                    });
        
        });
});
</script>

