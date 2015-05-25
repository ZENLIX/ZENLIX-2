           $(document).ready(function() {
               $.ajaxSetup({
                   // Disable caching of AJAX responses
                   cache: false
               });
               $('.fancybox').fancybox({
                   openEffect: 'elastic',
                   closeEffect: 'elastic'
               });

               function view_todo() {
                   $.fn.editable.defaults.mode = 'inline';
                   $('a#edit_item_todo').each(function(i, e) {
                       $(e).editable({
                           inputclass: 'form-control input-sm input-longtext',
                           emptytext: 'пусто',
                           params: {
                               mode: 'save_todo'
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
                           $.post(ACTIONPATH_PORTAL, {
                               mode: "sort_todo",
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

               function view_helper_qa() {
                   $.fn.editable.defaults.mode = 'inline';
                   $('a#edit_item_qa').each(function(i, e) {
                       $(e).editable({
                           inputclass: 'input-sm',
                           emptytext: 'пусто',
                           params: {
                               mode: 'save_manual_item_qa'
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
                           $.post(ACTIONPATH_PORTAL, {
                               mode: "sort_units_manual_qa",
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

               function view_helper_cat() {
                   $.fn.editable.defaults.mode = 'inline';
                   $('a#edit_item').each(function(i, e) {
                       $(e).editable({
                           inputclass: 'input-sm',
                           emptytext: 'пусто',
                           params: {
                               mode: 'save_manual_item'
                           },
                           tpl: "<input type='text' style='width: 350px'>"
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
                           $.post(ACTIONPATH_PORTAL, {
                               mode: "sort_units_manual",
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
               $("input[type='checkbox']:not(.simple), input[type='radio']:not(.simple)").iCheck({
                   checkboxClass: 'icheckbox_minimal',
                   radioClass: 'iradio_minimal'
               });

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
               moment.lang(MyLANG);
               makemytime(true);
               var ACTIONPATH = MyHOSTNAME + "action";
               var ACTIONPATH_PORTAL = MyHOSTNAME + "portal_action";

               function ispath(p1) {
                   var url = window.location.href;
                   /*
//var str = "foo/bar/test.html";
var n = url.lastIndexOf('/');
var result = url.substring(n + 1);
console.log(result);
*/
                   var zzz = false;
                   if (url.search(p1) >= 0) {
                       zzz = true;
                   }
                   return zzz;
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
               //find_in_manual
               $('body').on('click', 'button#find_in_manual', function(event) {
                   event.preventDefault();
                   window.location = "manual?find=" + $("input#navbar-search-input").val();
               });
               $('body').on('click', 'button#register_new', function(event) {
                   event.preventDefault();
                   $.ajax({
                       type: "POST",
                       url: ACTIONPATH,
                       dataType: "json",
                       data: "mode=register_new" + "&fio=" + $('#login_fio').val() + "&login=" + $('#login_name').val() + "&mail=" + $('#login_mail').val(),
                       success: function(html) {
                           if (html) {
                               $.each(html, function(i, item) {
                                   if (item.check_error == "true") {
                                       $("#main_form_register").html(item.msg);
                                       setTimeout(function() {
                                           window.location = "./";
                                       }, 5000);
                                   } else if (item.check_error == "false") {
                                       $("#error_result").html(item.msg);
                                   }
                               });
                           }
                       }
                   });
               });
               //
               $('body').on('click', '#delete_manual', function(event) {
                   event.preventDefault();
                   //var v=$(this).val();
                   var p = $(this).val();
                   //var type=$("#post").attr('value');
                   bootbox.confirm(get_lang_param('JS_del'), function(result) {
                       if (result == true) {
                           $.ajax({
                               type: "POST",
                               url: ACTIONPATH_PORTAL,
                               data: "mode=helper_item_del" + "&id=" + p,
                               success: function(html) {
                                   //$("#content_items").html(html);
                                   //view_helper_cat();
                                   window.location = MyHOSTNAME + "manual";
                               }
                           });
                       }
                   });
               });
               //make_new_post_idea
               $("#text_idea").keyup(function(e) {
                   if (e.keyCode == 13) {
                       // call function
                       $('#make_new_post_idea').click();
                   }
               });
               $("#text_problem").keyup(function(e) {
                   if (e.keyCode == 13) {
                       // call function
                       $('#make_new_post_problem').click();
                   }
               });
               $("#text_quest").keyup(function(e) {
                   if (e.keyCode == 13) {
                       // call function
                       $('#make_new_post_quest').click();
                   }
               });
               $("#text_review").keyup(function(e) {
                   if (e.keyCode == 13) {
                       // call function
                       $('#make_new_post_review').click();
                   }
               });
               $('body').on('click', 'button#new_post_page', function(event) {
                   event.preventDefault();
                   var v = $(this).val();
                   window.location = MyHOSTNAME + "new_post?p=" + v + "&session_key=1";
               });
               $('body').on('click', 'button#make_new_post_idea', function(event) {
                   event.preventDefault();
                   var t = $("#text_idea").val();
                   $("#new_post_page").val('1');
                   $.ajax({
                       type: "POST",
                       url: ACTIONPATH_PORTAL,
                       dataType: "json",
                       data: "mode=new_post_check" + "&text_idea=" + t,
                       success: function(html) {
                           //console.log(html);
                           $.each(html, function(i, item) {
                               if (item.check_state == false) {
                                   window.location = MyHOSTNAME + "new_post?p=1&session_key=1";
                               } else if (item.check_state == true) {
                                   $.ajax({
                                       type: "POST",
                                       url: ACTIONPATH_PORTAL,
                                       data: "mode=get_res_post_check" + "&text_idea=" + t,
                                       success: function(html) {
                                           $("#maybe_res").html(html);
                                           $("#maybe").hide().fadeIn(500);
                                       }
                                   });
                               }
                           });
                       }
                   });
               });
               $('body').on('click', 'button#make_new_post_problem', function(event) {
                   event.preventDefault();
                   $("#new_post_page").val('2');
                   var t = $("#text_problem").val();
                   $.ajax({
                       type: "POST",
                       url: ACTIONPATH_PORTAL,
                       dataType: "json",
                       data: "mode=new_post_check" + "&text_idea=" + t,
                       success: function(html) {
                           //console.log(html);
                           $.each(html, function(i, item) {
                               if (item.check_state == false) {
                                   window.location = MyHOSTNAME + "new_post?p=2&session_key=1";
                               } else if (item.check_state == true) {
                                   $.ajax({
                                       type: "POST",
                                       url: ACTIONPATH_PORTAL,
                                       data: "mode=get_res_post_check" + "&text_idea=" + t,
                                       success: function(html) {
                                           $("#maybe_res").html(html);
                                           $("#maybe").hide().fadeIn(500);
                                       }
                                   });
                               }
                           });
                       }
                   });
               });
               $('body').on('click', 'button#make_new_post_quest', function(event) {
                   event.preventDefault();
                   var t = $("#text_quest").val();
                   $("#new_post_page").val('3');
                   $.ajax({
                       type: "POST",
                       url: ACTIONPATH_PORTAL,
                       dataType: "json",
                       data: "mode=new_post_check" + "&text_idea=" + t,
                       success: function(html) {
                           //console.log(html);
                           $.each(html, function(i, item) {
                               if (item.check_state == false) {
                                   window.location = MyHOSTNAME + "new_post?p=3&session_key=1";
                               } else if (item.check_state == true) {
                                   $.ajax({
                                       type: "POST",
                                       url: ACTIONPATH_PORTAL,
                                       data: "mode=get_res_post_check" + "&text_idea=" + t,
                                       success: function(html) {
                                           $("#maybe_res").html(html);
                                           $("#maybe").hide().fadeIn(500);
                                       }
                                   });
                               }
                           });
                       }
                   });
               });
               $('body').on('click', 'button#make_new_post_review', function(event) {
                   event.preventDefault();
                   var t = $("#text_review").val();
                   $("#new_post_page").val('4');
                   $.ajax({
                       type: "POST",
                       url: ACTIONPATH_PORTAL,
                       dataType: "json",
                       data: "mode=new_post_check" + "&text_idea=" + t,
                       success: function(html) {
                           //console.log(html);
                           $.each(html, function(i, item) {
                               if (item.check_state == false) {
                                   window.location = MyHOSTNAME + "new_post?p=4&session_key=1";
                               } else if (item.check_state == true) {
                                   $.ajax({
                                       type: "POST",
                                       url: ACTIONPATH_PORTAL,
                                       data: "mode=get_res_post_check" + "&text_idea=" + t,
                                       success: function(html) {
                                           $("#maybe_res").html(html);
                                           $("#maybe").hide().fadeIn(500);
                                       }
                                   });
                               }
                           });
                       }
                   });
               });
               if (ispath('edit_cat')) {
                   //edit_manual_cat
                   $('body').on('click', 'i#edit_manual_cat', function(event) {
                       event.preventDefault();
                       //console.log('Pv');
                       var v = $(this).attr('value');
                       window.location = MyHOSTNAME + "manual?" + v + "&edit_manual";
                   });
                   $('body').on('click', 'i#open_link', function(event) {
                       event.preventDefault();
                       //console.log('Pv');
                       var v = $(this).attr('value');
                       window.location = MyHOSTNAME + "manual?" + v;
                   });
                   $(document).on('ifChanged', '#make_main_manual', function() {
                       //$("input#field_perf_name").on('change', function() {
                       //console.log($(this).closest('tr').attr('id'));
                       var hash = $(this).attr('value');
                       var name = $(this).prop('checked');
                       $.post(ACTIONPATH_PORTAL, {
                           mode: "change_manual_cat_main",
                           hash: hash,
                           name: name
                       });
                   });
                   $(document).on('ifChanged', '#make_cat_manual', function() {
                       //$("input#field_perf_name").on('change', function() {
                       //console.log($(this).closest('tr').attr('id'));
                       var hash = $(this).attr('value');
                       var name = $(this).prop('checked');
                       $.post(ACTIONPATH_PORTAL, {
                           mode: "change_manual_cat_type",
                           hash: hash,
                           name: name
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
                                   url: ACTIONPATH_PORTAL,
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
                   $('body').on('click', 'button#add_manual_item', function(event) {
                       event.preventDefault();
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: "mode=items_view",
                           success: function(html) {
                               $('#content_items').html(html);
                               view_helper_cat();
                           }
                       });
                   });
                   view_helper_cat();
               }
               if (ispath('edit_some_qa')) {
                   $('body').on('click', 'button#make_edit_manual_qa', function(event) {
                       event.preventDefault();
                       var v = $("#manual_hash").val();
                       var sHTML = $('#note').code();
                       //var title = $("#cat").val();
                       var subj = $("#subj").val();
                       var data = {
                           'mode': 'edit_manual_qa',
                           'subj': subj,
                           'msg': sHTML,
                           'hn': v
                           //'title':title
                       };
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: data,
                           dataType: "json",
                           success: function(html) {
                               console.log(html);
                               $.each(html, function(i, item) {
                                   if (item.check_error == true) {
                                       window.location = MyHOSTNAME + "manual?edit_qa";
                                   } else if (item.check_error == false) {
                                       //$('#res').html(item.msg); 
                                       $("#post_res").hide().html(item.msg).fadeIn(500);
                                   }
                               });
                           }
                       });
                   });
                   $('#note').summernote({
                       height: 300,
                       focus: true,
                       //lang: get_lang_param('summernote_lang'),
                      // disableDragAndDrop: true,
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
                       onImageUpload: function(files, editor, welEditable) {
                           sendFile(files[0], editor, welEditable);
                       },
                       oninit: function() {}
                   });
               }
               if (ispath('edit_qa')) {
                   $('body').on('click', 'i#del_item_qa', function(event) {
                       event.preventDefault();
                       var ids = $(this).attr('value');
                       bootbox.confirm(get_lang_param('JS_del'), function(result) {
                           if (result == true) {
                               $.ajax({
                                   type: "POST",
                                   url: ACTIONPATH_PORTAL,
                                   data: "mode=helper_qa_del" + "&id=" + ids,
                                   success: function(html) {
                                       $("#content_items").html(html);
                                       view_helper_qa();
                                   }
                               });
                           }
                           if (result == false) {
                               console.log('false');
                           }
                       });
                   });
                   $('body').on('click', 'i#edit_manual_qa', function(event) {
                       event.preventDefault();
                       //console.log('Pv');
                       var v = $(this).attr('value');
                       window.location = MyHOSTNAME + "manual?" + "edit_some_qa=" + v;
                   });
                   $('body').on('click', 'button#add_qa_item', function(event) {
                       event.preventDefault();
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: "mode=items_qa_view",
                           success: function(html) {
                               $('#content_items').html(html);
                               view_helper_qa();
                           }
                       });
                   });
                   view_helper_qa();
               }
               if (ispath('edit_manual')) {
                   $('body').on('click', 'button#make_edit_manual', function(event) {
                       event.preventDefault();
                       var v = $("#manual_hash").val();
                       var sHTML = $('#note').code();
                       //var title = $("#cat").val();
                       var subj = $("#subj").val();
                       var data = {
                           'mode': 'edit_manual',
                           'subj': subj,
                           'msg': sHTML,
                           'hn': v
                           //'title':title
                       };
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: data,
                           dataType: "json",
                           success: function(html) {
                               console.log(html);
                               $.each(html, function(i, item) {
                                   if (item.check_error == true) {
                                       window.location = MyHOSTNAME + "manual?" + v;
                                   } else if (item.check_error == false) {
                                       //$('#res').html(item.msg); 
                                       $("#post_res").hide().html(item.msg).fadeIn(500);
                                   }
                               });
                           }
                       });
                   });
                   var previewNode = document.querySelector("#template");
                   previewNode.id = "";
                   var previewTemplate = previewNode.parentNode.innerHTML;
                   previewNode.parentNode.removeChild(previewNode);
                   var ph = $('#manual_hash').val();
                   $('#myid').dropzone({
                       url: ACTIONPATH_PORTAL,
                       maxFilesize: 100,
                       paramName: "myfile",
                       params: {
                           mode: 'upload_post_file',
                           post_hash: ph,
                           type: '0',
                           is_tmp: '1'
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
                                   url: ACTIONPATH_PORTAL,
                                   data: "mode=delete_post_file" + "&uniq_code=" + server_file,
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
                   $('#note').summernote({
                       height: 300,
                       focus: true,
                       //lang: get_lang_param('summernote_lang'),
                      // disableDragAndDrop: false,
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
                       onImageUpload: function(files, editor, welEditable) {
                           sendFile(files[0], editor, welEditable);
                       },
                       oninit: function() {}
                   });
               }
               if (ispath('new_manual')) {
                   $('#note').summernote({
                       height: 300,
                       focus: true,
                       // lang: get_lang_param('summernote_lang'),
                      // disableDragAndDrop: true,
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
                       onImageUpload: function(files, editor, welEditable) {
                           sendFile(files[0], editor, welEditable);
                       },
                       oninit: function() {}
                   });
                   //make_new_post_data
                   $('body').on('click', 'button#make_new_manual_data', function(event) {
                       event.preventDefault();
                       var v = $("#manual_hash").val();
                       var sHTML = $('#note').code();
                       var type = $("#type").val();
                       var subj = $("#subj").val();
                       var data = {
                           'mode': 'add_manual',
                           'subj': subj,
                           'msg': sHTML,
                           'hn': v,
                           'type': type
                       };
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: data,
                           dataType: "json",
                           success: function(html) {
                               console.log(html);
                               $.each(html, function(i, item) {
                                   if (item.check_error == true) {
                                       window.location = MyHOSTNAME + "manual?" + v;
                                   } else if (item.check_error == false) {
                                       //$('#res').html(item.msg); 
                                       $("#post_res").hide().html(item.msg).fadeIn(500);
                                   }
                               });
                           }
                       });
                   });
                   var previewNode = document.querySelector("#template");
                   previewNode.id = "";
                   var previewTemplate = previewNode.parentNode.innerHTML;
                   previewNode.parentNode.removeChild(previewNode);
                   var ph = $('#manual_hash').val();
                   $('#myid').dropzone({
                       url: ACTIONPATH_PORTAL,
                       maxFilesize: 100,
                       paramName: "myfile",
                       params: {
                           mode: 'upload_post_file',
                           post_hash: ph,
                           type: '0',
                           is_tmp: '1'
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
                                   url: ACTIONPATH_PORTAL,
                                   data: "mode=delete_post_file" + "&uniq_code=" + server_file,
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
               if (ispath('new_post')) {
                   $('#note').summernote({
                       height: 300,
                       focus: true,
                       //lang: get_lang_param('summernote_lang'),
                      // disableDragAndDrop: true,
                       toolbar: [
                           //['style', ['style']], // no style button
                           ['style', ['bold', 'italic', 'underline', 'clear']],
                           ['fontsize', ['fontsize']],
                           ['color', ['color']],
                           ['para', ['ul', 'ol', 'paragraph']],
                           ['height', ['height']],
                           ['table', ['table']],
                           ['link', ['link']]
                       ],
                       onImageUpload: function(files, editor, welEditable) {
                           sendFile(files[0], editor, welEditable);
                       },
                       oninit: function() {}
                   });
                   var previewNode = document.querySelector("#template");
                   previewNode.id = "";
                   var previewTemplate = previewNode.parentNode.innerHTML;
                   previewNode.parentNode.removeChild(previewNode);
                   var ph = $('#post_hash').val();
                   $('#myid').dropzone({
                       url: ACTIONPATH_PORTAL,
                       maxFilesize: 100,
                       paramName: "myfile",
                       params: {
                           mode: 'upload_post_file',
                           post_hash: ph,
                           type: '0',
                           is_tmp: '0'
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
                                   url: ACTIONPATH_PORTAL,
                                   data: "mode=delete_post_file" + "&uniq_code=" + server_file,
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
                   //make_new_post_data
                   $('body').on('click', 'button#make_new_post_data', function(event) {
                       event.preventDefault();
                       var v = $("#post_hash").val();
                       var sHTML = $('#note').code();
                       var type = $("#type").val();
                       var subj = $("#subj").val();
                       var data = {
                           'mode': 'add_post',
                           'subj': subj,
                           'msg': sHTML,
                           'hn': v,
                           'type': type
                       };
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: data,
                           dataType: "json",
                           success: function(html) {
                               console.log(html);
                               $.each(html, function(i, item) {
                                   if (item.check_error == true) {
                                       window.location = MyHOSTNAME + "thread?" + v;
                                   } else if (item.check_error == false) {
                                       //$('#res').html(item.msg); 
                                       $("#post_res").hide().html(item.msg).fadeIn(500);
                                   }
                               });
                           }
                       });
                   });
               }
               if (ispath('cat')) {
                   var options_cat_post = {
                       currentPage: $("#cur_page").val(),
                       totalPages: $("#total_pages").val(),
                       bootstrapMajorVersion: 3,
                       size: "small",
                       itemContainerClass: function(type, page, current) {
                           return (page === current) ? "active" : "pointer-cursor";
                       },
                       onPageClicked: function(e, originalEvent, type, page) {
                           var current = $("#curent_page").attr('value');
                           var st_str = $("#st_str").attr('value');
                           if (page != current) {
                               window.location = MyHOSTNAME + "cat?" + $("#cat").val() + st_str + "&p=" + page;
                              
                           }
                       }
                   }
                   $('#cat_post').bootstrapPaginator(options_cat_post);
               }
               if (ispath('edit_feed')) {
                   $('body').on('click', 'button#make_edit_feed', function(event) {
                       event.preventDefault();
                       var v = $("#news_hash").val();
                       var sHTML = $('#note').code();
                       var title = $("#title").val();
                       var subj = $("#subj").val();
                       var data = {
                           'mode': 'edit_news',
                           'subj': subj,
                           'msg': sHTML,
                           'hn': v,
                           'title': title
                       };
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: data,
                           dataType: "json",
                           success: function(html) {
                               console.log(html);
                               $.each(html, function(i, item) {
                                   if (item.check_error == true) {
                                       window.location = MyHOSTNAME + "feed?" + v;
                                   } else if (item.check_error == false) {
                                       //$('#res').html(item.msg); 
                                       $("#post_res").hide().html(item.msg).fadeIn(500);
                                   }
                               });
                           }
                       });
                   });
                   $('#note').summernote({
                       height: 300,
                       focus: true,
                       //lang: get_lang_param('summernote_lang'),
                       //disableDragAndDrop: true,
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
                       onImageUpload: function(files, editor, welEditable) {
                           sendFile(files[0], editor, welEditable);
                       },
                       oninit: function() {}
                   });
               }
               if (ispath('version')) {
                   $(document).on('ifChanged', '#make_todo_success', function() {
                       //$("input#field_perf_name").on('change', function() {
                       //console.log($(this).closest('tr').attr('id'));
                       var hash = $(this).attr('value');
                       var name = $(this).prop('checked');
                       $.post(ACTIONPATH_PORTAL, {
                           mode: "change_todo_success",
                           hash: hash,
                           name: name
                       });
                   });
                   view_todo();
                   $('body').on('click', 'i#del_item_todo', function(event) {
                       event.preventDefault();
                       var ids = $(this).attr('value');
                       bootbox.confirm(get_lang_param('JS_del'), function(result) {
                           if (result == true) {
                               $.ajax({
                                   type: "POST",
                                   url: ACTIONPATH_PORTAL,
                                   data: "mode=todo_item_del" + "&id=" + ids,
                                   success: function(html) {
                                       $("#content_items").html(html);
                                       view_todo();
                                   }
                               });
                           }
                           if (result == false) {
                               console.log('false');
                           }
                       });
                   });
                   //conf_edit_version_banner
                   $('body').on('click', 'button#conf_edit_version_banner', function(event) {
                       event.preventDefault();
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: "mode=conf_edit_version_banner" + "&portal_box_version_n=" + encodeURIComponent($("#portal_box_version_n").val()) + "&portal_box_version_text=" + encodeURIComponent($("#portal_box_version_text").val()) + "&portal_box_version_icon=" + encodeURIComponent($("#portal_box_version_icon").val()),
                           success: function(html) {
                               //$('#conf_edit_version_banner_res').html(html);
                               $('#conf_edit_version_banner_res').hide().html(html).fadeIn(500);
                               setTimeout(function() {
                                   $('#conf_edit_version_banner_res').children('.alert').fadeOut(500);
                               }, 3000);
                           }
                       });
                   });
                   $('body').on('click', 'button#add_todo_item', function(event) {
                       event.preventDefault();
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: "mode=items_todo_view",
                           success: function(html) {
                               $('#content_items').html(html);
                               view_todo();
                           }
                       });
                   });
                   //make_new_feed
                   $('body').on('click', 'button#make_new_version', function(event) {
                       event.preventDefault();
                       var v = $("#news_hash").val();
                       var sHTML = $('#note').code();
                       var title = $("#title").val();
                       var subj = $("#subj").val();
                       var data = {
                           'mode': 'add_version',
                           'subj': subj,
                           'msg': sHTML,
                           'hn': v,
                           'title': title
                       };
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: data,
                           dataType: "json",
                           success: function(html) {
                               console.log(html);
                               $.each(html, function(i, item) {
                                   if (item.check_error == true) {
                                       window.location = MyHOSTNAME + "version?" + v;
                                   } else if (item.check_error == false) {
                                       //$('#res').html(item.msg); 
                                       $("#post_res").hide().html(item.msg).fadeIn(500);
                                   }
                               });
                           }
                       });
                   });
                   $('body').on('click', 'button#make_edit_version', function(event) {
                       event.preventDefault();
                       var v = $("#news_hash").val();
                       var sHTML = $('#note').code();
                       var title = $("#title").val();
                       var subj = $("#subj").val();
                       var data = {
                           'mode': 'edit_version',
                           'subj': subj,
                           'msg': sHTML,
                           'hn': v,
                           'title': title
                       };
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: data,
                           dataType: "json",
                           success: function(html) {
                               console.log(html);
                               $.each(html, function(i, item) {
                                   if (item.check_error == true) {
                                       window.location = MyHOSTNAME + "version?" + v;
                                   } else if (item.check_error == false) {
                                       //$('#res').html(item.msg); 
                                       $("#post_res").hide().html(item.msg).fadeIn(500);
                                   }
                               });
                           }
                       });
                   });
                   $('body').on('click', '#delete_version', function(event) {
                       event.preventDefault();
                       //var v=$(this).val();
                       var p = $(this).val();
                       //var type=$("#post").attr('value');
                       bootbox.confirm(get_lang_param('JS_del'), function(result) {
                           if (result == true) {
                               $.ajax({
                                   type: "POST",
                                   url: ACTIONPATH_PORTAL,
                                   data: {
                                       mode: 'del_version',
                                       news_hash: p
                                   },
                                   success: function(html) {
                                       //console.log(html);
                                       window.location = MyHOSTNAME + "version";
                                       //$("#"+v+" .editable_text").html(html);
                                   }
                               });
                           }
                       });
                   });
               }
               if (ispath('feed')) {
                   $('body').on('click', '#delete_news', function(event) {
                       event.preventDefault();
                       //var v=$(this).val();
                       var p = $(this).val();
                       //var type=$("#post").attr('value');
                       bootbox.confirm(get_lang_param('JS_del'), function(result) {
                           if (result == true) {
                               $.ajax({
                                   type: "POST",
                                   url: ACTIONPATH_PORTAL,
                                   data: {
                                       mode: 'del_news',
                                       news_hash: p
                                   },
                                   success: function(html) {
                                       //console.log(html);
                                       window.location = MyHOSTNAME + "feed";
                                       //$("#"+v+" .editable_text").html(html);
                                   }
                               });
                           }
                       });
                   });
               }
               if (ispath('new_feed')) {
                   //make_new_feed
                   $('body').on('click', 'button#make_new_feed', function(event) {
                       event.preventDefault();
                       var v = $("#news_hash").val();
                       var sHTML = $('#note').code();
                       var title = $("#title").val();
                       var subj = $("#subj").val();
                       var data = {
                           'mode': 'add_news',
                           'subj': subj,
                           'msg': sHTML,
                           'hn': v,
                           'title': title
                       };
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: data,
                           dataType: "json",
                           success: function(html) {
                               console.log(html);
                               $.each(html, function(i, item) {
                                   if (item.check_error == true) {
                                       window.location = MyHOSTNAME + "feed?" + v;
                                   } else if (item.check_error == false) {
                                       //$('#res').html(item.msg); 
                                       $("#post_res").hide().html(item.msg).fadeIn(500);
                                   }
                               });
                           }
                       });
                   });
                   $('#note').summernote({
                       height: 300,
                       focus: true,
                       //lang: get_lang_param('summernote_lang'),
                      // disableDragAndDrop: true,
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
                       onImageUpload: function(files, editor, welEditable) {
                           sendFile(files[0], editor, welEditable);
                       },
                       oninit: function() {}
                   });
               }
               if (ispath('thread')) {
                   $('body').on('click', '.make_cat_type', function(event) {
                       event.preventDefault();
                       var v = $(this).val();
                       var type = $(this).attr('option');
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: {
                               mode: 'set_post_cat',
                               post_hash: v,
                               type: type
                           },
                           success: function(html) {
                               //console.log(html);
                               window.location = MyHOSTNAME + "thread?" + v;
                               //$("#"+v+" .editable_text").html(html);
                           }
                       });
                   });
                   $(".direct-chat-msg").on({
                       mouseenter: function() {
                           //stuff to do on mouse enter
                           $(this).find(".subclass").fadeIn(800);
                       },
                       mouseleave: function() {
                           //stuff to do on mouse leave
                           $(this).find(".subclass").css("display", "none");
                       }
                   });
                   //main-post-del
                   $('body').on('click', '.main-post-del', function(event) {
                       event.preventDefault();
                       //var v=$(this).val();
                       var p = $(this).attr('value');
                       //var type=$("#post").attr('value');
                       bootbox.confirm(get_lang_param('JS_del'), function(result) {
                           if (result == true) {
                               $.ajax({
                                   type: "POST",
                                   url: ACTIONPATH_PORTAL,
                                   data: {
                                       mode: 'del_post',
                                       post_hash: p
                                   },
                                   success: function(html) {
                                       console.log(html);
                                       window.location = MyHOSTNAME;
                                   }
                               });
                           }
                       });
                   });
                   //.comment-bar
                   $('body').on('click', '.main-cancel-edit', function(event) {
                       event.preventDefault();
                       var v = $(this).val();
                       var type = $("#post").attr('value');
                       $("#" + v + " .editable_text").destroy();
                       $("#" + v + " .comment-bar").fadeIn(800);
                       $("#" + v + " .edit-bar").hide();
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: {
                               mode: 'get_orig_post',
                               post_hash: v,
                               type: type
                           },
                           success: function(html) {
                               //console.log(html);
                               //window.location = MyHOSTNAME + "thread?"+v;
                               $("#" + v + " .editable_text").html(html);
                           }
                       });
                   });
                   $('body').on('click', 'a#delete_file', function(event) {
                       event.preventDefault();
                       var code = $(this).attr('value');
                       var type = $("#post").attr('value');
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: {
                               mode: 'delete_file',
                               file_hash: code
                           },
                           success: function(html) {
                               //console.log(html);
                               window.location = MyHOSTNAME + "thread?" + type;
                               //$("#"+v+" .editable_text").html(html);
                           }
                       });
                   });
                   $('body').on('click', '.cancel-edit', function(event) {
                       event.preventDefault();
                       var v = $(this).val();
                       var type = $("#post").attr('value');
                       $("#" + v + " .editable_text").destroy();
                       //$("#"+v+" .comment-bar").fadeIn(800);
                       $("#" + v + " .edit-bar").hide();
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: {
                               mode: 'get_orig_comment',
                               post_hash: v,
                               type: type
                           },
                           success: function(html) {
                               //console.log(html);
                               //window.location = MyHOSTNAME + "thread?"+v;
                               $("#" + v + " .editable_text").html(html);
                           }
                       });
                   });
                   $('body').on('click', '.main-save-edit', function(event) {
                       event.preventDefault();
                       var v = $(this).val();
                       var m = $("#" + v + " .editable_text").code();
                       var type = $("#post").attr('value');
                       $("#" + v + " .editable_text").destroy();
                       //$("#"+v+" .comment-bar").fadeIn(800);
                       //$("#"+v+" .edit-bar").hide();
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: {
                               mode: 'update_post',
                               post_hash: v,
                               msg: m,
                               type: type
                           },
                           success: function(html) {
                               //console.log(html);
                               //window.location = MyHOSTNAME + "thread?"+v;
                               $("#" + v + " .editable_text").html(html);
                           }
                       });
                   });
                   $('body').on('click', '.save-edit', function(event) {
                       event.preventDefault();
                       var v = $(this).val();
                       var m = $("#" + v + " .editable_text").code();
                       var type = $("#post").attr('value');
                       $("#" + v + " .editable_text").destroy();
                       $("#" + v + " .comment-bar").fadeIn(800);
                       $("#" + v + " .edit-bar").hide();
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: {
                               mode: 'update_comment',
                               post_hash: v,
                               msg: m,
                               type: type
                           },
                           success: function(html) {
                               //console.log(html);
                               //window.location = MyHOSTNAME + "thread?"+v;
                               $("#" + v + " .editable_text").html(html);
                           }
                       });
                   });
                   $('body').on('click', '.post-del', function(event) {
                       event.preventDefault();
                       var v = $(this).val();
                       var p = $("#post").val();
                       var type = $("#post").attr('value');
                       bootbox.confirm(get_lang_param('JS_del'), function(result) {
                           if (result == true) {
                               $.ajax({
                                   type: "POST",
                                   url: ACTIONPATH_PORTAL,
                                   data: {
                                       mode: 'del_comment',
                                       post_hash: v,
                                       type: type
                                   },
                                   success: function(html) {
                                       //console.log(html);
                                       window.location = MyHOSTNAME + "thread?" + p;
                                       //$("#"+v+" .editable_text").html(html);
                                   }
                               });
                           }
                       });
                   });
                   //main-post-edit
                   $('body').on('click', '.main-post-edit', function(event) {
                       event.preventDefault();
                       var v = $(this).val();
                       $("#" + v + " .editable_text").summernote({
                           height: 200,
                           focus: false,
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
                           // lang: get_lang_param('summernote_lang'),
                           onImageUpload: function(files, editor, welEditable) {
                               sendFile(files[0], editor, welEditable);
                           },
                           oninit: function() {}
                       });
                       //$(".editable").html('');
                       $("#" + v + " .comment-bar").hide();
                       $("#" + v + " .edit-bar").fadeIn(800);
                       //$("#"+v+" .editable_text").hide();
                       //console.log(v);
                   });
                   $('body').on('click', '.post-edit', function(event) {
                       event.preventDefault();
                       var v = $(this).val();
                       $("#" + v + " .editable_text").summernote({
                           height: 200,
                           focus: false,
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
                           //lang: get_lang_param('summernote_lang'),
                           onImageUpload: function(files, editor, welEditable) {
                               sendFile(files[0], editor, welEditable);
                           },
                           oninit: function() {}
                       });
                       //$(".editable").html('');
                       $("#" + v + " .comment-bar").hide();
                       $("#" + v + " .edit-bar").fadeIn(800);
                       //$("#"+v+" .editable_text").hide();
                       //console.log(v);
                   });
                   var options_comment_page = {
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
                               window.location = MyHOSTNAME + "thread?" + $("#post").val() + "&p=" + page;
                              
                           }
                       }
                   }
                   $('#comm_pages').bootstrapPaginator(options_comment_page);
                   $('body').on('click', 'button#do_like', function(event) {
                       event.preventDefault();
                       var v = $("#post_hash").val();
                       var act = $(this).attr('value');
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: {
                               mode: 'set_post_like',
                               post_hash: v,
                               val: act
                           },
                           success: function(html) {
                               //console.log(html);
                               window.location = MyHOSTNAME + "thread?" + v;
                           }
                       });
                   });
                   //make_post_status
                   $('body').on('click', 'button#make_post_status', function(event) {
                       event.preventDefault();
                       var v = $("#post_hash").val();
                       var act = $(this).attr('value');
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: {
                               mode: 'set_post_status',
                               post_hash: v,
                               val: act
                           },
                           success: function(html) {
                               //console.log(html);
                               window.location = MyHOSTNAME + "thread?" + v;
                           }
                       });
                   });
                   //add_comment
                   //make_new_post_data
                   $('body').on('click', 'button#add_comment', function(event) {
                       event.preventDefault();
                       var v = $("#post_hash").val();
                       var c = $("#comment_hash").val();
                       var sHTML = $('#notes').code();
                       var type = $('#mc').prop('checked');
                       //var type = $("#type").val();
                       //var subj = $("#subj").val();
                       $.ajax({
                           type: "POST",
                           url: ACTIONPATH_PORTAL,
                           data: {
                               mode: 'add_comment',
                               msg: sHTML,
                               ph: v,
                               ch: c,
                               type: type
                           },
                           /*
                "mode=add_comment"+
                "&msg="+sHTML+
                "&ph="+v+
                "&ch="+c+
                "&type="+type,
*/
                           dataType: "json",
                           success: function(html) {
                               console.log(html);
                               $.each(html, function(i, item) {
                                   if (item.check_error == true) {
                                       window.location = MyHOSTNAME + "thread?" + v;
                                   } else if (item.check_error == false) {
                                       //$('#res').html(item.msg); 
                                       $("#post_res").hide().html(item.msg).fadeIn(500);
                                   }
                               });
                           }
                       });
                   });
                   $('#notes').summernote({
                       height: 200,
                       focus: false,
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
                       //lang: get_lang_param('summernote_lang'),
                       onImageUpload: function(files, editor, welEditable) {
                           sendFile(files[0], editor, welEditable);
                       },
                       oninit: function() {}
                   });
                   if (VALIDATE == true) {
                       var previewNode = document.querySelector("#template");
                       previewNode.id = "";
                       var previewTemplate = previewNode.parentNode.innerHTML;
                       previewNode.parentNode.removeChild(previewNode);
                       var ph = $("#comment_hash").val();
                       $('#myid').dropzone({
                           url: ACTIONPATH_PORTAL,
                           maxFilesize: 100,
                           paramName: "myfile",
                           params: {
                               mode: 'upload_post_file',
                               post_hash: ph,
                               type: '1',
                               is_tmp: '1'
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
                                       url: ACTIONPATH_PORTAL,
                                       data: "mode=delete_post_file" + "&uniq_code=" + server_file,
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
               }
           });