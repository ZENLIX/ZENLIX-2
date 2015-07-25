<?php
//$CONF['title_header'] = lang('NEW_title') . " - " . $CONF['name_of_firm'];
if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
    if ($_SESSION['helpdesk_user_id']) {
        include ("head.inc.php");
        include ("navbar.inc.php");
        
        //check_unlinked_file();
        
        
?>
<section class="content-header">
                    <h1>
                        404 Error Page
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php
        echo $CONF['hostname'] ?>index.php"><span class="icon-svg"></span> <?php
        echo $CONF['name_of_firm'] ?></a></li>
                        <li class="active">404 error</li>
                    </ol>
                </section>
               
<section class="content">

                    <div class="error-page">
                        <h2 class="headline text-info"> 404</h2>
                        <div class="error-content">
                            <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>
                            <p>
                                We could not find the page you were looking for.
                                Meanwhile, you may <a href="<?php
        echo $CONF['hostname'] ?>index.php">return to dashboard</a> or try using the search form.
                            </p>
                            
                        </div><!-- /.error-content -->
                    </div><!-- /.error-page -->

                </section>

<?php
        include ("footer.inc.php");
    }
} 
else {
    include 'auth.php';
}
?>
