//var my_errors = {fio: false, login: false, pass: false};
$(document).ready(function() {
    $.ajaxSetup({
        // Disable caching of AJAX responses
        cache: false
    });
    console.log(window.location.href);
    $(".main_i").css("display", "none");
    $(".main_i").fadeIn(800);
    $(".mf").css("display", "none");
    $(".mf").fadeIn(800);
    $("input[type='checkbox']:not(.simple), input[type='radio']:not(.simple)").iCheck({
        checkboxClass: 'icheckbox_minimal',
        radioClass: 'iradio_minimal'
    });
    var socket = io.connect(NODE_URL, {
        "secure": true,
       // "reconnection": false,
        "reconnectionDelay": 5000
    });
    socket.emit('join', {
        uniq_id: USER_HASH
    });
    //push_msg_action2user
    socket.on("new_msg", function(data) {
        //console.log(data.type_op);
        switch (data.type_op) {
            case 'ticket_create':
                if (data.zen_sid != ZENLIX_session_id) {
                    if (data.user_init != USER_HASH) {
                        active_noty_msg('ticket_create', data.t_id);
                    }
                    update_labels();
                    //if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    // if ((def_filename == "dashboard")) {
                    if ((def_filename == "dashboard") || (window.location == MyHOSTNAME) || (def_filename == "index.php")) {
                        update_page_dashboard();
                        makemytime(true);
                        update_dashboard_labels();
                    };
                    if (ispath('list')) {
                        update_list_page_content();
                        makemytime(true);
                    };
                    if (ispath('news')) {
                        refresh_news();
                    };
                }
                break;
            case 'ticket_refer':
                if (data.zen_sid != ZENLIX_session_id) {
                    if (data.user_init != USER_HASH) {
                        active_noty_msg('ticket_refer', data.t_id);
                    }
                    update_labels();
                    //if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    //  if ((def_filename == "dashboard")) {
                    if ((def_filename == "dashboard") || (window.location == MyHOSTNAME) || (def_filename == "index.php")) {
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
                    if (ispath('news')) {
                        refresh_news();
                    };
                }
                break;
            case 'ticket_ok':
                if (data.zen_sid != ZENLIX_session_id) {
                    if (data.user_init != USER_HASH) {
                        active_noty_msg('ticket_ok', data.t_id);
                    }
                    //if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    //if ((def_filename == "dashboard")) {
                    if ((def_filename == "dashboard") || (window.location == MyHOSTNAME) || (def_filename == "index.php")) {
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
                    if (ispath('news')) {
                        refresh_news();
                    };
                }
                break;
            case 'ticket_no_ok':
                if (data.zen_sid != ZENLIX_session_id) {
                    if (data.user_init != USER_HASH) {
                        active_noty_msg('ticket_no_ok', data.t_id);
                    }
                    //if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    //if ((def_filename == "dashboard")) {
                    if ((def_filename == "dashboard") || (window.location == MyHOSTNAME) || (def_filename == "index.php")) {
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
                    if (ispath('news')) {
                        refresh_news();
                    };
                }
                break;
            case 'ticket_lock':
                //consloe.log(USER_HASH+" == "+data.user_hash);
                if (data.zen_sid != ZENLIX_session_id) {
                    console.log("data.user_init:" + data.user_init + "| USER_HASH:" + USER_HASH);
                    if (data.user_init != USER_HASH) {
                        active_noty_msg('ticket_lock', data.t_id);
                    }
                    //if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    //if ((def_filename == "dashboard")) {
                    if ((def_filename == "dashboard") || (window.location == MyHOSTNAME) || (def_filename == "index.php")) {
                        update_page_dashboard();
                        makemytime(true);
                        update_dashboard_labels();
                    };
                    if (ispath('list')) {
                        update_list_page_content();
                        makemytime(true);
                    };
                    if (ispath('ticket')) {
                        //console.log('locked');
                        update_ticket_page(data.t_id);
                    };
                    if (ispath('news')) {
                        refresh_news();
                    };
                }
                break;
            case 'ticket_unlock':
                if (data.zen_sid != ZENLIX_session_id) {
                    if (data.user_init != USER_HASH) {
                        active_noty_msg('ticket_unlock', data.t_id);
                    }
                    //if ((def_filename == "index.php") || (window.location == MyHOSTNAME)) {
                    // if ((def_filename == "dashboard")) {
                    if ((def_filename == "dashboard") || (window.location == MyHOSTNAME) || (def_filename == "index.php")) {
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
                    if (ispath('news')) {
                        refresh_news();
                    };
                }
                break;
            case 'ticket_comment':
                if (data.zen_sid != ZENLIX_session_id) {
                    if (data.user_init != USER_HASH) {
                        if (!ispath('ticket')) {
                            active_noty_msg('ticket_comment', data.t_id);
                        }
                    }
                    if (ispath('ticket')) {
                        if ($('#ticket_id').val() == data.t_id) {
                            get_comments(data.t_id);
                            makemytime(true);
                        }
                    }
                    if (ispath('news')) {
                        refresh_news();
                    };
                }
                break;
            case 'message_send':
                //console.log(data.chat_id);
                if (USER_HASH != data.user_hash) {
                    if (data.zen_sid != ZENLIX_session_id) {
                        if (!ispath('messages')) {
                            if (data.user_init != USER_HASH) {
                                noty_message(data.chat_id);
                            }
                        }
                        if (ispath('messages')) {
                            messages_update_window(data.t_id);
                        }
                        update_labels_msg();
                        show_bar_unread_msg();
                        if (ispath('messages')) {
                            refresh_message_usr_list();
                        }
                    }
                }
                //console.log('yes');
                break;
            case 'logout':
                window.location = MyHOSTNAME + "logout";
                break;
        };
    });
    moment.lang(MyLANG);
    var my_errors = {
        fio: false,
        login: false,
        pass: false
    };
    var ACTIONPATH = MyHOSTNAME + "action";
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
                        var t = '<div style=\'float: left;\'><a style=\'color: rgb(243, 235, 235); cursor: inherit;\' href=\'' + item.url + 'ticket?' + item.hash + '\'><strong>' + item.ticket + ' #' + item.name + '</strong> </a></div><div style=\'float: right; padding-right: 10px;\'><small>' + item.time + '</small></div><br><hr style=\'margin-top: 5px; margin-bottom: 8px; border:0; border-top:0px solid #E4E4E4\'><em style=\'color: rgb(252, 252, 252); cursor: inherit;\'>' + item.at + '</em>';
                        noty({
                            text: t,
                            layout: USER_noty_layot,
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
                            layout: USER_noty_layot,
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
        $('.pops2').popover({
            html: true,
            trigger: 'manual',
            container: $(this).attr('id'),
            placement: 'bottom',
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
        String.prototype.toHHMMSS = function() {
            var sec_num = parseInt(this, 10); // don't forget the second param
            var hours = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = sec_num - (hours * 3600) - (minutes * 60);
            if (hours < 10) {
                hours = "0" + hours;
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            if (seconds < 10) {
                seconds = "0" + seconds;
            }
            var time = hours + ':' + minutes + ':' + seconds;
            return time;
        }
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
        $('time#e').each(function(i, e) {
            var time = moment($(e).attr('datetime'));
            $(e).html('<span>' + time.format("H:mm:ss") + '</span>');
        });
        $('time#f').each(function(i, e) {
            var time = $(e).attr('datetime');
            
            var duration = moment.duration(time * 1000, 'milliseconds');
            $(e).html('<span>' + duration.format("d " + MOMENTJS_DAY + ", h " + MOMENTJS_HOUR + ", m " + MOMENTJS_MINUTE + ", s " + MOMENTJS_SEC + "") + '</span>');
            //time.from(now)
            //var time = moment($(e).attr('datetime'));
            //$(e).html('<span>' + moment.duration(2, "seconds").humanize() + '</span>');
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
        data.append("mode", 'summernote_file_add');
        $.ajax({
            data: data,
            type: "POST",
            url: ACTIONPATH,
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
                $('select#' + target_id).trigger('change');
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
            url: MyHOSTNAME + "app/controllers/list_content.inc.php",
            data: "menu=" + encodeURIComponent(pt) + "&page=" + encodeURIComponent(oo),
            success: function(html) {
                $('[data-toggle="tooltip"]').tooltip('hide');
                $("#content").html(html);
                $('[data-toggle="tooltip"]').tooltip({
                    container: 'body',
                    html: true
                });
                makemytime(true);
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


    function check_user_msgs() {
        var total_msgs_main = $('#total_msgs_main').val();
        var targ = $('#target_user').val();
        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=total_msgs_user"+"&in="+targ,
            success: function(html) {
                $('#total_msgs_main').val(html);
                if (total_msgs_main != html) {
                   // if (targ == "main") {
                        $.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=messages_view" + "&target="+targ,
                            success: function(html) {
                                $("#content_chat").html(html);
                                makemytime(true);
                                var scroll = $('#content_chat');
                                var height = scroll[0].scrollHeight;
                                scroll.scrollTop(height);
                            }
                        });
                   // }
                }
            }
        });
    };

    function check_user_msgs_client() {


        var total_msgs_main = $('#total_msgs_main').val();
        var targ = $('#target_user').val();

        $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=check_request_status",
            success: function(html) {

                if (html == "empty"){

$.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=clientCloseStatus",
                            success: function(html) {
                                $("#content_chat_client").html(html);
                            }
                        });


                    
                }
                else if (html == "wait"){
                    $.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=clientWaitStatus",
                            success: function(html) {
                                $("#content_chat_client").html(html);
                            }
                        });
                }
                else if (html == "active"){
                            $.ajax({
            type: "POST",
            url: ACTIONPATH,
            data: "mode=total_msgs_user_client",
            success: function(html) {
                
                if (total_msgs_main != html) {
                   // if (targ == "main") {
                        $.ajax({
                            type: "POST",
                            url: ACTIONPATH,
                            data: "mode=messages_view_client",
                            success: function(html) {
                                $("#content_chat_client").html(html);
                                makemytime(true);
                                var scroll = $('#content_chat_client');
                                var height = scroll[0].scrollHeight;
                                scroll.scrollTop(height);
                            }
                        });
                   // }
                }
                $('#total_msgs_main').val(html);
            }
        });
                }
}
});


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
                    $('#total_msgs_main').val(html);
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

    function refresh_news() {
        window.location = MyHOSTNAME + "news";
    }

    function view_helper_cat() {
        $.fn.editable.defaults.mode = 'inline';
        $('a#edit_item').each(function(i, e) {
            $(e).editable({
                inputclass: 'input-sm',
                emptytext: 'пусто',
                params: {
                    mode: 'save_helper_item'
                }
            });
        });
        $('.sortable').nestedSortable({
            ForcePlaceholderSize: true,
            handle: 'div',
            helper: 'clone',
            items: 'li',
            opacity: .6,
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            maxLevels: 4,
            update: function() {
                list = $(this).nestedSortable('serialize');
                //console.log(list);
                $.post(ACTIONPATH, {
                    mode: "sort_units_helper",
                    list: list
                }, function(data) {
                    console.log(data);
                });
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
    //console.log(def_filename);
    $.noty.defaults = {
        layout: USER_noty_layot,
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

    if (ispath('news')) {
        $('.fancybox').fancybox({
            openEffect: 'elastic',
            closeEffect: 'elastic'
        });
}


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
                data: "mode=add_cron" + "&client_id_param=" + $("#client_id_param").val() + "&to=" + $("#to").val() + "&s2id_users_do=" + $("#users_do").val() + "&prio=" + $("#prio").val() + "&subj=" + $("#subj").val() + "&msg=" + $("#msg").val() + "&period=" + $("#period").val() + "&day_field=" + $("#day_field").val() + "&week_select=" + $("#week_select").val() + "&month_select=" + $("#month_select").val() + "&time_action=" + $("#time_action").val() + "&action_start=" + $("#action_start").val() + "&action_stop=" + $("#action_stop").val() + "&status_action=" + $("#status_action").val(),
                success: function(html) {
                    //console.log(html);
                    $.each(html, function(i, item) {
                        if (item.check_error == true) {
                            window.location = MyHOSTNAME + "scheduler";
                        } else if (item.check_error == false) {
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
            $("#action_start").val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
            $("#action_stop").val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
        });
        $('#reservation').daterangepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            timePicker: true,
            timePicker12Hour: false
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
            maximumSelectionSize: 15,
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
            source: MyHOSTNAME + "action?mode=getJSON_fio",
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
                                                    url: ACTIONPATH,
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
                                                        url: ACTIONPATH,
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
    if (ispath('sla_rep')) {
        $("#unitstat_id").select2({
            allowClear: true,
            maximumSelectionSize: 15,
            formatNoMatches: get_lang_param('JS_not_found')
        });
        $('#reservation').on('apply.daterangepicker', function(ev, picker) {
            $("#start_time").val(picker.startDate.format('YYYY-MM-DD'));
            $("#stop_time").val(picker.endDate.format('YYYY-MM-DD'));
        });
        $('#reservation').daterangepicker({
            format: 'YYYY-MM-DD'
        });
        $('body').on('click', 'button#sla_stat_make', function(event) {
            event.preventDefault();
            var p = $('#unitstat_id').val();
            var start_time = $("#start_time").val();
            var stop_time = $("#stop_time").val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_sla_period_stat" + "&start=" + start_time + "&end=" + stop_time + "&unit=" + p,
                success: function(html) {
                    $('#ts_res').html(html);
                    $(".knob").knob();
                    makemytime();
                }
            });
        });
    }
    if (ispath('main_stats')) {
        $("#unitstat_id").select2({
            allowClear: true,
            maximumSelectionSize: 15,
            formatNoMatches: get_lang_param('JS_not_found')
        });
        $('body').on('click', 'button#main_stat_make', function(event) {
            event.preventDefault();
            var p = $('#unitstat_id').val();
            var start_time = $("#start_time").val();
            var stop_time = $("#stop_time").val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_total_period_stat" + "&start=" + start_time + "&end=" + stop_time + "&unit=" + p,
                success: function(html) {
                    $('#ts_res').html(html);
                    $(".knob").knob();
                    makemytime();
                }
            });
        });
        $('#reservation').on('apply.daterangepicker', function(ev, picker) {
            $("#start_time").val(picker.startDate.format('YYYY-MM-DD'));
            $("#stop_time").val(picker.endDate.format('YYYY-MM-DD'));
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
            maximumSelectionSize: 15,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found'),
            escapeMarkup: function(m) {
                return m;
            }
        });
        $('body').on('click', 'button#user_stat_make', function(event) {
            event.preventDefault();
            var p = $('#user_list').val();
            var start_time = $("#start_time").val();
            var stop_time = $("#stop_time").val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_user_stat" + "&uid=" + p + "&start=" + start_time + "&end=" + stop_time,
                success: function(html) {
                    $('#content_stat').html(html);
                    $(".knob").knob();
                    makemytime();
                }
            });
        });
        $('#reservation').on('apply.daterangepicker', function(ev, picker) {
            var p = $('#user_list').val();
            $("#start_time").val(picker.startDate.format('YYYY-MM-DD'));
            $("#stop_time").val(picker.endDate.format('YYYY-MM-DD'));
        });
        $('#reservation').daterangepicker({
            format: 'YYYY-MM-DD'
        });
    };
    if (ispath('messages')) {
        //check_main_msgs()
        //setInterval(check_main_msgs(),2000);
        //clearInterval(interval_main);



        $('body').on('click', 'a#ClientChatRequest_action', function(event) {
            event.preventDefault();

            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=ClientChatRequest",
                success: function(html) {
                    $("#content_chat_client").html(html);
                    $("#target_user").val('wait');

                }
            });

});

//do_commentClient


if ($('#client_part').length) {
var tu=$("#target_user").val();

//if (tu != "false") {
        interval = setInterval(function() {
            check_user_msgs_client();
        }, 2000);
   // }

}

if ($('#do_commentClient').length) {
var scroll = $('#content_chat_client');
        var height = scroll[0].scrollHeight;
        scroll.scrollTop(height);
        $('input#msg').bind('keypress', function(e) {
            if (e.keyCode == 13) {
                $("button#do_commentClient").click();
            }
        });


        $('body').on('click', 'button#do_commentClient', function(event) {
            event.preventDefault();
            //var tid = $("#target_user").val();
            var m = $("input#msg").val().length;
            if ($("input#msg").val().replace(/ /g, '').length > 1) {
                $("input#msg").popover('hide');
                $("#for_msg").removeClass('has-error').addClass('has-success');
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=messages_sendClient" + "&textmsg=" + encodeURIComponent(($("input#msg").val())) ,
                    success: function(html) {
                        $("#content_chat_client").html(html);
                        $("input#msg").val('')
                        makemytime(true);
                        //comment_body
                        var scroll = $('#content_chat_client');
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




}




if (!$('#client_part').length) {


//startChatWithClient
        $('body').on('click', '#startChatWithClient', function(event) {
            event.preventDefault();
            var tu=$("#target_user").val();

                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=startChatWithClient" + "&target=" + tu ,
                    success: function(html) {
                        $("#content_chat").html(html);
                        $("input#msg").val('');
                        makemytime(true);
                        //comment_body
                        var scroll = $('#content_chat');
                        var height = scroll[0].scrollHeight;
                        scroll.scrollTop(height);
                        //console.log(height);
                    }
                });


        });

        //closeChatWithClient
        $('body').on('click', '#closeChatWithClient', function(event) {
            event.preventDefault();
            var tu=$("#target_user").val();

                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=stopChatWithClient" + "&target=" + tu ,
                    success: function(html) {
                        $("#content_chat").html(html);
                        $("input#msg").val('');
                        makemytime(true);
                        //comment_body
                        var scroll = $('#content_chat');
                        var height = scroll[0].scrollHeight;
                        scroll.scrollTop(height);
                        //console.log(height);
                    }
                });


        });


        interval = setInterval(function() {
            if ($("#target_user").val() == "main") {check_main_msgs();}
            else {
                check_user_msgs();
            }
            
        }, 2000);
        var scroll = $('#content_chat');
        var height = scroll[0].scrollHeight;
        scroll.scrollTop(height);
    }
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
                    $("input#msg").val('');
                    makemytime(true);
                    $('.loading1').removeClass('overlay');
                    $('.loading2').removeClass('loading-img');
                    //total_msgs_main
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



var ids = [];

        $('body').on('click', 'button#do_comment', function(event) {
            event.preventDefault();
            var tid = $(this).attr('value');
            var usr = $(this).attr('user');
            var m = $("textarea#msg").val().length;
            if ($("textarea#msg").val().replace(/ /g, '').length > 1) {
                $("textarea#msg").popover('hide');
                $("#for_msg").removeClass('has-error').addClass('has-success');


//console.log(ids);

                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=add_comment" + "&user=" + encodeURIComponent(usr) + "&textmsg=" + encodeURIComponent(($("textarea#msg").val())) + "&tid=" + tid+
                    "&files="+ids,
                    success: function(html) {
                        //$("#comment_content").html(html);
                        $('#comment_body').append(html);

                        
                        $("textarea#msg").val('')
                        makemytime(true);
                        //comment_body
                        var scroll = $('#comment_body');
                        var height = scroll[0].scrollHeight;
                        scroll.scrollTop(height);
                        $("#previews").html('');
                        ids = [];
                        
                        //console.log(height);
                    }
                });
            } else {
                $("textarea#msg").popover('show');
                $("#for_msg").addClass('has-error');
                setTimeout(function() {
                    $("textarea#msg").popover('hide');
                }, 2000);
            }
        });


        if ($('#myid').length) {
            var previewNode = document.querySelector("#template");
            previewNode.id = "";
            var previewTemplate = previewNode.parentNode.innerHTML;
            previewNode.parentNode.removeChild(previewNode);
            var ph = '1';
            $('#myid').dropzone({
                url: ACTIONPATH,
                maxFilesize: 100,
                paramName: "myfile",
                params: {
                    mode: 'upload_manual_file',
                    post_hash: ph,
                    type: '1'
                },
                removedfile: function(file) {
                  
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                },
                maxThumbnailFilesize: 5,
                previewTemplate: previewTemplate,
                previewsContainer: "#previews",
                autoQueue: true,
                maxFiles: 50,
                init: function() {


//var ids = [];





                    this.on('success', function(file, response) {
                        //$(file.previewTemplate).append('<span class="server_file">'+json.uniq_code+'</span>');
                        //$.each(json, function(i, item) {
                        //var obj = jQuery.parseJSON(json);
                        var obj = jQuery.parseJSON(response);
                        //console.log(obj);
                        $.each(obj, function(i, item) {
                            if (item.status == "ok") {
                                $(file.previewTemplate).append('<input type="hidden" class="server_file" value="' + item.uniq_code + '">');


ids.push(item.uniq_code);
//console.log(ids);

                            } else if (item.status == "error") {
                                //$(file.previewTemplate).append('<div class="alert alert-danger">'+item.msg+'</div>');
                                $(file.previewTemplate).html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + item.msg + '</div>').fadeOut(3000);
                            }
                        })
                        //});
                    });
                    this.on("removedfile", function(file) {
                        var server_file = $(file.previewTemplate).children('.server_file').val();
                        //console.log(server_file);

ids = jQuery.grep(ids, function(value) {
  return value != server_file;
});
//console.log(ids);






                        $.ajax({
                            type: 'POST',
                            url: ACTIONPATH,
                            data: "mode=delete_manual_file" + "&uniq_code=" + server_file,
                            dataType: 'html',
                        });
                    });
                    this.on("addedfile", function(file) {
                        //console.log(file);
                    });
                    this.on('drop', function(file) {
                        //alert('file');
                    });
                }
            });
        }


        var scroll = $('#comment_body');
        var height = scroll[0].scrollHeight;
        scroll.scrollTop(height);
        //$('input.file-inputs').bootstrapFileInput();
        $('#do_comment_file').change(function() {
            upl();
        });
        //tab_2
        $('body').on('click', 'a#get_new_log', function(event) {
            event.preventDefault();




            var t_id = $('#ticket_hash').val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_new_ticket_log" + "&ticket_hash=" + t_id,
                success: function(html) {
                    $('#tab_2').html(html);
                    makemytime(true);
                }
            });
        });
        var intr = null;
        //setInterval(plus_sec(), 1000);
        function gotimer_worker() {
            //setInterval(function() {
            if ($('#work_timer').attr('value') == 'true') {
                var t = $('#work_timer > #f').attr('datetime');
                t++;
                $('#work_timer > #f').attr('datetime', t);
                var el = $('#work_timer > time#f').attr('datetime');
                var duration = moment.duration(el * 1000, 'milliseconds');
                $('#work_timer > time#f').html('<span>' + duration.format("d " + MOMENTJS_DAY + ", h " + MOMENTJS_HOUR + ", m " + MOMENTJS_MINUTE + ", s " + MOMENTJS_SEC + "") + '</span>');
            }
            //  }, 1000);
            //$('#work_timer').attr('value', 'false');
        }

        function gotimer_deadline() {
            //setInterval(function() {
            if ($('#deadline_timer').attr('value') == 'true') {
                var t = $('#deadline_timer > #f').attr('datetime');
                t--;
                $('#deadline_timer > #f').attr('datetime', t);
                var el = $('#deadline_timer > time#f').attr('datetime');
                var duration = moment.duration(el * 1000, 'milliseconds');
                $('#deadline_timer > time#f').html('<span>' + duration.format("d " + MOMENTJS_DAY + ", h " + MOMENTJS_HOUR + ", m " + MOMENTJS_MINUTE + ", s " + MOMENTJS_SEC + "") + '</span>');
            }
            //  }, 1000);
            //$('#work_timer').attr('value', 'false');
        }
        if ($('#deadline_timer').attr('value') == 'true') {
            //gotimer_worker();
            intr = setInterval(gotimer_deadline, 1000);
            //clearInterval(intr);
        }
        if ($('#work_timer').attr('value') == 'true') {
            //gotimer_worker();
            intr = setInterval(gotimer_worker, 1000);
            //clearInterval(intr);
        }
        $('.fancybox').fancybox({
            openEffect: 'elastic',
            closeEffect: 'elastic'
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
                    $(".file-input-name").text('');
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
        $('textarea#msg').bind('keydown', function(e) {
            if (e.ctrlKey && e.keyCode == 13) {
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
            var s = $('#edit_subj').val(),
            m = $('#msg_up').val(),
                p = $('#prio').val(),
                t_hash = $('#ticket_hash').val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=save_edit_ticket" + 
                "&subj="+ s +
                "&t_hash=" + t_hash + "&prio=" + encodeURIComponent(p) + "&msg=" + encodeURIComponent(m),
                success: function(html) {
                    //console.log(html);
                    $('#myModal').modal('hide');
                    //$(elem).removeClass().addClass('success', 1000);
                    window.location = MyHOSTNAME + "ticket?" + t_hash;
                }
            });
        });
        $('body').on('click', 'button#del_ticket', function(event) {
            event.preventDefault();
            var t_hash = $('#ticket_hash').val();
            var lang_del = get_lang_param('JS_del');
            bootbox.confirm(lang_del, function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=del_ticket" + "&t_hash=" + t_hash,
                        success: function(html) {
                            //console.log(html);
                            //$('#myModal').modal('hide');
                            //$(elem).removeClass().addClass('success', 1000);
                            window.location = MyHOSTNAME + "list";
                        }
                    });
                } else if (result == false) {}
            });
        });
        $('body').on('click', 'button#action_ok', function(event) {
            event.preventDefault();
            var status_lock = $("button#action_ok").attr('status');
            var ok_val = $("button#action_ok").attr("value");
            var ok_val_tid = $("button#action_ok").attr("tid");
            var lang_ok = get_lang_param('JS_ok');
            var st = $("#action_refer_to").attr('value');
            if (st == '1') {
                $("#refer_to").fadeOut(500);
                $("#action_refer_to").removeClass('active').attr('value', '0');
            }
            if (status_lock == 'ok') {
                $("button#action_ok").attr('status', "no_ok").html("<i class=\"fa fa-check\"></i> " + lang_ok);
                $("button#action_lock").removeAttr('disabled');
                $("button#action_refer_to").removeAttr('disabled');
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=status_ok" + "&tid=" + ok_val_tid + "&user=" + encodeURIComponent(ok_val),
                    success: function(html) {
                        intr = setInterval(gotimer_worker, 1000);
                        //clearInterval(intr);
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
                        //intr= setInterval(gotimer_worker,1000);
                        clearInterval(intr);
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
            var st = $("#action_refer_to").attr('value');
            if (st == '1') {
                $("#refer_to").fadeOut(500);
                $("#action_refer_to").removeClass('active').attr('value', '0');
            }
            if (status_lock == 'lock') {
                $("button#action_lock").attr('status', "unlock").html("<i class='fa fa-unlock'></i> " + lang_unlock);
                $("#msg_e").hide();
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=lock" + "&tid=" + lock_val_tid + "&user=" + encodeURIComponent(lock_val),
                    success: function(html) {
                        $("#msg").hide().html(html).fadeIn(500);
                        $('#work_timer').attr('value', 'true');
                        intr = setInterval(gotimer_worker, 1000);
                        //clearInterval(intr);
                        //$("#action_ok").attr('disabled', "disabled");
                        $("#action_ok").removeAttr('disabled');
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
                        $("#action_ok").attr('disabled', "disabled");
                        $('#work_timer').attr('value', 'false');
                        //intr= setInterval(gotimer_worker,1000);
                        clearInterval(intr);
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

        $("#refer_to").hide();
        $("#t_users_do").select2({
            formatResult: format,
            formatSelection: format,
            allowClear: true,
            maximumSelectionSize: 15,
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

$('body').on('click', 'button#delete_user_file_vu', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=delete_user_file" + "&uniq_code=" + encodeURIComponent($("#delete_user_file_vu").val()),
                success: function(html) {
                   window.location = MyHOSTNAME + "view_user?" + $("input#user_id").attr('value');
                }
            });
        });

if ($('#myid').length) {
            var previewNode = document.querySelector("#template");
            previewNode.id = "";
            var previewTemplate = previewNode.parentNode.innerHTML;
            previewNode.parentNode.removeChild(previewNode);
            var ph = $("input#user_id").attr('value');
            $('#myid').dropzone({
                url: ACTIONPATH,
                maxFilesize: 100,
                paramName: "myfile",
                params: {
                    mode: 'upload_user_file',
                    post_hash: ph,
                    type: '1'
                },
                removedfile: function(file) {
                    //console.log('d:'+file);
                    //var name = file.name;
                    /*
$.ajax({
        type: 'POST',
        url: 'delete.php',
        data: "id="+name,
        dataType: 'html'
    });
*/
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                },
                maxThumbnailFilesize: 5,
                previewTemplate: previewTemplate,
                previewsContainer: "#previews",
                autoQueue: true,
                maxFiles: 50,
                init: function() {
                    this.on('success', function(file, response) {
                        //$(file.previewTemplate).append('<span class="server_file">'+json.uniq_code+'</span>');
                        //$.each(json, function(i, item) {
                        //var obj = jQuery.parseJSON(json);
                        var obj = jQuery.parseJSON(response);
                        //console.log(obj);
                        $.each(obj, function(i, item) {
                            if (item.status == "ok") {
                                $(file.previewTemplate).append('<input type="hidden" class="server_file" value="' + item.uniq_code + '">');
                            } else if (item.status == "error") {
                                //$(file.previewTemplate).append('<div class="alert alert-danger">'+item.msg+'</div>');
                                $(file.previewTemplate).html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + item.msg + '</div>').fadeOut(3000);
                            }
                        })
                        //});
                    });
                    this.on("removedfile", function(file) {
                        var server_file = $(file.previewTemplate).children('.server_file').val();
                        //console.log(server_file);
                        $.ajax({
                            type: 'POST',
                            url: ACTIONPATH,
                            data: "mode=delete_user_file" + "&uniq_code=" + server_file,
                            dataType: 'html',
                        });
                    });
                    this.on("addedfile", function(file) {
                        console.log(file);
                    });
                    this.on('drop', function(file) {
                        //alert('file');
                    });
                }
            });
        }



 $('.fancybox').fancybox({
            openEffect: 'elastic',
            closeEffect: 'elastic'
        });


        $(".knob").knob();
    };
    if (ispath('profile')) {

        $('.d_finish').daterangepicker({
            format: 'YYYY-MM-DD',
            timePicker: false,
            
            singleDatePicker: true
        });
        

        $('body').on('click', 'button#gen_new_api', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=gen_new_api",
                success: function(html) {
                    //window.location = MyHOSTNAME + "profile";
                    $("#api_code").html(html);
                }
            });
        });
        //edit_nf
        $('body').on('click', 'button#edit_nf', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=profile_edit_nf" + "&mail=" + $("#mail_nf").val() + "&sms=" + $("#sms_nf").val() + "&mob=" + $("#mob").val(),
                success: function(html) {
                    //window.location = MyHOSTNAME + "profile";
                    $("#nf_info").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#nf_info').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        $(".multi_field").select2({
            allowClear: true,
            maximumSelectionSize: 15,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found')
        });
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
                data: "mode=edit_profile_main_client" + "&fio=" + encodeURIComponent($("#fio").val()) + "&mail=" + encodeURIComponent($("#mail").val()) + "&lang=" + encodeURIComponent($("select#lang").val()) + "&skype=" + encodeURIComponent($("#skype").val()) + "&tel=" + encodeURIComponent($("#tel").val()) + "&adr=" + encodeURIComponent($("#adr").val()) + "&id=" + encodeURIComponent($("#edit_profile_main").attr('value')) + "&pb=" + $("#pb").val(),
                success: function(html) {
                    $("#m_info").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#m_info').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        $("#noty").on("change", function() {
            //alert( this.value );
            var p = this.value;
            // var t = 'test';
            noty({
                text: 'test',
                layout: p,
                type: 'information',
                timeout: 2000
            });
            /*
                        noty({
                            text: "test",
                            layout: this.value,
                            timeout: false
                        });
*/
            $.ionSound.play("button_tiny");
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
                    data: "mode=edit_profile_main" + "&mail=" + encodeURIComponent($("#mail").val()) + "&fio=" + encodeURIComponent($("#fio").val()) + "&lang=" + encodeURIComponent($("select#lang").val()) + "&skype=" + encodeURIComponent($("#skype").val()) + "&tel=" + encodeURIComponent($("#tel").val()) + "&adr=" + encodeURIComponent($("#adr").val()) + "&posada=" + encodeURIComponent($("#posada").val()) + "&id=" + encodeURIComponent($("#edit_profile_main").attr('value')) + "&user_layot=" + encodeURIComponent($("#noty").val()) + "&pb=" + $("#pb").val(),
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
        $('body').on('click', 'button#edit_profile_ad_f', function(event) {
            event.preventDefault();
            var add_from = $('#add_field_form').serialize();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=edit_profile_ad_f" + "&" + add_from,
                success: function(html) {
                    $("#ad_f_res").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#ad_f_res').children('.alert').fadeOut(500);
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
        $(".multi_field").select2({
            allowClear: true,
            maximumSelectionSize: 15,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found')
        });
        $('#d_finish').daterangepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            timePicker: true,
            timePicker12Hour: false,
            singleDatePicker: true,
            minDate: new Date()
        });
        $("#d_finish").change(function() {
            $('#d_finish_val').val($(this).val());
        });
        $('#d_finish').on('apply.daterangepicker', function(ev, picker) {
            //$("#action_start").val( picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
            $('#d_finish_val').val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
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
                            //$("select#to").val(html)
                            $("select#to").prop('selectedIndex', html);
                            $("select#to [value='" + html + "']").attr("selected", "selected");
                            $('select#to').trigger('chosen:updated');
                            $('select#to').trigger("liszt:updated");
                            //$('#for_to').popover('hide');
                            $('#for_to').removeClass('has-error');
                            $('#for_to').addClass('has-success');
                            //console.log($("select#to").val());
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
            //console.log($('#add_field_form').serialize());
            if (check_form_ticket() == 0) {
                enter_ticket();
            }
            //console.log($("#users_do").val());
            //alert(u_do);
        });
        $("#fio").autocomplete({
            max: 10,
            minLength: 2,
            source: MyHOSTNAME + "action?mode=getJSON_fio",
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
                                                    url: ACTIONPATH,
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
                                                        url: ACTIONPATH,
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
            maximumSelectionSize: 15,
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
            $('#exampleInputEmail1').attr('value', MyHOSTNAME + "/app/controllers/note.php?h=" + u);
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=get_notes" + "&hn=" + encodeURIComponent(u),
                success: function(html) {
                    $('#summernote').destroy();
                    $('#summernote').html(html);
                    $('#buttons').show();
                    $('#summernote').summernote({
                        height: 500,
                        focus: true,
                        //lang: get_lang_param('summernote_lang'),
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
                    $('#exampleInputEmail1').attr('value', MyHOSTNAME + "/app/controllers/note.php?h=" + u);
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
                                //lang: get_lang_param('summernote_lang'),
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
        $('.fancybox').fancybox({
            openEffect: 'elastic',
            closeEffect: 'elastic'
        });
        //delete_edited_manual_file
        $('body').on('click', 'button#delete_edited_manual_file', function(event) {
            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: ACTIONPATH,
                data: "mode=delete_manual_file" + "&uniq_code=" + $(this).val(),
                dataType: 'html',
                success: function(html) {
                    //$('#table_list').html(html);
                    console.log("ok");
                    window.location = MyHOSTNAME + "helper?h=" + $("#do_save_help").val() + "&edit";
                }
            });
        });
        if ($('#myid').length) {
            var previewNode = document.querySelector("#template");
            previewNode.id = "";
            var previewTemplate = previewNode.parentNode.innerHTML;
            previewNode.parentNode.removeChild(previewNode);
            var ph = $("#manual_hash").val();
            $('#myid').dropzone({
                url: ACTIONPATH,
                maxFilesize: 100,
                paramName: "myfile",
                params: {
                    mode: 'upload_manual_file',
                    post_hash: ph,
                    type: '1'
                },
                removedfile: function(file) {
                    //console.log('d:'+file);
                    //var name = file.name;
                    /*
$.ajax({
        type: 'POST',
        url: 'delete.php',
        data: "id="+name,
        dataType: 'html'
    });
*/
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                },
                maxThumbnailFilesize: 5,
                previewTemplate: previewTemplate,
                previewsContainer: "#previews",
                autoQueue: true,
                maxFiles: 50,
                init: function() {
                    this.on('success', function(file, response) {
                        //$(file.previewTemplate).append('<span class="server_file">'+json.uniq_code+'</span>');
                        //$.each(json, function(i, item) {
                        //var obj = jQuery.parseJSON(json);
                        var obj = jQuery.parseJSON(response);
                        //console.log(obj);
                        $.each(obj, function(i, item) {
                            if (item.status == "ok") {
                                $(file.previewTemplate).append('<input type="hidden" class="server_file" value="' + item.uniq_code + '">');
                            } else if (item.status == "error") {
                                //$(file.previewTemplate).append('<div class="alert alert-danger">'+item.msg+'</div>');
                                $(file.previewTemplate).html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + item.msg + '</div>').fadeOut(3000);
                            }
                        })
                        //});
                    });
                    this.on("removedfile", function(file) {
                        var server_file = $(file.previewTemplate).children('.server_file').val();
                        //console.log(server_file);
                        $.ajax({
                            type: 'POST',
                            url: ACTIONPATH,
                            data: "mode=delete_manual_file" + "&uniq_code=" + server_file,
                            dataType: 'html',
                        });
                    });
                    this.on("addedfile", function(file) {
                        console.log(file);
                    });
                    this.on('drop', function(file) {
                        //alert('file');
                    });
                }
            });
        }
        if ($('#summernote_help').length != 0) {
            $('#summernote_help').summernote({
                height: 300,
                focus: true,
                // lang: get_lang_param('summernote_lang'),
                onImageUpload: function(files, editor, welEditable) {
                    sendFile(files[0], editor, welEditable);
                }
            });
        }
        //del_item_no
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
            if ($(this).val().length > 1) {
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=find_help" + "&t=" + t,
                    success: function(html) {
                        $("#help_content").html(html);
                    }
                });
            } else if ($(this).val().length < 2) {
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=view_cats",
                    success: function(html) {
                        $("#help_content").html(html);
                    }
                });
            }
        });
        //units_help
        view_helper_cat();
        $('body').on('click', 'button#add_helper_item', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=items_view",
                success: function(html) {
                    $('#content_items').html(html);
                    view_helper_cat();
                }
            });
        });
        $('body').on('click', 'i#del_item_no', function(event) {
            event.preventDefault();
            //console.log('v');
            var lang_helper_error = get_lang_param('JS_HELPER_error_to_del');
            noty({
                text: lang_helper_error,
                layout: 'center',
                type: 'information',
                timeout: 3000
            });
        });
        $('body').on('click', 'i#del_item', function(event) {
            event.preventDefault();
            var ids = $(this).attr('value');
            bootbox.confirm(get_lang_param('JS_del'), function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=helper_item_del" + "&id=" + ids,
                        success: function(html) {
                            $("#content_items").html(html);
                            view_helper_cat();
                        }
                    });
                }
                if (result == false) {
                    console.log('false');
                }
            });
        });
        /*
        $('body').on('click', 'button#units_help', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=units_helper",
                success: function(html) {


                    $("#help_content").hide().html(html).fadeIn(500);




view_helper_cat();









//view_helper_cat();
                    
                }
            });
        });
*/
        /*
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
*/
        $('body').on('click', 'button#do_save_help', function(event) {
            event.preventDefault();
            var sHTML = $('#summernote_help').code();
            var hn = $(this).val();
            var u = $("#u").chosen().val();
            var is_client = $("#is_client").prop('checked');
            var lang_unit = get_lang_param('JS_unit');
            var lang_probl = get_lang_param('JS_probl');
            var t = $("#t").val();
            var cat_id = $("#cat").val();
            var data = {
                'mode': 'do_save_help',
                'u': u,
                't': t,
                'msg': sHTML,
                'hn': hn,
                'is_client': is_client,
                'cat_id': cat_id
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
            var cat = $("#cat").val();
            var data = {
                'mode': 'do_create_help',
                'u': u,
                't': t,
                'mh': $("#manual_hash").val(),
                'msg': sHTML,
                'is_client': is_client,
                'cat': cat
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
            data: "mode=view_cats",
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



        if ($('#myid').length) {
            var previewNode = document.querySelector("#template");
            previewNode.id = "";
            var previewTemplate = previewNode.parentNode.innerHTML;
            previewNode.parentNode.removeChild(previewNode);
            var ph = $("#hashname").val();
            $('#myid').dropzone({
                url: ACTIONPATH,
                maxFilesize: 100,
                paramName: "myfile",
                params: {
                    mode: 'upload_post_file',
                    post_hash: ph,
                    type: '0'
                },

                removedfile: function(file) {
                    //console.log('d:'+file);
                    //var name = file.name;
                    /*
$.ajax({
        type: 'POST',
        url: 'delete.php',
        data: "id="+name,
        dataType: 'html'
    });
*/
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                },
                maxThumbnailFilesize: 5,
                previewTemplate: previewTemplate,
                previewsContainer: "#previews",
                autoQueue: true,
                maxFiles: 50,
                init: function() {





                    // Using a closure.
      //var _this = this;



                    this.on('success', function(file, response) {
                        //$(file.previewTemplate).append('<span class="server_file">'+json.uniq_code+'</span>');
                        //$.each(json, function(i, item) {
                        //var obj = jQuery.parseJSON(json);
                        var obj = jQuery.parseJSON(response);
                        //console.log(obj);
                        $.each(obj, function(i, item) {
                            if (item.status == "ok") {
                                $(file.previewTemplate).append('<input type="hidden" class="server_file" value="' + item.uniq_code + '">');
                            } else if (item.status == "error") {
                                //$(file.previewTemplate).append('<div class="alert alert-danger">'+item.msg+'</div>');
                                $(file.previewTemplate).html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + item.msg + '</div>').fadeOut(3000);
                            }
                        })
                        //});
                    });
                    this.on("removedfile", function(file) {
                        var server_file = $(file.previewTemplate).children('.server_file').val();
                        //
                        $.ajax({
                            type: 'POST',
                            url: ACTIONPATH,
                            data: "mode=delete_post_file" + "&uniq_code=" + server_file,
                            dataType: 'html',
                        });
                        //console.log(file);
                    });
                    this.on("addedfile", function(file) {
                        console.log(file);
                    });
                    this.on('drop', function(file) {
                        //alert('file');
                    });
                }
            });
        }
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
            //console.log('da');
            var z = $("#username").text();
            var s = $("#subj").val();
            var to = $("select#to").val();
            var m = $("#msg").val().length;
            var error_code = 0;
            console.log(to);
            /*
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
            */
            if ($('#fio').val().length == 0) {
                error_code = 1;
                //$('#fio').popover('show');
                $('#for_fio').addClass('has-error');
                setTimeout(function() {
                    $("#fio").popover('hide');
                }, 2000);
            }
            if (to == '0') {
                error_code = 1;
                //$('#dsd').popover('show');
                $('#for_to').addClass('has-error');
                setTimeout(function() {
                    $("#dsd").popover('hide');
                }, 2000);
            }
            if ((s == null) || (s == "0")) {
                error_code = 1;
                //$("#for_subj").popover('show');
                $("#for_subj").addClass('has-error');
                setTimeout(function() {
                    $("#for_subj").popover('hide');
                }, 2000);
            }
            if (m == 0) {
                error_code = 1;
                //$("#msg").popover('show');
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



console.log(to);


            var error_code = 0;

            if (to == '0') {
                error_code = 1;
                //$('#dsd').popover('show');
                $('#for_to').addClass('has-error');
            }
            if (s == 0) {
                error_code = 1;
                //$("#for_subj").popover('show');
                $("#for_subj").addClass('has-error');
            }
            if (m == 0) {
                error_code = 1;
                //$("#msg").popover('show');
                $("#for_msg").addClass('has-error');
            }
            return error_code;
        }

        function enter_ticket_client() {
            //console.log($("#users_do").val());
            var u_do;
            var deadline_time = $("#d_finish_val").val();
            var add_from = $('#add_field_form').serialize();
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
                data: "mode=add_ticket" + "&type_add=client" + "&user_init_id=" + encodeURIComponent($("#user_init_id").val()) + "&user_do=" + encodeURIComponent(u_do) + "&subj=" + encodeURIComponent($("#subj").val()) + "&msg=" + encodeURIComponent($("#msg").val()) + "&unit_id=" + encodeURIComponent($("#to").val()) + "&prio=" + encodeURIComponent($("#prio").val()) + "&hashname=" + encodeURIComponent($("#hashname").val()) + "&deadline_time=" + deadline_time + "&" + add_from,
                success: function(html) {
                    //console.log(html);
                    window.location = MyHOSTNAME + "create?ok&h=" + html;
                }
            });
        }

        function enter_ticket() {
            //console.log($("#users_do").val());
            //console.log('da');
            var status_action = $("#status_action").val();
            var u_do;
            var deadline_time = $("#d_finish_val").val();
            var add_from = $('#add_field_form').serialize();
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
                    data: "mode=add_ticket" + "&type_add=add" + "&fio=" + encodeURIComponent($("#username").text()) + "&tel=" + encodeURIComponent($("#new_tel").text()) + "&login=" + encodeURIComponent($("#new_login").text()) + "&adr=" + encodeURIComponent($("#new_adr").text()) + "&tel=" + encodeURIComponent($("#new_tel").text()) + "&mail=" + encodeURIComponent($("#new_mail").text()) + "&posada=" + encodeURIComponent($("#new_posada").text()) + "&user_init_id=" + encodeURIComponent($("#user_init_id").val()) + "&user_do=" + encodeURIComponent(u_do) + "&subj=" + encodeURIComponent($("#subj").val()) + "&msg=" + encodeURIComponent($("#msg").val()) + "&unit_id=" + encodeURIComponent($("#to").val()) + "&prio=" + encodeURIComponent($("#prio").val()) + "&hashname=" + encodeURIComponent($("#hashname").val()) + "&deadline_time=" + deadline_time + "&" + add_from,
                    success: function(html) {
                        //window.location = "new.php?ok&h="+html;
                        window.location = MyHOSTNAME + "create?ok&h=" + html;
                        // console.log(html);
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
                    data: "mode=add_ticket" + "&type_add=edit" + "&client_id_param=" + encodeURIComponent($("#client_id_param").val()) + "&tel=" + encodeURIComponent($("#edit_tel").text()) + "&login=" + encodeURIComponent($("#edit_login").text()) + "&pod=" + encodeURIComponent($("#edit_unit").text()) + "&adr=" + encodeURIComponent($("#edit_adr").text()) + "&tel=" + encodeURIComponent($("#edit_tel").text()) + "&mail=" + encodeURIComponent($("#edit_mail").text()) + "&posada=" + encodeURIComponent($("#edit_posada").text()) + "&user_init_id=" + encodeURIComponent($("#user_init_id").val()) + "&user_do=" + encodeURIComponent(u_do) + "&subj=" + encodeURIComponent($("#subj").val()) + "&msg=" + encodeURIComponent($("#msg").val()) + "&unit_id=" + encodeURIComponent($("#to").val()) + "&prio=" + encodeURIComponent($("#prio").val()) + "&hashname=" + encodeURIComponent($("#hashname").val()) + "&deadline_time=" + deadline_time + "&" + add_from,
                    success: function(html) {
                        //console.log(html);
                        window.location = MyHOSTNAME + "create?ok&h=" + html;
                        //console.log(html);
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

    }
    if (ispath('list')) {
        $('body').on('click', 'a#make_sort', function(event) {
            event.preventDefault();
            var tr_id = $(this).attr('value');
            var pt = $("#page_type").attr('value');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=make_sort" + "&pt=" + encodeURIComponent(pt) + "&st=" + encodeURIComponent(tr_id),
                success: function() {
                    window.location = MyHOSTNAME + "list?" + pt;
                }
            });
        });
        $('body').on('click', 'a#reset_sort', function(event) {
            event.preventDefault();
            //var tr_id = $(this).attr('value');
            var pt = $("#page_type").attr('value');
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=reset_sort" + "&pt=" + encodeURIComponent(pt),
                success: function() {
                    window.location = MyHOSTNAME + "list?" + pt;
                }
            });
        });
        $('body').on('click', 'button#action_list_ok', function(event) {
            event.preventDefault();
            var status_ll = $(this).attr('status');
            var tr_id = $(this).attr('value');
            var elem = '#tr_' + tr_id;
            var elb = '.ela_' + tr_id;
            var us = $(this).attr('user');
            if (status_ll == "ok") {
                $(elb).attr('disabled', "disabled");
                //$("#action_list_lock").removeAttr('disabled');
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
                //$("#action_list_lock").attr('disabled', "disabled");
                $(elb).removeAttr('disabled');
                $(this).attr("status", "ok");
                $(this).html('<i class=\"fa fa-circle-o\"></i>');
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=status_ok" + "&tid=" + tr_id + "&user=" + encodeURIComponent(us),
                    success: function() {
                        $(elem).removeClass('success', 1000).addClass('warning', 1000).addClass('pops');
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
            var elb = '.elb_' + tr_id;
            var us = $(this).attr('user');
            if (status_ll == "lock") {
                $(this).attr("status", "unlock");
                $(this).html('<i class=\"fa fa-lock\"></i>');
                $.ajax({
                    type: "POST",
                    url: ACTIONPATH,
                    data: "mode=lock" + "&tid=" + tr_id + "&user=" + encodeURIComponent(us),
                    success: function() {
                        //$("#action_list_ok").attr('disabled', "disabled");
                        $(elb).removeAttr('disabled');
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
                        $(elb).attr('disabled', "disabled");
                        //$("#action_list_ok").removeAttr('disabled');
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
                        url: MyHOSTNAME + "app/controllers/client.list_content.inc.php",
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
                        url: MyHOSTNAME + "app/controllers/list_content.inc.php",
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
                        url: MyHOSTNAME + "app/controllers/list_content.inc.php",
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
                        url: MyHOSTNAME + "app/controllers/list_content.inc.php",
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
                        url: MyHOSTNAME + "app/controllers/client.list_content.inc.php",
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
    if (ispath('portal')) {
        //conf_edit_global_message
        $("#users_do").select2({
            formatResult: format,
            formatSelection: format,
            allowClear: true,
            maximumSelectionSize: 15,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found'),
            escapeMarkup: function(m) {
                return m;
            }
        });
        $('body').on('click', 'button#conf_edit_portal', function(event) {
            event.preventDefault();
            //console.log($('#to_msg').val());
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=conf_edit_portal" + "&status=" + encodeURIComponent($("#portal_status").val()) + "&msg_type=" + encodeURIComponent($("input[type=radio][name=optionsRadios_msg]:checked").val()) + "&msg_title=" + encodeURIComponent($("#msg_title").val()) + "&msg_text=" + encodeURIComponent($("#mess").val()) + "&portal_msg_status=" + encodeURIComponent($("#portal_msg_status").val()) + "&ntu=" + $("#users_do").val(),
                success: function(html) {
                    $("#conf_edit_portal_res").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#conf_edit_portal_res').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
    }
    if (ispath('mailers')) {
        $('body').on('click', 'button#check_mailers', function(event) {
            event.preventDefault();
            var sHTML = $('#mailers_msg').code();
            var data = {
                'mode': 'mailers_send',
                'subj_mailers': encodeURIComponent($('#subj_mailers').val()),
                'msg': sHTML,
                'type_to_mail': encodeURIComponent($("input[type=radio][name=optionsRadios]:checked").val()),
                'users_priv': $("#users_priv").val(),
                'users_units': $("#users_units").val(),
                'users_list': $("#users_list").val()
            };
            //console.log($('#to_msg').val());
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: data,
                success: function(html) {
                    $("#mailers_check_res").html(html);
                }
            });
        });
        $('body').on('click', 'button#send_mail', function(event) {
            event.preventDefault();
            var sHTML = $('#mailers_msg').code();
            var data = {
                'mode': 'mailers_send',
                'subj_mailers': $('#subj_mailers').val(),
                'msg': sHTML,
                'type_to_mail': encodeURIComponent($("input[type=radio][name=optionsRadios]:checked").val()),
                'users_priv': $("#users_priv").val(),
                'users_units': $("#users_units").val(),
                'users_list': $("#users_list").val(),
                'check': 'true'
            };
            //console.log($('#to_msg').val());
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: data,
                success: function(html) {
                    $("#conf_edit_portal_res").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#conf_edit_portal_res').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        $('input[type=radio][name=optionsRadios]').on('ifChanged', function(event) {
            //console.log(this.value);
            if (this.value == '1') {
                $('#users_priv').prop('disabled', true);
                $('#users_units').prop('disabled', true);
                $('#users_list').prop('disabled', false);
            } else if (this.value == '2') {
                $('#users_priv').prop('disabled', false);
                $('#users_units').prop('disabled', false);
                $('#users_list').prop('disabled', true);
            }
        });
        $(".msel").select2({
            allowClear: true,
            maximumSelectionSize: 15,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found')
        });
        $('#mailers_msg').summernote({
            height: 300,
            focus: true,
            //lang: get_lang_param('summernote_lang'),
            disableDragAndDrop: false,
            toolbar: [
                //['style', ['style']], // no style button
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['link', ['link']],
                ['codeview', ['codeview']]
            ],
            oninit: function() {}
        });
    }
    if (ispath('slaplans')) {
        function view_slaplans() {
            $.fn.editable.defaults.mode = 'inline';
            $('a#edit_item').each(function(i, e) {
                $(e).editable({
                    inputclass: 'form-control input-sm input-longtext',
                    emptytext: 'пусто',
                    params: {
                        mode: 'save_sla_item'
                    },
                    tpl: "<input type='text' style='width: 400px'>"
                });
            });
            $('.sortable').nestedSortable({
                ForcePlaceholderSize: true,
                handle: 'div',
                helper: 'clone',
                items: 'li',
                opacity: .6,
                placeholder: 'placeholder',
                revert: 250,
                tabSize: 25,
                tolerance: 'pointer',
                toleranceElement: '> div',
                maxLevels: 1,
                update: function() {
                    list = $(this).nestedSortable('serialize');
                    //console.log(list);
                    $.post(ACTIONPATH, {
                        mode: "sort_sla_plans",
                        list: list
                    }, function(data) {
                        console.log(data);
                    });
                }
            });
            $("input[type='checkbox']:not(.simple), input[type='radio']:not(.simple)").iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal'
            });
        };
        view_slaplans();
        $(document).on('ifChanged', '#make_sla_active', function() {
            //$("input#field_perf_name").on('change', function() {
            //console.log($(this).closest('tr').attr('id'));
            //var hash=$(this).attr('value');
            var name = $(this).prop('checked');
            $.post(ACTIONPATH, {
                mode: "make_sla_active",
                name: name
            });
        });
        $('body').on('click', 'button#save_sla_plan', function(event) {
            event.preventDefault();
            var ids = $(this).val();
            var data = {
                'mode': 'save_sla',
                'uniq_id': ids,
                'react_low_1': $('#react_low_1').val(),
                'react_low_2': $('#react_low_2').val(),
                'react_low_3': $('#react_low_3').val(),
                'react_low_4': $('#react_low_4').val(),
                'react_def_1': $('#react_def_1').val(),
                'react_def_2': $('#react_def_2').val(),
                'react_def_3': $('#react_def_3').val(),
                'react_def_4': $('#react_def_4').val(),
                'react_high_1': $('#react_high_1').val(),
                'react_high_2': $('#react_high_2').val(),
                'react_high_3': $('#react_high_3').val(),
                'react_high_4': $('#react_high_4').val(),
                'work_low_1': $('#work_low_1').val(),
                'work_low_2': $('#work_low_2').val(),
                'work_low_3': $('#work_low_3').val(),
                'work_low_4': $('#work_low_4').val(),
                'work_def_1': $('#work_def_1').val(),
                'work_def_2': $('#work_def_2').val(),
                'work_def_3': $('#work_def_3').val(),
                'work_def_4': $('#work_def_4').val(),
                'work_high_1': $('#work_high_1').val(),
                'work_high_2': $('#work_high_2').val(),
                'work_high_3': $('#work_high_3').val(),
                'work_high_4': $('#work_high_4').val(),
                'deadline_low_1': $('#deadline_low_1').val(),
                'deadline_low_2': $('#deadline_low_2').val(),
                'deadline_low_3': $('#deadline_low_3').val(),
                'deadline_low_4': $('#deadline_low_4').val(),
                'deadline_def_1': $('#deadline_def_1').val(),
                'deadline_def_2': $('#deadline_def_2').val(),
                'deadline_def_3': $('#deadline_def_3').val(),
                'deadline_def_4': $('#deadline_def_4').val(),
                'deadline_high_1': $('#deadline_high_1').val(),
                'deadline_high_2': $('#deadline_high_2').val(),
                'deadline_high_3': $('#deadline_high_3').val(),
                'deadline_high_4': $('#deadline_high_4').val()
            };
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: data,
                success: function(html) {
                    //$("#plan_res").html(html);
                    $("#plan_res").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#plan_res').children('.alert').fadeOut(500);
                    }, 3000);
                    //view_slaplans();
                }
            });
        });
        //add_slaplan_item
        //edit_sla_plan
        $('body').on('click', 'i#edit_sla_plan', function(event) {
            event.preventDefault();
            window.location = MyHOSTNAME + "config?slaplans&item=" + $(this).attr('value');
        });
        $('body').on('click', 'i#del_item_sla', function(event) {
            event.preventDefault();
            var ids = $(this).attr('value');
            bootbox.confirm(get_lang_param('JS_del'), function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=sla_del" + "&id=" + ids,
                        success: function(html) {
                            $("#content_sla_plans").html(html);
                            view_slaplans();
                        }
                    });
                }
                if (result == false) {
                    console.log('false');
                }
            });
        });
        $('body').on('click', 'button#add_slaplan_item', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=add_slaplan_item" + "&text=new_item",
                success: function(html) {
                    $("#content_sla_plans").html(html);
                    //$("#subj_text").val('');
                    view_slaplans();
                }
            });
        });
    }
    if (ispath('calendar')) {

        /* initialize the external events
         -----------------------------------------------------------------*/
        function ini_events(ele) {
            ele.each(function() {
                // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                // it doesn't need to have a start or end
                var eventObject = {
                    title: $.trim($(this).text()) // use the element's text as the event title
                };
                // store the Event Object in the DOM element so we can get to it later
                $(this).data('eventObject', eventObject);
                // make the event draggable using jQuery UI
                $(this).draggable({
                    zIndex: 1070,
                    revert: true, // will cause the event to go back to its
                    revertDuration: 0 //  original position after the drag
                });
            });
        }
        ini_events($('#external-events div.external-event'));
        /* initialize the calendar
         -----------------------------------------------------------------*/
        //Date for the calendar events (dummy data)
        var date = new Date();
        var d = date.getDate(),
            m = date.getMonth(),
            y = date.getFullYear();

        function loadCal(uidArray) {
            $('#calendar').fullCalendar('destroy');
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                firstDay: 1,
                lang: 'MyLANG',
                timeFormat: 'H(:mm)',
                buttonText: {
                    today: CAL_today,
                    month: CAL_month,
                    week: CAL_week,
                    day: CAL_day
                },
                //Random default events

                eventSources: [
                    // your event source
                    {
                        url: ACTIONPATH,
                        type: 'POST',
                        data: {
                            mode: 'get_cal_events',
                            filter: uidArray
                        },
                        error: function() {
                            alert('there was an error while fetching events!');
                        }
                    }
                    // any other sources...
                ],
                editable: true,
                droppable: true, // this allows things to be dropped onto the calendar !!!
                eventClick: function(calEvent, jsEvent, view) {
                    //get_cal_event
                    if (calEvent.editable == false) {
                        $.ajax({
                            url: ACTIONPATH,
                            data: {
                                mode: 'get_cal_event',
                                uniq_code: calEvent.id
                            },
                            //data: 'title='+ event.title+'&start='+ start +'&end='+ end,
                            type: "POST",
                            dataType: "json",
                            success: function(json) {
                                //alert("insert Successfully");
                                $.each(json, function(i, item) {
                                    $("#ei_name").text(item.title);
                                    $("#ei_desc").text(item.description);
                                    $("#ei_period").text(item.period);
                                    $("#ei_author").html(item.author);
                                })
                                $('#event_modal_info').modal('show');
                            }
                        });
                    } else if (calEvent.editable == true) {
                        $.ajax({
                            url: ACTIONPATH,
                            data: {
                                mode: 'get_cal_event',
                                uniq_code: calEvent.id
                            },
                            //data: 'title='+ event.title+'&start='+ start +'&end='+ end,
                            type: "POST",
                            dataType: "json",
                            success: function(json) {
                                //alert("insert Successfully");
                                $.each(json, function(i, item) {
                                    $("#event_name").val(item.title);
                                    $("#event_desc").val(item.description);
                                    //$("#visibility").val();
                                    $("#visibility").val(item.visibility);
                                    $("#current_backgroundColor").val(item.backgroundColor);
                                    $("#current_borderColor").val(item.borderColor);
                                    $("#cur_color_event").css({
                                        "background-color": item.backgroundColor,
                                        "border-color": item.borderColor
                                    });
                                    //console.log(item.allDay);
                                    $("#reservation").val(item.start + " - " + item.end);
                                    $("#current_start").val(item.start);
                                    $("#current_end").val(item.end);
                                    if (item.allDay == true) {
                                        $("input#all_day").iCheck('check');
                                        $("#reservation").prop('disabled', true);
                                    } else if (item.allDay == false) {
                                        $("input#all_day").iCheck('uncheck');
                                        $("#reservation").prop('disabled', false);
                                    }
                                });
                            }
                        });
                        $("#current_event_hash").val(calEvent.id);
                        $('#event_modal').modal('show');
                    }
                    //alert('Event id: ' + calEvent.id);
                },
                drop: function(date, allDay) { // this function is called when something is dropped
                    // retrieve the dropped element's stored Event Object
                    var originalEventObject = $(this).data('eventObject');
                    // we need to copy it, so that multiple events don't have a reference to the same object
                    var copiedEventObject = $.extend({}, originalEventObject);
                    // assign it the date that was reported
                    copiedEventObject.start = date;
                    copiedEventObject.allDay = allDay;
                    copiedEventObject.backgroundColor = $(this).css("background-color");
                    copiedEventObject.borderColor = $(this).css("border-color");
                    //console.log(copiedEventObject.title);
                    $.ajax({
                        url: ACTIONPATH,
                        data: {
                            mode: 'cal_insert_events',
                            title: copiedEventObject.title,
                            start: date.format(),
                            end: date.format(),
                            backgroundColor: $(this).css("background-color"),
                            borderColor: $(this).css("border-color")
                        },
                        //data: 'title='+ event.title+'&start='+ start +'&end='+ end,
                        type: "POST",
                        success: function(json) {
                            //alert("insert Successfully");
                        }
                    });
                    // render the event on the calendar
                    // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                    //$('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
                    //$('#calendar').fullCalendar("refetchEvents");
                    loadCal("0,1,2");
                    $(".make_event_filter").iCheck('check');
                    //$('#calendar').fullCalendar('refresh' );
                    // is the "remove after drop" checkbox checked?
                    if ($('#drop-remove').is(':checked')) {
                        // if so, remove the element from the "Draggable Events" list
                        $(this).remove();
                    }
                },
                eventDrop: function(event, delta) {
                    //var start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
                    //var end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
                    var start = moment(event.start).format("YYYY-MM-DD HH:mm:ss");
                    //var end = moment(event.end).format("YYYY-MM-DD HH:mm:ss");
                    var end = (event.end == null) ? start : event.end.format();
                    //console.log(event.end.format());
                    $.ajax({
                        url: ACTIONPATH,
                        data: {
                            mode: 'cal_drop_events',
                            title: event.title,
                            start: start,
                            end: end,
                            id: event.id,
                            allday: event.allDay
                        },
                        //data: 'title='+ event.title+'&start='+ start +'&end='+ end,
                        type: "POST",
                        success: function(json) {
                            // alert("Updated Successfully");
                        }
                    });
                    //console.log("a: "+event.id);
                },
                eventResize: function(event) {
                    var start = moment(event.start).format("YYYY-MM-DD HH:mm:ss");
                    var end = moment(event.end).format("YYYY-MM-DD HH:mm:ss");
                    //console.log(cr);
                    $.ajax({
                        url: ACTIONPATH,
                        data: {
                            mode: 'cal_resize_events',
                            title: event.title,
                            start: start,
                            end: end,
                            id: event.id,
                            allday: event.allDay
                        },
                        //data: 'title='+ event.title+'&start='+ start +'&end='+ end,
                        type: "POST",
                        success: function(json) {
                            // alert("Updated Successfully");
                        }
                    });
                },
                eventRender: function(event, element) {
                    element.popover({
                        title: event.name,
                        html: true,
                        trigger: 'manual',
                        placement: 'top',
                        title: event.title,
                        content: "<small>" + event.description + "</small>",
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
                        }, 100)
                    });
                }
            });
        }
        /* ADDING EVENTS */
        var currColor = "#3c8dbc"; //Red by default
        //Color chooser button
        var colorChooser = $("#color-chooser-btn");
        $("#color-chooser > li > a").click(function(e) {
            e.preventDefault();
            //Save color
            currColor = $(this).css("color");
            //Add color effect to button
            $('#add-new-event').css({
                "background-color": currColor,
                "border-color": currColor
            });
        });
        $("#add-new-event").click(function(e) {
            e.preventDefault();
            //Get value and make sure it is not null
            var val = $("#new-event").val();
            if (val.length == 0) {
                return;
            }
            //Create events
            var event = $("<div />");
            event.css({
                "background-color": currColor,
                "border-color": currColor,
                "color": "#fff"
            }).addClass("external-event");
            event.html(val);
            $('#external-events').prepend(event);
            //Add draggable funtionality
            ini_events(event);
            //Remove event from text input
            $("#new-event").val("");
        });
        $("#color-chooser_event > li > a").click(function(e) {
            e.preventDefault();
            //Save color
            currColor = $(this).css("color");
            //Add color effect to button
            $('#cur_color_event').css({
                "background-color": currColor,
                "border-color": currColor
            });
            $("#current_backgroundColor").val(currColor);
            $("#current_borderColor").val(currColor);
        });
        //cal_delete_current
        //.fullCalendar( 'removeEvent', id )
        $('body').on('click', 'button#cal_delete_current', function(event) {
            event.preventDefault();
            var cih = $("#current_event_hash").val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=cal_del_event" + "&uniq_code=" + cih,
                success: function() {
                    //window.location = MyHOSTNAME + "config";
                    $('#calendar').fullCalendar('removeEvents', cih);
                    $('#event_modal').modal('hide');
                }
            });
        });
        //make_event_filter
        //event_save_action
        $('body').on('click', 'button#event_save_action', function(event) {
            event.preventDefault();
            var cih = $("#current_event_hash").val();
            var eventname = $("#event_name").val();
            var eventdesc = $("#event_desc").val();
            var eventpriv = $("#visibility").val();
            var bc = $("#current_backgroundColor").val();
            var bbc = $("#current_borderColor").val();
            var alldayp = $("#all_day").prop('checked');
            var cs = $("#current_start").val();
            var ce = $("#current_end").val();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: {
                    mode: 'cal_edit_event',
                    uniq_code: cih,
                    name: eventname,
                    desc: eventdesc,
                    priv: eventpriv,
                    color: bc,
                    color_b: bbc,
                    allday: alldayp,
                    start: cs,
                    end: ce
                },
                success: function() {
                    //window.location = MyHOSTNAME + "config";
                    $('#calendar').fullCalendar("refetchEvents");
                    $('#event_modal').modal('hide');
                }
            });
        });
        $('#reservation').daterangepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            timePicker: true,
            timePicker12Hour: false
        });
        $('#reservation').on('apply.daterangepicker', function(ev, picker) {
            $("#current_start").val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
            $("#current_end").val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
        });
        loadCal($("#filter_events").val());
        $('#all_day').on('ifChanged', function(event) {
            if ($(this).is(":checked")) {
                $('#reservation').prop("disabled", true);
            } else {
                $('#reservation').prop("disabled", false);
            }
        });
        $('.make_event_filter').on('ifChanged', function(event) {
            console.log($('.make_event_filter:checked').map(function() {
                return this.value;
            }).get().join(','));
            $("#filter_events").val($('.make_event_filter:checked').map(function() {
                return this.value;
            }).get().join(','));
            //$('#calendar').fullCalendar("refetchEvents");
            //$('#calendar').fullCalendar( 'destroy' );
            loadCal($('.make_event_filter:checked').map(function() {
                return this.value;
            }).get().join(','));
            //$('#calendar').fullCalendar('render');
            //$('#calendar').fullCalendar("refetchEvents");
        });
    }
    if (ispath('config')) {


//clear_cache
        $('body').on('click', 'button#clear_cache', function(event) {
            event.preventDefault();


            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=conf_clear_cache",
                
                success: function(html) {
                    
                       
                            $("#conf_edit_main_res").hide().html(html).fadeIn(500);
                            setTimeout(function() {
                                $('#conf_edit_main_res').children('.alert').fadeOut(500);
                            }, 3000);
                        
                        //console.log(item.msg);
                    
                }
            });

        });





        $("select#to").on('change', function() {
            if ($('select#to').val() != 0) {
                $('#for_to').popover('hide');
                //$('#for_to').removeClass('has-error');
                //$('#for_to').addClass('has-success');
                $('#dsd').popover('hide');
            } else {
                $('#dsd').popover('show');
                $('#for_to').popover('show');
                //$('#for_to').addClass('has-error');
                setTimeout(function() {
                    $("#dsd").popover('hide');
                }, 2000);
            }
        });
        $("select#to").change(function() {
            var i = $('select#to').val();
            if ($('select#to').val() != 0) {
                $('#for_to').popover('hide');
                //$('#for_to').removeClass('has-error');
                //$('#for_to').addClass('has-success');
                createuserslist(i, 'users_do');
            } else {
                createuserslist(i, 'users_do');
                $('#for_to').popover('show');
                //$('#for_to').addClass('has-error');
                setTimeout(function() {
                    $("#for_to").popover('hide');
                }, 2000);
            }
        });
        $("#users_do").select2({
            formatResult: format,
            formatSelection: format,
            allowClear: true,
            maximumSelectionSize: 15,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found'),
            escapeMarkup: function(m) {
                return m;
            }
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
                            //$('#for_to').removeClass('has-error');
                            //$('#for_to').addClass('has-success');
                        }
                    });
                }
                if (p == 0) {
                    $("select#to").find('option:selected').removeAttr("selected");
                    $('select#to').trigger('chosen:updated');
                }
            }
        });
        $('input[type=radio][name=optionsRadios1]').on('ifChanged', function(event) {
            console.log(this.value);
            if (this.value == '0') {
                $('#to_msg').prop('disabled', false).trigger("chosen:updated");
            } else if (this.value == '1') {
                $('#to_msg').prop('disabled', true).trigger("chosen:updated");
            }
        });
        $('#to_msg').chosen({
            max_selected_options: 50
        });
        //conf_edit_global_message
        $('body').on('click', 'button#conf_edit_global_message', function(event) {
            event.preventDefault();
            //console.log($('#to_msg').val());
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=conf_edit_gm" + "&status=" + encodeURIComponent($("#gm_active").val()) + "&to_msg=" + encodeURIComponent($("input[type=radio][name=optionsRadios1]:checked").val()) + "&usr_list=" + encodeURIComponent($("#to_msg").val()) + "&msg_type=" + encodeURIComponent($("input[type=radio][name=optionsRadios_msg]:checked").val()) + "&gm_text=" + encodeURIComponent($("#gm_text").val()),
                success: function(html) {
                    $("#conf_edit_gm_res").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#conf_edit_gm_res').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        $('#file_logo').change(function() {
            $('#form_logo').submit();
        });
        $('body').on('click', 'button#del_logo_img', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=del_logo_img",
                success: function() {
                    window.location = MyHOSTNAME + "config";
                }
            });
        });
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
                data: "mode=conf_edit_pb" + "&api=" + encodeURIComponent($("input#pb_api").val()) + "&pb_active=" + encodeURIComponent($("select#pb_active").val()),
                success: function(html) {
                    $("#conf_edit_pb_res").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#conf_edit_pb_res').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        $('body').on('click', 'button#conf_edit_sms', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=conf_edit_sms" + "&smsc_active=" + encodeURIComponent($("#smsc_active").val()) + "&smsc_login=" + encodeURIComponent($("#smsc_login").val()) + "&smsc_pass=" + encodeURIComponent($("#smsc_pass").val()) + "&sms_nf=" + encodeURIComponent($("#sms_nf").val()),
                success: function(html) {
                    $("#conf_edit_sms_res").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#conf_edit_sms_res').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        $('body').on('click', 'button#conf_edit_ticket', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=conf_edit_ticket" + "&days2arch=" + encodeURIComponent($("input#days2arch").val()) + "&fix_subj=" + encodeURIComponent($("#fix_subj").val()) + "&file_uploads=" + encodeURIComponent($("#file_uploads").val()) + "&file_types=" + encodeURIComponent($("#file_types").val()) + "&file_size=" + encodeURIComponent($("#file_size").val() * 1024 * 1024) + "&ticket_last_time=" + encodeURIComponent($("#ticket_last_time").val()),
                dataType: "json",
                success: function(html) {
                    $.each(html, function(i, item) {
                        if (item.res == true) {
                            $("#conf_edit_ticket_res").hide().html(item.msg).fadeIn(500);
                            setTimeout(function() {
                                $('#conf_edit_ticket_res').children('.alert').fadeOut(500);
                            }, 3000);
                        } else if (item.res == false) {
                            //$('#res').html(item.msg); 
                            $("#conf_edit_ticket_res").hide().html(item.msg).fadeIn(500);
                        }
                        //console.log(item.msg);
                    });
                }
            });
        });
        $('body').on('click', 'button#conf_edit_main', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=conf_edit_main" + "&name_of_firm=" + encodeURIComponent($("input#name_of_firm").val()) + "&title_header=" + encodeURIComponent($("input#title_header").val()) + "&ldap=" + encodeURIComponent($("input#ldap_ip").val()) + "&ldapd=" + encodeURIComponent($("input#ldap_domain").val()) + "&hostname=" + encodeURIComponent($("input#hostname").val()) + "&mail=" + encodeURIComponent($("input#mail").val()) + "&first_login=" + encodeURIComponent($("#first_login").val()) + "&node_port=" + encodeURIComponent($("#node_port").val()) + "&time_zone=" + encodeURIComponent($("#time_zone").val()) + "&allow_register=" + encodeURIComponent($("#allow_register").val()) + "&lang=" + encodeURIComponent($("#lang").val()) + "&allow_forgot=" + encodeURIComponent($("#allow_forgot").val()) + "&api_status=" + $("#api_status").val() + "&twig_cache=" + $("#twig_cache").val(),
                dataType: "json",
                success: function(html) {
                    $.each(html, function(i, item) {
                        if (item.res == true) {
                            $("#conf_edit_main_res").hide().html(item.msg).fadeIn(500);
                            setTimeout(function() {
                                $('#conf_edit_main_res').children('.alert').fadeOut(500);
                            }, 3000);
                        } else if (item.res == false) {
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
        $('body').on('click', 'button#conf_edit_email_gate', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=conf_edit_email_gate" + "&email_gate_status=" + $("#email_gate_status").val() + "&email_gate_all=" + $("#email_gate_all").val() + "&to=" + $("#to").val() + "&users_do=" + $("#users_do").val() + "&email_gate_mailbox=" + $("#email_gate_mailbox").val() + "&email_gate_filter=" + encodeURIComponent($("#email_gate_filter").val()) + "&email_gate_host=" + $("#email_gate_host").val() + "&email_gate_cat=" + $("#email_gate_cat").val() + "&email_gate_port=" + $("#email_gate_port").val() + "&email_gate_login=" + $("#email_gate_login").val() + "&email_gate_pass=" + $("#email_gate_pass").val() + "&email_gate_cp=" + $("#email_gate_connect_param").val(),
                success: function(html) {
                    $("#conf_edit_email_gate_res").hide().html(html).fadeIn(500);
                    setTimeout(function() {
                        $('#conf_edit_email_gate_res').children('.alert').fadeOut(500);
                    }, 3000);
                }
            });
        });
        if ($('select#mail_type').val() == "sendmail") {
            $('#smtp_div').hide();
        } else if ($('select#mail_type').val() == "SMTP") {
            $('#smtp_div').show();
        }
        $(document).on('ifChanged', '#field_perf_client', function() {
            //$("input#field_perf_name").on('change', function() {
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).prop('checked');
            $.post(ACTIONPATH, {
                mode: "change_field_client",
                hash: hash,
                name: name
            });
        });
        $(document).on('ifChanged', '#field_perf_check', function() {
            //$("input#field_perf_name").on('change', function() {
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).prop('checked');
            $.post(ACTIONPATH, {
                mode: "change_field_check",
                hash: hash,
                name: name
            });
        });
        $(document).on('change', 'select#field_perf_select', function() {
            //$("input#field_perf_name").on('change', function() {
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).val();
            $.post(ACTIONPATH, {
                mode: "change_field_select",
                hash: hash,
                name: name
            });
        });
        $(document).on('change', 'input#field_perf_value', function() {
            //$("input#field_perf_name").on('change', function() {
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).val();
            $.post(ACTIONPATH, {
                mode: "change_field_value",
                hash: hash,
                name: name
            });
        });
        //field_perf_name
        $(document).on('change', 'input#field_perf_name', function() {
            //$("input#field_perf_name").on('change', function() {
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).val();
            $.post(ACTIONPATH, {
                mode: "change_field_name",
                hash: hash,
                name: name
            });
        });
        $(document).on('change', 'input#field_perf_placeholder', function() {
            //$("input#field_perf_placeholder").on('change', function() {
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).val();
            $.post(ACTIONPATH, {
                mode: "change_field_placeholder",
                hash: hash,
                name: name
            });
        });
        //del_field_item
        $('body').on('click', 'button#del_field_item', function(event) {
            event.preventDefault();
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            bootbox.confirm(get_lang_param('JS_del'), function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=del_field_item" + "&hash=" + hash,
                        success: function(html) {
                            $("#ticket_fields_res").html(html);
                            $("input[type='checkbox']:not(.simple), input[type='radio']:not(.simple)").iCheck({
                                checkboxClass: 'icheckbox_minimal',
                                radioClass: 'iradio_minimal'
                            });
                        }
                    });
                }
            });
        });
        //
        //conf_edit_ticket_res
        $('body').on('click', 'button#ticket_field_plus', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=add_additional_tickets_perf",
                success: function(html) {
                    $("#ticket_fields_res").html(html);
                    $("input[type='checkbox']:not(.simple), input[type='radio']:not(.simple)").iCheck({
                        checkboxClass: 'icheckbox_minimal',
                        radioClass: 'iradio_minimal'
                    });
                }
            });
        });
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



//unit_save

        $('body').on('click', 'button#unit_save', function(event) {
            event.preventDefault();
            var u = $(this).attr('value');

            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=unit_save" + "&id=" + u+
                "&main_user="+ $("#main_user").val()+
                "&name="+$("#name").val(),
                success: function(html) {
                    window.location = MyHOSTNAME + "units";
                }
            });

        });

        $('body').on('click', 'button#units_lock', function(event) {
            event.preventDefault();
            var u = $(this).attr('value');
            bootbox.confirm(get_lang_param('JS_unit_lock'), function(result) {
                if (result == true) {
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=units_lock" + "&id=" + u,
                success: function(html) {
                    window.location = MyHOSTNAME + "units";
                }
            });
        }
        });
        });

        $('body').on('click', 'button#units_unlock', function(event) {
            event.preventDefault();
            var u = $(this).attr('value');
            bootbox.confirm(get_lang_param('JS_unit_unlock'), function(result) {
                if (result == true) {
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=units_unlock" + "&id=" + u,
                success: function(html) {
                    window.location = MyHOSTNAME + "units";
                }
            });
        }
    });
        });


        $.fn.editable.defaults.mode = 'inline';

        $('a#edit_units').each(function(i, e) {
            $(e).editable({
                inputclass: 'input-sm',
                emptytext: 'пусто',
                params: {
                    mode: 'edit_units'
                }
            });
        });


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
                                    $.fn.editable.defaults.mode = 'inline';
        
        $('a#edit_units').each(function(i, e) {
            $(e).editable({
                inputclass: 'input-sm',
                emptytext: 'пусто',
                params: {
                    mode: 'edit_units'
                }
            });
        });
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
                            $.fn.editable.defaults.mode = 'inline';
        
        $('a#edit_units').each(function(i, e) {
            $(e).editable({
                inputclass: 'input-sm',
                emptytext: 'пусто',
                params: {
                    mode: 'edit_units'
                }
            });
        });
                }
            });
        });
    }
    if (ispath('users')) {


        $('body').on('click', 'button#delete_user_file', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=delete_user_file" + "&uniq_code=" + encodeURIComponent($("#delete_user_file").val()),
                success: function(html) {
                   window.location = MyHOSTNAME + "users?edit=" + $("button#edit_user").attr('value');
                }
            });
        });



$('.fancybox').fancybox({
            openEffect: 'elastic',
            closeEffect: 'elastic'
        });
var ids = [];
        if ($('#myid_create').length) {
            var previewNode = document.querySelector("#template");
            previewNode.id = "";
            var previewTemplate = previewNode.parentNode.innerHTML;
            previewNode.parentNode.removeChild(previewNode);
            var ph = '1';
            $('#myid_create').dropzone({
                url: ACTIONPATH,
                maxFilesize: 100,
                paramName: "myfile",
                params: {
                    mode: 'upload_user_file',
                    post_hash: ph,
                    type: '0'
                },
                removedfile: function(file) {
                  
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                },
                maxThumbnailFilesize: 5,
                previewTemplate: previewTemplate,
                previewsContainer: "#previews",
                autoQueue: true,
                maxFiles: 50,
                init: function() {


//var ids = [];





                    this.on('success', function(file, response) {
                        //$(file.previewTemplate).append('<span class="server_file">'+json.uniq_code+'</span>');
                        //$.each(json, function(i, item) {
                        //var obj = jQuery.parseJSON(json);
                        var obj = jQuery.parseJSON(response);
                        //console.log(obj);
                        $.each(obj, function(i, item) {
                            if (item.status == "ok") {
                                $(file.previewTemplate).append('<input type="hidden" class="server_file" value="' + item.uniq_code + '">');


ids.push(item.uniq_code);
//console.log(ids);

                            } else if (item.status == "error") {
                                //$(file.previewTemplate).append('<div class="alert alert-danger">'+item.msg+'</div>');
                                $(file.previewTemplate).html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + item.msg + '</div>').fadeOut(3000);
                            }
                        })
                        //});
                    });
                    this.on("removedfile", function(file) {
                        var server_file = $(file.previewTemplate).children('.server_file').val();
                        //console.log(server_file);

ids = jQuery.grep(ids, function(value) {
  return value != server_file;
});
//console.log(ids);






                        $.ajax({
                            type: 'POST',
                            url: ACTIONPATH,
                            data: "mode=delete_user_file" + "&uniq_code=" + server_file,
                            dataType: 'html',
                        });
                    });
                    this.on("addedfile", function(file) {
                        //console.log(file);
                    });
                    this.on('drop', function(file) {
                        //alert('file');
                    });
                }
            });
        }



if ($('#myid').length) {
            var previewNode = document.querySelector("#template");
            previewNode.id = "";
            var previewTemplate = previewNode.parentNode.innerHTML;
            previewNode.parentNode.removeChild(previewNode);
            var ph = $("button#edit_user").attr('value');
            $('#myid').dropzone({
                url: ACTIONPATH,
                maxFilesize: 100,
                paramName: "myfile",
                params: {
                    mode: 'upload_user_file',
                    post_hash: ph,
                    type: '1'
                },
                removedfile: function(file) {
                    //console.log('d:'+file);
                    //var name = file.name;
                    /*
$.ajax({
        type: 'POST',
        url: 'delete.php',
        data: "id="+name,
        dataType: 'html'
    });
*/
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                },
                maxThumbnailFilesize: 5,
                previewTemplate: previewTemplate,
                previewsContainer: "#previews",
                autoQueue: true,
                maxFiles: 50,
                init: function() {
                    this.on('success', function(file, response) {
                        //$(file.previewTemplate).append('<span class="server_file">'+json.uniq_code+'</span>');
                        //$.each(json, function(i, item) {
                        //var obj = jQuery.parseJSON(json);
                        var obj = jQuery.parseJSON(response);
                        //console.log(obj);
                        $.each(obj, function(i, item) {
                            if (item.status == "ok") {
                                $(file.previewTemplate).append('<input type="hidden" class="server_file" value="' + item.uniq_code + '">');
                            } else if (item.status == "error") {
                                //$(file.previewTemplate).append('<div class="alert alert-danger">'+item.msg+'</div>');
                                $(file.previewTemplate).html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + item.msg + '</div>').fadeOut(3000);
                            }
                        })
                        //});
                    });
                    this.on("removedfile", function(file) {
                        var server_file = $(file.previewTemplate).children('.server_file').val();
                        //console.log(server_file);
                        $.ajax({
                            type: 'POST',
                            url: ACTIONPATH,
                            data: "mode=delete_user_file" + "&uniq_code=" + server_file,
                            dataType: 'html',
                        });
                    });
                    this.on("addedfile", function(file) {
                        console.log(file);
                    });
                    this.on('drop', function(file) {
                        //alert('file');
                    });
                }
            });
        }





        if (!ispath('create')) {
            $(".multi_field").select2({
                allowClear: true,
                maximumSelectionSize: 15,
                width: '100%',
                formatNoMatches: get_lang_param('JS_not_found')
            });
        }


        $('.d_finish').daterangepicker({
            format: 'YYYY-MM-DD',
            timePicker: false,
            
            singleDatePicker: true
        });


        $('body').on('click', 'button#user_field_plus', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=add_additional_user_perf",
                success: function(html) {
                    $("#user_fields_res").html(html);
                    $("input[type='checkbox']:not(.simple), input[type='radio']:not(.simple)").iCheck({
                        checkboxClass: 'icheckbox_minimal',
                        radioClass: 'iradio_minimal'
                    });
                }
            });
        });
        $(document).on('ifChanged', '#field_perf_client', function() {
            //$("input#field_perf_name").on('change', function() {
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).prop('checked');
            $.post(ACTIONPATH, {
                mode: "change_userfield_client",
                hash: hash,
                name: name
            });
        });
        $(document).on('ifChanged', '#field_perf_check', function() {
            //$("input#field_perf_name").on('change', function() {
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).prop('checked');
            $.post(ACTIONPATH, {
                mode: "change_userfield_check",
                hash: hash,
                name: name
            });
        });
        $(document).on('change', 'select#field_perf_select', function() {
            //$("input#field_perf_name").on('change', function() {
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).val();
            $.post(ACTIONPATH, {
                mode: "change_userfield_select",
                hash: hash,
                name: name
            });
        });
        $(document).on('change', 'input#field_perf_value', function() {
            //$("input#field_perf_name").on('change', function() {
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).val();
            $.post(ACTIONPATH, {
                mode: "change_userfield_value",
                hash: hash,
                name: name
            });
        });
        //field_perf_name
        $(document).on('change', 'input#field_perf_name', function() {
            //$("input#field_perf_name").on('change', function() {
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).val();
            $.post(ACTIONPATH, {
                mode: "change_userfield_name",
                hash: hash,
                name: name
            });
        });
        $(document).on('change', 'input#field_perf_placeholder', function() {
            //$("input#field_perf_placeholder").on('change', function() {
            var hash = $(this).closest('tr').attr('id');
            var name = $(this).val();
            $.post(ACTIONPATH, {
                mode: "change_userfield_placeholder",
                hash: hash,
                name: name
            });
        });
        //del_field_item
        $('body').on('click', 'button#del_userfield_item', function(event) {
            event.preventDefault();
            //console.log($(this).closest('tr').attr('id'));
            var hash = $(this).closest('tr').attr('id');
            bootbox.confirm(get_lang_param('JS_del'), function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=del_userfield_item" + "&hash=" + hash,
                        success: function(html) {
                            $("#user_fields_res").html(html);
                            $("input[type='checkbox']:not(.simple), input[type='radio']:not(.simple)").iCheck({
                                checkboxClass: 'icheckbox_minimal',
                                radioClass: 'iradio_minimal'
                            });
                        }
                    });
                }
            });
        });
        //ldap_step3_obj
        //$('#exampleInputPassword1').prop("disabled", true);
        $("select#ldap_step3_obj").change(function() {
            if ($(this).val() == "all") {
                $('#users_do').prop("disabled", true);
            }
            if ($(this).val() == "selected") {
                $('#users_do').prop("disabled", false);
            }
        });
        //re_user
        $('body').on('click', 'button#re_user', function(event) {
            event.preventDefault();
            var exparam = $("#login_user2").attr('exclude-param');
            var ids = $(this).val();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: ACTIONPATH,
                data: "mode=check_login" + "&login=" + $("#login_user2").val() + "&exclude=" + exparam,
                success: function(html) {
                    $.each(html, function(i, item) {
                        if (item.check_login_status == true) {
                            $("#login_user_grp").removeClass('has-error').addClass('has-success');
                            //$("#errors").val('false');
                            $.ajax({
                                type: "POST",
                                url: ACTIONPATH,
                                data: "mode=re_user" + "&id=" + ids,
                                success: function(html) {
                                    //$("#content_subj").html(html);
                                    window.location = MyHOSTNAME + "users?edit=" + ids;
                                }
                            });
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
        });
        //Если при восстановлении два логина в системе?
        //del_user
        $('body').on('click', 'button#del_user', function(event) {
            event.preventDefault();
            var ids = $(this).val();
            bootbox.confirm(get_lang_param('JS_del'), function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: ACTIONPATH,
                        data: "mode=del_user" + "&id=" + ids,
                        success: function(html) {
                            //$("#content_subj").html(html);
                            window.location = MyHOSTNAME + "users?edit=" + ids;
                        }
                    });
                }
            });
        });
        //ldap_make_import
        $('body').on('click', 'button#ldap_make_import', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=make_ldap_import" + "&users_do=" + encodeURIComponent($('select#users_do').val()) + "&ldap_step3_obj=" + encodeURIComponent($('select#ldap_step3_obj').val()),
                success: function(html) {
                    $("#ldap_res").html(html);
                    //$("#units_text").val('');
                    //window.location = MyHOSTNAME + "users?import_step_3";
                }
            });
        });
        //ldap_import_next_2
        $('body').on('click', 'button#ldap_import_next_2', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=ldap_import_next_2" + "&lang=" + encodeURIComponent($('select#lang').val()) + "&priv=" + encodeURIComponent($("input[type=radio][name=optionsRadios]:checked").val()) + "&unit=" + encodeURIComponent($("#my-select").val()) + "&priv_add_client=" + encodeURIComponent($("#priv_add_client").prop('checked')) + "&priv_edit_client=" + encodeURIComponent($("#priv_edit_client").prop('checked')) + "&mess=" + encodeURIComponent($("textarea#mess").val()) + "&mess_t=" + encodeURIComponent($("input#msg_title").val()) + "&msg_type=" + encodeURIComponent($("input[type=radio][name=optionsRadios_msg]:checked").val()) + "&status=" + encodeURIComponent($("#lock").val()),
                success: function(html) {
                    //$("#ldap_res").html(html);
                    //$("#units_text").val('');
                    window.location = MyHOSTNAME + "users?import_step_3";
                }
            });
            //window.location = MyHOSTNAME + "users?import_step_3";
        });
        $('body').on('click', 'button#ldap_import_next', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=ldap_import_next" + "&ldap_admin_user=" + encodeURIComponent($("#ldap_admin_user").val()) + "&ldap_admin_pass=" + encodeURIComponent($("#ldap_admin_pass").val()) + "&ldap_ip=" + encodeURIComponent($("#ldap_ip").val()) + "&ldap_domain=" + encodeURIComponent($("#ldap_domain").val()) + "&users_fio=" + encodeURIComponent($("#users_fio").val()) + "&users_login=" + encodeURIComponent($("#users_login").val()) + "&users_mail=" + encodeURIComponent($("#users_mail").val()) + "&users_tel=" + encodeURIComponent($("#users_tel").val()) + "&users_adr=" + encodeURIComponent($("#users_adr").val()) + "&users_skype=" + encodeURIComponent($("#users_skype").val()) + "&users_unit=" + encodeURIComponent($("#users_unit").val()),
                success: function(html) {
                    //$("#ldap_res").html(html);
                    //$("#units_text").val('');
                    window.location = MyHOSTNAME + "users?import_step_2";
                }
            });
        });
        $('body').on('click', 'button#ldap_import_check', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=ldap_import_check" + "&ldap_admin_user=" + encodeURIComponent($("#ldap_admin_user").val()) + "&ldap_admin_pass=" + encodeURIComponent($("#ldap_admin_pass").val()) + "&ldap_ip=" + encodeURIComponent($("#ldap_ip").val()) + "&ldap_domain=" + encodeURIComponent($("#ldap_domain").val()),
                success: function(html) {
                    $("#ldap_res").html(html);
                    //$("#units_text").val('');
                    //window.location = MyHOSTNAME + "users?import_step_2";
                }
            });
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
        $("#users_do").select2({
            formatResult: format,
            formatSelection: format,
            allowClear: true,
            maximumSelectionSize: 15,
            width: '100%',
            formatNoMatches: get_lang_param('JS_not_found'),
            escapeMarkup: function(m) {
                return m;
            }
        });
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
                url: MyHOSTNAME + "/app/controllers/users.inc.php",
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
            maximumSelectionSize: 15,
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
                        url: MyHOSTNAME + "app/controllers/users.inc.php",
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
        $('#user_to_def').on('ifChanged', function(event) {
            if ($(this).is(":checked")) {
                $('#to').prop("disabled", false);
                $('#users_do').prop("disabled", false);
            } else {
                $('#to').prop("disabled", true);
                $('#users_do').prop("disabled", true);
            }
        });
        $('input[type=radio][name=optionsRadios]').on('ifChanged', function(event) {
            //$('input[type=radio][name=optionsRadios]').on('change', function(event) {
            //console.log(this.value);
            if (this.value == '0') {
                $('#priv_add_client').iCheck('enable');
                $('#priv_edit_client').iCheck('enable');
                $('#my-select').prop("disabled", false);
                $('#pidrozdil').prop("disabled", true);
                $('#main_unit_user').iCheck('disable');
            } else if (this.value == '1') {
                $('#priv_add_client').iCheck('enable');
                $('#priv_edit_client').iCheck('enable');
                $('#my-select').prop("disabled", false);
                $('#pidrozdil').prop("disabled", true);
                $('#main_unit_user').iCheck('disable');
            } else if (this.value == '2') {
                $('#priv_add_client').iCheck('enable');
                $('#priv_edit_client').iCheck('enable');
                $('#my-select').prop("disabled", false);
                $('#pidrozdil').prop("disabled", true);
                $('#main_unit_user').iCheck('disable');
            } else if (this.value == '4') {
                $('#priv_add_client').iCheck('disable');
                $('#priv_edit_client').iCheck('disable');
                $('#pidrozdil').prop("disabled", false);
                $('#my-select').prop("disabled", true);
                $('#main_unit_user').iCheck('enable');
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
            //console.log($("#my-select").val());
            var add_from = $('#add_field_form').serialize();
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
                    data: "mode=add_user" + "&fio=" + encodeURIComponent($("#fio_user").val()) 
                    + "&main_unit_user=" + encodeURIComponent($("#main_unit_user").prop('checked')) +
                     "&login=" + encodeURIComponent($("#login_user").val()) + "&pass=" + encodeURIComponent($("#exampleInputPassword1").val()) + "&unit=" + encodeURIComponent($("#my-select").val()) + "&priv=" + encodeURIComponent($("input[type=radio][name=optionsRadios]:checked").val()) + "&ldap_auth_key=" + encodeURIComponent($("#ldap_auth_key").prop('checked')) + "&mess=" + encodeURIComponent($("textarea#mess").val()) + "&mess_t=" + encodeURIComponent($("input#msg_title").val()) + "&push=" + encodeURIComponent($("input#push").val()) + "&tel=" + encodeURIComponent($("input#tel").val()) + "&skype=" + encodeURIComponent($("input#skype").val()) + "&adr=" + encodeURIComponent($("input#adr").val()) + "&posada=" + encodeURIComponent($("#posada").val()) + "&pidrozdil=" + encodeURIComponent($("#pidrozdil").val()) + "&lang=" + encodeURIComponent($('select#lang').val()) + "&priv_add_client=" + encodeURIComponent($("#priv_add_client").prop('checked')) + "&priv_edit_client=" + encodeURIComponent($("#priv_edit_client").prop('checked')) + "&mail=" + encodeURIComponent($("#mail").val()) + "&msg_type=" + encodeURIComponent($("input[type=radio][name=optionsRadios_msg]:checked").val()) + "&def_unit_id=" + encodeURIComponent($("#to").val()) + "&mail_nf=" + encodeURIComponent($("#mail_nf").val()) + "&def_user_id=" + encodeURIComponent($("#users_do").val()) + "&user_to_def=" + encodeURIComponent($("#user_to_def").prop('checked')) + "&" + add_from+
                        "&files="+ids,
                    success: function(html) {
                        //console.log(html);
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
            var add_from = $('#add_field_form').serialize();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=edit_user" + "&fio=" + encodeURIComponent($("#fio_user").val()) 
                + "&main_unit_user=" + encodeURIComponent($("#main_unit_user").prop('checked'))
                + "&login=" + encodeURIComponent($("#login_user2").val()) + "&pass=" + encodeURIComponent($("#exampleInputPassword1").val()) + "&unit=" + encodeURIComponent($("#my-select").val()) + "&priv=" + encodeURIComponent($("input[type=radio][name=optionsRadios]:checked").val()) + "&ldap_auth_key=" + encodeURIComponent($("#ldap_auth_key").prop('checked')) + "&mess=" + encodeURIComponent($("textarea#mess").val()) + "&mess_t=" + encodeURIComponent($("input#msg_title").val()) + "&push=" + encodeURIComponent($("input#push").val()) + "&tel=" + encodeURIComponent($("input#tel").val()) + "&skype=" + encodeURIComponent($("input#skype").val()) + "&adr=" + encodeURIComponent($("input#adr").val()) + "&posada=" + encodeURIComponent($("#posada").val()) + "&pidrozdil=" + encodeURIComponent($("#pidrozdil").val()) + "&lang=" + encodeURIComponent($('select#lang').val()) + "&priv_add_client=" + encodeURIComponent($("#priv_add_client").prop('checked')) + "&priv_edit_client=" + encodeURIComponent($("#priv_edit_client").prop('checked')) + "&mail=" + encodeURIComponent($("#mail").val()) + "&status=" + encodeURIComponent($("#lock").val()) + "&idu=" + encodeURIComponent(usid) + "&msg_type=" + encodeURIComponent($("input[type=radio][name=optionsRadios_msg]:checked").val()) + "&def_unit_id=" + encodeURIComponent($("#to").val()) + "&mail_nf=" + encodeURIComponent($("#mail_nf").val()) + "&def_user_id=" + encodeURIComponent($("#users_do").val()) + "&user_to_def=" + encodeURIComponent($("#user_to_def").prop('checked')) + "&" + add_from,
                success: function(html) {
                    //alert(html);
                    window.location = MyHOSTNAME + "users?edit=" + usid + "&ok";
                }
            });
        });
    }
    if (ispath('subj')) {
        function view_sla() {
            $.fn.editable.defaults.mode = 'inline';
            $('a#edit_item').each(function(i, e) {
                $(e).editable({
                    inputclass: 'form-control input-sm input-longtext',
                    emptytext: 'пусто',
                    params: {
                        mode: 'save_subj_item'
                    },
                    tpl: "<input type='text' style='width: 450px'>"
                });
            });
            $('.sortable').nestedSortable({
                ForcePlaceholderSize: true,
                handle: 'div',
                helper: 'clone',
                items: 'li',
                opacity: .6,
                placeholder: 'placeholder',
                revert: 250,
                tabSize: 25,
                tolerance: 'pointer',
                toleranceElement: '> div',
                maxLevels: 1,
                update: function() {
                    list = $(this).nestedSortable('serialize');
                    //console.log(list);
                    $.post(ACTIONPATH, {
                        mode: "sort_sla",
                        list: list
                    }, function(data) {
                        console.log(data);
                    });
                }
            });
            $("input[type='checkbox']:not(.simple), input[type='radio']:not(.simple)").iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal'
            });
        };
        view_sla();
        $('body').on('click', 'i#del_item_subj', function(event) {
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
                            view_sla();
                        }
                    });
                }
                if (result == false) {
                    console.log('false');
                }
            });
        });
        $('body').on('click', 'button#subj_add', function(event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: ACTIONPATH,
                data: "mode=subj_add" + "&text=new_item",
                success: function(html) {
                    $("#content_subj").html(html);
                    //$("#subj_text").val('');
                    view_sla();
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
                        if (item.res == true) {
                            window.location = MyHOSTNAME + "clients?add&ok";
                        } else if (item.res == false) {
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
                data: "mode=edit_user_approve" + "&fio=" + encodeURIComponent($("#fio_user").val()) + "&login=" + encodeURIComponent($("#login_user2").val()) + "&tel=" + encodeURIComponent($("input#tel").val()) + "&skype=" + encodeURIComponent($("input#skype").val()) + "&adr=" + encodeURIComponent($("input#adr").val()) + "&posada=" + encodeURIComponent($("#posada").val()) + "&cid=" + encodeURIComponent(usrid) + "&mail=" + encodeURIComponent($("#mail").val()),
                dataType: "json",
                success: function(html) {
                    //$("#res").hide().html(html).fadeIn(500);
                    $.each(html, function(i, item) {
                        if (item.res == true) {
                            window.location = MyHOSTNAME + "clients?edit=" + usrid + "&ok";
                        } else if (item.res == false) {
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
                url: MyHOSTNAME + "/app/controllers/clients.inc.php",
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
                        url: MyHOSTNAME + "app/controllers/clients.inc.php",
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
    if ((def_filename == "dashboard") || (window.location == MyHOSTNAME) || (def_filename == "index.php")) {
        console.log('true');
        //if ((def_filename == "dashboard")) {
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