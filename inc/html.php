<?php
/**
 * Created to ease the process of displaying html using php
 * 
 * @author Mohinish Sharma
 * @package mHelp\HTML
 * @version v1.1.2
 */

namespace mHelp\HTML {

    // HTML refrence class
    class HTMLelement
    {
        private $tag = ""; // tag element
        private $selfTag = false; // self cloesing tag
        public $eclass = null; // class object
        public $eattr = null; // attr object

        public function __construct($tag, $self = false, $options = array()) {
            $this->tag = $tag;
            $this->selfTag = $self;
            if(count($options)>0)
            {
                $this->eclass = new HTMLclass(array_key_exists("class",$options)?$options["class"]:"m".$tag);
                $this->eattr = new HTMLattributes(array_key_exists("id", $options)?$options["id"]:"m_".$tag);
            }
        }

    }

    //htmlclass class
    class HTMLclass
    {
        private $class = array(); // array of class name

        public function __construct($classname) {
            $this->add($classname);
        }

        public function add($cname)
        {
            $class = $this->_css_splitter($cname);
            $this->class = array_merge($this->class,$class);
            return true;
        }

        private function _css_splitter($cname)
        {
            if(is_string($cname))
            {
                if(strpos($cname," ") !== FALSE)
                    $class = explode(" ",$cname);
                else
                    $class = explode(".",$cname);
            }
            elseif(is_array($cname))
                $class = $cname;
            else
                return false;
            return $class;
        }

        public function remove($class)
        {
            $class = $this->_css_splitter($class);
            $nclass = array_diff($this->class,$class);
            $this->class = $nclass;
        }

        public function get()
        {
            $s ="class = \"";
            foreach($this->class as $c)
                $s .= $c. " ";
            $s = rtrim($s); 
            return $s."\"";
        }
    }

    //HTMLattributes class
    class HTMLattributes
    {
        private $attr = array(); // empty array of attributes
        private $attrname = array(); // set attribute names

        public function __construct($attr,$value = null) {
            $this->add($attr,$value);
            
        } // end constructor

        public function add($attr,$value = null)
        {
            if(is_null($value))
            {
                if(is_array($attr))
                {
                    foreach($attr as $k => $v)
                    {
                        if(is_numeric($k))
                            return false;
                    }
                    $this->attr = array_merge($this->attr,$attr);
                }
            }
            elseif(!is_numeric($attr))
                $this->attr = array_merge($this->attr,array($attr=>$value));
            else
                return false;
            
            $this->attrname = array_keys($this->attr);
            return true;
        }

        public function get()
        {
            $s = "";
            foreach($this->attr as $k => $v)
            {
                $a = $k."=\"".$v."\"";
                $s .= $a . " ";
            }
            $s = rtrim($s);
            return $s;
        }
    }
    
}

?>