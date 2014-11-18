<?php 
/** 
 * 
 * Use this class to do a backup of your database 
 * @author Raul Souza Silva (raul.3k@gmail.com) 
 * @category Database 
 * @copyright No one. You can copy, edit, do anything you want. If you change anything to better, please let me know. 
 * 
 */ 
Class DBBackup { 
    /** 
     * 
     * The host you will connect 
     * @var String 
     */ 
    private $host; 
    /** 
     * 
     * The driver you will use to connect 
     * @var String 
     */ 
    private $driver; 
    /** 
     * 
     * The user you will use to connect to a database 
     * @var String 
     */ 
    private $user; 
    /** 
     * 
     * The password you will use to connect to a database 
     * @var String 
     */ 
    private $password; 
    /** 
     * 
     * The database you will use to connect 
     * @var String 
     */ 
    private $dbName; 
    /** 
     * 
     * String to connect to the database using PDO 
     * @var String 
     */ 
    private $dsn; 

    /** 
     * 
     * Array with the tables of the database 
     * @var Array 
     */ 
    private $tables = array(); 

    /** 
     * 
     * Hold the connection 
     * @var ObjectConnection 
     */ 
    private $handler; 
    /** 
     * 
     * Array to hold the errors 
     * @var Array 
     */ 
    private $error = array(); 

    /** 
     * 
     * The result string. String with all queries 
     * @var String 
     */ 
    private $final; 

    /** 
     * 
     * The main function 
     * @method DBBackup 
     * @uses Constructor 
     * @param Array $args{host, driver, user, password, database} 
     * @example $db = new DBBackup(array('host'=>'my_host', 'driver'=>'bd_type(mysql)', 'user'=>'db_user', 'password'=>'db_password', 'database'=>'db_name'));
     */ 
    public function DBBackup($args){ 
        if(!$args['host']) $this->error[] = 'Parameter host missing'; 
        if(!$args['user']) $this->error[] = 'Parameter user missing'; 
        if(!isset($args['password'])) $this->error[] = 'Parameter password missing'; 
        if(!$args['database']) $this->error[] = 'Parameter database missing'; 
        if(!$args['driver']) $this->error[] = 'Parameter driver missing'; 

        if(count($this->error)>0){ 
            return; 
        } 

        $this->host = $args['host']; 
        $this->driver = $args['driver']; 
        $this->user = $args['user']; 
        $this->password = $args['password']; 
        $this->dbName = $args['database']; 
        

        $this->final = 'CREATE DATABASE ' . $this->dbName.";\n\n"; 

        if($this->host=='localhost'){ 
            // We have a little issue in unix systems when you set the host as localhost 
            $this->host = '127.0.0.1'; 
        } 
        $this->dsn = $this->driver.':host='.$this->host.';dbname='.$this->dbName; 

        $this->connect(); 
        $this->getTables(); 
        $this->generate(); 
    } 

    /** 
     * 
     * Call this function to get the database backup 
     * @example DBBackup::backup(); 
     */ 
    public function backup(){ 
        //return $this->final; 
        if(count($this->error)>0){ 
            return array('error'=>true, 'msg'=>$this->error); 
        } 
        return array('error'=>false, 'msg'=>$this->final); 
    } 

    /** 
     * 
     * Generate backup string 
     * @uses Private use 
     */ 
    private function generate(){ 
        foreach ($this->tables as $tbl) { 
            $this->final .= '--CREATING TABLE '.$tbl['name']."\n"; 
            $this->final .= $tbl['create'] . ";\n\n"; 
            $this->final .= '--INSERTING DATA INTO '.$tbl['name']."\n"; 
            $this->final .= $tbl['data']."\n\n\n"; 
        } 
        $this->final .= '-- THE END'."\n\n"; 
    } 

    /** 
     * 
     * Connect to a database 
     * @uses Private use 
     */ 
    private function connect(){ 
        try { 
            $this->handler = new PDO($this->dsn, $this->user, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); 
        } catch (PDOException $e) { 
            $this->handler = null; 
            $this->error[] = $e->getMessage(); 
            return false; 
        } 
    } 

    /** 
     * 
     * Get the list of tables 
     * @uses Private use 
     */ 
    private function getTables(){ 
        try { 
            $stmt = $this->handler->query('SHOW TABLES'); 
            $tbs = $stmt->fetchAll(); 
            $i=0; 
            foreach($tbs as $table){ 
                $this->tables[$i]['name'] = $table[0]; 
                $this->tables[$i]['create'] = $this->getColumns($table[0]); 
                $this->tables[$i]['data'] = $this->getData($table[0]); 
                $i++; 
            } 
            unset($stmt); 
            unset($tbs); 
            unset($i); 

            return true; 
        } catch (PDOException $e) { 
            $this->handler = null; 
            $this->error[] = $e->getMessage(); 
            return false; 
        } 
    } 

    /** 
     * 
     * Get the list of Columns 
     * @uses Private use 
     */ 
    private function getColumns($tableName){ 
        try { 
            $stmt = $this->handler->query('SHOW CREATE TABLE '.$tableName); 
            $q = $stmt->fetchAll(); 
            $q[0][1] = preg_replace("/AUTO_INCREMENT=[\w]*./", '', $q[0][1]); 
            return $q[0][1]; 
        } catch (PDOException $e){ 
            $this->handler = null; 
            $this->error[] = $e->getMessage(); 
            return false; 
        } 
    } 

    /** 
     * 
     * Get the insert data of tables 
     * @uses Private use 
     */ 
    private function getData($tableName){ 
        try { 
            $stmt = $this->handler->query('SELECT * FROM '.$tableName); 
            $q = $stmt->fetchAll(PDO::FETCH_NUM); 
            $data = ''; 
            foreach ($q as $pieces){ 
                foreach($pieces as &$value){ 
                    $value = htmlentities(addslashes($value)); 
                } 
                $data .= 'INSERT INTO '. $tableName .' VALUES (\'' . implode('\',\'', $pieces) . '\');'."\n"; 
            } 
            return $data; 
        } catch (PDOException $e){ 
            $this->handler = null; 
            $this->error[] = $e->getMessage(); 
            return false; 
        } 
    } 
} 
?>