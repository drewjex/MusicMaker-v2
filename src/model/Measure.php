<?php

class Measure {
    
    public $notes = array();
    public $chords = array();
    
    public function __construct($notes=array(), $chords=array()) {
        $this->notes = $notes;
        $this->chords = $chords;
    }
    
    public function __clone() {
         foreach($this as $key => $val) {
            if (is_object($val) || (is_array($val))) {
                $this->{$key} = unserialize(serialize($val));
            }
        }
    }
}

?>