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
include_once ('library/AltoRouter.php');

include_once ("inc/route_actions.php");

$router = new AltoRouter();

//echo get_base_path();
$router->setBasePath(get_base_path());

$router->map('GET', '/register', 'registerAction');
$router->map('GET', '/forgot', 'forgotAction');
$router->map('GET', '/index.php', 'indexAction');
$router->map('GET', '/', 'indexAction');
$router->map('GET', '/auth', 'auth_get');
$router->map('GET', '/create', 'createAction');
$router->map('GET', '/list', 'listAction');
$router->map('GET', '/stats', 'statsAction');
$router->map('GET', '/helper', 'helperAction');
$router->map('GET', '/notes', 'notesAction');
$router->map('GET', '/profile', 'profileAction');
$router->map('GET', '/users', 'usersAction');
$router->map('GET', '/help', 'helpAction');
$router->map('GET', '/deps', 'depsAction');
$router->map('GET', '/approve', 'approveAction');
$router->map('GET', '/units', 'unitsAction');
$router->map('GET', '/posada', 'posadaAction');
$router->map('GET', '/ticket', 'ticketAction');
$router->map('GET', '/subj', 'subjAction');
$router->map('GET', '/view_user', 'view_userAction');
$router->map('GET', '/userinfo', 'userinfoAction');
$router->map('GET', '/config', 'perfAction');
$router->map('GET', '/files', 'filesAction');
$router->map('GET', '/news', 'newsAction');
$router->map('GET', '/clients', 'clientsAction');
$router->map('GET', '/main_stats', 'all_statsAction');
$router->map('GET', '/user_stats', 'user_statsAction');
$router->map('GET', '/sla_rep', 'sla_repAction');
$router->map('GET', '/scheduler', 'schedulerAction');
$router->map('GET', '/messages', 'messagesAction');
$router->map('GET', '/print_ticket', 'print_ticketAction');
$router->map('GET', '/calendar', 'calendarAction');
$router->map('GET', '/portal', 'portalAction');
$router->map('GET', '/mailers', 'mailersAction');

///////////////////////////////////////
$router->map('GET', '/manual', 'manualAction');

$router->map('GET', '/version', 'versionAction');

$router->map('GET', '/feed', 'feedAction');

$router->map('GET', '/cat', 'catAction');

$router->map('GET', '/new_post', 'new_postAction');

$router->map('GET', '/thread', 'postAction');

$router->map('POST', '/auth', 'auth');

$router->map('POST', '/action', function () {
    global $dbConnection, $CONF,$CONF_MAIL;
    require 'actions.php';
});

$router->map('POST', '/portal_action', function () {
    global $dbConnection, $CONF, $CONF_MAIL;
    require 'inc/main_portal/actions.php';
});

$router->map('POST', '/api', function () {
    global $dbConnection, $CONF;
    require 'api.php';
});

$router->map('GET', '/dashboard', 'dashboardAction');

$router->map('GET', '/logout', 'logoutAction');

$match = $router->match();
if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} 
else {
    
    // no route was matched
    global $dbConnection, $CONF;
    $privs = get_privs();
    if ($privs == "CLIENT") {
        include ('inc/client.404.inc.php');
    } 
    else if ($privs == "USER") {
        include ('inc/404.inc.php');
    }
    if ($privs == "GUEST") {
        include ('inc/auth.php');
    }
}
?>
