<?php

/**
 * Created to ease the process of displaying table in html
 * 
 * @author Mohinish Sharma
 * @package mHelp
 * @version v1.1.2
 */


namespace mHelp {
    
    include 'table.php';
    use mHelp\Table\Table as table;
    use mHelp\Table\Row as row;
    use mHelp\Table\Cell as cell;

    class mHelp
    {

        private $_db = null; // Database object
        private $theme = "light"; // Theme to apply

        /**
         * initalizes database components
         * 
         * @param PDO/array Database PDO connection/Database credentials
         * @param array Option
         * @return void
         */

        public function __construct($db,$options = array()) {
            // Set database object if its null
            if(is_null($this->_db))
                if(is_array($db)) // Check if the database param is passed with array
                {
                    $err = false; //error flag
                    $d = array("hostname","port","username","password","database"); // all req. credentials
                    // to check if each required credentials is set or not
                    foreach ($d as $key) {
                        $err = !array_key_exists($key,$db);
                        if($err)
                            break;
                        elseif(is_null($db[$key])) // strlen fun missing
                        {
                            $err = true;
                            break;
                        }
                    }
                    if($err)
                        throw new \Error("Insufficent database credentials", 1); // Throw credentials error
                    else
                        $this->_db = new \PDO("mysql:host=".$db['hostname'].";dbname=". $db["database"],$db["username"],$db["password"]);

                }
            
            // Check if options param is available
            if(is_array($options))
                if(!empty($options))
                {
                    $this->theme = array_key_exists("theme",$options)?$options["theme"]:"light";
                }

        } // End construstor


        /**
         * Verify field for validation of input.
         * 
         * @param string Input value to validate.
         * @param string Expected type of input.
         * 
         * @return bool True if its valid OR False
         */
        public function verify_field($value,$type="none")
        {
            if(is_string($value))
                $l = strlen($value)==0?true:false;
            if($l)
                return false;
            switch($type) {
                case 'number': // check is its number
                    return is_numeric($value);
                    break;
                case 'alphabet': // check if its only alphabet
                    preg_match("/[A-Za-z]+/s",$value,$a);
                    return (abs(strlen($value)-strlen($a[0]))>0)?false:true;
                    break;
                case 'email': // check for email
                    preg_match("/[A-Za-z0-9_]+@[A-Za-z0-9]+.[A-Za-z]+/s",$value,$a);
                    return (abs(strlen($value)-strlen($a[0]))>0)?false:true;
                    break;
                case 'datetime':
                    $a = date_parse($value);
                    return ($a["error_count"]>0)?false:true;
                    break;
                default:
                    throw new Exception("No type was specified", 1);
            }
            return false;
        } // End verify_field

        /**
         * Generate HTML table for database table.
         * 
         * @param string Table name.
         * @param array Column to Column name array map.
         * @param array Option for the function.
         * 
         * @return string HTML table.
         */
        public function make_table($table_name,$col_name = array(),$options = array())
        {
            if(count($col_name)==0)
            {
                throw new \Exception("No column to column name array map provided", 1);
                return false;
            }
            $theme = (array_key_exists("theme",$options))?$options["theme"]:$this->theme; // check if theme option is set
            $ext = (array_key_exists("ext",$options))?$options["ext"]:""; // check if extenstion option is set
            $col_key_list = implode(",",array_keys($col_name)); //check and make list of available name
            if($table_name != "")
                if($this->_check_table($table_name))
                {
                    $table = new table("my_table",$col_name);
                    $table->classname()->add("hello");
                    $body = $table->body();
                    $stmt = $this->_db->prepare("SELECT ".$col_key_list." FROM ".$table_name." ".$ext);
                    if($stmt->execute())
                        if($stmt->rowCount()>0)
                            while($r = $stmt->fetch(\PDO::FETCH_ASSOC))
                            {
                                $row = new row();
                                foreach($r as $k => $v)
                                {
                                    $c = new cell($v);
                                    $row->add_cell($c);
                                }
                                $body->add_row($row);
                            }
                    return $table->get_HTML();
                }
            return false;
        } // End make_table

        /**
         * Check if table exixts in database
         * 
         * @param string Table name
         * @return bool true or false
         */
        private function _check_table($table_name)
        {
            try{
                $stmt = $this->_db->prepare("SHOW TABLES LIKE '".$table_name."'");
                if($stmt->execute())
                    return ($stmt->rowCount()>0)?true:false;
            } catch(\Exception $e){
                throw new \Exception($e, 1);
            }
            return false;
        } // End _check_table

        /**
        * Get active database connection for further use.
        *
        * @return PDO Active PDO connection
        */
        public function database_connection()
        {
            return $this->_db;
        } // End database_connection


    } // End class
    
} // End namespace
?>
