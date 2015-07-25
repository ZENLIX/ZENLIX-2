<?php

//session_start();
include_once ("../../functions.inc.php");

include ("../controllers/head.inc.php");



if (isset($_GET['h'])) {
    $h = ($_GET['h']);
    
    //$query="select hashname, message from notes where hashname='$h';";
    //$res = mysql_query($query) or die(mysql_error());
    
    $stmt = $dbConnection->prepare('select hashname, message from notes where hashname=:h');
    $stmt->execute(array(
        ':h' => $h
    ));
    $res1 = $stmt->fetchAll();
    
    if (empty($res1)) {
        
        //echo "no";
        
        
    } 
    else if (!empty($res1)) {
        
        //while ($row = mysql_fetch_assoc($res)) {
        foreach ($res1 as $row) {
            $msg = $row['message'];
        }
    }
?>
<br>
<header class="header">
            <center><a href="<?php
    echo $CONF['hostname'] ?>index.php" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                <img src="<?php echo get_logo_img('small'); ?>"> <br><?php
    echo $CONF['name_of_firm'] ?>
            </a></center>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->

          
            </nav>
        </header>


<section class="content">

                    <!-- row -->
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <div class="box box-solid">
                                <div class="box-body">
<?php
    echo $msg; ?>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                    </div>
</section>













        


<?php
}
include ("../controllers/footer.inc.php");
?>
