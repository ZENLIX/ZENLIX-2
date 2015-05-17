<?php

class router
{
    
    protected $routes = array();
    
    /*
    USER/CLIENT/GUEST
    */
    
    private function get_privs() {
        $val_status = 'GUEST';
        
        if (validate_user($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
            $val_status = 'USER';
        } 
        else if (validate_client($_SESSION['helpdesk_user_id'], $_SESSION['code'])) {
            $val_status = 'CLIENT';
        }
        
        return $val_status;
    }
    
    private function get_portal_status() {
        global $CONF;
        $main_portal = $CONF['main_portal'];
        $r = false;
        if ($main_portal == "true") {
            $r = true;
        }
        
        return $r;
    }
    
    public function map($arr) {
        
        $this->routes[] = $arr;
    }
    
    public function release() {
        
        //print_r($this->routes);
        
        $r = $this->routes;
        
        //print_r($_GET);
        
        foreach ($r as $value) {
            // code...
            unset($method);
            
            //unset($portal);
            foreach ($value as $key => $values) {
                
                $check_flag = false;
                
                //echo $key." ==> ".$values."<br>";
                
                if ($key == "method") {
                    $method = $values;
                } 
                else if ($key == "path") {
                    $path = $values;
                } 
                else if ($key == "params") {
                    $params = $values;
                } 
                else if ($key == "privs") {
                    $privs = $values;
                } 
                else if ($key == "portal") {
                    $portal = $values;
                } 
                else if ($key == "action") {
                    $action = $values;
                }
                
                /*
                if ($key == "method") {
                //echo $values;
                
                }
                
                if ($key == "path") {
                $path=$values;
                
                }
                
                if ($key == "params") {
                $params=$values;
                
                }
                */
                
                //echo $key."=>".$values."<br>";
                
                
            }
            
            //echo $method."<br>";
            
            if ($method == "GET") {
                
                //echo 'r';
                
                if ($_GET[$params] == $path) {
                    echo $portal;
                    $privs_get = self::get_privs();
                    
                    //echo $privs_get;
                    
                    if ($privs_get == $privs) {
                        
                        $get_portal_status = self::get_portal_status();
                        
                        echo $portal . "<br>";
                        
                        //if (isset($portal)) {
                        if ($get_portal_status == $portal) {
                            include ($action);
                        }
                        
                        //}
                        //else if (!isset($portal)) {
                        //	include ($action);
                        //}
                        
                        
                    }
                }
            }
        }
        
        //if self::check_privs('USER')
        
        
    }
}
?>