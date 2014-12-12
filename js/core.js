//var my_errors = {fio: false, login: false, pass: false};
$(document).ready(function() {
	$.ajaxSetup ({
    // Disable caching of AJAX responses
    cache: false
});


    var socket = io.connect(location.protocol + '//' + show_hostname(MyHOSTNAME) + ':' + NODE_PORT, {
        secure: true
    });
    socket.emit('join', {
        uniq_id: USER_HASH
    });

    //push_msg_action2user
    socket.on("new_msg", function(data) {

        switch (data.type_op) {
            case 'ticket_create':
                active_noty_msg('ticket_create', data.t_id);
                update_labels();
                if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    update_page_dashboard();
                    makemytime(true);
                    update_dashboard_labels();
                };
                if (ispath('list')) {
                    update_list_page_content();
                    makemytime(true);
                };
                break;
            case 'ticket_refer':
                active_noty_msg('ticket_refer', data.t_id);
                update_labels();
                if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    update_page_dashboard();
                    makemytime(true);
                    update_dashboard_labels();
                };
                if (ispath('list')) {
                    update_list_page_content();
                    makemytime(true);
                };
                if (ispath('ticket')) {
                    update_ticket_page(data.t_id);
                };
                break;
            case 'ticket_ok':
                active_noty_msg('ticket_ok', data.t_id);
                if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    update_page_dashboard();
                    makemytime(true);
                    update_labels();
                    update_dashboard_labels();
                };
                if (ispath('list')) {
                    update_list_page_content();
                    makemytime(true);
                    update_labels();
                };
                if (ispath('ticket')) {
                    update_ticket_page(data.t_id);
                    update_labels();
                };
                break;
            case 'ticket_no_ok':
                active_noty_msg('ticket_no_ok', data.t_id);
                if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    update_page_dashboard();
                    makemytime(true);
                    update_labels();
                    update_dashboard_labels();
                };
                if (ispath('list')) {
                    update_list_page_content();
                    makemytime(true);
                    update_labels();
                };
                if (ispath('ticket')) {
                    update_ticket_page(data.t_id);
                    update_labels();
                };
                break;
            case 'ticket_lock':
                active_noty_msg('ticket_lock', data.t_id);
                if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    update_page_dashboard();
                    makemytime(true);
                    update_dashboard_labels();
                };
                if (ispath('list')) {
                    update_list_page_content();
                    makemytime(true);
                };
                if (ispath('ticket')) {
                    update_ticket_page(data.t_id);
                };
                break;
            case 'ticket_unlock':
                active_noty_msg('ticket_unlock', data.t_id);
                if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    update_page_dashboard();
                    makemytime(true);
                    update_dashboard_labels();
                };
                if (ispath('list')) {
                    update_list_page_content();
                    makemytime(true);
                };
                if (ispath('ticket')) {
                    update_ticket_page(data.t_id);
                };
                break;
            case 'ticket_comment':
                active_noty_msg('ticket_comment', data.t_id);
                if (ispath('ticket')) {
                    if ($('#ticket_id').val() == data.t_id) {
                        get_comments(data.t_id);
                        makemytime(true);
                    }
                }
                break;
            case 'message_send':
                //console.log(data.chat_id);
                if (!ispath('messages')) {
                    noty_message(data.chat_id);
                }
                if (ispath('messages')) {
                    messages_update_window(data.t_id);
                }
                update_labels_msg();
                show_bar_unread_msg();
                if (ispath('messages')) {
                    refresh_message_usr_list();
                }
                //console.log('yes');
                break;
            case 'logout':
                window.location = MyHOSTNAME + "index.php?logout";
                break;
        };
            });
    moment.lang(MyLANG);
    var my_errors = {
        fio: false,
        login: false,
        pass: false
    };
    var ACTIONPATH = MyHOSTNAME + "actions.php";
    /*
###############################################
 FUNCTIONS begin
###############################################
*/
    function active_noty_msg(type_op, ticket_id) {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_noty_actions" + "&type=" + type_op + "&ticket_id=" + ticket_id,
            dataType: "json",
            success: function(html) {
                //console.log(html);
                if (html) {
                    $.each(html, function(i, item) {
                        var t = '<div style=\'float: left;\'><a style=\'color: rgb(243, 235, 235); cursor: inherit;\' target=\'_blank\' href=\'' + item.url + '/ticket?' + item.hash + '\'><strong>' + item.ticket + ' #' + item.name + '</strong> </a></div><div style=\'float: right; padding-right: 10px;\'><small>' + item.time + '</small></div><br><hr style=\'margin-top: 5px; margin-bottom: 8px; border:0; border-top:0px solid #E4E4E4\'><em style=\'color: rgb(252, 252, 252); cursor: inherit;\'>' + item.at + '</em>';
                        noty({
                            text: t,
                            layout: 'bottomRight',
                            timeout: false
                        });
                        $.ionSound.play("button_tiny");
                        $.titleAlert(item.up);
                        makemytime(true);
                    });
                }
            }
        });
    };

    function noty_message(msg_id) {

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_chat_message" + "&msg_id=" + msg_id,
            dataType: "json",
            success: function(html) {
                //console.log(html);
                if (html) {
                    $.each(html, function(i, item) {
                        var t = '<div style=\'float: left;\'><a style=\'color: rgb(243, 235, 235); cursor: inherit;\' target=\'_blank\' href=\'messages?to=' + item.uniq_id + '\'><strong><i class=\'fa fa-comments\'></i> ' + item.new_msg_text + '</strong> </a></div><div style=\'float: right; padding-right: 10px;\'><small>' + item.time_op + '</small></div><br><hr style=\'margin-top: 5px; margin-bottom: 8px; border:0; border-top:0px solid #E4E4E4\'><strong>' + item.user_from + ':</strong><em style=\'color: rgb(252, 252, 252); cursor: inherit;\'> ' + item.user_chat + '</em>';
                        noty({
                            text: t,
                            layout: 'bottomRight',
                            timeout: false
                        });
                        $.ionSound.play("button_tiny");
                        makemytime(true);
                    });
                }
            }
        });
    };

    function messages_update_window(user_id) {
        var tuser = $('#target_user').val();
        //console.log(user_id+' = '+tuser);
        if (user_id == tuser) {
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=messages_view" + "&target=" + tuser,
                success: function(html) {
                    $("#content_chat").html(html);
                    makemytime(true);
                    var scroll = $('#content_chat');
                    var height = scroll[0].scrollHeight;
                    scroll.scrollTop(height);
                }
            });
        }
    };

    function show_hostname(url) {
        var a = document.createElement('a');
        a.href = url;
        var hostname = a.hostname;
        return hostname;
    }

    function make_popover() {
        $('.pops').popover({
            html: true,
            trigger: 'manual',
            container: $(this).attr('id'),
            placement: 'right',
            content: function() {
                $return = '<div class="hover-hovercard"></div>';
            }
        }).on("mouseenter", function() {
            var _this = this;
            $(this).popover("show");
            $(this).siblings(".popover").on("mouseleave", function() {
                $(_this).popover('hide');
            });
        }).on("mouseleave", function() {
            var _this = this;
            setTimeout(function() {
                if (!$(".popover:hover").length) {
                    $(_this).popover("hide")
                }
            }, 100);
        });
    };

    function makemytime(s) {
        var now = moment().zone("+04:00");
        //.zone("+08:00")




        
               $('time#a').each(function(i, e) {
            var time = moment($(e).attr('datetime'));
            
            
            $(e).html('<span>' + time.from(now) + '</span>');
        });
        
        $('time#b').each(function(i, e) {
            var time = moment($(e).attr('datetime'));
            $(e).html('<span>' + time.from(now) + '</span>');
        });
        
        
        $('time#c').each(function(i, e) {
            var time = moment($(e).attr('datetime'));
            $(e).html('<span>' + time.format("ddd, Do MMM, H:mm:ss") + '</span>');
        });
        $('time#d').each(function(i, e) {
            var time = moment($(e).attr('datetime'));
            $(e).html('<span>' + time.format("dddd, Do MMMM") + '</span>');
        });
    };

    function get_host_conf() {
        var result = "";
        var zcode = "";
        var url = window.location.href;
        if (url.search("inc") >= 0) {
            zcode = "../";
        }
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_host_conf",
            async: false,
            success: function(html) {
                result = html;
            }
        });
        return (result);
    };

    function format(state) {
        var originalOption = state.element;
        return "<i class='fa fa-user status_icon_" + $(originalOption).data('foo') + "'></i> " + state.text;
    };

    function sendFile(file, editor, welEditable) {
        data = new FormData();
        data.append("file", file);
        $.ajax({
            data: data,
            type: "POST",
            url: MyHOSTNAME + "sys/up_summernote.php",
            cache: false,
            contentType: false,
            processData: false,
            success: function(url) {
                editor.insertImage(welEditable, url);
            }
        });
    };

    function get_lang_param(par) {
        var result = "";
        var zcode = "";
        var url = window.location.href;
        if (url.search("inc") >= 0) {
            zcode = "../";
        }
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_lang_param" + "&param=" + encodeURIComponent(par),
            async: false,
            success: function(html) {
                result = html;
            }
        });
        return (result);
    };

    function createuserslist(unit_id, target_id) {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_users_list" + "&unit=" + encodeURIComponent(unit_id),
            dataType: "json",
            success: function(html) {
                $('select#' + target_id).empty();
                if (html) {
                    $('select#' + target_id).append($("<option></option>"));
                    $.each(html, function(i, item) {
                        $('select#' + target_id).append($("<option></option>").attr("value", item.co).attr("data-foo", item.stat).text(item.name));
                    });
                }

                $('select#'+target_id).trigger('change');
                //$('select#'+target_id).trigger('chosen:updated');
            }
        });
    };

    function ispath(p1) {
        var url = window.location.href;
        var zzz = false;
        if (url.search(p1) >= 0) {
            zzz = true;
        }
        return zzz;
    };

    function update_status_time() {
        setInterval(function() {
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=update_status_time",
                success: function() {
                    makemytime(true);
                }
            });
        }, 60000);
    };

    function check_update() {
        makemytime(true);
        var ee = $("#main_last_new_ticket").val();
        var url = window.location.href;
        var zcode = "";
        if (url.search("inc") >= 0) {
            var zcode = "../";
        }
        /* if (ee) {
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=check_update"+
                    "&type=all"+
                    "&last_update="+encodeURIComponent(ee),
                success: function(html){
                    if (html == "no") {
                    }
                    else {
                        var new_lu=html;
                        $.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=list_ticket_update"+
                                "&last_update="+encodeURIComponent(ee),
                            dataType: "json",
                            success: function(html){
                                if (html) {
                                    $.each(html, function(i, item) {
                                        var t='<div style=\'float: left;\'><a style=\'color: rgb(243, 235, 235); cursor: inherit;\' target=\'_blank\' href=\''+item.url+'/ticket?'+item.hash+'\'><strong>'+item.ticket+' #'+item.name+'</strong> </a></div><div style=\'float: right; padding-right: 10px;\'><small>'+item.time+'</small></div><br><hr style=\'margin-top: 5px; margin-bottom: 8px; border:0; border-top:0px solid #E4E4E4\'><a style=\'color: rgb(252, 252, 252); cursor: inherit;\' target=\'_blank\' href=\''+item.url+'/ticket?'+item.hash+'\'>'+item.at+'</a>';
                                        noty({
                                            text: t,
                                            layout: 'bottomRight',
                                            timeout: false
                                        });
                                        $.ionSound.play("button_tiny");
                                        $.titleAlert(item.up);
                                        makemytime(true);
                                    });
                                }
                                $("#main_last_new_ticket").attr('value', new_lu);
                            }
                        });
                    }
                }});
        }*/
    };

    function update_ticket_page(t_id) {
        var t_hash = $('#ticket_hash').val();
        var tic_id = $('#ticket_id').val();
        //  ticket_id
        if (t_id == tic_id) {
            setInterval(function() {
                if ($('input#msg:focus').length == 0) {
                    window.location = MyHOSTNAME + "ticket?" + t_hash + "&refresh";
                }
            }, 1000);
        }
    }

    function get_comments(ticket_id) {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=view_comment" + "&tid=" + ticket_id,
            success: function(html) {
                $("#comment_content").html(html);
                //$("input#msg").val('');
                makemytime(true);
                //comment_body
                var scroll = $('#comment_body');
                var height = scroll[0].scrollHeight;
                scroll.scrollTop(height);
                //console.log(height);
            }
        });
    };

    function update_labels() {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_tt_label",
            success: function(html) {
                $('#tt_label').html(html);
            }
        });
        //get_tt_label
    }

    function update_labels_msg() {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=recalculate_messages",
            success: function(html) {
                $('#label_msg').html(html);
            }
        });
        //get_tt_label
    }

    function update_dashboard_labels() {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=update_dashboard_labels",
            dataType: "json",
            success: function(html) {
                if (html) {
                    $.each(html, function(i, item) {
                        $('h3#tool_1').html(item.tool1);
                        $('h3#tool_2').html(item.tool2);
                        $('h3#tool_3').html(item.tool3);
                        $('h3#tool_4').html(item.tool4);
                    });
                }
            }
        });
    };

    function update_list_page_content() {
        var oo = $("#curent_page").attr('value');
        var pt = $("#page_type").attr('value');
        $.ajax({
            type: "POST",
            url: MyHOSTNAME + "inc/list_content.inc.php",
            data: "menu=" + encodeURIComponent(pt) + "&page=" + encodeURIComponent(oo),
            success: function(html) {
                $('[data-toggle="tooltip"]').tooltip('hide');
                $("#content").html(html);
                $('[data-toggle="tooltip"]').tooltip({
                    container: 'body',
                    html: true
                });
            }
        });
        /*
                        $.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=update_list_labels",
                            dataType: "json",
                            success: function(html){
                                if (html) {
                                    $.each(html, function(i, item) {
                                        $('span#label_list_in').html(item.in);
                                        $('span#label_list_out').html(item.out);

                                    });
                                }
                            }
                        });
                              */
    };

    function update_page_dashboard() {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=dashboard_t",
            success: function(html) {
                $('#dashboard_t').html(html);
                $('#spinner').hide();
                $('[data-toggle="tooltip"]').tooltip('hide');
                $('[data-toggle="tooltip"]').tooltip({
                    container: 'body',
                    html: true
                });
                makemytime(true);
            }
        });
    };

    function refresh_message_usr_list() {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=message_user_list",
            success: function(html) {
                $("#user_list").html(html);
            }
        });
    };

    function show_bar_unread_msg() {
        $('#unread_msg').show;
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=view_unread_msgs_labels",
            success: function(html) {
                $('.label_unread_msg').html(html);
            }
        });
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=view_unread_msgs_total",
            success: function(html) {
                $('#nav_t_msgs').html(html);
            }
        });
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=view_unread_msgs",
            success: function(html) {
                $('#unread_msgs_content').html(html);
                makemytime(true);
            }
        });
        //label_unread_msg
        //unread_msgs_content
        //view_unread_msgs_labels
        //view_unread_msgs
    };

    function check_main_msgs() {
        var total_msgs_main = $('#total_msgs_main').val();
        var targ = $('#target_user').val();
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=total_msgs_main",
            success: function(html) {
                if (total_msgs_main != html) {
                    if (targ == "main") {
                        $.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=messages_view" + "&target=main",
                            success: function(html) {
                                $("#content_chat").html(html);
                                makemytime(true);
                                var scroll = $('#content_chat');
                                var height = scroll[0].scrollHeight;
                                scroll.scrollTop(height);
                            }
                        });
                    }
                }
            }
        });
    };
    /*
###############################################
 FUNCTIONS end
###############################################
*/
    /*
###############################################
 MAIN start options begin
###############################################
*/
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body',
        html: true
    });
    var settingsShow = function() {
        var showPanel = $(".chosen-select").find('option:selected').attr('id');
    }
    $(".chosen-select").chosen({
        no_results_text: get_lang_param('JS_not_found'),
        allow_single_deselect: true
    });
    $(".chosen-select").chosen().change(settingsShow);
    $("#spinner").hide();
    $.ionSound({
        sounds: ["button_tiny"]
    });
    var def_p = window.location.pathname.split("/");
    var def_filename = def_p[def_p.length - 1];
    $.noty.defaults = {
        layout: 'top',
        theme: 'relax',
        type: 'information',
        text: '',
        closeButton: true,
        dismissQueue: true,
        template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"><i class="fa fa-times"></i></div></div>',
        
        animation: {
            open: {
                height: 'toggle'
            },
            close: {
                height: 'toggle'
            },
            easing: 'swing',
            speed: 500
        },
        timeout: false,
        force: false,
        modal: false,
        maxVisible: 5,
        killer: false,
        closeWith: ['button'],
        callback: {
            onShow: function() {},
            afterShow: function() {},
            onClose: function() {},
            afterClose: function() {}
        },
        buttons: false
    };
    makemytime(true);
    make_popover();
    update_status_time();
    $('body').on('click', '#show_online_users', function(event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=show_online_users",
            success: function(html) {
                $('#online_users_content').html(html);
            }
        });
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=count_online_users",
            success: function(html) {
                $('.online_users_label').html(html);
            }
        });
    });
    //show_online_users
    /*
###############################################
 MAIN start options end
###############################################
*/
    //
    
    if (ispath('scheduler')) {
	    
	    
	    
	    
	            $('body').on('click', 'button#cron_delete', function(event) {
            event.preventDefault();
            var ids = $(this).attr('value');
            bootbox.confirm(get_lang_param('JS_del'), function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=cron_del" + "&id=" + ids,
                        success: function(html) {
                            //$("#content_deps").html(html);
								window.location = MyHOSTNAME + "scheduler";
                        }
                    });
                }
                if (result == false) {
                    console.log('false');
                }
            });
            /*
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=deps_del"+
                "&id="+$(this).attr('value'),
            success: function(html) {
                $("#content_deps").html(html);

            }
        });
        */
        });
        
        
        
	    
	    //add_scheduler
	    $('body').on('click', 'button#add_scheduler', function(event) {
            event.preventDefault();
            
            
            
            $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        dataType: "json",
                        data: "mode=add_cron"+
                        "&client_id_param="+$("#client_id_param").val()+
                        "&to="+$("#to").val()+
                        "&s2id_users_do="+$("#users_do").val()+
                        "&prio="+$("#prio").val()+
                        "&subj="+$("#subj").val()+
                        "&msg="+$("#msg").val()+
                        "&period="+$("#period").val()+
                        "&day_field="+$("#day_field").val()+
                        "&week_select="+$("#week_select").val()+
                        "&month_select="+$("#month_select").val()+
                        "&time_action="+$("#time_action").val()+
                        "&action_start="+$("#action_start").val()+
                        "&action_stop="+$("#action_stop").val()+
                        "&status_action="+$("#status_action").val()
                        
                        ,
                        success: function(html) {
                            //console.log(html);
                            $.each(html, function(i, item) {
                            if (item.check_error == true) {
	                            window.location = MyHOSTNAME + "scheduler";
	                           }
	                        else if (item.check_error == false) {
		                        $("#error_content").html(item.msg);
	                           }   
	                           });
	                            
                            
                        }
                    });
            
            
            });
	    
	    
	    $('.timepicker').timepicker({
                    showInputs: false,
                    minuteStep: 1,
                showSeconds: true,
                showMeridian: false
                });
	    
	    
	            $('#reservation').on('apply.daterangepicker', function(ev, picker) {
            $("#action_start").val( picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
            $("#action_stop").val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
            
            
        });
        $('#reservation').daterangepicker({
            format: 'YYYY-MM-DD HH:mm:ss',timePicker: true,timePicker12Hour: false
        });
	   
	    //$('button#btn_period_day').addClass('active');
	            $("#period_week").hide();
            	$("#period_month").hide();
            	
            	
	    $('body').on('click', 'button#btn_period_day', function(event) {
            event.preventDefault();
            $('button#btn_period_day').addClass('active');
            $('button#btn_period_week').removeClass('active');
            $('button#btn_period_month').removeClass('active');
            	$("#period_day").show();
            	$("#period_week").hide();
            	$("#period_month").hide();
            	
            	$("#period").val('day');
            });
            
            	    $('body').on('click', 'button#btn_period_week', function(event) {
            event.preventDefault();
            $('button#btn_period_day').removeClass('active');
            $('button#btn_period_week').addClass('active');
            $('button#btn_period_month').removeClass('active');
            	$("#period_day").hide();
            	$("#period_week").show();
            	$("#period_month").hide();
            	$("#period").val('week');
            });
            
            	    $('body').on('click', 'button#btn_period_month', function(event) {
            event.preventDefault();
            $('button#btn_period_day').removeClass('active');
            $('button#btn_period_week').removeClass('active');
            $('button#btn_period_month').addClass('active');
            	$("#period_day").hide();
            	$("#period_week").hide();
            	$("#period_month").show();
            	$("#period").val('month');
            });
            
            
            
            
            
            
            
	    
        $("select#users_do").change(function() {
            var p = $('select#users_do').val();
            var t = $('select#to').val();
            //console.log(p);
            if (t == 0) {
                if (p != 0) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=get_unit_id" + "&uid=" + p,
                        success: function(html) {
                            //console.log(html);
                            $("select#to [value='" + html + "']").attr("selected", "selected");
                            $('select#to').trigger('chosen:updated');
                            $('#for_to').popover('hide');
                            $('#for_to').removeClass('has-error');
                            $('#for_to').addClass('has-success');
                        }
                    });
                }
                if (p == 0) {
                    $("select#to").find('option:selected').removeAttr("selected");
                    $('select#to').trigger('chosen:updated');
                }
            }
        });
        $("select#to").on('change', function() {
            if ($('select#to').val() != 0) {
                $('#for_to').popover('hide');
                $('#for_to').removeClass('has-error');
                $('#for_to').addClass('has-success');
                $('#dsd').popover('hide');
            } else {
                $('#dsd').popover('show');
                $('#for_to').popover('show');
                $('#for_to').addClass('has-error');
                setTimeout(function() {
                    $("#dsd").popover('hide');
                }, 2000);
            }
        });
        $("select#to").change(function() {
            var i = $('select#to').val();
            if ($('select#to').val() != 0) {
                $('#for_to').popover('hide');
                $('#for_to').removeClass('has-error');
                $('#for_to').addClass('has-success');
                createuserslist(i, 'users_do');
            } else {
                createuserslist(i, 'users_do');
                $('#for_to').popover('show');
                $('#for_to').addClass('has-error');
                setTimeout(function() {
                    $("#for_to").popover('hide');
                }, 2000);
            }
        });
        //select_init_user
        $('body').on('click', 'a#select_init_user', function(event) {
            event.preventDefault();
            console.log($(this).attr('param-hash'));
            var ulogin = $('#user_name_login').val();
            var uinitid = $('#user_init_id').val();
            //$("#fio").val(ui.item.label);
            //$('#fio').val('system');
            $('#fio').popover('hide');
            $('#for_fio').removeClass('has-error').addClass('has-success');
            $("#user_info").hide().fadeIn(500);
            $("#alert_add").hide();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_client_from_new_t&get_my_info=0",
                success: function(html) {
                    $('#client_id_param').val(uinitid);
                    $('#fio').val(ulogin);
                    $("#user_info").hide().html(html).fadeIn(500);
                    $('#for_fio').addClass('has-success');
                    $("#status_action").val('edit');
                    makemytime(true);
                }
            });
        });

        $('body').on('click', 'button#prio_low', function(event) {
            event.preventDefault();
            $('button#prio_low').addClass('active');
            $('button#prio_normal').removeClass('active');
            $('button#prio_high').removeClass('active');
            $('i#lprio_low').addClass('fa fa-check');
            $("i#lprio_norm").removeClass("fa fa-check");
            $("i#lprio_high").removeClass("fa fa-check");
            $("#prio").val('0');
        });
        $('body').on('click', 'button#prio_normal', function(event) {
            event.preventDefault();
            $('button#prio_low').removeClass('active');
            $('button#prio_normal').addClass('active');
            $('button#prio_high').removeClass('active');
            $('i#lprio_low').removeClass('fa fa-check');
            $("i#lprio_norm").addClass("fa fa-check");
            $("i#lprio_high").removeClass("fa fa-check");
            $("#prio").val('1');
        });
        $('body').on('click', 'button#prio_high', function(event) {
            event.preventDefault();
            $('button#prio_low').removeClass('active');
            $('button#prio_normal').removeClass('active');
            $('button#prio_high').addClass('active');
            $('i#lprio_low').removeClass('fa fa-check');
            $("i#lprio_norm").removeClass("fa fa-check");
            $("i#lprio_high").addClass("fa fa-check");
            $("#prio").val('2');
        });
        $('body').on('click', 'button#reset_cron', function(event) {
            event.preventDefault();
            window.location = MyHOSTNAME + "scheduler?plus";
        });
        
$("#user_info").hide();
        $("#alert_add").hide();
        $("#users_do").select2({
            formatResult: format,
            formatSelection: format,
            allowClear: true,
            maximumSelectionSize: 5,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found'),
            escapeMarkup: function(m) {
                return m;
            }
        });
        $('textarea').autosize({
            append: "\n"
        });

	   $("#fio").autocomplete({
            max: 10,
            minLength: 2,
            source: MyHOSTNAME + "/inc/json.php?fio",
            focus: function(event, ui) {
                $("#fio").val(ui.item.label);
                return false;
            },
            select: function(event, ui) {
                $("#fio").val(ui.item.label);
                $("#client_id_param").val(ui.item.value);
                $('#fio').popover('hide');
                $('#for_fio').removeClass('has-error').addClass('has-success');
                $("#user_info").hide().fadeIn(500);
                $("#alert_add").hide();
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=get_client_from_new_t&get_client_info=" + ui.item.value,
                    success: function(html) {
                        $("#user_info").hide().html(html).fadeIn(500);
                        $('#edit_login').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто'
                        });
                        $('#edit_posada').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто',
                            mode: 'popup',
                            showbuttons: false
                        });
                        $('#edit_unit').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто',
                            mode: 'popup',
                            showbuttons: false
                        });
                        $('#edit_tel').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто'
                        });
                        $('#edit_adr').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто'
                        });
                        $('#edit_mail').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто'
                        });
                        $('#for_fio').addClass('has-success');
                        $("#status_action").val('edit');
                        makemytime(true);
                    }
                });
                return false;
            },
            change: function(event, ui) {
                //console.log(this.value);
                if ($('input#fio').val().length != 0) {
                    if (ui.item == null) {
                        /*
ajax запрос с фио или логин или номер тел человека

php:
если найдена 1 запись, то выдать найденого
если не найдено то
                    1. разрешено добавление клиентов - выдать новый пользователь
                    2. не разрешено добавление клиентов - выдать - не найден пользователь и поле фио очистить

*/
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: ACTIONPATH,
                            data: "mode=find_client&cron=true&name=" + encodeURIComponent($("#fio").val()),
                            success: function(html) {
                                $.each(html, function(i, item) {
                                    if (item.res == true) {
                                        ///////////
                                        $("#client_id_param").val(item.p);
                                        $('#fio').popover('hide');
                                        $('#for_fio').removeClass('has-error').addClass('has-success');
                                        $("#user_info").hide().fadeIn(500);
                                        $("#alert_add").hide();
                                        $.ajax({
                                            type: "POST",
                                            url: ACTIONPATH,
                                            data: "mode=get_client_from_new_t&get_client_info=" + encodeURIComponent(item.p),
                                            success: function(html) {
                                                $("#user_info").hide().html(html).fadeIn(500);
                                                $('#edit_login').editable({
                                                    type: 'text',
                                                    pk: 1,
                                                    url: MyHOSTNAME + 'actions.php',
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто',
                                                    params: {
                                                        mode: 'verify_login_nt'
                                                    }
                                                });
                                                $('#edit_posada').editable({
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто',
                                                    mode: 'popup',
                                                    showbuttons: false
                                                });
                                                $('#edit_unit').editable({
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто',
                                                    mode: 'popup',
                                                    showbuttons: false
                                                });
                                                $('#edit_tel').editable({
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто'
                                                });
                                                $('#edit_adr').editable({
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто'
                                                });
                                                $('#edit_mail').editable({
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто'
                                                });
                                                $('#for_fio').addClass('has-success');
                                                $("#status_action").val('edit');
                                                makemytime(true);
                                            }
                                        });
                                        ///////////
                                        //console.log(item.p);
                                    }
                                    if (item.res == false) {
                                        if (item.priv == true) { //console.log('add');
                                            $("#user_info").hide();
                                            $('#fio').popover('hide');
                                            $('#for_fio').removeClass('has-error');
                                            $('#for_fio').addClass('has-success');
                                            $("#status_action").val('add');
                                            $.ajax({
                                                type: "POST",
                                                url: ACTIONPATH,
                                                data: "mode=get_client_from_new_t&new_client_info=" + encodeURIComponent($("#fio").val()),
                                                success: function(html) {
                                                    $("#user_info").hide().html(html).fadeIn(500);
                                                    $('#username').editable({
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто'
                                                    });
                                                    $('#new_login').editable({
                                                        type: 'text',
                                                        pk: 1,
                                                        url: MyHOSTNAME + 'actions.php',
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто',
                                                        params: {
                                                            mode: 'verify_login_nt'
                                                        }
                                                    });
                                                    $('#new_posada').editable({
                                                        inputclass: 'input-sm posada_class',
                                                        emptytext: 'пусто',
                                                        mode: 'popup',
                                                        showbuttons: false
                                                    });
                                                    $('#new_unit').editable({
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто',
                                                        mode: 'popup',
                                                        showbuttons: false
                                                    });
                                                    $('#new_tel').editable({
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто'
                                                    });
                                                    $('#new_adr').editable({
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто'
                                                    });
                                                    $('#new_mail').editable({
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто'
                                                    });
                                                    makemytime(true);
                                                }
                                            });
                                        }
                                        if (item.priv == false) { //console.log('not add');
                                            $("#user_info").hide();
                                            $("#user_info").hide().html(item.msg_error).fadeIn(500);
                                            $("#status_action").val('');
                                            $("#fio").val('');
                                            makemytime(true);
                                        }
                                    }
                                });
                            }
                        });
                        /*
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=get_client_from_new_t&new_client_info="+$("#fio").val(),
                        success: function(html) {
                            $("#alert_add").hide().html(html).fadeIn(500);
                            
                            $('#username').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_login').editable({inputclass: 'input-sm', emptytext: 'пусто'});
                            $('#new_posada').editable({inputclass: 'input-sm posada_class',emptytext: 'пусто',mode: 'popup',showbuttons: false});
                            $('#new_unit').editable({inputclass: 'input-sm',emptytext: 'пусто',mode: 'popup',showbuttons: false});
                            $('#new_tel').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_adr').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_mail').editable({inputclass: 'input-sm', emptytext: 'пусто'});
                        }
                    });
                    */
                    } else {}
                }
            }
        });
	    
	     };
    

    if (ispath('main_stats')) {
        $('#reservation').on('apply.daterangepicker', function(ev, picker) {

            var p = $('#user_list').val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_total_period_stat"+
                "&start=" + picker.startDate.format('YYYY-MM-DD') + "&end=" + picker.endDate.format('YYYY-MM-DD'),
                success: function(html) {
                    $('#ts_res').html(html);
                    $(".knob").knob();
                    makemytime();
                }
            });
        });
        $('#reservation').daterangepicker({
            format: 'YYYY-MM-DD'
        });

    }


    if (ispath('user_stats')) {
        $("#user_list").select2({
            formatResult: format,
            formatSelection: format,
            allowClear: true,
            maximumSelectionSize: 5,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found'),
            escapeMarkup: function(m) {
                return m;
            }
        });
        $('#reservation').on('apply.daterangepicker', function(ev, picker) {
            console.log(picker.startDate.format('YYYY-MM-DD'));
            console.log(picker.endDate.format('YYYY-MM-DD'));
            var p = $('#user_list').val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_user_stat" + "&uid=" + p + "&start=" + picker.startDate.format('YYYY-MM-DD') + "&end=" + picker.endDate.format('YYYY-MM-DD'),
                success: function(html) {
                    $('#content_stat').html(html);
                    $(".knob").knob();
                    makemytime();
                }
            });
        });
        $('#reservation').daterangepicker({
            format: 'YYYY-MM-DD'
        });
    };
    if (ispath('messages')) {
        //check_main_msgs()
        //setInterval(check_main_msgs(),2000);
        //clearInterval(interval_main);
        interval = setInterval(function() {
            check_main_msgs();
        }, 2000);
        var scroll = $('#content_chat');
        var height = scroll[0].scrollHeight;
        scroll.scrollTop(height);
        $('input#msg').bind('keypress', function(e) {
            if (e.keyCode == 13) {
                $("button#do_comment").click();
            }
        });
        $("input#msg").keyup(function() {
            if ($(this).val().replace(/ /g, '').length > 1) {
                $("input#msg").popover('hide');
                $("#for_msg").removeClass('has-error').addClass('has-success');
            } else {
                $("input#msg").popover('show');
                $("#for_msg").addClass('has-error');
                setTimeout(function() {
                    $("input#msg").popover('hide');
                }, 2000);
            }
        });
        //select_main_chat
        $('body').on('click', '#select_main_chat', function(event) {
            event.preventDefault();
            $('#target_user').val('main');
            var user_chat = $(this).html();
            $('.user_li').removeClass('active');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=messages_view" + "&target=main",
                success: function(html) {
                    $("#content_chat").html(html);
                    $("input#msg").val('')
                    makemytime(true);
                    $("#title_chat").html(user_chat);
                    //comment_body
                    var scroll = $('#content_chat');
                    var height = scroll[0].scrollHeight;
                    scroll.scrollTop(height);
                    //console.log(height);
                }
            });
        });
        $("input#find_user").keyup(function() {
            var t = $(this).val(),
                t_l = $(this).val().length;
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=message_user_list" + "&t=" + t,
                success: function(html) {
                    $("#user_list").html(html);
                }
            });
        });
        $('body').on('click', '.user_li', function(event) {
            event.preventDefault();
            var user_id = $(this).attr('user-id');
            $('.loading1').addClass('overlay');
            $('.loading2').addClass('loading-img');
            $('.user_li').removeClass('active');
            $(this).addClass('active');
            $('#target_user').val(user_id);
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=messages_view" + "&target=" + user_id,
                success: function(html) {
                    $("#content_chat").html(html);
                    $("input#msg").val('')
                    makemytime(true);
                    $('.loading1').removeClass('overlay');
                    $('.loading2').removeClass('loading-img');
                    //comment_body
                    var scroll = $('#content_chat');
                    var height = scroll[0].scrollHeight;
                    scroll.scrollTop(height);
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=messages_title_username" + "&uid=" + user_id,
                        success: function(user_chat) {
                            $("#title_chat").html(user_chat);
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=recalculate_messages",
                        success: function(html) {
                            $('#label_msg').html(html);
                        }
                    });
                    var el = '#ul_label_' + user_id;
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=recalculate_messages_ul" + "&uid=" + user_id,
                        success: function(html) {
                            $(el).html(html);
                            show_bar_unread_msg();
                        }
                    });
                }
            });
        });
        $('body').on('click', 'button#do_comment', function(event) {
            event.preventDefault();
            var tid = $("#target_user").val();
            var m = $("input#msg").val().length;
            if ($("input#msg").val().replace(/ /g, '').length > 1) {
                $("input#msg").popover('hide');
                $("#for_msg").removeClass('has-error').addClass('has-success');
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=messages_send" + "&textmsg=" + encodeURIComponent(($("input#msg").val())) + "&target=" + tid,
                    success: function(html) {
                        $("#content_chat").html(html);
                        $("input#msg").val('')
                        makemytime(true);
                        //comment_body
                        var scroll = $('#content_chat');
                        var height = scroll[0].scrollHeight;
                        scroll.scrollTop(height);
                        //console.log(height);
                    }
                });
            } else {
                $("input#msg").popover('show');
                $("#for_msg").addClass('has-error');
                setTimeout(function() {
                    $("input#msg").popover('hide');
                }, 2000);
            }
        });
    };
    if (ispath('notes')) {
        //if (def_filename == "notes.php") {
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_list_notes",
            success: function(html) {
                $('#table_list').html(html);
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=get_first_note",
                    success: function(html) {}
                });
            }
        });
    }
    if (ispath('ticket')) {
        var scroll = $('#comment_body');
        var height = scroll[0].scrollHeight;
        scroll.scrollTop(height);
        $('.file-inputs').bootstrapFileInput();
        $('#do_comment_file').change(function() {
            upl();
        });

        function upl() {
            var file_data = $('#do_comment_file').prop('files')[0];
            var t_id = $('#ticket_hash').val();
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('mode', 'attach_file_comment');
            form_data.append('tid', t_id);
            //alert(form_data);                             
            $.ajax({
                url: ACTIONPATH, // point to server-side PHP script 
                dataType: 'text', // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function(html) {
                    //alert(html); // display response from the PHP script, if any
                    $("#comment_content").html(html);
                    $("input#msg").val('')
                    makemytime(true);
                    var scroll = $('#comment_body');
                    var height = scroll[0].scrollHeight;
                    scroll.scrollTop(height);
                }
            });
        }
        socket.on('connect_error', function() {
            //console.log('Socket is not connected.');
            var tt_id = $('#ticket_id').val();
            //get_comments(tt_id);
            /*
   $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=check_comments"+
                      "&tid="+$('#ticket_id').val(),
                success: function(html){ 
                    
                    $('#ticket_id').val()
                    
                }
                });
  
  */
            /*
  
  
  ajax check new comments
  ticket_total
  
  
  
  //get_comments(ticket_id)
    if (comments) do ajax fetch new comments
    
  */
        });
        $('body').on('click', 'button#prio_low', function(event) {
            event.preventDefault();
            $('button#prio_low').addClass('active');
            $('button#prio_normal').removeClass('active');
            $('button#prio_high').removeClass('active');
            $('i#lprio_low').addClass('fa fa-check');
            $("i#lprio_norm").removeClass("fa fa-check");
            $("i#lprio_high").removeClass("fa fa-check");
            $("#prio").val('0');
        });
        $('body').on('click', 'button#prio_normal', function(event) {
            event.preventDefault();
            $('button#prio_low').removeClass('active');
            $('button#prio_normal').addClass('active');
            $('button#prio_high').removeClass('active');
            $('i#lprio_low').removeClass('fa fa-check');
            $("i#lprio_norm").addClass("fa fa-check");
            $("i#lprio_high").removeClass("fa fa-check");
            $("#prio").val('1');
        });
        $('body').on('click', 'button#prio_high', function(event) {
            event.preventDefault();
            $('button#prio_low').removeClass('active');
            $('button#prio_normal').removeClass('active');
            $('button#prio_high').addClass('active');
            $('i#lprio_low').removeClass('fa fa-check');
            $("i#lprio_norm").removeClass("fa fa-check");
            $("i#lprio_high").addClass("fa fa-check");
            $("#prio").val('2');
        });
        $("textarea#msg").keyup(function() {
            if ($(this).val().length > 1) {
                $("textarea#msg").popover('hide');
                $("#for_msg").removeClass('has-error').addClass('has-success');
            } else {
                $("textarea#msg").popover('show');
                $("#for_msg").addClass('has-error');
            }
        });
        $('input#msg').bind('keypress', function(e) {
            if (e.keyCode == 13) {
                $("button#do_comment").click();
            }
        });
        $("input#msg").keyup(function() {
            if ($(this).val().replace(/ /g, '').length > 1) {
                $("input#msg").popover('hide');
                $("#for_msg").removeClass('has-error').addClass('has-success');
            } else {
                $("input#msg").popover('show');
                $("#for_msg").addClass('has-error');
                setTimeout(function() {
                    $("input#msg").popover('hide');
                }, 2000);
            }
        });
        $('body').on('click', 'button#action_refer_to', function(event) {
            event.preventDefault();
            var st = $("#action_refer_to").attr('value');
            if (st == '0') {
                $("#refer_to").fadeIn(500);
                $(this).addClass('active').attr('value', '1');
            }
            if (st == '1') {
                $("#refer_to").fadeOut(500);
                $(this).removeClass('active').attr('value', '0');
            }
        });
        $("select#t_users_do").change(function() {
            var p = $('select#t_users_do').val();
            var t = $('select#t_to').val();
            if (t == 0) {
                if (p != 0) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=get_unit_id" + "&uid=" + p,
                        success: function(html) {
                            $("select#t_to [value='" + html + "']").attr("selected", "selected");
                            $('select#t_to').trigger('chosen:updated');
                            $('#t_for_to').popover('hide');
                            $('#t_for_to').removeClass('has-error');
                            $('#t_for_to').addClass('has-success');
                        }
                    });
                }
                if (p == 0) {
                    $("select#t_to").find('option:selected').removeAttr("selected");
                    $('select#t_to').trigger('chosen:updated');
                }
            }
        });
        $("select#t_to").change(function() {
            var i = $('select#t_to').val();
            if ($('select#t_to').val() != 0) {
                $('#t_for_to').popover('hide');
                $('#t_for_to').removeClass('has-error');
                //$('#t_for_to').addClass('has-success');
                createuserslist(i, 't_users_do');
            } else if ($('select#t_to').val() == 0) {
                //createuserslist(i, 't_users_do');
                $('#t_for_to').popover('show');
                $('#t_for_to').addClass('has-error');
            }
            //console.log($('select#t_to').val());
        });
        $('body').on('click', 'button#save_edit_ticket', function(event) {
            event.preventDefault();
            var s = $('#subj').val(),
                m = $('#msg_up').val(),
                p = $('#prio').val(),
                t_hash = $('#ticket_hash').val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=save_edit_ticket" + "&t_hash=" + t_hash + "&subj=" + encodeURIComponent(s) + "&prio=" + encodeURIComponent(p) + "&msg=" + encodeURIComponent(m),
                success: function(html) {
                    //console.log(html);
                    $('#myModal').modal('hide');
                    //$(elem).removeClass().addClass('success', 1000);
                    window.location = MyHOSTNAME + "ticket?" + t_hash;
                }
            });
        });
        $('body').on('click', 'button#action_ok', function(event) {
            event.preventDefault();
            var status_lock = $("button#action_ok").attr('status');
            var ok_val = $("button#action_ok").attr("value");
            var ok_val_tid = $("button#action_ok").attr("tid");
            var lang_ok = get_lang_param('JS_ok');
            if (status_lock == 'ok') {
                $("button#action_ok").attr('status', "no_ok").html("<i class=\"fa fa-check\"></i> " + lang_ok);
                $("button#action_lock").removeAttr('disabled');
                $("button#action_refer_to").removeAttr('disabled');
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=status_ok" + "&tid=" + ok_val_tid + "&user=" + encodeURIComponent(ok_val),
                    success: function(html) {
                        $("#msg").hide().html(html).fadeIn(500);
                        setTimeout(function() {
                            $('#msg').children('.alert').fadeOut(500);
                        }, 3000);
                    }
                });
            }
            if (status_lock == 'no_ok') {
                var lang_nook = get_lang_param('JS_no_ok');
                $("button#action_lock").attr('disabled', "disabled");
                $("button#action_refer_to").attr('disabled', "disabled");
                $("button#action_ok").attr('status', "ok").html("<i class=''></i> " + lang_nook);
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=status_no_ok" + "&tid=" + ok_val_tid + "&user=" + encodeURIComponent(ok_val),
                    success: function(html) {
                        $("#msg").hide().html(html).fadeIn(500);
                        setTimeout(function() {
                            $('#msg').children('.alert').fadeOut(500);
                        }, 3000);
                    }
                });
            }
        });
        $('body').on('click', 'button#action_lock', function(event) {
            event.preventDefault();
            var lock_val = $("button#action_lock").attr("value");
            var lock_val_tid = $("button#action_lock").attr("tid");
            var status_lock = $("button#action_lock").attr('status');
            var lang_unlock = get_lang_param('JS_unlock');
            if (status_lock == 'lock') {
                $("button#action_lock").attr('status', "unlock").html("<i class='fa fa-unlock'></i> " + lang_unlock);
                $("#msg_e").hide();
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=lock" + "&tid=" + lock_val_tid + "&user=" + encodeURIComponent(lock_val),
                    success: function(html) {
                        $("#msg").hide().html(html).fadeIn(500);
                        setTimeout(function() {
                            $('#msg').children('.alert').fadeOut(500);
                        }, 3000);
                    }
                });
            }
            if (status_lock == 'unlock') {
                $("#msg_e").hide();
                var lang_lock = get_lang_param('JS_lock');
                $("button#action_lock").attr('status', "lock").html("<i class='fa fa-lock'></i> " + lang_lock);
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=unlock" + "&tid=" + lock_val_tid,
                    success: function(html) {
                        $("#msg").hide().html(html).fadeIn(500);
                        setTimeout(function() {
                            $('#msg').children('.alert').fadeOut(500);
                        }, 3000);
                    }
                });
            }
        });
        $('body').on('click', 'button#ref_ticket', function(event) {
            event.preventDefault();
            var to = $("select#t_to").val();
            var tou = $("select#t_users_do").val();
            var tom = $("#msg1").val();
            var u_do;
            if ($("#t_users_do").val() == null) {
                u_do = '0';
            } else if ($("#t_users_do").val() != null) {
                u_do = $("#t_users_do").val();
            }
            var error_code = 0;
            if (to == '0') {
                error_code = 1;
                $('#t_for_to').popover('show');
                $('#t_for_to').addClass('has-error');
            }
            if (error_code == 0) {
                var pp = $("button#ref_ticket").attr("value");
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=update_to" + "&ticket_id=" + pp + "&to=" + encodeURIComponent(to) + "&tou=" + encodeURIComponent(u_do) + "&tom=" + encodeURIComponent(tom),
                    success: function(html) {
                        $("#ccc").hide().html(html).fadeIn(500);
                        window.location = MyHOSTNAME + "list?in";
                    }
                });
            }
        });
        $('body').on('click', 'button#do_comment', function(event) {
            event.preventDefault();
            var tid = $(this).attr('value');
            var usr = $(this).attr('user');
            var m = $("input#msg").val().length;
            if ($("input#msg").val().replace(/ /g, '').length > 1) {
                $("input#msg").popover('hide');
                $("#for_msg").removeClass('has-error').addClass('has-success');
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=add_comment" + "&user=" + encodeURIComponent(usr) + "&textmsg=" + encodeURIComponent(($("input#msg").val())) + "&tid=" + tid,
                    success: function(html) {
                        $("#comment_content").html(html);
                        $("input#msg").val('')
                        makemytime(true);
                        //comment_body
                        var scroll = $('#comment_body');
                        var height = scroll[0].scrollHeight;
                        scroll.scrollTop(height);
                        //console.log(height);
                    }
                });
            } else {
                $("input#msg").popover('show');
                $("#for_msg").addClass('has-error');
                setTimeout(function() {
                    $("input#msg").popover('hide');
                }, 2000);
            }
        });
        $("#refer_to").hide();
        $("#t_users_do").select2({
            formatResult: format,
            formatSelection: format,
            allowClear: true,
            maximumSelectionSize: 5,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found'),
            escapeMarkup: function(m) {
                return m;
            }
        });
        $('textarea').autosize({
            append: "\n"
        });
        //if (def_filename == "ticket.php") {
        makemytime(true);
        /*
        setInterval(function(){
            var lu=$("#last_update").attr('value');
            var tid=$("#ticket_id").attr('value');
            check_update();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                dataType: "json",
                data: "mode=check_update_one"+
                    "&id="+tid+
                    "&last_update="+lu,
                success: function(html){


                    if (html) {
                        $.each(html, function(i, item) {


                            if (item.type == "update") {window.location = MyHOSTNAME+"ticket?"+item.hash+"&refresh"; }
                            else if (item.type == "comment") {
                                $.ajax({
                                    type: "POST",
                                    url: ACTIONPATH,
                                    data: "mode=view_comment"+
                                        "&tid="+encodeURIComponent(tid),
                                    success: function(r) {

                                        $("#comment_content").html(r);

                                        $("#last_update").attr('value',item.time);
                                        makemytime(true);
                                          var scroll    = $('#comment_body');
  var height = scroll[0].scrollHeight;
  scroll.scrollTop(height);
console.log(height);
                                    }
                                });

                            }
                            else if (item.type == "no") {

                            }



                        });

                    }

                }
            });




        },5000);
*/
    }
    if (ispath('userinfo')) {
        makemytime(true);
    }
    if (ispath('view_user')) {
        $(".knob").knob();
    };
    if (ispath('profile')) {
        $("input#login_user").keyup(function() {
            var exparam = $(this).attr('exclude-param');
            if ($(this).val().length > 3) {
                $("#login_user_grp").removeClass('has-error').addClass('has-success');
                //$("#errors").val('false');
                my_errors.login = false;
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ACTIONPATH,
                    data: "mode=check_login" + "&login=" + $(this).val() + "&exclude=" + exparam,
                    success: function(html) {
                        $.each(html, function(i, item) {
                            if (item.check_login_status == true) {
                                $("#login_user_grp").removeClass('has-error').addClass('has-success');
                                //$("#errors").val('false');
                                my_errors.login = false;
                            } else if (item.check_login_status == false) {
                                $("#login_user_grp").addClass('has-error');
                                //$("#errors").val('true');
                                my_errors.login = true;
                            }
                        });
                        //console.log(html);
                    }
                });
            } else {
                $("#login_user_grp").addClass('has-error');
                //$("#errors").val('true');
                my_errors.login = true;
            }
        });
        $('#file_avatar').change(function() {
            $('#form_avatar').submit();
        });
        $('body').on('click', 'button#del_profile_img', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=del_profile_img",
                success: function() {
                    window.location = MyHOSTNAME + "profile";
                }
            });
        });
        $('body').on('click', 'button#edit_profile_main_client', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=edit_profile_main_client" + "&fio=" + encodeURIComponent($("#fio").val()) + "&mail=" + encodeURIComponent($("#mail").val()) + "&lang=" + encodeURIComponent($("select#lang").val()) + "&skype=" + encodeURIComponent($("#skype").val()) + "&tel=" + encodeURIComponent($("#tel").val()) + "&adr=" + encodeURIComponent($("#adr").val()) + "&id=" + encodeURIComponent($("#edit_profile_main").attr('value')),
                success: function(html) {
                    $("#m_info").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#m_info').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        $('body').on('click', 'button#edit_profile_main', function(event) {
            event.preventDefault();
            if ($("#fio").val().length < 3) {
                //$("#errors").val('true');
                my_errors.fio = true;
                $("#fio_user_grp").addClass('has-error');
            }
            //var er=$("#errors").val();
            var er = my_errors.fio;
            if (er == false) {
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=edit_profile_main" + "&mail=" + encodeURIComponent($("#mail").val()) + "&fio=" + encodeURIComponent($("#fio").val()) + "&lang=" + encodeURIComponent($("select#lang").val()) + "&skype=" + encodeURIComponent($("#skype").val()) + "&tel=" + encodeURIComponent($("#tel").val()) + "&adr=" + encodeURIComponent($("#adr").val()) + "&posada=" + encodeURIComponent($("#posada").val()) + "&unit=" + encodeURIComponent($("#pidrozdil").val()) + "&id=" + encodeURIComponent($("#edit_profile_main").attr('value')),
                    success: function(html) {
                        $("#m_info").hide().html(html).fadeIn(500);
                        setTimeout(function() {
                            $('#m_info').children('.alert').fadeOut(500);
                        }, 3000);
                    }
                });
            } else {
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");
            }
        });
        $('body').on('click', 'button#edit_profile_pass', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=edit_profile_pass" + "&old_pass=" + encodeURIComponent($("#old_pass").val()) + "&new_pass=" + encodeURIComponent($("#new_pass").val()) + "&new_pass2=" + encodeURIComponent($("#new_pass2").val()) + "&id=" + encodeURIComponent($("#edit_profile_main").attr('value')),
                success: function(html) {
                    $("#p_info").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#p_info').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        //if (def_filename == "profile.php") {
        /*  setInterval(function(){
            check_update();
        },5000);*/
    }
    if (ispath('create')) {
        $.fn.editable.defaults.mode = 'inline';
        $("select#users_do").change(function() {
            var p = $('select#users_do').val();
            var t = $('select#to').val();
            //console.log(p);
            if (t == 0) {
                if (p != 0) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=get_unit_id" + "&uid=" + p,
                        success: function(html) {
                            //console.log(html);
                            $("select#to [value='" + html + "']").attr("selected", "selected");
                            $('select#to').trigger('chosen:updated');
                            $('#for_to').popover('hide');
                            $('#for_to').removeClass('has-error');
                            $('#for_to').addClass('has-success');
                        }
                    });
                }
                if (p == 0) {
                    $("select#to").find('option:selected').removeAttr("selected");
                    $('select#to').trigger('chosen:updated');
                }
            }
        });
        $("select#to").on('change', function() {
            if ($('select#to').val() != 0) {
                $('#for_to').popover('hide');
                $('#for_to').removeClass('has-error');
                $('#for_to').addClass('has-success');
                $('#dsd').popover('hide');
            } else {
                $('#dsd').popover('show');
                $('#for_to').popover('show');
                $('#for_to').addClass('has-error');
                setTimeout(function() {
                    $("#dsd").popover('hide');
                }, 2000);
            }
        });
        $("select#to").change(function() {
            var i = $('select#to').val();
            if ($('select#to').val() != 0) {
                $('#for_to').popover('hide');
                $('#for_to').removeClass('has-error');
                $('#for_to').addClass('has-success');
                createuserslist(i, 'users_do');
            } else {
                createuserslist(i, 'users_do');
                $('#for_to').popover('show');
                $('#for_to').addClass('has-error');
                setTimeout(function() {
                    $("#for_to").popover('hide');
                }, 2000);
            }
        });
        //select_init_user
        $('body').on('click', 'a#select_init_user', function(event) {
            event.preventDefault();
            console.log($(this).attr('param-hash'));
            var ulogin = $('#user_name_login').val();
            var uinitid = $('#user_init_id').val();
            //$("#fio").val(ui.item.label);
            //$('#fio').val('system');
            $('#fio').popover('hide');
            $('#for_fio').removeClass('has-error').addClass('has-success');
            $("#user_info").hide().fadeIn(500);
            $("#alert_add").hide();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_client_from_new_t&get_my_info=0",
                success: function(html) {
                    $('#client_id_param').val(uinitid);
                    $('#fio').val(ulogin);
                    $("#user_info").hide().html(html).fadeIn(500);
                    $('#for_fio').addClass('has-success');
                    $("#status_action").val('edit');
                    makemytime(true);
                }
            });
        });
        $('body').on('click', 'button#prio_low', function(event) {
            event.preventDefault();
            $('button#prio_low').addClass('active');
            $('button#prio_normal').removeClass('active');
            $('button#prio_high').removeClass('active');
            $('i#lprio_low').addClass('fa fa-check');
            $("i#lprio_norm").removeClass("fa fa-check");
            $("i#lprio_high").removeClass("fa fa-check");
            $("#prio").val('0');
        });
        $('body').on('click', 'button#prio_normal', function(event) {
            event.preventDefault();
            $('button#prio_low').removeClass('active');
            $('button#prio_normal').addClass('active');
            $('button#prio_high').removeClass('active');
            $('i#lprio_low').removeClass('fa fa-check');
            $("i#lprio_norm").addClass("fa fa-check");
            $("i#lprio_high").removeClass("fa fa-check");
            $("#prio").val('1');
        });
        $('body').on('click', 'button#prio_high', function(event) {
            event.preventDefault();
            $('button#prio_low').removeClass('active');
            $('button#prio_normal').removeClass('active');
            $('button#prio_high').addClass('active');
            $('i#lprio_low').removeClass('fa fa-check');
            $("i#lprio_norm").removeClass("fa fa-check");
            $("i#lprio_high").addClass("fa fa-check");
            $("#prio").val('2');
        });
        $('body').on('click', 'button#enter_ticket_client', function(event) {
            event.preventDefault();
            if (check_form_ticket_client() == 0) {
                enter_ticket_client();
                //console.log('ok');
            }
        });
        $('body').on('click', 'button#enter_ticket', function(event) {
            event.preventDefault();
            if (check_form_ticket() == 0) {
                enter_ticket();
            }
            
            //console.log($("#users_do").val());
            //alert(u_do);
        });
        $("#fio").autocomplete({
            max: 10,
            minLength: 2,
            source: MyHOSTNAME + "/inc/json.php?fio",
            focus: function(event, ui) {
                $("#fio").val(ui.item.label);
                return false;
            },
            select: function(event, ui) {
                $("#fio").val(ui.item.label);
                $("#client_id_param").val(ui.item.value);
                $('#fio').popover('hide');
                $('#for_fio').removeClass('has-error').addClass('has-success');
                $("#user_info").hide().fadeIn(500);
                $("#alert_add").hide();
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=get_client_from_new_t&get_client_info=" + ui.item.value,
                    success: function(html) {
                        $("#user_info").hide().html(html).fadeIn(500);
                        $('#edit_login').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто'
                        });
                        $('#edit_posada').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто',
                            mode: 'popup',
                            showbuttons: false
                        });
                        $('#edit_unit').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто',
                            mode: 'popup',
                            showbuttons: false
                        });
                        $('#edit_tel').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто'
                        });
                        $('#edit_adr').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто'
                        });
                        $('#edit_mail').editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто'
                        });
                        $('#for_fio').addClass('has-success');
                        $("#status_action").val('edit');
                        makemytime(true);
                    }
                });
                return false;
            },
            change: function(event, ui) {
                //console.log(this.value);
                if ($('input#fio').val().length != 0) {
                    if (ui.item == null) {
                        /*
ajax запрос с фио или логин или номер тел человека

php:
если найдена 1 запись, то выдать найденого
если не найдено то
                    1. разрешено добавление клиентов - выдать новый пользователь
                    2. не разрешено добавление клиентов - выдать - не найден пользователь и поле фио очистить

*/
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: ACTIONPATH,
                            data: "mode=find_client&name=" + encodeURIComponent($("#fio").val()),
                            success: function(html) {
                                $.each(html, function(i, item) {
                                    if (item.res == true) {
                                        ///////////
                                        $("#client_id_param").val(item.p);
                                        $('#fio').popover('hide');
                                        $('#for_fio').removeClass('has-error').addClass('has-success');
                                        $("#user_info").hide().fadeIn(500);
                                        $("#alert_add").hide();
                                        $.ajax({
                                            type: "POST",
                                            url: ACTIONPATH,
                                            data: "mode=get_client_from_new_t&get_client_info=" + encodeURIComponent(item.p),
                                            success: function(html) {
                                                $("#user_info").hide().html(html).fadeIn(500);
                                                $('#edit_login').editable({
                                                    type: 'text',
                                                    pk: 1,
                                                    url: MyHOSTNAME + 'actions.php',
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто',
                                                    params: {
                                                        mode: 'verify_login_nt'
                                                    }
                                                });
                                                $('#edit_posada').editable({
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто',
                                                    mode: 'popup',
                                                    showbuttons: false
                                                });
                                                $('#edit_unit').editable({
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто',
                                                    mode: 'popup',
                                                    showbuttons: false
                                                });
                                                $('#edit_tel').editable({
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто'
                                                });
                                                $('#edit_adr').editable({
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто'
                                                });
                                                $('#edit_mail').editable({
                                                    inputclass: 'input-sm',
                                                    emptytext: 'пусто'
                                                });
                                                $('#for_fio').addClass('has-success');
                                                $("#status_action").val('edit');
                                                makemytime(true);
                                            }
                                        });
                                        ///////////
                                        //console.log(item.p);
                                    }
                                    if (item.res == false) {
                                        if (item.priv == true) { //console.log('add');
                                            $("#user_info").hide();
                                            $('#fio').popover('hide');
                                            $('#for_fio').removeClass('has-error');
                                            $('#for_fio').addClass('has-success');
                                            $("#status_action").val('add');
                                            $.ajax({
                                                type: "POST",
                                                url: ACTIONPATH,
                                                data: "mode=get_client_from_new_t&new_client_info=" + encodeURIComponent($("#fio").val()),
                                                success: function(html) {
                                                    $("#user_info").hide().html(html).fadeIn(500);
                                                    $('#username').editable({
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто'
                                                    });
                                                    $('#new_login').editable({
                                                        type: 'text',
                                                        pk: 1,
                                                        url: MyHOSTNAME + 'actions.php',
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто',
                                                        params: {
                                                            mode: 'verify_login_nt'
                                                        }
                                                    });
                                                    $('#new_posada').editable({
                                                        inputclass: 'input-sm posada_class',
                                                        emptytext: 'пусто',
                                                        mode: 'popup',
                                                        showbuttons: false
                                                    });
                                                    $('#new_unit').editable({
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто',
                                                        mode: 'popup',
                                                        showbuttons: false
                                                    });
                                                    $('#new_tel').editable({
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто'
                                                    });
                                                    $('#new_adr').editable({
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто'
                                                    });
                                                    $('#new_mail').editable({
                                                        inputclass: 'input-sm',
                                                        emptytext: 'пусто'
                                                    });
                                                    makemytime(true);
                                                }
                                            });
                                        }
                                        if (item.priv == false) { //console.log('not add');
                                            $("#user_info").hide();
                                            $("#user_info").hide().html(item.msg_error).fadeIn(500);
                                            $("#status_action").val('');
                                            $("#fio").val('');
                                            makemytime(true);
                                        }
                                    }
                                });
                            }
                        });
                        /*
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=get_client_from_new_t&new_client_info="+$("#fio").val(),
                        success: function(html) {
                            $("#alert_add").hide().html(html).fadeIn(500);
                            
                            $('#username').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_login').editable({inputclass: 'input-sm', emptytext: 'пусто'});
                            $('#new_posada').editable({inputclass: 'input-sm posada_class',emptytext: 'пусто',mode: 'popup',showbuttons: false});
                            $('#new_unit').editable({inputclass: 'input-sm',emptytext: 'пусто',mode: 'popup',showbuttons: false});
                            $('#new_tel').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_adr').editable({inputclass: 'input-sm',emptytext: 'пусто'});
                            $('#new_mail').editable({inputclass: 'input-sm', emptytext: 'пусто'});
                        }
                    });
                    */
                    } else {}
                }
            }
        });
        $("#user_info").hide();
        $("#alert_add").hide();
        $("#users_do").select2({
            formatResult: format,
            formatSelection: format,
            allowClear: true,
            maximumSelectionSize: 5,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found'),
            escapeMarkup: function(m) {
                return m;
            }
        });
        $('textarea').autosize({
            append: "\n"
        });
        //if (def_filename == "new.php") {
        /*  setInterval(function(){
            check_update();
        },5000);*/
    }
    if (ispath('notes')) {
        $('body').on('click', 'button#save_notes', function(event) {
            event.preventDefault();
            var u = $(this).attr('value');
            var sHTML = $('#summernote').code();
            var data = {
                'mode': 'save_notes',
                'hn': u,
                'msg': sHTML
            };
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: data,
                success: function(html) {
                    //console.log(html);
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=get_list_notes",
                        success: function(html) {
                            $('#table_list').html(html);
                            noty({
                                text: get_lang_param('note_save'),
                                layout: 'center',
                                type: 'information',
                                timeout: 2000
                            });
                        }
                    });
                }
            });
        });
        $('body').on('click', 'a#to_notes', function(event) {
            event.preventDefault();
            var u = $(this).attr('value');
            var hostadr = get_host_conf();
            var langp = get_lang_param('JS_save');
            var langup = get_lang_param('JS_pub');
            $('#exampleInputEmail1').attr('value', MyHOSTNAME + "/inc/note.php?h=" + u);
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_notes" + "&hn=" + encodeURIComponent(u),
                success: function(html) {
                    $('#summernote').destroy();
                    $('#summernote').html(html);
                    $('#buttons').show();
                    $('#summernote').summernote({
                        height: 300,
                        focus: true,
                        lang: get_lang_param('summernote_lang'),
                        onImageUpload: function(files, editor, welEditable) {
                            sendFile(files[0], editor, welEditable);
                        },
                        oninit: function() {
                            var openBtn = '<button id="save_notes" value="' + u + '" type="button" class="btn btn-success btn-sm btn-small" title="' + langp + '" data-event="something" tabindex="-1"><i class="fa fa-check-circle"></i></button>';
                            var saveBtn = '<button id="saveFileBtn" type="button" class="btn btn-warning btn-sm btn-small" title="' + langup + '" data-event="something" tabindex="-1" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-bullhorn"></i> </button>';
                            var fileGroup = '<div class="note-file btn-group">' + openBtn + saveBtn + '</div>';
                            $(fileGroup).prependTo($('.note-toolbar'));
                            $('#save_notes').tooltip({
                                container: 'body',
                                placement: 'bottom'
                            });
                            $('#saveFileBtn').tooltip({
                                container: 'body',
                                placement: 'bottom'
                            });
                        }
                    });
                }
            });
        });
        $('body').on('click', 'span#del_notes', function(event) {
            event.preventDefault();
            var n_id = $(this).attr('value');
            var langp = get_lang_param('JS_create');
            var lang_del = get_lang_param('JS_del');
            bootbox.confirm(lang_del, function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=del_notes" + "&nid=" + encodeURIComponent(n_id),
                        success: function(html) {
                            //alert(html);
                            $.ajax({
                                type: "POST",
                                url: ACTIONPATH,
                                data: "mode=get_list_notes",
                                success: function(html) {
                                    $('#table_list').html(html);
                                    $('#summernote').destroy();
                                    $('#summernote').html("<div class=\"jumbotron\"><p><center>" + langp + "</center></p></div>");
                                    $('#buttons').hide();
                                }
                            });
                        }
                    });
                } else if (result == false) {}
            });
        });
        $('body').on('click', 'button#create_new_note', function(event) {
            event.preventDefault();
            var langp = get_lang_param('JS_save');
            var langup = get_lang_param('JS_pub');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=create_notes",
                success: function(html) {
                    var u = html;
                    var hostadr = get_host_conf();
                    $('#exampleInputEmail1').attr('value', MyHOSTNAME + "/inc/note.php?h=" + u);
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=get_notes" + "&hn=" + encodeURIComponent(u),
                        success: function(html) {
                            $('#summernote').destroy();
                            $('#summernote').html(html);
                            $('#buttons').show();
                            $('#summernote').summernote({
                                height: 300,
                                focus: true,
                                lang: get_lang_param('summernote_lang'),
                                onImageUpload: function(files, editor, welEditable) {
                                    sendFile(files[0], editor, welEditable);
                                },
                                oninit: function() {
                                    var openBtn = '<button id="save_notes" value="' + u + '" type="button" class="btn btn-success btn-sm btn-small" title="' + langp + '" data-event="something" tabindex="-1"><i class="fa fa-check-circle"></i></button>';
                                    var saveBtn = '<button id="saveFileBtn" type="button" class="btn btn-warning btn-sm btn-small" title="' + langup + '" data-event="something" tabindex="-1" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-bullhorn"></i> </button>';
                                    var fileGroup = '<div class="note-file btn-group">' + openBtn + saveBtn + '</div>';
                                    $(fileGroup).prependTo($('.note-toolbar'));
                                    $('#save_notes').tooltip({
                                        container: 'body',
                                        placement: 'bottom'
                                    });
                                    $('#saveFileBtn').tooltip({
                                        container: 'body',
                                        placement: 'bottom'
                                    });
                                }
                            });
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=get_list_notes",
                        success: function(html) {
                            $('#table_list').html(html);
                        }
                    });
                }
            });
        });
        //if (def_filename == "notes.php") {
        $('#buttons').hide();
        /*  setInterval(function(){
            check_update();
        },5000);*/
    }
    if (ispath('files')) {
        /*      setInterval(function(){
            check_update();
        },5000);*/
    }
    if (ispath('helper')) {


                if ($('#summernote_help').length != 0) {
                $('#summernote_help').summernote({
                        height: 300,
                        focus: true,
                        lang: get_lang_param('summernote_lang'),
                        onImageUpload: function(files, editor, welEditable) {
                            sendFile(files[0], editor, welEditable);
                        }
                    });
}

        $('body').on('click', 'button#del_helper', function(event) {
            event.preventDefault();
            var hn = $(this).val();
            var langdel = get_lang_param('JS_del');
            bootbox.confirm(langdel, function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=del_help" + "&hn=" + hn,
                        success: function(html) {
                            $.ajax({
                                type: "POST",
                                url: ACTIONPATH,
                                data: "mode=list_help",
                                success: function(html) {
                                    window.location = MyHOSTNAME + "helper";
                                }
                            });
                        }
                    });
                }
            });
        });
        $("input#find_helper").keyup(function() {
            var t = $(this).val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=find_help" + "&t=" + t,
                success: function(html) {
                    $("#help_content").html(html);
                }
            });
        });
        $('body').on('click', 'button#create_new_help', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=create_helper",
                success: function(html) {
                    $("#help_content").hide().html(html).fadeIn(500);
                    var settingsShow = function() {
                        var showPanel = $(".chosen-select").find('option:selected').attr('id');
                    }
                    $(".chosen-select").chosen({
                        no_results_text: get_lang_param('JS_not_found'),
                        allow_single_deselect: true,
                    });
                    $(".chosen-select").chosen().change(settingsShow);
                    $('#summernote_help').summernote({
                        height: 300,
                        focus: true,
                        lang: get_lang_param('summernote_lang'),
                        onImageUpload: function(files, editor, welEditable) {
                            sendFile(files[0], editor, welEditable);
                        }
                    });
                }
            });
        });
        $('body').on('click', 'button#do_save_help', function(event) {
            event.preventDefault();
            var sHTML = $('#summernote_help').code();
            var hn = $(this).val();
            var u = $("#u").chosen().val();
            var is_client = $("#is_client").prop('checked');
            var lang_unit = get_lang_param('JS_unit');
            var lang_probl = get_lang_param('JS_probl');
            var t = $("#t").val();
            var data = {
                'mode': 'do_save_help',
                'u': u,
                't': t,
                'msg': sHTML,
                'hn': hn,
                'is_client': is_client
            };
            var error_code = 0;
            if (u == null) {
                error_code = 1;
                noty({
                    text: lang_unit,
                    layout: 'center',
                    type: 'information',
                    timeout: 2000
                });
            }
            if ($("#t").val().length == 0) {
                error_code = 1;
                noty({
                    text: lang_probl,
                    layout: 'center',
                    type: 'information',
                    timeout: 2000
                });
            }
            if (error_code == 0) {
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: data,
                    success: function(html) {
                        window.location = MyHOSTNAME + "helper";
                    }
                });
            }
        });
        $('body').on('click', 'button#do_create_help', function(event) {
            event.preventDefault();
            var sHTML = $('#summernote_help').code();
            var u = $("#u").chosen().val();
            var lang_unit = get_lang_param('JS_unit');
            var lang_probl = get_lang_param('JS_probl');
            var is_client = $("#is_client").prop('checked');
            var t = $("#t").val();
            var data = {
                'mode': 'do_create_help',
                'u': u,
                't': t,
                'msg': sHTML,
                'is_client': is_client
            };
            var error_code = 0;
            //alert (u);
            if (u == null) {
                error_code = 1;
                noty({
                    text: lang_unit,
                    layout: 'center',
                    type: 'information',
                    timeout: 2000
                });
            }
            if ($("#t").val().length == 0) {
                error_code = 1;
                noty({
                    text: lang_probl,
                    layout: 'center',
                    type: 'information',
                    timeout: 2000
                });
            }
            if (error_code == 0) {
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: data,
                    success: function(html) {
                        window.location = MyHOSTNAME + "helper";
                    }
                });
            }
        });
/*
        $('body').on('click', 'button#edit_helper', function(event) {
            event.preventDefault();
            var hn = $(this).val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=edit_helper" + "&hn=" + encodeURIComponent(hn),
                success: function(html) {
                    $("#help_content").html(html);
                    var settingsShow = function() {
                        var showPanel = $(".chosen-select").find('option:selected').attr('id');
                    }
                    $(".chosen-select").chosen({
                        no_results_text: get_lang_param('JS_not_found'),
                        allow_single_deselect: true,
                    });
                    $(".chosen-select").chosen().change(settingsShow);
                    $('#summernote_help').summernote({
                        height: 300,
                        focus: true,
                        lang: get_lang_param('summernote_lang'),
                        onImageUpload: function(files, editor, welEditable) {
                            sendFile(files[0], editor, welEditable);
                        }
                    });
                }
            });
        });
*/
        //if (def_filename == "helper.php") {
        /*   setInterval(function(){
            check_update();
        },5000);*/
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=list_help",
            success: function(html) {
                $("#help_content").html(html);


            }
        });
    };
    if (ispath('stats')) {
        //if (def_filename == "stats.php") {
        /*  setInterval(function(){
            check_update();
        },5000);*/
    }
    if (ispath('create')) {
        $("textarea#msg").keyup(function() {
            if ($(this).val().length > 1) {
                $("textarea#msg").popover('hide');
                $("#for_msg").removeClass('has-error').addClass('has-success');
            } else {
                $("textarea#msg").popover('show');
                setTimeout(function() {
                    $("textarea#msg").popover('hide');
                }, 2000);
                $("#for_msg").addClass('has-error');
            }
        });
        /*
    $("select#subj").blur(function() {

        if ( $('select#subj').val() != 0 ){

            $('#for_subj').popover('hide');
            $('#for_subj').removeClass('has-error').addClass('has-success');
            //$('#for_subj');
        }
        else {

            $('#for_subj').popover('show');
             setTimeout(function(){ $("#for_subj").popover('hide'); }, 2000);
            $('#for_subj').addClass('has-error');

        }

    });
    */
        $("select#subj").change(function() {
            //console.log($('select#subj').val());
            if ($('select#subj').val() == 0) {
                $('#for_subj').popover('show');
                setTimeout(function() {
                    $("#for_subj").popover('hide');
                }, 2000);
                $('#for_subj').addClass('has-error');
                //$('#for_subj');
            } else if ($('select#subj').val() == null) {
                $('#for_subj').popover('show');
                setTimeout(function() {
                    $("#for_subj").popover('hide');
                }, 2000);
                $('#for_subj').addClass('has-error');
            } else {
                $('#for_subj').popover('hide');
                $('#for_subj').removeClass('has-error').addClass('has-success');
            }
            //console.log($('select#subj').val());
        });
        //if (def_filename == "new.php") {
        $('body').on('click', 'button#reset_ticket', function(event) {
            event.preventDefault();
            window.location = MyHOSTNAME + "create";
        });

        function check_form_ticket() {
            var z = $("#username").text();
            var s = $("#subj").val();
            var to = $("select#to").val();
            var m = $("#msg").val().length;
            var error_code = 0;
            if ($('#s_start').length) {
                //if ($(this).)
                $('button#start_upload').popover('show');
                error_code = 1;
            }
            if (!$('#s_start').length) {
                //if ($(this).)
                $('button#start_upload').popover('hide');
                error_code = 0;
            }
            if ($('#fio').val().length == 0) {
                error_code = 1;
                $('#fio').popover('show');
                $('#for_fio').addClass('has-error');
                setTimeout(function() {
                    $("#fio").popover('hide');
                }, 2000);
            }
            if (to == '0') {
                error_code = 1;
                $('#dsd').popover('show');
                $('#for_to').addClass('has-error');
                setTimeout(function() {
                    $("#dsd").popover('hide');
                }, 2000);
            }
            if (s == null) {
                error_code = 1;
                $("#for_subj").popover('show');
                $("#for_subj").addClass('has-error');
                setTimeout(function() {
                    $("#for_subj").popover('hide');
                }, 2000);
            }
            if (m == 0) {
                error_code = 1;
                $("#msg").popover('show');
                $("#for_msg").addClass('has-error');
                setTimeout(function() {
                    $("textarea#msg").popover('hide');
                }, 2000);
            }
            return error_code;
        }

        function check_form_ticket_client() {
            //var z=$("#username").text();
            var s = $("#subj").val();
            var to = $("select#to").val();
            var m = $("#msg").val().length;
            var error_code = 0;
            if ($('#s_start').length) {
                //if ($(this).)
                $('button#start_upload').popover('show');
                error_code = 1;
            }
            if (!$('#s_start').length) {
                //if ($(this).)
                $('button#start_upload').popover('hide');
                error_code = 0;
            }
            if (to == '0') {
                error_code = 1;
                $('#dsd').popover('show');
                $('#for_to').addClass('has-error');
            }
            if (s == 0) {
                error_code = 1;
                $("#for_subj").popover('show');
                $("#for_subj").addClass('has-error');
            }
            if (m == 0) {
                error_code = 1;
                $("#msg").popover('show');
                $("#for_msg").addClass('has-error');
            }
            return error_code;
        }

        function enter_ticket_client() {
            var u_do;
            if ($("#users_do").val() == null) {
                u_do = '0';
            } else if ($("#users_do").val() != null) {
                u_do = $("#users_do").val();
            }
            $('#enter_ticket').html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
            $('#reset_ticket').prop('disabled', true);
            $.ajax({
                type: "POST",
                //async: false,
                url: ACTIONPATH,
                data: "mode=add_ticket" + "&type_add=client" + "&user_init_id=" + encodeURIComponent($("#user_init_id").val()) + "&user_do=" + encodeURIComponent(u_do) + "&subj=" + encodeURIComponent($("#subj").val()) + "&msg=" + encodeURIComponent($("#msg").val()) + "&unit_id=" + encodeURIComponent($("#to").val()) + "&prio=" + encodeURIComponent($("#prio").val()) + "&hashname=" + encodeURIComponent($("#hashname").val()),
                success: function(html) {
                    //console.log(html);
                    window.location = MyHOSTNAME + "create?ok&h=" + html;
                }
            });
        }

        function enter_ticket() {
            var status_action = $("#status_action").val();
            var u_do;
            if (status_action == 'add') {
                //uploadObj.startUpload();
                $('#enter_ticket').html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
                $('#reset_ticket').prop('disabled', true);
                if ($("#users_do").val() == null) {
                    u_do = '0';
                } else if ($("#users_do").val() != null) {
                    u_do = $("#users_do").val();
                }
                $.ajax({
                    type: "POST",
                    //async: false,
                    url: ACTIONPATH,
                    data: "mode=add_ticket" + "&type_add=add" + "&fio=" + encodeURIComponent($("#username").text()) + "&tel=" + encodeURIComponent($("#new_tel").text()) + "&login=" + encodeURIComponent($("#new_login").text()) + "&pod=" + encodeURIComponent($("#new_unit").text()) + "&adr=" + encodeURIComponent($("#new_adr").text()) + "&tel=" + encodeURIComponent($("#new_tel").text()) + "&mail=" + encodeURIComponent($("#new_mail").text()) + "&posada=" + encodeURIComponent($("#new_posada").text()) + "&user_init_id=" + encodeURIComponent($("#user_init_id").val()) + "&user_do=" + encodeURIComponent(u_do) + "&subj=" + encodeURIComponent($("#subj").val()) + "&msg=" + encodeURIComponent($("#msg").val()) + "&unit_id=" + encodeURIComponent($("#to").val()) + "&prio=" + encodeURIComponent($("#prio").val()) + "&hashname=" + encodeURIComponent($("#hashname").val()),
                    success: function(html) {
                        //window.location = "new.php?ok&h="+html;
                        window.location = MyHOSTNAME + "create?ok&h=" + html;
                    }
                });
            }
            if (status_action == 'edit') {
                //uploadObj.startUpload();
                $('#enter_ticket').html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
                $('#reset_ticket').prop('disabled', true);
                if ($("#users_do").val() == null) {
                    u_do = '0';
                } else if ($("#users_do").val() != null) {
                    u_do = $("#users_do").val();
                }
                $.ajax({
                    type: "POST",
                    //async: false,
                    url: ACTIONPATH,
                    data: "mode=add_ticket" + "&type_add=edit" + "&client_id_param=" + encodeURIComponent($("#client_id_param").val()) + "&tel=" + encodeURIComponent($("#edit_tel").text()) + "&login=" + encodeURIComponent($("#edit_login").text()) + "&pod=" + encodeURIComponent($("#edit_unit").text()) + "&adr=" + encodeURIComponent($("#edit_adr").text()) + "&tel=" + encodeURIComponent($("#edit_tel").text()) + "&mail=" + encodeURIComponent($("#edit_mail").text()) + "&posada=" + encodeURIComponent($("#edit_posada").text()) + "&user_init_id=" + encodeURIComponent($("#user_init_id").val()) + "&user_do=" + encodeURIComponent(u_do) + "&subj=" + encodeURIComponent($("#subj").val()) + "&msg=" + encodeURIComponent($("#msg").val()) + "&unit_id=" + encodeURIComponent($("#to").val()) + "&prio=" + encodeURIComponent($("#prio").val()) + "&hashname=" + encodeURIComponent($("#hashname").val()),
                    success: function(html) {
                        //console.log(html);
                        window.location = MyHOSTNAME + "create?ok&h=" + html;
                    }
                });
            }
        }
        /*
    var lang_dd= get_lang_param('TICKET_file_upload_msg');

    var uploadObj = $("#fileuploader").uploadFile({
        allowedTypes: "jpeg,jpg,png,gif,doc,docx,xls,xlsx,rtf,pdf,zip,bmp",
        url: MyHOSTNAME+"/sys/upload.php",
        multiple:true,
        autoSubmit:false,
        fileName:"myfile",
        formData: {"hashname":$("#hashname").val()},
        maxFileSize:30000000,
        showStatusAfterSuccess:false,
        dragDropStr: "<span><b>"+lang_dd+"</b></span>",
        abortStr:"abort",
        cancelStr:get_lang_param('upload_cancel'),
        doneStr:"done",
        sizeErrorStr:get_lang_param('upload_errorsize')

    }
    );
    */
        var p = new RegExp('(\.|\/)' + $('input#file_types').val());
        // Initialize the jQuery File Upload widget:
        $('#fileupload').fileupload({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: MyHOSTNAME + 'sys/index.php',
            autoUpload: true,
            disableValidation: false,
            acceptFileTypes: p,
            maxFileSize: $('input#file_size').val(),
            formData: {
                hashname: $('input#hashname').val()
            }
        }).on('fileuploadprocessalways', function(e, data) {
            $(this).removeClass('fileupload-processing');
        }).on('fileuploaddone', function(e, data) {
            $.each(data.result.files, function(index, file) {
                if (file.url) {
                    var link = $('<a>').attr('target', '_blank').prop('href', file.url);
                    $(data.context.children()[index]).wrap(link);
                } else if (file.error) {
                    var error = $('<span class="text-danger"/>').text(file.error);
                    $(data.context.children()[index]).append('<br>').append(error);
                }
            });
        }).on('fileuploadstop', function(e, data) {
            /*if (check_form_ticket() == 0 ) {
     enter_ticket();
     }
     
     */
            //enter_ticket();
        }).on('fileuploadadd', function(e, data) {
            /* ... 
    $("#uploadBtn").on('click',function () {
                data.submit();
                console.log('hello');
            });
            */
        }).on('fileuploadsubmit', function(e, data) {
            console.log(data);
        });
    }
    if (ispath('list')) {
        $('body').on('click', 'button#action_list_ok', function(event) {
            event.preventDefault();
            var status_ll = $(this).attr('status');
            var tr_id = $(this).attr('value');
            var elem = '#tr_' + tr_id;
            var us = $(this).attr('user');
            if (status_ll == "ok") {
                $(this).attr("status", "unok");
                $(this).html('<i class=\"fa fa-check-circle-o\"></i>');
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=status_no_ok" + "&tid=" + tr_id + "&user=" + encodeURIComponent(us),
                    success: function() {
                        $(elem).removeClass().addClass('success', 1000).addClass('pops');
                    }
                });
            }
            if (status_ll == "unok") {
                $(this).attr("status", "ok");
                $(this).html('<i class=\"fa fa-circle-o\"></i>');
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=status_ok" + "&tid=" + tr_id + "&user=" + encodeURIComponent(us),
                    success: function() {
                        $(elem).removeClass('success', 1000);
                    }
                });
            }
        });
        $('body').on('click', 'button#action_arch_now', function(event) {
            event.preventDefault();
            var tr_id = $(this).attr('value');
            var elem = '#tr_' + tr_id;
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=arch_now" + "&tid=" + tr_id,
                success: function() {
                    $(elem).fadeOut(500);
                }
            });
        });
        $('body').on('click', 'button#action_list_lock', function(event) {
            event.preventDefault();
            var status_ll = $(this).attr('status');
            var tr_id = $(this).attr('value');
            var elem = '#tr_' + tr_id;
            var us = $(this).attr('user');
            if (status_ll == "lock") {
                $(this).attr("status", "unlock");
                $(this).html('<i class=\"fa fa-lock\"></i>');
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=lock" + "&tid=" + tr_id + "&user=" + encodeURIComponent(us),
                    success: function() {
                        $(elem).removeClass().addClass('warning', 1000);
                        $(elem).addClass('pops');
                    }
                });
            }
            if (status_ll == "unlock") {
                $(this).attr("status", "lock");
                $(this).html('<i class=\"fa fa-unlock\"></i>');
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=unlock" + "&tid=" + tr_id,
                    success: function() {
                        $(elem).removeClass('warning', 1000);
                    }
                });
            }
        });
        $('body').on('click', 'button#sort_list', function(event) {
            event.preventDefault();
            var pt = $("#page_type").attr('value');
            var st = $(this).attr('value');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=sort_list" + "&pt=" + encodeURIComponent(pt) + "&st=" + encodeURIComponent(st),
                success: function() {
                    window.location = MyHOSTNAME + "list?" + pt;
                }
            });
        });
        $('body').on('click', 'button#list_set_ticket', function(event) {
            event.preventDefault();
            var pt = $("#page_type").attr('value');
            var z = $(this).text();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=set_list_count" + "&pt=" + encodeURIComponent(pt) + "&v=" + encodeURIComponent(z),
                success: function() {
                    window.location = MyHOSTNAME + "list?" + pt;
                }
            });
        });
        $('[data-toggle="tooltip"]').tooltip({
            container: 'body',
            html: true
        });
        var options_client = {
            currentPage: $("#cur_page").val(),
            totalPages: $("#total_pages").val(),
            bootstrapMajorVersion: 3,
            size: "small",
            itemContainerClass: function(type, page, current) {
                return (page === current) ? "active" : "pointer-cursor";
            },
            onPageClicked: function(e, originalEvent, type, page) {
                var current = $("#curent_page").attr('value');
                if (page != current) {
                    $("#spinner").fadeIn(300);
                    $("#curent_page").attr('value', page);
                    $.ajax({
                        type: "POST",
                        url: MyHOSTNAME + "inc/client.list_content.inc.php",
                        data: "menu=out" + "&page=" + encodeURIComponent(page),
                        success: function(html) {
                            $("#content").hide().html(html).fadeIn(500);
                            $("#spinner").hide();
                            $('[data-toggle="tooltip"]').tooltip({
                                container: 'body',
                                html: true
                            });
                            makemytime(true);
                        }
                    });
                }
            }
        }
        $('#example_client').bootstrapPaginator(options_client);
        var options_in = {
            currentPage: $("#cur_page").val(),
            totalPages: $("#total_pages").val(),
            bootstrapMajorVersion: 3,
            size: "small",
            itemContainerClass: function(type, page, current) {
                return (page === current) ? "active" : "pointer-cursor";
            },
            onPageClicked: function(e, originalEvent, type, page) {
                var current = $("#curent_page").attr('value');
                if (page != current) {
                    $("#curent_page").attr('value', page);
                    $("#spinner").fadeIn(300);
                    $.ajax({
                        type: "POST",
                        url: MyHOSTNAME + "inc/list_content.inc.php",
                        data: "menu=in" + "&page=" + encodeURIComponent(page),
                        success: function(html) {
                            $("#content").hide().html(html).fadeIn(500);
                            $("#spinner").hide();
                            $('[data-toggle="tooltip"]').tooltip({
                                container: 'body',
                                html: true
                            });
                            //$('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                            make_popover();
                            makemytime(true);
                        }
                    });
                }
            }
        }
        var options_out = {
            currentPage: $("#cur_page").val(),
            totalPages: $("#total_pages").val(),
            bootstrapMajorVersion: 3,
            size: "small",
            itemContainerClass: function(type, page, current) {
                return (page === current) ? "active" : "pointer-cursor";
            },
            onPageClicked: function(e, originalEvent, type, page) {
                var current = $("#curent_page").attr('value');
                if (page != current) {
                    $("#spinner").fadeIn(300);
                    $("#curent_page").attr('value', page);
                    $.ajax({
                        type: "POST",
                        url: MyHOSTNAME + "inc/list_content.inc.php",
                        data: "menu=out" + "&page=" + encodeURIComponent(page),
                        success: function(html) {
                            $("#content").hide().html(html).fadeIn(500);
                            $("#spinner").hide();
                            $('[data-toggle="tooltip"]').tooltip({
                                container: 'body',
                                html: true
                            });
                            //$('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                            make_popover();
                            makemytime(true);
                        }
                    });
                }
            }
        }
        var options_arch = {
            currentPage: $("#cur_page").val(),
            totalPages: $("#total_pages").val(),
            bootstrapMajorVersion: 3,
            size: "small",
            itemContainerClass: function(type, page, current) {
                return (page === current) ? "active" : "pointer-cursor";
            },
            onPageClicked: function(e, originalEvent, type, page) {
                var current = $("#curent_page").attr('value');
                if (page != current) {
                    $("#curent_page").attr('value', page);
                    $("#spinner").fadeIn(300);
                    $.ajax({
                        type: "POST",
                        url: MyHOSTNAME + "inc/list_content.inc.php",
                        data: "menu=arch" + "&page=" + encodeURIComponent(page),
                        success: function(html) {
                            $("#content").hide().html(html).fadeIn(500);
                            $("#spinner").hide();
                            $('[data-toggle="tooltip"]').tooltip({
                                container: 'body',
                                html: true
                            });
                            //$('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                            make_popover();
                            makemytime(true);
                        }
                    });
                }
            }
        }
        var options_client_out = {
            currentPage: $("#cur_page").val(),
            totalPages: $("#total_pages").val(),
            bootstrapMajorVersion: 3,
            size: "small",
            itemContainerClass: function(type, page, current) {
                return (page === current) ? "active" : "pointer-cursor";
            },
            onPageClicked: function(e, originalEvent, type, page) {
                var current = $("#curent_page").attr('value');
                if (page != current) {
                    $("#spinner").fadeIn(300);
                    $("#curent_page").attr('value', page);
                    $.ajax({
                        type: "POST",
                        url: MyHOSTNAME + "inc/client.list_content.inc.php",
                        data: "menu=out" + "&page=" + encodeURIComponent(page),
                        success: function(html) {
                            $("#content").hide().html(html).fadeIn(500);
                            $("#spinner").hide();
                            $('[data-toggle="tooltip"]').tooltip({
                                container: 'body',
                                html: true
                            });
                            //$('[data-toggle="tooltip"]').tooltip({container: 'body', html:true});
                            make_popover();
                            makemytime(true);
                        }
                    });
                }
            }
        }
        $('#example_in').bootstrapPaginator(options_in);
        $('#example_out').bootstrapPaginator(options_out);
        $('#example_arch').bootstrapPaginator(options_arch);
        $('#client_example_out').bootstrapPaginator(options_client_out);
    }
    if (ispath('config')) {
        $('body').on('click', 'button#check_update', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=check_version",
                success: function(html) {
                    $("#result_update").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#result_update').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        $("select#mail_type").change(function() {
            if ($('select#mail_type').val() == "sendmail") {
                $('#smtp_div').hide();
            } else if ($('select#mail_type').val() == "SMTP") {
                $('#smtp_div').show();
            }
        });
        $('body').on('click', 'button#conf_edit_pb', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=conf_edit_pb" + "&api=" + encodeURIComponent($("input#pb_api").val()),
                success: function(html) {
                    $("#conf_edit_pb_res").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#conf_edit_pb_res').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        $('body').on('click', 'button#conf_edit_main', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=conf_edit_main" + "&name_of_firm=" + encodeURIComponent($("input#name_of_firm").val()) + "&title_header=" + encodeURIComponent($("input#title_header").val()) + "&ldap=" + encodeURIComponent($("input#ldap_ip").val()) + "&ldapd=" + encodeURIComponent($("input#ldap_domain").val()) + "&hostname=" + encodeURIComponent($("input#hostname").val()) + "&mail=" + encodeURIComponent($("input#mail").val()) + "&days2arch=" + encodeURIComponent($("input#days2arch").val()) + "&first_login=" + encodeURIComponent($("#first_login").val()) + "&fix_subj=" + encodeURIComponent($("#fix_subj").val()) + "&file_uploads=" + encodeURIComponent($("#file_uploads").val()) + "&file_types=" + encodeURIComponent($("#file_types").val()) + "&node_port=" + encodeURIComponent($("#node_port").val()) + "&time_zone=" + encodeURIComponent($("#time_zone").val()) + "&file_size=" + encodeURIComponent($("#file_size").val() * 1024 * 1024)+"&allow_register=" + encodeURIComponent($("#allow_register").val()),
                dataType: "json",
                success: function(html) {

 $.each(html, function(i, item) {
                        
                        if (item.res == true) { $("#conf_edit_main_res").hide().html(item.msg).fadeIn(500);
                    setTimeout(function() {
                        $('#conf_edit_main_res').children('.alert').fadeOut(500);
                    }, 3000); }
                        else if (item.res == false) { 
                            //$('#res').html(item.msg); 
                            $("#conf_edit_main_res").hide().html(item.msg).fadeIn(500);
                           
                            }
                            //console.log(item.msg);
                        
                    });


                    
                }
            });
        });
        $('body').on('click', 'button#conf_test_mail', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=conf_edit_mail" + "&mail_active=" + encodeURIComponent($("#mail_active").val()) + "&host=" + encodeURIComponent($("#host").val()) + "&port=" + encodeURIComponent($("#port").val()) + "&auth=" + encodeURIComponent($("#auth").val()) + "&auth_type=" + encodeURIComponent($("#auth_type").val()) + "&username=" + encodeURIComponent($("#username").val()) + "&password=" + encodeURIComponent($("#password").val()) + "&from=" + encodeURIComponent($("#from").val()) + "&type=" + encodeURIComponent($("#mail_type").val()),
                success: function(html) {
                    $("#conf_edit_mail_res").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#conf_edit_mail_res').children('.alert').fadeOut(500);
                    }, 3000);
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=conf_test_mail",
                        success: function(html) {
                            $('#conf_test_mail_res').html(html);
                        }
                    });
                }
            });
        });
        $('body').on('click', 'button#conf_edit_mail', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=conf_edit_mail" + "&mail_active=" + encodeURIComponent($("#mail_active").val()) + "&host=" + encodeURIComponent($("#host").val()) + "&port=" + encodeURIComponent($("#port").val()) + "&auth=" + encodeURIComponent($("#auth").val()) + "&auth_type=" + encodeURIComponent($("#auth_type").val()) + "&username=" + encodeURIComponent($("#username").val()) + "&password=" + encodeURIComponent($("#password").val()) + "&from=" + encodeURIComponent($("#from").val()) + "&type=" + encodeURIComponent($("#mail_type").val()),
                success: function(html) {
                    $("#conf_edit_mail_res").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#conf_edit_mail_res').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        if ($('select#mail_type').val() == "sendmail") {
            $('#smtp_div').hide();
        } else if ($('select#mail_type').val() == "SMTP") {
            $('#smtp_div').show();
        }
    }
    if (ispath('deps')) {
        $.fn.editable.defaults.mode = 'inline';
        $('a#edit_deps').each(function(i, e) {
            $(e).editable({
                inputclass: 'input-sm',
                emptytext: 'пусто',
                params: {
                    mode: 'edit_deps'
                }
            });
        });
        $('body').on('click', 'button#deps_del', function(event) {
            event.preventDefault();
            var ids = $(this).attr('value');
            bootbox.confirm(get_lang_param('JS_del'), function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=deps_del" + "&id=" + ids,
                        success: function(html) {
                            $("#content_deps").html(html);
                            $('a#edit_deps').each(function(i, e) {
                                $(e).editable({
                                    inputclass: 'input-sm',
                                    emptytext: 'пусто',
                                    params: {
                                        mode: 'edit_deps'
                                    }
                                });
                            });
                        }
                    });
                }
                if (result == false) {
                    console.log('false');
                }
            });
            /*
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=deps_del"+
                "&id="+$(this).attr('value'),
            success: function(html) {
                $("#content_deps").html(html);

            }
        });
        */
        });
        $('body').on('click', 'button#deps_add', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=deps_add" + "&text=" + encodeURIComponent($("#deps_text").val()),
                success: function(html) {
                    $("#content_deps").html(html);
                    $("#deps_text").val('');
                    $('a#edit_deps').each(function(i, e) {
                        $(e).editable({
                            inputclass: 'input-sm',
                            emptytext: 'пусто',
                            params: {
                                mode: 'edit_deps'
                            }
                        });
                    });
                }
            });
        });
        $('body').on('click', 'button#deps_show', function(event) {
            event.preventDefault();
            var u = $(this).attr('value');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=deps_show" + "&id=" + u,
                success: function(html) {
                    window.location = MyHOSTNAME + "deps";
                }
            });
        });
        $('body').on('click', 'button#deps_hide', function(event) {
            event.preventDefault();
            var u = $(this).attr('value');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=deps_hide" + "&id=" + u,
                success: function(html) {
                    window.location = MyHOSTNAME + "deps";
                }
            });
        });
    }
    if (ispath('files')) {
        $('body').on('click', 'button#files_del', function(event) {
            event.preventDefault();
            var ids = $(this).attr('value');
            bootbox.confirm(get_lang_param('JS_del'), function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=files_del" + "&id=" + ids,
                        success: function(html) {
                            window.location = MyHOSTNAME + "files";
                        }
                    });
                }
            });
        });
    }
    if (ispath('units')) {
        $('body').on('click', 'button#units_del', function(event) {
            event.preventDefault();
            var ids = $(this).attr('value');
            bootbox.confirm(get_lang_param('JS_del'), function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=units_del" + "&id=" + ids,
                        success: function(html) {
                            $("#content_units").html(html);
                        }
                    });
                }
            });
        });
        $('body').on('click', 'button#units_add', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=units_add" + "&text=" + encodeURIComponent($("#units_text").val()),
                success: function(html) {
                    $("#content_units").html(html);
                    $("#units_text").val('');
                }
            });
        });
    }
    if (ispath('users')) {
        //push_msg_action2user
        $('[data-toggle="tooltip"]').tooltip({
                                container: 'body',
                                html: true
                            });
        
        $('body').on('click', 'button#make_logout_user', function(event) {
            event.preventDefault();
            var usid = $(this).attr('value');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=push_msg_action2user" + "&user=" + encodeURIComponent(usid) + "&op=logout",
                success: function(html) {
                    //alert(html);
                    window.location = MyHOSTNAME + "users";
                }
            });
        });
        $("input#fio_find_admin").keyup(function() {
            var t = $(this).val(),
                t_l = $(this).val().length;
            $.ajax({
                type: "POST",
                url: MyHOSTNAME + "/inc/users.inc.php",
                data: "menu=list" + "&page=1" + "&t=" + t,
                success: function(html) {
                    $("#content_users").html(html);
                    $('[data-toggle="tooltip"]').tooltip({
                                container: 'body',
                                html: true
                            });
                }
            });
        });
        $("#my-select").select2({
            allowClear: true,
            maximumSelectionSize: 5,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found')
        });
        var options_workers = {
            currentPage: $("#cur_page").val(),
            totalPages: $("#total_pages").val(),
            bootstrapMajorVersion: 3,
            size: "small",
            itemContainerClass: function(type, page, current) {
                return (page === current) ? "active" : "pointer-cursor";
            },
            onPageClicked: function(e, originalEvent, type, page) {
                var current = $("#curent_page").attr('value');
                if (page != current) {
                    $("#curent_page").attr('value', page);
                    $.ajax({
                        type: "POST",
                        url: MyHOSTNAME + "inc/users.inc.php",
                        data: "page=" + encodeURIComponent(page) + "&menu=list",
                        success: function(html) {
                            $("#content_users").hide().html(html).fadeIn(500);
                            $('[data-toggle="tooltip"]').tooltip({
                                container: 'body',
                                html: true
                            });
                        }
                    });
                }
            }
        }
        $('#example_users').bootstrapPaginator(options_workers);
        $("input#exampleInputPassword1").keyup(function() {
            if ($(this).val().length > 3) {
                $ //("#errors").val('false');
                my_errors.pass = false;
                $("#pass_user_grp").removeClass('has-error').addClass('has-success');
            } else {
                //$("#errors").val('true');
                my_errors.pass = true;
                $("#pass_user_grp").removeClass('has-success').addClass('has-error');
            }
        });
        $('#ldap_auth_key').on('ifChanged', function(event) {
            if ($(this).is(":checked")) {
                $('#exampleInputPassword1').prop("disabled", true);
            } else {
                $('#exampleInputPassword1').prop("disabled", false);
            }
        });
        //input[type=radio][name=optionsRadios]
        $('input[type=radio][name=optionsRadios]').on('ifChanged', function(event) {
            if (this.value == '0') {
                $('#priv_add_client').iCheck('enable');
                $('#priv_edit_client').iCheck('enable');
                $('#my-select').prop("disabled", false);
            } else if (this.value == '1') {
                $('#priv_add_client').iCheck('enable');
                $('#priv_edit_client').iCheck('enable');
                $('#my-select').prop("disabled", false);
            } else if (this.value == '2') {
                $('#priv_add_client').iCheck('enable');
                $('#priv_edit_client').iCheck('enable');
                $('#my-select').prop("disabled", false);
            } else if (this.value == '4') {
                $('#priv_add_client').iCheck('disable');
                $('#priv_edit_client').iCheck('disable');
                $('#my-select').prop("disabled", true);
                //my-select
            }
        });
        $("input#login_user").keyup(function() {
            if ($(this).val().length > 3) {
                $("#login_user_grp").removeClass('has-error').addClass('has-success');
                //$("#errors").val('false');
                my_errors.login = false;
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ACTIONPATH,
                    data: "mode=check_login" + "&login=" + $(this).val(),
                    success: function(html) {
                        $.each(html, function(i, item) {
                            if (item.check_login_status == true) {
                                $("#login_user_grp").removeClass('has-error').addClass('has-success');
                                //$("#errors").val('false');
                                my_errors.login = false;
                            } else if (item.check_login_status == false) {
                                $("#login_user_grp").addClass('has-error');
                                //$("#errors").val('true');
                                my_errors.login = true;
                            }
                        });
                        //console.log(html);
                    }
                });
            } else {
                $("#login_user_grp").addClass('has-error');
                //$("#errors").val('true');
                my_errors.login = true;
            }
        });
        $("input#login_user2").keyup(function() {
            var exparam = $(this).attr('exclude-param');
            if ($(this).val().length > 3) {
                $("#login_user_grp").removeClass('has-error').addClass('has-success');
                //$("#errors").val('false');
                my_errors.login = false;
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ACTIONPATH,
                    data: "mode=check_login" + "&login=" + $(this).val() + "&exclude=" + exparam,
                    success: function(html) {
                        $.each(html, function(i, item) {
                            if (item.check_login_status == true) {
                                $("#login_user_grp").removeClass('has-error').addClass('has-success');
                                //$("#errors").val('false');
                                my_errors.login = false;
                            } else if (item.check_login_status == false) {
                                $("#login_user_grp").addClass('has-error');
                                //$("#errors").val('true');
                                my_errors.login = true;
                            }
                        });
                        //console.log(html);
                    }
                });
            } else {
                $("#login_user_grp").addClass('has-error');
                //$("#errors").val('true');
                my_errors.login = true;
            }
        });
        $("input#fio_user").keyup(function() {
            if ($(this).val().length > 3) {
                //$("#errors").val('false');
                my_errors.fio = false;
                $("#fio_user_grp").removeClass('has-error').addClass('has-success');
            } else {
                //$("#errors").val('true');
                my_errors.fio = true;
                $("#fio_user_grp").removeClass('has-success').addClass('has-error');
            }
        });
        $('body').on('click', 'button#create_user', function(event) {
            event.preventDefault();
            if ($("#fio_user").val().length < 3) {
                //$("#errors").val('true');
                my_errors.fio = true;
                $("#fio_user_grp").addClass('has-error');
            }
            if ($("#exampleInputPassword1").val().length < 3) {
                //$("#errors").val('true');
                my_errors.pass = true;
                $("#pass_user_grp").addClass('has-error');
            }
            if ($("#login_user").val().length < 3) {
                //$("#errors").val('true');
                my_errors.login = true;
                $("#login_user_grp").addClass('has-error');
            }
            //var er=$("#errors").val();
            var er = my_errors.login || my_errors.fio || my_errors.pass;
            if (er == false) {
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=add_user" + "&fio=" + encodeURIComponent($("#fio_user").val()) + "&login=" + encodeURIComponent($("#login_user").val()) + "&pass=" + encodeURIComponent($("#exampleInputPassword1").val()) + "&unit=" + encodeURIComponent($("#my-select").val()) + "&priv=" + encodeURIComponent($("input[type=radio][name=optionsRadios]:checked").val()) + "&ldap_auth_key=" + encodeURIComponent($("#ldap_auth_key").prop('checked')) + "&mess=" + encodeURIComponent($("textarea#mess").val()) + "&mess_t=" + encodeURIComponent($("input#msg_title").val()) + "&push=" + encodeURIComponent($("input#push").val()) + "&tel=" + encodeURIComponent($("input#tel").val()) + "&skype=" + encodeURIComponent($("input#skype").val()) + "&adr=" + encodeURIComponent($("input#adr").val()) + "&posada=" + encodeURIComponent($("#posada").val()) + "&pidrozdil=" + encodeURIComponent($("#pidrozdil").val()) + "&lang=" + encodeURIComponent($('select#lang').val()) + "&priv_add_client=" + encodeURIComponent($("#priv_add_client").prop('checked')) + "&priv_edit_client=" + encodeURIComponent($("#priv_edit_client").prop('checked')) + "&mail=" + encodeURIComponent($("#mail").val())+
                    "&msg_type="+encodeURIComponent($("input[type=radio][name=optionsRadios_msg]:checked").val()),
                    success: function(html) {
                        window.location = MyHOSTNAME + "users?create&ok";
                    }
                });
            } else {
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");
            }
        });
        $('body').on('click', 'button#edit_user', function(event) {
            event.preventDefault();
            var usid = $(this).attr('value');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=edit_user" + "&fio=" + encodeURIComponent($("#fio_user").val()) + "&login=" + encodeURIComponent($("#login_user2").val()) + "&pass=" + encodeURIComponent($("#exampleInputPassword1").val()) + "&unit=" + encodeURIComponent($("#my-select").val()) + "&priv=" + encodeURIComponent($("input[type=radio][name=optionsRadios]:checked").val()) + "&ldap_auth_key=" + encodeURIComponent($("#ldap_auth_key").prop('checked')) + "&mess=" + encodeURIComponent($("textarea#mess").val()) + "&mess_t=" + encodeURIComponent($("input#msg_title").val()) + "&push=" + encodeURIComponent($("input#push").val()) + "&tel=" + encodeURIComponent($("input#tel").val()) + "&skype=" + encodeURIComponent($("input#skype").val()) + "&adr=" + encodeURIComponent($("input#adr").val()) + "&posada=" + encodeURIComponent($("#posada").val()) + "&pidrozdil=" + encodeURIComponent($("#pidrozdil").val()) + "&lang=" + encodeURIComponent($('select#lang').val()) + "&priv_add_client=" + encodeURIComponent($("#priv_add_client").prop('checked')) + "&priv_edit_client=" + encodeURIComponent($("#priv_edit_client").prop('checked')) + "&mail=" + encodeURIComponent($("#mail").val()) + "&status=" + encodeURIComponent($("#lock").val()) + "&idu=" + encodeURIComponent(usid)+
                    "&msg_type="+encodeURIComponent($("input[type=radio][name=optionsRadios_msg]:checked").val()),
                success: function(html) {
                    //alert(html);
                    window.location = MyHOSTNAME + "users?edit=" + usid + "&ok";
                }
            });
        });
    }
    if (ispath('subj')) {
        $('body').on('click', 'button#subj_del', function(event) {
            event.preventDefault();
            var ids = $(this).attr('value');
            bootbox.confirm(get_lang_param('JS_del'), function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=subj_del" + "&id=" + ids,
                        success: function(html) {
                            $("#content_subj").html(html);
                        }
                    });
                }
            });
        });
        $('body').on('click', 'button#subj_add', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=subj_add" + "&text=" + encodeURIComponent($("#subj_text").val()),
                success: function(html) {
                    $("#content_subj").html(html);
                    $("#subj_text").val('');
                }
            });
        });
    }
    if (ispath('clients')) {
        $('body').on('click', 'button#create_user_approve', function(event) {
            event.preventDefault();
            //var er=$("#errors").val();
            $('#res').html('');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=add_user_approve" + "&fio=" + encodeURIComponent($("#fio_user").val()) + "&login=" + encodeURIComponent($("#login_user").val()) + "&tel=" + encodeURIComponent($("input#tel").val()) + "&skype=" + encodeURIComponent($("input#skype").val()) + "&adr=" + encodeURIComponent($("input#adr").val()) + "&posada=" + encodeURIComponent($("#posada").val()) + "&pidrozdil=" + encodeURIComponent($("#pidrozdil").val()) + "&mail=" + encodeURIComponent($("#mail").val()),
                dataType: "json",
                success: function(html) {
                    //$("#res").hide().html(html).fadeIn(500);
                    $.each(html, function(i, item) {
                        
                        if (item.res == true) { window.location = MyHOSTNAME + "clients?add&ok"; }
                        else if (item.res == false) { 
                            $('#res').html(item.msg); 
                           
                            }
                            //console.log(item.msg);
                        
                    });
                    //window.location = MyHOSTNAME + "clients?add&ok";
                }
            });
        });
        $('body').on('click', 'button#edit_user_approve', function(event) {
            event.preventDefault();
            var usrid = $(this).attr('value');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=edit_user_approve" + "&fio=" + encodeURIComponent($("#fio_user").val()) + "&login=" + encodeURIComponent($("#login_user2").val()) + "&tel=" + encodeURIComponent($("input#tel").val()) + "&skype=" + encodeURIComponent($("input#skype").val()) + "&adr=" + encodeURIComponent($("input#adr").val()) + "&posada=" + encodeURIComponent($("#posada").val()) + "&pidrozdil=" + encodeURIComponent($("#pidrozdil").val()) + "&cid=" + encodeURIComponent(usrid) + "&mail=" + encodeURIComponent($("#mail").val()),
                dataType: "json",
                success: function(html) {
                    //$("#res").hide().html(html).fadeIn(500);
                    
                                        $.each(html, function(i, item) {
                        
                        if (item.res == true) { window.location = MyHOSTNAME + "clients?edit=" + usrid + "&ok"; }
                        else if (item.res == false) { 
                            $('#res').html(item.msg); 
                           
                            }
                            //console.log(item.msg);
                        
                    });


                }
            });
        });
        $("input#fio_find_admin").keyup(function() {
            var t = $(this).val(),
                t_l = $(this).val().length;
            $.ajax({
                type: "POST",
                url: MyHOSTNAME + "/inc/clients.inc.php",
                data: "menu=list" + "&page=1" + "&t=" + t,
                success: function(html) {
                    $("#content_clients").html(html);
                }
            });
        });
        var options_clients = {
            currentPage: $("#cur_page").val(),
            totalPages: $("#total_pages").val(),
            bootstrapMajorVersion: 3,
            size: "small",
            itemContainerClass: function(type, page, current) {
                return (page === current) ? "active" : "pointer-cursor";
            },
            onPageClicked: function(e, originalEvent, type, page) {
                var current = $("#curent_page").attr('value');
                if (page != current) {
                    $("#curent_page").attr('value', page);
                    $.ajax({
                        type: "POST",
                        url: MyHOSTNAME + "inc/clients.inc.php",
                        data: "page=" + encodeURIComponent(page) + "&menu=list",
                        success: function(html) {
                            $("#content_clients").hide().html(html).fadeIn(500);
                            $('[data-toggle="tooltip"]').tooltip({
                                container: 'body',
                                html: true
                            });
                        }
                    });
                }
            }
        }
        $('#example_clients').bootstrapPaginator(options_clients);
    }
    if (ispath('posada')) {
        $('body').on('click', 'button#posada_add', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=posada_add" + "&text=" + encodeURIComponent($("#posada_text").val()),
                success: function(html) {
                    $("#content_posada").html(html);
                    $("#posada_text").val('');
                }
            });
        });
        $('body').on('click', 'button#posada_del', function(event) {
            event.preventDefault();
            var ids = $(this).attr('value');
            bootbox.confirm(get_lang_param('JS_del'), function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=posada_del" + "&id=" + ids,
                        success: function(html) {
                            $("#content_posada").html(html);
                        }
                    });
                }
            });
        });
    }
    if (ispath('approve')) {
        $('body').on('click', 'button#action_aprove_yes', function(event) {
            event.preventDefault();
            var table_id = $(this).attr('value');
            var elem = ".table_" + table_id;
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=aprove_yes" + "&id=" + encodeURIComponent(table_id),
                success: function() {
                    $(elem).fadeOut(500);
                }
            });
        });
        $('body').on('click', 'button#action_aprove_no', function(event) {
            event.preventDefault();
            var table_id = $(this).attr('value');
            var elem = ".table_" + table_id;
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=aprove_no" + "&id=" + encodeURIComponent(table_id),
                success: function() {
                    $(elem).fadeOut(500);
                }
            });
        });
    }
    if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
        $('body').on('click', 'button#dashboard_set_ticket', function(event) {
            event.preventDefault();
            var p = $(this).text();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=dashboard_t" + "&p=" + encodeURIComponent(p),
                success: function(html) {
                    $('#spinner').show();
                    $('#dashboard_t').html(html);
                    $('#spinner').hide();
                    $('[data-toggle="tooltip"]').tooltip('hide');
                    $('[data-toggle="tooltip"]').tooltip({
                        container: 'body',
                        html: true
                    });
                    makemytime(true);
                    make_popover();
                }
            });
        });
        //alert(ACTIONPATH);
        $('body').on('click', 'a#more_news', function(event) {
            event.preventDefault();
            var tid = $(this).attr('value');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=last_news" + "&v=" + encodeURIComponent($(this).attr('value')),
                success: function(html) {
                    $('#last_news').html(html);
                    $('[data-toggle="tooltip"]').tooltip('hide');
                    $('[data-toggle="tooltip"]').tooltip({
                        container: 'body',
                        html: true
                    });
                    makemytime(false);
                }
            });
        });
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=last_news",
            success: function(html) {
                $('#last_news').html(html);
                //console.log($('#then').html());
                makemytime(false);
            }
        });
        $('#spinner').show();
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=dashboard_t",
            success: function(html) {
                $('#dashboard_t').html(html);
                $('#spinner').hide();
                $('[data-toggle="tooltip"]').tooltip({
                    container: 'body',
                    html: true
                });
                makemytime(true);
                make_popover();
            }
        });
        /*     setInterval(function(){
            check_update_index();


        },5000);*/
    }
    $('body').on('click', 'a#go_back', function(event) {
        event.preventDefault();
        history.back(1);
    });
    $('body').on('click', 'a#print_t', function() {
        window.print();
    });
    /*
    $('body').on('click', 'button#do_report', function(event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=get_report"+
                "&id="+$("#user_report").val()+
                "&s="+$("#d_start").val()+
                "&e="+$("#d_stop").val(),
            success: function(html) {
                $("#content_report").html(html);

            }
        });

    });
   */
    /*
    $('body').on('click', 'button#editable_enable', function(event) {
        event.preventDefault();
        $('#edit_subj_ticket').editable('toggleDisabled');
        $('#edit_msg_ticket').editable('toggleDisabled');
    });
*/
    /*if (def_filename == "reports.php") {

        $('#reportrange').daterangepicker(
            {
                ranges: {
                    'Сьогодні': [moment(), moment()],
                    'Вчора': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'За тиждень': [moment().subtract('days', 6), moment()],
                    'За 30 днів': [moment().subtract('days', 29), moment()],
                    'За місяць': [moment().startOf('month'), moment().endOf('month')],
                    'Прошлий місяць': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                },
                startDate: moment().subtract('days', 29),
                endDate: moment()
            },
            function(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#d_start').attr('value', start.format('YYYY-MM-DD'));
                $('#d_stop').attr('value', end.format('YYYY-MM-DD'));
            }
        );

    }
*/
});