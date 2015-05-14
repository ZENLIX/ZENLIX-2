<?php
session_start();

include_once ("conf.php");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (!isset($CONF_DB)) {
include "sys/install.php";
exit(0);
}

//если нет файла конфигурации то открыть установку системы


//если пользователь авторизирован:
    //GET_logout


//GET_register
//GET_forgot
//GET_api



//if (isset($CONF_DB)) {
include_once ("functions.inc.php");
include_once('library/AltoRouter.php');

include_once("inc/route_actions.php");




$router = new AltoRouter();

//echo get_base_path();
$router->setBasePath(get_base_path());

$router->map( 'GET', '/register', 'registerAction');
$router->map( 'GET', '/forgot', 'forgotAction');
$router->map( 'GET', '/index.php', 'indexAction' );
$router->map( 'GET', '/', 'indexAction' );
$router->map( 'GET', '/auth', 'auth_get');
$router->map( 'GET', '/create', 'createAction');
$router->map( 'GET', '/list', 'listAction');
$router->map( 'GET', '/stats', 'statsAction');
$router->map( 'GET', '/helper', 'helperAction');
$router->map( 'GET', '/notes', 'notesAction');
$router->map( 'GET', '/profile', 'profileAction');
$router->map( 'GET', '/users', 'usersAction');
$router->map( 'GET', '/help', 'helpAction');
$router->map( 'GET', '/deps', 'depsAction');
$router->map( 'GET', '/approve', 'approveAction');
$router->map( 'GET', '/units', 'unitsAction');
$router->map( 'GET', '/posada', 'posadaAction');
$router->map( 'GET', '/ticket', 'ticketAction');
$router->map( 'GET', '/subj', 'subjAction');
$router->map( 'GET', '/view_user', 'view_userAction');
$router->map( 'GET', '/userinfo', 'userinfoAction');
$router->map( 'GET', '/config', 'perfAction');
$router->map( 'GET', '/files', 'filesAction');
$router->map( 'GET', '/news', 'newsAction');
$router->map( 'GET', '/clients', 'clientsAction');
$router->map( 'GET', '/main_stats', 'all_statsAction');
$router->map( 'GET', '/user_stats', 'user_statsAction');
$router->map( 'GET', '/sla_rep', 'sla_repAction');
$router->map( 'GET', '/scheduler', 'schedulerAction');
$router->map( 'GET', '/messages', 'messagesAction');
$router->map( 'GET', '/print_ticket', 'print_ticketAction');
$router->map( 'GET', '/calendar', 'calendarAction');
$router->map( 'GET', '/portal', 'portalAction');
$router->map( 'GET', '/mailers', 'mailersAction');


///////////////////////////////////////
$router->map( 'GET', '/manual','manualAction');

$router->map( 'GET', '/version', 'versionAction');

$router->map( 'GET', '/feed', 'feedAction');

$router->map( 'GET', '/cat', 'catAction');

$router->map( 'GET', '/new_post', 'new_postAction');

$router->map( 'GET', '/thread', 'postAction');



$router->map( 'POST', '/auth', 'auth');



$router->map( 'POST', '/action', function() {
    global $dbConnection, $CONF;
    require 'actions.php';
});


$router->map( 'GET', '/dashboard', 'dashboardAction');



$router->map( 'GET', '/logout', 'logoutAction');








$match = $router->match();
if( $match && is_callable( $match['target'] ) ) {
    call_user_func_array( $match['target'], $match['params'] ); 
} else {
    // no route was matched
    global $dbConnection, $CONF;
    $privs=get_privs();
    if ($privs == "CLIENT") {include ('inc/client.404.inc.php');}
    else if ($privs == "USER") {include ('inc/404.inc.php');}
    if ($privs == "GUEST") {include ('inc/auth.php');}
}

/*
$router= new router();


$router->map(array(
'path'=>'register',
'method'=>'GET',
'params'=>'page',
'privs'=>'GUEST', 
'portal'=>false,
'action'=>'inc/register.php'
    ));


$router->map(array(
'path'=>'register',
'method'=>'GET',
'params'=>'page',
'privs'=>'GUEST', 
'portal'=>true,
'action'=>'inc/main_portal/register.php'
    ));




$router->map(array(
'path'=>'forgot',
'method'=>'GET',
'params'=>'page',
'privs'=>'GUEST', 
'portal'=>false, /////////////
'action'=>'inc/forgot.php'
    ));


$router->map(array(
'path'=>'api',
'method'=>'GET',
'params'=>'page',
'privs'=>'GUEST', 
'portal'=>false, /////////////
'action'=>'api.php'
    ));








$router->release();


*/

//}
//else {
//    include "sys/install.php";
//}







/*
по привилегиям USER/CLIENT/GUEST
по типам GET/POST
по порталу TRUE/FALSE





function map($path, $type, $params, $privs, $portal) {



if ($type == "post") {

}
if ($type == "get") {
    

    if ($_GET[$params] == $path) {

        if ($privs == "GUEST") {

        }

    }


}



}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


*/





/*
if (isset($CONF_DB)) {
    
//проверка файла конфигурации системы


include ("functions.inc.php");





    $main_portal=$CONF['main_portal'];




//проверка пользователя?
$val_sta=false;
if ((validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) || (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code']))) {
    $val_sta=true;
}











   // $validate_p = false;
if ($val_sta == true) {


    if (isset($_GET['logout'])) {
        session_destroy();
        unset($_SESSION);
        session_unset();
        setcookie('authhash_uid', "");
        setcookie('authhash_code', "");
        unset($_COOKIE['authhash_uid']);
        unset($_COOKIE['authhash_code']);
        session_regenerate_id();
        //$_SESSION['z.times']=1;
        header("Location: " . $CONF['hostname']);
    }

}
    
    if ($_GET['page'] == "register") {

        if ($main_portal == "true") {
include ('inc/main_portal/register.php');
        }
        else if ($main_portal == "false") {
include ('inc/register.php');
        }
        
    } else 

    
    if ($_GET['page'] == "forgot") {
        include ('inc/forgot.php');
    } 
    else if ($_GET['page'] == "api") {
        include ('api.php');
    }
    else {
        













/////////////////START IF LOGIN-AUTH FORM SUBMITTED/////////////////////////

        //echo($_COOKIE['authhash_code']);
        $rq = 0;
        if (isset($_POST['login']) && isset($_POST['password'])) {
            
            $rq = 1;

 if ($_SESSION['z.times'] < 5 ) {
    
 

            $req_url = $_POST['req_url'];
            $rm = $_POST['remember_me'];
            
            $login = ($_POST['login']);
            $password = md5($_POST['password']);
            
            //LDAP-auth
            if (get_user_authtype($login)) {
                if (ldap_auth($login, $_POST['password'])) {
                    
                    $stmt = $dbConnection->prepare('SELECT id,login,fio from users where login=:login AND status=1');
                    $stmt->execute(array(':login' => $login));
                    if ($stmt->rowCount() == 1) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $_SESSION['helpdesk_user_id'] = $row['id'];
                        $_SESSION['helpdesk_user_login'] = $row['login'];
                        $_SESSION['helpdesk_user_fio'] = $row['fio'];
                        $_SESSION['helpdesk_user_type'] = "user";
                        $_SESSION['zenlix.session_id']=md5(time());
                        $_SESSION['code'] = $_POST['password'];
                        unset($_SESSION['z.times']);
                        unset($_SESSION['z.times_lt']);
                        if ($rm == "1") {
                            
                            //UPDATE USERS set=password_ad
                            //password_ad encode
                            
                            // setcookie('authhash_uid', $_SESSION['helpdesk_user_id'], time()+60*60*24*7);
                            // setcookie('authhash_code', $_SESSION['code'], time()+60*60*24*7);
                            
                        }
                    }
                } else {
                    $va = "error";
                }
            }
            
            //SYSTEM auth
            else if (get_user_authtype($login) == false) {
                
                $stmt = $dbConnection->prepare('SELECT id,login,fio from users where login=:login AND pass=:pass AND status=1');
                $stmt->execute(array(':login' => $login, ':pass' => $password));
                

                
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $_SESSION['helpdesk_user_id'] = $row['id'];
                    $_SESSION['helpdesk_user_login'] = $row['login'];
                    $_SESSION['helpdesk_user_fio'] = $row['fio'];
                    $_SESSION['helpdesk_user_type'] = "user";
                     $_SESSION['zenlix.session_id']=md5(time());
                    $_SESSION['code'] = md5($password);
                    unset($_SESSION['z.times']);
                    unset($_SESSION['z.times_lt']);
                    if ($rm == "1") {
                        
                        setcookie('authhash_uid', $_SESSION['helpdesk_user_id'], time() + 60 * 60 * 24 * 7);
                        setcookie('authhash_code', $_SESSION['code'], time() + 60 * 60 * 24 * 7);
                    }
                }

                
                else {
                    $va = 'error';
                }
            }

}



if ($va == 'error') {

 if (!isset($_SESSION['z.times'])) {
    $_SESSION['z.times']=1;
    $_SESSION['z.times_lt']=time();
    
}
else if (isset($_SESSION['z.times'])) {
$_SESSION['z.times']++;
$_SESSION['z.times_lt']=time();
}


    //$_SESSION['z.error_code']=md5(time());
    //$_SESSION['z.times']++;

}


        }

/////////////////END IF LOGIN-AUTH FORM SUBMITTED/////////////////////////







        
        //if (isset($_SESSION['code']) ) {
        if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
            $url = parse_url($CONF['hostname']);
            
            if ($rq == 1) {
                if ($main_portal == "false") {
                header("Location: ".site_proto(). $url['host'] ."/". $req_url);
            }
            else if ($main_portal == "true") {
                header("Location: " . site_proto() . get_conf_param('hostname') . "/dashboard");
            }
            }
            if ($rq == 0) {
                
                if (!isset($_GET['page'])) {
                    

if ($main_portal == "true") {
    include 'inc/main_portal/index.php';
}

else if ($main_portal == "false") {
    include ("inc/dashboard.php");
    }
                    //
                    
              
                }
                
                if (isset($_GET['page'])) {
                    
                    switch ($_GET['page']) {



case 'auth':
 
if ($val_sta == true) {
    header("Location: " . site_proto() . get_conf_param('hostname') . "/dashboard");
}
else if ($val_sta == false) {
    include 'inc/auth.php';
}

                            break;


                        case 'action':
                            include ('actions.php');
                            break;



                        case 'create':
                            include ('inc/new.php');
                            break;

                        case 'list':
                            include ('inc/list.php');
                            break;

                        case 'stats':
                            include ('inc/stats.php');
                            break;

                        case 'helper':
                            include ('inc/helper.php');
                            break;

                        case 'notes':
                            include ('inc/notes.php');
                            break;

                        case 'profile':
                            include ('inc/profile.php');
                            break;

                        case 'help':
                            include ('inc/help.php');
                            break;

                        case 'users':
                            include ('inc/users.php');
                            break;

                        case 'deps':
                            include ('inc/deps.php');
                            break;

                        case 'approve':
                            include ('inc/approve.php');
                            break;

                        case 'posada':
                            include ('inc/posada.php');
                            break;

                        case 'units':
                            include ('inc/units.php');
                            break;

                        case 'subj':
                            include ('inc/subj.php');
                            break;

                        case 'ticket':
                            include ('inc/ticket.php');
                            break;

                        case 'view_user':
                            include ('inc/view_user.php');
                            break;

                        case 'userinfo':
                            include ('inc/userinfo.php');
                            break;

                        case 'config':
                            include ('inc/perf.php');
                            break;

                        case 'files':
                            include ('inc/files.php');
                            break;

                        case 'news':
                            include ('inc/news.php');
                            break;

                        case 'clients':
                            include ('inc/clients.php');
                            break;

                        case 'main_stats':
                            include ('inc/all_stats.php');
                            break;

                        case 'user_stats':
                            include ('inc/user_stats.php');
                            break;
                        case 'sla_rep':
                            include ('inc/sla_rep.php');
                            break;
                            
                        case 'scheduler':
                            include ('inc/scheduler.php');
                            break;

                        case 'messages':
                            include ('inc/messages.php');
                            break;

                        case 'print_ticket':
                            include ('inc/print_ticket.php');
                            break;

                         case 'calendar':
                            include ('inc/calendar.php');
                            break;    

                        case 'dashboard':
                            include ('inc/dashboard.php');
                            break;

                                                case 'portal':
                            include ('inc/portal.php');
                            break;
case 'mailers':
                            include ('inc/mailers.php');
                            break;


                        case 'manual':
                        include 'inc/main_portal/manual.php';
                            break;



                        case 'version':
                        include 'inc/main_portal/version.php';
                            break;
                       
                            case 'feed':
                        include 'inc/main_portal/feed.php';
                            break;
                            case 'cat':
                        include 'inc/main_portal/cat.php';
                            break;
                        case 'new_post':
                            include ('inc/main_portal/new_post.php');
                            break;
                            case 'thread':
                            include ('inc/main_portal/post.php');
                            break;
                        default:
                            include ('inc/404.inc.php');
                    }
                }
            }
        } else if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
            $url = parse_url($CONF['hostname']);
            
            if ($rq == 1) {
                if ($main_portal == "false") {
                header("Location: ".site_proto() . $url['host'] ."/". $req_url);
            }
            else if ($main_portal == "true") {
                header("Location: " . site_proto() . get_conf_param('hostname') . "/index.php");
            }
            }
            if ($rq == 0) {
                
                if (!isset($_GET['page'])) {
                    

                    //include ("inc/client.dashboard.php");

                    if ($main_portal == "true") {
    include 'inc/main_portal/index.php';
}

else if ($main_portal == "false") {
    include ("inc/client.dashboard.php");
    }

                }
                
                if (isset($_GET['page'])) {





                    
                    switch ($_GET['page']) {

case 'auth':
 
if ($val_sta == true) {
    header("Location: " . site_proto() . get_conf_param('hostname') . "/dashboard");
}
else if ($val_sta == false) {
    include 'inc/auth.php';
}

                            break;

                        case 'action':
                            include ('actions.php');
                            break;

                        case 'create':
                            include ('inc/client.new.php');
                            break;

                        case 'list':
                            include ('inc/client.list.php');
                            break;

                        case 'ticket':
                            include ('inc/client.ticket.php');
                            break;

                        case 'helper':
                            include ('inc/client.helper.php');
                            break;

                        case 'profile':
                            include ('inc/client.profile.php');
                            break;

                        case 'view_user':
                            include ('inc/client.view_user.php');
                            break;
                        case 'dashboard':
                            include ('inc/client.dashboard.php');
                            break;


                        case 'version':
                        include 'inc/main_portal/version.php';
                            break;
                        case 'manual':
                        include 'inc/main_portal/manual.php';
                            break;
                             case 'cat':
                        include 'inc/main_portal/cat.php';
                            break;
                            case 'feed':
            //include ("inc/head.inc.php");
                        include 'inc/main_portal/feed.php';
                            break;
                        case 'new_post':
                            include ('inc/main_portal/new_post.php');
                            break;
                            case 'thread':
                            include ('inc/main_portal/post.php');
                            break;
                        default:
                            include ('inc/client.404.inc.php');
                    }
                }
            }
        } else {

            //if ($main_portal == false) {



            


if ($main_portal == "true") {

if (!isset($_GET['page'])) {
include 'inc/main_portal/index.php';
}


if (isset($_GET['page'])) {
                    switch ($_GET['page']) {
                        case 'auth':
                      
       // if ($main_portal == true) {

 //include 'inc/main_portal/auth.php';
        //}



if ($val_sta == true) {
    header("Location: " . site_proto() . get_conf_param('hostname') . "");
}
else if ($val_sta == false) {
    include 'inc/main_portal/auth.php';
}






                            break;

                        case 'manual':
                        include 'inc/main_portal/manual.php';
                            break;

                        case 'cat':
                        include 'inc/main_portal/cat.php';
                            break;

                        
                    case 'version':
                        include 'inc/main_portal/version.php';
                            break;

                            
                        case 'feed':
            //include ("inc/head.inc.php");
            include 'inc/main_portal/feed.php';
                            break;
                        case 'new_post':
                            include ('inc/main_portal/new_post.php');
                            break;

                                                    case 'thread':
                            include ('inc/main_portal/post.php');
                            break;

                          default:
                            include 'inc/main_portal/index.php'; 

                        }
}


}
if ($main_portal == "false") {
            include ("inc/head.inc.php");
            include 'inc/auth.php';
}



            
        }
    }





    //конец проверки файла конфигурации системы
} else {
    include "sys/install.php";
}

//ob_end_flush();
*/
?>
