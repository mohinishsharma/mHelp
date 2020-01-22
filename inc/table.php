<?php

/**
 * Created to ease the process of displaying table in html
 * 
 * @author Mohinish Sharma
 * @package mHelp\Table
 * @version v1.1.2
 */

namespace mHelp\Table {

    include __DIR__.'/html.php';

    use mHelp\HTML\HTMLclass as HTMLclass;
    use mHelp\HTML\HTMLattributes as HTMLattributes;

    // Table class
    class Table
    {
        private $theme = "light"; // theme varibale
        private $class = "mtable"; // class for this tag
        private $name = ""; // Self name
        private $id = "mtable_"; // self id
        private $head = null; //table head contents
        private $body = null; //table body contents
        private $tag = "table"; //html tag keyword
        public $eclass = null; // HTML class object
        public $eattr = null; // HTML attr object

        /**
         * Create table element.
         * 
         * @param string Table name
         * @param array Head names
         * @param array Options
         */
        public function __construct($table_name,$head_name = array(),$options = array()) {
            $this->name = ($table_name == "")?"Table":$table_name; // setting table name
            $this->id .= (array_key_exists("id",$options))?$options["id"]:$table_name; // set table id
            $this->theme = (array_key_exists("theme",$options))?$options["theme"]:$this->theme; // Setting theme of table
            $this->class .= (array_key_exists("class",$options))?" ".$options["class"]:""; // Settting class of table
            $this->eclass = new HTMLclass($this->class); // creating class object
            $this->eattr = new HTMLattributes("id",$this->id); // creating attr object
            if(count($head_name)> 0)
            {
                $head_name = array_values($head_name); // geting head name
                $head = new Head(); // creating head element
                $hr = new Row(); // creating row element
                // adding cell to row
                foreach ($head_name as $name) {
                    $c = new Cell($name,array("type"=>"th"));
                    $hr->add_cell($c); // adding cell to head row
                }
                $head->add_row($hr); // adding row to head element
                $this->head = $head; // assinging head to head variable

                $this->body = new Body(); // creating new body element
            }
        }// end table constructor

        /**
         * Check if table has a body element.
         * 
         * @return bool True if exists else false.
         */
        public function check_body()
        {
            return !is_null($this->body);
        }

        /**
         * Set or Get the body element of table.
         * use body() to get body object.
         * 
         * @param Body Table Body object.
         * @return Body/bool
         */
        public function body($body = null)
        {
            if($this->check_body())
                return $this->body;
            elseif($body instanceof Body)
                $this->body = $body;
            else
                return false;
            return true;
        }

        /**
         * Check if table has a head element.
         * 
         * @return bool True if exists else false.
         */
        public function check_head()
        {
            return !is_null($this->head);
        }

        /**
         * Set or Get the head element of table.
         * use head() to get head object.
         * 
         * @param Head Table head object.
         * @return Head/bool
         */
        public function head($head = null)
        {
            if($this->check_head())
                return $this->head;
            elseif($head instanceof Head)
                $this->head = $head;
            else
                return false;
            return true;
        }

        public function classname()
        {
            return $this->eclass;
        }

        public function get_HTML()
        {
            $string = "<".$this->tag." ".$this->eattr->get()." ". $this->eclass->get() .">";
            $string .= $this->head()->get_HTML();
            $string .= $this->body()->get_HTML();
            $string .= "</".$this->tag.">";
            return $string;
        }


    }// End table class

    // cell of the table
    class Cell
    {
        private $text = ""; // default text
        private $class = "mcell"; // class for this tag
        private $tag = "td"; // tag keyword
        private $eclass = null; //class object
        public function __construct($text,$option = array()) {
            $this->text = $text; //set the text to display
            if(count($option)>0)
            {
                $this->tag = (array_key_exists("type",$option))?$option["type"]:$this->tag; // set type of tag
                $this->class .= (array_key_exists("class",$option))?" ".$option["class"]:""; // set type of class
            }
            $this->eclass = new HTMLclass($this->class);
        }

        //Acculumator of the data
        private function _generate_cell()
        {
            return array(
                "type"=>$this->tag,
                "text"=>$this->text,
                "class"=>$this->eclass->get()
            );
        }

        //Get HTML for cell
        public function get_HTML()
        {
            $cell = $this->_generate_cell();
            $string  = "<".$cell["type"]." ".$cell["class"].">".$cell["text"]."</".$cell["type"].">";
            return $string;
        }
        
    } // end cell class

    // row of the table
    class Row
    {
        private $tag = "tr"; // row tag
        private $cells = array(); // row cells
        private $class ="mrow"; // row default class
        private $eclass = null; // row class object
        public function __construct($cell = null,$option= array()) {
            if($cell instanceof Cell)
                $this->add_cell($cell);
            elseif(is_array($cell))
                foreach($cell as $c)
                    ($c instanceof Cell)?add_cell($c):die("Error while adding cell");
            // check for options
            if(count($option)>0)
            {
                $this->tag = (array_key_exists("type",$option))?$option["type"]:$this->tag; // set type of tag
                $this->class .= (array_key_exists("class",$option))?" ".$option["class"]:""; // set type of class
            }
            $this->eclass = new HTMLclass($this->class);
        } // end constructor

        // Add cell function
        public function add_cell($cell)
        {
            if($cell instanceof Cell){
                array_push($this->cells,$cell);
                return true;
            }
            else
                return false;
        }// end add cell function

        public function get_HTML()
        {
            $string = "<".$this->tag." ".$this->eclass->get().">";
            foreach($this->cells as $cell)
            {
                $string .= $cell->get_HTML();
            }
            $string .= "</".$this->tag.">";
            return $string;
        }

    } // END row class

    // head of table
    class Head 
    {
        private $rows = array(); // array of rows to store
        private $id = "thead_"; // id if any provided
        private $class = "mhead"; // classname if any provided
        private $tag = "thead";

        public function __construct($rows = null) {
            if($rows instanceof Row)
            {
                $rows = $rows->get_HTML();
                array_push($this->rows,$rows);
            }
            elseif(is_array($rows))
                foreach($rows as $row)
                    if($row instanceof Row)
                        array_push($this->rows,$row);
                
        }// end constructor

        public function add_row($row)
        {
            if($row instanceof Row){
                array_push($this->rows,$row);
                return true;
            }
            else
                return false;
        }

        public function get_HTML()
        {
            $string = "<".$this->tag." class=\"".$this->class."\">";
            foreach($this->rows as $row)
            {
                $string .= $row->get_HTML();
            }
            $string .= "</".$this->tag.">";
            return $string;
        }

    } // End of Head

    // Body of the table
    class Body
    {
        private $rows = array(); // array of rows to store
        private $id = "tbody_"; // id if any provided
        private $class = "mbody"; // classname if any provided
        private $tag = "tbody";

        public function __construct($rows = null) {
            if($rows instanceof Row)
                array_push($this->rows,$rows);
            elseif(is_array($rows))
                foreach($rows as $row)
                    if($row instanceof Row)
                        array_push($this->rows,$row);
                
        }// end constructor

        public function add_row($row)
        {
            if($row instanceof Row){
                array_push($this->rows,$row);
                return true;
            }
            else
                return false;
        } 

        public function get_HTML()
        {
            $string = "<".$this->tag." class=\"".$this->class."\">";
            foreach($this->rows as $row)
            {
                $string .= $row->get_HTML();
            }
            $string .= "</".$this->tag.">";
            return $string;
        }

    } // END body class
} // END table namespace

?>